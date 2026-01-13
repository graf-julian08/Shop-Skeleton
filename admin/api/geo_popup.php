<?php
/**
 * Geo-Location Popup API
 * Handles CRUD operations for popup elements and settings
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
Database::configure($database);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = intval($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    // Ensure tables exist
    ensureTablesExist();

    switch ($action) {
        case 'get':
            handleGetPopup($shopId);
            break;
        case 'save':
            handleSavePopup($shopId);
            break;
        case 'get_settings':
            handleGetSettings($shopId);
            break;
        case 'save_settings':
            handleSaveSettings($shopId);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action: ' . $action]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

/**
 * Ensure required tables exist
 */
function ensureTablesExist(): void
{
    // Popup elements table
    Database::query("
        CREATE TABLE IF NOT EXISTS geo_popup_elements (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL DEFAULT 1,
            element_id VARCHAR(50) NOT NULL,
            element_type VARCHAR(50) NOT NULL,
            pos_x INT DEFAULT 0,
            pos_y INT DEFAULT 0,
            z_index INT DEFAULT 0,
            content_json TEXT,
            style_json TEXT,
            breakpoint VARCHAR(20) DEFAULT 'desktop',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_shop (shop_id),
            INDEX idx_element (element_id)
        )
    ");

    // Popup settings table
    Database::query("
        CREATE TABLE IF NOT EXISTS geo_popup_settings (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL DEFAULT 1,
            enabled TINYINT(1) DEFAULT 1,
            api_service VARCHAR(50) DEFAULT 'ip-api',
            api_key VARCHAR(255) DEFAULT NULL,
            popup_behavior VARCHAR(50) DEFAULT 'mismatch',
            cookie_duration VARCHAR(20) DEFAULT '7d',
            fallback_action VARCHAR(50) DEFAULT 'default',
            background_color VARCHAR(20) DEFAULT '#1a1a1a',
            border_radius INT DEFAULT 12,
            shadow VARCHAR(20) DEFAULT 'md',
            canvas_width INT DEFAULT 400,
            canvas_height INT DEFAULT 320,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_shop (shop_id)
        )
    ");
}

/**
 * Get popup configuration
 */
function handleGetPopup(int $shopId): void
{
    $elements = Database::fetchAll(
        "SELECT * FROM geo_popup_elements WHERE shop_id = ? ORDER BY z_index",
        [$shopId]
    );

    $settings = Database::fetch(
        "SELECT * FROM geo_popup_settings WHERE shop_id = ?",
        [$shopId]
    );

    // Parse JSON fields
    foreach ($elements as &$el) {
        $el['content'] = json_decode($el['content_json'] ?? '{}', true);
        $el['style'] = json_decode($el['style_json'] ?? '{}', true);
    }

    echo json_encode([
        'success' => true,
        'elements' => $elements,
        'settings' => $settings ?: getDefaultSettings()
    ]);
}

/**
 * Save popup configuration
 */
function handleSavePopup(int $shopId): void
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        return;
    }

    $elements = $input['elements'] ?? [];
    $settings = $input['settings'] ?? [];

    // Delete existing elements
    Database::query(
        "DELETE FROM geo_popup_elements WHERE shop_id = ?",
        [$shopId]
    );

    // Insert new elements
    foreach ($elements as $index => $element) {
        Database::query(
            "INSERT INTO geo_popup_elements 
             (shop_id, element_id, element_type, pos_x, pos_y, z_index, content_json, style_json, breakpoint)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $shopId,
                $element['id'] ?? ('el-' . $index),
                $element['type'] ?? 'text',
                intval($element['x'] ?? 0),
                intval($element['y'] ?? 0),
                $index,
                json_encode($element['content'] ?? []),
                json_encode($element['style'] ?? []),
                $element['breakpoint'] ?? 'desktop'
            ]
        );
    }

    // Save settings if provided
    if (!empty($settings)) {
        Database::query(
            "INSERT INTO geo_popup_settings 
             (shop_id, background_color, border_radius, shadow)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
             background_color = VALUES(background_color),
             border_radius = VALUES(border_radius),
             shadow = VALUES(shadow),
             updated_at = NOW()",
            [
                $shopId,
                $settings['backgroundColor'] ?? '#1a1a1a',
                intval($settings['borderRadius'] ?? 12),
                $settings['shadow'] ?? 'md'
            ]
        );
    }

    echo json_encode([
        'success' => true,
        'message' => 'Popup gespeichert',
        'elements_count' => count($elements)
    ]);
}

/**
 * Get geo-location settings
 */
function handleGetSettings(int $shopId): void
{
    $settings = Database::fetch(
        "SELECT * FROM geo_popup_settings WHERE shop_id = ?",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'settings' => $settings ?: getDefaultSettings()
    ]);
}

/**
 * Save geo-location settings
 */
function handleSaveSettings(int $shopId): void
{
    $enabled = isset($_POST['enabled']) ? intval($_POST['enabled']) : 1;
    $apiService = $_POST['api_service'] ?? 'ip-api';
    $apiKey = $_POST['api_key'] ?? null;
    $popupBehavior = $_POST['popup_behavior'] ?? 'mismatch';
    $cookieDuration = $_POST['cookie_duration'] ?? '7d';
    $fallbackAction = $_POST['fallback_action'] ?? 'default';

    Database::query(
        "INSERT INTO geo_popup_settings 
         (shop_id, enabled, api_service, api_key, popup_behavior, cookie_duration, fallback_action)
         VALUES (?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
         enabled = VALUES(enabled),
         api_service = VALUES(api_service),
         api_key = VALUES(api_key),
         popup_behavior = VALUES(popup_behavior),
         cookie_duration = VALUES(cookie_duration),
         fallback_action = VALUES(fallback_action),
         updated_at = NOW()",
        [
            $shopId,
            $enabled,
            $apiService,
            $apiKey,
            $popupBehavior,
            $cookieDuration,
            $fallbackAction
        ]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Einstellungen gespeichert'
    ]);
}

/**
 * Get default settings
 */
function getDefaultSettings(): array
{
    return [
        'enabled' => 1,
        'api_service' => 'ip-api',
        'api_key' => null,
        'popup_behavior' => 'mismatch',
        'cookie_duration' => '7d',
        'fallback_action' => 'default',
        'background_color' => '#1a1a1a',
        'border_radius' => 12,
        'shadow' => 'md',
        'canvas_width' => 400,
        'canvas_height' => 320
    ];
}
