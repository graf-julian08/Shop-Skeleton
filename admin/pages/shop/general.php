<?php
/** 
 * Shop - Allgemeine Einstellungen
 * CRUD: Read + Update (single-record entity)
 */

// Check permission
Auth::requirePermission('shop.settings');

// Handle form submission
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = ShopController::handleGeneralSettings();
}

// Load shop data
$shop = Shop::getDefault();
if (!$shop) {
    echo '<div class="alert alert-error">Shop nicht gefunden. Bitte führen Sie den Seed-Befehl aus.</div>';
    return;
}

// Get dropdown options
$timezones = Shop::getTimezones();
$dateFormats = Shop::getDateFormats();
$weightUnits = Shop::getWeightUnits();
?>

<form method="POST" action="">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Allgemeine Einstellungen</h1>
            <p class="page-subtitle">Grundlegende Shop-Konfiguration</p>
        </div>
        <div class="page-header-actions">
            <button type="submit" class="btn btn-primary">
                <span class="material-symbols-rounded">save</span> Speichern
            </button>
        </div>
    </div>

    <?php if ($result): ?>
        <?php if ($result['success']): ?>
            <div class="alert alert-success">
                <span class="material-symbols-rounded">check_circle</span>
                <?= htmlspecialchars($result['message']) ?>
                <button type="button" class="alert-close"><span class="material-symbols-rounded">close</span></button>
            </div>
        <?php else: ?>
            <div class="alert alert-error">
                <span class="material-symbols-rounded">error</span>
                <?= htmlspecialchars($result['message']) ?>
                <button type="button" class="alert-close"><span class="material-symbols-rounded">close</span></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3>Shop-Informationen</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Shop-Name <span style="color:var(--error)">*</span></label>
                    <input type="text" id="name" name="name"
                        class="form-input <?= isset($result['errors']['name']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($shop['name']) ?>" required>
                    <?php if (isset($result['errors']['name'])): ?>
                        <small class="form-error"><?= htmlspecialchars($result['errors']['name']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="domain">Shop-URL</label>
                    <input type="text" id="domain" name="domain" class="form-input"
                        value="<?= htmlspecialchars($shop['domain'] ?? 'http://localhost:8085') ?>"
                        placeholder="https://mein-shop.ch">
                    <small style="color:var(--text-muted);font-size:11px;display:block;margin-top:4px;">Diese URL wird
                        für Sitemap, E-Mails und Links verwendet</small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="email">E-Mail-Adresse</label>
                    <input type="email" id="email" name="email" class="form-input"
                        value="<?= htmlspecialchars($shop['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Telefon</label>
                    <input type="text" id="phone" name="phone" class="form-input"
                        value="<?= htmlspecialchars($shop['phone'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Shop-Beschreibung</label>
                <textarea id="description" name="description"
                    class="form-textarea"><?= htmlspecialchars($shop['description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Regionale Einstellungen</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="default_currency">Standardwährung</label>
                    <select id="default_currency" name="default_currency" class="form-select">
                        <option value="EUR" <?= ($shop['default_currency'] ?? 'EUR') === 'EUR' ? 'selected' : '' ?>>EUR -
                            Euro (€)</option>
                        <option value="USD" <?= ($shop['default_currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD - US
                            Dollar ($)</option>
                        <option value="CHF" <?= ($shop['default_currency'] ?? '') === 'CHF' ? 'selected' : '' ?>>CHF -
                            Schweizer Franken</option>
                        <option value="GBP" <?= ($shop['default_currency'] ?? '') === 'GBP' ? 'selected' : '' ?>>GBP -
                            Britisches Pfund (£)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="timezone">Zeitzone</label>
                    <select id="timezone" name="timezone" class="form-select">
                        <?php foreach ($timezones as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($shop['timezone'] ?? 'Europe/Berlin') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="date_format">Datumsformat</label>
                    <select id="date_format" name="date_format" class="form-select">
                        <?php foreach ($dateFormats as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($shop['date_format'] ?? 'DD.MM.YYYY') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="weight_unit">Gewichtseinheit</label>
                    <select id="weight_unit" name="weight_unit" class="form-select">
                        <?php foreach ($weightUnits as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($shop['weight_unit'] ?? 'kg') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Shop-Status</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="is_active" value="1" <?= ($shop['is_active'] ?? 1) ? 'checked' : '' ?>>
                    <span>Shop ist aktiv und für Kunden sichtbar</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="maintenance_mode" value="1" <?= ($shop['maintenance_mode'] ?? 0) ? 'checked' : '' ?>>
                    <span>Wartungsmodus aktivieren</span>
                </label>
                <p class="form-hint">Im Wartungsmodus sehen Besucher eine Wartungsseite</p>
            </div>
        </div>
        <div class="card-footer">
            <a href="?page=dashboard" class="btn">Abbrechen</a>
            <button type="submit" class="btn btn-primary">Änderungen speichern</button>
        </div>
    </div>
</form>