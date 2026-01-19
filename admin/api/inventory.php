<?php
/**
 * Inventory API
 * Complete inventory management operations
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';

Database::configure($database);

// Auto-migration: Ensure warehouses table matches expected schema
// The table may have been created by schema.sql with code/address columns
// This migration ensures compatibility with both schemas
try {
    // First, try to create table if it doesn't exist (using schema.sql structure)
    Database::query("
        CREATE TABLE IF NOT EXISTS warehouses (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            code VARCHAR(50) DEFAULT '',
            address TEXT,
            is_active TINYINT(1) DEFAULT 1,
            is_default TINYINT(1) DEFAULT 0,
            priority INT DEFAULT 0,
            INDEX idx_shop_id (shop_id)
        )
    ");

    // Insert default warehouse if none exists
    $defaultWarehouse = Database::fetch(
        "SELECT id FROM warehouses WHERE shop_id = ? LIMIT 1",
        [1]
    );
    if (!$defaultWarehouse) {
        Database::insert('warehouses', [
            'shop_id' => 1,
            'name' => 'Hauptlager',
            'code' => 'MAIN',
            'address' => 'Standard Lagerort',
            'is_active' => 1,
            'is_default' => 1,
            'priority' => 0
        ]);
    }
} catch (Exception $e) {
    // Ignore migration errors
}


$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_inventory_stats':
            handleGetInventoryStats($shopId);
            break;
        case 'get_inventory_products':
            handleGetInventoryProducts($shopId);
            break;
        case 'update_stock':
            handleUpdateStock($shopId);
            break;
        case 'bulk_update_stock':
            handleBulkUpdateStock($shopId);
            break;
        case 'export_inventory':
            handleExportInventory($shopId);
            break;
        case 'get_warehouses':
            handleGetWarehouses($shopId);
            break;
        case 'add_warehouse':
            handleAddWarehouse($shopId);
            break;
        case 'update_warehouse':
            handleUpdateWarehouse($shopId);
            break;
        case 'delete_warehouse':
            handleDeleteWarehouse($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// GET INVENTORY STATS (for KPI cards)
// =====================================================================
function handleGetInventoryStats(int $shopId): void
{
    // Total stock (sum of all quantities for products that manage stock)
    $totalStock = Database::fetch(
        "SELECT COALESCE(SUM(quantity), 0) as total FROM products WHERE shop_id = ? AND manage_stock = 1",
        [$shopId]
    );

    // Low stock count (products with quantity > 0 and <= low_stock_threshold)
    $lowStock = Database::fetch(
        "SELECT COUNT(*) as count FROM products 
         WHERE shop_id = ? AND manage_stock = 1 AND quantity > 0 AND quantity <= low_stock_threshold",
        [$shopId]
    );

    // Out of stock count (products with quantity = 0)
    $outOfStock = Database::fetch(
        "SELECT COUNT(*) as count FROM products 
         WHERE shop_id = ? AND manage_stock = 1 AND quantity = 0",
        [$shopId]
    );

    // In stock count (products with quantity > low_stock_threshold OR manage_stock = 0)
    $inStock = Database::fetch(
        "SELECT COUNT(*) as count FROM products 
         WHERE shop_id = ? AND (manage_stock = 0 OR quantity > low_stock_threshold)",
        [$shopId]
    );

    // Total products count
    $totalProducts = Database::fetch(
        "SELECT COUNT(*) as count FROM products WHERE shop_id = ?",
        [$shopId]
    );

    // Reserved/Ordered items (from pending order items if order_items table exists)
    $reservedItems = 0;
    try {
        $reserved = Database::fetch(
            "SELECT COALESCE(SUM(oi.quantity), 0) as total 
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             WHERE o.shop_id = ? AND o.status IN ('pending', 'processing')",
            [$shopId]
        );
        $reservedItems = (int) ($reserved['total'] ?? 0);
    } catch (Exception $e) {
        // order_items table might not exist
        $reservedItems = 0;
    }

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_stock' => (int) ($totalStock['total'] ?? 0),
            'low_stock' => (int) ($lowStock['count'] ?? 0),
            'out_of_stock' => (int) ($outOfStock['count'] ?? 0),
            'in_stock' => (int) ($inStock['count'] ?? 0),
            'reserved' => $reservedItems,
            'total_products' => (int) ($totalProducts['count'] ?? 0)
        ]
    ]);
}

// =====================================================================
// GET INVENTORY PRODUCTS (with filtering and sorting)
// =====================================================================
function handleGetInventoryProducts(int $shopId): void
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 50)));
    $offset = ($page - 1) * $perPage;

    $search = trim($_GET['search'] ?? '');
    $stockStatus = $_GET['stock_status'] ?? ''; // all, in_stock, low_stock, out_of_stock
    $sortBy = $_GET['sort_by'] ?? 'availability';
    $sortDir = strtoupper($_GET['sort_dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

    // Build WHERE clause
    $where = ["p.shop_id = ?"];
    $params = [$shopId];

    if ($search) {
        $where[] = "(p.name LIKE ? OR p.sku LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    if ($stockStatus === 'in_stock') {
        $where[] = "(p.manage_stock = 0 OR p.quantity > p.low_stock_threshold)";
    } elseif ($stockStatus === 'low_stock') {
        $where[] = "p.manage_stock = 1 AND p.quantity > 0 AND p.quantity <= p.low_stock_threshold";
    } elseif ($stockStatus === 'out_of_stock') {
        $where[] = "p.manage_stock = 1 AND p.quantity = 0";
    }

    $whereClause = implode(' AND ', $where);

    // Determine ORDER BY
    // Availability sorts: out_of_stock first, then low_stock, then in_stock
    // We use a CASE expression to create a sortable status
    if ($sortBy === 'availability') {
        $orderClause = "
            CASE 
                WHEN p.manage_stock = 1 AND p.quantity = 0 THEN 0
                WHEN p.manage_stock = 1 AND p.quantity <= p.low_stock_threshold THEN 1
                ELSE 2
            END " . $sortDir;
    } elseif ($sortBy === 'name') {
        $orderClause = "p.name " . $sortDir;
    } elseif ($sortBy === 'sku') {
        $orderClause = "p.sku " . $sortDir;
    } elseif ($sortBy === 'quantity') {
        $orderClause = "p.quantity " . $sortDir;
    } else {
        $orderClause = "p.name ASC";
    }

    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM products p WHERE {$whereClause}";
    $countResult = Database::fetch($countQuery, $params);
    $total = (int) ($countResult['total'] ?? 0);

    // Get products
    $query = "
        SELECT 
            p.id,
            p.name,
            p.sku,
            p.type,
            p.quantity,
            p.manage_stock,
            p.low_stock_threshold,
            p.status,
            COALESCE(
                (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.sort_order ASC LIMIT 1),
                ''
            ) as thumbnail,
            CASE 
                WHEN p.manage_stock = 0 THEN 'unlimited'
                WHEN p.quantity = 0 THEN 'out_of_stock'
                WHEN p.quantity <= p.low_stock_threshold THEN 'low_stock'
                ELSE 'in_stock'
            END as stock_status
        FROM products p
        WHERE {$whereClause}
        ORDER BY {$orderClause}, p.name ASC
        LIMIT ? OFFSET ?
    ";

    $products = Database::fetchAll($query, array_merge($params, [$perPage, $offset]));

    // Calculate reserved items per product (if order_items exists)
    $productIds = array_column($products, 'id');
    $reservedByProduct = [];

    if (!empty($productIds)) {
        try {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $reservedRows = Database::fetchAll(
                "SELECT oi.product_id, COALESCE(SUM(oi.quantity), 0) as reserved
                 FROM order_items oi
                 JOIN orders o ON o.id = oi.order_id
                 WHERE oi.product_id IN ({$placeholders}) 
                 AND o.status IN ('pending', 'processing')
                 GROUP BY oi.product_id",
                $productIds
            );
            foreach ($reservedRows as $row) {
                $reservedByProduct[$row['product_id']] = (int) $row['reserved'];
            }
        } catch (Exception $e) {
            // order_items table might not exist
        }
    }

    // Add reserved and available to each product
    foreach ($products as &$product) {
        $reserved = $reservedByProduct[$product['id']] ?? 0;
        $product['reserved'] = $reserved;
        $product['available'] = max(0, (int) $product['quantity'] - $reserved);
    }

    echo json_encode([
        'success' => true,
        'products' => $products,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ]
    ]);
}

// =====================================================================
// UPDATE STOCK (single product quick update)
// =====================================================================
function handleUpdateStock(int $shopId): void
{
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $adjustmentType = $_POST['adjustment_type'] ?? 'set'; // set, add, subtract

    if ($productId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Produkt-ID']);
        return;
    }

    // Verify product exists and belongs to shop
    $product = Database::fetch(
        "SELECT id, quantity, name FROM products WHERE id = ? AND shop_id = ?",
        [$productId, $shopId]
    );

    if (!$product) {
        echo json_encode(['success' => false, 'error' => 'Produkt nicht gefunden']);
        return;
    }

    // Calculate new quantity
    $currentQuantity = (int) $product['quantity'];

    switch ($adjustmentType) {
        case 'add':
            $newQuantity = $currentQuantity + $quantity;
            break;
        case 'subtract':
            $newQuantity = max(0, $currentQuantity - $quantity);
            break;
        default: // 'set'
            $newQuantity = max(0, $quantity);
    }

    // Update product
    Database::update('products', [
        'quantity' => $newQuantity,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$productId]);

    echo json_encode([
        'success' => true,
        'message' => 'Bestand aktualisiert',
        'product_id' => $productId,
        'old_quantity' => $currentQuantity,
        'new_quantity' => $newQuantity
    ]);
}

// =====================================================================
// BULK UPDATE STOCK (CSV upload)
// =====================================================================
function handleBulkUpdateStock(int $shopId): void
{
    // Expect JSON array of updates: [{ sku: "...", quantity: X }, ...]
    $updates = $_POST['updates'] ?? '';

    if (is_string($updates)) {
        $updates = json_decode($updates, true);
    }

    if (!is_array($updates) || empty($updates)) {
        echo json_encode(['success' => false, 'error' => 'Keine Updates gefunden']);
        return;
    }

    $successCount = 0;
    $errorCount = 0;
    $errors = [];

    foreach ($updates as $update) {
        $sku = trim($update['sku'] ?? '');
        $quantity = (int) ($update['quantity'] ?? 0);

        if (empty($sku)) {
            $errorCount++;
            continue;
        }

        // Find product by SKU
        $product = Database::fetch(
            "SELECT id FROM products WHERE sku = ? AND shop_id = ?",
            [$sku, $shopId]
        );

        if (!$product) {
            $errorCount++;
            $errors[] = "SKU nicht gefunden: {$sku}";
            continue;
        }

        // Update quantity
        Database::update('products', [
            'quantity' => max(0, $quantity),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$product['id']]);

        $successCount++;
    }

    echo json_encode([
        'success' => true,
        'message' => "{$successCount} Produkte aktualisiert" . ($errorCount > 0 ? ", {$errorCount} Fehler" : ""),
        'updated' => $successCount,
        'failed' => $errorCount,
        'errors' => $errors
    ]);
}

// =====================================================================
// EXPORT INVENTORY (CSV download)
// =====================================================================
function handleExportInventory(int $shopId): void
{
    // Get all products with inventory data
    $products = Database::fetchAll(
        "SELECT 
            p.sku,
            p.name,
            p.quantity,
            p.low_stock_threshold,
            p.manage_stock,
            p.status,
            CASE 
                WHEN p.manage_stock = 0 THEN 'Unbegrenzt'
                WHEN p.quantity = 0 THEN 'Ausverkauft'
                WHEN p.quantity <= p.low_stock_threshold THEN 'Niedriger Bestand'
                ELSE 'Auf Lager'
            END as stock_status_label
         FROM products p
         WHERE p.shop_id = ?
         ORDER BY p.sku",
        [$shopId]
    );

    // Build CSV
    $headers = ['SKU', 'Produktname', 'Bestand', 'Mindestbestand', 'Lagerverwaltung', 'Status', 'Bestandsstatus'];

    $csvData = [];
    $csvData[] = $headers;

    foreach ($products as $product) {
        $csvData[] = [
            $product['sku'],
            $product['name'],
            $product['quantity'],
            $product['low_stock_threshold'],
            $product['manage_stock'] ? 'Ja' : 'Nein',
            $product['status'],
            $product['stock_status_label']
        ];
    }

    echo json_encode([
        'success' => true,
        'filename' => 'inventar_' . date('Y-m-d_His') . '.csv',
        'data' => $csvData
    ]);
}

// =====================================================================
// GET WAREHOUSES
// =====================================================================
function handleGetWarehouses(int $shopId): void
{
    $warehouses = Database::fetchAll(
        "SELECT * FROM warehouses WHERE shop_id = ? ORDER BY is_default DESC, name ASC",
        [$shopId]
    );

    // Get product count per warehouse (simplified: all products in default warehouse)
    // Also map 'address' to 'location' for frontend compatibility
    foreach ($warehouses as &$warehouse) {
        $warehouse['location'] = $warehouse['address'] ?? ''; // Map address to location for frontend

        if ($warehouse['is_default']) {
            $count = Database::fetch(
                "SELECT COALESCE(SUM(quantity), 0) as total FROM products WHERE shop_id = ? AND manage_stock = 1",
                [$shopId]
            );
            $warehouse['item_count'] = (int) ($count['total'] ?? 0);
        } else {
            $warehouse['item_count'] = 0;
        }
    }

    echo json_encode([
        'success' => true,
        'warehouses' => $warehouses
    ]);
}

// =====================================================================
// ADD WAREHOUSE
// =====================================================================
function handleAddWarehouse(int $shopId): void
{
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['location'] ?? ''); // Frontend sends 'location', we use it as 'address'

    // Generate a unique code from name
    $code = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', substr($name, 0, 10))) . '_' . time();

    if (empty($name)) {
        echo json_encode(['success' => false, 'error' => 'Lagername ist erforderlich']);
        return;
    }

    $warehouseId = Database::insert('warehouses', [
        'shop_id' => $shopId,
        'name' => $name,
        'code' => $code,
        'address' => $address,
        'is_active' => 1,
        'is_default' => 0,
        'priority' => 0
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Lager erstellt',
        'id' => $warehouseId
    ]);
}

// =====================================================================
// UPDATE WAREHOUSE
// =====================================================================
function handleUpdateWarehouse(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['location'] ?? ''); // Frontend sends 'location', we use it as 'address'

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Lager-ID']);
        return;
    }

    if (empty($name)) {
        echo json_encode(['success' => false, 'error' => 'Lagername ist erforderlich']);
        return;
    }

    Database::update('warehouses', [
        'name' => $name,
        'address' => $address
    ], 'id = ? AND shop_id = ?', [$id, $shopId]);

    echo json_encode([
        'success' => true,
        'message' => 'Lager aktualisiert'
    ]);
}

// =====================================================================
// DELETE WAREHOUSE
// =====================================================================
function handleDeleteWarehouse(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Lager-ID']);
        return;
    }

    // Check if it's the default warehouse
    $warehouse = Database::fetch(
        "SELECT is_default FROM warehouses WHERE id = ? AND shop_id = ?",
        [$id, $shopId]
    );

    if (!$warehouse) {
        echo json_encode(['success' => false, 'error' => 'Lager nicht gefunden']);
        return;
    }

    if ($warehouse['is_default']) {
        echo json_encode(['success' => false, 'error' => 'Das Standardlager kann nicht gelöscht werden']);
        return;
    }

    Database::delete('warehouses', 'id = ? AND shop_id = ?', [$id, $shopId]);

    echo json_encode([
        'success' => true,
        'message' => 'Lager gelöscht'
    ]);
}
