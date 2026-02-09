<?php
/**
 * Translation Service for Admin Panel
 * Uses cached translations with fallback to LibreTranslate API
 * 
 * Supports automatic translation for ALL languages
 */

class TranslationService
{
    private static $translations = [];
    private static $currentLang = 'de';
    private static $cacheFile;

    /**
     * Initialize the translation service
     */
    public static function init(string $langCode = 'de'): void
    {
        self::$currentLang = $langCode;
        self::$cacheFile = __DIR__ . '/../data/translations_cache.json';
        self::loadTranslations();
    }

    /**
     * Load translations: Base first, then Cache overlay
     */
    private static function loadTranslations(): void
    {
        // 1. Load base translations first
        self::loadBaseTranslations();

        // 2. Merge with cache if exists
        $cacheFile = self::$cacheFile;
        if (file_exists($cacheFile)) {
            $cacheData = json_decode(file_get_contents($cacheFile), true);
            if ($cacheData) {
                // Merge cache into translations recursively
                self::$translations = array_replace_recursive(self::$translations, $cacheData);
            }
        }
    }

    /**
     * Load base translations from admin_translations.json
     */
    private static function loadBaseTranslations(): void
    {
        $baseFile = __DIR__ . '/../data/admin_translations.json';
        if (file_exists($baseFile)) {
            $data = json_decode(file_get_contents($baseFile), true);
            if ($data) {
                self::$translations = $data;
            }
        }
    }

    /**
     * Get translation for a key
     * Base language is English - Google Translate widget handles other languages
     */
    public static function translate(string $key, array $params = []): string
    {
        $lang = self::$currentLang;

        // Try current language from cache
        if (isset(self::$translations[$lang][$key])) {
            return self::replacePlaceholders(self::$translations[$lang][$key], $params);
        }

        // Fallback to English (base language)
        if (isset(self::$translations['en'][$key])) {
            return self::replacePlaceholders(self::$translations['en'][$key], $params);
        }

        // Last fallback to German (legacy)
        if (isset(self::$translations['de'][$key])) {
            return self::replacePlaceholders(self::$translations['de'][$key], $params);
        }

        return $key; // Return key as fallback
    }

    /**
     * Translate text using free translation services
     * Reduced timeout for better UX
     */
    private static function translateText(string $text, string $from, string $to): ?string
    {
        // Use Google Translate free endpoint (unofficial)
        $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=' .
            urlencode($from) . '&tl=' . urlencode($to) . '&dt=t&q=' . urlencode($text);

        $context = stream_context_create([
            'http' => [
                'timeout' => 2, // Reduced from 5s to 2s for better UX
                'user_agent' => 'Mozilla/5.0',
                'ignore_errors' => true
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data[0][0][0])) {
                return $data[0][0][0];
            }
        }

        return null;
    }

    /**
     * Replace placeholders in translation
     */
    private static function replacePlaceholders(string $text, array $params): string
    {
        foreach ($params as $key => $value) {
            $text = str_replace(':' . $key, $value, $text);
        }
        return $text;
    }

    /**
     * Save translations cache
     */
    private static function saveCache(): void
    {
        file_put_contents(
            self::$cacheFile,
            json_encode(self::$translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Get all translations for current language
     * Returns cached translations or German fallback
     * Note: Actual translation is handled by Google Translate widget in browser
     */
    public static function getAllTranslations(): array
    {
        $lang = self::$currentLang;

        // German is source - return directly
        if ($lang === 'de' && isset(self::$translations['de'])) {
            return self::$translations['de'];
        }

        // Get source keys
        if (!isset(self::$translations['de'])) {
            self::loadBaseTranslations();
        }

        $germanKeys = self::$translations['de'] ?? [];
        if (empty($germanKeys)) {
            return [];
        }

        // Return cached translations for this language, or German as fallback
        if (isset(self::$translations[$lang]) && !empty(self::$translations[$lang])) {
            // Merge: cached translations + missing keys from German
            return array_merge($germanKeys, self::$translations[$lang]);
        }

        // No cached translations for this language - return German (widget will translate)
        return $germanKeys;
    }

    /**
     * Pre-generate translations for a language
     */
    public static function generateTranslationsForLanguage(string $langCode): bool
    {
        if (!isset(self::$translations['de'])) {
            return false;
        }

        self::$translations[$langCode] = [];

        foreach (self::$translations['de'] as $key => $germanText) {
            $translated = self::translateText($germanText, 'de', $langCode);
            self::$translations[$langCode][$key] = $translated ?? $germanText;
        }

        self::saveCache();
        return true;
    }

    /**
     * Get current language code
     */
    public static function getCurrentLang(): string
    {
        return self::$currentLang;
    }
}

/**
 * Translation helper function - replaces the old __() function
 */
function t(string $key, array $params = []): string
{
    return TranslationService::translate($key, $params);
}
