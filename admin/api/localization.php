<?php
/**
 * Localization API v2
 * Redesigned with Locales (Language + Currency + Regional Format combined)
 * Automatic translations via LibreTranslate/MyMemory API
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
        case 'get_available_locales':
            handleGetAvailableLocales();
            break;
        case 'get_active_locales':
            handleGetActiveLocales($shopId);
            break;
        case 'activate_locale':
            handleActivateLocale($shopId);
            break;
        case 'deactivate_locale':
            handleDeactivateLocale($shopId);
            break;
        case 'set_default_locale':
            handleSetDefaultLocale($shopId);
            break;
        case 'save_locale':
            handleSaveLocale($shopId);
            break;
        case 'toggle_locale':
            handleToggleLocale($shopId);
            break;
        case 'delete_locale':
            handleDeleteLocale($shopId);
            break;
        case 'get_translations':
            handleGetTranslations($shopId);
            break;
        case 'save_translation':
            handleSaveTranslation($shopId);
            break;
        case 'auto_translate':
            handleAutoTranslate($shopId);
            break;
        case 'get_translation_stats':
            handleGetTranslationStats($shopId);
            break;
        case 'save_currency':
            handleSaveCurrency($shopId);
            break;
        case 'set_default_currency':
            handleSetDefaultCurrency($shopId);
            break;
        case 'delete_currency':
            handleDeleteCurrency($shopId);
            break;
        case 'toggle_currency':
            handleToggleCurrency($shopId);
            break;
        case 'reseed_locales':
            handleReseedLocales($shopId);
            break;
        case 'reseed_currencies':
            handleReseedCurrencies($shopId);
            break;
        case 'seed_translations':
            handleSeedTranslations($shopId);
            break;
        case 'get_all_locales_from_json':
            handleGetAllLocalesFromJson();
            break;
        // Country handlers
        case 'get_countries':
            handleGetCountries($shopId);
            break;
        case 'set_default_country':
            handleSetDefaultCountry($shopId);
            break;
        case 'toggle_country':
            handleToggleCountry($shopId);
            break;
        case 'save_country':
            handleSaveCountry($shopId);
            break;
        case 'seed_countries':
            handleSeedCountries($shopId);
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
// LOCALE HANDLERS
// =====================================================================

function handleGetAvailableLocales(): void
{
    $localesFile = __DIR__ . '/../data/locales.json';

    if (!file_exists($localesFile)) {
        echo json_encode(['success' => false, 'error' => 'Locales file not found']);
        return;
    }

    $data = json_decode(file_get_contents($localesFile), true);

    echo json_encode([
        'success' => true,
        'locales' => $data['locales'] ?? []
    ]);
}

function handleGetActiveLocales(int $shopId): void
{
    $locales = Database::fetchAll(
        "SELECT * FROM shop_locales WHERE shop_id = ? ORDER BY is_default DESC, language_name",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'locales' => $locales
    ]);
}

function handleActivateLocale(int $shopId): void
{
    $localeCode = trim($_POST['locale_code'] ?? '');

    if (empty($localeCode)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Locale code required']);
        return;
    }

    // Load locale data from JSON
    $localesFile = __DIR__ . '/../data/locales.json';
    $data = json_decode(file_get_contents($localesFile), true);
    $localeData = null;

    foreach ($data['locales'] as $locale) {
        if ($locale['code'] === $localeCode) {
            $localeData = $locale;
            break;
        }
    }

    if (!$localeData) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Locale not found: ' . $localeCode]);
        return;
    }

    // Check if already active
    $existing = Database::fetch(
        "SELECT id FROM shop_locales WHERE shop_id = ? AND code = ?",
        [$shopId, $localeCode]
    );

    if ($existing) {
        echo json_encode(['success' => true, 'message' => 'Locale already active', 'id' => $existing['id']]);
        return;
    }

    // Check if this is the first locale (make it default)
    $count = Database::fetch("SELECT COUNT(*) as cnt FROM shop_locales WHERE shop_id = ?", [$shopId]);
    $isDefault = ($count['cnt'] ?? 0) == 0 ? 1 : 0;

    // Insert locale
    $id = Database::insert('shop_locales', [
        'shop_id' => $shopId,
        'code' => $localeCode,
        'language_code' => $localeData['language_code'],
        'language_name' => $localeData['language_name'],
        'language_native' => $localeData['language_native'],
        'country_code' => $localeData['country_code'],
        'country_name' => $localeData['country_name'],
        'currency_code' => $localeData['currency_code'],
        'currency_symbol' => $localeData['currency_symbol'],
        'currency_position' => $localeData['currency_position'] ?? 'before',
        'decimal_separator' => $localeData['decimal_separator'],
        'thousands_separator' => $localeData['thousands_separator'],
        'date_format' => $localeData['date_format'],
        'time_format' => $localeData['time_format'],
        'timezone' => $localeData['timezone'],
        'is_rtl' => isset($localeData['rtl']) && $localeData['rtl'] ? 1 : 0,
        'is_default' => $isDefault,
        'is_active' => 1
    ]);

    // Auto-translate base translations
    $baseTranslations = $data['base_translations'] ?? [];
    $translatedCount = 0;

    if (!empty($baseTranslations) && $localeData['language_code'] !== 'en') {
        $translatedCount = autoTranslateForLocale($shopId, $localeCode, $localeData['language_code'], $baseTranslations);
    } else {
        // For English, just insert the base translations
        foreach ($baseTranslations as $key => $value) {
            $parts = explode('.', $key, 2);
            $group = $parts[0] ?? 'common';
            $translationKey = $parts[1] ?? $key;

            Database::query(
                "INSERT IGNORE INTO translations (shop_id, locale, translation_group, translation_key, translation_value, is_auto_translated, is_custom) VALUES (?, ?, ?, ?, ?, 0, 0)",
                [$shopId, $localeCode, $group, $translationKey, $value]
            );
            $translatedCount++;
        }
    }

    echo json_encode([
        'success' => true,
        'id' => $id,
        'translated_count' => $translatedCount,
        'message' => "Locale aktiviert mit $translatedCount Übersetzungen"
    ]);
}

function handleDeactivateLocale(int $shopId): void
{
    $localeCode = trim($_POST['locale_code'] ?? '');

    if (empty($localeCode)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Locale code required']);
        return;
    }

    // Check if default
    $locale = Database::fetch(
        "SELECT is_default FROM shop_locales WHERE shop_id = ? AND code = ?",
        [$shopId, $localeCode]
    );

    if ($locale && $locale['is_default']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Standard-Locale kann nicht deaktiviert werden']);
        return;
    }

    // Delete translations for this locale
    Database::query("DELETE FROM translations WHERE shop_id = ? AND locale = ?", [$shopId, $localeCode]);

    // Delete locale
    Database::delete('shop_locales', 'shop_id = ? AND code = ?', [$shopId, $localeCode]);

    echo json_encode(['success' => true, 'message' => 'Locale deaktiviert']);
}

function handleSetDefaultLocale(int $shopId): void
{
    $localeCode = trim($_POST['locale_code'] ?? '');

    if (empty($localeCode)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Locale code required']);
        return;
    }

    // Remove default from all
    Database::query("UPDATE shop_locales SET is_default = 0 WHERE shop_id = ?", [$shopId]);

    // Set new default
    Database::query(
        "UPDATE shop_locales SET is_default = 1, is_active = 1 WHERE shop_id = ? AND code = ?",
        [$shopId, $localeCode]
    );

    echo json_encode(['success' => true, 'message' => 'Standard-Locale gesetzt']);
}

// =====================================================================
// TRANSLATION HANDLERS
// =====================================================================

function handleGetTranslations(int $shopId): void
{
    $locale = $_GET['locale'] ?? null;
    $search = $_GET['search'] ?? null;
    $customOnly = isset($_GET['custom_only']) && $_GET['custom_only'] === '1';

    $params = [$shopId];
    $sql = "SELECT * FROM translations WHERE shop_id = ?";

    if ($locale) {
        $sql .= " AND locale = ?";
        $params[] = $locale;
    }

    if ($customOnly) {
        $sql .= " AND is_custom = 1";
    }

    if ($search) {
        $sql .= " AND (translation_key LIKE ? OR translation_value LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY translation_group, translation_key LIMIT 200";

    $translations = Database::fetchAll($sql, $params);

    echo json_encode([
        'success' => true,
        'translations' => $translations
    ]);
}

function handleSaveTranslation(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);
    $value = $_POST['value'] ?? '';

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID required']);
        return;
    }

    // Update translation and mark as custom
    Database::query(
        "UPDATE translations SET translation_value = ?, is_custom = 1 WHERE id = ? AND shop_id = ?",
        [$value, $id, $shopId]
    );

    echo json_encode(['success' => true, 'message' => 'Übersetzung gespeichert']);
}

function handleAutoTranslate(int $shopId): void
{
    $localeCode = trim($_POST['locale_code'] ?? '');

    if (empty($localeCode)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Locale code required']);
        return;
    }

    // Get locale info
    $locale = Database::fetch(
        "SELECT * FROM shop_locales WHERE shop_id = ? AND code = ?",
        [$shopId, $localeCode]
    );

    if (!$locale) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Locale not found']);
        return;
    }

    // Get all non-custom translations that need updating
    $translations = Database::fetchAll(
        "SELECT id, translation_group, translation_key, translation_value 
         FROM translations 
         WHERE shop_id = ? AND locale = ? AND is_custom = 0",
        [$shopId, $localeCode]
    );

    if (empty($translations)) {
        echo json_encode(['success' => true, 'translated' => 0, 'message' => 'Keine Übersetzungen zu aktualisieren']);
        return;
    }

    // Get English translations as source
    $englishTranslations = [];
    $enLocale = Database::fetch("SELECT code FROM shop_locales WHERE shop_id = ? AND language_code = 'en'", [$shopId]);

    if ($enLocale) {
        $enTrans = Database::fetchAll(
            "SELECT translation_group, translation_key, translation_value FROM translations WHERE shop_id = ? AND locale = ?",
            [$shopId, $enLocale['code']]
        );
        foreach ($enTrans as $t) {
            $englishTranslations[$t['translation_group'] . '.' . $t['translation_key']] = $t['translation_value'];
        }
    }

    // Load base translations as fallback
    $localesFile = __DIR__ . '/../data/locales.json';
    $data = json_decode(file_get_contents($localesFile), true);
    $baseTranslations = $data['base_translations'] ?? [];

    $translated = 0;
    foreach ($translations as $t) {
        $key = $t['translation_group'] . '.' . $t['translation_key'];
        $sourceText = $englishTranslations[$key] ?? $baseTranslations[$key] ?? $t['translation_value'];

        $translatedText = translateWithApi($sourceText, 'en', $locale['language_code']);

        if ($translatedText && $translatedText !== $sourceText) {
            Database::query(
                "UPDATE translations SET translation_value = ?, is_auto_translated = 1 WHERE id = ?",
                [$translatedText, $t['id']]
            );
            $translated++;
        }
    }

    echo json_encode([
        'success' => true,
        'translated' => $translated,
        'message' => "$translated Übersetzungen aktualisiert"
    ]);
}

function handleGetTranslationStats(int $shopId): void
{
    $stats = Database::fetchAll(
        "SELECT locale, 
                COUNT(*) as total,
                SUM(CASE WHEN is_custom = 1 THEN 1 ELSE 0 END) as custom,
                SUM(CASE WHEN is_auto_translated = 1 THEN 1 ELSE 0 END) as auto_translated
         FROM translations 
         WHERE shop_id = ? 
         GROUP BY locale",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}

// =====================================================================
// TRANSLATION API HELPERS
// =====================================================================

function autoTranslateForLocale(int $shopId, string $localeCode, string $targetLang, array $baseTranslations): int
{
    $translated = 0;

    foreach ($baseTranslations as $key => $value) {
        $parts = explode('.', $key, 2);
        $group = $parts[0] ?? 'common';
        $translationKey = $parts[1] ?? $key;

        // Translate from English to target language
        $translatedValue = translateWithApi($value, 'en', $targetLang);

        if (!$translatedValue) {
            $translatedValue = $value; // Fallback to original
        }

        Database::query(
            "INSERT IGNORE INTO translations (shop_id, locale, translation_group, translation_key, translation_value, is_auto_translated, is_custom) VALUES (?, ?, ?, ?, ?, ?, 0)",
            [$shopId, $localeCode, $group, $translationKey, $translatedValue, $translatedValue !== $value ? 1 : 0]
        );

        $translated++;
    }

    return $translated;
}

function translateWithApi(string $text, string $sourceLang, string $targetLang): ?string
{
    if ($sourceLang === $targetLang || empty($text)) {
        return $text;
    }

    // Try MyMemory API (free, 1000 requests/day)
    $url = 'https://api.mymemory.translated.net/get?' . http_build_query([
        'q' => $text,
        'langpair' => $sourceLang . '|' . $targetLang
    ]);

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response) {
        $data = json_decode($response, true);
        if (
            isset($data['responseData']['translatedText']) &&
            $data['responseStatus'] == 200 &&
            !str_contains(strtolower($data['responseData']['translatedText']), 'mymemory')
        ) {
            return $data['responseData']['translatedText'];
        }
    }

    // Fallback - return original
    return null;
}

// =====================================================================
// LOCALE CRUD (for table-based UI)
// =====================================================================

function handleSaveLocale(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);
    $isDefault = intval($_POST['is_default'] ?? 0);

    $data = [
        'shop_id' => $shopId,
        'code' => trim($_POST['code'] ?? ''),
        'language_code' => trim($_POST['language_code'] ?? ''),
        'language_name' => trim($_POST['language_name'] ?? ''),
        'language_native' => trim($_POST['language_native'] ?? ''),
        'country_code' => strtoupper(substr($_POST['code'] ?? '', -2)),
        'country_name' => trim($_POST['country_name'] ?? ''),
        'currency_code' => trim($_POST['currency_code'] ?? 'EUR'),
        'currency_symbol' => getCurrencySymbol($_POST['currency_code'] ?? 'EUR'),
        'currency_position' => 'after',
        'decimal_separator' => ',',
        'thousands_separator' => '.',
        'date_format' => $_POST['date_format'] ?? 'd.m.Y',
        'time_format' => $_POST['time_format'] ?? 'H:i',
        'timezone' => $_POST['timezone'] ?? 'Europe/Berlin',
        'is_rtl' => 0,
        'is_active' => intval($_POST['is_active'] ?? 1),
        'is_default' => $isDefault
    ];

    // If setting as default, clear other defaults first
    if ($isDefault) {
        Database::query("UPDATE shop_locales SET is_default = 0 WHERE shop_id = ?", [$shopId]);
    }

    if ($id) {
        unset($data['shop_id']);
        Database::query(
            "UPDATE shop_locales SET code = ?, language_code = ?, language_name = ?, language_native = ?, country_name = ?, currency_code = ?, currency_symbol = ?, date_format = ?, time_format = ?, is_active = ?, is_default = ? WHERE id = ? AND shop_id = ?",
            [$data['code'], $data['language_code'], $data['language_name'], $data['language_native'], $data['country_name'], $data['currency_code'], $data['currency_symbol'], $data['date_format'], $data['time_format'], $data['is_active'], $data['is_default'], $id, $shopId]
        );
    } else {
        $id = Database::insert('shop_locales', $data);
    }

    echo json_encode(['success' => true, 'id' => $id]);
}

function handleToggleLocale(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);
    $isActive = intval($_POST['is_active'] ?? 0);

    Database::query(
        "UPDATE shop_locales SET is_active = ? WHERE id = ? AND shop_id = ?",
        [$isActive, $id, $shopId]
    );

    echo json_encode(['success' => true]);
}

function handleDeleteLocale(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);

    // Check if default
    $locale = Database::fetch("SELECT is_default FROM shop_locales WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if ($locale && $locale['is_default']) {
        echo json_encode(['success' => false, 'error' => 'Standard-Locale kann nicht gelöscht werden']);
        return;
    }

    Database::delete('shop_locales', 'id = ? AND shop_id = ?', [$id, $shopId]);
    echo json_encode(['success' => true]);
}

// =====================================================================
// CURRENCY CRUD
// =====================================================================

function handleToggleCurrency(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);
    $isActive = intval($_POST['is_active'] ?? 0);

    Database::query(
        "UPDATE currencies SET is_active = ? WHERE id = ? AND shop_id = ?",
        [$isActive, $id, $shopId]
    );

    echo json_encode(['success' => true]);
}

function handleSaveCurrency(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);
    $isDefault = intval($_POST['is_default'] ?? 0);

    $data = [
        'shop_id' => $shopId,
        'code' => strtoupper(trim($_POST['code'] ?? '')),
        'name' => trim($_POST['name'] ?? ''),
        'symbol' => trim($_POST['symbol'] ?? ''),
        'exchange_rate' => floatval($_POST['exchange_rate'] ?? 1),
        'decimal_places' => intval($_POST['decimal_places'] ?? 2),
        'decimal_separator' => '.',
        'thousands_separator' => ',',
        'symbol_position' => $_POST['symbol_position'] ?? 'before',
        'is_active' => 1,
        'is_default' => $isDefault
    ];

    // If setting as default, clear other defaults first
    if ($isDefault) {
        Database::query("UPDATE currencies SET is_default = 0 WHERE shop_id = ?", [$shopId]);
    }

    if ($id) {
        unset($data['shop_id']);
        Database::query(
            "UPDATE currencies SET code = ?, name = ?, symbol = ?, exchange_rate = ?, decimal_places = ?, symbol_position = ?, is_default = ? WHERE id = ? AND shop_id = ?",
            [$data['code'], $data['name'], $data['symbol'], $data['exchange_rate'], $data['decimal_places'], $data['symbol_position'], $data['is_default'], $id, $shopId]
        );
    } else {
        $id = Database::insert('currencies', $data);
    }

    echo json_encode(['success' => true, 'id' => $id]);
}

function handleSetDefaultCurrency(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);

    Database::query("UPDATE currencies SET is_default = 0 WHERE shop_id = ?", [$shopId]);
    Database::query("UPDATE currencies SET is_default = 1, is_active = 1 WHERE id = ? AND shop_id = ?", [$id, $shopId]);

    echo json_encode(['success' => true]);
}

function handleDeleteCurrency(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);

    // Check if default
    $curr = Database::fetch("SELECT is_default FROM currencies WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if ($curr && $curr['is_default']) {
        echo json_encode(['success' => false, 'error' => 'Standard-Währung kann nicht gelöscht werden']);
        return;
    }

    Database::delete('currencies', 'id = ? AND shop_id = ?', [$id, $shopId]);
    echo json_encode(['success' => true]);
}

function getCurrencySymbol(string $code): string
{
    $symbols = [
        'EUR' => '€',
        'USD' => '$',
        'GBP' => '£',
        'CHF' => 'CHF',
        'JPY' => '¥',
        'CNY' => '¥',
        'AUD' => '$',
        'CAD' => '$',
        'SEK' => 'kr',
        'NOK' => 'kr',
        'DKK' => 'kr',
        'PLN' => 'zł',
        'CZK' => 'Kč',
        'HUF' => 'Ft',
        'RON' => 'lei',
        'RUB' => '₽',
        'INR' => '₹',
        'KRW' => '₩',
        'BRL' => 'R$',
        'MXN' => '$'
    ];
    return $symbols[strtoupper($code)] ?? $code;
}

// =====================================================================
// RESEED AND TRANSLATION KEYS
// =====================================================================

function handleReseedLocales(int $shopId): void
{
    $localesFile = __DIR__ . '/../data/locales.json';
    if (!file_exists($localesFile)) {
        echo json_encode(['success' => false, 'error' => 'locales.json not found']);
        return;
    }

    $data = json_decode(file_get_contents($localesFile), true);
    $locales = $data['locales'] ?? [];

    // Get current default
    $currentDefault = Database::fetch(
        "SELECT code FROM shop_locales WHERE shop_id = ? AND is_default = 1",
        [$shopId]
    );
    $defaultCode = $currentDefault['code'] ?? 'de_DE';

    $inserted = 0;
    $updated = 0;

    foreach ($locales as $loc) {
        $isDefault = $loc['code'] === $defaultCode ? 1 : 0;

        // Check if exists
        $existing = Database::fetch(
            "SELECT id FROM shop_locales WHERE shop_id = ? AND code = ?",
            [$shopId, $loc['code']]
        );

        if ($existing) {
            // Update
            Database::query(
                "UPDATE shop_locales SET language_code = ?, language_name = ?, language_native = ?, country_code = ?, country_name = ?, currency_code = ?, currency_symbol = ?, date_format = ?, time_format = ?, timezone = ?, is_rtl = ? WHERE id = ?",
                [
                    $loc['language_code'],
                    $loc['language_name'],
                    $loc['language_native'] ?? $loc['language_name'],
                    $loc['country_code'],
                    $loc['country_name'],
                    $loc['currency_code'],
                    $loc['currency_symbol'],
                    $loc['date_format'],
                    $loc['time_format'],
                    $loc['timezone'],
                    !empty($loc['rtl']) ? 1 : 0,
                    $existing['id']
                ]
            );
            $updated++;
        } else {
            // Insert
            Database::query(
                "INSERT INTO shop_locales (shop_id, code, language_code, language_name, language_native, country_code, country_name, currency_code, currency_symbol, currency_position, decimal_separator, thousands_separator, date_format, time_format, timezone, is_rtl, is_default, is_active) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $shopId,
                    $loc['code'],
                    $loc['language_code'],
                    $loc['language_name'],
                    $loc['language_native'] ?? $loc['language_name'],
                    $loc['country_code'],
                    $loc['country_name'],
                    $loc['currency_code'],
                    $loc['currency_symbol'],
                    'after',
                    ',',
                    '.',
                    $loc['date_format'],
                    $loc['time_format'],
                    $loc['timezone'],
                    !empty($loc['rtl']) ? 1 : 0,
                    $isDefault,
                    1
                ]
            );
            $inserted++;
        }
    }

    echo json_encode([
        'success' => true,
        'inserted' => $inserted,
        'updated' => $updated,
        'total' => count($locales)
    ]);

}

function handleReseedCurrencies(int $shopId): void
{
    $currenciesFile = __DIR__ . '/../data/currencies.json';
    if (!file_exists($currenciesFile)) {
        echo json_encode(['success' => false, 'error' => 'currencies.json not found']);
        return;
    }

    $data = json_decode(file_get_contents($currenciesFile), true);
    // Handle both array-of-objects or object-with-currencies-key formats
    $currencies = isset($data['currencies']) ? $data['currencies'] : $data;

    // Get current default
    $currentDefault = Database::fetch(
        "SELECT code FROM currencies WHERE shop_id = ? AND is_default = 1",
        [$shopId]
    );
    $defaultCode = $currentDefault['code'] ?? 'EUR';

    $inserted = 0;
    $updated = 0;

    foreach ($currencies as $curr) {
        $code = strtoupper($curr['code']);
        $isDefault = $code === $defaultCode ? 1 : 0;

        // Check if exists
        $existing = Database::fetch(
            "SELECT id FROM currencies WHERE shop_id = ? AND code = ?",
            [$shopId, $code]
        );

        if ($existing) {
            // Update metadata but keep exchange rate if it was manually set? 
            // Actually user wants "Standard with correct exchange rate", but exchange rate comes from API.
            // The JSON doesn't have exchange rates usually (defaults to 1.0).
            // We should ONLY update structural data here.
            Database::query(
                "UPDATE currencies SET name = ?, symbol = ?, decimal_places = ?, symbol_position = ? WHERE id = ?",
                [
                    $curr['name'],
                    $curr['symbol'],
                    $curr['decimal_places'] ?? 2,
                    $curr['symbol_position'] ?? 'before',
                    $existing['id']
                ]
            );
            $updated++;
        } else {
            // Insert
            Database::query(
                "INSERT INTO currencies (shop_id, code, name, symbol, exchange_rate, decimal_places, symbol_position, is_default, is_active) 
                 VALUES (?, ?, ?, ?, 1.0, ?, ?, ?, 0)",
                [
                    $shopId,
                    $code,
                    $curr['name'],
                    $curr['symbol'],
                    $curr['decimal_places'] ?? 2,
                    $curr['symbol_position'] ?? 'before',
                    $isDefault
                ]
            );
            $inserted++;
        }
    }

    echo json_encode([
        'success' => true,
        'inserted' => $inserted,
        'updated' => $updated,
        'total' => count($currencies)
    ]);
}


function handleSeedTranslations(int $shopId): void
{
    $keysFile = __DIR__ . '/../data/translation_keys.json';
    if (!file_exists($keysFile)) {
        echo json_encode(['success' => false, 'error' => 'translation_keys.json not found']);
        return;
    }

    $data = json_decode(file_get_contents($keysFile), true);
    $keyGroups = $data['keys'] ?? [];

    // Get locale for translations
    $locale = $_POST['locale'] ?? 'en_US';

    $inserted = 0;
    foreach ($keyGroups as $group => $keys) {
        foreach ($keys as $key => $defaultValue) {
            // Check if exists
            $existing = Database::fetch(
                "SELECT id FROM translations WHERE shop_id = ? AND locale = ? AND translation_group = ? AND translation_key = ?",
                [$shopId, $locale, $group, $key]
            );

            if (!$existing) {
                Database::query(
                    "INSERT IGNORE INTO translations (shop_id, locale, translation_group, translation_key, translation_value) VALUES (?, ?, ?, ?, ?)",
                    [$shopId, $locale, $group, $key, $defaultValue]
                );
                $inserted++;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'inserted' => $inserted,
        'locale' => $locale
    ]);
}

function handleGetAllLocalesFromJson(): void
{
    $localesFile = __DIR__ . '/../data/locales.json';
    if (!file_exists($localesFile)) {
        echo json_encode(['success' => false, 'error' => 'locales.json not found']);
        return;
    }

    $data = json_decode(file_get_contents($localesFile), true);
    echo json_encode([
        'success' => true,
        'locales' => $data['locales'] ?? [],
        'count' => count($data['locales'] ?? [])
    ]);
}

// =====================================================================
// COUNTRY HANDLERS
// =====================================================================

function handleGetCountries(int $shopId): void
{
    $countries = Database::fetchAll(
        "SELECT * FROM countries WHERE shop_id = ? ORDER BY is_default DESC, name",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'countries' => $countries
    ]);
}

function handleSetDefaultCountry(int $shopId): void
{
    $code = trim($_POST['code'] ?? '');

    if (empty($code)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Country code required']);
        return;
    }

    // Remove default from all
    Database::query("UPDATE countries SET is_default = 0 WHERE shop_id = ?", [$shopId]);

    // Set new default and activate it
    Database::query(
        "UPDATE countries SET is_default = 1, is_active = 1 WHERE shop_id = ? AND code = ?",
        [$shopId, $code]
    );

    echo json_encode(['success' => true, 'message' => 'Standard-Land gesetzt']);
}

function handleToggleCountry(int $shopId): void
{
    $code = trim($_POST['code'] ?? '');
    $isActive = intval($_POST['is_active'] ?? 0);

    // Check if default country - can't deactivate default
    $country = Database::fetch(
        "SELECT is_default FROM countries WHERE shop_id = ? AND code = ?",
        [$shopId, $code]
    );

    if ($country && $country['is_default'] && !$isActive) {
        echo json_encode(['success' => false, 'error' => 'Standard-Land kann nicht deaktiviert werden']);
        return;
    }

    Database::query(
        "UPDATE countries SET is_active = ? WHERE shop_id = ? AND code = ?",
        [$isActive, $shopId, $code]
    );

    echo json_encode(['success' => true]);
}

function handleSaveCountry(int $shopId): void
{
    $code = trim($_POST['code'] ?? '');
    $isActive = intval($_POST['is_active'] ?? 0);
    $isDefault = intval($_POST['is_default'] ?? 0);

    // Validate
    if (empty($code)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Country code required']);
        return;
    }

    // If setting as default, clear other defaults first
    if ($isDefault) {
        Database::query("UPDATE countries SET is_default = 0 WHERE shop_id = ?", [$shopId]);
        $isActive = 1; // Default must be active
    }

    // Update country
    Database::query(
        "UPDATE countries SET is_active = ?, is_default = ? WHERE shop_id = ? AND code = ?",
        [$isActive, $isDefault, $shopId, $code]
    );

    echo json_encode(['success' => true, 'message' => 'Land gespeichert']);
}

function handleSeedCountries(int $shopId): void
{
    $countriesFile = __DIR__ . '/../data/countries.php';

    if (!file_exists($countriesFile)) {
        echo json_encode(['success' => false, 'error' => 'Countries data file not found']);
        return;
    }

    $allCountries = require $countriesFile;

    if (!is_array($allCountries) || empty($allCountries)) {
        echo json_encode(['success' => false, 'error' => 'Invalid countries data']);
        return;
    }

    $inserted = 0;
    $updated = 0;

    foreach ($allCountries as $country) {
        // Check if exists
        $existing = Database::fetch(
            "SELECT id FROM countries WHERE shop_id = ? AND code = ?",
            [$shopId, $country['code']]
        );

        if ($existing) {
            // Update metadata only
            Database::query(
                "UPDATE countries SET name = ?, region = ?, languages = ?, currency_code = ? WHERE id = ?",
                [$country['name'], $country['region'], $country['languages'], $country['currency_code'], $existing['id']]
            );
            $updated++;
        } else {
            // Insert new - inactive by default
            Database::query(
                "INSERT INTO countries (shop_id, code, name, region, languages, currency_code, is_active, is_default) VALUES (?, ?, ?, ?, ?, ?, 0, 0)",
                [$shopId, $country['code'], $country['name'], $country['region'], $country['languages'], $country['currency_code']]
            );
            $inserted++;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "$inserted Länder hinzugefügt, $updated aktualisiert",
        'inserted' => $inserted,
        'updated' => $updated,
        'total' => count($allCountries)
    ]);
}


