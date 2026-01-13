<?php 
/** 
 * Shop - Design & Themes
 * CRUD: Read + Update (single-record entity linked to shop)
 */

// Check permission
Auth::requirePermission('shop.design');

// Handle form submission
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = ShopController::handleDesignSettings();
}

// Load design data
$design = ShopDesign::getDefault();
if (!$design) {
    echo '<div class="alert alert-error">Design-Einstellungen nicht gefunden. Bitte führen Sie den Seed-Befehl aus.</div>';
    return;
}

// Get dropdown options
$fonts = ShopDesign::getFonts();
$headerStyles = ShopDesign::getHeaderStyles();
$footerStyles = ShopDesign::getFooterStyles();
?>

<form method="POST" action="" enctype="multipart/form-data">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Design</h1>
            <p class="page-subtitle">Theme & visuelle Anpassungen</p>
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

    <div class="alert alert-info">
        <span class="material-symbols-rounded">info</span>
        <div class="alert-content">Änderungen werden erst nach dem Speichern im Live-Shop sichtbar.</div>
        <button type="button" class="alert-close"><span class="material-symbols-rounded">close</span></button>
    </div>

    <div class="card">
        <div class="card-header"><h3>Farben</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="color_primary">Primärfarbe</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="color" 
                               id="color_primary_picker"
                               value="<?= htmlspecialchars($design['color_primary'] ?? '#7c3aed') ?>" 
                               style="width:48px;height:38px;border:none;cursor:pointer;"
                               onchange="document.getElementById('color_primary').value = this.value">
                        <input type="text" 
                               id="color_primary" 
                               name="color_primary" 
                               class="form-input <?= isset($result['errors']['color_primary']) ? 'is-invalid' : '' ?>" 
                               value="<?= htmlspecialchars($design['color_primary'] ?? '#7c3aed') ?>" 
                               style="width:120px;"
                               onchange="document.getElementById('color_primary_picker').value = this.value">
                    </div>
                    <?php if (isset($result['errors']['color_primary'])): ?>
                        <small class="form-error"><?= htmlspecialchars($result['errors']['color_primary']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="color_secondary">Sekundärfarbe</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="color" 
                               id="color_secondary_picker"
                               value="<?= htmlspecialchars($design['color_secondary'] ?? '#1a1a1a') ?>" 
                               style="width:48px;height:38px;border:none;cursor:pointer;"
                               onchange="document.getElementById('color_secondary').value = this.value">
                        <input type="text" 
                               id="color_secondary" 
                               name="color_secondary" 
                               class="form-input <?= isset($result['errors']['color_secondary']) ? 'is-invalid' : '' ?>" 
                               value="<?= htmlspecialchars($design['color_secondary'] ?? '#1a1a1a') ?>" 
                               style="width:120px;"
                               onchange="document.getElementById('color_secondary_picker').value = this.value">
                    </div>
                    <?php if (isset($result['errors']['color_secondary'])): ?>
                        <small class="form-error"><?= htmlspecialchars($result['errors']['color_secondary']) ?></small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="color_accent">Akzentfarbe</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="color" 
                               id="color_accent_picker"
                               value="<?= htmlspecialchars($design['color_accent'] ?? '#22c55e') ?>" 
                               style="width:48px;height:38px;border:none;cursor:pointer;"
                               onchange="document.getElementById('color_accent').value = this.value">
                        <input type="text" 
                               id="color_accent" 
                               name="color_accent" 
                               class="form-input <?= isset($result['errors']['color_accent']) ? 'is-invalid' : '' ?>" 
                               value="<?= htmlspecialchars($design['color_accent'] ?? '#22c55e') ?>" 
                               style="width:120px;"
                               onchange="document.getElementById('color_accent_picker').value = this.value">
                    </div>
                    <?php if (isset($result['errors']['color_accent'])): ?>
                        <small class="form-error"><?= htmlspecialchars($result['errors']['color_accent']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label" for="color_text">Textfarbe</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="color" 
                               id="color_text_picker"
                               value="<?= htmlspecialchars($design['color_text'] ?? '#ffffff') ?>" 
                               style="width:48px;height:38px;border:none;cursor:pointer;"
                               onchange="document.getElementById('color_text').value = this.value">
                        <input type="text" 
                               id="color_text" 
                               name="color_text" 
                               class="form-input <?= isset($result['errors']['color_text']) ? 'is-invalid' : '' ?>" 
                               value="<?= htmlspecialchars($design['color_text'] ?? '#ffffff') ?>" 
                               style="width:120px;"
                               onchange="document.getElementById('color_text_picker').value = this.value">
                    </div>
                    <?php if (isset($result['errors']['color_text'])): ?>
                        <small class="form-error"><?= htmlspecialchars($result['errors']['color_text']) ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Logo & Favicon</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Logo</label>
                    <?php if (!empty($design['logo_path'])): ?>
                        <div style="margin-bottom:12px;padding:12px;background:var(--bg-lighter);border-radius:8px;display:flex;align-items:center;gap:12px;">
                            <img src="<?= htmlspecialchars($design['logo_path']) ?>" alt="Logo" style="max-height:60px;max-width:200px;object-fit:contain;">
                            <span style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars(basename($design['logo_path'])) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="upload-zone <?= isset($result['errors']['logo_file']) ? 'is-invalid' : '' ?>" style="cursor:pointer;" onclick="document.getElementById('logo_file').click()">
                        <span class="material-symbols-rounded" style="color:var(--text-muted);font-size:32px;">cloud_upload</span>
                        <p style="margin:8px 0 4px;">Logo hier ablegen oder klicken zum Hochladen</p>
                        <p style="font-size:12px;color:var(--text-muted);">PNG, JPG, SVG, WebP (max. 5MB)</p>
                        <input type="file" 
                               id="logo_file" 
                               name="logo_file" 
                               accept="image/png,image/jpeg,image/svg+xml,image/webp"
                               style="display:none;"
                               onchange="document.getElementById('logo_name').textContent = this.files[0]?.name || ''">
                        <span id="logo_name" style="font-size:12px;color:var(--accent);margin-top:8px;display:block;"></span>
                    </div>
                    <?php if (isset($result['errors']['logo_file'])): ?>
                        <small class="form-error"><?= htmlspecialchars($result['errors']['logo_file']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label">Favicon</label>
                    <?php if (!empty($design['favicon_path'])): ?>
                        <div style="margin-bottom:12px;padding:12px;background:var(--bg-lighter);border-radius:8px;display:flex;align-items:center;gap:12px;">
                            <img src="<?= htmlspecialchars($design['favicon_path']) ?>" alt="Favicon" style="max-height:32px;max-width:32px;">
                            <span style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars(basename($design['favicon_path'])) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="upload-zone <?= isset($result['errors']['favicon_file']) ? 'is-invalid' : '' ?>" style="cursor:pointer;" onclick="document.getElementById('favicon_file').click()">
                        <span class="material-symbols-rounded" style="color:var(--text-muted);font-size:32px;">cloud_upload</span>
                        <p style="margin:8px 0 4px;">Favicon hochladen</p>
                        <p style="font-size:12px;color:var(--text-muted);">ICO, PNG 32x32 (max. 1MB)</p>
                        <input type="file" 
                               id="favicon_file" 
                               name="favicon_file" 
                               accept="image/x-icon,image/png,image/vnd.microsoft.icon"
                               style="display:none;"
                               onchange="document.getElementById('favicon_name').textContent = this.files[0]?.name || ''">
                        <span id="favicon_name" style="font-size:12px;color:var(--accent);margin-top:8px;display:block;"></span>
                    </div>
                    <?php if (isset($result['errors']['favicon_file'])): ?>
                        <small class="form-error"><?= htmlspecialchars($result['errors']['favicon_file']) ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Typografie</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="font_heading">Überschriften-Schriftart</label>
                    <select id="font_heading" name="font_heading" class="form-select">
                        <?php foreach ($fonts as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($design['font_heading'] ?? 'Inter') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="font_body">Fließtext-Schriftart</label>
                    <select id="font_body" name="font_body" class="form-select">
                        <?php foreach ($fonts as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($design['font_body'] ?? 'Inter') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Layout</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="header_style">Header-Stil</label>
                    <select id="header_style" name="header_style" class="form-select">
                        <?php foreach ($headerStyles as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($design['header_style'] ?? 'default') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="footer_style">Footer-Stil</label>
                    <select id="footer_style" name="footer_style" class="form-select">
                        <?php foreach ($footerStyles as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= ($design['footer_style'] ?? 'columns') === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="?page=dashboard" class="btn">Abbrechen</a>
            <button type="submit" class="btn btn-primary">Speichern</button>
        </div>
    </div>
</form>
