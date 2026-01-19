<?php
/**
 * Products API
 * Complete CRUD operations for product management
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

Database::configure($database);
Auth::init();

// Auto-migration: Add is_default column to product_variants if it doesn't exist
try {
    $columns = Database::fetchAll("SHOW COLUMNS FROM product_variants LIKE 'is_default'");
    if (empty($columns)) {
        Database::query("ALTER TABLE product_variants ADD COLUMN is_default TINYINT(1) DEFAULT 0 COMMENT 'Marks the default variant for display' AFTER is_active");
    }
} catch (Exception $e) {
    // Ignore migration errors - column might already exist
}

// Auto-migration: Add variant_id column to product_images if it doesn't exist
try {
    $columns = Database::fetchAll("SHOW COLUMNS FROM product_images LIKE 'variant_id'");
    if (empty($columns)) {
        Database::query("ALTER TABLE product_images ADD COLUMN variant_id BIGINT UNSIGNED NULL COMMENT 'Links image to a specific variant' AFTER product_id");
    }
} catch (Exception $e) {
    // Ignore migration errors - column might already exist
}


$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_products':
            handleGetProducts($shopId);
            break;
        case 'get_product':
            handleGetProduct($shopId);
            break;
        case 'save_product':
            handleSaveProduct($shopId);
            break;
        case 'delete_product':
            handleDeleteProduct($shopId);
            break;
        case 'toggle_status':
            handleToggleStatus($shopId);
            break;
        case 'get_categories':
            handleGetCategories($shopId);
            break;
        case 'get_stats':
            handleGetStats($shopId);
            break;
        case 'bulk_action':
            handleBulkAction($shopId);
            break;
        case 'get_tax_classes':
            handleGetTaxClasses($shopId);
            break;
        case 'export_products':
            handleExportProducts($shopId);
            break;
        // Multi-Currency Pricing
        case 'get_shop_currency':
            handleGetShopCurrency($shopId);
            break;
        case 'get_currencies':
            handleGetCurrencies($shopId);
            break;
        case 'save_currency_prices':
            handleSaveCurrencyPrices($shopId);
            break;
        case 'calculate_prices':
            handleCalculatePrices($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// GET PRODUCTS (with filters, search, pagination)
// =====================================================================
function handleGetProducts(int $shopId): void
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 20)));
    $offset = ($page - 1) * $perPage;

    $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? '';
    $type = $_GET['type'] ?? '';
    $categoryId = (int) ($_GET['category_id'] ?? 0);
    $availability = $_GET['availability'] ?? '';
    $sortBy = $_GET['sort_by'] ?? 'created_at';
    $sortDir = strtoupper($_GET['sort_dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

    // Get display currency (URL param or shop default)
    $displayCurrency = $_GET['display_currency'] ?? null;

    // Get shop default currency and all currencies
    $shopCurrency = Database::fetch(
        "SELECT code, symbol FROM currencies WHERE shop_id = ? AND is_default = 1",
        [$shopId]
    );
    $defaultCurrencyCode = $shopCurrency['code'] ?? 'USD';
    $defaultCurrencySymbol = $shopCurrency['symbol'] ?? '$';

    // If display currency specified, get its info
    $displayCurrencyCode = $displayCurrency ?: $defaultCurrencyCode;
    $displayCurrencyInfo = Database::fetch(
        "SELECT code, symbol, exchange_rate FROM currencies WHERE shop_id = ? AND code = ?",
        [$shopId, $displayCurrencyCode]
    );
    if (!$displayCurrencyInfo) {
        $displayCurrencyInfo = ['code' => $defaultCurrencyCode, 'symbol' => $defaultCurrencySymbol, 'exchange_rate' => 1];
    }

    // Build WHERE clause
    $where = ["p.shop_id = ?"];
    $params = [$shopId];

    if ($search) {
        $where[] = "(p.name LIKE ? OR p.sku LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    if ($status) {
        $where[] = "p.status = ?";
        $params[] = $status;
    }

    if ($type) {
        $where[] = "p.type = ?";
        $params[] = $type;
    }

    if ($categoryId > 0) {
        $where[] = "EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = ?)";
        $params[] = $categoryId;
    }

    if ($availability === 'in_stock') {
        $where[] = "(p.manage_stock = 0 OR p.quantity > 0)";
    } elseif ($availability === 'out_of_stock') {
        $where[] = "p.manage_stock = 1 AND p.quantity = 0";
    } elseif ($availability === 'low_stock') {
        $where[] = "p.manage_stock = 1 AND p.quantity > 0 AND p.quantity <= p.low_stock_threshold";
    }

    $whereClause = implode(' AND ', $where);

    // Validate sort column
    $validSorts = ['name', 'price', 'quantity', 'created_at', 'updated_at', 'status'];
    if (!in_array($sortBy, $validSorts)) {
        $sortBy = 'created_at';
    }

    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM products p WHERE {$whereClause}";
    $countResult = Database::fetch($countQuery, $params);
    $total = (int) ($countResult['total'] ?? 0);

    // Get products with base_currency
    // Thumbnail logic: 
    // 1. For products with variants: use image from default variant (is_default=1), or first variant if no default
    // 2. For simple products: use first product image
    $query = "
        SELECT p.*, 
               (SELECT GROUP_CONCAT(c.name SEPARATOR ', ') 
                FROM product_categories pc 
                JOIN categories c ON c.id = pc.category_id 
                WHERE pc.product_id = p.id) as category_names,
               COALESCE(
                   -- First try to get product image linked to default variant
                   (SELECT pi.image_url 
                    FROM product_images pi 
                    WHERE pi.product_id = p.id 
                    AND pi.variant_id IS NOT NULL 
                    AND pi.variant_id IN (SELECT pv.id FROM product_variants pv WHERE pv.parent_product_id = p.id AND pv.is_default = 1)
                    ORDER BY pi.sort_order ASC LIMIT 1),
                   -- Then try any variant-linked image
                   (SELECT pi.image_url 
                    FROM product_images pi 
                    WHERE pi.product_id = p.id 
                    AND pi.variant_id IS NOT NULL 
                    AND pi.variant_id IN (SELECT pv.id FROM product_variants pv WHERE pv.parent_product_id = p.id)
                    ORDER BY pi.sort_order ASC LIMIT 1),
                   -- Fall back to first product image (no variant link or simple product)
                   (SELECT pi.image_url 
                    FROM product_images pi 
                    WHERE pi.product_id = p.id
                    ORDER BY pi.sort_order ASC LIMIT 1)
               ) as thumbnail
        FROM products p 
        WHERE {$whereClause}
        ORDER BY p.{$sortBy} {$sortDir}
        LIMIT ? OFFSET ?
    ";

    $products = Database::fetchAll($query, array_merge($params, [$perPage, $offset]));

    // Get all currency exchange rates for conversion
    $currencies = Database::fetchAll(
        "SELECT code, exchange_rate FROM currencies WHERE shop_id = ?",
        [$shopId]
    );
    $exchangeRates = [];
    foreach ($currencies as $c) {
        $exchangeRates[$c['code']] = (float) $c['exchange_rate'];
    }

    // Get all product currency overrides for products in result
    $productIds = array_column($products, 'id');
    $overrides = [];
    if (!empty($productIds)) {
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $overrideRows = Database::fetchAll(
            "SELECT product_id, currency_code, price FROM product_currency_prices WHERE product_id IN ({$placeholders})",
            $productIds
        );
        foreach ($overrideRows as $row) {
            $overrides[$row['product_id']][$row['currency_code']] = (float) $row['price'];
        }
    }

    // Calculate display_price for each product
    foreach ($products as &$product) {
        $basePrice = (float) $product['price'];
        $baseCurrency = $product['base_currency'] ?: $defaultCurrencyCode;
        $productId = $product['id'];

        // Check if there's an override for the display currency
        if (isset($overrides[$productId][$displayCurrencyCode])) {
            $product['display_price'] = $overrides[$productId][$displayCurrencyCode];
        } else {
            // Convert from product's base currency to display currency
            $baseRate = $exchangeRates[$baseCurrency] ?? 1;
            $targetRate = $exchangeRates[$displayCurrencyCode] ?? 1;

            // Price in reference currency = basePrice / baseRate
            // Price in target currency = (basePrice / baseRate) * targetRate
            $product['display_price'] = ($basePrice / $baseRate) * $targetRate;
        }

        $product['display_currency'] = $displayCurrencyCode;
        $product['display_symbol'] = $displayCurrencyInfo['symbol'];
    }

    echo json_encode([
        'success' => true,
        'products' => $products,
        'currency' => [
            'code' => $displayCurrencyCode,
            'symbol' => $displayCurrencyInfo['symbol'],
            'default_code' => $defaultCurrencyCode,
            'default_symbol' => $defaultCurrencySymbol
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
// GET SINGLE PRODUCT
// =====================================================================
function handleGetProduct(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
        return;
    }

    $product = Database::fetch(
        "SELECT * FROM products WHERE id = ? AND shop_id = ?",
        [$id, $shopId]
    );

    if (!$product) {
        echo json_encode(['success' => false, 'error' => 'Product not found']);
        return;
    }

    // Get categories
    $categories = Database::fetchAll(
        "SELECT c.* FROM categories c 
         JOIN product_categories pc ON pc.category_id = c.id 
         WHERE pc.product_id = ?",
        [$id]
    );

    // Get variants with all pricing data
    $variants = Database::fetchAll(
        "SELECT * FROM product_variants WHERE parent_product_id = ? ORDER BY id",
        [$id]
    );

    // Calculate price_adjustment for each variant (variant price - parent price)
    $parentPrice = (float) $product['price'];
    foreach ($variants as &$variant) {
        $variantPrice = (float) $variant['price'];
        $variant['price_adjustment'] = $variantPrice - $parentPrice;
    }
    unset($variant); // Break the reference

    // Load variant currency overrides
    if (!empty($variants)) {
        $variantIds = array_column($variants, 'id');
        $placeholders = implode(',', array_fill(0, count($variantIds), '?'));

        $currencyOverrides = Database::fetchAll(
            "SELECT variant_id, currency_code, price FROM variant_currency_prices 
             WHERE variant_id IN ({$placeholders})",
            $variantIds
        );

        // Group overrides by variant_id
        $overridesByVariant = [];
        foreach ($currencyOverrides as $override) {
            $overridesByVariant[$override['variant_id']][$override['currency_code']] = (float) $override['price'];
        }

        // Attach to each variant
        foreach ($variants as &$variant) {
            $variant['currency_overrides'] = $overridesByVariant[$variant['id']] ?? [];
        }
        unset($variant);
    }

    // Get images
    $images = Database::fetchAll(
        "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order",
        [$id]
    );

    $product['categories'] = $categories;
    $product['variants'] = $variants;
    $product['images'] = $images;

    echo json_encode([
        'success' => true,
        'product' => $product
    ]);
}

// =====================================================================
// SAVE PRODUCT (Create / Update)
// =====================================================================
function handleSaveProduct(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    // Required fields
    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $type = $_POST['type'] ?? 'simple';

    // Validation
    $errors = [];

    if (empty($name)) {
        $errors[] = 'Produktname ist erforderlich';
    }

    if (empty($sku)) {
        $errors[] = 'SKU ist erforderlich';
    } else {
        // Check SKU uniqueness
        $existing = Database::fetch(
            "SELECT id FROM products WHERE shop_id = ? AND sku = ? AND id != ?",
            [$shopId, $sku, $id]
        );
        if ($existing) {
            $errors[] = 'SKU existiert bereits';
        }
    }

    if ($price <= 0) {
        $errors[] = 'Preis muss größer als 0 sein';
    }

    $categoryIds = $_POST['category_ids'] ?? [];
    if (is_string($categoryIds)) {
        $categoryIds = json_decode($categoryIds, true) ?: [];
    }

    if (empty($categoryIds)) {
        $errors[] = 'Mindestens eine Kategorie ist erforderlich';
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        return;
    }

    // Build data array
    $data = [
        'shop_id' => $shopId,
        'type' => $type,
        'sku' => $sku,
        'name' => $name,
        'slug' => generateUniqueSlug($shopId, $_POST['slug'] ?? '', $name, $id),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price' => $price,
        'special_price' => !empty($_POST['special_price']) ? (float) $_POST['special_price'] : null,
        'special_price_from' => !empty($_POST['special_price_from']) ? $_POST['special_price_from'] : null,
        'special_price_to' => !empty($_POST['special_price_to']) ? $_POST['special_price_to'] : null,
        'cost_price' => !empty($_POST['cost_price']) ? (float) $_POST['cost_price'] : null,
        'tax_class_id' => (int) ($_POST['tax_class_id'] ?? 1),
        'status' => $_POST['status'] ?? 'draft',
        'is_visible' => isset($_POST['is_visible']) ? (int) $_POST['is_visible'] : 1,
        'is_featured' => isset($_POST['is_featured']) ? (int) $_POST['is_featured'] : 0,
        'is_new' => isset($_POST['is_new']) ? (int) $_POST['is_new'] : 0,
        'manage_stock' => isset($_POST['manage_stock']) ? (int) $_POST['manage_stock'] : 1,
        'quantity' => (int) ($_POST['quantity'] ?? 0),
        'low_stock_threshold' => (int) ($_POST['low_stock_threshold'] ?? 5),
        'allow_backorders' => isset($_POST['allow_backorders']) ? (int) $_POST['allow_backorders'] : 0,
        'meta_title' => trim($_POST['meta_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'meta_keywords' => trim($_POST['meta_keywords'] ?? '')
    ];

    // Physical product specific fields (simple, configurable, bundle, grouped)
    if (in_array($type, ['simple', 'configurable', 'bundle', 'grouped'])) {
        $data['weight'] = (float) ($_POST['weight'] ?? 0);
        $data['length'] = (float) ($_POST['length'] ?? 0);
        $data['width'] = (float) ($_POST['width'] ?? 0);
        $data['height'] = (float) ($_POST['height'] ?? 0);
    }

    // Digital product specific fields
    if ($type === 'digital') {
        $data['is_downloadable'] = 1;
        $data['download_limit'] = (int) ($_POST['download_limit'] ?? 0);
        $data['download_expiry_days'] = (int) ($_POST['download_expiry_days'] ?? 0);
        // Digital products default to unlimited stock (no inventory tracking)
        // unless manage_stock is explicitly set to 1
        if (!isset($_POST['manage_stock']) || $_POST['manage_stock'] !== '1') {
            $data['manage_stock'] = 0;
        }
    }

    if ($id > 0) {
        // Update
        $data['updated_at'] = date('Y-m-d H:i:s');
        Database::update('products', $data, 'id = ? AND shop_id = ?', [$id, $shopId]);
        $productId = $id;
        $message = 'Produkt aktualisiert';
    } else {
        // Insert
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $productId = Database::insert('products', $data);
        $message = 'Produkt erstellt';
    }

    // Update categories
    Database::delete('product_categories', 'product_id = ?', [$productId]);
    foreach ($categoryIds as $catId) {
        Database::insert('product_categories', [
            'product_id' => $productId,
            'category_id' => (int) $catId
        ]);
    }

    // =====================================================
    // HANDLE VARIANTS
    // =====================================================

    // First, handle specific variant deletions (if any)
    $deleteVariantIds = $_POST['delete_variant_ids'] ?? '';
    if (!empty($deleteVariantIds)) {
        $variantIdsToDelete = is_string($deleteVariantIds) ? json_decode($deleteVariantIds, true) : $deleteVariantIds;

        if (is_array($variantIdsToDelete) && !empty($variantIdsToDelete)) {
            foreach ($variantIdsToDelete as $variantId) {
                Database::delete('product_variants', 'id = ? AND parent_product_id = ?', [(int) $variantId, $productId]);
            }
        }
    }

    // Then, handle new variant generation (this replaces ALL variants if new ones are submitted)
    $variantsJson = $_POST['variants'] ?? '';
    if (!empty($variantsJson)) {
        $variants = is_string($variantsJson) ? json_decode($variantsJson, true) : $variantsJson;

        if (is_array($variants) && !empty($variants)) {
            // Delete existing variants for this product (only if creating new ones)
            Database::delete('product_variants', 'parent_product_id = ?', [$productId]);

            // Also delete existing variant currency overrides
            Database::query(
                "DELETE vcp FROM variant_currency_prices vcp 
                 INNER JOIN product_variants pv ON pv.id = vcp.variant_id 
                 WHERE pv.parent_product_id = ?",
                [$productId]
            );

            // Insert new variants
            foreach ($variants as $variant) {
                // Handle new format (with 'attributes' object) or legacy format
                $attributes = isset($variant['attributes']) ? $variant['attributes'] : $variant;

                // Build SKU and name from attributes
                $attrValues = is_array($attributes) ? array_values($attributes) : [];
                $variantSku = isset($variant['sku']) && !empty($variant['sku'])
                    ? $variant['sku']
                    : $sku . '-' . strtoupper(str_replace(' ', '-', implode('-', $attrValues)));
                $variantName = $name . ' - ' . implode(' / ', $attrValues);

                // Calculate final price from base price + adjustment
                $priceAdjustment = isset($variant['price_adjustment']) ? (float) $variant['price_adjustment'] : 0;
                $finalPrice = $price + $priceAdjustment;

                // Get other pricing fields
                $specialPrice = isset($variant['special_price']) && $variant['special_price'] !== null
                    ? (float) $variant['special_price']
                    : null;
                $costPrice = isset($variant['cost_price']) && $variant['cost_price'] !== null
                    ? (float) $variant['cost_price']
                    : null;

                // Get inventory fields
                $stock = isset($variant['stock']) ? (int) $variant['stock'] : 0;
                $weight = isset($variant['weight']) && $variant['weight'] !== null ? (float) $variant['weight'] : null;
                $isActive = isset($variant['is_active']) ? (int) $variant['is_active'] : 1;
                $isDefault = isset($variant['is_default']) ? (int) $variant['is_default'] : 0;

                $variantId = Database::insert('product_variants', [
                    'parent_product_id' => $productId,
                    'sku' => $variantSku,
                    'name' => $variantName,
                    'attributes' => json_encode($attributes),
                    'price' => $finalPrice,
                    'special_price' => $specialPrice,
                    'quantity' => $stock,
                    'weight' => $weight,
                    'is_active' => $isActive,
                    'is_default' => $isDefault,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Save variant currency overrides if present
                if (!empty($variant['currency_overrides']) && is_array($variant['currency_overrides'])) {
                    foreach ($variant['currency_overrides'] as $currencyCode => $overridePrice) {
                        if ($overridePrice !== null && $overridePrice !== '') {
                            // Use existing product_currency_prices table with variant_id or create variant-specific table
                            Database::query(
                                "INSERT INTO variant_currency_prices (variant_id, currency_code, price, created_at) 
                                 VALUES (?, ?, ?, NOW())
                                 ON DUPLICATE KEY UPDATE price = VALUES(price)",
                                [$variantId, $currencyCode, (float) $overridePrice]
                            );
                        }
                    }
                }
            }

            // Ensure at least one variant is marked as default
            $hasDefault = Database::fetch(
                "SELECT id FROM product_variants WHERE parent_product_id = ? AND is_default = 1 LIMIT 1",
                [$productId]
            );
            if (!$hasDefault) {
                // Set the first variant as default
                $firstVariant = Database::fetch(
                    "SELECT id FROM product_variants WHERE parent_product_id = ? ORDER BY id ASC LIMIT 1",
                    [$productId]
                );
                if ($firstVariant) {
                    Database::update('product_variants', ['is_default' => 1], 'id = ?', [$firstVariant['id']]);
                }
            }
        }
    }

    // =====================================================
    // HANDLE IMAGE UPLOADS
    // =====================================================
    $uploadDir = __DIR__ . '/../uploads/products/' . $productId . '/';

    // Create product upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Get existing images to delete (if specified)
    $deleteImageIds = [];
    if (!empty($_POST['delete_image_ids'])) {
        $deleteImageIds = json_decode($_POST['delete_image_ids'], true) ?: [];
    }

    // Delete specified images
    foreach ($deleteImageIds as $imgId) {
        $existingImg = Database::fetch(
            "SELECT image_url FROM product_images WHERE id = ? AND product_id = ?",
            [(int) $imgId, $productId]
        );
        if ($existingImg) {
            // Delete physical file
            $filePath = __DIR__ . '/../' . $existingImg['image_url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            Database::delete('product_images', 'id = ?', [(int) $imgId]);
        }
    }

    // Update sort order for existing images
    if (!empty($_POST['image_order'])) {
        $imageOrder = json_decode($_POST['image_order'], true) ?: [];
        foreach ($imageOrder as $order => $imgId) {
            if (is_numeric($imgId)) {
                Database::update(
                    'product_images',
                    ['sort_order' => (int) $order],
                    'id = ? AND product_id = ?',
                    [(int) $imgId, $productId]
                );
            }
        }
    }

    // Get current max sort order
    $maxSort = Database::fetch(
        "SELECT COALESCE(MAX(sort_order), -1) as max_sort FROM product_images WHERE product_id = ?",
        [$productId]
    );
    $sortOrder = ($maxSort['max_sort'] ?? -1) + 1;

    // Handle new image uploads
    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
        $files = $_FILES['images'];
        $uploadedImages = [];

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK)
                continue;
            if ($files['size'][$i] > 5 * 1024 * 1024)
                continue; // 5MB limit

            $tmpName = $files['tmp_name'][$i];
            $originalName = $files['name'][$i];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($ext, $allowedTypes))
                continue;

            // Generate unique filename
            $newFilename = uniqid('img_') . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $newFilename;

            if (move_uploaded_file($tmpName, $targetPath)) {
                // Save to database
                $imageUrl = 'uploads/products/' . $productId . '/' . $newFilename;
                Database::insert('product_images', [
                    'product_id' => $productId,
                    'image_url' => $imageUrl,
                    'alt_text' => pathinfo($originalName, PATHINFO_FILENAME),
                    'sort_order' => $sortOrder,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $uploadedImages[] = $imageUrl;
                $sortOrder++;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'id' => $productId
    ]);
}

// =====================================================================
// DELETE PRODUCT
// =====================================================================
function handleDeleteProduct(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
        return;
    }

    // Delete related data
    Database::delete('product_categories', 'product_id = ?', [$id]);
    Database::delete('product_variants', 'parent_product_id = ?', [$id]);
    Database::delete('product_images', 'product_id = ?', [$id]);

    // Delete product
    $deleted = Database::delete('products', 'id = ? AND shop_id = ?', [$id, $shopId]);

    echo json_encode([
        'success' => $deleted > 0,
        'message' => $deleted > 0 ? 'Produkt gelöscht' : 'Produkt nicht gefunden'
    ]);
}

// =====================================================================
// TOGGLE STATUS
// =====================================================================
function handleToggleStatus(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'draft';

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
        return;
    }

    $validStatuses = ['draft', 'active', 'archived'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        return;
    }

    Database::update('products', [
        'status' => $status,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ? AND shop_id = ?', [$id, $shopId]);

    $statusLabels = [
        'draft' => 'Entwurf',
        'active' => 'Aktiv',
        'archived' => 'Archiviert'
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Status geändert zu: ' . $statusLabels[$status]
    ]);
}

// =====================================================================
// GET CATEGORIES
// =====================================================================
function handleGetCategories(int $shopId): void
{
    $categories = Database::fetchAll(
        "SELECT id, name, parent_id, slug FROM categories WHERE shop_id = ? AND is_active = 1 ORDER BY name",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);
}

// =====================================================================
// GET STATS (for tabs)
// =====================================================================
function handleGetStats(int $shopId): void
{
    $stats = [
        'all' => 0,
        'active' => 0,
        'draft' => 0,
        'archived' => 0
    ];

    $result = Database::fetchAll(
        "SELECT status, COUNT(*) as count FROM products WHERE shop_id = ? GROUP BY status",
        [$shopId]
    );

    $total = 0;
    foreach ($result as $row) {
        $stats[$row['status']] = (int) $row['count'];
        $total += (int) $row['count'];
    }
    $stats['all'] = $total;

    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}

// =====================================================================
// BULK ACTION
// =====================================================================
function handleBulkAction(int $shopId): void
{
    $ids = $_POST['ids'] ?? [];
    $action = $_POST['bulk_action'] ?? '';

    if (is_string($ids)) {
        $ids = json_decode($ids, true) ?: [];
    }

    if (empty($ids)) {
        echo json_encode(['success' => false, 'error' => 'Keine Produkte ausgewählt']);
        return;
    }

    $ids = array_map('intval', $ids);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $count = 0;

    switch ($action) {
        case 'activate':
            $stmt = Database::query(
                "UPDATE products SET status = 'active', updated_at = NOW() WHERE shop_id = ? AND id IN ({$placeholders})",
                array_merge([$shopId], $ids)
            );
            $count = $stmt->rowCount();
            $message = "{$count} Produkte aktiviert";
            break;

        case 'deactivate':
            $stmt = Database::query(
                "UPDATE products SET status = 'draft', updated_at = NOW() WHERE shop_id = ? AND id IN ({$placeholders})",
                array_merge([$shopId], $ids)
            );
            $count = $stmt->rowCount();
            $message = "{$count} Produkte deaktiviert";
            break;

        case 'archive':
            $stmt = Database::query(
                "UPDATE products SET status = 'archived', updated_at = NOW() WHERE shop_id = ? AND id IN ({$placeholders})",
                array_merge([$shopId], $ids)
            );
            $count = $stmt->rowCount();
            $message = "{$count} Produkte archiviert";
            break;

        case 'delete':
            foreach ($ids as $id) {
                Database::delete('product_categories', 'product_id = ?', [$id]);
                Database::delete('product_variants', 'parent_product_id = ?', [$id]);
                Database::delete('product_images', 'product_id = ?', [$id]);
            }
            $stmt = Database::query(
                "DELETE FROM products WHERE shop_id = ? AND id IN ({$placeholders})",
                array_merge([$shopId], $ids)
            );
            $count = $stmt->rowCount();
            $message = "{$count} Produkte gelöscht";
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Ungültige Aktion']);
            return;
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'count' => $count
    ]);
}

// =====================================================================
// HELPER FUNCTIONS
// =====================================================================
function generateSlug(string $text): string
{
    $text = strtolower($text);
    $text = preg_replace('/[äÄ]/', 'ae', $text);
    $text = preg_replace('/[öÖ]/', 'oe', $text);
    $text = preg_replace('/[üÜ]/', 'ue', $text);
    $text = preg_replace('/ß/', 'ss', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

function generateUniqueSlug(int $shopId, string $customSlug, string $name, int $excludeId = 0): string
{
    $baseSlug = !empty(trim($customSlug)) ? generateSlug($customSlug) : generateSlug($name);

    if (empty($baseSlug)) {
        $baseSlug = 'produkt-' . time();
    }

    $slug = $baseSlug;
    $counter = 1;

    while (true) {
        $existing = Database::fetch(
            "SELECT id FROM products WHERE shop_id = ? AND slug = ? AND id != ?",
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

// =====================================================================
// GET TAX CLASSES
// =====================================================================
function handleGetTaxClasses(int $shopId): void
{
    $taxClasses = Database::fetchAll(
        "SELECT id, name, code, is_default FROM tax_classes WHERE shop_id = ? ORDER BY is_default DESC, name",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'tax_classes' => $taxClasses
    ]);
}

// =====================================================================
// EXPORT PRODUCTS
// =====================================================================
function handleExportProducts(int $shopId): void
{
    $format = $_GET['format'] ?? 'json';

    // Get all products with related data
    $products = Database::fetchAll(
        "SELECT p.*, 
                (SELECT GROUP_CONCAT(c.name SEPARATOR ', ') 
                 FROM product_categories pc 
                 JOIN categories c ON pc.category_id = c.id 
                 WHERE pc.product_id = p.id) as category_names,
                (SELECT GROUP_CONCAT(c.id) 
                 FROM product_categories pc 
                 JOIN categories c ON pc.category_id = c.id 
                 WHERE pc.product_id = p.id) as category_ids
         FROM products p 
         WHERE p.shop_id = ? 
         ORDER BY p.created_at DESC",
        [$shopId]
    );

    // Add images to each product
    foreach ($products as &$product) {
        $images = Database::fetchAll(
            "SELECT image_url, alt_text, sort_order FROM product_images WHERE product_id = ? ORDER BY sort_order",
            [$product['id']]
        );
        $product['images'] = $images;
    }

    $filename = 'products_export_' . date('Y-m-d_H-i-s');

    if ($format === 'sql') {
        // SQL Export
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '.sql"');

        $sql = "-- Products Export\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Shop ID: " . $shopId . "\n";
        $sql .= "-- Total Products: " . count($products) . "\n\n";

        foreach ($products as $p) {
            $sql .= "-- Product: " . $p['name'] . " (SKU: " . $p['sku'] . ")\n";
            $sql .= "INSERT INTO products (shop_id, type, sku, name, slug, short_description, description, ";
            $sql .= "price, special_price, special_price_from, special_price_to, cost_price, tax_class_id, ";
            $sql .= "status, is_visible, is_featured, is_new, manage_stock, quantity, low_stock_threshold, ";
            $sql .= "allow_backorders, weight, length, width, height, is_downloadable, download_limit, ";
            $sql .= "download_expiry_days, meta_title, meta_description, meta_keywords, created_at, updated_at) VALUES (";

            $values = [
                $p['shop_id'],
                "'" . addslashes($p['type']) . "'",
                "'" . addslashes($p['sku']) . "'",
                "'" . addslashes($p['name']) . "'",
                "'" . addslashes($p['slug']) . "'",
                "'" . addslashes($p['short_description'] ?? '') . "'",
                "'" . addslashes($p['description'] ?? '') . "'",
                $p['price'],
                $p['special_price'] ? $p['special_price'] : 'NULL',
                $p['special_price_from'] ? "'" . $p['special_price_from'] . "'" : 'NULL',
                $p['special_price_to'] ? "'" . $p['special_price_to'] . "'" : 'NULL',
                $p['cost_price'] ? $p['cost_price'] : 'NULL',
                $p['tax_class_id'],
                "'" . $p['status'] . "'",
                $p['is_visible'],
                $p['is_featured'],
                $p['is_new'],
                $p['manage_stock'],
                $p['quantity'],
                $p['low_stock_threshold'],
                $p['allow_backorders'],
                $p['weight'],
                $p['length'],
                $p['width'],
                $p['height'],
                $p['is_downloadable'],
                $p['download_limit'] ? $p['download_limit'] : 'NULL',
                $p['download_expiry_days'] ? $p['download_expiry_days'] : 'NULL',
                "'" . addslashes($p['meta_title'] ?? '') . "'",
                "'" . addslashes($p['meta_description'] ?? '') . "'",
                "'" . addslashes($p['meta_keywords'] ?? '') . "'",
                "'" . $p['created_at'] . "'",
                "'" . $p['updated_at'] . "'"
            ];

            $sql .= implode(", ", $values) . ");\n\n";
        }

        echo $sql;
        exit;
    } else {
        // JSON Export (default)
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '.json"');

        $export = [
            'export_info' => [
                'generated_at' => date('Y-m-d H:i:s'),
                'shop_id' => $shopId,
                'total_products' => count($products),
                'format' => 'json'
            ],
            'products' => $products
        ];

        echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// =====================================================================
// MULTI-CURRENCY PRICING
// =====================================================================

/**
 * Get shop's default currency and all active currencies
 */
function handleGetShopCurrency(int $shopId): void
{
    // Get default currency
    $default = Database::fetch(
        "SELECT code, name, symbol, exchange_rate FROM currencies WHERE shop_id = ? AND is_default = 1",
        [$shopId]
    );

    if (!$default) {
        // Fallback to USD
        $default = ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.0];
    }

    // Get all active currencies for dropdown
    $currencies = Database::fetchAll(
        "SELECT code, name, symbol, exchange_rate, decimal_places 
         FROM currencies 
         WHERE shop_id = ? AND is_active = 1 
         ORDER BY CASE WHEN is_default = 1 THEN 0 ELSE 1 END, name",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'default_currency' => $default,
        'currencies' => $currencies
    ]);
}

/**
 * Get all currencies with exchange rates for conversion
 */
function handleGetCurrencies(int $shopId): void
{
    $currencies = Database::fetchAll(
        "SELECT code, name, symbol, exchange_rate, decimal_places, is_default 
         FROM currencies 
         WHERE shop_id = ? AND is_active = 1 
         ORDER BY name",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'currencies' => $currencies
    ]);
}

/**
 * Save currency-specific prices for a product
 */
function handleSaveCurrencyPrices(int $shopId): void
{
    $productId = (int) ($_POST['product_id'] ?? 0);
    $pricesJson = $_POST['prices'] ?? '[]';
    $roundingStep = $_POST['rounding_step'] ?? null;
    $baseCurrency = $_POST['base_currency'] ?? null;

    if ($productId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
        return;
    }

    $prices = json_decode($pricesJson, true);
    if (!is_array($prices)) {
        echo json_encode(['success' => false, 'error' => 'Invalid prices format']);
        return;
    }

    // Update product's base currency and rounding
    if ($baseCurrency || $roundingStep !== null) {
        $updates = [];
        $params = [];

        if ($baseCurrency) {
            $updates[] = "base_currency = ?";
            $params[] = $baseCurrency;
        }
        if ($roundingStep !== null) {
            $updates[] = "price_rounding_step = ?";
            $params[] = $roundingStep !== '' ? (float) $roundingStep : null;
        }

        if (!empty($updates)) {
            $params[] = $productId;
            $params[] = $shopId;
            Database::query(
                "UPDATE products SET " . implode(', ', $updates) . " WHERE id = ? AND shop_id = ?",
                $params
            );
        }
    }

    // Delete existing custom prices for this product
    Database::delete('product_currency_prices', 'product_id = ?', [$productId]);

    // Insert new custom prices
    $inserted = 0;
    foreach ($prices as $currencyCode => $priceData) {
        $price = isset($priceData['price']) && $priceData['price'] !== '' ? (float) $priceData['price'] : null;
        $specialPrice = isset($priceData['special_price']) && $priceData['special_price'] !== '' ? (float) $priceData['special_price'] : null;

        // Only insert if at least one price is set
        if ($price !== null || $specialPrice !== null) {
            Database::insert('product_currency_prices', [
                'product_id' => $productId,
                'currency_code' => strtoupper($currencyCode),
                'price' => $price,
                'special_price' => $specialPrice
            ]);
            $inserted++;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Währungspreise gespeichert ({$inserted} Überschreibungen)",
        'inserted' => $inserted
    ]);
}

/**
 * Calculate converted prices for preview
 */
function handleCalculatePrices(int $shopId): void
{
    $basePrice = (float) ($_GET['base_price'] ?? $_POST['base_price'] ?? 0);
    $specialPrice = (float) ($_GET['special_price'] ?? $_POST['special_price'] ?? 0);
    $baseCurrency = strtoupper($_GET['base_currency'] ?? $_POST['base_currency'] ?? 'USD');
    $roundingStep = (float) ($_GET['rounding_step'] ?? $_POST['rounding_step'] ?? 0);
    $productId = (int) ($_GET['product_id'] ?? $_POST['product_id'] ?? 0);

    // Get base currency exchange rate
    $baseCurrencyData = Database::fetch(
        "SELECT exchange_rate FROM currencies WHERE shop_id = ? AND code = ?",
        [$shopId, $baseCurrency]
    );
    $baseRate = $baseCurrencyData ? (float) $baseCurrencyData['exchange_rate'] : 1.0;

    // Get all active currencies
    $currencies = Database::fetchAll(
        "SELECT code, name, symbol, exchange_rate, decimal_places, is_default 
         FROM currencies 
         WHERE shop_id = ? AND is_active = 1 
         ORDER BY name",
        [$shopId]
    );

    // Get existing custom prices for this product
    $customPrices = [];
    if ($productId > 0) {
        $existingPrices = Database::fetchAll(
            "SELECT currency_code, price, special_price FROM product_currency_prices WHERE product_id = ?",
            [$productId]
        );
        foreach ($existingPrices as $cp) {
            $customPrices[$cp['currency_code']] = [
                'price' => $cp['price'],
                'special_price' => $cp['special_price']
            ];
        }
    }

    $result = [];
    foreach ($currencies as $curr) {
        $code = $curr['code'];
        $targetRate = (float) $curr['exchange_rate'];
        $decimals = (int) ($curr['decimal_places'] ?? 2);

        // Convert: basePrice in baseCurrency → USD → targetCurrency
        // Formula: price * (targetRate / baseRate)
        $convertedPrice = $basePrice * ($targetRate / $baseRate);
        $convertedSpecial = $specialPrice > 0 ? $specialPrice * ($targetRate / $baseRate) : 0;

        // Apply rounding if set
        if ($roundingStep > 0) {
            $convertedPrice = roundToStep($convertedPrice, $roundingStep);
            if ($convertedSpecial > 0) {
                $convertedSpecial = roundToStep($convertedSpecial, $roundingStep);
            }
        }

        // Check for custom override
        $hasOverride = isset($customPrices[$code]);
        $overridePrice = $hasOverride ? $customPrices[$code]['price'] : null;
        $overrideSpecial = $hasOverride ? $customPrices[$code]['special_price'] : null;

        $result[$code] = [
            'code' => $code,
            'name' => $curr['name'],
            'symbol' => $curr['symbol'],
            'is_default' => (bool) $curr['is_default'],
            'calculated_price' => round($convertedPrice, $decimals),
            'calculated_special' => round($convertedSpecial, $decimals),
            'override_price' => $overridePrice,
            'override_special' => $overrideSpecial,
            'has_override' => $hasOverride,
            'final_price' => $overridePrice !== null ? (float) $overridePrice : round($convertedPrice, $decimals),
            'final_special' => $overrideSpecial !== null ? (float) $overrideSpecial : ($convertedSpecial > 0 ? round($convertedSpecial, $decimals) : null)
        ];
    }

    echo json_encode([
        'success' => true,
        'base_currency' => $baseCurrency,
        'base_price' => $basePrice,
        'special_price' => $specialPrice,
        'rounding_step' => $roundingStep,
        'prices' => $result
    ]);
}

/**
 * Round price to nearest step
 */
function roundToStep(float $value, float $step): float
{
    if ($step <= 0)
        return $value;
    return round($value / $step) * $step;
}
