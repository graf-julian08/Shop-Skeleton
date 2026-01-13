<?php
/**
 * Exchange Rates API - Enterprise Grade
 * Uses Frankfurter API (ECB data) - fetches ALL rates in ONE request
 * https://www.frankfurter.app/
 * Free, no API key required, extremely reliable
 * 
 * Features:
 * - Single API call for ALL currencies (no timeouts)
 * - Automatic hourly background updates via cron
 * - Fallback to cached rates if API unavailable
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
        case 'fetch_rates':
            handleFetchRates($shopId);
            break;
        case 'save_rates':
            // Browser sends rates directly (hybrid approach)
            handleSaveRates($shopId);
            break;
        case 'get_rates':
            handleGetRates($shopId);
            break;
        case 'get_last_update':
            handleGetLastUpdate($shopId);
            break;
        case 'auto_update':
            // For cron job - silent update
            handleAutoUpdate($shopId);
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
 * Fetch exchange rates from Frankfurter API (ECB data)
 * SINGLE REQUEST for ALL currencies - extremely fast and reliable
 */
function handleFetchRates(int $shopId): void
{
    // Get base currency (default currency for the shop)
    $defaultCurrency = Database::fetch(
        "SELECT code FROM currencies WHERE shop_id = ? AND is_default = 1",
        [$shopId]
    );
    $baseCurrency = $defaultCurrency['code'] ?? 'EUR';

    // Get ALL currencies from database
    $currencies = Database::fetchAll(
        "SELECT id, code FROM currencies WHERE shop_id = ?",
        [$shopId]
    );

    if (empty($currencies)) {
        echo json_encode(['success' => false, 'error' => 'Keine Währungen in der Datenbank']);
        return;
    }

    $updated = 0;
    $errors = [];
    $rates = [];

    // Helper function to fetch URL using cURL (more reliable than file_get_contents)
    $fetchUrl = function ($url) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_HTTPHEADER => [
                    'User-Agent: BagistoGenerator/2.0',
                    'Accept: application/json'
                ],
                CURLOPT_FOLLOWLOCATION => true,
                // Disable SSL verification for dev environments (can be enabled in production)
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                error_log("Exchange rate cURL error: " . $error);
                return false;
            }
            return ($httpCode >= 200 && $httpCode < 300) ? $response : false;
        } else {
            // Fallback to file_get_contents
            $context = stream_context_create([
                'http' => [
                    'timeout' => 20,
                    'ignore_errors' => true,
                    'header' => "User-Agent: BagistoGenerator/2.0\r\nAccept: application/json"
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]);
            return @file_get_contents($url, false, $context);
        }
    };

    // Frankfurter API: Get ALL rates in ONE request
    // Try primary base, fallback to EUR if not supported
    $apiUrl = "https://api.frankfurter.app/latest?from={$baseCurrency}";
    $response = $fetchUrl($apiUrl);

    $apiRates = [];
    $apiBase = $baseCurrency;

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['rates']) && is_array($data['rates'])) {
            $apiRates = $data['rates'];
            $apiBase = $data['base'] ?? $baseCurrency;
        }
    }

    // Fallback: If base currency not supported, fetch EUR rates and convert
    if (empty($apiRates) && $baseCurrency !== 'EUR') {
        $apiUrl = "https://api.frankfurter.app/latest?from=EUR";
        $response = $fetchUrl($apiUrl);

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['rates']) && is_array($data['rates'])) {
                $eurRates = $data['rates'];
                $baseToEur = $eurRates[$baseCurrency] ?? null;

                if ($baseToEur && $baseToEur > 0) {
                    // Convert all EUR rates to base currency rates
                    foreach ($eurRates as $code => $rate) {
                        $apiRates[$code] = $rate / $baseToEur;
                    }
                    // Add EUR itself
                    $apiRates['EUR'] = 1 / $baseToEur;
                    $apiBase = $baseCurrency;
                }
            }
        }
    }


    // Still no rates? Show error
    if (empty($apiRates)) {
        echo json_encode([
            'success' => false,
            'error' => 'API nicht erreichbar. Bitte später erneut versuchen.'
        ]);
        return;
    }

    // Update all currencies
    foreach ($currencies as $curr) {
        $targetCode = strtoupper($curr['code']);

        // Base currency is always 1.0
        if ($targetCode === $apiBase) {
            Database::query(
                "UPDATE currencies SET exchange_rate = 1.0 WHERE id = ?",
                [$curr['id']]
            );
            $rates[$targetCode] = 1.0;
            $updated++;
            continue;
        }

        // Check if we have a rate for this currency
        if (isset($apiRates[$targetCode])) {
            $rate = round(floatval($apiRates[$targetCode]), 6);
            Database::query(
                "UPDATE currencies SET exchange_rate = ? WHERE id = ?",
                [$rate, $curr['id']]
            );
            $rates[$targetCode] = $rate;
            $updated++;
        } else {
            // Currency not supported by ECB (exotic currencies)
            $errors[] = $targetCode;
        }
    }

    // Store last update timestamp
    $timestamp = date('Y-m-d H:i:s');
    saveLastUpdateTimestamp($shopId, $timestamp);

    $message = "$updated Wechselkurse aktualisiert (Basis: $apiBase, Quelle: EZB)";
    if (!empty($errors)) {
        $errorList = array_slice($errors, 0, 5);
        $message .= ". Nicht unterstützt: " . implode(', ', $errorList);
        if (count($errors) > 5) {
            $message .= " (+" . (count($errors) - 5) . " weitere)";
        }
    }

    echo json_encode([
        'success' => true,
        'updated' => $updated,
        'base_currency' => $apiBase,
        'timestamp' => $timestamp,
        'rates' => $rates,
        'errors' => $errors,
        'message' => $message
    ]);
}

/**
 * Silent auto-update for cron jobs
 * Can be called hourly: curl "your-site.com/admin/api/exchange_rates.php?action=auto_update&shop_id=1"
 */
function handleAutoUpdate(int $shopId): void
{
    // Check if last update was less than 1 hour ago
    $lastUpdate = getLastUpdateTimestamp($shopId);
    if ($lastUpdate) {
        $lastTime = strtotime($lastUpdate);
        $hourAgo = time() - 3600;
        if ($lastTime > $hourAgo) {
            echo json_encode([
                'success' => true,
                'message' => 'Kürzlich aktualisiert, übersprungen',
                'last_update' => $lastUpdate
            ]);
            return;
        }
    }

    // Run the update
    ob_start();
    handleFetchRates($shopId);
    $result = ob_get_clean();

    // Log the result (optional)
    $data = json_decode($result, true);

    echo json_encode([
        'success' => $data['success'] ?? false,
        'auto_update' => true,
        'updated' => $data['updated'] ?? 0,
        'timestamp' => $data['timestamp'] ?? null
    ]);
}

/**
 * Get cached exchange rates from database
 */
function handleGetRates(int $shopId): void
{
    $currencies = Database::fetchAll(
        "SELECT code, exchange_rate FROM currencies WHERE shop_id = ? AND is_active = 1 ORDER BY code",
        [$shopId]
    );

    $lastUpdate = getLastUpdateTimestamp($shopId);

    echo json_encode([
        'success' => true,
        'currencies' => $currencies,
        'last_update' => $lastUpdate
    ]);
}

/**
 * Get last update timestamp
 */
function handleGetLastUpdate(int $shopId): void
{
    $lastUpdate = getLastUpdateTimestamp($shopId);

    echo json_encode([
        'success' => true,
        'last_update' => $lastUpdate
    ]);
}

/**
 * Helper: Save timestamp
 */
function saveLastUpdateTimestamp(int $shopId, string $timestamp): void
{
    try {
        Database::query(
            "CREATE TABLE IF NOT EXISTS shop_settings (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                shop_id BIGINT UNSIGNED NOT NULL,
                setting_key VARCHAR(100) NOT NULL,
                setting_value TEXT,
                UNIQUE KEY uk_shop_setting (shop_id, setting_key)
            )"
        );

        Database::query(
            "INSERT INTO shop_settings (shop_id, setting_key, setting_value) 
             VALUES (?, 'exchange_rates_updated', ?) 
             ON DUPLICATE KEY UPDATE setting_value = ?",
            [$shopId, $timestamp, $timestamp]
        );
    } catch (Exception $e) {
        // Ignore
    }
}

/**
 * Helper: Get last update timestamp
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
 * Handle rates received from browser (hybrid approach)
 * Browser fetches from Frankfurter API, sends to PHP to save
 */
function handleSaveRates(int $shopId): void
{
    $base = trim($_POST['base'] ?? 'EUR');
    $ratesJson = $_POST['rates'] ?? '';

    if (empty($ratesJson)) {
        echo json_encode(['success' => false, 'error' => 'No rates provided']);
        return;
    }

    $apiRates = json_decode($ratesJson, true);

    if (!is_array($apiRates) || empty($apiRates)) {
        echo json_encode(['success' => false, 'error' => 'Invalid rates format']);
        return;
    }

    // Get all currencies from database
    $currencies = Database::fetchAll(
        "SELECT id, code FROM currencies WHERE shop_id = ?",
        [$shopId]
    );

    if (empty($currencies)) {
        echo json_encode(['success' => false, 'error' => 'No currencies in database']);
        return;
    }

    $updated = 0;
    $rates = [];

    foreach ($currencies as $curr) {
        $code = strtoupper($curr['code']);

        // Base currency is always 1.0
        if ($code === $base) {
            Database::query(
                "UPDATE currencies SET exchange_rate = 1.0 WHERE id = ?",
                [$curr['id']]
            );
            $rates[$code] = 1.0;
            $updated++;
            continue;
        }

        // Check if we have a rate for this currency
        if (isset($apiRates[$code])) {
            $rate = round(floatval($apiRates[$code]), 6);
            Database::query(
                "UPDATE currencies SET exchange_rate = ? WHERE id = ?",
                [$rate, $curr['id']]
            );
            $rates[$code] = $rate;
            $updated++;
        }
    }

    // Save timestamp
    $timestamp = date('Y-m-d H:i:s');
    saveLastUpdateTimestamp($shopId, $timestamp);

    echo json_encode([
        'success' => true,
        'updated' => $updated,
        'base_currency' => $base,
        'timestamp' => $timestamp,
        'rates' => $rates
    ]);
}

