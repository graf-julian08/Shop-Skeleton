<?php
/**
 * Shop - Lokalisierung v4 (Enterprise Grade)
 * - 133 Google Translate languages
 * - 135 Stripe currencies  
 * - Proper delete confirmation
 * - Single default handling
 * - Validation for new entries
 * - Geo-location integration
 */

global $database;
require_once __DIR__ . '/../../includes/Database.php';
if (is_array($database)) {
    Database::configure($database);
}

/**
 * Format exchange rate with Swiss style: 1'000.0000
 * Uses apostrophe as thousands separator
 */
function formatExchangeRate(float $rate): string
{
    // Format with 4 decimal places
    $formatted = number_format($rate, 4, '.', "'");
    return $formatted;
}

$shopId = 1;

// Seed locales from JSON if empty
$localeCount = Database::fetch("SELECT COUNT(*) as cnt FROM shop_locales WHERE shop_id = ?", [$shopId]);
if (($localeCount['cnt'] ?? 0) == 0) {
    $localesFile = __DIR__ . '/../../data/locales.json';
    if (file_exists($localesFile)) {
        $data = json_decode(file_get_contents($localesFile), true);
        foreach ($data['locales'] ?? [] as $idx => $loc) {
            Database::query(
                "INSERT IGNORE INTO shop_locales (shop_id, code, language_code, language_name, language_native, country_code, country_name, currency_code, currency_symbol, currency_position, decimal_separator, thousands_separator, date_format, time_format, timezone, is_rtl, is_default, is_active) 
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
                    $idx === 0 ? 1 : 0, // First one is default  
                    1
                ]
            );
        }
    }
}

// Seed currencies from JSON if empty
$currencyCount = Database::fetch("SELECT COUNT(*) as cnt FROM currencies WHERE shop_id = ?", [$shopId]);
if (($currencyCount['cnt'] ?? 0) == 0) {
    $currenciesFile = __DIR__ . '/../../data/currencies.json';
    if (file_exists($currenciesFile)) {
        $data = json_decode(file_get_contents($currenciesFile), true);
        foreach ($data['currencies'] ?? [] as $idx => $curr) {
            Database::query(
                "INSERT IGNORE INTO currencies (shop_id, code, name, symbol, exchange_rate, decimal_places, decimal_separator, thousands_separator, symbol_position, is_default, is_active) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $shopId,
                    $curr['code'],
                    $curr['name'],
                    $curr['symbol'],
                    1.0,
                    $curr['decimal_places'],
                    '.',
                    ',',
                    $curr['symbol_position'] ?? 'before',
                    $curr['code'] === 'EUR' ? 1 : 0,
                    1
                ]
            );
        }
    }
}

// Load data
$locales = Database::fetchAll(
    "SELECT * FROM shop_locales WHERE shop_id = ? ORDER BY is_default DESC, language_name, country_name",
    [$shopId]
) ?: [];

$currencies = Database::fetchAll(
    "SELECT * FROM currencies WHERE shop_id = ? ORDER BY is_default DESC, name",
    [$shopId]
) ?: [];

// Seed countries if empty
$countryCount = Database::fetch("SELECT COUNT(*) as cnt FROM countries WHERE shop_id = ?", [$shopId]);
if (($countryCount['cnt'] ?? 0) == 0) {
    $countriesFile = __DIR__ . '/../../admin/data/countries.php';
    if (file_exists($countriesFile)) {
        $allCountries = require $countriesFile;
        foreach ($allCountries as $country) {
            Database::query(
                "INSERT IGNORE INTO countries (shop_id, code, name, region, languages, currency_code, is_active, is_default) VALUES (?, ?, ?, ?, ?, ?, 0, 0)",
                [$shopId, $country['code'], $country['name'], $country['region'], $country['languages'], $country['currency_code']]
            );
        }
    }
}

// Get countries count for badge
$countriesCount = Database::fetch("SELECT COUNT(*) as cnt FROM countries WHERE shop_id = ?", [$shopId]);
$countriesTotal = $countriesCount['cnt'] ?? 0;

// Load validations
$localesFile = __DIR__ . '/../../data/locales.json';
$validLocales = [];
$validLanguageCodes = [];
if (file_exists($localesFile)) {
    $data = json_decode(file_get_contents($localesFile), true);
    foreach ($data['locales'] ?? [] as $loc) {
        $validLocales[$loc['code']] = $loc;
    }
    $validLanguageCodes = $data['google_translate_supported'] ?? [];
}

$currenciesFile = __DIR__ . '/../../data/currencies.json';
$validCurrencies = [];
if (file_exists($currenciesFile)) {
    $data = json_decode(file_get_contents($currenciesFile), true);
    foreach ($data['currencies'] ?? [] as $curr) {
        $validCurrencies[$curr['code']] = $curr;
    }
}
?>

<style>
    /* Toggle Switch - Green Glass Effect */
    .toggle-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .toggle-label {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        user-select: none;
    }

    .toggle-label:has(.toggle-input:disabled) {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .toggle-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-switch {
        position: relative;
        width: 52px;
        height: 28px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .toggle-switch::before {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 20px;
        height: 20px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .toggle-input:checked+.toggle-switch {
        background: linear-gradient(135deg, rgba(52, 199, 89, 0.8), rgba(48, 209, 88, 0.6));
        border-color: rgba(52, 199, 89, 0.4);
        box-shadow: 0 0 20px rgba(52, 199, 89, 0.3),
            inset 0 1px 1px rgba(255, 255, 255, 0.2);
    }

    .toggle-input:checked+.toggle-switch::before {
        transform: translateX(24px);
        background: #fff;
        box-shadow: 0 2px 12px rgba(52, 199, 89, 0.4);
    }

    .toggle-input:focus+.toggle-switch {
        outline: 2px solid rgba(52, 199, 89, 0.5);
        outline-offset: 2px;
    }

    .toggle-input:disabled+.toggle-switch {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .toggle-text {
        font-size: 14px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.9);
    }

    /* Form hint styling */
    .form-hint {
        display: block;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.5);
        margin-top: 6px;
    }
</style>

<div class="page-header">
    <div class="page-header-content">
        <h1>Lokalisierung</h1>
        <p class="page-subtitle">Sprachen, Währungen und Übersetzungen für deinen Shop</p>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="sprachen">
        <span class="material-symbols-rounded">translate</span>
        Sprachen
        <span class="badge badge-info"><?= count($locales) ?></span>
    </button>
    <button class="tab" data-tab="waehrungen">
        <span class="material-symbols-rounded">payments</span>
        Währungen
        <span class="badge badge-info"><?= count($currencies) ?></span>
    </button>
    <button class="tab" data-tab="laender">
        <span class="material-symbols-rounded">public</span>
        Länder
        <span class="badge badge-info"><?= $countriesTotal ?></span>
    </button>
    <button class="tab" data-tab="geolocation">
        <span class="material-symbols-rounded">location_on</span>
        Geo-Location
    </button>
</div>

<!-- Tab: Sprachen -->
<div data-tab-content="sprachen">
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <h3>Alle Sprachen</h3>
                <span class="text-muted">(<?= count($locales) ?> Sprachen, alle Google Translate kompatibel)</span>
            </div>
            <div class="card-header-actions">
                <div class="search-box">
                    <span class="material-symbols-rounded">search</span>
                    <input type="text" id="locale-search" placeholder="Sprache suchen..."
                        oninput="locManager.filterTable('locales')">
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table" id="locales-table">
                <thead>
                    <tr>
                        <th>Sprache</th>
                        <th>Code</th>
                        <th>Währung</th>
                        <th>Format</th>
                        <th>Zeitzone</th>
                        <th>Status</th>
                        <th style="width: 60px;">Aktionen</th>
                    </tr>
                </thead>
                <tbody id="locales-tbody">
                    <?php foreach ($locales as $loc): ?>
                        <tr data-id="<?= $loc['id'] ?>">
                            <td>
                                <strong><?= htmlspecialchars($loc['language_native']) ?></strong>
                                <span class="text-muted">(<?= htmlspecialchars($loc['country_name']) ?>)</span>
                            </td>
                            <td><code><?= $loc['code'] ?></code></td>
                            <td><span class="currency-badge"><?= $loc['currency_symbol'] ?>
                                    <?= $loc['currency_code'] ?></span></td>
                            <td class="text-muted text-sm"><?= $loc['date_format'] ?></td>
                            <td class="text-muted text-sm"><?= $loc['timezone'] ?></td>
                            <td>
                                <?php if ($loc['is_default']): ?>
                                    <span class="badge badge-success">Aktiviert</span>
                                    <span class="badge badge-primary" style="margin-left: 4px;">Standard</span>
                                <?php elseif ($loc['is_active']): ?>
                                    <span class="badge badge-success">Aktiviert</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Deaktiviert</span>
                                <?php endif; ?>
                            </td>
                            <td class="table-actions">
                                <button class="btn btn-sm"
                                    onclick="locManager.openLocaleEditModal(<?= $loc['id'] ?>, '<?= htmlspecialchars($loc['language_native'], ENT_QUOTES) ?>', <?= $loc['is_active'] ? 'true' : 'false' ?>, <?= $loc['is_default'] ? 'true' : 'false' ?>)"
                                    title="Status bearbeiten">
                                    <span class="material-symbols-rounded">edit</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Währungen -->
<div data-tab-content="waehrungen" style="display: none;">
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <h3>Währungen</h3>
                <span class="text-muted">(<?= count($currencies) ?> Währungen, alle Stripe kompatibel)</span>
            </div>
            <div class="card-header-actions">
                <div class="search-box">
                    <span class="material-symbols-rounded">search</span>
                    <input type="text" id="currency-search" placeholder="Währung suchen..."
                        oninput="locManager.filterTable('currencies')">
                </div>
                <button class="btn btn-sm" onclick="locManager.fetchExchangeRates()" id="fetch-rates-btn"
                    title="Wechselkurse von Hexarate kostenfrei laden">
                    <span class="material-symbols-rounded">currency_exchange</span>
                    Kurse aktualisieren
                </button>
            </div>
        </div>
        <div class="card-subtitle" id="exchange-rates-info">
            <span class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle;">schedule</span>
            <span id="rates-last-update">Lade Aktualisierungszeit...</span>
            <span class="text-muted" style="margin-left: 8px;">(Quelle: ExchangeRate-API)</span>
        </div>
        <div class="card-body table-responsive">
            <table class="table" id="currencies-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Symbol</th>
                        <th>Wechselkurs</th>
                        <th>Format</th>
                        <th>Status</th>
                        <th style="width: 60px;">Aktionen</th>
                    </tr>
                </thead>
                <tbody id="currencies-tbody">
                    <?php foreach ($currencies as $curr): ?>
                        <tr data-id="<?= $curr['id'] ?>">
                            <td><strong><?= htmlspecialchars($curr['name']) ?></strong></td>
                            <td><code><?= $curr['code'] ?></code></td>
                            <td class="currency-symbol-cell"><?= $curr['symbol'] ?></td>
                            <td><?= formatExchangeRate($curr['exchange_rate']) ?></td>
                            <td class="text-muted">
                                <?= $curr['symbol_position'] === 'before' ? $curr['symbol'] . '100' : '100' . $curr['symbol'] ?>
                            </td>
                            <td>
                                <?php if ($curr['is_default']): ?>
                                    <span class="badge badge-success">Aktiviert</span>
                                    <span class="badge badge-primary" style="margin-left: 4px;">Standard</span>
                                <?php elseif ($curr['is_active']): ?>
                                    <span class="badge badge-success">Aktiviert</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Deaktiviert</span>
                                <?php endif; ?>
                            </td>
                            <td class="table-actions">
                                <button class="btn btn-sm"
                                    onclick="locManager.openCurrencyEditModal(<?= $curr['id'] ?>, '<?= htmlspecialchars($curr['name'], ENT_QUOTES) ?>', <?= $curr['is_active'] ? 'true' : 'false' ?>, <?= $curr['is_default'] ? 'true' : 'false' ?>)"
                                    title="Status bearbeiten">
                                    <span class="material-symbols-rounded">edit</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Länder -->
<div data-tab-content="laender" style="display: none;">
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <h3>Alle Länder</h3>
                <span class="text-muted">195+ Länder mit Regionen, Sprachen & Währungen</span>
            </div>
            <div class="card-header-actions">
                <div class="search-box">
                    <span class="material-symbols-rounded">search</span>
                    <input type="text" id="country-search" placeholder="Land suchen..."
                        oninput="locManager.filterTable('countries')">
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table" id="countries-table">
                <thead>
                    <tr>
                        <th>Land</th>
                        <th>ISO Code</th>
                        <th>Region</th>
                        <th>Sprache(n)</th>
                        <th>Währung</th>
                        <th>Status</th>
                        <th style="width: 60px;">Aktionen</th>
                    </tr>
                </thead>
                <tbody id="countries-tbody">
                    <?php
                    // Load countries from database
                    $countriesFromDb = Database::fetchAll(
                        "SELECT * FROM countries WHERE shop_id = ? ORDER BY is_default DESC, name",
                        [$shopId]
                    ) ?: [];

                    foreach ($countriesFromDb as $country):
                        $isActive = $country['is_active'] == 1;
                        $isDefault = $country['is_default'] == 1;
                        ?>
                        <tr data-code="<?= $country['code'] ?>">
                            <td><strong><?= htmlspecialchars($country['name']) ?></strong></td>
                            <td><code><?= $country['code'] ?></code></td>
                            <td class="text-muted"><?= htmlspecialchars($country['region']) ?></td>
                            <td class="text-muted text-sm"><?= htmlspecialchars($country['languages']) ?></td>
                            <td><span class="currency-badge"><?= $country['currency_code'] ?></span></td>
                            <td>
                                <?php if ($isDefault): ?>
                                    <span class="badge badge-success">Aktiviert</span>
                                    <span class="badge badge-primary" style="margin-left: 4px;">Standard</span>
                                <?php elseif ($isActive): ?>
                                    <span class="badge badge-success">Aktiviert</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Deaktiviert</span>
                                <?php endif; ?>
                            </td>
                            <td class="table-actions">
                                <button class="btn btn-sm"
                                    onclick="locManager.openCountryEditModal('<?= $country['code'] ?>', '<?= htmlspecialchars($country['name'], ENT_QUOTES) ?>', <?= $isActive ? 'true' : 'false' ?>, <?= $isDefault ? 'true' : 'false' ?>)"
                                    title="Status bearbeiten">
                                    <span class="material-symbols-rounded">edit</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Geo-Location (Improved UI) -->
<div data-tab-content="geolocation" style="display: none;">
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <h3>Geo-Location Einstellungen</h3>
                <span class="text-muted">Automatische Sprach- und Währungserkennung</span>
            </div>
        </div>
        <div class="card-body">
            <!-- Feature Cards Grid -->
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <!-- Card 1: Auto-Detection -->
                <div
                    style="background: linear-gradient(135deg, rgba(52, 199, 89, 0.15), rgba(48, 209, 88, 0.05)); border: 1px solid rgba(52, 199, 89, 0.3); border-radius: 12px; padding: 20px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <span class="material-symbols-rounded"
                            style="color: #34c759; font-size: 28px;">location_on</span>
                        <strong style="font-size: 16px;">Standorterkennung</strong>
                    </div>
                    <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin: 0;">
                        Erkennt automatisch den Standort deiner Besucher und schlägt die passende Sprache vor.
                    </p>
                </div>

                <!-- Card 2: Language Popup -->
                <div
                    style="background: linear-gradient(135deg, rgba(0, 122, 255, 0.15), rgba(10, 132, 255, 0.05)); border: 1px solid rgba(0, 122, 255, 0.3); border-radius: 12px; padding: 20px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <span class="material-symbols-rounded" style="color: #007aff; font-size: 28px;">language</span>
                        <strong style="font-size: 16px;">Sprachauswahl-Popup</strong>
                    </div>
                    <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin: 0;">
                        Zeigt Besuchern ein elegantes Popup zur Bestätigung ihrer bevorzugten Sprache.
                    </p>
                </div>

                <!-- Card 3: Currency Matching -->
                <div
                    style="background: linear-gradient(135deg, rgba(255, 149, 0, 0.15), rgba(255, 159, 10, 0.05)); border: 1px solid rgba(255, 149, 0, 0.3); border-radius: 12px; padding: 20px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <span class="material-symbols-rounded" style="color: #ff9500; font-size: 28px;">payments</span>
                        <strong style="font-size: 16px;">Währungsanpassung</strong>
                    </div>
                    <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin: 0;">
                        Passt Preise automatisch an die lokale Währung des Besuchers an.
                    </p>
                </div>
            </div>

            <!-- Settings Section -->
            <div
                style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 24px; border: 1px solid rgba(255,255,255,0.08); margin-bottom: 24px;">
                <h4 style="margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-rounded">settings</span>
                    API-Konfiguration
                </h4>

                <div class="form-group" style="margin-bottom: 20px;">
                    <div class="toggle-group">
                        <label class="toggle-label">
                            <input type="checkbox" id="geo-enabled" class="toggle-input" checked
                                onchange="locManager.toggleGeoSettings()">
                            <span class="toggle-switch"></span>
                            <span class="toggle-text">Geo-Location aktivieren</span>
                        </label>
                    </div>
                </div>

                <div id="geo-settings-container">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">API Service</label>
                            <select id="geo-service" class="form-select" onchange="locManager.saveGeoSettings()">
                                <option value="ip-api" selected>ip-api.com (Kostenlos, 45 req/min)</option>
                                <option value="ipapi">ipapi.co (1000/Tag, API-Key optional)</option>
                                <option value="ipinfo">ipinfo.io (50.000/Monat mit Key)</option>
                            </select>
                        </div>

                        <div class="form-group" id="geo-apikey-group" style="display: none;">
                            <label class="form-label">API-Key (optional)</label>
                            <input type="text" id="geo-apikey" class="form-input" placeholder="Für höhere Rate-Limits"
                                onblur="locManager.saveGeoSettings()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popup Behavior Settings -->
            <div
                style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 24px; border: 1px solid rgba(255,255,255,0.08); margin-bottom: 24px;">
                <h4 style="margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-rounded">tune</span>
                    Popup-Verhalten
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Popup anzeigen</label>
                        <select id="geo-popup-behavior" class="form-select" onchange="locManager.saveGeoSettings()">
                            <option value="mismatch" selected>Nur bei Standort-Mismatch</option>
                            <option value="always">Immer beim ersten Besuch</option>
                            <option value="never">Nie (nur automatisch wechseln)</option>
                        </select>
                        <small class="form-hint">Wann soll das Popup erscheinen?</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cookie-Dauer</label>
                        <select id="geo-cookie-duration" class="form-select" onchange="locManager.saveGeoSettings()">
                            <option value="session">Nur diese Session</option>
                            <option value="1d">1 Tag</option>
                            <option value="7d" selected>7 Tage</option>
                            <option value="30d">30 Tage</option>
                            <option value="365d">1 Jahr</option>
                        </select>
                        <small class="form-hint">Wie lange soll die Auswahl gespeichert werden?</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Fallback bei API-Fehler</label>
                        <select id="geo-fallback" class="form-select" onchange="locManager.saveGeoSettings()">
                            <option value="default" selected>Standard-Sprache des Shops</option>
                            <option value="browser">Browser-Sprache verwenden</option>
                            <option value="ask">Popup immer zeigen</option>
                        </select>
                        <small class="form-hint">Was passiert wenn die API nicht erreichbar ist?</small>
                    </div>
                </div>
            </div>

            <!-- Popup Designer Section -->
            <div
                style="background: linear-gradient(135deg, rgba(175, 82, 222, 0.1), rgba(191, 90, 242, 0.05)); border-radius: 12px; padding: 24px; border: 1px solid rgba(175, 82, 222, 0.3);">
                <div
                    style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <h4 style="margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-rounded" style="color: #af52de;">design_services</span>
                            Popup-Designer
                        </h4>
                        <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin: 0 0 16px 0;">
                            Gestalte das Standort-Popup mit dem Drag-and-Drop-Editor. Füge Texte, Bilder, Flaggen und
                            Buttons hinzu.
                        </p>
                        <a href="?page=shop/geo_popup_editor" class="btn btn-primary">
                            <span class="material-symbols-rounded">edit</span>
                            Popup bearbeiten
                        </a>
                    </div>

                    <!-- Popup Preview -->
                    <div id="geo-popup-preview" style="flex-shrink: 0;">
                        <div
                            style="background: var(--bg-card); border-radius: 12px; padding: 20px; border: 1px solid rgba(255,255,255,0.1); width: 280px; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                            <div style="display: flex; justify-content: center; gap: 8px; margin-bottom: 12px;">
                                <span style="font-size: 24px;">🇨🇭</span>
                                <span style="font-size: 24px;">→</span>
                                <span style="font-size: 24px;">🇺🇸</span>
                            </div>
                            <h5 style="margin: 0 0 8px 0; font-size: 15px;">Falscher Shop?</h5>
                            <p style="color: rgba(255,255,255,0.6); font-size: 13px; margin: 0 0 16px 0;">
                                Du befindest dich in der Schweiz, aber siehst die US-Version.
                            </p>
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <button class="btn btn-sm" style="opacity: 0.8;">Hier bleiben</button>
                                <button class="btn btn-sm btn-primary">Zur 🇨🇭 Version</button>
                            </div>
                        </div>
                        <p style="text-align: center; color: rgba(255,255,255,0.4); font-size: 11px; margin-top: 8px;">
                            Live-Vorschau
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div class="modal" id="delete-modal">
    <div class="modal-content modal-sm">
        <div class="modal-header modal-header-danger">
            <span class="material-symbols-rounded">warning</span>
            <h3>Wirklich löschen?</h3>
        </div>
        <div class="modal-body">
            <p id="delete-message">Möchtest du diesen Eintrag wirklich löschen?</p>
            <p class="text-muted">Diese Aktion kann nicht rückgängig gemacht werden.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" data-close-modal>Abbrechen</button>
            <button type="button" class="btn btn-danger" id="delete-confirm-btn"
                onclick="locManager.executeDelete()">Löschen</button>
        </div>
    </div>
</div>

<!-- Locale Modal -->
<div class="modal" id="locale-modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3 id="locale-modal-title">Sprache hinzufügen</h3>
            <button class="btn btn-icon" data-close-modal>
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <form id="locale-form" onsubmit="locManager.saveLocale(event)">
            <input type="hidden" name="id" value="">
            <div class="modal-body">
                <div id="locale-validation-warning" class="info-box info-box-warning" style="display: none;">
                    <span class="material-symbols-rounded">warning</span>
                    <div>
                        <strong>Unbekannte Sprache</strong>
                        <p>Diese Sprache wird möglicherweise nicht von Google Translate unterstützt.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Locale-Code *</label>
                        <input type="text" name="code" class="form-input" placeholder="z.B. de_DE, en_US" required
                            oninput="locManager.validateLocaleCode(this.value)">
                        <span class="form-hint">ISO 639-1 Sprache + ISO 3166-1 Land</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sprachcode</label>
                        <input type="text" name="language_code" class="form-input" placeholder="z.B. de, en">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Sprachname *</label>
                        <input type="text" name="language_name" class="form-input" placeholder="z.B. German" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Native Name *</label>
                        <input type="text" name="language_native" class="form-input" placeholder="z.B. Deutsch"
                            required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Land</label>
                        <input type="text" name="country_name" class="form-input" placeholder="z.B. Germany">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Währung</label>
                        <select name="currency_code" class="form-select" id="locale-currency-select">
                            <?php foreach ($currencies as $curr): ?>
                                <option value="<?= $curr['code'] ?>"><?= $curr['code'] ?> - <?= $curr['name'] ?>
                                    (<?= $curr['symbol'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Datumsformat</label>
                        <select name="date_format" class="form-select">
                            <option value="d.m.Y">TT.MM.JJJJ (31.12.2024)</option>
                            <option value="d/m/Y">TT/MM/JJJJ (31/12/2024)</option>
                            <option value="m/d/Y">MM/TT/JJJJ (12/31/2024)</option>
                            <option value="Y-m-d">JJJJ-MM-TT (2024-12-31)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Zeitformat</label>
                        <select name="time_format" class="form-select">
                            <option value="H:i">24h (14:30)</option>
                            <option value="g:i A">12h (2:30 PM)</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="is_default">
                            <span>Als Standard setzen</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="is_active" checked>
                            <span>Aktiv</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-close-modal>Abbrechen</button>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
        </form>
    </div>
</div>

<!-- Message Modal (Success/Error) -->
<div class="modal" id="message-modal">
    <div class="modal-content modal-sm">
        <div class="modal-header" id="message-modal-header">
            <span class="material-symbols-rounded" id="message-modal-icon">info</span>
            <h3 id="message-modal-title">Info</h3>
        </div>
        <div class="modal-body">
            <p id="message-modal-text"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-close-modal>OK</button>
        </div>
    </div>
</div>

<div class="modal" id="confirm-modal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <span class="material-symbols-rounded">help</span>
            <h3>Bestätigung</h3>
        </div>
        <div class="modal-body">
            <p id="confirm-modal-text"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" data-close-modal>Abbrechen</button>
            <button type="button" class="btn btn-primary" id="confirm-modal-btn">Bestätigen</button>
        </div>
    </div>
</div>

<!-- Edit Status Modal (Premium Design) -->
<div class="modal" id="edit-status-modal">
    <div class="modal-content modal-sm" style="overflow: hidden;">
        <div class="modal-header"
            style="background: linear-gradient(135deg, rgba(0, 122, 255, 0.2), rgba(88, 86, 214, 0.15)); border-bottom: 1px solid rgba(255,255,255,0.1);">
            <span class="material-symbols-rounded" style="color: #007aff;">tune</span>
            <h3 id="edit-status-modal-title">Status bearbeiten</h3>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <input type="hidden" id="edit-status-id">
            <input type="hidden" id="edit-status-type">

            <!-- Active Status Card -->
            <div
                style="background: rgba(52, 199, 89, 0.08); border: 1px solid rgba(52, 199, 89, 0.2); border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span class="material-symbols-rounded"
                            style="color: #34c759; font-size: 24px;">power_settings_new</span>
                        <div>
                            <strong style="font-size: 15px;">Aktiviert</strong>
                            <p style="font-size: 12px; color: rgba(255,255,255,0.5); margin: 2px 0 0 0;">Element im Shop
                                anzeigen</p>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <label class="toggle-label" style="gap: 0;">
                            <input type="checkbox" id="edit-status-active" class="toggle-input">
                            <span class="toggle-switch"></span>
                        </label>
                    </div>
                </div>
            </div>
            <small class="form-hint" id="edit-status-active-hint"
                style="display: block; margin-bottom: 16px;">Mindestens eine Sprache/Währung muss aktiv sein.</small>

            <!-- Default Status Card -->
            <div
                style="background: rgba(0, 122, 255, 0.08); border: 1px solid rgba(0, 122, 255, 0.2); border-radius: 12px; padding: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span class="material-symbols-rounded" style="color: #007aff; font-size: 24px;">star</span>
                        <div>
                            <strong style="font-size: 15px;">Als Standard</strong>
                            <p style="font-size: 12px; color: rgba(255,255,255,0.5); margin: 2px 0 0 0;">Wird neuen
                                Besuchern angezeigt</p>
                        </div>
                    </div>
                    <div class="toggle-group">
                        <label class="toggle-label" style="gap: 0;">
                            <input type="checkbox" id="edit-status-default" class="toggle-input">
                            <span class="toggle-switch"></span>
                        </label>
                    </div>
                </div>
            </div>
            <small class="form-hint" style="display: block; margin-top: 8px;">Beim Setzen als Standard wird der
                vorherige Standard automatisch entfernt.</small>
        </div>
        <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08); padding: 16px 24px;">
            <button type="button" class="btn" data-close-modal>Abbrechen</button>
            <button type="button" class="btn btn-primary" onclick="locManager.saveStatusEdit()">
                <span class="material-symbols-rounded">check</span>
                Speichern
            </button>
        </div>
    </div>
</div>

<!-- Prompt Modal (Edit Translation) -->
<div class="modal" id="prompt-modal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <span class="material-symbols-rounded">edit</span>
            <h3 id="prompt-modal-title">Bearbeiten</h3>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label" id="prompt-modal-label">Wert</label>
                <textarea id="prompt-modal-input" class="form-input" rows="4" style="width: 100%;"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" data-close-modal>Abbrechen</button>
            <button type="button" class="btn btn-primary" id="prompt-modal-btn">Speichern</button>
        </div>
    </div>
</div>

<!-- Currency Modal -->
<div class="modal" id="currency-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="currency-modal-title">Währung hinzufügen</h3>
            <button class="btn btn-icon" data-close-modal>
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <form id="currency-form" onsubmit="locManager.saveCurrency(event)">
            <input type="hidden" name="id" value="">
            <div class="modal-body">
                <div id="currency-validation-warning" class="info-box info-box-warning" style="display: none;">
                    <span class="material-symbols-rounded">warning</span>
                    <div>
                        <strong>Unbekannte Währung</strong>
                        <p>Diese Währung wird von Stripe nicht unterstützt.</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Code *</label>
                        <input type="text" name="code" class="form-input" placeholder="z.B. EUR" required maxlength="3"
                            oninput="locManager.validateCurrencyCode(this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-input" placeholder="z.B. Euro" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Symbol *</label>
                        <input type="text" name="symbol" class="form-input" placeholder="z.B. €" required maxlength="5">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Wechselkurs</label>
                        <input type="number" name="exchange_rate" class="form-input" value="1.00" step="0.0001" min="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Symbol-Position</label>
                        <select name="symbol_position" class="form-select">
                            <option value="before">Vor Betrag (€100)</option>
                            <option value="after">Nach Betrag (100€)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dezimalstellen</label>
                        <input type="number" name="decimal_places" class="form-input" value="2" min="0" max="4">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="is_default">
                        <span>Als Standard setzen</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-close-modal>Abbrechen</button>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Card Subtitle for Exchange Rates Info */
    .card-subtitle {
        padding: 10px 24px;
        background: rgba(99, 102, 241, 0.08);
        border-bottom: 1px solid var(--border-color);
        font-size: 0.85em;
        color: var(--text-muted);
    }

    /* Spinning Animation for Loading States */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .spinning {
        animation: spin 1s linear infinite;
    }

    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Modal System */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .modal.open {
        display: flex;
    }

    .modal-content {
        background: var(--card-bg, #1a1a2e);
        border-radius: 16px;
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
        animation: modalSlideIn 0.2s ease-out;
    }

    .modal-lg {
        max-width: 640px;
    }

    .modal-sm {
        max-width: 400px;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.1em;
    }

    .modal-header-danger {
        background: rgba(239, 68, 68, 0.1);
        gap: 12px;
        justify-content: flex-start;
    }

    .modal-header-danger .material-symbols-rounded {
        color: #ef4444;
        font-size: 28px;
    }

    .modal-header-success {
        background: rgba(34, 197, 94, 0.1);
        gap: 12px;
        justify-content: flex-start;
    }

    .modal-header-success .material-symbols-rounded {
        color: #22c55e;
        font-size: 28px;
    }

    .modal-header-info {
        background: rgba(99, 102, 241, 0.1);
        gap: 12px;
        justify-content: flex-start;
    }

    .modal-header-info .material-symbols-rounded {
        color: #6366f1;
        font-size: 28px;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
    }

    /* Page Styles */
    .card-header-left {
        display: flex;
        align-items: baseline;
        gap: 12px;
    }

    .currency-badge {
        background: rgba(99, 102, 241, 0.15);
        color: var(--primary-color);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.85em;
        font-weight: 500;
    }

    .currency-symbol-cell {
        font-size: 1.3em;
        font-weight: 600;
    }

    .text-sm {
        font-size: 0.85em;
    }

    /* Clickable Status Badges */
    .badge-clickable {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .badge-clickable:hover {
        transform: scale(1.05);
        filter: brightness(1.1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .badge-secondary {
        background: rgba(107, 114, 128, 0.2);
        color: #9ca3af;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--surface-color);
        border-radius: 8px;
        padding: 8px 14px;
        min-width: 200px;
    }

    .search-box input {
        background: transparent;
        border: none;
        color: inherit;
        width: 100%;
        outline: none;
    }

    .search-box .material-symbols-rounded {
        color: var(--text-muted);
        font-size: 20px;
    }

    .info-box {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .info-box .material-symbols-rounded {
        font-size: 24px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .info-box strong {
        display: block;
        margin-bottom: 4px;
    }

    .info-box p {
        margin: 0;
        font-size: 0.9em;
        opacity: 0.9;
    }

    .info-box-primary {
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .info-box-primary .material-symbols-rounded {
        color: #6366f1;
    }

    .info-box-warning {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .info-box-warning .material-symbols-rounded {
        color: #f59e0b;
    }

    /* Minimal Info Box */
    .info-box-minimal {
        padding: 10px 16px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .info-box-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-box-minimal .material-symbols-rounded {
        font-size: 20px;
    }

    .info-box-minimal p,
    .info-box-minimal span {
        font-size: 0.9em;
        margin: 0;
    }

    .btn-icon-close {
        background: transparent;
        border: none;
        padding: 8px;
        cursor: pointer;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
        min-width: 32px;
        min-height: 32px;
    }

    .btn-icon-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-color);
    }

    .btn-icon-close .material-symbols-rounded {
        font-size: 20px;
    }

    .translation-toolbar {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .translation-input {
        width: 100%;
        background: transparent;
        border: 1px solid transparent;
        color: inherit;
        padding: 8px 12px;
        border-radius: 6px;
    }

    .translation-input:hover {
        background: var(--surface-color);
    }

    .translation-input:focus {
        outline: none;
        border-color: var(--primary-color);
        background: var(--surface-color);
    }

    .translation-input.is-custom {
        color: var(--success-color);
    }

    .loading-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
    }

    .spinning {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-hint {
        font-size: 0.8em;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .card-header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    /* Geo-Location Preview */
    .geo-preview {
        margin-top: 30px;
    }

    .geo-preview h4 {
        margin-bottom: 16px;
        font-size: 1em;
    }

    .geo-popup-preview {
        background: var(--surface-color);
        border-radius: 12px;
        padding: 20px;
        max-width: 400px;
    }

    .geo-popup-content {
        display: flex;
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }

    .geo-popup-icon {
        font-size: 48px;
        color: var(--primary-color);
    }

    .geo-popup-text p {
        margin: 4px 0;
    }

    .geo-popup-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    /* Form Switch Toggle */
    .form-switch {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
    }

    .form-switch input {
        display: none;
    }

    .switch-slider {
        width: 48px;
        height: 26px;
        background: var(--border-color);
        border-radius: 13px;
        position: relative;
        transition: background 0.3s;
    }

    .switch-slider::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        top: 3px;
        left: 3px;
        transition: transform 0.3s;
    }

    .form-switch input:checked+.switch-slider {
        background: var(--primary-color);
    }

    .form-switch input:checked+.switch-slider::after {
        transform: translateX(22px);
    }

    .switch-label {
        font-weight: 500;
    }

    /* Divider */
    .divider {
        border: none;
        border-top: 1px solid var(--border-color);
        margin: 24px 0;
    }

    /* Geo Popup Section */
    .geo-popup-section h4 {
        margin: 0 0 8px 0;
        font-size: 1.1em;
    }

    .popup-preview-card {
        background: var(--surface-color);
        border-radius: 12px;
        overflow: hidden;
        margin: 16px 0;
        max-width: 400px;
        border: 1px solid var(--border-color);
    }

    .popup-preview-header {
        background: var(--bg-color);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        font-size: 0.9em;
        border-bottom: 1px solid var(--border-color);
    }

    .popup-preview-header .material-symbols-rounded {
        font-size: 18px;
        color: var(--primary-color);
    }

    .popup-preview-body {
        padding: 20px;
        text-align: center;
    }

    .popup-preview-body p {
        margin: 0 0 16px 0;
    }

    .popup-preview-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
</style>

<script>
    class LocalizationManager {
        constructor() {
            this.shopId = 1;
            this.locales = <?= json_encode($locales) ?>;
            this.currencies = <?= json_encode($currencies) ?>;
            this.validLocales = <?= json_encode(array_keys($validLocales)) ?>;
            this.validCurrencies = <?= json_encode(array_keys($validCurrencies)) ?>;
            this.validLanguageCodes = <?= json_encode($validLanguageCodes) ?>;
            this.translations = [];
            this.deleteTarget = null;
            this.init();
        }

        init() {
            this.initTabs();
            this.initModals();
            this.checkInfoBox();
            this.loadGeoSettings();
        }

        checkInfoBox() {
            if (!localStorage.getItem('translationInfoClosed')) {
                const box = document.getElementById('translation-info-box');
                if (box) box.style.display = 'flex';
            }
        }

        closeInfoBox() {
            const box = document.getElementById('translation-info-box');
            if (box) {
                box.style.display = 'none';
                localStorage.setItem('translationInfoClosed', 'true');
            }
        }

        initTabs() {
            const savedTab = localStorage.getItem('localization-active-tab') || 'sprachen';

            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    const name = tab.dataset.tab;
                    this.switchTab(name);
                    localStorage.setItem('localization-active-tab', name);
                });
            });

            // Restore saved tab on load
            this.switchTab(savedTab);
        }

        switchTab(name) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`.tab[data-tab="${name}"]`)?.classList.add('active');
            document.querySelectorAll('[data-tab-content]').forEach(c => c.style.display = 'none');
            const content = document.querySelector(`[data-tab-content="${name}"]`);
            if (content) content.style.display = 'block';

            if (name === 'uebersetzungen' && this.translations.length === 0) {
                this.loadTranslations();
            }
        }

        initModals() {
            document.querySelectorAll('[data-close-modal]').forEach(btn => {
                btn.addEventListener('click', () => btn.closest('.modal')?.classList.remove('open'));
            });
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) modal.classList.remove('open');
                });
            });
        }

        // Table filtering
        filterTable(type) {
            let searchId;
            if (type === 'locales') searchId = 'locale-search';
            else if (type === 'currencies') searchId = 'currency-search';
            else if (type === 'countries') searchId = 'country-search';
            else return; const search = document.getElementById(searchId).value.toLowerCase();
            const tbody = document.getElementById(`${type}-tbody`);

            tbody.querySelectorAll('tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(search) ? '' : 'none';
            });
        }

        // Delete confirmation
        confirmDelete(type, id, name) {
            this.deleteTarget = { type, id };
            document.getElementById('delete-message').innerHTML = `Möchtest du <strong>${name}</strong> wirklich löschen?`;
            document.getElementById('delete-modal').classList.add('open');
        }

        // Reseed all locales from JSON
        async reseedLocales() {
            if (!confirm('Alle 133 Sprachen aus locales.json laden? Bestehende Sprachen werden aktualisiert.')) return;

            this.showToast('Lade alle Sprachen...', 'info');

            try {
                const res = await fetch('/admin/api/localization.php?action=reseed_locales', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `shop_id=${this.shopId}`
                });
                const json = await res.json();

                if (json.success) {
                    this.showToast(`${json.total} Sprachen geladen (${json.inserted} neu, ${json.updated} aktualisiert)`, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.showToast(json.error, 'error');
                }
            } catch (err) {
                this.showToast('Fehler beim Laden', 'error');
            }
        }

        // Seed translation keys
        async seedTranslationKeys(locale) {
            try {
                const res = await fetch('/admin/api/localization.php?action=seed_translations', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `shop_id=${this.shopId}&locale=${locale}`
                });
                const json = await res.json();
                return json;
            } catch (err) {
                console.error('Error seeding translations:', err);
                return { success: false };
            }
        }

        async executeDelete() {
            if (!this.deleteTarget) return;

            const { type, id } = this.deleteTarget;
            const action = type === 'locale' ? 'delete_locale' : 'delete_currency';

            try {
                const res = await fetch(`/admin/api/localization.php?action=${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&shop_id=${this.shopId}`
                });
                const json = await res.json();

                document.getElementById('delete-modal').classList.remove('open');

                if (json.success) {
                    this.showToast('Erfolgreich gelöscht', 'success');
                    document.querySelector(`tr[data-id="${id}"]`)?.remove();
                } else {
                    this.showToast(json.error, 'error');
                }
            } catch (err) {
                this.showToast('Fehler beim Löschen', 'error');
            }

            this.deleteTarget = null;
        }

        // Validation
        validateLocaleCode(code) {
            const warning = document.getElementById('locale-validation-warning');
            const langCode = code.split('_')[0]?.toLowerCase();

            if (code.length >= 2 && !this.validLanguageCodes.includes(langCode)) {
                warning.style.display = 'flex';
            } else {
                warning.style.display = 'none';
            }
        }

        validateCurrencyCode(code) {
            const warning = document.getElementById('currency-validation-warning');
            code = code.toUpperCase();

            if (code.length === 3 && !this.validCurrencies.includes(code)) {
                warning.style.display = 'flex';
            } else {
                warning.style.display = 'none';
            }
        }

        // Locale CRUD
        openLocaleModal(locale = null) {
            const modal = document.getElementById('locale-modal');
            const form = document.getElementById('locale-form');
            const title = document.getElementById('locale-modal-title');
            document.getElementById('locale-validation-warning').style.display = 'none';

            if (locale) {
                title.textContent = 'Sprache bearbeiten';
                form.elements.id.value = locale.id;
                form.elements.code.value = locale.code;
                form.elements.language_code.value = locale.language_code;
                form.elements.language_name.value = locale.language_name;
                form.elements.language_native.value = locale.language_native;
                form.elements.country_name.value = locale.country_name;
                form.elements.currency_code.value = locale.currency_code;
                form.elements.date_format.value = locale.date_format;
                form.elements.time_format.value = locale.time_format;
                form.elements.is_default.checked = locale.is_default == 1;
                form.elements.is_active.checked = locale.is_active == 1;
            } else {
                title.textContent = 'Neue Sprache';
                form.reset();
                form.elements.id.value = '';
                form.elements.is_active.checked = true;
            }
            modal.classList.add('open');
        }

        editLocale(id) {
            const locale = this.locales.find(l => l.id == id);
            if (locale) this.openLocaleModal(locale);
        }

        async saveLocale(e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);
            data.append('shop_id', this.shopId);
            data.append('is_active', form.elements.is_active.checked ? 1 : 0);
            data.append('is_default', form.elements.is_default.checked ? 1 : 0);

            try {
                const res = await fetch('/admin/api/localization.php?action=save_locale', {
                    method: 'POST',
                    body: new URLSearchParams(data)
                });
                const json = await res.json();
                if (json.success) {
                    this.showToast('Sprache gespeichert', 'success');
                    location.reload();
                } else {
                    this.showToast(json.error, 'error');
                }
            } catch (err) {
                this.showToast('Fehler beim Speichern', 'error');
            }
        }

        async toggleActive(type, id, active) {
            const action = type === 'locale' ? 'toggle_locale' : 'toggle_currency';
            const typeName = type === 'locale' ? 'Sprache' : 'Währung';
            try {
                const res = await fetch(`/admin/api/localization.php?action=${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&is_active=${active ? 1 : 0}&shop_id=${this.shopId}`
                });
                const json = await res.json();
                if (json.success) {
                    this.showToast(`${typeName} ${active ? 'aktiviert' : 'deaktiviert'}`, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    this.showToast(json.error || 'Fehler', 'error');
                }
            } catch (err) {
                this.showToast('Fehler beim Speichern', 'error');
                location.reload();
            }
        }

        openLocaleEditModal(id, name, active, isDefault) {
            this.prepareEditStatus('locale', id, name, active, isDefault);
        }

        openCurrencyEditModal(id, name, active, isDefault) {
            this.prepareEditStatus('currency', id, name, active, isDefault);
        }

        openCountryEditModal(code, name, active, isDefault) {
            // Countries now support both active and default status
            const isActive = Boolean(active === true || active === 1 || active === '1' || active === 'true');
            const isDefaultVal = Boolean(isDefault === true || isDefault === 1 || isDefault === '1' || isDefault === 'true');

            document.getElementById('edit-status-id').value = code;
            document.getElementById('edit-status-type').value = 'country';
            document.getElementById('edit-status-modal-title').textContent = `Land bearbeiten: ${name}`;

            const activeCheckbox = document.getElementById('edit-status-active');
            const defaultCheckbox = document.getElementById('edit-status-default');

            activeCheckbox.checked = isActive;
            defaultCheckbox.checked = isDefaultVal;

            // Reset visibility
            defaultCheckbox.parentElement.parentElement.parentElement.style.opacity = '1';
            defaultCheckbox.parentElement.parentElement.parentElement.title = '';

            if (isDefaultVal) {
                activeCheckbox.disabled = true;
                defaultCheckbox.disabled = true;
                activeCheckbox.parentElement.title = "Standard-Land muss aktiv sein";
                defaultCheckbox.parentElement.title = "Standard kann nicht direkt entfernt werden";
            } else {
                activeCheckbox.disabled = false;
                defaultCheckbox.disabled = false;
                activeCheckbox.parentElement.title = "";
                defaultCheckbox.parentElement.title = "";
            }

            document.getElementById('edit-status-modal').classList.add('open');
        }

        prepareEditStatus(type, id, name, active, isDefault) {
            // Ensure boolean values (PHP may pass 0/1 or strings)
            const isActive = Boolean(active === true || active === 1 || active === '1' || active === 'true');
            const isDefaultVal = Boolean(isDefault === true || isDefault === 1 || isDefault === '1' || isDefault === 'true');

            document.getElementById('edit-status-id').value = id;
            document.getElementById('edit-status-type').value = type;
            document.getElementById('edit-status-modal-title').textContent = `${type === 'locale' ? 'Sprache' : 'Währung'} bearbeiten: ${name}`;

            const activeCheckbox = document.getElementById('edit-status-active');
            const defaultCheckbox = document.getElementById('edit-status-default');
            const activeHint = document.getElementById('edit-status-active-hint');

            activeCheckbox.checked = isActive;
            defaultCheckbox.checked = isDefaultVal;

            // Reset default option visibility (may have been hidden by country edit)
            defaultCheckbox.parentElement.parentElement.parentElement.style.opacity = '1';
            defaultCheckbox.parentElement.parentElement.parentElement.title = '';

            if (isDefaultVal) {
                activeCheckbox.disabled = true;
                defaultCheckbox.disabled = true;
                activeCheckbox.parentElement.title = "Standard-Element muss aktiv sein";
                defaultCheckbox.parentElement.title = "Standard kann nicht direkt entfernt werden";
                activeHint.style.display = 'none';
            } else {
                activeCheckbox.disabled = false;
                defaultCheckbox.disabled = false;
                activeCheckbox.parentElement.title = "";
                defaultCheckbox.parentElement.title = "";
                activeHint.style.display = 'block';
            }

            const modal = document.getElementById('edit-status-modal');
            modal.classList.add('open');
        }

        async saveStatusEdit() {
            const id = document.getElementById('edit-status-id').value;
            const type = document.getElementById('edit-status-type').value;
            const wantActive = document.getElementById('edit-status-active').checked;
            const wantDefault = document.getElementById('edit-status-default').checked;

            // Handle country type - now persists to database
            if (type === 'country') {
                document.getElementById('edit-status-modal').classList.remove('open');

                try {
                    this.showToast('Speichere...', 'info');
                    const res = await fetch('/admin/api/localization.php?action=save_country', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `code=${id}&is_active=${wantActive ? 1 : 0}&is_default=${wantDefault ? 1 : 0}&shop_id=${this.shopId}`
                    });
                    const json = await res.json();

                    if (json.success) {
                        if (wantDefault) {
                            this.showToast('Land als Standard gesetzt', 'success');
                        } else if (wantActive) {
                            this.showToast('Land aktiviert', 'success');
                        } else {
                            this.showToast('Land deaktiviert', 'success');
                        }
                        // Reload to show correct data from DB
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        this.showToast(json.error || 'Fehler beim Speichern', 'error');
                    }
                } catch (err) {
                    console.error('Error saving country:', err);
                    this.showToast('Fehler beim Speichern', 'error');
                }
                return;
            }

            const list = type === 'locale' ? this.locales : this.currencies;
            const item = list.find(x => String(x.id) === String(id));
            const typeName = type === 'locale' ? 'Sprache' : 'Währung';

            console.log('saveStatusEdit', { id, type, wantActive, wantDefault, item });

            if (!item) {
                console.error('Item not found for id:', id);
                this.showToast('Element nicht gefunden', 'error');
                return;
            }

            // Robust boolean conversion for PHP data
            const wasActive = item.is_active == 1 || item.is_active === '1' || item.is_active === true;
            const wasDefault = item.is_default == 1 || item.is_default === '1' || item.is_default === true;

            console.log('Status comparison', { wasActive, wantActive, wasDefault, wantDefault });

            // VALIDATION RULE 1: Standard-Element kann nicht deaktiviert werden
            if (wasDefault && !wantActive) {
                this.showToast(`Standard-${typeName} kann nicht deaktiviert werden. Setze zuerst eine andere ${typeName} als Standard.`, 'error');
                return;
            }

            // VALIDATION RULE 2: Deaktiviertes Element kann nicht als Standard gesetzt werden
            if (wantDefault && !wantActive && !wasActive) {
                this.showToast(`${typeName} muss zuerst aktiviert werden, bevor sie als Standard gesetzt werden kann.`, 'error');
                return;
            }

            // VALIDATION RULE 3: Mindestens ein Element muss aktiv bleiben
            if (!wantActive && wasActive) {
                const activeCount = list.filter(x => x.is_active == 1 || x.is_active === '1' || x.is_active === true).length;
                if (activeCount <= 1) {
                    this.showToast(`Es muss mindestens eine ${typeName} aktiv bleiben!`, 'error');
                    return;
                }
            }

            // Close modal first
            document.getElementById('edit-status-modal').classList.remove('open');

            // Case 1: Set as Default (will also activate if not already)
            if (wantDefault && !wasDefault) {
                const action = type === 'locale' ? 'set_default_locale' : 'set_default_currency';
                const body = type === 'locale' ? `locale_code=${item.code}` : `id=${id}`;

                try {
                    this.showToast('Speichere...', 'info');
                    const res = await fetch(`/admin/api/localization.php?action=${action}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `${body}&shop_id=${this.shopId}`
                    });
                    const json = await res.json();
                    console.log('Set default response:', json);
                    if (json.success) {
                        this.showToast(`${typeName} als Standard gesetzt`, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        this.showToast(json.error || 'Fehler beim Speichern', 'error');
                    }
                } catch (err) {
                    console.error('Error setting default:', err);
                    this.showToast('Fehler beim Speichern', 'error');
                }
                return;
            }

            // Case 2: Toggle Active Status
            if (wantActive !== wasActive) {
                console.log('Toggling active status to:', wantActive);
                await this.toggleActive(type, id, wantActive);
                return;
            }

            // No changes detected
            this.showToast('Keine Änderungen vorgenommen', 'info');
        }

        // Currency CRUD
        openCurrencyModal(curr = null) {
            const modal = document.getElementById('currency-modal');
            const form = document.getElementById('currency-form');
            const title = document.getElementById('currency-modal-title');
            document.getElementById('currency-validation-warning').style.display = 'none';

            if (curr) {
                title.textContent = 'Währung bearbeiten';
                form.elements.id.value = curr.id;
                form.elements.code.value = curr.code;
                form.elements.name.value = curr.name;
                form.elements.symbol.value = curr.symbol;
                form.elements.exchange_rate.value = curr.exchange_rate;
                form.elements.symbol_position.value = curr.symbol_position;
                form.elements.decimal_places.value = curr.decimal_places;
                form.elements.is_default.checked = curr.is_default == 1;
            } else {
                title.textContent = 'Neue Währung';
                form.reset();
                form.elements.id.value = '';
            }
            modal.classList.add('open');
        }

        editCurrency(id) {
            const curr = this.currencies.find(c => c.id == id);
            if (curr) this.openCurrencyModal(curr);
        }

        async saveCurrency(e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);
            data.append('shop_id', this.shopId);
            data.append('is_default', form.elements.is_default.checked ? 1 : 0);

            try {
                const res = await fetch('/admin/api/localization.php?action=save_currency', {
                    method: 'POST',
                    body: new URLSearchParams(data)
                });
                const json = await res.json();
                if (json.success) {
                    this.showToast('Währung gespeichert', 'success');
                    location.reload();
                } else {
                    this.showToast(json.error, 'error');
                }
            } catch (err) {
                this.showToast('Fehler beim Speichern', 'error');
            }
        }


        // Translations
        async loadTranslations() {
            const locale = document.getElementById('translation-locale')?.value;
            const customOnly = document.getElementById('show-custom-only')?.checked;
            const tbody = document.getElementById('translations-tbody');

            tbody.innerHTML = '<tr><td colspan="3" class="loading-state"><span class="material-symbols-rounded spinning">progress_activity</span> Lade...</td></tr>';

            try {
                const params = new URLSearchParams({
                    action: 'get_translations',
                    shop_id: this.shopId,
                    locale: locale,
                    custom_only: customOnly ? '1' : '0'
                });
                const res = await fetch(`/admin/api/localization.php?${params}`);
                const json = await res.json();

                if (json.success) {
                    this.translations = json.translations;
                    this.renderTranslations();
                } else {
                    tbody.innerHTML = '<tr><td colspan="3" class="loading-state">Keine Übersetzungen. Klicke "Schlüssel Laden"</td></tr>';
                }
            } catch (err) {
                tbody.innerHTML = '<tr><td colspan="3" class="loading-state">Fehler beim Laden</td></tr>';
            }
        }

        renderTranslations() {
            const tbody = document.getElementById('translations-tbody');
            const search = (document.getElementById('translation-search')?.value || '').toLowerCase();

            const filtered = this.translations.filter(t =>
                !search || t.translation_key.toLowerCase().includes(search) || t.translation_value.toLowerCase().includes(search)
            );

            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="loading-state">Keine Übersetzungen gefunden. Klicke "Schlüssel Laden"</td></tr>';
                return;
            }

            tbody.innerHTML = filtered.map(t => `
            <tr data-translation-id="${t.id}">
                <td><code>${t.translation_group}.${t.translation_key}</code></td>
                <td class="translation-value-cell">${this.escapeHtml(t.translation_value)}</td>
                <td class="table-actions">
                    <button class="btn btn-sm btn-icon" onclick="locManager.editTranslation(${t.id}, '${this.escapeHtml(t.translation_group)}.${this.escapeHtml(t.translation_key)}', '${this.escapeHtml(t.translation_value).replace(/'/g, "\\'")}')">
                        <span class="material-symbols-rounded">chevron_right</span>
                    </button>
                </td>
            </tr>
        `).join('');
        }

        editTranslation(id, key, currentValue) {
            const modal = document.getElementById('prompt-modal');
            const input = document.getElementById('prompt-modal-input');
            const btn = document.getElementById('prompt-modal-btn');

            document.getElementById('prompt-modal-title').textContent = key;
            input.value = currentValue;

            const saveHandler = () => {
                const newValue = input.value;
                if (newValue !== currentValue) {
                    this.saveTranslation(id, newValue);
                    // Update table cell
                    const row = document.querySelector(`tr[data-translation-id="${id}"]`);
                    if (row) {
                        row.querySelector('.translation-value-cell').textContent = newValue;
                    }
                }
                modal.classList.remove('open');
                btn.removeEventListener('click', saveHandler);
            };

            btn.onclick = saveHandler;
            modal.classList.add('open');
        }

        filterTranslations() {
            this.renderTranslations();
        }

        async saveTranslation(id, value) {
            try {
                await fetch('/admin/api/localization.php?action=save_translation', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&value=${encodeURIComponent(value)}&shop_id=${this.shopId}`
                });
                this.showToast('Übersetzung gespeichert', 'success');
            } catch (err) {
                console.error(err);
            }
        }

        // Seed all translation keys
        async seedAllTranslationKeys() {
            const locale = document.getElementById('translation-locale')?.value;
            if (!locale) {
                this.showToast('Bitte Sprache auswählen', 'error');
                return;
            }

            this.confirmAction('Alle 200 Standard-Texte laden? Bestehende werden nicht überschrieben.', async () => {
                this.showToast('Lade alle Schlüssel...', 'info');

                try {
                    const res = await fetch('/admin/api/localization.php?action=seed_translations', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `shop_id=${this.shopId}&locale=${locale}`
                    });
                    const json = await res.json();

                    if (json.success) {
                        this.showToast(`${json.inserted} neue Schlüssel geladen`, 'success');
                        this.loadTranslations();
                    } else {
                        this.showToast(json.error || 'Fehler', 'error');
                    }
                } catch (err) {
                    this.showToast('Fehler beim Laden', 'error');
                }
            });
        }

        async refreshTranslations() {
            const locale = document.getElementById('translation-locale')?.value;
            this.confirmAction('Automatische Übersetzungen neu generieren? Manuelle Anpassungen bleiben erhalten.', async () => {
                this.showToast('Übersetze...', 'info');

                try {
                    const res = await fetch('/admin/api/localization.php?action=auto_translate', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `locale_code=${locale}&shop_id=${this.shopId}`
                    });
                    const json = await res.json();
                    this.showToast(`${json.translated || 0} Übersetzungen aktualisiert`, 'success');
                    this.loadTranslations();
                } catch (err) {
                    this.showToast('Fehler', 'error');
                }
            });
        }

        confirmAction(message, callback) {
            const modal = document.getElementById('confirm-modal');
            const text = document.getElementById('confirm-modal-text');
            const btn = document.getElementById('confirm-modal-btn');

            text.textContent = message;

            const confirmHandler = () => {
                callback();
                modal.classList.remove('open');
                btn.removeEventListener('click', confirmHandler);
            };

            btn.onclick = confirmHandler;
            modal.classList.add('open');
        }

        toggleGeoSettings() {
            const enabled = document.getElementById('geo-enabled').checked;
            const container = document.getElementById('geo-settings-container');
            container.style.display = enabled ? 'block' : 'none';
            // Auto-save when toggling
            this.saveGeoSettings();
        }

        toggleApiKeyField() {
            const service = document.getElementById('geo-service').value;
            document.getElementById('geo-apikey-group').style.display = service === 'ipapi' ? 'block' : 'none';
        }

        loadGeoSettings() {
            const settings = JSON.parse(localStorage.getItem('geo-settings') || '{}');

            // Apply saved settings
            const enabledCheckbox = document.getElementById('geo-enabled');
            const serviceSelect = document.getElementById('geo-service');
            const apiKeyInput = document.getElementById('geo-apikey');

            if (settings.enabled !== undefined) {
                enabledCheckbox.checked = settings.enabled;
            }
            if (settings.service) {
                serviceSelect.value = settings.service;
            }
            if (settings.apiKey) {
                apiKeyInput.value = settings.apiKey;
            }

            // Apply visibility
            this.toggleApiKeyField();
            document.getElementById('geo-settings-container').style.display =
                enabledCheckbox.checked ? 'block' : 'none';
        }

        saveGeoSettings() {
            const enabled = document.getElementById('geo-enabled').checked;
            const service = document.getElementById('geo-service').value;
            const apiKey = document.getElementById('geo-apikey').value;

            // Toggle API key field visibility
            this.toggleApiKeyField();

            // Save to localStorage
            localStorage.setItem('geo-settings', JSON.stringify({
                enabled,
                service,
                apiKey
            }));

            this.showToast('Einstellungen gespeichert', 'success');
        }

        showToast(message, type = 'info') {
            const modal = document.getElementById('message-modal');
            const text = document.getElementById('message-modal-text');
            const header = document.getElementById('message-modal-header');
            const icon = document.getElementById('message-modal-icon');
            const title = document.getElementById('message-modal-title');

            text.textContent = message;

            // Reset classes
            header.className = 'modal-header';

            if (type === 'success') {
                header.classList.add('modal-header-success');
                icon.textContent = 'check_circle';
                title.textContent = 'Erfolg';
            } else if (type === 'error') {
                header.classList.add('modal-header-danger');
                icon.textContent = 'error';
                title.textContent = 'Fehler';
            } else {
                header.classList.add('modal-header-info');
                icon.textContent = 'info';
                title.textContent = 'Info';
            }

            modal.classList.add('open');
        }


        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        // Exchange Rates - Using ExchangeRate-API (supports ALL currencies including exotic ones)
        // open.er-api.com is free and provides rates for 160+ currencies
        async fetchExchangeRates(silent = false) {
            const btn = document.getElementById('fetch-rates-btn');
            const originalText = btn ? btn.innerHTML : '';

            if (btn && !silent) {
                btn.innerHTML = '<span class="material-symbols-rounded spinning">progress_activity</span> Lädt...';
                btn.disabled = true;
            }

            try {
                // Step 1: Get shop's base currency (the one set as default)
                const shopBaseCurrency = this.currencies.find(c => c.is_default == 1)?.code || 'USD';

                // Step 2: Fetch rates from ExchangeRate-API with shop's base currency
                // This API supports ALL currencies as base currency
                const apiUrl = `https://open.er-api.com/v6/latest/${shopBaseCurrency}`;
                const apiRes = await fetch(apiUrl);

                if (!apiRes.ok) {
                    throw new Error('Exchange Rate API nicht erreichbar');
                }

                const apiData = await apiRes.json();

                if (apiData.result !== 'success' || !apiData.rates) {
                    throw new Error('Keine Kursdaten erhalten');
                }

                // Step 3: The rates are already relative to our base currency
                const finalRates = apiData.rates;
                const finalBase = apiData.base_code;

                console.log(`Exchange rates based on ${finalBase}:`, Object.keys(finalRates).length, 'currencies');

                // Step 4: Send rates to our PHP backend to save
                const saveRes = await fetch('/admin/api/exchange_rates.php?action=save_rates', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `shop_id=${this.shopId}&base=${finalBase}&rates=${encodeURIComponent(JSON.stringify(finalRates))}`
                });
                const saveJson = await saveRes.json();

                if (saveJson.success) {
                    if (!silent) {
                        this.showToast(`${saveJson.updated} Wechselkurse aktualisiert (Quelle: ExchangeRate-API)`, 'success');
                    }
                    // Update last update time
                    document.getElementById('rates-last-update').textContent =
                        'Zuletzt aktualisiert: ' + this.formatDate(saveJson.timestamp);
                    // Reload page after short delay to show fresh data
                    if (!silent) {
                        setTimeout(() => location.reload(), 1500);
                    }
                } else {
                    throw new Error(saveJson.error || 'Fehler beim Speichern');
                }
            } catch (err) {
                if (!silent) {
                    this.showToast('Fehler: ' + err.message, 'error');
                }
                console.warn('Exchange rate fetch failed:', err);
            } finally {
                if (btn && !silent) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }
        }


        updateRatesDisplay(rates) {
            if (!rates) return;
            Object.entries(rates).forEach(([code, rate]) => {
                const row = document.querySelector(`#currencies-tbody tr td code:contains('${code}')`);
                // We'll reload the page anyway, just visual feedback
            });
        }

        async loadLastUpdate() {
            try {
                const res = await fetch('/admin/api/exchange_rates.php?action=get_last_update&shop_id=' + this.shopId);
                const json = await res.json();
                const el = document.getElementById('rates-last-update');
                const btn = document.getElementById('fetch-rates-btn');
                const infoEl = document.getElementById('exchange-rates-info');

                if (json.success && json.last_update) {
                    const lastUpdate = new Date(json.last_update);
                    const now = new Date();
                    const diffMs = now - lastUpdate;
                    const diffHours = diffMs / (1000 * 60 * 60);
                    const diffMinutes = Math.floor(diffMs / (1000 * 60));

                    // Determine freshness status
                    let statusIcon, statusClass, statusText;

                    if (diffHours < 1) {
                        // Fresh - updated within last hour
                        statusIcon = 'check_circle';
                        statusClass = 'color: var(--success-color)';
                        statusText = `Aktuell (vor ${diffMinutes} Min.)`;

                        // Rate limit: Disable button for 1 hour
                        if (btn) {
                            btn.disabled = true;
                            btn.title = `Nächste Aktualisierung möglich in ${60 - diffMinutes} Minuten (Rate Limit: 1x pro Stunde)`;
                            btn.innerHTML = '<span class="material-symbols-rounded">schedule</span> Kurse aktuell';
                        }
                    } else if (diffHours < 24) {
                        // Stale - updated within last 24 hours
                        statusIcon = 'schedule';
                        statusClass = 'color: var(--warning-color)';
                        const hours = Math.floor(diffHours);
                        statusText = `Vor ${hours} Stunde${hours > 1 ? 'n' : ''} aktualisiert`;

                        if (btn) {
                            btn.disabled = false;
                            btn.title = 'Wechselkurse jetzt aktualisieren';
                            btn.innerHTML = '<span class="material-symbols-rounded">currency_exchange</span> Kurse aktualisieren';
                        }
                    } else {
                        // Old - more than 24 hours
                        statusIcon = 'warning';
                        statusClass = 'color: var(--danger-color)';
                        const days = Math.floor(diffHours / 24);
                        statusText = `Veraltet! Vor ${days} Tag${days > 1 ? 'en' : ''} aktualisiert`;

                        if (btn) {
                            btn.disabled = false;
                            btn.title = 'Dringend aktualisieren empfohlen!';
                            btn.innerHTML = '<span class="material-symbols-rounded">priority_high</span> Kurse aktualisieren!';
                        }
                    }

                    el.innerHTML = `<span class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle; ${statusClass}">${statusIcon}</span> ${statusText}`;

                } else {
                    el.innerHTML = '<span class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle; color: var(--danger-color)">error</span> Noch nie aktualisiert - klicke "Kurse aktualisieren"';
                    if (btn) {
                        btn.disabled = false;
                        btn.title = 'Wechselkurse erstmals laden';
                    }
                }
            } catch (err) {
                document.getElementById('rates-last-update').innerHTML = '<span class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle;">cloud_off</span> Status nicht verfügbar';
            }
        }


        formatDate(dateStr) {
            if (!dateStr) return 'Unbekannt';
            const d = new Date(dateStr);
            return d.toLocaleDateString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        async checkLibraryCompleteness() {
            // Auto-seed if library is empty or incomplete
            if (this.locales.length < 20) {
                await fetch('/admin/api/localization.php?action=reseed_locales&shop_id=' + this.shopId);
                location.reload();
            } else if (this.currencies.length < 20) {
                await fetch('/admin/api/localization.php?action=reseed_currencies&shop_id=' + this.shopId);
                location.reload();
            }
        }
    }

    const locManager = new LocalizationManager();
    // Load exchange rate timestamp
    locManager.loadLastUpdate();
    // Note: Exchange rates are now MANUAL only (click "Kurse aktualisieren" button)
    // This prevents connection errors on page load
</script>