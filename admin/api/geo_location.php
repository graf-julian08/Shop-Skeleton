<?php
/**
 * Geo-Location API
 * Uses ip-api.com (free, no key required, 45 req/min per visitor IP)
 * 
 * Endpoints:
 * - detect: Returns visitor's country/language based on IP
 * - match_locale: Returns best matching locale for country code
 */

header('Content-Type: application/json');

global $database;
require_once __DIR__ . '/../includes/Database.php';
if (is_array($database)) {
    Database::configure($database);
}

$action = $_GET['action'] ?? '';
$shopId = intval($_GET['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'detect':
            handleDetectLocation();
            break;
        case 'match_locale':
            handleMatchLocale($shopId);
            break;
        case 'get_settings':
            handleGetSettings($shopId);
            break;
        case 'save_settings':
            handleSaveSettings($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

/**
 * Detect visitor location using ip-api.com
 * Free tier: 45 requests/minute per client IP (not per API key)
 */
function handleDetectLocation(): void
{
    // Get visitor IP
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

    // For local development, use a test IP
    if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
        // Return mock data for localhost
        echo json_encode([
            'success' => true,
            'ip' => $ip,
            'is_local' => true,
            'data' => [
                'country' => 'Germany',
                'countryCode' => 'DE',
                'region' => 'Bayern',
                'city' => 'Munich',
                'timezone' => 'Europe/Berlin',
                'currency' => 'EUR',
                'languages' => 'de'
            ]
        ]);
        return;
    }

    // Call ip-api.com (free, no key required)
    $url = "http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,city,timezone,currency,lat,lon";

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if (!$response) {
        echo json_encode([
            'success' => false,
            'error' => 'Could not reach geo-location service'
        ]);
        return;
    }

    $data = json_decode($response, true);

    if ($data['status'] !== 'success') {
        echo json_encode([
            'success' => false,
            'error' => $data['message'] ?? 'Location detection failed'
        ]);
        return;
    }

    // Map country code to language code
    $countryToLang = [
        'DE' => 'de',
        'AT' => 'de',
        'CH' => 'de',
        'LI' => 'de',
        'LU' => 'de',
        'US' => 'en',
        'GB' => 'en',
        'AU' => 'en',
        'CA' => 'en',
        'NZ' => 'en',
        'IE' => 'en',
        'FR' => 'fr',
        'BE' => 'fr',
        'MC' => 'fr',
        'ES' => 'es',
        'MX' => 'es',
        'AR' => 'es',
        'CO' => 'es',
        'CL' => 'es',
        'PE' => 'es',
        'IT' => 'it',
        'SM' => 'it',
        'PT' => 'pt',
        'BR' => 'pt',
        'NL' => 'nl',
        'PL' => 'pl',
        'RU' => 'ru',
        'CN' => 'zh-CN',
        'TW' => 'zh-TW',
        'HK' => 'zh-TW',
        'JP' => 'ja',
        'KR' => 'ko',
        'TR' => 'tr',
        'SA' => 'ar',
        'AE' => 'ar',
        'EG' => 'ar',
        'IL' => 'he',
        'IN' => 'hi',
        'TH' => 'th',
        'VN' => 'vi',
        'ID' => 'id',
        'MY' => 'ms',
        'PH' => 'tl',
        'SE' => 'sv',
        'NO' => 'no',
        'DK' => 'da',
        'FI' => 'fi',
        'CZ' => 'cs',
        'HU' => 'hu',
        'RO' => 'ro',
        'BG' => 'bg',
        'GR' => 'el',
        'UA' => 'uk'
    ];

    $data['languages'] = $countryToLang[$data['countryCode']] ?? 'en';

    echo json_encode([
        'success' => true,
        'ip' => $ip,
        'data' => $data
    ]);
}

/**
 * Find the best matching locale for a country/language
 */
function handleMatchLocale(int $shopId): void
{
    $countryCode = strtoupper($_GET['country'] ?? '');
    $langCode = strtolower($_GET['lang'] ?? '');

    if (!$countryCode) {
        echo json_encode(['success' => false, 'error' => 'Country code required']);
        return;
    }

    // First try exact match (language + country)
    $localeCode = $langCode . '_' . $countryCode;
    $locale = Database::fetch(
        "SELECT * FROM shop_locales WHERE shop_id = ? AND code = ? AND is_active = 1",
        [$shopId, $localeCode]
    );

    // If not found, try just by country
    if (!$locale) {
        $locale = Database::fetch(
            "SELECT * FROM shop_locales WHERE shop_id = ? AND country_code = ? AND is_active = 1 LIMIT 1",
            [$shopId, $countryCode]
        );
    }

    // If still not found, try by language
    if (!$locale && $langCode) {
        $locale = Database::fetch(
            "SELECT * FROM shop_locales WHERE shop_id = ? AND language_code = ? AND is_active = 1 LIMIT 1",
            [$shopId, $langCode]
        );
    }

    // If still not found, return default
    if (!$locale) {
        $locale = Database::fetch(
            "SELECT * FROM shop_locales WHERE shop_id = ? AND is_default = 1",
            [$shopId]
        );
    }

    if ($locale) {
        // Get matching currency
        $currency = Database::fetch(
            "SELECT * FROM currencies WHERE shop_id = ? AND code = ? AND is_active = 1",
            [$shopId, $locale['currency_code']]
        );

        if (!$currency) {
            $currency = Database::fetch(
                "SELECT * FROM currencies WHERE shop_id = ? AND is_default = 1",
                [$shopId]
            );
        }

        echo json_encode([
            'success' => true,
            'locale' => $locale,
            'currency' => $currency
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No matching locale found'
        ]);
    }
}

/**
 * Get geo-location settings
 */
function handleGetSettings(int $shopId): void
{
    $settings = Database::fetch(
        "SELECT * FROM shop_settings WHERE shop_id = ? AND setting_key = 'geo_location'",
        [$shopId]
    );

    $data = $settings ? json_decode($settings['setting_value'], true) : [
        'enabled' => true,
        'service' => 'ip-api',
        'api_key' => '',
        'show_popup' => true,
        'remember_choice' => true
    ];

    echo json_encode(['success' => true, 'settings' => $data]);
}

/**
 * Save geo-location settings
 */
function handleSaveSettings(int $shopId): void
{
    $settings = [
        'enabled' => isset($_POST['enabled']) ? (bool) $_POST['enabled'] : true,
        'service' => $_POST['service'] ?? 'ip-api',
        'api_key' => $_POST['api_key'] ?? '',
        'show_popup' => isset($_POST['show_popup']) ? (bool) $_POST['show_popup'] : true,
        'remember_choice' => isset($_POST['remember_choice']) ? (bool) $_POST['remember_choice'] : true
    ];

    $existing = Database::fetch(
        "SELECT id FROM shop_settings WHERE shop_id = ? AND setting_key = 'geo_location'",
        [$shopId]
    );

    if ($existing) {
        Database::query(
            "UPDATE shop_settings SET setting_value = ? WHERE id = ?",
            [json_encode($settings), $existing['id']]
        );
    } else {
        Database::insert('shop_settings', [
            'shop_id' => $shopId,
            'setting_key' => 'geo_location',
            'setting_value' => json_encode($settings)
        ]);
    }

    echo json_encode(['success' => true]);
}
