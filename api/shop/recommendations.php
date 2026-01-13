<?php
/**
 * Shop Recommendations API
 * Returns recommendations for frontend display with smart visibility logic
 * 
 * Usage: /api/shop/recommendations.php?position=homepage&session_id=xxx
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../admin/config.php';
require_once __DIR__ . '/../../admin/includes/Database.php';

Database::configure($database);

$position = $_GET['position'] ?? 'homepage';
$shopId = (int) ($_GET['shop_id'] ?? 1);
$sessionId = $_GET['session_id'] ?? session_id();
$customerId = (int) ($_GET['customer_id'] ?? 0);
$productId = (int) ($_GET['product_id'] ?? 0); // Current product (for product_page)

try {
    // Get active rules for this position
    $rules = Database::fetchAll(
        "SELECT * FROM recommendation_rules 
         WHERE shop_id = ? AND position = ? AND is_active = 1
         ORDER BY id ASC",
        [$shopId, $position]
    );

    $recommendations = [];

    foreach ($rules as $rule) {
        $ruleData = [
            'rule_id' => $rule['id'],
            'name' => $rule['name'],
            'type' => $rule['rule_type'],
            'display' => true, // Will be set to false if no products
            'products' => [],
            'css_class' => 'recommendation-section recommendation-' . $rule['rule_type']
        ];

        // Fetch products based on rule type
        switch ($rule['rule_type']) {
            case 'recently_viewed':
                // STRICT CHECK: Only show if user has actually viewed products
                $viewedProducts = getRecentlyViewedProducts($shopId, $sessionId, $customerId, $rule['product_count']);

                if (empty($viewedProducts)) {
                    // NO products viewed - hide section completely
                    $ruleData['display'] = false;
                    $ruleData['reason'] = 'no_viewed_products';
                } else {
                    $ruleData['products'] = $viewedProducts;
                }
                break;

            case 'similar':
                // Only works on product_page or cart (needs reference product)
                if (!$productId && $position !== 'cart') {
                    $ruleData['display'] = false;
                    $ruleData['reason'] = 'no_reference_product';
                } else {
                    $ruleData['products'] = getSimilarProducts($shopId, $productId, $rule['product_count']);
                    if (empty($ruleData['products'])) {
                        $ruleData['display'] = false;
                        $ruleData['reason'] = 'no_similar_products';
                    }
                }
                break;

            case 'bought_together':
                // Needs reference product
                if (!$productId && $position !== 'cart') {
                    $ruleData['display'] = false;
                    $ruleData['reason'] = 'no_reference_product';
                } else {
                    $ruleData['products'] = getBoughtTogetherProducts($shopId, $productId, $rule['product_count']);
                    if (empty($ruleData['products'])) {
                        // Fallback to trending if no bought-together data
                        $ruleData['products'] = getTrendingProducts($shopId, $rule['product_count']);
                    }
                }
                break;

            case 'trending':
                $ruleData['products'] = getTrendingProducts($shopId, $rule['product_count']);
                if (empty($ruleData['products'])) {
                    $ruleData['display'] = false;
                    $ruleData['reason'] = 'no_trending_products';
                }
                break;

            case 'bestseller':
                $ruleData['products'] = getBestsellerProducts($shopId, $rule['product_count']);
                if (empty($ruleData['products'])) {
                    $ruleData['display'] = false;
                    $ruleData['reason'] = 'no_bestseller_products';
                }
                break;
        }

        $recommendations[] = $ruleData;
    }

    echo json_encode([
        'success' => true,
        'position' => $position,
        'recommendations' => $recommendations,
        'count' => count(array_filter($recommendations, fn($r) => $r['display']))
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// PRODUCT FETCHING FUNCTIONS
// =====================================================================

/**
 * Get products the user has recently viewed
 * Returns empty array if no views exist for this user
 */
function getRecentlyViewedProducts(int $shopId, string $sessionId, int $customerId, int $limit): array
{
    $where = "shop_id = ?";
    $params = [$shopId];

    // Check by customer_id first, then session_id
    if ($customerId > 0) {
        $where .= " AND customer_id = ?";
        $params[] = $customerId;
    } else {
        $where .= " AND session_id = ?";
        $params[] = $sessionId;
    }

    $views = Database::fetchAll(
        "SELECT DISTINCT product_id, MAX(viewed_at) as last_viewed 
         FROM product_views 
         WHERE {$where}
         GROUP BY product_id
         ORDER BY last_viewed DESC
         LIMIT ?",
        array_merge($params, [$limit])
    );

    if (empty($views)) {
        return []; // No views = no products = section will be hidden
    }

    $productIds = array_column($views, 'product_id');
    return getProductsByIds($shopId, $productIds);
}

/**
 * Get similar products based on category
 */
function getSimilarProducts(int $shopId, int $productId, int $limit): array
{
    if (!$productId)
        return [];

    // Get product's category
    $product = Database::fetch(
        "SELECT category_id FROM products WHERE id = ? AND shop_id = ?",
        [$productId, $shopId]
    );

    if (!$product || !$product['category_id']) {
        return [];
    }

    return Database::fetchAll(
        "SELECT id, name, price, image_url 
         FROM products 
         WHERE shop_id = ? AND category_id = ? AND id != ? AND is_active = 1
         ORDER BY RAND()
         LIMIT ?",
        [$shopId, $product['category_id'], $productId, $limit]
    ) ?: [];
}

/**
 * Get products that were bought together
 */
function getBoughtTogetherProducts(int $shopId, int $productId, int $limit): array
{
    if (!$productId)
        return [];

    // This would need order_items table to work properly
    // For now, return empty to fallback to trending
    return [];
}

/**
 * Get trending products (most viewed recently)
 */
function getTrendingProducts(int $shopId, int $limit): array
{
    $trending = Database::fetchAll(
        "SELECT product_id, COUNT(*) as views 
         FROM product_views 
         WHERE shop_id = ? AND viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
         GROUP BY product_id
         ORDER BY views DESC
         LIMIT ?",
        [$shopId, $limit]
    );

    if (empty($trending)) {
        // Fallback to random products
        return Database::fetchAll(
            "SELECT id, name, price, image_url 
             FROM products 
             WHERE shop_id = ? AND is_active = 1
             ORDER BY RAND()
             LIMIT ?",
            [$shopId, $limit]
        ) ?: [];
    }

    $productIds = array_column($trending, 'product_id');
    return getProductsByIds($shopId, $productIds);
}

/**
 * Get bestseller products
 */
function getBestsellerProducts(int $shopId, int $limit): array
{
    // This would need order_items table
    // For now, fallback to random featured products
    return Database::fetchAll(
        "SELECT id, name, price, image_url 
         FROM products 
         WHERE shop_id = ? AND is_active = 1 AND is_featured = 1
         ORDER BY sales_count DESC, RAND()
         LIMIT ?",
        [$shopId, $limit]
    ) ?: [];
}

/**
 * Get products by IDs
 */
function getProductsByIds(int $shopId, array $productIds): array
{
    if (empty($productIds))
        return [];

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));

    return Database::fetchAll(
        "SELECT id, name, price, image_url 
         FROM products 
         WHERE shop_id = ? AND id IN ({$placeholders}) AND is_active = 1",
        array_merge([$shopId], $productIds)
    ) ?: [];
}
