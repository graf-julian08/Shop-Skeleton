<?php
/**
 * Cancellations API
 * Complete order cancellation management with refund workflow
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

Database::configure($database);
Auth::init();

// =====================================================================
// AUTO-MIGRATION: Create cancellations table
// =====================================================================
try {
    Database::query("
        CREATE TABLE IF NOT EXISTS cancellations (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            order_id BIGINT UNSIGNED NOT NULL,
            cancellation_number VARCHAR(50) UNIQUE NOT NULL,
            reason ENUM('customer_request','payment_failed','fraud','out_of_stock','duplicate','other') DEFAULT 'customer_request',
            reason_details TEXT,
            status ENUM('pending','approved','refunded','rejected') DEFAULT 'pending',
            refund_amount DECIMAL(12,2) DEFAULT 0,
            refund_status ENUM('none','partial','full') DEFAULT 'none',
            original_total DECIMAL(12,2) DEFAULT 0,
            currency_code VARCHAR(3) DEFAULT 'EUR',
            cancelled_by ENUM('customer','admin') DEFAULT 'admin',
            notes TEXT,
            processed_by VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            processed_at TIMESTAMP NULL,
            INDEX idx_shop (shop_id),
            INDEX idx_order (order_id),
            INDEX idx_status (status),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
} catch (Exception $e) {
    // Table might already exist
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_cancellations':
            handleGetCancellations($shopId);
            break;
        case 'get_cancellation':
            handleGetCancellation($shopId);
            break;
        case 'cancel_order':
            handleCancelOrder($shopId);
            break;
        case 'process_cancellation':
            handleProcessCancellation($shopId);
            break;
        case 'get_stats':
            handleGetStats($shopId);
            break;
        case 'generate_test_data':
            handleGenerateTestData($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// GET CANCELLATIONS (with pagination, filters, sorting)
// =====================================================================
function handleGetCancellations(int $shopId): void
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 20)));
    $offset = ($page - 1) * $perPage;

    $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? '';
    $reason = $_GET['reason'] ?? '';
    $activeOnly = isset($_GET['active_only']) && $_GET['active_only'] == '1';
    $sortBy = $_GET['sort_by'] ?? 'created_at';
    $sortDir = strtoupper($_GET['sort_dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
    $displayCurrency = $_GET['display_currency'] ?? null;

    // Get exchange rates
    $currencies = Database::fetchAll("SELECT code, symbol, exchange_rate FROM currencies WHERE shop_id = ?", [$shopId]);
    $exchangeRates = [];
    $currencySymbols = [];
    foreach ($currencies as $c) {
        $exchangeRates[$c['code']] = (float) $c['exchange_rate'];
        $currencySymbols[$c['code']] = $c['symbol'];
    }

    // Default/display currency
    $defaultCurrency = Database::fetch("SELECT code, symbol FROM currencies WHERE shop_id = ? AND is_default = 1", [$shopId]);
    $defaultCurrencyCode = $defaultCurrency['code'] ?? 'EUR';
    $defaultCurrencySymbol = $defaultCurrency['symbol'] ?? '€';

    $displayCurrencyCode = $displayCurrency ?: $defaultCurrencyCode;
    $displayCurrencySymbol = $currencySymbols[$displayCurrencyCode] ?? $defaultCurrencySymbol;
    $displayRate = $exchangeRates[$displayCurrencyCode] ?? 1;

    // Build WHERE clause
    $where = ["c.shop_id = ?"];
    $params = [$shopId];

    if ($search) {
        $where[] = "(c.cancellation_number LIKE ? OR o.order_number LIKE ? OR cust.first_name LIKE ? OR cust.last_name LIKE ?)";
        $searchTerm = "%{$search}%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }

    if ($status && $status !== 'all') {
        $where[] = "c.status = ?";
        $params[] = $status;
    }

    if ($reason && $reason !== 'all') {
        $where[] = "c.reason = ?";
        $params[] = $reason;
    }

    if ($activeOnly) {
        $where[] = "c.status IN ('pending', 'approved')";
    }

    $whereClause = implode(' AND ', $where);

    // Validate sort column
    $validSorts = ['created_at', 'original_total', 'status', 'cancellation_number'];
    if (!in_array($sortBy, $validSorts)) {
        $sortBy = 'created_at';
    }

    // Total count
    $total = Database::fetch("
        SELECT COUNT(*) as count 
        FROM cancellations c
        LEFT JOIN orders o ON c.order_id = o.id
        LEFT JOIN customers cust ON o.customer_id = cust.id
        WHERE {$whereClause}
    ", $params);

    // Get cancellations
    $query = "
        SELECT c.*,
               o.order_number, o.grand_total as order_total, o.currency_code as order_currency,
               CONCAT(COALESCE(cust.first_name, ''), ' ', COALESCE(cust.last_name, '')) as customer_name,
               cust.email as customer_email,
               (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = c.order_id) as item_count
        FROM cancellations c
        LEFT JOIN orders o ON c.order_id = o.id
        LEFT JOIN customers cust ON o.customer_id = cust.id
        WHERE {$whereClause}
        ORDER BY c.{$sortBy} {$sortDir}
        LIMIT ? OFFSET ?
    ";

    $cancellations = Database::fetchAll($query, array_merge($params, [$perPage, $offset]));

    // Convert amounts to display currency
    foreach ($cancellations as &$cancel) {
        $orderCurrency = $cancel['order_currency'] ?: 'EUR';
        $orderRate = $exchangeRates[$orderCurrency] ?? 1;

        // Convert to base then to display currency
        $originalInBase = (float) $cancel['original_total'] / $orderRate;
        $refundInBase = (float) $cancel['refund_amount'] / $orderRate;

        $cancel['display_original'] = $originalInBase * $displayRate;
        $cancel['display_refund'] = $refundInBase * $displayRate;
        $cancel['display_currency'] = $displayCurrencyCode;
        $cancel['display_symbol'] = $displayCurrencySymbol;
        $cancel['customer_name'] = trim($cancel['customer_name']) ?: 'Gast';
    }

    echo json_encode([
        'success' => true,
        'cancellations' => $cancellations,
        'currency' => [
            'code' => $displayCurrencyCode,
            'symbol' => $displayCurrencySymbol,
            'default_code' => $defaultCurrencyCode
        ],
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => (int) $total['count'],
            'total_pages' => ceil($total['count'] / $perPage)
        ]
    ]);
}

// =====================================================================
// GET SINGLE CANCELLATION
// =====================================================================
function handleGetCancellation(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid cancellation ID']);
        return;
    }

    $cancellation = Database::fetch("
        SELECT c.*,
               o.order_number, o.grand_total as order_total, o.currency_code as order_currency,
               o.created_at as order_date, o.payment_method, o.shipping_method,
               o.billing_address, o.shipping_address,
               CONCAT(COALESCE(cust.first_name, ''), ' ', COALESCE(cust.last_name, '')) as customer_name,
               cust.email as customer_email
        FROM cancellations c
        LEFT JOIN orders o ON c.order_id = o.id
        LEFT JOIN customers cust ON o.customer_id = cust.id
        WHERE c.id = ? AND c.shop_id = ?
    ", [$id, $shopId]);

    if (!$cancellation) {
        echo json_encode(['success' => false, 'error' => 'Cancellation not found']);
        return;
    }

    // Get order items
    $items = Database::fetchAll("
        SELECT oi.*, p.name as product_name
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ", [$cancellation['order_id']]);

    // Parse JSON addresses
    $cancellation['billing_address'] = $cancellation['billing_address'] ? json_decode($cancellation['billing_address'], true) : null;
    $cancellation['shipping_address'] = $cancellation['shipping_address'] ? json_decode($cancellation['shipping_address'], true) : null;
    $cancellation['customer_name'] = trim($cancellation['customer_name']) ?: 'Gast';
    $cancellation['items'] = $items;

    // Get currency info
    $currency = Database::fetch("SELECT symbol FROM currencies WHERE shop_id = ? AND code = ?", [$shopId, $cancellation['order_currency'] ?: 'EUR']);
    $cancellation['currency_symbol'] = $currency['symbol'] ?? '€';

    echo json_encode([
        'success' => true,
        'cancellation' => $cancellation
    ]);
}

// =====================================================================
// CANCEL ORDER (create new cancellation)
// =====================================================================
function handleCancelOrder(int $shopId): void
{
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $reason = $_POST['reason'] ?? 'customer_request';
    $reasonDetails = trim($_POST['reason_details'] ?? '');
    $cancelledBy = $_POST['cancelled_by'] ?? 'admin';

    if ($orderId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid order ID']);
        return;
    }

    // Check if order exists and is not already cancelled
    $order = Database::fetch("SELECT * FROM orders WHERE id = ? AND shop_id = ?", [$orderId, $shopId]);
    if (!$order) {
        echo json_encode(['success' => false, 'error' => 'Order not found']);
        return;
    }

    if ($order['status'] === 'cancelled') {
        echo json_encode(['success' => false, 'error' => 'Order is already cancelled']);
        return;
    }

    // Check if cancellation already exists
    $existing = Database::fetch("SELECT id FROM cancellations WHERE order_id = ?", [$orderId]);
    if ($existing) {
        echo json_encode(['success' => false, 'error' => 'Cancellation already exists for this order']);
        return;
    }

    // Generate cancellation number
    $cancellationNumber = 'CAN-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

    // Create cancellation
    $cancellationId = Database::insert('cancellations', [
        'shop_id' => $shopId,
        'order_id' => $orderId,
        'cancellation_number' => $cancellationNumber,
        'reason' => $reason,
        'reason_details' => $reasonDetails,
        'status' => 'pending',
        'original_total' => $order['grand_total'],
        'currency_code' => $order['currency_code'] ?: 'EUR',
        'cancelled_by' => $cancelledBy,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    // Update order status
    Database::update('orders', [
        'status' => 'cancelled',
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$orderId]);

    // Add to order history
    Database::insert('order_status_history', [
        'order_id' => $orderId,
        'status' => 'Storniert',
        'comment' => 'Bestellung wurde storniert. Grund: ' . getReasonLabel($reason),
        'created_at' => date('Y-m-d H:i:s')
    ]);

    echo json_encode([
        'success' => true,
        'cancellation_id' => $cancellationId,
        'cancellation_number' => $cancellationNumber,
        'message' => 'Bestellung wurde storniert'
    ]);
}

// =====================================================================
// PROCESS CANCELLATION (Approve, Refund, Reject)
// =====================================================================
function handleProcessCancellation(int $shopId): void
{
    $cancellationId = (int) ($_POST['cancellation_id'] ?? 0);
    $processAction = $_POST['process_action'] ?? '';
    $refundAmount = isset($_POST['refund_amount']) ? (float) $_POST['refund_amount'] : null;
    $notes = trim($_POST['notes'] ?? '');

    if ($cancellationId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid cancellation ID']);
        return;
    }

    $cancellation = Database::fetch("SELECT * FROM cancellations WHERE id = ? AND shop_id = ?", [$cancellationId, $shopId]);
    if (!$cancellation) {
        echo json_encode(['success' => false, 'error' => 'Cancellation not found']);
        return;
    }

    $update = ['processed_at' => date('Y-m-d H:i:s')];

    switch ($processAction) {
        case 'approve':
            if ($cancellation['status'] !== 'pending') {
                echo json_encode(['success' => false, 'error' => 'Can only approve pending cancellations']);
                return;
            }
            $update['status'] = 'approved';
            break;

        case 'refund':
            if (!in_array($cancellation['status'], ['pending', 'approved'])) {
                echo json_encode(['success' => false, 'error' => 'Cannot refund this cancellation']);
                return;
            }

            // Calculate refund amount
            $originalTotal = (float) $cancellation['original_total'];
            $actualRefund = $refundAmount !== null ? min($refundAmount, $originalTotal) : $originalTotal;

            $update['status'] = 'refunded';
            $update['refund_amount'] = $actualRefund;
            $update['refund_status'] = $actualRefund >= $originalTotal ? 'full' : 'partial';

            // Update order payment status
            Database::update('orders', [
                'payment_status' => 'refunded',
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$cancellation['order_id']]);

            // Add to order history
            Database::insert('order_status_history', [
                'order_id' => $cancellation['order_id'],
                'status' => 'Erstattet',
                'comment' => 'Erstattung durchgeführt: ' . number_format($actualRefund, 2, ',', '.') . ' ' . $cancellation['currency_code'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
            break;

        case 'reject':
            if ($cancellation['status'] !== 'pending') {
                echo json_encode(['success' => false, 'error' => 'Can only reject pending cancellations']);
                return;
            }
            $update['status'] = 'rejected';

            // Restore order status back to pending
            Database::update('orders', [
                'status' => 'pending',
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$cancellation['order_id']]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            return;
    }

    if ($notes) {
        $update['notes'] = $notes;
    }

    Database::update('cancellations', $update, 'id = ?', [$cancellationId]);

    echo json_encode([
        'success' => true,
        'message' => 'Stornierung aktualisiert'
    ]);
}

// =====================================================================
// GET STATS (with period filter, currency conversion)
// =====================================================================
function handleGetStats(int $shopId): void
{
    $period = $_GET['period'] ?? 'week';
    $displayCurrency = $_GET['display_currency'] ?? null;

    // Period filter for cancellations (uses 'c' alias)
    $periodConditions = [
        'today' => "DATE(c.created_at) = CURDATE()",
        'week' => "c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
        'month' => "c.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
        'year' => "c.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)",
        'all' => "1=1"
    ];
    $periodWhere = $periodConditions[$period] ?? $periodConditions['week'];

    // Period filter for orders table (uses 'o' alias)
    $orderPeriodConditions = [
        'today' => "DATE(o.created_at) = CURDATE()",
        'week' => "o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
        'month' => "o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
        'year' => "o.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)",
        'all' => "1=1"
    ];
    $orderPeriodWhere = $orderPeriodConditions[$period] ?? $orderPeriodConditions['week'];

    // Get exchange rates
    $currencies = Database::fetchAll("SELECT code, symbol, exchange_rate, is_default FROM currencies WHERE shop_id = ?", [$shopId]);
    $exchangeRates = [];
    $currencySymbols = [];
    $defaultCurrencyCode = 'EUR';
    $defaultCurrencySymbol = '€';

    foreach ($currencies as $c) {
        $exchangeRates[$c['code']] = (float) $c['exchange_rate'];
        $currencySymbols[$c['code']] = $c['symbol'];
        if ((int) $c['is_default'] === 1) {
            $defaultCurrencyCode = $c['code'];
            $defaultCurrencySymbol = $c['symbol'];
        }
    }

    $displayCurrencyCode = $displayCurrency ?: $defaultCurrencyCode;
    $displayCurrencySymbol = $currencySymbols[$displayCurrencyCode] ?? $defaultCurrencySymbol;
    $displayRate = $exchangeRates[$displayCurrencyCode] ?? 1;

    // Total cancellations in period
    $periodStats = Database::fetch("
        SELECT COUNT(*) as count,
               COALESCE(SUM(CASE WHEN status = 'refunded' THEN refund_amount ELSE 0 END), 0) as total_refunds,
               COALESCE(SUM(original_total), 0) as total_cancelled
        FROM cancellations c
        WHERE c.shop_id = ? AND {$periodWhere}
    ", [$shopId]);

    // Active/pending cancellations
    $activeCount = Database::fetch("
        SELECT COUNT(*) as count FROM cancellations WHERE shop_id = ? AND status IN ('pending', 'approved')
    ", [$shopId])['count'];

    // Pending refunds (approved but not yet refunded)
    $pendingRefunds = Database::fetch("
        SELECT COALESCE(SUM(original_total), 0) as total
        FROM cancellations WHERE shop_id = ? AND status = 'approved'
    ", [$shopId])['total'];

    // Total orders in period for cancellation rate
    $totalOrders = Database::fetch("
        SELECT COUNT(*) as count FROM orders o
        WHERE o.shop_id = ? AND {$orderPeriodWhere}
    ", [$shopId])['count'];

    // Cancellation rate
    $cancelRate = $totalOrders > 0 ? ((int) $periodStats['count'] / (int) $totalOrders) * 100 : 0;

    // Total count (all time)
    $totalCount = Database::fetch("SELECT COUNT(*) as count FROM cancellations WHERE shop_id = ?", [$shopId])['count'];

    // Reasons breakdown for period
    $reasons = Database::fetchAll("
        SELECT reason, COUNT(*) as count
        FROM cancellations c
        WHERE c.shop_id = ? AND {$periodWhere}
        GROUP BY reason
        ORDER BY count DESC
    ", [$shopId]);

    $totalReasons = array_sum(array_column($reasons, 'count'));
    foreach ($reasons as &$r) {
        $r['percent'] = $totalReasons > 0 ? ($r['count'] / $totalReasons) * 100 : 0;
    }

    // Convert amounts to display currency (assuming base currency storage)
    $totalRefundsConverted = (float) $periodStats['total_refunds'] * $displayRate;
    $pendingRefundsConverted = (float) $pendingRefunds * $displayRate;
    $totalCancelledConverted = (float) $periodStats['total_cancelled'] * $displayRate;

    echo json_encode([
        'success' => true,
        'stats' => [
            'period_count' => (int) $periodStats['count'],
            'cancel_rate' => round($cancelRate, 1),
            'total_refunds' => $totalRefundsConverted,
            'pending_refunds' => $pendingRefundsConverted,
            'total_cancelled' => $totalCancelledConverted,
            'active' => (int) $activeCount,
            'total' => (int) $totalCount,
            'reasons' => $reasons
        ],
        'currency' => [
            'code' => $displayCurrencyCode,
            'symbol' => $displayCurrencySymbol,
            'default_code' => $defaultCurrencyCode
        ],
        'available_currencies' => $currencies
    ]);
}

// =====================================================================
// GENERATE TEST DATA
// =====================================================================
function handleGenerateTestData(int $shopId): void
{
    $force = isset($_GET['force']) || isset($_POST['force']);

    // Check if cancellations already exist
    $existing = Database::fetch("SELECT COUNT(*) as count FROM cancellations WHERE shop_id = ?", [$shopId]);
    if ($existing['count'] > 0 && !$force) {
        echo json_encode(['success' => true, 'message' => 'Test data already exists', 'count' => $existing['count']]);
        return;
    }

    // If force, delete existing
    if ($force && $existing['count'] > 0) {
        Database::query("DELETE FROM cancellations WHERE shop_id = ?", [$shopId]);
    }

    // Get some orders to cancel (prefer recent ones)
    $orders = Database::fetchAll("
        SELECT o.*, 
               CONCAT(COALESCE(c.first_name, ''), ' ', COALESCE(c.last_name, '')) as customer_name
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        WHERE o.shop_id = ? 
        ORDER BY o.created_at DESC
        LIMIT 15
    ", [$shopId]);

    if (count($orders) < 5) {
        echo json_encode(['success' => false, 'error' => 'Not enough orders to create cancellations. Please generate order test data first.']);
        return;
    }

    $reasons = ['customer_request', 'payment_failed', 'fraud', 'out_of_stock', 'duplicate', 'other'];
    $statuses = ['pending', 'approved', 'refunded', 'refunded', 'rejected'];
    $reasonDetails = [
        'customer_request' => ['Kunde hat Meinung geändert', 'Versehentlich bestellt', 'Anderes Produkt gefunden'],
        'payment_failed' => ['Kreditkarte abgelehnt', 'Zahlungstimeout', 'Unzureichende Deckung'],
        'fraud' => ['Verdächtige Aktivität', 'Gestohlene Kreditkarte', 'Falsche Identität'],
        'out_of_stock' => ['Lieferant hat nicht', 'Lagerbestand falsch', 'Produktionsproblem'],
        'duplicate' => ['Doppelte Bestellung', 'Kunde hat zweimal geklickt'],
        'other' => ['Sonstiger Grund', 'Auf Anfrage storniert']
    ];

    $createdCount = 0;

    // Create 8 cancellations from the orders
    for ($i = 0; $i < min(8, count($orders)); $i++) {
        $order = $orders[$i];
        $reason = $reasons[array_rand($reasons)];
        $status = $statuses[array_rand($statuses)];
        $details = $reasonDetails[$reason][array_rand($reasonDetails[$reason])];

        // Random date within last 30 days
        $daysAgo = rand(0, 30);
        $cancelDate = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days -" . rand(0, 23) . " hours"));

        $cancellationNumber = 'CAN-' . date('Ymd', strtotime($cancelDate)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

        $refundAmount = 0;
        $refundStatus = 'none';
        if ($status === 'refunded') {
            // 80% full refund, 20% partial
            if (rand(1, 10) <= 8) {
                $refundAmount = $order['grand_total'];
                $refundStatus = 'full';
            } else {
                $refundAmount = round((float) $order['grand_total'] * (rand(50, 90) / 100), 2);
                $refundStatus = 'partial';
            }
        }

        Database::insert('cancellations', [
            'shop_id' => $shopId,
            'order_id' => $order['id'],
            'cancellation_number' => $cancellationNumber,
            'reason' => $reason,
            'reason_details' => $details,
            'status' => $status,
            'refund_amount' => $refundAmount,
            'refund_status' => $refundStatus,
            'original_total' => $order['grand_total'],
            'currency_code' => $order['currency_code'] ?: 'EUR',
            'cancelled_by' => rand(0, 1) ? 'customer' : 'admin',
            'created_at' => $cancelDate,
            'processed_at' => in_array($status, ['refunded', 'rejected']) ? date('Y-m-d H:i:s', strtotime($cancelDate . ' +1 day')) : null
        ]);

        $createdCount++;
    }

    echo json_encode([
        'success' => true,
        'message' => "{$createdCount} Test-Stornierungen erstellt",
        'count' => $createdCount
    ]);
}

// =====================================================================
// HELPER: Get reason label
// =====================================================================
function getReasonLabel(string $reason): string
{
    $labels = [
        'customer_request' => 'Kundenwunsch',
        'payment_failed' => 'Zahlung fehlgeschlagen',
        'fraud' => 'Betrugsverdacht',
        'out_of_stock' => 'Nicht lieferbar',
        'duplicate' => 'Doppelte Bestellung',
        'other' => 'Sonstiges'
    ];
    return $labels[$reason] ?? $reason;
}
