<?php
/**
 * Bundles API
 * Complete CRUD operations for bundle management with multi-currency support
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';

Database::configure($database);

// =====================================================================
// AUTO-MIGRATION: Ensure bundles tables exist with latest schema
// =====================================================================
try {
    // Check if bundles table exists
    $tableExists = Database::fetchAll("SHOW TABLES LIKE 'bundles'");
    if (empty($tableExists)) {
        Database::query("
            CREATE TABLE bundles (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                shop_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description TEXT,
                bundle_type ENUM('standard', 'limited') DEFAULT 'standard',
                price_type ENUM('percentage', 'fixed_price', 'fixed_discount') DEFAULT 'percentage',
                discount_value DECIMAL(15,4) DEFAULT 0,
                fixed_total_price DECIMAL(15,4) NULL COMMENT 'Optional fixed bundle price override',
                base_currency VARCHAR(3) DEFAULT 'EUR',
                valid_from DATE NULL,
                valid_to DATE NULL,
                status ENUM('active', 'draft', 'archived') DEFAULT 'draft',
                sold_count INT UNSIGNED DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_bundle_shop (shop_id),
                UNIQUE KEY uk_bundle_slug (shop_id, slug)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    } else {
        // Add new columns if they don't exist
        $columns = Database::fetchAll("SHOW COLUMNS FROM bundles");
        $columnNames = array_column($columns, 'Field');

        // Add columns one by one with individual error handling
        if (!in_array('fixed_total_price', $columnNames)) {
            try {
                Database::query("ALTER TABLE bundles ADD COLUMN fixed_total_price DECIMAL(15,4) NULL AFTER discount_value");
            } catch (Exception $e) { /* Column might already exist */
            }
        }
        if (!in_array('base_currency', $columnNames)) {
            try {
                Database::query("ALTER TABLE bundles ADD COLUMN base_currency VARCHAR(3) DEFAULT 'EUR' AFTER fixed_total_price");
            } catch (Exception $e) { /* Column might already exist */
            }
        }
        if (!in_array('valid_from', $columnNames)) {
            try {
                Database::query("ALTER TABLE bundles ADD COLUMN valid_from DATE NULL AFTER base_currency");
            } catch (Exception $e) { /* Column might already exist */
            }
        }
        if (!in_array('valid_to', $columnNames)) {
            try {
                Database::query("ALTER TABLE bundles ADD COLUMN valid_to DATE NULL AFTER valid_from");
            } catch (Exception $e) { /* Column might already exist */
            }
        }

        // Update bundle_type enum to remove 'subscription' and rename 'promotional' to 'limited'
        try {
            Database::query("ALTER TABLE bundles MODIFY COLUMN bundle_type ENUM('standard', 'limited', 'promotional', 'subscription') DEFAULT 'standard'");
            Database::query("UPDATE bundles SET bundle_type = 'limited' WHERE bundle_type IN ('promotional', 'subscription')");
            Database::query("ALTER TABLE bundles MODIFY COLUMN bundle_type ENUM('standard', 'limited') DEFAULT 'standard'");
        } catch (Exception $e) {
            // Ignore if already correct
        }
    }

    // Check if bundle_items table exists
    $itemsTableExists = Database::fetchAll("SHOW TABLES LIKE 'bundle_items'");
    if (empty($itemsTableExists)) {
        Database::query("
            CREATE TABLE bundle_items (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                bundle_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                quantity INT DEFAULT 1,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE,
                INDEX idx_bundle_items (bundle_id),
                UNIQUE KEY uk_bundle_product (bundle_id, product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    // Check if bundle_currency_prices table exists
    $currencyTableExists = Database::fetchAll("SHOW TABLES LIKE 'bundle_currency_prices'");
    if (empty($currencyTableExists)) {
        Database::query("
            CREATE TABLE bundle_currency_prices (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                bundle_id BIGINT UNSIGNED NOT NULL,
                currency_code VARCHAR(3) NOT NULL,
                price DECIMAL(15,4) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (bundle_id) REFERENCES bundles(id) ON DELETE CASCADE,
                UNIQUE KEY uk_bundle_currency (bundle_id, currency_code)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
} catch (Exception $e) {
    // Ignore migration errors - tables might already exist
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_bundles':
            handleGetBundles($shopId);
            break;
        case 'get_bundle':
            handleGetBundle($shopId);
            break;
        case 'save_bundle':
            handleSaveBundle($shopId);
            break;
        case 'delete_bundle':
            handleDeleteBundle($shopId);
            break;
        case 'toggle_status':
            handleToggleStatus($shopId);
            break;
        case 'get_products':
            handleGetProducts($shopId);
            break;
        case 'get_currencies':
            handleGetCurrencies($shopId);
            break;
        case 'get_shop_currency':
            handleGetShopCurrency($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// GET BUNDLES (with stats)
// =====================================================================
function handleGetBundles(int $shopId)
{
    $status = $_GET['status'] ?? 'all';
    $search = $_GET['search'] ?? '';

    $whereClause = "b.shop_id = ?";
    $params = [$shopId];

    if ($status !== 'all') {
        $whereClause .= " AND b.status = ?";
        $params[] = $status;
    }

    if (!empty($search)) {
        $whereClause .= " AND (b.name LIKE ? OR b.slug LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $query = "
        SELECT b.*,
               (SELECT COUNT(*) FROM bundle_items bi WHERE bi.bundle_id = b.id) as product_count,
               (SELECT SUM(bi.quantity) FROM bundle_items bi WHERE bi.bundle_id = b.id) as total_items
        FROM bundles b
        WHERE {$whereClause}
        ORDER BY b.updated_at DESC
    ";

    $bundles = Database::fetchAll($query, $params);

    // Get stats
    $stats = Database::fetch("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
            SUM(CASE WHEN status = 'archived' THEN 1 ELSE 0 END) as archived
        FROM bundles WHERE shop_id = ?
    ", [$shopId]);

    echo json_encode([
        'success' => true,
        'bundles' => $bundles,
        'stats' => $stats
    ]);
}

// =====================================================================
// GET SINGLE BUNDLE
// =====================================================================
function handleGetBundle(int $shopId)
{
    $bundleId = (int) ($_GET['id'] ?? 0);

    if (!$bundleId) {
        echo json_encode(['success' => false, 'error' => 'Bundle ID required']);
        return;
    }

    $bundle = Database::fetch("
        SELECT * FROM bundles WHERE id = ? AND shop_id = ?
    ", [$bundleId, $shopId]);

    if (!$bundle) {
        echo json_encode(['success' => false, 'error' => 'Bundle not found']);
        return;
    }

    // Get products in bundle with quantities
    $products = Database::fetchAll("
        SELECT bi.*, bi.quantity, p.name, p.sku, p.price, p.status as product_status, p.type,
               (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.sort_order ASC LIMIT 1) as thumbnail
        FROM bundle_items bi
        JOIN products p ON p.id = bi.product_id
        WHERE bi.bundle_id = ?
        ORDER BY bi.sort_order ASC
    ", [$bundleId]);

    $bundle['products'] = $products;

    // Get currency price overrides
    $currencyPrices = Database::fetchAll("
        SELECT currency_code, price FROM bundle_currency_prices WHERE bundle_id = ?
    ", [$bundleId]);

    $bundle['currency_prices'] = [];
    foreach ($currencyPrices as $cp) {
        $bundle['currency_prices'][$cp['currency_code']] = (float) $cp['price'];
    }

    echo json_encode([
        'success' => true,
        'bundle' => $bundle
    ]);
}

// =====================================================================
// SAVE BUNDLE (Create / Update)
// =====================================================================
function handleSaveBundle(int $shopId)
{
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        return;
    }

    $bundleId = (int) ($data['id'] ?? 0);
    $name = trim($data['name'] ?? '');
    $customSlug = trim($data['slug'] ?? '');
    $description = trim($data['description'] ?? '');
    $bundleType = $data['bundle_type'] ?? 'standard';
    $priceType = $data['price_type'] ?? 'percentage';
    $discountValue = (float) ($data['discount_value'] ?? 0);
    $fixedTotalPrice = isset($data['fixed_total_price']) && $data['fixed_total_price'] !== ''
        ? (float) $data['fixed_total_price']
        : null;
    $baseCurrency = $data['base_currency'] ?? 'EUR';
    $validFrom = !empty($data['valid_from']) ? $data['valid_from'] : null;
    $validTo = !empty($data['valid_to']) ? $data['valid_to'] : null;
    $status = $data['status'] ?? 'draft';
    $products = $data['products'] ?? []; // Array of {id, quantity}
    $currencyPrices = $data['currency_prices'] ?? [];

    // Validation
    $errors = [];
    if (empty($name)) {
        $errors['name'] = 'Bundle-Name ist erforderlich';
    }
    if (empty($products) || count($products) < 2) {
        $errors['products'] = 'Mindestens 2 Produkte müssen ausgewählt werden';
    }

    // Validate discount value based on price type
    if ($priceType === 'percentage') {
        if ($discountValue <= 0 || $discountValue > 100) {
            $errors['discount_value'] = 'Prozentrabatt muss zwischen 1 und 100 liegen';
        }
    } elseif ($priceType === 'fixed_discount') {
        if ($discountValue <= 0) {
            $errors['discount_value'] = 'Rabattbetrag muss größer als 0 sein';
        }
    } elseif ($priceType === 'fixed_price') {
        if ($discountValue <= 0) {
            $errors['discount_value'] = 'Fester Preis muss größer als 0 sein';
        }
    }

    // Validate dates for limited bundles
    if ($bundleType === 'limited') {
        if (empty($validFrom)) {
            $errors['valid_from'] = 'Startdatum ist erforderlich für zeitlich begrenzte Bundles';
        }
        if (empty($validTo)) {
            $errors['valid_to'] = 'Enddatum ist erforderlich für zeitlich begrenzte Bundles';
        }
        if (!empty($validFrom) && !empty($validTo) && $validTo < $validFrom) {
            $errors['valid_to'] = 'Enddatum darf nicht vor dem Startdatum liegen';
        }
    } else {
        // Clear dates for standard bundles
        $validFrom = null;
        $validTo = null;
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        return;
    }

    // Generate slug
    $slug = generateUniqueSlug($shopId, $customSlug, $name, $bundleId);

    try {
        Database::beginTransaction();

        $bundleData = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'bundle_type' => $bundleType,
            'price_type' => $priceType,
            'discount_value' => $discountValue,
            'fixed_total_price' => $fixedTotalPrice,
            'base_currency' => $baseCurrency,
            'valid_from' => $validFrom,
            'valid_to' => $validTo,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($bundleId > 0) {
            // Update existing bundle
            Database::update('bundles', $bundleData, 'id = ? AND shop_id = ?', [$bundleId, $shopId]);

            // Delete old products
            Database::delete('bundle_items', 'bundle_id = ?', [$bundleId]);

            // Delete old currency prices
            Database::delete('bundle_currency_prices', 'bundle_id = ?', [$bundleId]);
        } else {
            // Create new bundle
            $bundleData['shop_id'] = $shopId;
            $bundleData['created_at'] = date('Y-m-d H:i:s');
            $bundleId = Database::insert('bundles', $bundleData);
        }

        // Insert products with quantities
        foreach ($products as $index => $product) {
            $productId = is_array($product) ? (int) $product['id'] : (int) $product;
            $quantity = is_array($product) ? (int) ($product['quantity'] ?? 1) : 1;

            Database::insert('bundle_items', [
                'bundle_id' => $bundleId,
                'product_id' => $productId,
                'quantity' => max(1, $quantity),
                'sort_order' => $index
            ]);
        }

        // Insert currency price overrides
        foreach ($currencyPrices as $currencyCode => $price) {
            if ($price !== null && $price !== '') {
                Database::insert('bundle_currency_prices', [
                    'bundle_id' => $bundleId,
                    'currency_code' => $currencyCode,
                    'price' => (float) $price
                ]);
            }
        }

        Database::commit();

        echo json_encode([
            'success' => true,
            'message' => $bundleId ? 'Bundle gespeichert' : 'Bundle erstellt',
            'bundle_id' => $bundleId
        ]);

    } catch (Exception $e) {
        Database::rollback();
        echo json_encode(['success' => false, 'error' => 'Fehler beim Speichern: ' . $e->getMessage()]);
    }
}

// =====================================================================
// DELETE BUNDLE
// =====================================================================
function handleDeleteBundle(int $shopId)
{
    $bundleId = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

    if (!$bundleId) {
        echo json_encode(['success' => false, 'error' => 'Bundle ID required']);
        return;
    }

    $deleted = Database::delete('bundles', 'id = ? AND shop_id = ?', [$bundleId, $shopId]);

    if ($deleted) {
        echo json_encode(['success' => true, 'message' => 'Bundle gelöscht']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Bundle nicht gefunden']);
    }
}

// =====================================================================
// TOGGLE STATUS
// =====================================================================
function handleToggleStatus(int $shopId)
{
    $bundleId = (int) ($_POST['id'] ?? 0);

    if (!$bundleId) {
        echo json_encode(['success' => false, 'error' => 'Bundle ID required']);
        return;
    }

    $bundle = Database::fetch("SELECT status FROM bundles WHERE id = ? AND shop_id = ?", [$bundleId, $shopId]);

    if (!$bundle) {
        echo json_encode(['success' => false, 'error' => 'Bundle not found']);
        return;
    }

    $newStatus = $bundle['status'] === 'active' ? 'draft' : 'active';

    Database::update('bundles', [
        'status' => $newStatus,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$bundleId]);

    echo json_encode([
        'success' => true,
        'message' => $newStatus === 'active' ? 'Bundle aktiviert' : 'Bundle deaktiviert',
        'new_status' => $newStatus
    ]);
}

// =====================================================================
// GET PRODUCTS (for selection table)
// =====================================================================
function handleGetProducts(int $shopId)
{
    $search = $_GET['search'] ?? '';
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(100, max(10, (int) ($_GET['per_page'] ?? 50)));
    $offset = ($page - 1) * $perPage;

    $whereClause = "p.shop_id = ? AND p.status = 'active'";
    $params = [$shopId];

    if (!empty($search)) {
        $whereClause .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Get shop default currency
    $shopCurrency = Database::fetch(
        "SELECT code, symbol FROM currencies WHERE shop_id = ? AND is_default = 1",
        [$shopId]
    );
    $currencySymbol = $shopCurrency['symbol'] ?? '€';
    $currencyCode = $shopCurrency['code'] ?? 'EUR';

    // Count total
    $countResult = Database::fetch("SELECT COUNT(*) as total FROM products p WHERE {$whereClause}", $params);
    $total = (int) ($countResult['total'] ?? 0);

    $products = Database::fetchAll("
        SELECT p.id, p.name, p.sku, p.price, p.type, p.status, p.quantity,
               (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.sort_order ASC LIMIT 1) as thumbnail
        FROM products p
        WHERE {$whereClause}
        ORDER BY p.name ASC
        LIMIT ? OFFSET ?
    ", array_merge($params, [$perPage, $offset]));

    echo json_encode([
        'success' => true,
        'products' => $products,
        'currency' => [
            'code' => $currencyCode,
            'symbol' => $currencySymbol
        ],
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ]
    ]);
}

// =====================================================================
// GET CURRENCIES
// =====================================================================
function handleGetCurrencies(int $shopId)
{
    $currencies = Database::fetchAll("
        SELECT code, name, symbol, exchange_rate, is_default 
        FROM currencies 
        WHERE shop_id = ? 
        ORDER BY is_default DESC, code ASC
    ", [$shopId]);

    echo json_encode([
        'success' => true,
        'currencies' => $currencies
    ]);
}

// =====================================================================
// GET SHOP DEFAULT CURRENCY
// =====================================================================
function handleGetShopCurrency(int $shopId)
{
    $currency = Database::fetch(
        "SELECT code, symbol, name FROM currencies WHERE shop_id = ? AND is_default = 1",
        [$shopId]
    );

    if (!$currency) {
        $currency = ['code' => 'EUR', 'symbol' => '€', 'name' => 'Euro'];
    }

    echo json_encode([
        'success' => true,
        'currency' => $currency
    ]);
}

// =====================================================================
// HELPER FUNCTIONS
// =====================================================================
function generateSlug(string $text): string
{
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[äÄ]/', 'ae', $slug);
    $slug = preg_replace('/[öÖ]/', 'oe', $slug);
    $slug = preg_replace('/[üÜ]/', 'ue', $slug);
    $slug = preg_replace('/ß/', 'ss', $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

function generateUniqueSlug(int $shopId, string $customSlug, string $name, int $excludeId = 0): string
{
    $baseSlug = !empty($customSlug) ? generateSlug($customSlug) : generateSlug($name);

    if (empty($baseSlug)) {
        $baseSlug = 'bundle-' . time();
    }

    $slug = $baseSlug;
    $counter = 1;

    while (true) {
        $existing = Database::fetch(
            "SELECT id FROM bundles WHERE shop_id = ? AND slug = ? AND id != ?",
            [$shopId, $slug, $excludeId]
        );

        if (!$existing) {
            break;
        }

        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}
