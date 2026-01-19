<?php
/**
 * Returns API
 * RMA (Return Merchandise Authorization) management
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

Database::configure($database);
Auth::init();

// =====================================================================
// AUTO-MIGRATION: Create returns tables
// =====================================================================
try {
    Database::query("
        CREATE TABLE IF NOT EXISTS returns (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            order_id BIGINT UNSIGNED NOT NULL,
            return_number VARCHAR(50) NOT NULL,
            status ENUM('requested','approved','shipped','received','refunded','rejected') DEFAULT 'requested',
            reason TEXT,
            return_type ENUM('refund','exchange','repair') DEFAULT 'refund',
            refund_amount DECIMAL(12,2) DEFAULT 0,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            processed_at TIMESTAMP NULL,
            INDEX idx_order (order_id),
            INDEX idx_status (status),
            INDEX idx_shop (shop_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    Database::query("
        CREATE TABLE IF NOT EXISTS return_items (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            return_id BIGINT UNSIGNED NOT NULL,
            order_item_id BIGINT UNSIGNED,
            product_id BIGINT UNSIGNED,
            sku VARCHAR(100),
            name VARCHAR(255),
            quantity INT DEFAULT 1,
            item_condition ENUM('unopened','opened','used','damaged') DEFAULT 'used',
            reason TEXT,
            INDEX idx_return (return_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    Database::query("
        CREATE TABLE IF NOT EXISTS shipping_labels (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shipment_id BIGINT UNSIGNED,
            return_id BIGINT UNSIGNED,
            carrier_code VARCHAR(50),
            tracking_number VARCHAR(100),
            label_data LONGBLOB,
            label_format VARCHAR(20) DEFAULT 'PDF',
            label_type ENUM('outbound','return') DEFAULT 'outbound',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_shipment (shipment_id),
            INDEX idx_return (return_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

} catch (Exception $e) {
    // Tables might already exist
}

// =====================================================================
// ROUTE ACTIONS
// =====================================================================
$action = $_REQUEST['action'] ?? 'get_returns';
$shopId = 1;

switch ($action) {
    case 'get_returns':
        handleGetReturns($shopId);
        break;
    case 'get_return':
        handleGetReturn($shopId);
        break;
    case 'create_return':
        handleCreateReturn($shopId);
        break;
    case 'update_return':
        handleUpdateReturn($shopId);
        break;
    case 'process_return':
        handleProcessReturn($shopId);
        break;
    case 'get_stats':
        handleGetStats($shopId);
        break;
    case 'generate_return_label':
        handleGenerateReturnLabel($shopId);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
}

// =====================================================================
// GET RETURNS (with pagination, filters, active_only)
// =====================================================================
function handleGetReturns(int $shopId): void
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 25)));
    $offset = ($page - 1) * $perPage;
    $status = trim($_GET['status'] ?? '');
    $reason = trim($_GET['reason'] ?? '');
    $search = trim($_GET['search'] ?? '');
    $activeOnly = (int) ($_GET['active_only'] ?? 0);

    $where = ['r.shop_id = ?'];
    $params = [$shopId];

    // Filter active returns (not refunded/rejected)
    if ($activeOnly) {
        $where[] = "r.status NOT IN ('refunded', 'rejected')";
    }

    if ($status) {
        $where[] = 'r.status = ?';
        $params[] = $status;
    }

    if ($reason) {
        $where[] = 'r.reason = ?';
        $params[] = $reason;
    }

    if ($search) {
        $where[] = "(r.return_number LIKE ? OR o.order_number LIKE ? OR CONCAT(c.first_name, ' ', c.last_name) LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    $whereClause = implode(' AND ', $where);

    // Count
    $countResult = Database::fetch("
        SELECT COUNT(*) as total 
        FROM returns r
        LEFT JOIN orders o ON r.order_id = o.id
        LEFT JOIN customers c ON o.customer_id = c.id
        WHERE {$whereClause}
    ", $params);
    $total = (int) ($countResult['total'] ?? 0);

    // Get returns with joins
    $returns = Database::fetchAll("
        SELECT r.*, 
               o.order_number,
               CONCAT(COALESCE(c.first_name, ''), ' ', COALESCE(c.last_name, '')) as customer_name,
               COALESCE(c.email, '') as customer_email,
               (SELECT COUNT(*) FROM return_items WHERE return_id = r.id) as item_count
        FROM returns r
        LEFT JOIN orders o ON r.order_id = o.id
        LEFT JOIN customers c ON o.customer_id = c.id
        WHERE {$whereClause}
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?
    ", array_merge($params, [$perPage, $offset]));

    echo json_encode([
        'success' => true,
        'returns' => $returns,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ]
    ]);
}

// =====================================================================
// GET SINGLE RETURN
// =====================================================================
function handleGetReturn(int $shopId): void
{
    $returnId = (int) ($_GET['id'] ?? 0);

    $return = Database::fetch("
        SELECT r.*, 
               o.order_number, o.shipping_address,
               CONCAT(COALESCE(c.first_name, ''), ' ', COALESCE(c.last_name, '')) as customer_name,
               COALESCE(c.email, '') as customer_email
        FROM returns r
        LEFT JOIN orders o ON r.order_id = o.id
        LEFT JOIN customers c ON o.customer_id = c.id
        WHERE r.id = ? AND r.shop_id = ?
    ", [$returnId, $shopId]);

    if (!$return) {
        echo json_encode(['success' => false, 'error' => 'Retoure nicht gefunden']);
        return;
    }

    // Get return items
    $items = Database::fetchAll("
        SELECT * FROM return_items WHERE return_id = ?
    ", [$returnId]);

    $return['items'] = $items;

    echo json_encode(['success' => true, 'return' => $return]);
}

// =====================================================================
// CREATE RETURN
// =====================================================================
function handleCreateReturn(int $shopId): void
{
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    $returnType = $_POST['return_type'] ?? 'refund';
    $items = $_POST['items'] ?? [];

    if (!$orderId) {
        echo json_encode(['success' => false, 'error' => 'Bestellung erforderlich']);
        return;
    }

    // Generate return number
    $returnNumber = 'RMA-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

    // Create return
    $returnId = Database::insert('returns', [
        'shop_id' => $shopId,
        'order_id' => $orderId,
        'return_number' => $returnNumber,
        'status' => 'requested',
        'reason' => $reason,
        'return_type' => $returnType
    ]);

    // Add items
    if (is_string($items)) {
        $items = json_decode($items, true) ?? [];
    }

    foreach ($items as $item) {
        Database::insert('return_items', [
            'return_id' => $returnId,
            'order_item_id' => $item['order_item_id'] ?? null,
            'product_id' => $item['product_id'] ?? null,
            'sku' => $item['sku'] ?? '',
            'name' => $item['name'] ?? '',
            'quantity' => $item['quantity'] ?? 1,
            'item_condition' => $item['condition'] ?? 'used',
            'reason' => $item['reason'] ?? ''
        ]);
    }

    echo json_encode([
        'success' => true,
        'return_id' => $returnId,
        'return_number' => $returnNumber,
        'message' => 'Retoure erstellt: ' . $returnNumber
    ]);
}

// =====================================================================
// UPDATE RETURN
// =====================================================================
function handleUpdateReturn(int $shopId): void
{
    $returnId = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $refundAmount = (float) ($_POST['refund_amount'] ?? 0);

    $return = Database::fetch("SELECT * FROM returns WHERE id = ? AND shop_id = ?", [$returnId, $shopId]);
    if (!$return) {
        echo json_encode(['success' => false, 'error' => 'Retoure nicht gefunden']);
        return;
    }

    $updates = [];
    if ($status) {
        $updates['status'] = $status;
        if ($status === 'refunded' || $status === 'received') {
            $updates['processed_at'] = date('Y-m-d H:i:s');
        }
    }
    if ($notes) {
        $updates['notes'] = $notes;
    }
    if ($refundAmount > 0) {
        $updates['refund_amount'] = $refundAmount;
    }

    if (!empty($updates)) {
        Database::update('returns', $updates, 'id = ?', [$returnId]);
    }

    echo json_encode(['success' => true, 'message' => 'Retoure aktualisiert']);
}

// =====================================================================
// PROCESS RETURN (Approve, Receive, Refund)
// =====================================================================
function handleProcessReturn(int $shopId): void
{
    $returnId = (int) ($_POST['return_id'] ?? $_POST['id'] ?? 0);
    $action = $_POST['process_action'] ?? 'approve';

    $return = Database::fetch("SELECT * FROM returns WHERE id = ? AND shop_id = ?", [$returnId, $shopId]);
    if (!$return) {
        echo json_encode(['success' => false, 'error' => 'Retoure nicht gefunden']);
        return;
    }

    $statusMap = [
        'approve' => 'approved',
        'ship' => 'shipped',
        'receive' => 'received',
        'refund' => 'refunded',
        'reject' => 'rejected'
    ];

    $newStatus = $statusMap[$action] ?? $return['status'];

    $updates = ['status' => $newStatus];
    if ($newStatus === 'refunded' || $newStatus === 'received') {
        $updates['processed_at'] = date('Y-m-d H:i:s');
    }

    // For refund, calculate refund amount from order if not set
    if ($newStatus === 'refunded' && empty($return['refund_amount'])) {
        $order = Database::fetch("SELECT grand_total FROM orders WHERE id = ?", [$return['order_id']]);
        $updates['refund_amount'] = (float) ($order['grand_total'] ?? 0);
    }

    Database::update('returns', $updates, 'id = ?', [$returnId]);

    $messages = [
        'approve' => 'Retoure genehmigt',
        'ship' => 'Als unterwegs markiert',
        'receive' => 'Retoure als erhalten markiert',
        'refund' => 'Rückerstattung durchgeführt',
        'reject' => 'Retoure abgelehnt'
    ];

    echo json_encode([
        'success' => true,
        'message' => $messages[$action] ?? 'Status aktualisiert',
        'new_status' => $newStatus
    ]);
}

// =====================================================================
// GET STATS (with period filter, return rate, reasons breakdown)
// =====================================================================
function handleGetStats(int $shopId): void
{
    $period = $_GET['period'] ?? 'week';

    // Build period date filter
    $periodStart = match ($period) {
        'week' => date('Y-m-d', strtotime('-7 days')),
        'month' => date('Y-m-d', strtotime('-30 days')),
        'year' => date('Y-m-d', strtotime('-365 days')),
        default => '1970-01-01'
    };

    // Open returns (not refunded/rejected)
    $openCount = Database::fetch("
        SELECT COUNT(*) as cnt FROM returns 
        WHERE shop_id = ? AND status NOT IN ('refunded', 'rejected')
    ", [$shopId]);

    // Active returns (same as open)
    $activeCount = Database::fetch("
        SELECT COUNT(*) as cnt FROM returns 
        WHERE shop_id = ? AND status NOT IN ('refunded', 'rejected')
    ", [$shopId]);

    // Total returns
    $totalCount = Database::fetch("
        SELECT COUNT(*) as cnt FROM returns WHERE shop_id = ?
    ", [$shopId]);

    // Returns in period
    $periodCount = Database::fetch("
        SELECT COUNT(*) as cnt FROM returns 
        WHERE shop_id = ? AND created_at >= ?
    ", [$shopId, $periodStart]);

    // Calculate return rate: returns / completed orders in period
    $ordersInPeriod = Database::fetch("
        SELECT COUNT(*) as cnt FROM orders 
        WHERE shop_id = ? AND status IN ('completed', 'delivered', 'shipped') AND created_at >= ?
    ", [$shopId, $periodStart]);

    $orderCount = max(1, (int) ($ordersInPeriod['cnt'] ?? 1));
    $returnCount = (int) ($periodCount['cnt'] ?? 0);
    $returnRate = min(100, ($returnCount / $orderCount) * 100); // Cap at 100%

    // Total refunds amount
    $refundSum = Database::fetch("
        SELECT COALESCE(SUM(refund_amount), 0) as total FROM returns 
        WHERE shop_id = ? AND status = 'refunded'
    ", [$shopId]);

    // Reasons breakdown for period
    $reasons = Database::fetchAll("
        SELECT reason, COUNT(*) as count
        FROM returns 
        WHERE shop_id = ? AND created_at >= ?
        GROUP BY reason
        ORDER BY count DESC
    ", [$shopId, $periodStart]);

    // Calculate percent for each reason
    $totalReasons = array_sum(array_column($reasons, 'count'));
    $reasonsWithPercent = [];
    foreach ($reasons as $r) {
        $reasonsWithPercent[] = [
            'reason' => $r['reason'] ?? 'other',
            'count' => (int) $r['count'],
            'percent' => $totalReasons > 0 ? ((int) $r['count'] / $totalReasons) * 100 : 0
        ];
    }

    echo json_encode([
        'success' => true,
        'stats' => [
            'open' => (int) ($openCount['cnt'] ?? 0),
            'active' => (int) ($activeCount['cnt'] ?? 0),
            'total' => (int) ($totalCount['cnt'] ?? 0),
            'period_count' => (int) ($periodCount['cnt'] ?? 0),
            'return_rate' => round($returnRate, 1),
            'total_refunds' => (float) ($refundSum['total'] ?? 0),
            'reasons' => $reasonsWithPercent
        ]
    ]);
}

// =====================================================================
// GENERATE RETURN LABEL
// =====================================================================
function handleGenerateReturnLabel(int $shopId): void
{
    $returnId = (int) ($_POST['id'] ?? 0);
    $carrierId = (int) ($_POST['carrier_id'] ?? 0);

    $return = Database::fetch("
        SELECT r.*, o.* 
        FROM returns r 
        JOIN orders o ON r.order_id = o.id 
        WHERE r.id = ? AND r.shop_id = ?
    ", [$returnId, $shopId]);

    if (!$return) {
        echo json_encode(['success' => false, 'error' => 'Retoure nicht gefunden']);
        return;
    }

    // Load carrier service
    require_once __DIR__ . '/../includes/CarrierService.php';
    $carrier = CarrierService::load($carrierId);

    if (!$carrier) {
        // Use local label generator
        require_once __DIR__ . '/../includes/LabelGenerator.php';
        $generator = new LabelGenerator();

        $labelData = $generator->generate([
            'shipment' => ['shipment_number' => $return['return_number']],
            'sender' => [ // Customer sends back
                'name' => ($return['billing_first_name'] ?? '') . ' ' . ($return['billing_last_name'] ?? ''),
                'street' => $return['billing_street'] ?? '',
                'city' => $return['billing_city'] ?? '',
                'postal_code' => $return['billing_postal_code'] ?? '',
                'country_code' => 'DE'
            ],
            'recipient' => [ // Shop receives
                'name' => 'Retouren-Abteilung',
                'company' => 'Mein Online Shop',
                'street' => 'Musterstraße 1',
                'city' => 'Berlin',
                'postal_code' => '10115',
                'country_code' => 'DE'
            ],
            'carrier' => 'Retoure',
            'tracking_number' => $return['return_number']
        ]);

        // Save label
        Database::insert('shipping_labels', [
            'return_id' => $returnId,
            'carrier_code' => 'local',
            'tracking_number' => $return['return_number'],
            'label_data' => base64_decode($labelData),
            'label_format' => 'PDF',
            'label_type' => 'return'
        ]);

        echo json_encode([
            'success' => true,
            'label_data' => $labelData,
            'label_format' => 'PDF',
            'message' => 'Retourenlabel generiert'
        ]);
        return;
    }

    // Use carrier API for label
    $result = $carrier->createLabel(
        ['shipment_number' => $return['return_number']],
        [ // Customer sends back
            'name' => ($return['billing_first_name'] ?? '') . ' ' . ($return['billing_last_name'] ?? ''),
            'street' => $return['billing_street'] ?? '',
            'city' => $return['billing_city'] ?? '',
            'postal_code' => $return['billing_postal_code'] ?? '',
            'country_code' => 'DE'
        ],
        [ // Shop receives
            'name' => 'Retouren-Abteilung',
            'company' => 'Mein Online Shop',
            'street' => 'Musterstraße 1',
            'city' => 'Berlin',
            'postal_code' => '10115',
            'country_code' => 'DE'
        ],
        [['weight' => 1]]
    );

    if ($result['success']) {
        Database::insert('shipping_labels', [
            'return_id' => $returnId,
            'carrier_code' => $carrier->getCode(),
            'tracking_number' => $result['tracking_number'] ?? '',
            'label_data' => base64_decode($result['label_data'] ?? ''),
            'label_format' => $result['label_format'] ?? 'PDF',
            'label_type' => 'return'
        ]);
    }

    echo json_encode($result);
}
