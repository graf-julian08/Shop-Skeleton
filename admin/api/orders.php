<?php
/**
 * Orders API
 * Complete CRUD operations for order management
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

Database::configure($database);
Auth::init();

// =====================================================================
// AUTO-MIGRATION: Create orders tables if they don't exist
// =====================================================================
try {
    // Orders table
    Database::query("
        CREATE TABLE IF NOT EXISTS orders (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            order_number VARCHAR(50) UNIQUE NOT NULL,
            customer_id BIGINT UNSIGNED NULL,
            subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
            shipping_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            grand_total DECIMAL(12,2) NOT NULL DEFAULT 0,
            currency_code VARCHAR(3) NOT NULL DEFAULT 'EUR',
            status ENUM('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
            payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
            payment_method VARCHAR(100),
            shipping_method VARCHAR(100),
            tracking_number VARCHAR(255),
            billing_address JSON,
            shipping_address JSON,
            customer_notes TEXT,
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_shop_status (shop_id, status),
            INDEX idx_customer (customer_id),
            INDEX idx_order_number (order_number),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Order items table
    Database::query("
        CREATE TABLE IF NOT EXISTS order_items (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NULL,
            variant_id BIGINT UNSIGNED NULL,
            sku VARCHAR(255),
            name VARCHAR(500) NOT NULL,
            options JSON,
            quantity INT UNSIGNED NOT NULL DEFAULT 1,
            unit_price DECIMAL(12,2) NOT NULL,
            total_price DECIMAL(12,2) NOT NULL,
            INDEX idx_order (order_id),
            INDEX idx_product (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Order status history table
    Database::query("
        CREATE TABLE IF NOT EXISTS order_status_history (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            status VARCHAR(50) NOT NULL,
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Auto-migration: Add missing columns to order_items if table already existed
    try {
        $columns = Database::fetchAll("SHOW COLUMNS FROM order_items LIKE 'unit_price'");
        if (empty($columns)) {
            Database::query("ALTER TABLE order_items ADD COLUMN unit_price DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER quantity");
        }
    } catch (Exception $e) {
    }

    try {
        $columns = Database::fetchAll("SHOW COLUMNS FROM order_items LIKE 'total_price'");
        if (empty($columns)) {
            Database::query("ALTER TABLE order_items ADD COLUMN total_price DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER unit_price");
        }
    } catch (Exception $e) {
    }

} catch (Exception $e) {
    // Tables might already exist
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_orders':
            handleGetOrders($shopId);
            break;
        case 'get_order':
            handleGetOrder($shopId);
            break;
        case 'get_stats':
            handleGetStats($shopId);
            break;
        case 'update_status':
            handleUpdateStatus($shopId);
            break;
        case 'update_payment_status':
            handleUpdatePaymentStatus($shopId);
            break;
        case 'export_orders':
            handleExportOrders($shopId);
            break;
        case 'generate_test_data':
            handleGenerateTestData($shopId);
            break;
        case 'reset_tables':
            handleResetTables();
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// RESET TABLES (drop and recreate with correct schema)
// =====================================================================
function handleResetTables(): void
{
    Database::query("SET FOREIGN_KEY_CHECKS=0");
    Database::query("DROP TABLE IF EXISTS order_status_history");
    Database::query("DROP TABLE IF EXISTS order_items");
    Database::query("DROP TABLE IF EXISTS orders");
    Database::query("SET FOREIGN_KEY_CHECKS=1");

    // Recreate orders table
    Database::query("
        CREATE TABLE orders (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            order_number VARCHAR(50) UNIQUE NOT NULL,
            customer_id BIGINT UNSIGNED NULL,
            subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
            shipping_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            grand_total DECIMAL(12,2) NOT NULL DEFAULT 0,
            currency_code VARCHAR(3) NOT NULL DEFAULT 'EUR',
            status ENUM('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
            payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
            payment_method VARCHAR(100),
            shipping_method VARCHAR(100),
            tracking_number VARCHAR(255),
            billing_address JSON,
            shipping_address JSON,
            customer_notes TEXT,
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_shop_status (shop_id, status),
            INDEX idx_customer (customer_id),
            INDEX idx_order_number (order_number),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Recreate order_items table
    Database::query("
        CREATE TABLE order_items (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NULL,
            variant_id BIGINT UNSIGNED NULL,
            sku VARCHAR(255),
            name VARCHAR(500) NOT NULL,
            options JSON,
            quantity INT UNSIGNED NOT NULL DEFAULT 1,
            unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
            total_price DECIMAL(12,2) NOT NULL DEFAULT 0,
            INDEX idx_order (order_id),
            INDEX idx_product (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Recreate order_status_history table
    Database::query("
        CREATE TABLE order_status_history (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            status VARCHAR(50) NOT NULL,
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    echo json_encode(['success' => true, 'message' => 'Tables reset successfully']);
}

// =====================================================================
// GET ORDERS (with filters, search, pagination, currency conversion)
// =====================================================================
function handleGetOrders(int $shopId): void
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 20)));
    $offset = ($page - 1) * $perPage;

    $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? '';
    $paymentStatus = $_GET['payment_status'] ?? '';
    $period = $_GET['period'] ?? '';
    $sortBy = $_GET['sort_by'] ?? 'created_at';
    $sortDir = strtoupper($_GET['sort_dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
    $displayCurrency = $_GET['display_currency'] ?? null;

    // Get shop default currency
    $shopCurrency = Database::fetch(
        "SELECT code, symbol FROM currencies WHERE shop_id = ? AND is_default = 1",
        [$shopId]
    );
    $defaultCurrencyCode = $shopCurrency['code'] ?? 'EUR';
    $defaultCurrencySymbol = $shopCurrency['symbol'] ?? '€';

    // Get display currency info
    $displayCurrencyCode = $displayCurrency ?: $defaultCurrencyCode;
    $displayCurrencyInfo = Database::fetch(
        "SELECT code, symbol, exchange_rate FROM currencies WHERE shop_id = ? AND code = ?",
        [$shopId, $displayCurrencyCode]
    );
    if (!$displayCurrencyInfo) {
        $displayCurrencyInfo = ['code' => $defaultCurrencyCode, 'symbol' => $defaultCurrencySymbol, 'exchange_rate' => 1];
    }

    // Get all exchange rates
    $currencies = Database::fetchAll("SELECT code, exchange_rate FROM currencies WHERE shop_id = ?", [$shopId]);
    $exchangeRates = [];
    foreach ($currencies as $c) {
        $exchangeRates[$c['code']] = (float) $c['exchange_rate'];
    }

    // Build WHERE clause
    $where = ["o.shop_id = ?"];
    $params = [$shopId];

    if ($search) {
        $where[] = "(o.order_number LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ?)";
        $searchTerm = "%{$search}%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }

    if ($status && $status !== 'all') {
        $where[] = "o.status = ?";
        $params[] = $status;
    }

    if ($paymentStatus && $paymentStatus !== 'all') {
        $where[] = "o.payment_status = ?";
        $params[] = $paymentStatus;
    }

    // Period filter
    $periodMap = [
        'today' => 'CURDATE()',
        '7d' => 'DATE_SUB(NOW(), INTERVAL 7 DAY)',
        '30d' => 'DATE_SUB(NOW(), INTERVAL 30 DAY)',
        'year' => 'DATE_SUB(NOW(), INTERVAL 1 YEAR)',
        'all' => null
    ];
    if (isset($periodMap[$period]) && $periodMap[$period]) {
        $where[] = "o.created_at >= " . $periodMap[$period];
    }

    $whereClause = implode(' AND ', $where);

    // Validate sort column
    $validSorts = ['order_number', 'grand_total', 'created_at', 'status'];
    if (!in_array($sortBy, $validSorts)) {
        $sortBy = 'created_at';
    }

    // Get total count
    $total = Database::fetch("SELECT COUNT(*) as count FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE {$whereClause}", $params);

    // Get orders
    $query = "
        SELECT o.*, 
               c.first_name, c.last_name, c.email,
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        WHERE {$whereClause}
        ORDER BY o.{$sortBy} {$sortDir}
        LIMIT ? OFFSET ?
    ";
    $orders = Database::fetchAll($query, array_merge($params, [$perPage, $offset]));

    // Convert prices to display currency
    foreach ($orders as &$order) {
        $orderCurrency = $order['currency_code'] ?: 'EUR';
        $grandTotal = (float) $order['grand_total'];

        // Convert to display currency
        $baseRate = $exchangeRates[$orderCurrency] ?? 1;
        $targetRate = $exchangeRates[$displayCurrencyCode] ?? 1;
        $order['display_total'] = ($grandTotal / $baseRate) * $targetRate;
        $order['display_currency'] = $displayCurrencyCode;
        $order['display_symbol'] = $displayCurrencyInfo['symbol'];

        // Customer name
        $order['customer_name'] = trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?: 'Gast';
    }

    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'currency' => [
            'code' => $displayCurrencyCode,
            'symbol' => $displayCurrencyInfo['symbol'],
            'default_code' => $defaultCurrencyCode,
            'default_symbol' => $defaultCurrencySymbol
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
// GET SINGLE ORDER
// =====================================================================
function handleGetOrder(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid order ID']);
        return;
    }

    $order = Database::fetch(
        "SELECT o.*, c.first_name, c.last_name, c.email, c.phone
         FROM orders o
         LEFT JOIN customers c ON o.customer_id = c.id
         WHERE o.id = ? AND o.shop_id = ?",
        [$id, $shopId]
    );

    if (!$order) {
        echo json_encode(['success' => false, 'error' => 'Order not found']);
        return;
    }

    // Get order items with product info
    $items = Database::fetchAll(
        "SELECT oi.*, p.slug as product_slug
         FROM order_items oi
         LEFT JOIN products p ON oi.product_id = p.id
         WHERE oi.order_id = ?",
        [$id]
    );

    // Get status history
    $history = Database::fetchAll(
        "SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at DESC",
        [$id]
    );

    // Parse JSON addresses
    $order['billing_address'] = $order['billing_address'] ? json_decode($order['billing_address'], true) : null;
    $order['shipping_address'] = $order['shipping_address'] ? json_decode($order['shipping_address'], true) : null;

    // Parse item options
    foreach ($items as &$item) {
        $item['options'] = $item['options'] ? json_decode($item['options'], true) : null;
    }

    $order['customer_name'] = trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?: 'Gast';
    $order['items'] = $items;
    $order['history'] = $history;

    echo json_encode([
        'success' => true,
        'order' => $order
    ]);
}

// =====================================================================
// GET STATS
// =====================================================================
function handleGetStats(int $shopId): void
{
    $stats = [
        'all' => 0,
        'pending' => 0,
        'processing' => 0,
        'shipped' => 0,
        'delivered' => 0,
        'cancelled' => 0,
        'payment_pending' => 0,
        'payment_paid' => 0
    ];

    // Status counts
    $statusCounts = Database::fetchAll(
        "SELECT status, COUNT(*) as count FROM orders WHERE shop_id = ? GROUP BY status",
        [$shopId]
    );
    $total = 0;
    foreach ($statusCounts as $row) {
        $stats[$row['status']] = (int) $row['count'];
        $total += (int) $row['count'];
    }
    $stats['all'] = $total;

    // Payment status counts
    $paymentCounts = Database::fetchAll(
        "SELECT payment_status, COUNT(*) as count FROM orders WHERE shop_id = ? GROUP BY payment_status",
        [$shopId]
    );
    foreach ($paymentCounts as $row) {
        $stats['payment_' . $row['payment_status']] = (int) $row['count'];
    }

    // Today's revenue
    $todayRevenue = Database::fetch(
        "SELECT COALESCE(SUM(grand_total), 0) as total FROM orders WHERE shop_id = ? AND payment_status = 'paid' AND DATE(created_at) = CURDATE()",
        [$shopId]
    );
    $stats['today_revenue'] = (float) $todayRevenue['total'];

    echo json_encode(['success' => true, 'stats' => $stats]);
}

// =====================================================================
// UPDATE ORDER STATUS
// =====================================================================
function handleUpdateStatus(int $shopId): void
{
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $newStatus = $_POST['status'] ?? '';
    $comment = trim($_POST['comment'] ?? '');

    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
    if (!in_array($newStatus, $validStatuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        return;
    }

    // Verify order exists
    $order = Database::fetch("SELECT id, status FROM orders WHERE id = ? AND shop_id = ?", [$orderId, $shopId]);
    if (!$order) {
        echo json_encode(['success' => false, 'error' => 'Order not found']);
        return;
    }

    // Update status
    Database::update('orders', [
        'status' => $newStatus,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$orderId]);

    // Add to history
    $statusLabels = [
        'pending' => 'Ausstehend',
        'processing' => 'In Bearbeitung',
        'shipped' => 'Versendet',
        'delivered' => 'Zugestellt',
        'cancelled' => 'Storniert',
        'refunded' => 'Erstattet'
    ];

    Database::insert('order_status_history', [
        'order_id' => $orderId,
        'status' => $statusLabels[$newStatus] ?? $newStatus,
        'comment' => $comment ?: "Status geändert zu: " . ($statusLabels[$newStatus] ?? $newStatus),
        'created_at' => date('Y-m-d H:i:s')
    ]);

    echo json_encode(['success' => true, 'message' => 'Status aktualisiert']);
}

// =====================================================================
// UPDATE PAYMENT STATUS
// =====================================================================
function handleUpdatePaymentStatus(int $shopId): void
{
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $newStatus = $_POST['payment_status'] ?? '';

    $validStatuses = ['pending', 'paid', 'failed', 'refunded'];
    if (!in_array($newStatus, $validStatuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid payment status']);
        return;
    }

    $order = Database::fetch("SELECT id FROM orders WHERE id = ? AND shop_id = ?", [$orderId, $shopId]);
    if (!$order) {
        echo json_encode(['success' => false, 'error' => 'Order not found']);
        return;
    }

    Database::update('orders', [
        'payment_status' => $newStatus,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$orderId]);

    $statusLabels = ['pending' => 'Ausstehend', 'paid' => 'Bezahlt', 'failed' => 'Fehlgeschlagen', 'refunded' => 'Erstattet'];

    Database::insert('order_status_history', [
        'order_id' => $orderId,
        'status' => 'Zahlung: ' . ($statusLabels[$newStatus] ?? $newStatus),
        'comment' => 'Zahlungsstatus geändert zu: ' . ($statusLabels[$newStatus] ?? $newStatus),
        'created_at' => date('Y-m-d H:i:s')
    ]);

    echo json_encode(['success' => true, 'message' => 'Zahlungsstatus aktualisiert']);
}

// =====================================================================
// EXPORT ORDERS
// =====================================================================
function handleExportOrders(int $shopId): void
{
    $format = $_GET['format'] ?? 'json';
    $status = $_GET['status'] ?? 'all';

    $where = ["shop_id = ?"];
    $params = [$shopId];

    if ($status !== 'all') {
        $where[] = "status = ?";
        $params[] = $status;
    }

    $orders = Database::fetchAll(
        "SELECT * FROM orders WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC",
        $params
    );

    if ($format === 'sql') {
        header('Content-Type: text/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename=orders_export_' . date('Y-m-d') . '.sql');

        echo "-- Orders Export " . date('Y-m-d H:i:s') . "\n";
        echo "-- Total orders: " . count($orders) . "\n\n";

        foreach ($orders as $order) {
            $values = [
                "'" . addslashes($order['order_number']) . "'",
                $order['customer_id'] !== null ? $order['customer_id'] : 'NULL',
                $order['subtotal'],
                $order['shipping_amount'],
                $order['tax_amount'],
                $order['discount_amount'],
                $order['grand_total'],
                "'" . addslashes($order['currency_code']) . "'",
                "'" . addslashes($order['status']) . "'",
                "'" . addslashes($order['payment_status']) . "'",
                $order['payment_method'] ? "'" . addslashes($order['payment_method']) . "'" : 'NULL',
                $order['shipping_method'] ? "'" . addslashes($order['shipping_method']) . "'" : 'NULL',
                $order['tracking_number'] ? "'" . addslashes($order['tracking_number']) . "'" : 'NULL',
                $order['billing_address'] ? "'" . addslashes($order['billing_address']) . "'" : 'NULL',
                $order['shipping_address'] ? "'" . addslashes($order['shipping_address']) . "'" : 'NULL',
                "'" . $order['created_at'] . "'"
            ];
            echo "INSERT INTO orders (order_number, customer_id, subtotal, shipping_amount, tax_amount, discount_amount, grand_total, currency_code, status, payment_status, payment_method, shipping_method, tracking_number, billing_address, shipping_address, created_at) VALUES (" . implode(', ', $values) . ");\n";
        }
        exit;
    }

    // JSON
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename=orders_export_' . date('Y-m-d') . '.json');
    echo json_encode(['orders' => $orders], JSON_PRETTY_PRINT);
    exit;
}

// =====================================================================
// GENERATE TEST DATA
// =====================================================================
function handleGenerateTestData(int $shopId): void
{
    $force = isset($_GET['force']) || isset($_POST['force']);

    // Check if orders already exist
    $existing = Database::fetch("SELECT COUNT(*) as count FROM orders WHERE shop_id = ?", [$shopId]);
    if ($existing['count'] > 0 && !$force) {
        echo json_encode(['success' => true, 'message' => 'Test data already exists', 'count' => $existing['count']]);
        return;
    }

    // If force, delete existing data
    if ($force && $existing['count'] > 0) {
        Database::query("DELETE FROM order_status_history WHERE order_id IN (SELECT id FROM orders WHERE shop_id = ?)", [$shopId]);
        Database::query("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE shop_id = ?)", [$shopId]);
        Database::query("DELETE FROM orders WHERE shop_id = ?", [$shopId]);
    }

    // Get real customers
    $customers = Database::fetchAll("SELECT id, first_name, last_name, email, phone FROM customers WHERE shop_id = ? LIMIT 10", [$shopId]);
    if (empty($customers)) {
        // Create minimal customer data if none exist
        $customers = [
            ['id' => null, 'first_name' => 'Max', 'last_name' => 'Mustermann', 'email' => 'max@example.com'],
            ['id' => null, 'first_name' => 'Anna', 'last_name' => 'Schmidt', 'email' => 'anna@example.com'],
        ];
    }

    // Get real products
    $products = Database::fetchAll("SELECT id, name, sku, price FROM products WHERE shop_id = ? AND status = 'active' LIMIT 20", [$shopId]);
    if (empty($products)) {
        $products = [
            ['id' => null, 'name' => 'Premium Kopfhörer', 'sku' => 'PROD-001', 'price' => 199.99],
            ['id' => null, 'name' => 'Bluetooth Lautsprecher', 'sku' => 'PROD-002', 'price' => 89.99],
            ['id' => null, 'name' => 'USB-C Kabel', 'sku' => 'PROD-003', 'price' => 14.99],
        ];
    }

    $paymentMethods = ['PayPal', 'Kreditkarte', 'SEPA Lastschrift', 'Klarna', 'Apple Pay'];
    $shippingMethods = ['DHL Standard', 'DHL Express', 'Hermes', 'DPD', 'UPS'];
    $statuses = ['pending', 'processing', 'shipped', 'delivered', 'pending', 'processing'];
    $paymentStatuses = ['paid', 'paid', 'paid', 'pending', 'paid', 'pending'];

    $addresses = [
        ['name' => 'Max Mustermann', 'street' => 'Musterstraße 123', 'city' => 'Berlin', 'zip' => '10115', 'country' => 'Deutschland'],
        ['name' => 'Anna Schmidt', 'street' => 'Hauptstraße 45', 'city' => 'München', 'zip' => '80331', 'country' => 'Deutschland'],
        ['name' => 'Thomas Weber', 'street' => 'Bahnhofstraße 78', 'city' => 'Hamburg', 'zip' => '20095', 'country' => 'Deutschland'],
        ['name' => 'Lisa Müller', 'street' => 'Schillerplatz 12', 'city' => 'Frankfurt', 'zip' => '60313', 'country' => 'Deutschland'],
    ];

    $createdOrders = 0;

    // Generate 15 orders
    for ($i = 0; $i < 15; $i++) {
        $customer = $customers[array_rand($customers)];
        $address = $addresses[array_rand($addresses)];
        $status = $statuses[array_rand($statuses)];
        $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

        // Random date within last 30 days
        $daysAgo = rand(0, 30);
        $orderDate = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days -" . rand(0, 23) . " hours -" . rand(0, 59) . " minutes"));

        $orderNumber = '#' . (10000 + $i + rand(1, 100));

        // Random items (1-4 products)
        $numItems = rand(1, 4);
        $orderItems = [];
        $subtotal = 0;

        for ($j = 0; $j < $numItems; $j++) {
            $product = $products[array_rand($products)];
            $qty = rand(1, 3);
            $price = (float) $product['price'];
            $total = $price * $qty;
            $subtotal += $total;

            $orderItems[] = [
                'product_id' => $product['id'],
                'sku' => $product['sku'],
                'name' => $product['name'],
                'quantity' => $qty,
                'unit_price' => $price,
                'total_price' => $total
            ];
        }

        $shipping = rand(0, 1) ? 0 : (rand(1, 3) * 4.99);
        $tax = round($subtotal * 0.19, 2);
        $grandTotal = $subtotal + $shipping + $tax;

        // Insert order
        $orderId = Database::insert('orders', [
            'shop_id' => $shopId,
            'order_number' => $orderNumber,
            'customer_id' => $customer['id'],
            'subtotal' => $subtotal,
            'shipping_amount' => $shipping,
            'tax_amount' => $tax,
            'grand_total' => $grandTotal,
            'currency_code' => 'EUR',
            'status' => $status,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethods[array_rand($paymentMethods)],
            'shipping_method' => $shippingMethods[array_rand($shippingMethods)],
            'billing_address' => json_encode($address),
            'shipping_address' => json_encode($address),
            'created_at' => $orderDate,
            'updated_at' => $orderDate
        ]);

        // Insert order items
        foreach ($orderItems as $item) {
            $item['order_id'] = $orderId;
            Database::insert('order_items', $item);
        }

        // Insert initial status history
        Database::insert('order_status_history', [
            'order_id' => $orderId,
            'status' => 'Bestellung aufgegeben',
            'comment' => 'Bestellung wurde erfolgreich aufgegeben',
            'created_at' => $orderDate
        ]);

        // Add payment history if paid
        if ($paymentStatus === 'paid') {
            $paymentDate = date('Y-m-d H:i:s', strtotime($orderDate . ' +' . rand(1, 30) . ' minutes'));
            Database::insert('order_status_history', [
                'order_id' => $orderId,
                'status' => 'Zahlung: Bezahlt',
                'comment' => 'Zahlung erfolgreich erhalten',
                'created_at' => $paymentDate
            ]);
        }

        // Add shipping history if shipped/delivered
        if (in_array($status, ['shipped', 'delivered'])) {
            $shipDate = date('Y-m-d H:i:s', strtotime($orderDate . ' +' . rand(1, 3) . ' days'));
            Database::insert('order_status_history', [
                'order_id' => $orderId,
                'status' => 'Versendet',
                'comment' => 'Paket wurde versendet',
                'created_at' => $shipDate
            ]);

            if ($status === 'delivered') {
                $deliverDate = date('Y-m-d H:i:s', strtotime($shipDate . ' +' . rand(1, 5) . ' days'));
                Database::insert('order_status_history', [
                    'order_id' => $orderId,
                    'status' => 'Zugestellt',
                    'comment' => 'Paket wurde zugestellt',
                    'created_at' => $deliverDate
                ]);
            }
        }

        $createdOrders++;
    }

    echo json_encode(['success' => true, 'message' => "{$createdOrders} Testbestellungen erstellt"]);
}
