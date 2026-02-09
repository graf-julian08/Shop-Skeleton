<?php
/**
 * Admin Panel Translations
 * Initializes translation system and provides helper functions
 */

require_once __DIR__ . '/TranslationService.php';

// Global variables for compatibility
$GLOBALS['_admin_translations'] = [];
$GLOBALS['_admin_locale'] = 'de';
$GLOBALS['_admin_lang_code'] = 'de';

/**
 * Initialize admin translations
 */
function initAdminTranslations(int $shopId = 1): void
{
    // Load system settings to get locale
    require_once __DIR__ . '/system_settings.php';
    $settings = loadAdminSettings($shopId);

    $locale = $settings['locale'] ?? 'de';

    // Extract language code (de_DE -> de, or just de -> de)
    $langCode = strpos($locale, '_') !== false ? explode('_', $locale)[0] : $locale;

    $GLOBALS['_admin_locale'] = $locale;
    $GLOBALS['_admin_lang_code'] = $langCode;

    // Initialize TranslationService
    TranslationService::init($langCode);

    // Get all translations for JavaScript
    $GLOBALS['_admin_translations'] = TranslationService::getAllTranslations();
}

/**
 * Translation helper function
 */
function __(string $key, array $params = []): string
{
    return TranslationService::translate($key, $params);
}

/**
 * Get current admin locale
 */
function getAdminLocale(): string
{
    return $GLOBALS['_admin_locale'] ?? 'de';
}

/**
 * Get current admin language code
 */
function getAdminLangCode(): string
{
    return $GLOBALS['_admin_lang_code'] ?? 'de';
}

/**
 * Check if current language is RTL
 */
function isRtlLanguage(): bool
{
    $rtlLanguages = ['ar', 'he', 'fa', 'ur', 'yi', 'ps', 'sd', 'ug'];
    return in_array(getAdminLangCode(), $rtlLanguages);
}
