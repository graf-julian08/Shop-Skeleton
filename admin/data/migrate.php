<?php
/**
 * Migration: Add is_custom column to translations table
 * and reseed locales/currencies from JSON files
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
Database::configure($database);

echo "Starting migration...\n";

// Step 0: Alter column sizes if needed
try {
    Database::query("ALTER TABLE shop_locales MODIFY language_code VARCHAR(20)");
    Database::query("ALTER TABLE shop_locales MODIFY code VARCHAR(20)");
    echo "✓ Updated column sizes\n";
} catch (Exception $e) {
    echo "! Column resize: " . $e->getMessage() . "\n";
}

// Step 1: Add is_custom column if not exists
try {
    Database::query("ALTER TABLE translations ADD COLUMN is_custom TINYINT(1) DEFAULT 0");
    echo "✓ Added is_custom column to translations table\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "✓ is_custom column already exists\n";
    } else {
        echo "! Error adding column: " . $e->getMessage() . "\n";
    }
}

// Step 2: Clear and reseed locales
$shopId = 1;

// Delete existing locales
Database::query("DELETE FROM shop_locales WHERE shop_id = ?", [$shopId]);
echo "✓ Cleared existing locales\n";

// Load new locales from JSON
$localesFile = __DIR__ . '/locales.json';
$localesData = json_decode(file_get_contents($localesFile), true);
$locales = $localesData['locales'] ?? [];

$insertedLocales = 0;
foreach ($locales as $loc) {
    Database::query(
        "INSERT INTO shop_locales (shop_id, code, language_code, language_name, language_native, country_code, country_name, currency_code, currency_symbol, currency_position, decimal_separator, thousands_separator, date_format, time_format, timezone, is_rtl, is_default, is_active) 
         VALUES (?, ?, ?, ?, ?, '', '', 'EUR', '€', 'after', ',', '.', 'd.m.Y', 'H:i', 'UTC', ?, ?, 1)",
        [
            $shopId,
            $loc['code'],
            $loc['language_code'],
            $loc['language_name'],
            $loc['language_native'],
            ($loc['rtl'] ?? false) ? 1 : 0,
            $loc['code'] === 'de' ? 1 : 0
        ]
    );
    $insertedLocales++;
}
echo "✓ Inserted $insertedLocales locales\n";

// Step 3: Clear and reseed currencies
Database::query("DELETE FROM currencies WHERE shop_id = ?", [$shopId]);
echo "✓ Cleared existing currencies\n";

$currenciesFile = __DIR__ . '/currencies.json';
$currenciesData = json_decode(file_get_contents($currenciesFile), true);
$currencies = $currenciesData['currencies'] ?? [];

$insertedCurrencies = 0;
foreach ($currencies as $curr) {
    Database::query(
        "INSERT INTO currencies (shop_id, code, name, symbol, exchange_rate, decimal_places, decimal_separator, thousands_separator, symbol_position, is_default, is_active) 
         VALUES (?, ?, ?, ?, 1.0, ?, '.', ',', ?, ?, 1)",
        [
            $shopId,
            $curr['code'],
            $curr['name'],
            $curr['symbol'],
            $curr['decimal_places'],
            $curr['symbol_position'] ?? 'before',
            $curr['code'] === 'EUR' ? 1 : 0
        ]
    );
    $insertedCurrencies++;
}
echo "✓ Inserted $insertedCurrencies currencies\n";

echo "\n=== Migration Complete ===\n";
echo "Total locales: $insertedLocales\n";
echo "Total currencies: $insertedCurrencies\n";
