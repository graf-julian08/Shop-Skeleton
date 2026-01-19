<?php
/**
 * Categories API
 * Endpoints: get_categories, get_category, save_category, delete_category, toggle_status
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';

Database::configure($database);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_categories':
            handleGetCategories($shopId);
            break;
        case 'get_category':
            handleGetCategory($shopId);
            break;
        case 'save_category':
            handleSaveCategory($shopId);
            break;
        case 'delete_category':
            handleDeleteCategory($shopId);
            break;
        case 'toggle_status':
            handleToggleStatus($shopId);
            break;
        case 'get_stats':
            handleGetStats($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// GET CATEGORIES (with hierarchy and product counts)
// =====================================================================
function handleGetCategories(int $shopId): void
{
    $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? '';

    $where = ["c.shop_id = ?"];
    $params = [$shopId];

    if ($search) {
        $where[] = "c.name LIKE ?";
        $params[] = "%{$search}%";
    }

    if ($status === 'active') {
        $where[] = "c.is_active = 1";
    } elseif ($status === 'inactive') {
        $where[] = "c.is_active = 0";
    }

    $whereClause = implode(' AND ', $where);

    // Get categories with product count
    $query = "
        SELECT c.*, 
               (SELECT COUNT(*) FROM product_categories pc WHERE pc.category_id = c.id) as product_count,
               (SELECT COUNT(*) FROM categories sub WHERE sub.parent_id = c.id) as children_count
        FROM categories c 
        WHERE {$whereClause}
        ORDER BY c.parent_id IS NULL DESC, c.sort_order ASC, c.name ASC
    ";

    $categories = Database::fetchAll($query, $params);

    // Build hierarchy
    $categoriesById = [];
    foreach ($categories as $cat) {
        $cat['children'] = [];
        $categoriesById[$cat['id']] = $cat;
    }

    $hierarchy = [];
    foreach ($categoriesById as $id => $cat) {
        if ($cat['parent_id'] && isset($categoriesById[$cat['parent_id']])) {
            $categoriesById[$cat['parent_id']]['children'][] = &$categoriesById[$id];
        } else {
            $hierarchy[] = &$categoriesById[$id];
        }
    }

    echo json_encode([
        'success' => true,
        'categories' => $hierarchy,
        'flat' => $categories
    ]);
}

// =====================================================================
// GET SINGLE CATEGORY
// =====================================================================
function handleGetCategory(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid category ID']);
        return;
    }

    $category = Database::fetch(
        "SELECT c.*, 
                (SELECT COUNT(*) FROM product_categories pc WHERE pc.category_id = c.id) as product_count
         FROM categories c 
         WHERE c.id = ? AND c.shop_id = ?",
        [$id, $shopId]
    );

    if (!$category) {
        echo json_encode(['success' => false, 'error' => 'Category not found']);
        return;
    }

    // Get parent info if exists
    if ($category['parent_id']) {
        $parent = Database::fetch(
            "SELECT id, name FROM categories WHERE id = ?",
            [$category['parent_id']]
        );
        $category['parent'] = $parent;
    }

    // Get children
    $children = Database::fetchAll(
        "SELECT id, name, slug, is_active, sort_order,
                (SELECT COUNT(*) FROM product_categories pc WHERE pc.category_id = c.id) as product_count
         FROM categories c 
         WHERE c.parent_id = ? 
         ORDER BY c.sort_order, c.name",
        [$id]
    );
    $category['children'] = $children;

    echo json_encode([
        'success' => true,
        'category' => $category
    ]);
}

// =====================================================================
// SAVE CATEGORY (Create or Update)
// =====================================================================
function handleSaveCategory(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $parentId = (int) ($_POST['parent_id'] ?? 0) ?: null;
    $isActive = (int) ($_POST['is_active'] ?? 1);
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $metaKeywords = trim($_POST['meta_keywords'] ?? '');

    // Validation
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Kategoriename ist erforderlich';
    }

    // Check if trying to deactivate a category with products
    if ($id > 0 && $isActive == 0) {
        $currentStatus = Database::fetch("SELECT is_active FROM categories WHERE id = ?", [$id]);
        if ($currentStatus && $currentStatus['is_active'] == 1) {
            $productCount = Database::fetch(
                "SELECT COUNT(*) as count FROM product_categories WHERE category_id = ?",
                [$id]
            )['count'];

            if ($productCount > 0) {
                $errors[] = "Kategorie kann nicht deaktiviert werden. Sie enthält {$productCount} Produkte.";
            }
        }
    }

    // Generate slug if empty
    if (empty($slug)) {
        $slug = generateSlug($name);
    }

    // Check slug uniqueness
    $existingSlug = Database::fetch(
        "SELECT id FROM categories WHERE slug = ? AND shop_id = ? AND id != ?",
        [$slug, $shopId, $id]
    );
    if ($existingSlug) {
        $slug = $slug . '-' . time();
    }

    // Prevent circular parent reference
    if ($parentId && $parentId === $id) {
        $errors[] = 'Kategorie kann nicht ihre eigene Elternkategorie sein';
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        return;
    }

    // Calculate level and path
    $level = 0;
    $path = null;
    if ($parentId) {
        $parent = Database::fetch("SELECT level, path, id FROM categories WHERE id = ?", [$parentId]);
        if ($parent) {
            $level = ($parent['level'] ?? 0) + 1;
            $path = $parent['path'] ? $parent['path'] . '/' . $parentId : (string) $parentId;
        }
    }

    if ($id > 0) {
        // Update
        Database::query(
            "UPDATE categories SET 
                name = ?, slug = ?, description = ?, parent_id = ?, 
                is_active = ?, meta_title = ?, meta_description = ?, meta_keywords = ?,
                level = ?, path = ?, updated_at = NOW()
             WHERE id = ? AND shop_id = ?",
            [
                $name,
                $slug,
                $description,
                $parentId,
                $isActive,
                $metaTitle,
                $metaDescription,
                $metaKeywords,
                $level,
                $path,
                $id,
                $shopId
            ]
        );

        $message = 'Kategorie aktualisiert';
    } else {
        // Insert
        $id = Database::insert('categories', [
            'shop_id' => $shopId,
            'parent_id' => $parentId,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'is_active' => $isActive,
            'is_visible_in_menu' => 1, // Always visible
            'sort_order' => 0, // Default
            'level' => $level,
            'path' => $path,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_keywords' => $metaKeywords,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $message = 'Kategorie erstellt';
    }

    // Handle image upload
    if (!empty($_FILES['image']['tmp_name'])) {
        $imagePath = handleImageUpload($id, 'image');
        if ($imagePath) {
            Database::query(
                "UPDATE categories SET image_path = ? WHERE id = ?",
                [$imagePath, $id]
            );
        }
    }

    // Handle banner upload
    if (!empty($_FILES['banner']['tmp_name'])) {
        $bannerPath = handleImageUpload($id, 'banner');
        if ($bannerPath) {
            Database::query(
                "UPDATE categories SET banner_path = ? WHERE id = ?",
                [$bannerPath, $id]
            );
        }
    }

    // Handle image deletion
    if ($_POST['delete_image'] ?? false) {
        $cat = Database::fetch("SELECT image_path FROM categories WHERE id = ?", [$id]);
        if ($cat && $cat['image_path']) {
            $fullPath = __DIR__ . '/../' . $cat['image_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            Database::query("UPDATE categories SET image_path = NULL WHERE id = ?", [$id]);
        }
    }

    if ($_POST['delete_banner'] ?? false) {
        $cat = Database::fetch("SELECT banner_path FROM categories WHERE id = ?", [$id]);
        if ($cat && $cat['banner_path']) {
            $fullPath = __DIR__ . '/../' . $cat['banner_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            Database::query("UPDATE categories SET banner_path = NULL WHERE id = ?", [$id]);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'id' => $id
    ]);
}

// =====================================================================
// DELETE CATEGORY
// =====================================================================
function handleDeleteCategory(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid category ID']);
        return;
    }

    // Check for children - still block this
    $childCount = Database::fetch(
        "SELECT COUNT(*) as count FROM categories WHERE parent_id = ?",
        [$id]
    )['count'];

    if ($childCount > 0) {
        echo json_encode([
            'success' => false,
            'error' => "Kategorie hat {$childCount} Unterkategorien. Bitte zuerst diese löschen."
        ]);
        return;
    }

    // Get category for image cleanup
    $category = Database::fetch(
        "SELECT image_path, banner_path FROM categories WHERE id = ? AND shop_id = ?",
        [$id, $shopId]
    );

    if (!$category) {
        echo json_encode(['success' => false, 'error' => 'Kategorie nicht gefunden']);
        return;
    }

    // Get product count for message
    $productCount = Database::fetch(
        "SELECT COUNT(*) as count FROM product_categories WHERE category_id = ?",
        [$id]
    )['count'];

    // Delete product-category links first
    if ($productCount > 0) {
        // Get product IDs linked to this category
        $linkedProducts = Database::fetchAll(
            "SELECT product_id FROM product_categories WHERE category_id = ?",
            [$id]
        );

        // Delete the category links
        Database::delete('product_categories', 'category_id = ?', [$id]);

        // Delete products that were only in this category (no other categories)
        foreach ($linkedProducts as $lp) {
            $otherCats = Database::fetch(
                "SELECT COUNT(*) as count FROM product_categories WHERE product_id = ?",
                [$lp['product_id']]
            )['count'];

            if ($otherCats == 0) {
                // Delete product images first
                $productImages = Database::fetchAll(
                    "SELECT image_url FROM product_images WHERE product_id = ?",
                    [$lp['product_id']]
                );
                foreach ($productImages as $img) {
                    $imgPath = __DIR__ . '/../' . $img['image_url'];
                    if (file_exists($imgPath)) {
                        unlink($imgPath);
                    }
                }
                Database::delete('product_images', 'product_id = ?', [$lp['product_id']]);
                Database::delete('products', 'id = ?', [$lp['product_id']]);
            }
        }
    }

    // Delete category images
    foreach (['image_path', 'banner_path'] as $field) {
        if ($category[$field]) {
            $fullPath = __DIR__ . '/../' . $category[$field];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    // Delete category
    Database::delete('categories', 'id = ? AND shop_id = ?', [$id, $shopId]);

    $message = 'Kategorie gelöscht';
    if ($productCount > 0) {
        $message .= " ({$productCount} verknüpfte Produkte wurden ebenfalls entfernt)";
    }

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);
}

// =====================================================================
// TOGGLE STATUS
// =====================================================================
function handleToggleStatus(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $status = (int) ($_POST['is_active'] ?? 0);

    // If trying to deactivate, check for products
    if ($status == 0) {
        $productCount = Database::fetch(
            "SELECT COUNT(*) as count FROM product_categories WHERE category_id = ?",
            [$id]
        )['count'];

        if ($productCount > 0) {
            echo json_encode([
                'success' => false,
                'error' => "Kategorie kann nicht deaktiviert werden. Sie enthält {$productCount} Produkte. Bitte erst die Produkte entfernen oder in eine andere Kategorie verschieben."
            ]);
            return;
        }
    }

    Database::query(
        "UPDATE categories SET is_active = ?, updated_at = NOW() WHERE id = ? AND shop_id = ?",
        [$status, $id, $shopId]
    );

    echo json_encode([
        'success' => true,
        'message' => $status ? 'Kategorie aktiviert' : 'Kategorie deaktiviert'
    ]);
}

// =====================================================================
// GET STATS
// =====================================================================
function handleGetStats(int $shopId): void
{
    $stats = Database::fetch(
        "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive,
            SUM(CASE WHEN parent_id IS NULL THEN 1 ELSE 0 END) as root,
            SUM(CASE WHEN parent_id IS NOT NULL THEN 1 ELSE 0 END) as children
         FROM categories WHERE shop_id = ?",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}

// =====================================================================
// HELPER FUNCTIONS
// =====================================================================
function generateSlug(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $text = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function handleImageUpload(int $categoryId, string $type): ?string
{
    $file = $_FILES[$type] ?? null;
    if (!$file || !$file['tmp_name']) {
        return null;
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }

    // Validate size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return null;
    }

    // Create upload directory
    $uploadDir = __DIR__ . '/../uploads/categories/' . $categoryId;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $type . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return 'uploads/categories/' . $categoryId . '/' . $filename;
    }

    return null;
}
