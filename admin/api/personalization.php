<?php
/**
 * Personalization API
 * Handles recommendation rules, tracking, and settings
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';

Database::configure($database);
Auth::init();

// Get action and shop_id
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_rules':
            handleGetRules($shopId);
            break;
        case 'save_rule':
            handleSaveRule($shopId);
            break;
        case 'delete_rule':
            handleDeleteRule($shopId);
            break;
        case 'toggle_rule':
            handleToggleRule($shopId);
            break;
        case 'get_stats':
            handleGetStats($shopId);
            break;
        case 'get_settings':
            handleGetSettings($shopId);
            break;
        case 'save_settings':
            handleSaveSettings($shopId);
            break;
        case 'clear_tracking':
            handleClearTracking($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// RULES HANDLERS
// =====================================================================

function handleGetRules(int $shopId): void
{
    $rules = Database::fetchAll(
        "SELECT r.*, 
                (SELECT COUNT(*) FROM recommendation_clicks rc WHERE rc.rule_id = r.id AND rc.clicked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as clicks_7d,
                (SELECT COUNT(*) FROM recommendation_clicks rc WHERE rc.rule_id = r.id AND rc.clicked_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as clicks_30d,
                (SELECT COUNT(*) FROM recommendation_clicks rc WHERE rc.rule_id = r.id AND rc.converted = 1 AND rc.clicked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as conversions_7d
         FROM recommendation_rules r 
         WHERE r.shop_id = ? 
         ORDER BY r.priority ASC, r.created_at DESC",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'rules' => $rules
    ]);
}

function handleSaveRule(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $ruleType = $_POST['rule_type'] ?? 'similar';
    $position = $_POST['position'] ?? 'product_page';
    $productCount = (int) ($_POST['product_count'] ?? 4);
    $isActive = (int) ($_POST['is_active'] ?? 1);
    $priority = (int) ($_POST['priority'] ?? 0);

    if (empty($name)) {
        echo json_encode(['success' => false, 'error' => 'Name ist erforderlich']);
        return;
    }

    $data = [
        'shop_id' => $shopId,
        'name' => $name,
        'rule_type' => $ruleType,
        'position' => $position,
        'product_count' => min(max($productCount, 1), 20),
        'is_active' => $isActive,
        'priority' => $priority
    ];

    if ($id > 0) {
        // Update
        Database::update('recommendation_rules', $data, 'id = ? AND shop_id = ?', [$id, $shopId]);
        echo json_encode(['success' => true, 'message' => 'Regel aktualisiert', 'id' => $id]);
    } else {
        // Insert
        $newId = Database::insert('recommendation_rules', $data);
        echo json_encode(['success' => true, 'message' => 'Regel erstellt', 'id' => $newId]);
    }
}

function handleDeleteRule(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Regel-ID']);
        return;
    }

    // Delete associated clicks first
    Database::delete('recommendation_clicks', 'rule_id = ?', [$id]);

    // Delete the rule
    $deleted = Database::delete('recommendation_rules', 'id = ? AND shop_id = ?', [$id, $shopId]);

    echo json_encode([
        'success' => $deleted > 0,
        'message' => $deleted > 0 ? 'Regel gelöscht' : 'Regel nicht gefunden'
    ]);
}

function handleToggleRule(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $isActive = (int) ($_POST['is_active'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Regel-ID']);
        return;
    }

    Database::update('recommendation_rules', ['is_active' => $isActive], 'id = ? AND shop_id = ?', [$id, $shopId]);

    echo json_encode([
        'success' => true,
        'message' => $isActive ? 'Regel aktiviert' : 'Regel deaktiviert'
    ]);
}

// =====================================================================
// STATS HANDLERS
// =====================================================================

function handleGetStats(int $shopId): void
{
    // Total clicks last 7 days
    $clicks7d = Database::fetch(
        "SELECT COUNT(*) as total FROM recommendation_clicks WHERE shop_id = ? AND clicked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
        [$shopId]
    );

    // Total clicks last 30 days
    $clicks30d = Database::fetch(
        "SELECT COUNT(*) as total FROM recommendation_clicks WHERE shop_id = ? AND clicked_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
        [$shopId]
    );

    // Previous 7 days for comparison
    $clicksPrev7d = Database::fetch(
        "SELECT COUNT(*) as total FROM recommendation_clicks WHERE shop_id = ? AND clicked_at >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND clicked_at < DATE_SUB(NOW(), INTERVAL 7 DAY)",
        [$shopId]
    );

    // Conversions
    $conversions7d = Database::fetch(
        "SELECT COUNT(*) as total FROM recommendation_clicks WHERE shop_id = ? AND converted = 1 AND clicked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
        [$shopId]
    );

    // Active rules count
    $activeRules = Database::fetch(
        "SELECT COUNT(*) as total FROM recommendation_rules WHERE shop_id = ? AND is_active = 1",
        [$shopId]
    );

    // Total product views
    $productViews = Database::fetch(
        "SELECT COUNT(*) as total FROM product_views WHERE shop_id = ? AND viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
        [$shopId]
    );

    // Calculate trends
    $clicksTotal = (int) ($clicks7d['total'] ?? 0);
    $clicksPrevTotal = (int) ($clicksPrev7d['total'] ?? 0);
    $clicksTrend = $clicksPrevTotal > 0
        ? round((($clicksTotal - $clicksPrevTotal) / $clicksPrevTotal) * 100, 1)
        : 0;

    // Conversion rate
    $convRate = $clicksTotal > 0
        ? round(((int) ($conversions7d['total'] ?? 0) / $clicksTotal) * 100, 1)
        : 0;

    echo json_encode([
        'success' => true,
        'stats' => [
            'clicks_7d' => $clicksTotal,
            'clicks_30d' => (int) ($clicks30d['total'] ?? 0),
            'clicks_trend' => $clicksTrend,
            'conversions_7d' => (int) ($conversions7d['total'] ?? 0),
            'conversion_rate' => $convRate,
            'active_rules' => (int) ($activeRules['total'] ?? 0),
            'product_views_7d' => (int) ($productViews['total'] ?? 0)
        ]
    ]);
}

// =====================================================================
// SETTINGS HANDLERS
// =====================================================================

function handleGetSettings(int $shopId): void
{
    $settings = Database::fetch(
        "SELECT setting_value FROM settings WHERE shop_id = ? AND scope = 'shop' AND setting_key = 'personalization'",
        [$shopId]
    );

    $defaults = [
        'tracking_enabled' => true,
        'cookie_lifetime_days' => 30,
        'default_product_count' => 4,
        'show_on_mobile' => true,
        'lazy_load' => true
    ];

    $data = $defaults;
    if ($settings && !empty($settings['setting_value'])) {
        $data = array_merge($defaults, json_decode($settings['setting_value'], true) ?? []);
    }

    echo json_encode([
        'success' => true,
        'settings' => $data
    ]);
}

function handleSaveSettings(int $shopId): void
{
    $data = [
        'tracking_enabled' => isset($_POST['tracking_enabled']) && $_POST['tracking_enabled'] === '1',
        'cookie_lifetime_days' => (int) ($_POST['cookie_lifetime_days'] ?? 30),
        'default_product_count' => (int) ($_POST['default_product_count'] ?? 4),
        'show_on_mobile' => isset($_POST['show_on_mobile']) && $_POST['show_on_mobile'] === '1',
        'lazy_load' => isset($_POST['lazy_load']) && $_POST['lazy_load'] === '1'
    ];

    // Check if exists
    $existing = Database::fetch(
        "SELECT id FROM settings WHERE shop_id = ? AND scope = 'shop' AND setting_key = 'personalization'",
        [$shopId]
    );

    if ($existing) {
        Database::update('settings', [
            'setting_value' => json_encode($data)
        ], 'id = ?', [$existing['id']]);
    } else {
        Database::insert('settings', [
            'shop_id' => $shopId,
            'scope' => 'shop',
            'setting_key' => 'personalization',
            'setting_value' => json_encode($data)
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Einstellungen gespeichert']);
}

function handleClearTracking(int $shopId): void
{
    // Clear product views
    $viewsDeleted = Database::delete('product_views', 'shop_id = ?', [$shopId]);

    // Clear recommendation clicks
    $clicksDeleted = Database::delete('recommendation_clicks', 'shop_id = ?', [$shopId]);

    echo json_encode([
        'success' => true,
        'message' => "Tracking-Daten gelöscht: {$viewsDeleted} Views, {$clicksDeleted} Klicks"
    ]);
}
