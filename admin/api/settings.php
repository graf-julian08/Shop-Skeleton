<?php
/**
 * System Settings API
 * Handles admin panel settings with database persistence
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
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
    switch ($action) {
        case 'get_settings':
            handleGetSettings($shopId);
            break;
        case 'save_settings':
            handleSaveSettings($shopId);
            break;
        case 'get_languages':
            handleGetLanguages($shopId);
            break;
        case 'get_timezones':
            handleGetTimezones();
            break;
        case 'toggle_dark_mode':
            handleToggleDarkMode($shopId);
            break;
        case 'toggle_sidebar':
            handleToggleSidebar($shopId);
            break;
        case 'get_translations':
            handleGetTranslations($_GET['lang'] ?? 'de');
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action: ' . $action]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

// =====================================================================
// SETTINGS HANDLERS
// =====================================================================

function handleGetSettings(int $shopId): void
{
    // Get all admin settings (using 'global' scope)
    $settings = Database::fetchAll(
        "SELECT setting_key, setting_value FROM settings WHERE shop_id = ? AND scope = 'global' AND setting_key LIKE 'admin_%'",
        [$shopId]
    );

    $result = [];
    foreach ($settings as $setting) {
        // Remove 'admin_' prefix for cleaner key names
        $key = str_replace('admin_', '', $setting['setting_key']);
        $value = json_decode($setting['setting_value'], true);
        $result[$key] = $value ?? $setting['setting_value'];
    }

    // Set defaults if not present
    $defaults = [
        'locale' => 'de_DE',
        'timezone' => 'Europe/Berlin',
        'dark_mode' => true,
        'sidebar_collapsed' => false,
        'sidebar_remember' => false,
        'caching_enabled' => true,
        'asset_minification' => true,
        'debug_mode' => false,
        'maintenance_mode' => false,
        'maintenance_message' => 'Wir führen gerade Wartungsarbeiten durch. Bitte versuchen Sie es später erneut.',
        'maintenance_allowed_ips' => ''
    ];

    foreach ($defaults as $key => $default) {
        if (!isset($result[$key])) {
            $result[$key] = $default;
        }
    }

    echo json_encode([
        'success' => true,
        'settings' => $result
    ]);
}

function handleSaveSettings(int $shopId): void
{
    $settings = [
        'admin_locale' => trim($_POST['locale'] ?? 'de_DE'),
        'admin_timezone' => trim($_POST['timezone'] ?? 'Europe/Berlin'),
        'admin_dark_mode' => filter_var($_POST['dark_mode'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'admin_sidebar_collapsed' => filter_var($_POST['sidebar_collapsed'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'admin_sidebar_remember' => filter_var($_POST['sidebar_remember'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'admin_caching_enabled' => filter_var($_POST['caching_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'admin_asset_minification' => filter_var($_POST['asset_minification'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'admin_debug_mode' => filter_var($_POST['debug_mode'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'admin_maintenance_mode' => filter_var($_POST['maintenance_mode'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'admin_maintenance_message' => trim($_POST['maintenance_message'] ?? ''),
        'admin_maintenance_allowed_ips' => trim($_POST['maintenance_allowed_ips'] ?? '')
    ];

    foreach ($settings as $key => $value) {
        $jsonValue = json_encode($value);

        // Upsert using INSERT ... ON DUPLICATE KEY UPDATE (using 'global' scope)
        Database::query(
            "INSERT INTO settings (shop_id, scope, setting_key, setting_value) 
             VALUES (?, 'global', ?, ?) 
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()",
            [$shopId, $key, $jsonValue]
        );
    }

    echo json_encode([
        'success' => true,
        'message' => 'Einstellungen gespeichert'
    ]);
}

function handleGetLanguages(int $shopId): void
{
    // Complete list of languages supported by Google Translate
    // Sorted alphabetically by English name
    $allLanguages = [
        ['code' => 'af', 'language_name' => 'Afrikaans', 'language_native' => 'Afrikaans'],
        ['code' => 'sq', 'language_name' => 'Albanian', 'language_native' => 'Shqip'],
        ['code' => 'am', 'language_name' => 'Amharic', 'language_native' => 'አማርኛ'],
        ['code' => 'ar', 'language_name' => 'Arabic', 'language_native' => 'العربية'],
        ['code' => 'hy', 'language_name' => 'Armenian', 'language_native' => 'Հայերdelays'],
        ['code' => 'az', 'language_name' => 'Azerbaijani', 'language_native' => 'Azərbaycan'],
        ['code' => 'eu', 'language_name' => 'Basque', 'language_native' => 'Euskara'],
        ['code' => 'be', 'language_name' => 'Belarusian', 'language_native' => 'Беларуская'],
        ['code' => 'bn', 'language_name' => 'Bengali', 'language_native' => 'বাংলা'],
        ['code' => 'bs', 'language_name' => 'Bosnian', 'language_native' => 'Bosanski'],
        ['code' => 'bg', 'language_name' => 'Bulgarian', 'language_native' => 'Български'],
        ['code' => 'ca', 'language_name' => 'Catalan', 'language_native' => 'Català'],
        ['code' => 'ceb', 'language_name' => 'Cebuano', 'language_native' => 'Cebuano'],
        ['code' => 'zh', 'language_name' => 'Chinese (Simplified)', 'language_native' => '中文 (简体)'],
        ['code' => 'zh-TW', 'language_name' => 'Chinese (Traditional)', 'language_native' => '中文 (繁體)'],
        ['code' => 'hr', 'language_name' => 'Croatian', 'language_native' => 'Hrvatski'],
        ['code' => 'cs', 'language_name' => 'Czech', 'language_native' => 'Čeština'],
        ['code' => 'da', 'language_name' => 'Danish', 'language_native' => 'Dansk'],
        ['code' => 'nl', 'language_name' => 'Dutch', 'language_native' => 'Nederlands'],
        ['code' => 'en', 'language_name' => 'English', 'language_native' => 'English'],
        ['code' => 'eo', 'language_name' => 'Esperanto', 'language_native' => 'Esperanto'],
        ['code' => 'et', 'language_name' => 'Estonian', 'language_native' => 'Eesti'],
        ['code' => 'fi', 'language_name' => 'Finnish', 'language_native' => 'Suomi'],
        ['code' => 'fr', 'language_name' => 'French', 'language_native' => 'Français'],
        ['code' => 'gl', 'language_name' => 'Galician', 'language_native' => 'Galego'],
        ['code' => 'ka', 'language_name' => 'Georgian', 'language_native' => 'ქართული'],
        ['code' => 'de', 'language_name' => 'German', 'language_native' => 'Deutsch'],
        ['code' => 'el', 'language_name' => 'Greek', 'language_native' => 'Ελληνικά'],
        ['code' => 'gu', 'language_name' => 'Gujarati', 'language_native' => 'ગુજરાતી'],
        ['code' => 'ht', 'language_name' => 'Haitian Creole', 'language_native' => 'Kreyòl Ayisyen'],
        ['code' => 'ha', 'language_name' => 'Hausa', 'language_native' => 'Hausa'],
        ['code' => 'haw', 'language_name' => 'Hawaiian', 'language_native' => 'ʻŌlelo Hawaiʻi'],
        ['code' => 'he', 'language_name' => 'Hebrew', 'language_native' => 'עברית'],
        ['code' => 'hi', 'language_name' => 'Hindi', 'language_native' => 'हिन्दी'],
        ['code' => 'hmn', 'language_name' => 'Hmong', 'language_native' => 'Hmong'],
        ['code' => 'hu', 'language_name' => 'Hungarian', 'language_native' => 'Magyar'],
        ['code' => 'is', 'language_name' => 'Icelandic', 'language_native' => 'Íslenska'],
        ['code' => 'ig', 'language_name' => 'Igbo', 'language_native' => 'Igbo'],
        ['code' => 'id', 'language_name' => 'Indonesian', 'language_native' => 'Bahasa Indonesia'],
        ['code' => 'ga', 'language_name' => 'Irish', 'language_native' => 'Gaeilge'],
        ['code' => 'it', 'language_name' => 'Italian', 'language_native' => 'Italiano'],
        ['code' => 'ja', 'language_name' => 'Japanese', 'language_native' => '日本語'],
        ['code' => 'jv', 'language_name' => 'Javanese', 'language_native' => 'Basa Jawa'],
        ['code' => 'kn', 'language_name' => 'Kannada', 'language_native' => 'ಕನ್ನಡ'],
        ['code' => 'kk', 'language_name' => 'Kazakh', 'language_native' => 'Қазақ'],
        ['code' => 'km', 'language_name' => 'Khmer', 'language_native' => 'ខ្មែរ'],
        ['code' => 'rw', 'language_name' => 'Kinyarwanda', 'language_native' => 'Kinyarwanda'],
        ['code' => 'ko', 'language_name' => 'Korean', 'language_native' => '한국어'],
        ['code' => 'ku', 'language_name' => 'Kurdish', 'language_native' => 'Kurdî'],
        ['code' => 'ky', 'language_name' => 'Kyrgyz', 'language_native' => 'Кыргызча'],
        ['code' => 'lo', 'language_name' => 'Lao', 'language_native' => 'ລາວ'],
        ['code' => 'la', 'language_name' => 'Latin', 'language_native' => 'Latina'],
        ['code' => 'lv', 'language_name' => 'Latvian', 'language_native' => 'Latviešu'],
        ['code' => 'lt', 'language_name' => 'Lithuanian', 'language_native' => 'Lietuvių'],
        ['code' => 'lb', 'language_name' => 'Luxembourgish', 'language_native' => 'Lëtzebuergesch'],
        ['code' => 'mk', 'language_name' => 'Macedonian', 'language_native' => 'Македонски'],
        ['code' => 'mg', 'language_name' => 'Malagasy', 'language_native' => 'Malagasy'],
        ['code' => 'ms', 'language_name' => 'Malay', 'language_native' => 'Bahasa Melayu'],
        ['code' => 'ml', 'language_name' => 'Malayalam', 'language_native' => 'മലയാളം'],
        ['code' => 'mt', 'language_name' => 'Maltese', 'language_native' => 'Malti'],
        ['code' => 'mi', 'language_name' => 'Maori', 'language_native' => 'Te Reo Māori'],
        ['code' => 'mr', 'language_name' => 'Marathi', 'language_native' => 'मराठी'],
        ['code' => 'mn', 'language_name' => 'Mongolian', 'language_native' => 'Монгол'],
        ['code' => 'my', 'language_name' => 'Myanmar (Burmese)', 'language_native' => 'မြန်မာ'],
        ['code' => 'ne', 'language_name' => 'Nepali', 'language_native' => 'नेपाली'],
        ['code' => 'no', 'language_name' => 'Norwegian', 'language_native' => 'Norsk'],
        ['code' => 'ny', 'language_name' => 'Nyanja (Chichewa)', 'language_native' => 'Chichewa'],
        ['code' => 'or', 'language_name' => 'Odia (Oriya)', 'language_native' => 'ଓଡ଼ିଆ'],
        ['code' => 'ps', 'language_name' => 'Pashto', 'language_native' => 'پښتو'],
        ['code' => 'fa', 'language_name' => 'Persian', 'language_native' => 'فارسی'],
        ['code' => 'pl', 'language_name' => 'Polish', 'language_native' => 'Polski'],
        ['code' => 'pt', 'language_name' => 'Portuguese', 'language_native' => 'Português'],
        ['code' => 'pa', 'language_name' => 'Punjabi', 'language_native' => 'ਪੰਜਾਬੀ'],
        ['code' => 'ro', 'language_name' => 'Romanian', 'language_native' => 'Română'],
        ['code' => 'ru', 'language_name' => 'Russian', 'language_native' => 'Русский'],
        ['code' => 'sm', 'language_name' => 'Samoan', 'language_native' => 'Gagana Samoa'],
        ['code' => 'gd', 'language_name' => 'Scots Gaelic', 'language_native' => 'Gàidhlig'],
        ['code' => 'sr', 'language_name' => 'Serbian', 'language_native' => 'Српски'],
        ['code' => 'st', 'language_name' => 'Sesotho', 'language_native' => 'Sesotho'],
        ['code' => 'sn', 'language_name' => 'Shona', 'language_native' => 'ChiShona'],
        ['code' => 'sd', 'language_name' => 'Sindhi', 'language_native' => 'سنڌي'],
        ['code' => 'si', 'language_name' => 'Sinhala', 'language_native' => 'සිංහල'],
        ['code' => 'sk', 'language_name' => 'Slovak', 'language_native' => 'Slovenčina'],
        ['code' => 'sl', 'language_name' => 'Slovenian', 'language_native' => 'Slovenščina'],
        ['code' => 'so', 'language_name' => 'Somali', 'language_native' => 'Soomaali'],
        ['code' => 'es', 'language_name' => 'Spanish', 'language_native' => 'Español'],
        ['code' => 'su', 'language_name' => 'Sundanese', 'language_native' => 'Basa Sunda'],
        ['code' => 'sw', 'language_name' => 'Swahili', 'language_native' => 'Kiswahili'],
        ['code' => 'sv', 'language_name' => 'Swedish', 'language_native' => 'Svenska'],
        ['code' => 'tl', 'language_name' => 'Tagalog (Filipino)', 'language_native' => 'Tagalog'],
        ['code' => 'tg', 'language_name' => 'Tajik', 'language_native' => 'Тоҷикӣ'],
        ['code' => 'ta', 'language_name' => 'Tamil', 'language_native' => 'தமிழ்'],
        ['code' => 'tt', 'language_name' => 'Tatar', 'language_native' => 'Татарча'],
        ['code' => 'te', 'language_name' => 'Telugu', 'language_native' => 'తెలుగు'],
        ['code' => 'th', 'language_name' => 'Thai', 'language_native' => 'ไทย'],
        ['code' => 'tr', 'language_name' => 'Turkish', 'language_native' => 'Türkçe'],
        ['code' => 'tk', 'language_name' => 'Turkmen', 'language_native' => 'Türkmen'],
        ['code' => 'uk', 'language_name' => 'Ukrainian', 'language_native' => 'Українська'],
        ['code' => 'ur', 'language_name' => 'Urdu', 'language_native' => 'اردو'],
        ['code' => 'ug', 'language_name' => 'Uyghur', 'language_native' => 'ئۇيغۇرچە'],
        ['code' => 'uz', 'language_name' => 'Uzbek', 'language_native' => 'Oʻzbekcha'],
        ['code' => 'vi', 'language_name' => 'Vietnamese', 'language_native' => 'Tiếng Việt'],
        ['code' => 'cy', 'language_name' => 'Welsh', 'language_native' => 'Cymraeg'],
        ['code' => 'xh', 'language_name' => 'Xhosa', 'language_native' => 'isiXhosa'],
        ['code' => 'yi', 'language_name' => 'Yiddish', 'language_native' => 'ייִדיש'],
        ['code' => 'yo', 'language_name' => 'Yoruba', 'language_native' => 'Yorùbá'],
        ['code' => 'zu', 'language_name' => 'Zulu', 'language_native' => 'isiZulu'],
    ];

    // Also get active languages from shop_locales
    // Use a simple query and deduplicate in PHP to avoid SQL mode issues
    $shopLanguagesRaw = Database::fetchAll(
        "SELECT code, language_name, language_native 
         FROM shop_locales 
         WHERE shop_id = ? AND is_active = 1",
        [$shopId]
    );

    // Deduplicate by language code prefix (e.g., de_DE -> de)
    $shopLanguages = [];
    $seenCodes = [];
    foreach ($shopLanguagesRaw as $lang) {
        $shortCode = explode('_', $lang['code'])[0];
        if (!isset($seenCodes[$shortCode])) {
            $seenCodes[$shortCode] = true;
            $shopLanguages[] = [
                'code' => $shortCode,
                'language_name' => $lang['language_name'],
                'language_native' => $lang['language_native']
            ];
        }
    }

    // Merge shop languages with the full list (shop languages first if they exist)
    $existingCodes = array_column($allLanguages, 'code');
    foreach ($shopLanguages as $lang) {
        if (!in_array($lang['code'], $existingCodes)) {
            array_unshift($allLanguages, $lang);
        }
    }

    echo json_encode([
        'success' => true,
        'languages' => $allLanguages
    ]);
}

function handleGetTimezones(): void
{
    $timezones = DateTimeZone::listIdentifiers();

    // Group by region and sort
    $grouped = [];
    foreach ($timezones as $tz) {
        $parts = explode('/', $tz, 2);
        $region = $parts[0];
        $city = $parts[1] ?? $tz;

        if (!isset($grouped[$region])) {
            $grouped[$region] = [];
        }
        $grouped[$region][] = [
            'value' => $tz,
            'label' => str_replace('_', ' ', $city),
            'full_label' => str_replace('_', ' ', $tz)
        ];
    }

    // Sort regions alphabetically
    ksort($grouped);

    // Sort cities within each region alphabetically
    foreach ($grouped as $region => &$cities) {
        usort($cities, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });
    }

    // Build flat list with optgroup info
    $result = [];
    foreach ($grouped as $region => $cities) {
        foreach ($cities as $city) {
            $result[] = [
                'value' => $city['value'],
                'label' => $city['full_label'],
                'region' => $region
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'timezones' => $result,
        'grouped' => $grouped
    ]);
}

function handleToggleDarkMode(int $shopId): void
{
    $darkMode = filter_var($_POST['dark_mode'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $jsonValue = json_encode($darkMode);

    Database::query(
        "INSERT INTO settings (shop_id, scope, setting_key, setting_value) 
         VALUES (?, 'global', 'admin_dark_mode', ?) 
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()",
        [$shopId, $jsonValue]
    );

    echo json_encode([
        'success' => true,
        'dark_mode' => $darkMode
    ]);
}

function handleToggleSidebar(int $shopId): void
{
    $collapsed = filter_var($_POST['sidebar_collapsed'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $jsonValue = json_encode($collapsed);

    Database::query(
        "INSERT INTO settings (shop_id, scope, setting_key, setting_value) 
         VALUES (?, 'global', 'admin_sidebar_collapsed', ?) 
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()",
        [$shopId, $jsonValue]
    );

    echo json_encode([
        'success' => true,
        'sidebar_collapsed' => $collapsed
    ]);
}

function handleGetTranslations(string $langCode): void
{
    require_once __DIR__ . '/../includes/TranslationService.php';

    // Initialize with requested language
    TranslationService::init($langCode);

    // Get all translations for this language
    $translations = TranslationService::getAllTranslations();

    echo json_encode([
        'success' => true,
        'lang' => $langCode,
        'translations' => $translations
    ]);
}
