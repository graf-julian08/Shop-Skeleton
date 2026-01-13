<?php
/**
 * Navigation Settings Page
 * 
 * Global settings for navigation menu type, side menu style,
 * hamburger animation, and panel settings
 */

// Load settings from database
$navSettings = Database::fetch(
    "SELECT * FROM navigation_settings WHERE shop_id = ?",
    [1]
);

// Defaults if not found
if (!$navSettings) {
    $navSettings = [
        'menu_type' => 'header_links',
        'side_menu_style' => 'side_by_side',
        'hamburger_mode' => 'animated',
        'hamburger_animation' => 1,
        'hamburger_custom_icon_media_id' => null,
        'side_menu_direction' => 'left',
        'side_menu_width_min' => 400,
        'side_menu_width_max' => 600,
        'side_menu_width_percent' => 25,
        'side_menu_animation' => 'slide',
        'side_menu_animation_speed' => 300,
        'side_menu_backdrop' => 1,
        'side_menu_backdrop_color' => '#000000',
        'side_menu_backdrop_opacity' => 50
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_nav_settings'])) {
    $updateData = [
        'menu_type' => $_POST['menu_type'] ?? 'header_links',
        'side_menu_style' => $_POST['side_menu_style'] ?? 'side_by_side',
        'hamburger_mode' => $_POST['hamburger_mode'] ?? 'animated',
        'hamburger_animation' => ($_POST['hamburger_mode'] ?? 'animated') === 'animated' ? 1 : 0,
        'hamburger_custom_icon_media_id' => !empty($_POST['hamburger_custom_icon']) ? intval($_POST['hamburger_custom_icon']) : null,
        'side_menu_direction' => $_POST['side_menu_direction'] ?? 'left',
        'side_menu_width_min' => intval($_POST['side_menu_width_min'] ?? 400),
        'side_menu_width_max' => intval($_POST['side_menu_width_max'] ?? 600),
        'side_menu_width_percent' => intval($_POST['side_menu_width_percent'] ?? 25),
        'side_menu_animation' => $_POST['side_menu_animation'] ?? 'slide',
        'side_menu_animation_speed' => intval($_POST['side_menu_animation_speed'] ?? 300),
        'side_menu_backdrop' => isset($_POST['side_menu_backdrop']) ? 1 : 0,
        'side_menu_backdrop_color' => $_POST['side_menu_backdrop_color'] ?? '#000000',
        'side_menu_backdrop_opacity' => intval($_POST['side_menu_backdrop_opacity'] ?? 50)
    ];

    // Check if record exists
    $existing = Database::fetch("SELECT id FROM navigation_settings WHERE shop_id = 1");

    if ($existing) {
        Database::update('navigation_settings', $updateData, 'shop_id = ?', [1]);
    } else {
        $updateData['shop_id'] = 1;
        Database::insert('navigation_settings', $updateData);
    }

    // Reload
    $navSettings = Database::fetch("SELECT * FROM navigation_settings WHERE shop_id = ?", [1]);

    $successMessage = 'Einstellungen wurden gespeichert.';
}

// Get hamburger mode from data (handle legacy)
$hamburgerMode = $navSettings['hamburger_mode'] ?? ($navSettings['hamburger_animation'] ? 'animated' : 'none');
$backdropOpacity = $navSettings['side_menu_backdrop_opacity'] ?? 50;
?>

<div class="page-content">
    <div class="content-header">
        <div class="header-title">
            <h1>Navigation Einstellungen</h1>
            <span class="header-subtitle">Globale Einstellungen für das Hauptmenü</span>
        </div>
        <div class="header-actions">
            <a href="?page=shop/navigation" class="btn">
                <span class="material-symbols-rounded">arrow_back</span>
                Zurück zur Navigation
            </a>
        </div>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <span class="material-symbols-rounded">check_circle</span>
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="settings-form">
        <input type="hidden" name="save_nav_settings" value="1">

        <!-- Menu Type Section -->
        <div class="content-card">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-rounded">menu</span>
                    Menü-Typ
                </h2>
            </div>
            <div class="card-body">
                <p class="form-hint" style="margin-bottom: 16px;">
                    Wählen Sie, wie die Navigation im Shop angezeigt werden soll.
                </p>

                <div class="menu-type-selector">
                    <label
                        class="menu-type-option <?= $navSettings['menu_type'] === 'header_links' ? 'selected' : '' ?>">
                        <input type="radio" name="menu_type" value="header_links"
                            <?= $navSettings['menu_type'] === 'header_links' ? 'checked' : '' ?>
                        onchange="toggleMenuTypeOptions()">
                        <div class="option-content">
                            <div class="option-icon">
                                <span class="material-symbols-rounded">view_headline</span>
                            </div>
                            <div class="option-text">
                                <strong>Header-Links</strong>
                                <span>Links stehen direkt im Header (Prada, Ralph Lauren)</span>
                            </div>
                            <div class="option-preview header-links-preview">
                                <div class="preview-header">
                                    <span>LOGO</span>
                                    <div class="preview-links">
                                        <span>Damen</span>
                                        <span>Herren</span>
                                        <span>Kinder</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="menu-type-option <?= $navSettings['menu_type'] === 'side_menu' ? 'selected' : '' ?>">
                        <input type="radio" name="menu_type" value="side_menu"
                            <?= $navSettings['menu_type'] === 'side_menu' ? 'checked' : '' ?>
                        onchange="toggleMenuTypeOptions()">
                        <div class="option-content">
                            <div class="option-icon">
                                <span class="material-symbols-rounded">menu_open</span>
                            </div>
                            <div class="option-text">
                                <strong>Side-Menu (Hamburger)</strong>
                                <span>Hamburger-Icon öffnet Seitenmenü (Louis Vuitton, Gucci)</span>
                            </div>
                            <div class="option-preview side-menu-preview">
                                <div class="preview-header">
                                    <span class="preview-hamburger">☰</span>
                                    <span>LOGO</span>
                                </div>
                                <div class="preview-sidebar"></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Side Menu Options (shown only when side_menu is selected) -->
        <div id="side-menu-options" class="content-card"
            style="<?= $navSettings['menu_type'] !== 'side_menu' ? 'display:none;' : '' ?>">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-rounded">menu_open</span>
                    Side-Menu Einstellungen
                </h2>
            </div>
            <div class="card-body">

                <!-- Side Menu Style -->
                <div class="form-group">
                    <label class="form-label">Unter-Navigation Stil</label>
                    <p class="form-hint">Wie werden Unterkategorien angezeigt, wenn man auf eine Hauptkategorie geht?
                    </p>

                    <div class="side-style-selector">
                        <label
                            class="side-style-option <?= $navSettings['side_menu_style'] === 'side_by_side' ? 'selected' : '' ?>">
                            <input type="radio" name="side_menu_style" value="side_by_side"
                                <?= $navSettings['side_menu_style'] === 'side_by_side' ? 'checked' : '' ?>>
                            <div class="style-preview side-by-side">
                                <div class="panel panel-main">Haupt</div>
                                <div class="panel panel-sub">Sub</div>
                            </div>
                            <span class="style-name">Nebeneinander</span>
                            <span class="style-example">Louis Vuitton</span>
                        </label>

                        <label
                            class="side-style-option <?= $navSettings['side_menu_style'] === 'push_overlay' ? 'selected' : '' ?>">
                            <input type="radio" name="side_menu_style" value="push_overlay"
                                <?= $navSettings['side_menu_style'] === 'push_overlay' ? 'checked' : '' ?>>
                            <div class="style-preview push-overlay">
                                <div class="panel panel-main faded">Haupt</div>
                                <div class="panel panel-sub overlay">← Sub</div>
                            </div>
                            <span class="style-name">Übereinander (Push)</span>
                            <span class="style-example">Gucci</span>
                        </label>
                    </div>
                </div>

                <hr class="form-divider">

                <!-- Hamburger Icon Settings - 3 Big Divs -->
                <div class="form-group">
                    <label class="form-label">Hamburger-Icon Verhalten</label>
                    <p class="form-hint">Wählen Sie, wie das Menü-Icon im Header dargestellt wird.</p>

                    <div class="hamburger-mode-selector">
                        <label class="hamburger-mode-option <?= $hamburgerMode === 'animated' ? 'selected' : '' ?>">
                            <input type="radio" name="hamburger_mode" value="animated"
                                <?= $hamburgerMode === 'animated' ? 'checked' : '' ?>
                                onchange="toggleHamburgerMode()">
                            <div class="mode-preview">
                                <div class="hamburger-animated">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                            <div class="mode-text">
                                <strong>Animation zu X</strong>
                                <span>Hamburger animiert sich zum X-Symbol</span>
                            </div>
                        </label>

                        <label class="hamburger-mode-option <?= $hamburgerMode === 'none' ? 'selected' : '' ?>">
                            <input type="radio" name="hamburger_mode" value="none"
                                <?= $hamburgerMode === 'none' ? 'checked' : '' ?>
                                onchange="toggleHamburgerMode()">
                            <div class="mode-preview">
                                <div class="hamburger-static">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                            <div class="mode-text">
                                <strong>Keine Animation</strong>
                                <span>Einfaches statisches Hamburger-Icon</span>
                            </div>
                        </label>

                        <label class="hamburger-mode-option <?= $hamburgerMode === 'custom' ? 'selected' : '' ?>">
                            <input type="radio" name="hamburger_mode" value="custom"
                                <?= $hamburgerMode === 'custom' ? 'checked' : '' ?>
                                onchange="toggleHamburgerMode()">
                            <div class="mode-preview">
                                <div class="hamburger-custom">
                                    <span class="material-symbols-rounded">image</span>
                                </div>
                            </div>
                            <div class="mode-text">
                                <strong>Eigenes Icon / Logo</strong>
                                <span>Eigenes Bild oder SVG hochladen</span>
                            </div>
                        </label>
                    </div>

                    <!-- Custom Icon Upload (shown when 'custom' selected) -->
                    <div id="hamburger-custom-upload" class="form-row"
                        style="margin-top: 16px; <?= $hamburgerMode !== 'custom' ? 'display:none;' : '' ?>">
                        <div class="media-picker" data-field="hamburger_custom_icon" data-folder="icons"
                            data-media-id="<?= $navSettings['hamburger_custom_icon_media_id'] ?? '' ?>"></div>
                        <input type="hidden" name="hamburger_custom_icon"
                            value="<?= $navSettings['hamburger_custom_icon_media_id'] ?? '' ?>">
                    </div>
                </div>

                <hr class="form-divider">

                <!-- Direction & Size -->
                <div class="form-group">
                    <label class="form-label">Öffnungsrichtung</label>
                    <div class="direction-selector">
                        <label
                            class="direction-option <?= $navSettings['side_menu_direction'] === 'left' ? 'selected' : '' ?>">
                            <input type="radio" name="side_menu_direction" value="left"
                                <?= $navSettings['side_menu_direction'] === 'left' ? 'checked' : '' ?>>
                            <span class="material-symbols-rounded">arrow_back</span>
                            <span>Von Links</span>
                        </label>
                        <label
                            class="direction-option <?= $navSettings['side_menu_direction'] === 'right' ? 'selected' : '' ?>">
                            <input type="radio" name="side_menu_direction" value="right"
                                <?= $navSettings['side_menu_direction'] === 'right' ? 'checked' : '' ?>>
                            <span class="material-symbols-rounded">arrow_forward</span>
                            <span>Von Rechts</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Panel-Breite</label>
                    <div class="form-row three-col">
                        <div class="form-field">
                            <label>Breite (%)</label>
                            <input type="number" name="side_menu_width_percent" class="form-input"
                                value="<?= $navSettings['side_menu_width_percent'] ?>" min="15" max="50" step="5">
                        </div>
                        <div class="form-field">
                            <label>Min. Breite (px)</label>
                            <input type="number" name="side_menu_width_min" class="form-input"
                                value="<?= $navSettings['side_menu_width_min'] ?>" min="300" max="500" step="50">
                        </div>
                        <div class="form-field">
                            <label>Max. Breite (px)</label>
                            <input type="number" name="side_menu_width_max" class="form-input"
                                value="<?= $navSettings['side_menu_width_max'] ?>" min="400" max="800" step="50">
                        </div>
                    </div>
                </div>

                <hr class="form-divider">

                <!-- Animation -->
                <div class="form-group">
                    <label class="form-label">Öffnungs-Animation</label>
                    <div class="form-row two-col">
                        <div class="form-field">
                            <label>Animations-Typ</label>
                            <select name="side_menu_animation" class="form-select">
                                <option value="slide" <?= $navSettings['side_menu_animation'] === 'slide' ? 'selected' : '' ?>>Einschieben (Slide)</option>
                                <option value="fade" <?= $navSettings['side_menu_animation'] === 'fade' ? 'selected' : '' ?>>Einblenden (Fade)</option>
                                <option value="none" <?= $navSettings['side_menu_animation'] === 'none' ? 'selected' : '' ?>>Keine Animation</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label>Geschwindigkeit (ms)</label>
                            <input type="number" name="side_menu_animation_speed" class="form-input"
                                value="<?= $navSettings['side_menu_animation_speed'] ?>" min="100" max="1000" step="50">
                        </div>
                    </div>
                </div>

                <!-- Backdrop with Opacity Slider -->
                <div class="form-group">
                    <label class="form-label">Hintergrund-Overlay</label>
                    <div class="form-row">
                        <label class="form-checkbox">
                            <input type="checkbox" name="side_menu_backdrop" value="1"
                                <?= $navSettings['side_menu_backdrop'] ? 'checked' : '' ?>
                            onchange="toggleBackdropOptions()">
                            <span class="checkbox-label">Dunkler Hintergrund wenn Menü offen</span>
                        </label>
                    </div>
                    <div id="backdrop-options" class="backdrop-settings"
                        style="margin-top: 16px; <?= !$navSettings['side_menu_backdrop'] ? 'display:none;' : '' ?>">
                        <div class="form-row two-col">
                            <div class="form-field">
                                <label>Overlay-Farbe</label>
                                <input type="color" name="side_menu_backdrop_color" class="form-input form-color"
                                    value="<?= substr($navSettings['side_menu_backdrop_color'], 0, 7) ?>">
                            </div>
                            <div class="form-field">
                                <label>Deckkraft: <span id="opacity-value"><?= $backdropOpacity ?></span>%</label>
                                <div class="opacity-slider-container">
                                    <input type="range" name="side_menu_backdrop_opacity" class="opacity-slider"
                                        value="<?= $backdropOpacity ?>" min="0" max="100" step="5"
                                        oninput="updateOpacityDisplay(this.value)">
                                    <div class="opacity-preview" id="opacity-preview"
                                        style="background: rgba(0,0,0,<?= $backdropOpacity / 100 ?>)"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Links Options (shown only when header_links is selected) -->
        <div id="header-links-options" class="content-card"
            style="<?= $navSettings['menu_type'] !== 'header_links' ? 'display:none;' : '' ?>">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-rounded">view_headline</span>
                    Header-Links Einstellungen
                </h2>
            </div>
            <div class="card-body">
                <p class="form-hint">
                    Die Links werden direkt im Header angezeigt. Mega-Menüs erscheinen unter dem Header bei Hover.
                </p>
                <div class="info-box">
                    <span class="material-symbols-rounded">info</span>
                    <p>Detaillierte Mega-Menu Einstellungen finden Sie bei den einzelnen Navigation-Links oder in den <a
                            href="?page=shop/mega_menu_settings">Mega-Menu Einstellungen</a>.</p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <span class="material-symbols-rounded">save</span>
                Einstellungen speichern
            </button>
        </div>
    </form>
</div>

<style>
    /* Menu Type Selector */
    .menu-type-selector {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .menu-type-option {
        display: block;
        cursor: pointer;
    }

    .menu-type-option input {
        display: none;
    }

    .menu-type-option .option-content {
        border: 2px solid var(--border-color, #333);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.2s;
        background: var(--bg-lighter, #1a1a1a);
    }

    .menu-type-option.selected .option-content,
    .menu-type-option:has(input:checked) .option-content {
        border-color: var(--accent, #6366f1);
        background: rgba(99, 102, 241, 0.1);
    }

    .option-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: var(--accent, #6366f1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    .option-icon .material-symbols-rounded {
        font-size: 28px;
        color: white;
    }

    .option-text strong {
        display: block;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .option-text span {
        font-size: 12px;
        color: var(--text-muted, #888);
    }

    .option-preview {
        margin-top: 16px;
        padding: 12px;
        background: var(--bg-card, #222);
        border-radius: 8px;
        font-size: 10px;
    }

    .header-links-preview .preview-header {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 8px 12px;
        background: var(--bg-main, #111);
        border-radius: 4px;
    }

    .header-links-preview .preview-links {
        display: flex;
        gap: 12px;
    }

    .header-links-preview .preview-links span {
        color: var(--text-muted, #888);
    }

    .side-menu-preview .preview-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        background: var(--bg-main, #111);
        border-radius: 4px;
        margin-bottom: 8px;
    }

    .side-menu-preview .preview-hamburger {
        font-size: 16px;
    }

    .side-menu-preview .preview-sidebar {
        width: 40%;
        height: 40px;
        background: var(--accent, #6366f1);
        opacity: 0.3;
        border-radius: 0 4px 4px 0;
    }

    /* Side Style Selector */
    .side-style-selector {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-top: 12px;
    }

    .side-style-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        padding: 16px;
        border: 2px solid var(--border-color, #333);
        border-radius: 8px;
        transition: all 0.2s;
    }

    .side-style-option input {
        display: none;
    }

    .side-style-option.selected,
    .side-style-option:has(input:checked) {
        border-color: var(--accent, #6366f1);
        background: rgba(99, 102, 241, 0.1);
    }

    .style-preview {
        display: flex;
        gap: 4px;
        margin-bottom: 12px;
        height: 60px;
    }

    .style-preview .panel {
        width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        color: white;
        border-radius: 4px;
    }

    .style-preview .panel-main {
        background: var(--accent, #6366f1);
    }

    .style-preview .panel-sub {
        background: #10b981;
    }

    .style-preview.push-overlay {
        position: relative;
    }

    .style-preview.push-overlay .panel-main.faded {
        opacity: 0.3;
    }

    .style-preview.push-overlay .panel-sub.overlay {
        position: absolute;
        left: 0;
        width: 50px;
    }

    .style-name {
        font-weight: 600;
        font-size: 13px;
    }

    .style-example {
        font-size: 11px;
        color: var(--text-muted, #888);
    }

    /* Hamburger Mode Selector */
    .hamburger-mode-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-top: 12px;
    }

    .hamburger-mode-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        padding: 20px 16px;
        border: 2px solid var(--border-color, #333);
        border-radius: 12px;
        transition: all 0.2s;
        text-align: center;
    }

    .hamburger-mode-option input {
        display: none;
    }

    .hamburger-mode-option.selected,
    .hamburger-mode-option:has(input:checked) {
        border-color: var(--accent, #6366f1);
        background: rgba(99, 102, 241, 0.1);
    }

    .mode-preview {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-card, #222);
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .hamburger-animated,
    .hamburger-static {
        display: flex;
        flex-direction: column;
        gap: 5px;
        width: 24px;
    }

    .hamburger-animated span,
    .hamburger-static span {
        display: block;
        height: 3px;
        background: var(--accent, #6366f1);
        border-radius: 2px;
        transition: all 0.3s;
    }

    .hamburger-mode-option:hover .hamburger-animated span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 6px);
    }
    .hamburger-mode-option:hover .hamburger-animated span:nth-child(2) {
        opacity: 0;
    }
    .hamburger-mode-option:hover .hamburger-animated span:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -6px);
    }

    .hamburger-custom {
        width: 40px;
        height: 40px;
        border: 2px dashed var(--border-color, #444);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hamburger-custom .material-symbols-rounded {
        font-size: 20px;
        color: var(--text-muted, #888);
    }

    .mode-text strong {
        display: block;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .mode-text span {
        font-size: 11px;
        color: var(--text-muted, #888);
    }

    /* Direction Selector */
    .direction-selector {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    .direction-option {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 12px 20px;
        border: 2px solid var(--border-color, #333);
        border-radius: 8px;
        transition: all 0.2s;
    }

    .direction-option input {
        display: none;
    }

    .direction-option.selected,
    .direction-option:has(input:checked) {
        border-color: var(--accent, #6366f1);
        background: rgba(99, 102, 241, 0.1);
    }

    /* Form helpers */
    .form-divider {
        border: none;
        border-top: 1px solid var(--border-color, #333);
        margin: 24px 0;
    }

    .form-row.two-col {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .form-row.three-col {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .form-field label {
        display: block;
        font-size: 12px;
        color: var(--text-muted, #888);
        margin-bottom: 6px;
    }

    .form-color {
        height: 40px;
        padding: 4px;
    }

    /* Opacity Slider */
    .opacity-slider-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .opacity-slider {
        flex: 1;
        height: 8px;
        -webkit-appearance: none;
        appearance: none;
        background: linear-gradient(to right, transparent, #000);
        border-radius: 4px;
        cursor: pointer;
    }

    .opacity-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--accent, #6366f1);
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .opacity-preview {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        border: 1px solid var(--border-color, #333);
    }

    .info-box {
        display: flex;
        gap: 12px;
        padding: 16px;
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.3);
        border-radius: 8px;
    }

    .info-box .material-symbols-rounded {
        color: var(--accent, #6366f1);
        flex-shrink: 0;
    }

    .info-box p {
        margin: 0;
        font-size: 13px;
        color: var(--text-secondary, #aaa);
    }

    .info-box a {
        color: var(--accent, #6366f1);
    }

    .form-actions {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--border-color, #333);
    }

    .btn-lg {
        padding: 14px 28px;
        font-size: 15px;
    }

    .backdrop-settings {
        padding: 16px;
        background: var(--bg-lighter, #1a1a1a);
        border-radius: 8px;
        border: 1px solid var(--border-color, #333);
    }

    @media (max-width: 768px) {
        .menu-type-selector,
        .hamburger-mode-selector {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    function toggleMenuTypeOptions() {
        const menuType = document.querySelector('input[name="menu_type"]:checked').value;
        const sideOptions = document.getElementById('side-menu-options');
        const headerOptions = document.getElementById('header-links-options');

        if (menuType === 'side_menu') {
            sideOptions.style.display = 'block';
            headerOptions.style.display = 'none';
        } else {
            sideOptions.style.display = 'none';
            headerOptions.style.display = 'block';
        }

        // Update selected class
        document.querySelectorAll('.menu-type-option').forEach(opt => {
            opt.classList.toggle('selected', opt.querySelector('input').checked);
        });
    }

    function toggleHamburgerMode() {
        const mode = document.querySelector('input[name="hamburger_mode"]:checked').value;
        const customUpload = document.getElementById('hamburger-custom-upload');
        
        customUpload.style.display = mode === 'custom' ? 'block' : 'none';

        // Update selected class
        document.querySelectorAll('.hamburger-mode-option').forEach(opt => {
            opt.classList.toggle('selected', opt.querySelector('input').checked);
        });
    }

    function toggleBackdropOptions() {
        const hasBackdrop = document.querySelector('input[name="side_menu_backdrop"]').checked;
        document.getElementById('backdrop-options').style.display = hasBackdrop ? 'block' : 'none';
    }

    function updateOpacityDisplay(value) {
        document.getElementById('opacity-value').textContent = value;
        document.getElementById('opacity-preview').style.background = `rgba(0,0,0,${value/100})`;
    }

    // Update selected states on radio change
    document.querySelectorAll('.side-style-option input, .direction-option input').forEach(input => {
        input.addEventListener('change', function () {
            const container = this.closest('.side-style-selector, .direction-selector');
            container.querySelectorAll('.side-style-option, .direction-option').forEach(opt => {
                opt.classList.toggle('selected', opt.querySelector('input').checked);
            });
        });
    });
</script>