<?php
/**
 * System Settings Loader
 * Loads and applies system settings from database
 * 
 * Settings implemented:
 * - Debug Mode: Enable/disable PHP error reporting
 * - Caching: Browser caching headers
 * - Maintenance Mode: Block visitors except allowed IPs
 * - Timezone: Set PHP timezone
 */

// Global settings cache
$GLOBALS['_admin_settings'] = null;

/**
 * Load admin settings from database
 */
function loadAdminSettings(int $shopId = 1): array
{
    if ($GLOBALS['_admin_settings'] !== null) {
        return $GLOBALS['_admin_settings'];
    }

    require_once __DIR__ . '/Database.php';

    $settings = [];
    $rows = Database::fetchAll(
        "SELECT setting_key, setting_value FROM settings 
         WHERE shop_id = ? AND scope = 'global' AND setting_key LIKE 'admin_%'",
        [$shopId]
    );

    foreach ($rows as $row) {
        $key = str_replace('admin_', '', $row['setting_key']);
        $value = json_decode($row['setting_value'], true);
        $settings[$key] = $value !== null ? $value : $row['setting_value'];
    }

    // Set defaults
    $defaults = [
        'locale' => 'de',
        'timezone' => 'Europe/Berlin',
        'dark_mode' => true,
        'sidebar_collapsed' => false,
        'sidebar_remember' => false,
        'caching_enabled' => true,
        'minification_enabled' => true,
        'debug_mode' => false,
        'maintenance_mode' => false,
        'maintenance_message' => '',
        'maintenance_allowed_ips' => ''
    ];

    $GLOBALS['_admin_settings'] = array_merge($defaults, $settings);
    return $GLOBALS['_admin_settings'];
}

/**
 * Get a single admin setting
 */
function getAdminSetting(string $key, $default = null)
{
    $settings = loadAdminSettings();
    return $settings[$key] ?? $default;
}

/**
 * Apply debug mode setting
 */
function applyDebugMode(): void
{
    $debugMode = getAdminSetting('debug_mode', false);

    if ($debugMode) {
        // Enable all error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
    } else {
        // Disable error display (log only)
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        ini_set('log_errors', '1');
    }
}

/**
 * Apply caching headers
 */
function applyCachingHeaders(): void
{
    $cachingEnabled = getAdminSetting('caching_enabled', true);

    if ($cachingEnabled) {
        // Enable browser caching for static resources
        header('Cache-Control: public, max-age=3600'); // 1 hour cache
    } else {
        // Disable caching
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
    }
}

/**
 * Apply timezone setting
 */
function applyTimezone(): void
{
    $timezone = getAdminSetting('timezone', 'Europe/Berlin');
    if (in_array($timezone, timezone_identifiers_list())) {
        date_default_timezone_set($timezone);
    }
}

/**
 * Check maintenance mode
 * Returns true if maintenance mode is active and user is NOT allowed
 */
function checkMaintenanceMode(): bool
{
    $maintenanceMode = getAdminSetting('maintenance_mode', false);

    if (!$maintenanceMode) {
        return false; // Not in maintenance
    }

    // Check if current IP is allowed
    $allowedIps = getAdminSetting('maintenance_allowed_ips', '');
    $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';

    if (!empty($allowedIps)) {
        $ipList = array_map('trim', explode("\n", $allowedIps));
        $ipList = array_filter($ipList); // Remove empty lines

        if (in_array($currentIp, $ipList)) {
            return false; // IP is allowed
        }

        // Check for localhost variants
        if (
            in_array($currentIp, ['127.0.0.1', '::1']) &&
            (in_array('127.0.0.1', $ipList) || in_array('::1', $ipList) || in_array('localhost', $ipList))
        ) {
            return false; // Localhost is allowed
        }
    }

    // Also allow logged-in admins
    if (isset($_SESSION['admin_user']) && !empty($_SESSION['admin_user'])) {
        return false; // Admin user can access
    }

    return true; // Maintenance mode active, block access
}

/**
 * Show maintenance page
 */
function showMaintenancePage(): void
{
    $message = getAdminSetting('maintenance_message', '');
    if (empty($message)) {
        $message = 'We are currently performing scheduled maintenance. Please try again later.';
    }

    http_response_code(503);
    header('Retry-After: 3600');

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 600;
        }
        p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: rgba(255,255,255,0.8);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔧</div>
        <h1>Maintenance Mode</h1>
        <p>' . htmlspecialchars($message) . '</p>
    </div>
</body>
</html>';
    exit;
}

/**
 * Apply all system settings
 */
function applySystemSettings(): void
{
    loadAdminSettings();
    applyDebugMode();
    applyTimezone();
    // Note: Caching headers are applied selectively, not globally

    // Check maintenance mode (only for non-admin pages)
    // Admin panel itself should always be accessible
}

/**
 * Check if minification is enabled
 */
function isMinificationEnabled(): bool
{
    return getAdminSetting('minification_enabled', true);
}

/**
 * Check if caching is enabled  
 */
function isCachingEnabled(): bool
{
    return getAdminSetting('caching_enabled', true);
}

/**
 * Check if debug mode is enabled
 */
function isDebugModeEnabled(): bool
{
    return getAdminSetting('debug_mode', false);
}
