<?php
/**
 * Fulfillment API
 * Enterprise-level shipment, carrier, and picklist management
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Mailer.php';

Database::configure($database);
Auth::init();

// =====================================================================
// AUTO-MIGRATION: Create fulfillment tables
// =====================================================================
try {
    // Carriers table
    Database::query("
        CREATE TABLE IF NOT EXISTS carriers (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(50) NOT NULL,
            logo_url VARCHAR(500),
            tracking_url_template VARCHAR(500),
            api_endpoint VARCHAR(500),
            api_key VARCHAR(500),
            api_secret VARCHAR(500),
            account_number VARCHAR(100),
            is_active TINYINT(1) DEFAULT 1,
            is_default TINYINT(1) DEFAULT 0,
            settings JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_shop (shop_id),
            INDEX idx_code (code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Shipments table
    Database::query("
        CREATE TABLE IF NOT EXISTS shipments (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            order_id BIGINT UNSIGNED NOT NULL,
            shipment_number VARCHAR(50) NOT NULL,
            warehouse_id BIGINT UNSIGNED,
            carrier_id BIGINT UNSIGNED,
            tracking_number VARCHAR(255),
            status ENUM('pending','picking','packed','shipped','in_transit','out_for_delivery','delivered','failed','returned') DEFAULT 'pending',
            weight DECIMAL(10,3),
            dimensions JSON,
            shipping_cost DECIMAL(12,2),
            label_url VARCHAR(500),
            notes TEXT,
            shipped_at TIMESTAMP NULL,
            delivered_at TIMESTAMP NULL,
            estimated_delivery DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_shop (shop_id),
            INDEX idx_order (order_id),
            INDEX idx_status (status),
            INDEX idx_tracking (tracking_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Shipment items table
    Database::query("
        CREATE TABLE IF NOT EXISTS shipment_items (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shipment_id BIGINT UNSIGNED NOT NULL,
            order_item_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED,
            sku VARCHAR(255),
            name VARCHAR(500),
            quantity INT UNSIGNED NOT NULL,
            picked TINYINT(1) DEFAULT 0,
            packed TINYINT(1) DEFAULT 0,
            INDEX idx_shipment (shipment_id),
            INDEX idx_order_item (order_item_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Picklists table
    Database::query("
        CREATE TABLE IF NOT EXISTS picklists (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            picklist_number VARCHAR(50) NOT NULL,
            warehouse_id BIGINT UNSIGNED,
            status ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
            created_by VARCHAR(100),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            completed_at TIMESTAMP NULL,
            INDEX idx_shop (shop_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Picklist items table
    Database::query("
        CREATE TABLE IF NOT EXISTS picklist_items (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            picklist_id BIGINT UNSIGNED NOT NULL,
            shipment_id BIGINT UNSIGNED,
            order_id BIGINT UNSIGNED,
            product_id BIGINT UNSIGNED,
            sku VARCHAR(255),
            product_name VARCHAR(500),
            quantity INT UNSIGNED NOT NULL,
            picked_quantity INT UNSIGNED DEFAULT 0,
            location VARCHAR(100),
            INDEX idx_picklist (picklist_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Shipment status history
    Database::query("
        CREATE TABLE IF NOT EXISTS shipment_status_history (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shipment_id BIGINT UNSIGNED NOT NULL,
            status VARCHAR(50) NOT NULL,
            location VARCHAR(255),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_shipment (shipment_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

} catch (Exception $e) {
    // Tables might already exist
}

// =====================================================================
// AUTO-MIGRATION: Add missing columns to existing shipments table
// =====================================================================
try {
    // Check and add shop_id column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'shop_id'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN shop_id BIGINT UNSIGNED NOT NULL DEFAULT 1 AFTER id");
        Database::query("ALTER TABLE shipments ADD INDEX idx_shop (shop_id)");
    }

    // Check and add shipment_number column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'shipment_number'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN shipment_number VARCHAR(50) NOT NULL DEFAULT '' AFTER order_id");
    }

    // Check and add warehouse_id column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'warehouse_id'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN warehouse_id BIGINT UNSIGNED NULL AFTER shipment_number");
    }

    // Check and add carrier_id column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'carrier_id'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN carrier_id BIGINT UNSIGNED NULL AFTER warehouse_id");
    }

    // Check and add weight column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'weight'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN weight DECIMAL(10,3) NULL");
    }

    // Check and add dimensions column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'dimensions'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN dimensions JSON NULL");
    }

    // Check and add shipping_cost column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'shipping_cost'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN shipping_cost DECIMAL(12,2) NULL");
    }

    // Check and add label_url column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'label_url'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN label_url VARCHAR(500) NULL");
    }

    // Check and add notes column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'notes'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN notes TEXT NULL");
    }

    // Check and add estimated_delivery column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'estimated_delivery'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN estimated_delivery DATE NULL");
    }

    // Check and add updated_at column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipments LIKE 'updated_at'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipments ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

} catch (Exception $e) {
    // Migration errors are non-fatal
}

// =====================================================================
// AUTO-MIGRATION: Add missing columns to existing shipment_items table
// =====================================================================
try {
    // Check and add id column (primary key)
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipment_items LIKE 'id'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipment_items ADD COLUMN id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT FIRST");
    }

    // Check and add product_id column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipment_items LIKE 'product_id'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipment_items ADD COLUMN product_id BIGINT UNSIGNED NULL AFTER order_item_id");
    }

    // Check and add sku column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipment_items LIKE 'sku'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipment_items ADD COLUMN sku VARCHAR(255) NULL AFTER product_id");
    }

    // Check and add name column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipment_items LIKE 'name'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipment_items ADD COLUMN name VARCHAR(500) NULL AFTER sku");
    }

    // Check and add picked column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipment_items LIKE 'picked'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipment_items ADD COLUMN picked TINYINT(1) DEFAULT 0");
    }

    // Check and add packed column
    $cols = Database::fetchAll("SHOW COLUMNS FROM shipment_items LIKE 'packed'");
    if (empty($cols)) {
        Database::query("ALTER TABLE shipment_items ADD COLUMN packed TINYINT(1) DEFAULT 0");
    }

} catch (Exception $e) {
    // Migration errors are non-fatal
}

// =====================================================================
// INSERT DEFAULT CARRIERS IF NONE EXIST
// =====================================================================
try {
    $carrierCount = Database::fetch("SELECT COUNT(*) as cnt FROM carriers WHERE shop_id = 1");
    if ((int) ($carrierCount['cnt'] ?? 0) === 0) {
        $defaultCarriers = [
            ['DHL', 'dhl', 'https://www.dhl.de/img/logos/dhl-logo.svg', 'https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?piececode={tracking}', 'https://api-eu.dhl.com', 1, 1],
            ['DPD', 'dpd', 'https://www.dpd.com/de/wp-content/uploads/sites/77/2019/04/dpd-logo.svg', 'https://tracking.dpd.de/status/de_DE/parcel/{tracking}', 'https://api.dpd.com', 1, 0],
            ['UPS', 'ups', 'https://www.ups.com/assets/resources/webcontent/images/ups-logo.svg', 'https://www.ups.com/track?tracknum={tracking}', 'https://onlinetools.ups.com/api', 1, 0],
            ['Hermes', 'hermes', 'https://www.myhermes.de/content/dam/myhermes/images/logo.svg', 'https://www.myhermes.de/empfangen/sendungsverfolgung/sendungsinformation/{tracking}', 'https://api.hermesworld.com', 1, 0],
            ['Swiss Post', 'swisspost', 'https://www.post.ch/-/media/post/logos/post-logo.svg', 'https://www.post.ch/swisspost-tracking?formattedParcelCodes={tracking}', 'https://wedec.post.ch/api', 1, 0],
            ['GLS', 'gls', 'https://gls-group.eu/DE/media/logos/gls-logo.svg', 'https://gls-group.eu/DE/de/paketverfolgung?match={tracking}', 'https://api.gls-group.eu', 1, 0],
            ['FedEx', 'fedex', 'https://www.fedex.com/content/dam/fedex/us-united-states/FedEx-Logo.png', 'https://www.fedex.com/fedextrack/?trknbr={tracking}', 'https://apis.fedex.com', 1, 0],
            ['Austrian Post', 'austrianpost', 'https://www.post.at/static/images/logo.svg', 'https://www.post.at/sv/sendungsverfolgung?pnum={tracking}', 'https://api.post.at', 1, 0]
        ];

        foreach ($defaultCarriers as $c) {
            Database::insert('carriers', [
                'shop_id' => 1,
                'name' => $c[0],
                'code' => $c[1],
                'logo_url' => $c[2],
                'tracking_url_template' => $c[3],
                'api_endpoint' => $c[4],
                'is_active' => $c[5],
                'is_default' => $c[6],
                'settings' => json_encode(['test_mode' => true])
            ]);
        }
    }
} catch (Exception $e) {
}

// =====================================================================
// ROUTE ACTIONS
// =====================================================================
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        // Stats & Dashboard
        case 'get_stats':
            handleGetStats($shopId);
            break;

        // Pending Orders (ready to ship)
        case 'get_pending_orders':
            handleGetPendingOrders($shopId);
            break;

        // Shipments
        case 'get_shipments':
            handleGetShipments($shopId);
            break;
        case 'get_shipment':
            handleGetShipment($shopId);
            break;
        case 'create_shipment':
            handleCreateShipment($shopId);
            break;
        case 'update_shipment_status':
            handleUpdateShipmentStatus($shopId);
            break;
        case 'assign_tracking':
            handleAssignTracking($shopId);
            break;
        case 'mark_shipped':
            handleMarkShipped($shopId);
            break;

        // Carriers
        case 'get_carriers':
            handleGetCarriers($shopId);
            break;
        case 'update_carrier':
            handleUpdateCarrier($shopId);
            break;
        case 'toggle_carrier':
            handleToggleCarrier($shopId);
            break;
        case 'set_default_carrier':
            handleSetDefaultCarrier($shopId);
            break;

        // Picklists
        case 'generate_picklist':
            handleGeneratePicklist($shopId);
            break;
        case 'get_picklist':
            handleGetPicklist($shopId);
            break;
        case 'update_picklist_item':
            handleUpdatePicklistItem($shopId);
            break;

        // Test Data
        case 'generate_test_data':
            handleGenerateTestData($shopId);
            break;

        // Advanced Features
        case 'bulk_ship':
            handleBulkShip($shopId);
            break;
        case 'generate_label':
            handleGenerateLabel($shopId);
            break;
        case 'print_picklist':
            handlePrintPicklist($shopId);
            break;
        case 'get_picklists':
            handleGetPicklists($shopId);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// GET STATS (KPI Cards)
// =====================================================================
function handleGetStats(int $shopId): void
{
    // Pending orders (paid but not shipped) - same logic as get_pending_orders
    $pending = Database::fetch(
        "SELECT COUNT(*) as cnt FROM orders o 
         WHERE o.shop_id = ? 
         AND o.payment_status = 'paid' 
         AND o.status IN ('pending', 'processing')
         AND NOT EXISTS (
             SELECT 1 FROM shipments s 
             WHERE s.order_id = o.id 
             AND s.status NOT IN ('failed', 'returned')
         )",
        [$shopId]
    );

    // Shipped today
    $shippedToday = Database::fetch(
        "SELECT COUNT(*) as cnt FROM shipments WHERE shop_id = ? AND DATE(shipped_at) = CURDATE()",
        [$shopId]
    );

    // In transit
    $inTransit = Database::fetch(
        "SELECT COUNT(*) as cnt FROM shipments WHERE shop_id = ? AND status IN ('shipped', 'in_transit', 'out_for_delivery')",
        [$shopId]
    );

    // Delivered today
    $deliveredToday = Database::fetch(
        "SELECT COUNT(*) as cnt FROM shipments WHERE shop_id = ? AND DATE(delivered_at) = CURDATE()",
        [$shopId]
    );

    // Problems (failed shipments)
    $problems = Database::fetch(
        "SELECT COUNT(*) as cnt FROM shipments WHERE shop_id = ? AND status IN ('failed', 'returned')",
        [$shopId]
    );

    // Total shipments count for badge
    $totalShipments = Database::fetch(
        "SELECT COUNT(*) as cnt FROM shipments WHERE shop_id = ?",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'stats' => [
            'pending' => (int) ($pending['cnt'] ?? 0),
            'shipped_today' => (int) ($shippedToday['cnt'] ?? 0),
            'in_transit' => (int) ($inTransit['cnt'] ?? 0),
            'delivered_today' => (int) ($deliveredToday['cnt'] ?? 0),
            'problems' => (int) ($problems['cnt'] ?? 0),
            'total_shipments' => (int) ($totalShipments['cnt'] ?? 0)
        ]
    ]);
}

// =====================================================================
// GET PENDING ORDERS (Ready to Ship)
// =====================================================================
function handleGetPendingOrders(int $shopId): void
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 25)));
    $offset = ($page - 1) * $perPage;
    $search = trim($_GET['search'] ?? '');
    $warehouseId = (int) ($_GET['warehouse_id'] ?? 0);

    $where = ["o.shop_id = ?", "o.payment_status = 'paid'", "o.status IN ('pending', 'processing')"];
    $params = [$shopId];

    // Exclude orders that have any shipment (not failed/returned)
    // A fully shipped order should not appear in pending list
    $where[] = "NOT EXISTS (
        SELECT 1 FROM shipments s 
        WHERE s.order_id = o.id 
        AND s.status NOT IN ('failed', 'returned')
    )";

    if ($search) {
        $where[] = "(o.order_number LIKE ? OR o.shipping_address->>'$.name' LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    $whereClause = implode(' AND ', $where);

    // Count
    $countResult = Database::fetch("SELECT COUNT(*) as total FROM orders o WHERE {$whereClause}", $params);
    $total = (int) ($countResult['total'] ?? 0);

    // Get orders with items
    $orders = Database::fetchAll("
        SELECT 
            o.id, o.order_number, o.customer_id, o.grand_total, o.currency_code,
            o.shipping_method, o.shipping_address, o.created_at,
            (SELECT CONCAT(c.first_name, ' ', c.last_name) FROM customers c WHERE c.id = o.customer_id) as customer_name,
            (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count,
            (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) as total_items
        FROM orders o
        WHERE {$whereClause}
        ORDER BY o.created_at ASC
        LIMIT ? OFFSET ?
    ", array_merge($params, [$perPage, $offset]));

    // Get items for each order
    foreach ($orders as &$order) {
        $order['items'] = Database::fetchAll(
            "SELECT oi.*, p.sku as product_sku FROM order_items oi 
             LEFT JOIN products p ON p.id = oi.product_id 
             WHERE oi.order_id = ?",
            [$order['id']]
        );
        $order['shipping_address'] = json_decode($order['shipping_address'], true);
    }

    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ]
    ]);
}

// =====================================================================
// GET SHIPMENTS (with filters)
// =====================================================================
function handleGetShipments(int $shopId): void
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 25)));
    $offset = ($page - 1) * $perPage;
    $status = $_GET['status'] ?? '';
    $carrierId = (int) ($_GET['carrier_id'] ?? 0);
    $search = trim($_GET['search'] ?? '');

    $where = ["s.shop_id = ?"];
    $params = [$shopId];

    if ($status && $status !== 'all') {
        $where[] = "s.status = ?";
        $params[] = $status;
    }
    if ($carrierId > 0) {
        $where[] = "s.carrier_id = ?";
        $params[] = $carrierId;
    }
    if ($search) {
        $where[] = "(s.shipment_number LIKE ? OR s.tracking_number LIKE ? OR o.order_number LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    $whereClause = implode(' AND ', $where);

    try {
        $countResult = Database::fetch("
            SELECT COUNT(*) as total FROM shipments s 
            LEFT JOIN orders o ON o.id = s.order_id 
            WHERE {$whereClause}
        ", $params);
        $total = (int) ($countResult['total'] ?? 0);

        $shipments = Database::fetchAll("
            SELECT 
                s.*,
                o.order_number, o.grand_total, o.currency_code, o.shipping_address,
                c.name as carrier_name, c.code as carrier_code, c.logo_url as carrier_logo,
                c.tracking_url_template,
                w.name as warehouse_name,
                (SELECT COUNT(*) FROM shipment_items WHERE shipment_id = s.id) as item_count
            FROM shipments s
            LEFT JOIN orders o ON o.id = s.order_id
            LEFT JOIN carriers c ON c.id = s.carrier_id
            LEFT JOIN warehouses w ON w.id = s.warehouse_id
            WHERE {$whereClause}
            ORDER BY s.created_at DESC
            LIMIT ? OFFSET ?
        ", array_merge($params, [$perPage, $offset]));

        foreach ($shipments as &$shipment) {
            $shipment['shipping_address'] = json_decode($shipment['shipping_address'], true);
            $shipment['tracking_url'] = $shipment['tracking_number'] && $shipment['tracking_url_template']
                ? str_replace('{tracking}', $shipment['tracking_number'], $shipment['tracking_url_template'])
                : null;
        }

        echo json_encode([
            'success' => true,
            'shipments' => $shipments,
            'pagination' => ['page' => $page, 'per_page' => $perPage, 'total' => $total, 'total_pages' => ceil($total / $perPage)]
        ]);
    } catch (Exception $e) {
        // Tables might not exist yet
        echo json_encode(['success' => true, 'shipments' => [], 'pagination' => ['page' => 1, 'per_page' => $perPage, 'total' => 0, 'total_pages' => 0]]);
    }
}

// =====================================================================
// GET SINGLE SHIPMENT
// =====================================================================
function handleGetShipment(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);

    $shipment = Database::fetch("
        SELECT s.*, o.order_number, o.grand_total, o.currency_code, o.shipping_address, o.billing_address,
               c.name as carrier_name, c.code as carrier_code, c.tracking_url_template,
               w.name as warehouse_name
        FROM shipments s
        LEFT JOIN orders o ON o.id = s.order_id
        LEFT JOIN carriers c ON c.id = s.carrier_id
        LEFT JOIN warehouses w ON w.id = s.warehouse_id
        WHERE s.id = ? AND s.shop_id = ?
    ", [$id, $shopId]);

    if (!$shipment) {
        echo json_encode(['success' => false, 'error' => 'Sendung nicht gefunden']);
        return;
    }

    $shipment['items'] = Database::fetchAll("SELECT * FROM shipment_items WHERE shipment_id = ?", [$id]);
    $shipment['history'] = Database::fetchAll("SELECT * FROM shipment_status_history WHERE shipment_id = ? ORDER BY created_at DESC", [$id]);
    $shipment['shipping_address'] = json_decode($shipment['shipping_address'], true);
    $shipment['tracking_url'] = $shipment['tracking_number'] && $shipment['tracking_url_template']
        ? str_replace('{tracking}', $shipment['tracking_number'], $shipment['tracking_url_template'])
        : null;

    echo json_encode(['success' => true, 'shipment' => $shipment]);
}

// =====================================================================
// CREATE SHIPMENT (supports partial)
// =====================================================================
function handleCreateShipment(int $shopId): void
{
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $warehouseId = (int) ($_POST['warehouse_id'] ?? 0);
    $carrierId = (int) ($_POST['carrier_id'] ?? 0);
    $items = $_POST['items'] ?? []; // [{order_item_id, quantity}]

    if (is_string($items))
        $items = json_decode($items, true);

    if ($orderId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Bestell-ID']);
        return;
    }

    // Verify order exists
    $order = Database::fetch("SELECT * FROM orders WHERE id = ? AND shop_id = ?", [$orderId, $shopId]);
    if (!$order) {
        echo json_encode(['success' => false, 'error' => 'Bestellung nicht gefunden']);
        return;
    }

    // Get default carrier if not specified
    if ($carrierId <= 0) {
        $defaultCarrier = Database::fetch("SELECT id FROM carriers WHERE shop_id = ? AND is_default = 1 AND is_active = 1", [$shopId]);
        $carrierId = $defaultCarrier ? (int) $defaultCarrier['id'] : 0;
    }

    // Get default warehouse if not specified
    if ($warehouseId <= 0) {
        $defaultWarehouse = Database::fetch("SELECT id FROM warehouses WHERE shop_id = ? AND is_default = 1", [$shopId]);
        $warehouseId = $defaultWarehouse ? (int) $defaultWarehouse['id'] : 0;
    }

    // Generate shipment number
    $shipmentNumber = 'SHP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Create shipment
    $shipmentId = Database::insert('shipments', [
        'shop_id' => $shopId,
        'order_id' => $orderId,
        'shipment_number' => $shipmentNumber,
        'warehouse_id' => $warehouseId ?: null,
        'carrier_id' => $carrierId ?: null,
        'status' => 'pending',
        'estimated_delivery' => date('Y-m-d', strtotime('+3 days'))
    ]);

    // Add items
    if (empty($items)) {
        // Ship all items from order
        $orderItems = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
        foreach ($orderItems as $item) {
            Database::insert('shipment_items', [
                'shipment_id' => $shipmentId,
                'order_item_id' => $item['id'],
                'product_id' => $item['product_id'],
                'sku' => $item['sku'],
                'name' => $item['name'],
                'quantity' => $item['quantity']
            ]);
        }
    } else {
        // Partial shipment
        foreach ($items as $item) {
            $orderItem = Database::fetch("SELECT * FROM order_items WHERE id = ? AND order_id = ?", [$item['order_item_id'], $orderId]);
            if ($orderItem) {
                Database::insert('shipment_items', [
                    'shipment_id' => $shipmentId,
                    'order_item_id' => $orderItem['id'],
                    'product_id' => $orderItem['product_id'],
                    'sku' => $orderItem['sku'],
                    'name' => $orderItem['name'],
                    'quantity' => min($item['quantity'], $orderItem['quantity'])
                ]);
            }
        }
    }

    // Add history entry
    Database::insert('shipment_status_history', [
        'shipment_id' => $shipmentId,
        'status' => 'Sendung erstellt',
        'comment' => 'Sendung wurde angelegt'
    ]);

    // Update order status to processing
    Database::update('orders', ['status' => 'processing'], 'id = ?', [$orderId]);

    echo json_encode(['success' => true, 'shipment_id' => $shipmentId, 'shipment_number' => $shipmentNumber]);
}

// =====================================================================
// UPDATE SHIPMENT STATUS
// =====================================================================
function handleUpdateShipmentStatus(int $shopId): void
{
    $shipmentId = (int) ($_POST['shipment_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $comment = trim($_POST['comment'] ?? '');
    $location = trim($_POST['location'] ?? '');

    $validStatuses = ['pending', 'picking', 'packed', 'shipped', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'error' => 'Ungültiger Status']);
        return;
    }

    $shipment = Database::fetch("SELECT * FROM shipments WHERE id = ? AND shop_id = ?", [$shipmentId, $shopId]);
    if (!$shipment) {
        echo json_encode(['success' => false, 'error' => 'Sendung nicht gefunden']);
        return;
    }

    $updates = ['status' => $status];
    if ($status === 'shipped' && !$shipment['shipped_at'])
        $updates['shipped_at'] = date('Y-m-d H:i:s');
    if ($status === 'delivered' && !$shipment['delivered_at'])
        $updates['delivered_at'] = date('Y-m-d H:i:s');

    Database::update('shipments', $updates, 'id = ?', [$shipmentId]);

    // Status labels
    $statusLabels = [
        'pending' => 'Ausstehend',
        'picking' => 'Kommissionierung',
        'packed' => 'Verpackt',
        'shipped' => 'Versendet',
        'in_transit' => 'Im Transit',
        'out_for_delivery' => 'In Zustellung',
        'delivered' => 'Zugestellt',
        'failed' => 'Fehlgeschlagen',
        'returned' => 'Zurückgesendet'
    ];

    Database::insert('shipment_status_history', [
        'shipment_id' => $shipmentId,
        'status' => $statusLabels[$status] ?? $status,
        'location' => $location,
        'comment' => $comment
    ]);

    // Update order if all shipments delivered
    if ($status === 'delivered') {
        $orderId = $shipment['order_id'];
        $allDelivered = Database::fetch("SELECT COUNT(*) as cnt FROM shipments WHERE order_id = ? AND status != 'delivered'", [$orderId]);
        if ((int) $allDelivered['cnt'] === 0) {
            Database::update('orders', ['status' => 'delivered'], 'id = ?', [$orderId]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Status aktualisiert']);
}

// =====================================================================
// ASSIGN TRACKING NUMBER
// =====================================================================
function handleAssignTracking(int $shopId): void
{
    $shipmentId = (int) ($_POST['shipment_id'] ?? 0);
    $trackingNumber = trim($_POST['tracking_number'] ?? '');

    if (empty($trackingNumber)) {
        echo json_encode(['success' => false, 'error' => 'Tracking-Nummer erforderlich']);
        return;
    }

    $shipment = Database::fetch("SELECT * FROM shipments WHERE id = ? AND shop_id = ?", [$shipmentId, $shopId]);
    if (!$shipment) {
        echo json_encode(['success' => false, 'error' => 'Sendung nicht gefunden']);
        return;
    }

    Database::update('shipments', ['tracking_number' => $trackingNumber], 'id = ?', [$shipmentId]);

    Database::insert('shipment_status_history', [
        'shipment_id' => $shipmentId,
        'status' => 'Tracking zugewiesen',
        'comment' => "Tracking-Nummer: {$trackingNumber}"
    ]);

    // Also update order tracking
    Database::update('orders', ['tracking_number' => $trackingNumber], 'id = ?', [$shipment['order_id']]);

    echo json_encode(['success' => true, 'message' => 'Tracking-Nummer zugewiesen']);
}

// =====================================================================
// MARK AS SHIPPED (quick action)
// =====================================================================
function handleMarkShipped(int $shopId): void
{
    $shipmentId = (int) ($_POST['shipment_id'] ?? 0);
    $trackingNumber = trim($_POST['tracking_number'] ?? '');
    $sendEmail = filter_var($_POST['send_email'] ?? true, FILTER_VALIDATE_BOOLEAN);

    $shipment = Database::fetch("SELECT * FROM shipments WHERE id = ? AND shop_id = ?", [$shipmentId, $shopId]);
    if (!$shipment) {
        echo json_encode(['success' => false, 'error' => 'Sendung nicht gefunden']);
        return;
    }

    $updates = ['status' => 'shipped', 'shipped_at' => date('Y-m-d H:i:s')];
    if ($trackingNumber)
        $updates['tracking_number'] = $trackingNumber;

    Database::update('shipments', $updates, 'id = ?', [$shipmentId]);
    Database::update('orders', ['status' => 'shipped', 'tracking_number' => $trackingNumber ?: $shipment['tracking_number']], 'id = ?', [$shipment['order_id']]);

    Database::insert('shipment_status_history', [
        'shipment_id' => $shipmentId,
        'status' => 'Versendet',
        'comment' => $trackingNumber ? "Tracking: {$trackingNumber}" : 'Sendung wurde versendet'
    ]);

    // Send email notification if enabled
    $emailSent = false;
    if ($sendEmail) {
        try {
            // Get order and customer data
            $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$shipment['order_id']]);
            $customer = null;
            if ($order && !empty($order['customer_id'])) {
                $customer = Database::fetch("SELECT * FROM customers WHERE id = ?", [$order['customer_id']]);
            }

            // Use order email if no customer
            if (!$customer && $order) {
                $customer = [
                    'email' => $order['email'] ?? $order['billing_email'] ?? '',
                    'first_name' => $order['billing_first_name'] ?? 'Kunde',
                    'last_name' => $order['billing_last_name'] ?? ''
                ];
            }

            // Get carrier name
            $carrier = null;
            if (!empty($shipment['carrier_id'])) {
                $carrier = Database::fetch("SELECT name FROM carriers WHERE id = ?", [$shipment['carrier_id']]);
            }

            // Merge updated data into shipment for email
            $shipmentForEmail = array_merge($shipment, $updates);
            if ($carrier) {
                $shipmentForEmail['carrier_name'] = $carrier['name'];
            }

            if ($customer && !empty($customer['email'])) {
                $result = Mailer::sendShipmentNotification($shipmentForEmail, $order, $customer);
                $emailSent = $result['success'] ?? false;
            }
        } catch (Exception $e) {
            // Email failed, but shipment was still marked
            error_log("Shipment email failed: " . $e->getMessage());
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Sendung als versendet markiert' . ($emailSent ? ' - E-Mail versandt' : ''),
        'email_sent' => $emailSent
    ]);
}

// =====================================================================
// GET CARRIERS
// =====================================================================
function handleGetCarriers(int $shopId): void
{
    $carriers = Database::fetchAll("SELECT * FROM carriers WHERE shop_id = ? ORDER BY is_default DESC, name ASC", [$shopId]);

    // Count shipments per carrier (with fallback if table doesn't exist)
    foreach ($carriers as &$carrier) {
        try {
            $count = Database::fetch("SELECT COUNT(*) as cnt FROM shipments WHERE carrier_id = ?", [$carrier['id']]);
            $carrier['shipment_count'] = (int) ($count['cnt'] ?? 0);
        } catch (Exception $e) {
            $carrier['shipment_count'] = 0;
        }
        $carrier['settings'] = json_decode($carrier['settings'], true) ?: [];
        $carrier['has_api_key'] = !empty($carrier['api_key']);
    }

    echo json_encode(['success' => true, 'carriers' => $carriers]);
}

// =====================================================================
// UPDATE CARRIER
// =====================================================================
function handleUpdateCarrier(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $apiKey = trim($_POST['api_key'] ?? '');
    $apiSecret = trim($_POST['api_secret'] ?? '');
    $accountNumber = trim($_POST['account_number'] ?? '');
    $settings = $_POST['settings'] ?? [];

    if (is_string($settings))
        $settings = json_decode($settings, true);

    $carrier = Database::fetch("SELECT * FROM carriers WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if (!$carrier) {
        echo json_encode(['success' => false, 'error' => 'Carrier nicht gefunden']);
        return;
    }

    $updates = ['settings' => json_encode($settings ?: [])];
    if ($apiKey)
        $updates['api_key'] = $apiKey;
    if ($apiSecret)
        $updates['api_secret'] = $apiSecret;
    if ($accountNumber)
        $updates['account_number'] = $accountNumber;

    Database::update('carriers', $updates, 'id = ?', [$id]);

    echo json_encode(['success' => true, 'message' => 'Carrier aktualisiert']);
}

// =====================================================================
// TOGGLE CARRIER ACTIVE
// =====================================================================
function handleToggleCarrier(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    $carrier = Database::fetch("SELECT * FROM carriers WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if (!$carrier) {
        echo json_encode(['success' => false, 'error' => 'Carrier nicht gefunden']);
        return;
    }

    Database::update('carriers', ['is_active' => $carrier['is_active'] ? 0 : 1], 'id = ?', [$id]);

    echo json_encode(['success' => true, 'is_active' => !$carrier['is_active']]);
}

// =====================================================================
// SET DEFAULT CARRIER
// =====================================================================
function handleSetDefaultCarrier(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    Database::update('carriers', ['is_default' => 0], 'shop_id = ?', [$shopId]);
    Database::update('carriers', ['is_default' => 1], 'id = ? AND shop_id = ?', [$id, $shopId]);

    echo json_encode(['success' => true, 'message' => 'Standard-Carrier gesetzt']);
}

// =====================================================================
// GENERATE PICKLIST
// =====================================================================
function handleGeneratePicklist(int $shopId): void
{
    $shipmentIds = $_POST['shipment_ids'] ?? [];
    if (is_string($shipmentIds))
        $shipmentIds = json_decode($shipmentIds, true);

    if (empty($shipmentIds)) {
        echo json_encode(['success' => false, 'error' => 'Keine Sendungen ausgewählt']);
        return;
    }

    $picklistNumber = 'PL-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

    $picklistId = Database::insert('picklists', [
        'shop_id' => $shopId,
        'picklist_number' => $picklistNumber,
        'status' => 'pending',
        'created_by' => 'Admin'
    ]);

    // Aggregate items by product
    $placeholders = implode(',', array_fill(0, count($shipmentIds), '?'));
    $items = Database::fetchAll("
        SELECT si.product_id, si.sku, si.name, SUM(si.quantity) as total_qty, s.id as shipment_id, s.order_id
        FROM shipment_items si
        JOIN shipments s ON s.id = si.shipment_id
        WHERE s.id IN ({$placeholders})
        GROUP BY si.product_id, si.sku, si.name, s.id, s.order_id
    ", $shipmentIds);

    foreach ($items as $item) {
        Database::insert('picklist_items', [
            'picklist_id' => $picklistId,
            'shipment_id' => $item['shipment_id'],
            'order_id' => $item['order_id'],
            'product_id' => $item['product_id'],
            'sku' => $item['sku'],
            'product_name' => $item['name'],
            'quantity' => $item['total_qty'],
            'location' => 'A-' . rand(1, 10) . '-' . rand(1, 50) // Simulated location
        ]);
    }

    // Mark shipments as picking
    Database::query("UPDATE shipments SET status = 'picking' WHERE id IN ({$placeholders})", $shipmentIds);

    echo json_encode(['success' => true, 'picklist_id' => $picklistId, 'picklist_number' => $picklistNumber]);
}

// =====================================================================
// GET PICKLIST
// =====================================================================
function handleGetPicklist(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);

    $picklist = Database::fetch("SELECT * FROM picklists WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if (!$picklist) {
        echo json_encode(['success' => false, 'error' => 'Picklist nicht gefunden']);
        return;
    }

    $picklist['items'] = Database::fetchAll("
        SELECT pi.*, o.order_number 
        FROM picklist_items pi 
        LEFT JOIN orders o ON o.id = pi.order_id
        WHERE pi.picklist_id = ? 
        ORDER BY pi.location, pi.sku
    ", [$id]);

    echo json_encode(['success' => true, 'picklist' => $picklist]);
}

// =====================================================================
// UPDATE PICKLIST ITEM (mark as picked)
// =====================================================================
function handleUpdatePicklistItem(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $pickedQty = (int) ($_POST['picked_quantity'] ?? 0);

    Database::update('picklist_items', ['picked_quantity' => $pickedQty], 'id = ?', [$id]);

    echo json_encode(['success' => true]);
}

// =====================================================================
// GENERATE TEST DATA
// =====================================================================
function handleGenerateTestData(int $shopId): void
{
    // Create some test shipments from existing orders
    $orders = Database::fetchAll("
        SELECT o.id FROM orders o 
        WHERE o.shop_id = ? AND o.payment_status = 'paid'
        AND NOT EXISTS (SELECT 1 FROM shipments s WHERE s.order_id = o.id)
        LIMIT 10
    ", [$shopId]);

    if (empty($orders)) {
        echo json_encode(['success' => true, 'message' => 'Keine offenen Bestellungen für Testdaten']);
        return;
    }

    $carriers = Database::fetchAll("SELECT id FROM carriers WHERE shop_id = ? AND is_active = 1", [$shopId]);
    $warehouses = Database::fetchAll("SELECT id FROM warehouses WHERE shop_id = ?", [$shopId]);

    $statuses = ['pending', 'picking', 'packed', 'shipped', 'in_transit', 'delivered'];
    $created = 0;

    foreach ($orders as $order) {
        $status = $statuses[array_rand($statuses)];
        $carrierId = $carriers ? $carriers[array_rand($carriers)]['id'] : null;
        $warehouseId = $warehouses ? $warehouses[array_rand($warehouses)]['id'] : null;

        $shipmentNumber = 'SHP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $tracking = in_array($status, ['shipped', 'in_transit', 'delivered']) ? 'JJD' . rand(100000000, 999999999) : null;

        $shipmentId = Database::insert('shipments', [
            'shop_id' => $shopId,
            'order_id' => $order['id'],
            'shipment_number' => $shipmentNumber,
            'warehouse_id' => $warehouseId,
            'carrier_id' => $carrierId,
            'tracking_number' => $tracking,
            'status' => $status,
            'shipped_at' => in_array($status, ['shipped', 'in_transit', 'delivered']) ? date('Y-m-d H:i:s', strtotime('-' . rand(1, 5) . ' days')) : null,
            'delivered_at' => $status === 'delivered' ? date('Y-m-d H:i:s') : null,
            'estimated_delivery' => date('Y-m-d', strtotime('+' . rand(1, 5) . ' days'))
        ]);

        // Add items
        $orderItems = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);
        foreach ($orderItems as $item) {
            Database::insert('shipment_items', [
                'shipment_id' => $shipmentId,
                'order_item_id' => $item['id'],
                'product_id' => $item['product_id'],
                'sku' => $item['sku'],
                'name' => $item['name'],
                'quantity' => $item['quantity']
            ]);
        }

        $created++;
    }

    echo json_encode(['success' => true, 'message' => "{$created} Test-Sendungen erstellt"]);
}

// =====================================================================
// BULK SHIP (multiple shipments at once)
// =====================================================================
function handleBulkShip(int $shopId): void
{
    $shipmentIds = $_POST['shipment_ids'] ?? [];
    if (is_string($shipmentIds)) {
        $shipmentIds = json_decode($shipmentIds, true);
    }

    if (empty($shipmentIds)) {
        echo json_encode(['success' => false, 'error' => 'Keine Sendungen ausgewählt']);
        return;
    }

    $success = 0;
    $failed = 0;

    foreach ($shipmentIds as $shipmentId) {
        $shipment = Database::fetch("SELECT * FROM shipments WHERE id = ? AND shop_id = ?", [$shipmentId, $shopId]);
        if (!$shipment) {
            $failed++;
            continue;
        }

        if ($shipment['status'] === 'shipped' || $shipment['status'] === 'delivered') {
            $failed++;
            continue;
        }

        Database::update('shipments', [
            'status' => 'shipped',
            'shipped_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$shipmentId]);

        Database::update('orders', ['status' => 'shipped'], 'id = ?', [$shipment['order_id']]);

        Database::insert('shipment_status_history', [
            'shipment_id' => $shipmentId,
            'status' => 'Versendet',
            'comment' => 'Bulk-Versand'
        ]);

        $success++;
    }

    echo json_encode([
        'success' => true,
        'message' => "{$success} Sendungen versendet" . ($failed > 0 ? ", {$failed} fehlgeschlagen" : ''),
        'success_count' => $success,
        'failed_count' => $failed
    ]);
}

// =====================================================================
// GENERATE SHIPPING LABEL
// =====================================================================
function handleGenerateLabel(int $shopId): void
{
    $shipmentId = (int) ($_POST['shipment_id'] ?? 0);

    $shipment = Database::fetch("
        SELECT s.*, o.*, c.code as carrier_code
        FROM shipments s
        JOIN orders o ON o.id = s.order_id
        LEFT JOIN carriers c ON c.id = s.carrier_id
        WHERE s.id = ? AND s.shop_id = ?
    ", [$shipmentId, $shopId]);

    if (!$shipment) {
        echo json_encode(['success' => false, 'error' => 'Sendung nicht gefunden']);
        return;
    }

    // Try to use carrier API
    require_once __DIR__ . '/../includes/CarrierService.php';

    $carrierId = $shipment['carrier_id'] ?? 0;
    $carrier = $carrierId ? CarrierService::load($carrierId) : null;

    // Parse addresses
    $shippingAddress = json_decode($shipment['shipping_address'] ?? '{}', true) ?: [];

    $sender = [
        'company' => 'Mein Online Shop',
        'name' => 'Versandabteilung',
        'street' => 'Musterstraße 1',
        'city' => 'Berlin',
        'postal_code' => '10115',
        'country_code' => 'DE'
    ];

    $recipient = [
        'name' => ($shippingAddress['first_name'] ?? '') . ' ' . ($shippingAddress['last_name'] ?? ''),
        'street' => $shippingAddress['street'] ?? $shipment['billing_street'] ?? '',
        'city' => $shippingAddress['city'] ?? $shipment['billing_city'] ?? '',
        'postal_code' => $shippingAddress['postal_code'] ?? $shipment['billing_postal_code'] ?? '',
        'country_code' => $shippingAddress['country_code'] ?? 'DE',
        'email' => $shipment['email'] ?? ''
    ];

    $packages = [['weight' => 1.0]];

    if ($carrier && $carrier->isConfigured()) {
        $result = $carrier->createLabel($shipment, $sender, $recipient, $packages);

        // If carrier API failed, fallback to local generator
        if (!$result['success']) {
            require_once __DIR__ . '/../includes/LabelGenerator.php';
            $generator = new LabelGenerator();

            $labelData = $generator->generate([
                'shipment' => $shipment,
                'sender' => $sender,
                'recipient' => $recipient,
                'carrier' => $shipment['carrier_code'] ?? 'Versand',
                'tracking_number' => $shipment['tracking_number'] ?? $shipment['shipment_number']
            ]);

            $result = [
                'success' => true,
                'label_data' => $labelData,
                'label_format' => 'PDF',
                'tracking_number' => $shipment['tracking_number'] ?? $shipment['shipment_number'],
                'is_local' => true,
                'api_error' => $result['error'] ?? 'API nicht verfügbar'
            ];
        }
    } else {
        // Use local label generator
        require_once __DIR__ . '/../includes/LabelGenerator.php';
        $generator = new LabelGenerator();

        $labelData = $generator->generate([
            'shipment' => $shipment,
            'sender' => $sender,
            'recipient' => $recipient,
            'carrier' => $shipment['carrier_code'] ?? 'Versand',
            'tracking_number' => $shipment['tracking_number'] ?? $shipment['shipment_number']
        ]);

        $result = [
            'success' => true,
            'label_data' => $labelData,
            'label_format' => 'PDF',
            'tracking_number' => $shipment['tracking_number'] ?? $shipment['shipment_number']
        ];
    }

    // Save label if successful
    if ($result['success'] && !empty($result['label_data'])) {
        Database::insert('shipping_labels', [
            'shipment_id' => $shipmentId,
            'carrier_code' => $shipment['carrier_code'] ?? 'local',
            'tracking_number' => $result['tracking_number'] ?? '',
            'label_data' => base64_decode($result['label_data']),
            'label_format' => $result['label_format'] ?? 'PDF',
            'label_type' => 'outbound'
        ]);
    }

    echo json_encode($result);
}

// =====================================================================
// PRINT PICKLIST (Generate PDF)
// =====================================================================
function handlePrintPicklist(int $shopId): void
{
    $picklistId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

    $picklist = Database::fetch("SELECT * FROM picklists WHERE id = ? AND shop_id = ?", [$picklistId, $shopId]);
    if (!$picklist) {
        echo json_encode(['success' => false, 'error' => 'Picklist nicht gefunden']);
        return;
    }

    $items = Database::fetchAll("
        SELECT pi.*, o.order_number, s.shipment_number
        FROM picklist_items pi
        LEFT JOIN orders o ON o.id = pi.order_id
        LEFT JOIN shipments s ON s.id = pi.shipment_id
        WHERE pi.picklist_id = ?
        ORDER BY pi.location, pi.sku
    ", [$picklistId]);

    // Group by location for efficient picking
    $locations = [];
    foreach ($items as $item) {
        $loc = $item['location'] ?? 'Unbekannt';
        if (!isset($locations[$loc])) {
            $locations[$loc] = [];
        }
        $locations[$loc][] = $item;
    }

    require_once __DIR__ . '/../includes/LabelGenerator.php';
    $generator = new LabelGenerator();

    // Generate picklist content
    $orders = [];
    foreach ($items as $item) {
        $orderId = $item['order_id'];
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'order_number' => $item['order_number'] ?? '#' . $orderId,
                'id' => $orderId,
                'items' => []
            ];
        }
        $orders[$orderId]['items'][] = $item;
    }

    $pdfData = $generator->generatePicklist(array_values($orders));

    echo json_encode([
        'success' => true,
        'picklist_number' => $picklist['picklist_number'],
        'pdf_data' => $pdfData,
        'item_count' => count($items),
        'location_count' => count($locations)
    ]);
}

// =====================================================================
// GET PICKLISTS
// =====================================================================
function handleGetPicklists(int $shopId): void
{
    $status = $_GET['status'] ?? '';
    $limit = (int) ($_GET['limit'] ?? 50);
    $offset = (int) ($_GET['offset'] ?? 0);

    $where = ['shop_id = ?'];
    $params = [$shopId];

    if ($status) {
        $where[] = 'status = ?';
        $params[] = $status;
    }

    $whereClause = implode(' AND ', $where);

    $picklists = Database::fetchAll("
        SELECT p.*, 
               (SELECT COUNT(*) FROM picklist_items WHERE picklist_id = p.id) as item_count,
               (SELECT SUM(quantity) FROM picklist_items WHERE picklist_id = p.id) as total_items,
               (SELECT SUM(picked_quantity) FROM picklist_items WHERE picklist_id = p.id) as picked_items
        FROM picklists p
        WHERE {$whereClause}
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ", array_merge($params, [$limit, $offset]));

    $total = Database::fetch("SELECT COUNT(*) as cnt FROM picklists WHERE {$whereClause}", $params);

    echo json_encode([
        'success' => true,
        'picklists' => $picklists,
        'total' => (int) ($total['cnt'] ?? 0)
    ]);
}
