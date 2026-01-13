<?php
/**
 * Shop Tracking API
 * Tracks product views and recommendation clicks
 * 
 * Usage:
 * - Track view: POST /api/shop/tracking.php?action=view&product_id=123&session_id=xxx
 * - Track click: POST /api/shop/tracking.php?action=click&rule_id=1&product_id=123&session_id=xxx
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../admin/config.php';
require_once __DIR__ . '/../../admin/includes/Database.php';

Database::configure($database);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);
$sessionId = $_GET['session_id'] ?? $_POST['session_id'] ?? session_id();
$customerId = (int) ($_GET['customer_id'] ?? $_POST['customer_id'] ?? 0);
$productId = (int) ($_GET['product_id'] ?? $_POST['product_id'] ?? 0);

try {
    switch ($action) {
        case 'view':
            trackProductView($shopId, $productId, $sessionId, $customerId);
            break;

        case 'click':
            $ruleId = (int) ($_GET['rule_id'] ?? $_POST['rule_id'] ?? 0);
            trackRecommendationClick($shopId, $ruleId, $productId, $sessionId, $customerId);
            break;

        case 'convert':
            // Mark recommendation click as converted (after purchase)
            $clickId = (int) ($_GET['click_id'] ?? $_POST['click_id'] ?? 0);
            markConversion($clickId);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

/**
 * Track a product view
 */
function trackProductView(int $shopId, int $productId, string $sessionId, int $customerId): void
{
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid product_id']);
        return;
    }

    // Prevent duplicate views within short timeframe (5 minutes)
    $recentView = Database::fetch(
        "SELECT id FROM product_views 
         WHERE shop_id = ? AND product_id = ? AND session_id = ? 
         AND viewed_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)",
        [$shopId, $productId, $sessionId]
    );

    if ($recentView) {
        echo json_encode(['success' => true, 'message' => 'View already tracked recently']);
        return;
    }

    Database::insert('product_views', [
        'shop_id' => $shopId,
        'product_id' => $productId,
        'session_id' => $sessionId,
        'customer_id' => $customerId ?: null
    ]);

    echo json_encode(['success' => true, 'message' => 'View tracked']);
}

/**
 * Track a click on a recommendation
 */
function trackRecommendationClick(int $shopId, int $ruleId, int $productId, string $sessionId, int $customerId): void
{
    if ($ruleId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid rule_id']);
        return;
    }

    $clickId = Database::insert('recommendation_clicks', [
        'shop_id' => $shopId,
        'rule_id' => $ruleId,
        'product_id' => $productId,
        'session_id' => $sessionId,
        'customer_id' => $customerId ?: null
    ]);

    echo json_encode(['success' => true, 'message' => 'Click tracked', 'click_id' => $clickId]);
}

/**
 * Mark a recommendation click as converted (purchase made)
 */
function markConversion(int $clickId): void
{
    if ($clickId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid click_id']);
        return;
    }

    Database::update('recommendation_clicks', ['converted' => 1], 'id = ?', [$clickId]);

    echo json_encode(['success' => true, 'message' => 'Conversion marked']);
}
