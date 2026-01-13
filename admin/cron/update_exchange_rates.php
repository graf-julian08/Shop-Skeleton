#!/usr/bin/env php
<?php
/**
 * CRON JOB: Exchange Rate Auto-Update
 * 
 * This script automatically updates exchange rates in the background.
 * Should be run via cron job (recommended: every 6 hours or daily)
 * 
 * Example crontab entries:
 * 
 *   Update every 6 hours:
 *     0 0,6,12,18 * * * /usr/bin/php /path/to/update_exchange_rates.php
 * 
 *   Update daily at 6:00 AM:
 *     0 6 * * * /usr/bin/php /path/to/update_exchange_rates.php
 * 
 *   Update twice daily (6:00 AM and 6:00 PM):
 *     0 6,18 * * * /usr/bin/php /path/to/update_exchange_rates.php
 * 
 * Features:
 * - Rate limiting: Won't update if last update was < 1 hour ago
 * - Only updates ACTIVE currencies (lazy loading)
 * - Uses ExchangeRate-API (160+ currencies)
 * - Logs all activity
 * - Can be run manually for testing
 * 
 * Usage:
 *   php update_exchange_rates.php [--force] [--shop-id=1]
 * 
 *   --force     Force update even if rates are fresh
 *   --shop-id   Shop ID to update (default: 1)
 */


// Ensure this runs from CLI only
if (php_sapi_name() !== 'cli') {
    die('This script must be run from the command line.');
}

// Parse command line arguments
$args = getopt('', ['force', 'shop-id::', 'quiet']);
$force = isset($args['force']);
$quiet = isset($args['quiet']);
$shopId = intval($args['shop-id'] ?? 1);

// Configuration
define('RATE_LIMIT_SECONDS', 3600); // 1 hour
define('API_URL', 'https://open.er-api.com/v6/latest/');

// Load application config
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
Database::configure($database);

// Logging function
function logMessage(string $message, bool $quiet = false): void
{
    $timestamp = date('Y-m-d H:i:s');
    $formatted = "[$timestamp] $message";

    if (!$quiet) {
        echo $formatted . PHP_EOL;
    }

    // Also write to log file
    $logFile = __DIR__ . '/../../logs/exchange_rates.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    file_put_contents($logFile, $formatted . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Main execution
try {
    logMessage("=== Exchange Rate Cron Job Started ===", $quiet);
    logMessage("Shop ID: $shopId, Force: " . ($force ? 'Yes' : 'No'), $quiet);

    // Check rate limit
    $lastUpdate = getLastUpdateTimestamp($shopId);
    if ($lastUpdate && !$force) {
        $lastTime = strtotime($lastUpdate);
        $elapsed = time() - $lastTime;

        if ($elapsed < RATE_LIMIT_SECONDS) {
            $remaining = ceil((RATE_LIMIT_SECONDS - $elapsed) / 60);
            logMessage("Rate limit active: Last update was " . round($elapsed / 60) . " minutes ago. Next update in $remaining minutes.", $quiet);
            logMessage("Use --force to override rate limit.", $quiet);
            exit(0);
        }
    }

    // Get default currency (base)
    $defaultCurrency = Database::fetch(
        "SELECT code FROM currencies WHERE shop_id = ? AND is_default = 1",
        [$shopId]
    );
    $baseCurrency = $defaultCurrency['code'] ?? 'USD';
    logMessage("Base currency: $baseCurrency", $quiet);

    // Get only ACTIVE currencies (lazy loading optimization)
    $currencies = Database::fetchAll(
        "SELECT id, code FROM currencies WHERE shop_id = ? AND is_active = 1",
        [$shopId]
    );

    $totalCurrencies = count($currencies);
    logMessage("Active currencies to update: $totalCurrencies", $quiet);

    if (empty($currencies)) {
        logMessage("No active currencies found. Exiting.", $quiet);
        exit(0);
    }

    // Fetch rates from ExchangeRate-API
    $apiUrl = API_URL . $baseCurrency;
    logMessage("Fetching from: $apiUrl", $quiet);

    $response = fetchUrl($apiUrl);

    if (!$response) {
        logMessage("ERROR: API request failed", $quiet);
        exit(1);
    }

    $data = json_decode($response, true);

    if (!isset($data['result']) || $data['result'] !== 'success' || !isset($data['rates'])) {
        logMessage("ERROR: Invalid API response", $quiet);
        exit(1);
    }

    $apiRates = $data['rates'];
    logMessage("API returned " . count($apiRates) . " currencies", $quiet);

    // Update currencies
    $updated = 0;
    $skipped = 0;

    foreach ($currencies as $curr) {
        $code = strtoupper($curr['code']);

        // Base currency is always 1.0
        if ($code === $baseCurrency) {
            Database::query(
                "UPDATE currencies SET exchange_rate = 1.0 WHERE id = ?",
                [$curr['id']]
            );
            $updated++;
            continue;
        }

        // Update if rate exists
        if (isset($apiRates[$code])) {
            $rate = round(floatval($apiRates[$code]), 6);
            Database::query(
                "UPDATE currencies SET exchange_rate = ? WHERE id = ?",
                [$rate, $curr['id']]
            );
            $updated++;
        } else {
            $skipped++;
        }
    }

    // Save timestamp
    $timestamp = date('Y-m-d H:i:s');
    saveLastUpdateTimestamp($shopId, $timestamp);

    logMessage("SUCCESS: Updated $updated currencies, skipped $skipped (not in API)", $quiet);
    logMessage("=== Exchange Rate Cron Job Completed ===", $quiet);

    exit(0);

} catch (Exception $e) {
    logMessage("FATAL ERROR: " . $e->getMessage(), $quiet);
    exit(1);
}

/**
 * Fetch URL using cURL
 */
function fetchUrl(string $url): ?string
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_HTTPHEADER => [
            'User-Agent: BagistoGenerator/2.0 CronJob',
            'Accept: application/json'
        ],
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        logMessage("cURL Error: $error");
        return null;
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        logMessage("HTTP Error: $httpCode");
        return null;
    }

    return $response;
}

/**
 * Get last update timestamp from database
 */
function getLastUpdateTimestamp(int $shopId): ?string
{
    try {
        $result = Database::fetch(
            "SELECT setting_value FROM shop_settings WHERE shop_id = ? AND setting_key = 'exchange_rates_updated'",
            [$shopId]
        );
        return $result['setting_value'] ?? null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Save last update timestamp to database
 */
function saveLastUpdateTimestamp(int $shopId, string $timestamp): void
{
    try {
        Database::query(
            "INSERT INTO shop_settings (shop_id, setting_key, setting_value) 
             VALUES (?, 'exchange_rates_updated', ?) 
             ON DUPLICATE KEY UPDATE setting_value = ?",
            [$shopId, $timestamp, $timestamp]
        );
    } catch (Exception $e) {
        logMessage("Warning: Could not save timestamp: " . $e->getMessage());
    }
}
