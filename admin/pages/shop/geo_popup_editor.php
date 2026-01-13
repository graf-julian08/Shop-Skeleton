<?php
/**
 * Geo-Location Popup Editor - Fullpage Visual Editor
 * 
 * Grid-based drag-and-drop editor for geo-location popups
 * Based on mega_menu_editor.php - adapted for popup design
 */

// Database is already configured by the admin router (index.php)
// No need to re-configure here

$shopId = intval($_GET['shop_id'] ?? 1);


// Canvas defaults for popup (smaller than mega menu)
$defaultCanvasWidth = 400;  // Desktop popup width
$defaultCanvasHeight = 320; // Desktop popup height
$mobileCanvasWidth = 320;   // Mobile popup width  
$mobileCanvasHeight = 400;  // Mobile popup height (taller for vertical layout)

// Get existing popup elements
$elements = [];
try {
    $elements = Database::fetchAll(
        "SELECT * FROM geo_popup_elements WHERE shop_id = ? ORDER BY z_index",
        [$shopId]
    );
    foreach ($elements as &$el) {
        $el['content'] = json_decode($el['content_json'] ?? '{}', true);
        $el['style'] = json_decode($el['style_json'] ?? '{}', true);
    }
} catch (Exception $e) {
    // Table might not exist yet
}

// Get popup settings
$popupSettings = [];
try {
    $popupSettings = Database::fetch(
        "SELECT * FROM geo_popup_settings WHERE shop_id = ?",
        [$shopId]
    );
} catch (Exception $e) {
    // Table might not exist
}

// Get countries for flag selector
$countries = [];
try {
    $countries = Database::fetchAll(
        "SELECT code, name FROM countries WHERE shop_id = ? AND is_active = 1 ORDER BY name",
        [$shopId]
    );
} catch (Exception $e) {
    // Use defaults
    $countries = [
        ['code' => 'US', 'name' => 'United States'],
        ['code' => 'DE', 'name' => 'Germany'],
        ['code' => 'CH', 'name' => 'Switzerland'],
        ['code' => 'FR', 'name' => 'France'],
        ['code' => 'GB', 'name' => 'United Kingdom'],
    ];
}
?>

<div class="geo-popup-editor-fullpage">
    <!-- Header -->
    <header class="editor-header">
        <div class="header-left">
            <a href="?page=shop/localization&tab=geo-location" class="back-link">
                <span class="material-symbols-rounded">arrow_back</span>
                Zurück zur Lokalisierung
            </a>
            <h1>
                <span class="material-symbols-rounded" style="color: #af52de;">pin_drop</span>
                Geo-Location Popup Designer
            </h1>
        </div>
        <div class="header-center">
            <!-- Device Toggle -->
            <div class="device-toggle">
                <button type="button" class="device-btn active" data-device="desktop" title="Desktop">
                    <span class="material-symbols-rounded">desktop_windows</span>
                </button>
                <button type="button" class="device-btn" data-device="mobile" title="Mobile">
                    <span class="material-symbols-rounded">smartphone</span>
                </button>
            </div>
        </div>
        <div class="header-right">
            <span class="save-status" id="save-status">
                <span class="material-symbols-rounded">check_circle</span>
                <span class="status-text">Gespeichert</span>
            </span>
            <button type="button" class="btn" id="btn-preview">
                <span class="material-symbols-rounded">visibility</span>
                Vorschau
            </button>
            <button type="button" class="btn btn-primary" id="btn-save">
                <span class="material-symbols-rounded">save</span>
                Speichern
            </button>
        </div>
    </header>

    <div class="editor-body">
        <!-- Left Sidebar: Components -->
        <aside class="editor-sidebar">
            <!-- Popup Components -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">
                    <span class="material-symbols-rounded">widgets</span>
                    Komponenten
                </h3>
                <div class="components-grid">
                    <div class="component-item" draggable="true" data-component="heading">
                        <span class="material-symbols-rounded">title</span>
                        <span>Überschrift</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="text">
                        <span class="material-symbols-rounded">text_fields</span>
                        <span>Text</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="flag">
                        <span class="material-symbols-rounded">flag</span>
                        <span>Flagge</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="flag-pair">
                        <span class="material-symbols-rounded">compare_arrows</span>
                        <span>Flaggen-Paar</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="button-stay">
                        <span class="material-symbols-rounded">cancel</span>
                        <span>Hier bleiben</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="button-switch">
                        <span class="material-symbols-rounded">swap_horiz</span>
                        <span>Wechseln</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="button-group">
                        <span class="material-symbols-rounded">view_column</span>
                        <span>Button-Gruppe</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="country-select">
                        <span class="material-symbols-rounded">list</span>
                        <span>Länder-Auswahl</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="image">
                        <span class="material-symbols-rounded">image</span>
                        <span>Bild</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="close-button">
                        <span class="material-symbols-rounded">close</span>
                        <span>Schliessen-X</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="divider">
                        <span class="material-symbols-rounded">horizontal_rule</span>
                        <span>Trenner</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="spacer">
                        <span class="material-symbols-rounded">height</span>
                        <span>Abstand</span>
                    </div>
                </div>
            </div>

            <!-- Quick Templates -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">
                    <span class="material-symbols-rounded">dashboard</span>
                    Vorlagen
                </h3>
                <div class="templates-list">
                    <button type="button" class="template-btn" data-template="classic">
                        <span class="template-preview template-classic"></span>
                        <span>Klassisch</span>
                    </button>
                    <button type="button" class="template-btn" data-template="minimal">
                        <span class="template-preview template-minimal"></span>
                        <span>Minimal</span>
                    </button>
                    <button type="button" class="template-btn" data-template="flags">
                        <span class="template-preview template-flags"></span>
                        <span>Mit Flaggen</span>
                    </button>
                    <button type="button" class="template-btn" data-template="dropdown">
                        <span class="template-preview template-dropdown"></span>
                        <span>Mit Dropdown</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Canvas -->
        <main class="editor-canvas-wrapper">
            <div class="canvas-info">
                <span class="material-symbols-rounded">info</span>
                Ziehe Komponenten auf die Canvas um dein Popup zu gestalten
            </div>
            <div class="canvas-scroll">
                <div class="popup-canvas" id="popup-canvas"
                    style="width: <?= $defaultCanvasWidth ?>px; height: <?= $defaultCanvasHeight ?>px;">
                    <div class="canvas-grid"></div>
                    <div class="canvas-elements" id="canvas-elements">
                        <!-- Default content if empty -->
                        <?php if (empty($elements)): ?>
                            <div class="default-popup-content">
                                <div class="popup-flag-pair">
                                    <span class="flag-emoji">🇨🇭</span>
                                    <span class="arrow">→</span>
                                    <span class="flag-emoji">🇺🇸</span>
                                </div>
                                <h3 class="popup-title">Falscher Shop?</h3>
                                <p class="popup-text">Du befindest dich in der Schweiz, aber siehst die US-Version.</p>
                                <div class="popup-buttons">
                                    <button class="popup-btn popup-btn-secondary">Hier bleiben</button>
                                    <button class="popup-btn popup-btn-primary">Zur 🇨🇭 Version</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="canvas-dimensions" id="canvas-dimensions">
                <?= $defaultCanvasWidth ?> ×
                <?= $defaultCanvasHeight ?>px
            </div>
        </main>

        <!-- Right Sidebar: Element Settings -->
        <aside class="editor-settings">
            <div class="settings-empty" id="settings-empty">
                <span class="material-symbols-rounded">touch_app</span>
                <p>Wähle ein Element aus, um es zu bearbeiten</p>
            </div>
            <div class="settings-content" id="settings-content" style="display: none;">
                <!-- Element Type Header -->
                <div class="settings-header">
                    <span class="element-type-icon material-symbols-rounded" id="el-type-icon">text_fields</span>
                    <span class="element-type-label" id="el-type-label">Text</span>
                    <button type="button" class="icon-btn-sm" id="btn-delete-element" title="Löschen">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>

                <!-- Position Section -->
                <div class="settings-section">
                    <div class="section-header">
                        <span class="material-symbols-rounded">open_with</span>
                        <span>Position</span>
                    </div>
                    <div class="position-inputs">
                        <div class="position-field">
                            <label>X</label>
                            <input type="number" id="el-pos-x" class="pos-input" value="0" step="1">
                            <span class="unit">px</span>
                        </div>
                        <div class="position-field">
                            <label>Y</label>
                            <input type="number" id="el-pos-y" class="pos-input" value="0" step="1">
                            <span class="unit">px</span>
                        </div>
                    </div>
                </div>

                <!-- Size Section -->
                <div class="settings-section">
                    <div class="section-header">
                        <span class="material-symbols-rounded">aspect_ratio</span>
                        <span>Grösse</span>
                    </div>
                    <div class="size-inputs">
                        <div class="size-field">
                            <label>B</label>
                            <input type="number" id="el-width" class="size-input" value="100" step="1">
                            <select id="el-width-unit" class="unit-select">
                                <option value="px">px</option>
                                <option value="%" selected>%</option>
                                <option value="auto">auto</option>
                            </select>
                        </div>
                        <div class="size-field">
                            <label>H</label>
                            <input type="number" id="el-height" class="size-input" value="auto" step="1">
                            <select id="el-height-unit" class="unit-select">
                                <option value="px">px</option>
                                <option value="%">%</option>
                                <option value="auto" selected>auto</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Content Section (dynamic based on element type) -->
                <div class="settings-section" id="content-settings">
                    <div class="section-header">
                        <span class="material-symbols-rounded">edit</span>
                        <span>Inhalt</span>
                    </div>
                    <div id="content-fields">
                        <!-- Filled dynamically -->
                    </div>
                </div>

                <!-- Styling Section -->
                <div class="settings-section">
                    <div class="section-header">
                        <span class="material-symbols-rounded">palette</span>
                        <span>Styling</span>
                    </div>

                    <!-- Colors -->
                    <div class="style-row">
                        <label>Hintergrund</label>
                        <div class="color-input-wrap">
                            <input type="color" id="el-bg-color" value="#ffffff">
                            <input type="text" id="el-bg-hex" class="color-text" value="#ffffff">
                        </div>
                    </div>
                    <div class="style-row">
                        <label>Textfarbe</label>
                        <div class="color-input-wrap">
                            <input type="color" id="el-text-color" value="#333333">
                            <input type="text" id="el-text-hex" class="color-text" value="#333333">
                        </div>
                    </div>

                    <!-- Font Size -->
                    <div class="style-row">
                        <label>Schriftgrösse</label>
                        <div class="slider-wrap">
                            <input type="range" id="el-font-size" min="10" max="48" value="14">
                            <span id="el-font-size-value">14px</span>
                        </div>
                    </div>

                    <!-- Border Radius -->
                    <div class="style-row">
                        <label>Eckenradius</label>
                        <div class="slider-wrap">
                            <input type="range" id="el-border-radius" min="0" max="30" value="8">
                            <span id="el-border-radius-value">8px</span>
                        </div>
                    </div>

                    <!-- Padding -->
                    <div class="style-row">
                        <label>Innenabstand</label>
                        <div class="padding-inputs">
                            <input type="number" id="el-padding" value="12" min="0" max="50">
                            <span>px</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="settings-actions">
                    <button type="button" class="action-btn" id="btn-duplicate" title="Duplizieren">
                        <span class="material-symbols-rounded">content_copy</span>
                    </button>
                    <button type="button" class="action-btn" id="btn-bring-front" title="Nach vorne">
                        <span class="material-symbols-rounded">flip_to_front</span>
                    </button>
                    <button type="button" class="action-btn" id="btn-send-back" title="Nach hinten">
                        <span class="material-symbols-rounded">flip_to_back</span>
                    </button>
                </div>
            </div>

            <!-- Popup Background Settings -->
            <div class="popup-settings-section">
                <h4>
                    <span class="material-symbols-rounded">layers</span>
                    Popup-Hintergrund
                </h4>
                <div class="style-row">
                    <label>Farbe</label>
                    <div class="color-input-wrap">
                        <input type="color" id="popup-bg-color" value="#1a1a1a">
                        <input type="text" id="popup-bg-hex" class="color-text" value="#1a1a1a">
                    </div>
                </div>
                <div class="style-row">
                    <label>Schatten</label>
                    <select id="popup-shadow" class="style-select">
                        <option value="none">Kein</option>
                        <option value="sm">Klein</option>
                        <option value="md" selected>Mittel</option>
                        <option value="lg">Gross</option>
                        <option value="xl">Extra Gross</option>
                    </select>
                </div>
                <div class="style-row">
                    <label>Rahmenradius</label>
                    <div class="slider-wrap">
                        <input type="range" id="popup-radius" min="0" max="32" value="12">
                        <span id="popup-radius-value">12px</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="preview-modal" style="display: none;">
    <div class="preview-backdrop" onclick="geoPopupEditor.closePreview()"></div>
    <div class="preview-container">
        <div class="preview-header">
            <h3>
                <span class="material-symbols-rounded">visibility</span>
                Popup-Vorschau
            </h3>
            <button type="button" class="modal-close" onclick="geoPopupEditor.closePreview()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="preview-body">
            <div class="preview-popup" id="preview-popup">
                <!-- Preview rendered here -->
            </div>
        </div>
        <div class="preview-footer">
            <span class="preview-hint">
                <span class="material-symbols-rounded">info</span>
                So sieht das Popup für deine Besucher aus
            </span>
            <button type="button" class="btn" onclick="geoPopupEditor.closePreview()">Schliessen</button>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<script>
    window.GEO_POPUP_DATA = {
        shopId: <?= $shopId ?>,
        elements: <?= json_encode($elements) ?>,
        settings: <?= json_encode($popupSettings ?: new stdClass()) ?>,
        countries: <?= json_encode($countries) ?>,
        canvasWidth: <?= $defaultCanvasWidth ?>,
        canvasHeight: <?= $defaultCanvasHeight ?>,
        mobileWidth: <?= $mobileCanvasWidth ?>,
        mobileHeight: <?= $mobileCanvasHeight ?>
    };
</script>

<style>
    /* ========== GEO POPUP EDITOR STYLES ========== */

    .geo-popup-editor-fullpage {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        background: var(--bg-main, #0d0d0d);
    }

    /* Header */
    .editor-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        background: var(--bg-card, #1a1a1a);
        border-bottom: 1px solid var(--border-color, #333);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .back-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--text-muted, #888);
        text-decoration: none;
        font-size: 13px;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: var(--text, #fff);
    }

    .editor-header h1 {
        margin: 0;
        font-size: 16px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .header-center {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .device-toggle {
        display: flex;
        background: var(--bg-lighter, #2a2a2a);
        border-radius: 8px;
        padding: 4px;
    }

    .device-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 32px;
        border: none;
        background: transparent;
        color: var(--text-muted);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .device-btn:hover {
        color: var(--text);
    }

    .device-btn.active {
        background: var(--primary, #6366f1);
        color: white;
    }

    .device-btn .material-symbols-rounded {
        font-size: 20px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .save-status {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--success, #22c55e);
    }

    /* Body Layout */
    .editor-body {
        flex: 1;
        display: flex;
        overflow: hidden;
    }

    /* Left Sidebar */
    .editor-sidebar {
        width: 280px;
        background: var(--bg-card, #1a1a1a);
        border-right: 1px solid var(--border-color, #333);
        overflow-y: auto;
        padding: 16px;
    }

    .sidebar-section {
        margin-bottom: 24px;
    }

    .sidebar-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin: 0 0 12px 0;
    }

    .sidebar-title .material-symbols-rounded {
        font-size: 18px;
    }

    .components-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .component-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 12px 8px;
        background: var(--bg-lighter, #2a2a2a);
        border: 1px solid var(--border-color, #333);
        border-radius: 8px;
        cursor: grab;
        transition: all 0.2s;
        font-size: 11px;
        color: var(--text-muted);
    }

    .component-item:hover {
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.1);
        color: var(--text);
    }

    .component-item:active {
        cursor: grabbing;
    }

    .component-item .material-symbols-rounded {
        font-size: 24px;
    }

    /* Templates */
    .templates-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .template-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        background: var(--bg-lighter);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        cursor: pointer;
        color: var(--text);
        font-size: 13px;
        transition: all 0.2s;
    }

    .template-btn:hover {
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.1);
    }

    .template-preview {
        width: 40px;
        height: 30px;
        background: var(--bg-card);
        border-radius: 4px;
        border: 1px solid var(--border-color);
    }

    /* Canvas Area */
    .editor-canvas-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--bg-main);
        position: relative;
        overflow: auto;
        padding: 40px;
    }

    .canvas-info {
        position: absolute;
        top: 16px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.3);
        border-radius: 8px;
        font-size: 13px;
        color: var(--text-muted);
    }

    .canvas-scroll {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .popup-canvas {
        background: var(--bg-card);
        border-radius: 12px;
        border: 2px solid var(--border-color);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        position: relative;
        overflow: hidden;
        transition: width 0.3s, height 0.3s;
    }

    .popup-canvas:hover {
        border-color: var(--primary);
    }

    .canvas-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 20px 20px;
        pointer-events: none;
    }

    .canvas-elements {
        position: relative;
        width: 100%;
        height: 100%;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Default Popup Content */
    .default-popup-content {
        text-align: center;
    }

    .popup-flag-pair {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .flag-emoji {
        font-size: 32px;
    }

    .arrow {
        font-size: 24px;
        color: var(--text-muted);
    }

    .popup-title {
        margin: 0 0 8px 0;
        font-size: 18px;
        font-weight: 600;
    }

    .popup-text {
        margin: 0 0 20px 0;
        color: var(--text-muted);
        font-size: 14px;
    }

    .popup-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .popup-btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .popup-btn-secondary {
        background: var(--bg-lighter);
        color: var(--text);
    }

    .popup-btn-secondary:hover {
        background: var(--bg-active);
    }

    .popup-btn-primary {
        background: var(--primary);
        color: white;
    }

    .popup-btn-primary:hover {
        filter: brightness(1.1);
    }

    .canvas-dimensions {
        position: absolute;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 12px;
        color: var(--text-muted);
        background: var(--bg-card);
        padding: 4px 12px;
        border-radius: 4px;
    }

    /* Right Sidebar */
    .editor-settings {
        width: 300px;
        background: var(--bg-card);
        border-left: 1px solid var(--border-color);
        overflow-y: auto;
        padding: 16px;
    }

    .settings-empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
    }

    .settings-empty .material-symbols-rounded {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .settings-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 12px;
        margin-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .element-type-icon {
        font-size: 20px;
        color: var(--primary);
    }

    .element-type-label {
        flex: 1;
        font-weight: 600;
    }

    .icon-btn-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: var(--bg-lighter);
        color: var(--text-muted);
        border-radius: 6px;
        cursor: pointer;
    }

    .icon-btn-sm:hover {
        background: var(--danger, #ef4444);
        color: white;
    }

    .settings-section {
        margin-bottom: 20px;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .section-header .material-symbols-rounded {
        font-size: 16px;
    }

    .position-inputs,
    .size-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .position-field,
    .size-field {
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--bg-lighter);
        padding: 8px 10px;
        border-radius: 6px;
    }

    .position-field label,
    .size-field label {
        font-size: 11px;
        color: var(--text-muted);
        width: 16px;
    }

    .pos-input,
    .size-input {
        flex: 1;
        width: 100%;
        background: transparent;
        border: none;
        color: var(--text);
        font-size: 13px;
        outline: none;
    }

    .unit,
    .unit-select {
        font-size: 11px;
        color: var(--text-muted);
    }

    .unit-select {
        background: transparent;
        border: none;
        color: var(--text-muted);
    }

    .style-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .style-row label {
        font-size: 13px;
        color: var(--text-muted);
    }

    .color-input-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-lighter);
        padding: 4px 8px;
        border-radius: 6px;
    }

    .color-input-wrap input[type="color"] {
        width: 24px;
        height: 24px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .color-text {
        width: 70px;
        background: transparent;
        border: none;
        color: var(--text);
        font-size: 12px;
        font-family: monospace;
    }

    .slider-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .slider-wrap input[type="range"] {
        width: 100px;
    }

    .slider-wrap span {
        font-size: 12px;
        color: var(--text-muted);
        min-width: 40px;
    }

    .style-select {
        background: var(--bg-lighter);
        border: none;
        color: var(--text);
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 13px;
    }

    .padding-inputs {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .padding-inputs input {
        width: 50px;
        background: var(--bg-lighter);
        border: none;
        color: var(--text);
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 13px;
    }

    .padding-inputs span {
        font-size: 12px;
        color: var(--text-muted);
    }

    .settings-actions {
        display: flex;
        gap: 8px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }

    .action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        background: var(--bg-lighter);
        border: none;
        color: var(--text-muted);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: var(--bg-active);
        color: var(--text);
    }

    .popup-settings-section {
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }

    .popup-settings-section h4 {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin: 0 0 16px 0;
    }

    /* Preview Modal */
    .preview-modal {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .preview-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
    }

    .preview-container {
        position: relative;
        background: var(--bg-card);
        border-radius: 16px;
        max-width: 90vw;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5);
    }

    .preview-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .preview-header h3 {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 16px;
    }

    .modal-close {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: var(--bg-lighter);
        color: var(--text-muted);
        border-radius: 8px;
        cursor: pointer;
    }

    .modal-close:hover {
        background: var(--danger);
        color: white;
    }

    .preview-body {
        padding: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 300px;
    }

    .preview-popup {
        background: var(--bg-card);
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        min-width: 320px;
    }

    .preview-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-top: 1px solid var(--border-color);
    }

    .preview-hint {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-muted);
    }

    /* Form hint style */
    .form-hint {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }
</style>

<script>
    class GeoPopupEditor {
        constructor() {
            this.data = window.GEO_POPUP_DATA;
            this.elements = [];
            this.selectedElement = null;
            this.isDragging = false;
            this.currentDevice = 'desktop';
            this.autoSaveTimeout = null;
            this.hasUnsavedChanges = false;

            // Extended flag map
            this.flags = {
                'US': '🇺🇸', 'DE': '🇩🇪', 'CH': '🇨🇭', 'FR': '🇫🇷', 'GB': '🇬🇧',
                'IT': '🇮🇹', 'ES': '🇪🇸', 'AT': '🇦🇹', 'NL': '🇳🇱', 'BE': '🇧🇪',
                'CA': '🇨🇦', 'AU': '🇦🇺', 'JP': '🇯🇵', 'CN': '🇨🇳', 'BR': '🇧🇷',
                'MX': '🇲🇽', 'RU': '🇷🇺', 'IN': '🇮🇳', 'KR': '🇰🇷', 'SE': '🇸🇪',
                'NO': '🇳🇴', 'DK': '🇩🇰', 'FI': '🇫🇮', 'PL': '🇵🇱', 'PT': '🇵🇹',
                'GR': '🇬🇷', 'TR': '🇹🇷', 'IE': '🇮🇪', 'NZ': '🇳🇿', 'SG': '🇸🇬',
                'AE': '🇦🇪', 'SA': '🇸🇦', 'ZA': '🇿🇦', 'IL': '🇮🇱', 'TH': '🇹🇭'
            };

            this.init();
        }

        async init() {
            await this.loadFromDatabase();
            this.bindEvents();
            this.renderElements();
            this.applyPopupSettings();
        }

        async loadFromDatabase() {
            try {
                const response = await fetch(`/admin/api/geo_popup.php?action=get&shop_id=${this.data.shopId}`);
                const result = await response.json();
                if (result.success && result.elements?.length > 0) {
                    this.elements = result.elements.map(el => ({
                        id: el.element_id || el.id,
                        type: el.element_type || el.type,
                        x: parseInt(el.pos_x || el.x) || 0,
                        y: parseInt(el.pos_y || el.y) || 0,
                        content: el.content || {},
                        style: el.style || this.getDefaultStyle(el.element_type || el.type)
                    }));
                    if (result.settings) {
                        this.data.settings = result.settings;
                    }
                }
            } catch (e) {
                console.log('No saved popup data found, using defaults');
            }
        }

        applyPopupSettings() {
            const s = this.data.settings || {};
            const canvas = document.getElementById('popup-canvas');
            if (s.background_color) {
                canvas.style.background = s.background_color;
                document.getElementById('popup-bg-color').value = s.background_color;
                document.getElementById('popup-bg-hex').value = s.background_color;
            }
            if (s.border_radius) {
                canvas.style.borderRadius = s.border_radius + 'px';
                document.getElementById('popup-radius').value = s.border_radius;
                document.getElementById('popup-radius-value').textContent = s.border_radius + 'px';
            }
        }

        bindEvents() {
            // Device toggle
            document.querySelectorAll('.device-btn').forEach(btn => {
                btn.addEventListener('click', () => this.switchDevice(btn.dataset.device));
            });

            // Preview & Save buttons
            document.getElementById('btn-preview')?.addEventListener('click', () => this.openPreview());
            document.getElementById('btn-save')?.addEventListener('click', () => this.save());

            // Component drag
            document.querySelectorAll('.component-item').forEach(item => {
                item.addEventListener('dragstart', (e) => this.onDragStart(e, item));
                item.addEventListener('dragend', () => this.onDragEnd());
            });

            // Canvas drop zone
            const canvas = document.getElementById('popup-canvas');
            if (canvas) {
                canvas.addEventListener('dragover', (e) => { e.preventDefault(); canvas.classList.add('drag-over'); });
                canvas.addEventListener('dragleave', () => canvas.classList.remove('drag-over'));
                canvas.addEventListener('drop', (e) => { canvas.classList.remove('drag-over'); this.onDrop(e); });
                canvas.addEventListener('click', (e) => { if (e.target === canvas || e.target.classList.contains('canvas-elements')) this.deselectAll(); });
            }

            // Templates
            document.querySelectorAll('.template-btn').forEach(btn => {
                btn.addEventListener('click', () => this.loadTemplate(btn.dataset.template));
            });

            // Popup background settings with auto-save
            document.getElementById('popup-bg-color')?.addEventListener('input', (e) => {
                document.getElementById('popup-canvas').style.background = e.target.value;
                document.getElementById('popup-bg-hex').value = e.target.value;
                this.triggerAutoSave();
            });
            document.getElementById('popup-radius')?.addEventListener('input', (e) => {
                document.getElementById('popup-canvas').style.borderRadius = e.target.value + 'px';
                document.getElementById('popup-radius-value').textContent = e.target.value + 'px';
                this.triggerAutoSave();
            });

            // Delete button
            document.getElementById('btn-delete-element')?.addEventListener('click', () => this.deleteSelected());

            // Position inputs
            document.getElementById('el-pos-x')?.addEventListener('change', (e) => this.updateSelectedPosition('x', parseInt(e.target.value)));
            document.getElementById('el-pos-y')?.addEventListener('change', (e) => this.updateSelectedPosition('y', parseInt(e.target.value)));

            // Style inputs
            document.getElementById('el-bg-color')?.addEventListener('input', (e) => this.updateSelectedStyle('backgroundColor', e.target.value));
            document.getElementById('el-text-color')?.addEventListener('input', (e) => this.updateSelectedStyle('color', e.target.value));
            document.getElementById('el-font-size')?.addEventListener('input', (e) => {
                this.updateSelectedStyle('fontSize', parseInt(e.target.value));
                document.getElementById('el-font-size-value').textContent = e.target.value + 'px';
            });
            document.getElementById('el-border-radius')?.addEventListener('input', (e) => {
                this.updateSelectedStyle('borderRadius', parseInt(e.target.value));
                document.getElementById('el-border-radius-value').textContent = e.target.value + 'px';
            });
            document.getElementById('el-padding')?.addEventListener('input', (e) => this.updateSelectedStyle('padding', parseInt(e.target.value)));

            // Duplicate, bring front, send back
            document.getElementById('btn-duplicate')?.addEventListener('click', () => this.duplicateSelected());
            document.getElementById('btn-bring-front')?.addEventListener('click', () => this.bringToFront());
            document.getElementById('btn-send-back')?.addEventListener('click', () => this.sendToBack());

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Delete' || e.key === 'Backspace') {
                    if (this.selectedElement && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                        e.preventDefault();
                        this.deleteSelected();
                    }
                }
                if ((e.metaKey || e.ctrlKey) && e.key === 's') {
                    e.preventDefault();
                    this.save();
                }
                if (e.key === 'Escape') {
                    this.deselectAll();
                }
            });

            // Warn before leaving with unsaved changes
            window.addEventListener('beforeunload', (e) => {
                if (this.hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        }

        switchDevice(device) {
            this.currentDevice = device;
            document.querySelectorAll('.device-btn').forEach(btn => btn.classList.toggle('active', btn.dataset.device === device));
            const canvas = document.getElementById('popup-canvas');
            const dimensions = document.getElementById('canvas-dimensions');
            if (device === 'mobile') {
                canvas.style.width = this.data.mobileWidth + 'px';
                canvas.style.height = this.data.mobileHeight + 'px';
                dimensions.textContent = `${this.data.mobileWidth} × ${this.data.mobileHeight}px`;
            } else {
                canvas.style.width = this.data.canvasWidth + 'px';
                canvas.style.height = this.data.canvasHeight + 'px';
                dimensions.textContent = `${this.data.canvasWidth} × ${this.data.canvasHeight}px`;
            }
        }

        onDragStart(e, item) {
            this.isDragging = true;
            e.dataTransfer.setData('component', item.dataset.component);
            e.dataTransfer.effectAllowed = 'copy';
            item.classList.add('dragging');
        }

        onDragEnd() {
            this.isDragging = false;
            document.querySelectorAll('.component-item').forEach(item => item.classList.remove('dragging'));
        }

        onDrop(e) {
            e.preventDefault();
            const componentType = e.dataTransfer.getData('component');
            if (componentType) this.addElement(componentType, e);
        }

        addElement(type, event) {
            const canvas = document.getElementById('popup-canvas');
            const rect = canvas.getBoundingClientRect();
            const element = {
                id: 'el-' + Date.now(),
                type: type,
                x: event ? Math.max(0, event.clientX - rect.left - 50) : 50,
                y: event ? Math.max(0, event.clientY - rect.top - 20) : 50,
                content: this.getDefaultContent(type),
                style: this.getDefaultStyle(type)
            };
            this.elements.push(element);
            this.renderElement(element);
            this.selectElement(element.id);
            this.triggerAutoSave();
        }

        getDefaultContent(type) {
            const defaults = {
                'heading': { text: 'Falscher Shop?' },
                'text': { text: 'Du befindest dich in einem anderen Land.' },
                'flag': { country: 'CH' },
                'flag-pair': { from: 'US', to: 'CH' },
                'button-stay': { text: 'Hier bleiben', action: 'close' },
                'button-switch': { text: 'Zur Version wechseln', action: 'redirect' },
                'button-group': { stayText: 'Hier bleiben', switchText: 'Wechseln' },
                'country-select': { label: 'Land wählen', showFlags: true },
                'image': { src: '/admin/assets/img/placeholder.png', alt: 'Bild' },
                'close-button': { position: 'top-right' },
                'divider': { color: '#333333', thickness: 1 },
                'spacer': { height: 20 }
            };
            return { ...defaults[type] } || {};
        }

        getDefaultStyle(type) {
            const base = { backgroundColor: 'transparent', color: '#ffffff', fontSize: 14, padding: 0, borderRadius: 0 };
            if (type === 'heading') return { ...base, fontSize: 20, fontWeight: 600 };
            if (type === 'button-stay') return { ...base, backgroundColor: '#2a2a2a', padding: 12, borderRadius: 8 };
            if (type === 'button-switch') return { ...base, backgroundColor: '#6366f1', padding: 12, borderRadius: 8 };
            if (type === 'button-group') return { ...base, gap: 12 };
            return base;
        }

        renderElements() {
            const container = document.getElementById('canvas-elements');
            container.innerHTML = '';
            // Remove default content if we have elements
            if (this.elements.length > 0) {
                const defaultContent = container.querySelector('.default-popup-content');
                if (defaultContent) defaultContent.remove();
            }
            this.elements.forEach(el => this.renderElement(el));
        }

        renderElement(element) {
            const container = document.getElementById('canvas-elements');
            // Remove default content
            const defaultContent = container.querySelector('.default-popup-content');
            if (defaultContent) defaultContent.remove();

            const el = document.createElement('div');
            el.className = 'popup-element';
            el.id = element.id;
            el.dataset.type = element.type;
            el.style.cssText = `position:absolute;left:${element.x}px;top:${element.y}px;cursor:move;`;
            el.innerHTML = this.getElementHTML(element);
            el.addEventListener('click', (e) => { e.stopPropagation(); this.selectElement(element.id); });
            el.addEventListener('mousedown', (e) => this.startDrag(e, element));
            el.addEventListener('dblclick', () => this.editElementContent(element));
            container.appendChild(el);
        }

        getElementHTML(element) {
            const c = element.content || {};
            const s = element.style || {};
            switch (element.type) {
                case 'heading':
                    return `<h3 style="margin:0;font-size:${s.fontSize || 20}px;font-weight:${s.fontWeight || 600};color:${s.color || '#fff'};">${c.text || 'Überschrift'}</h3>`;
                case 'text':
                    return `<p style="margin:0;font-size:${s.fontSize || 14}px;color:${s.color || 'rgba(255,255,255,0.7)'};">${c.text || 'Text'}</p>`;
                case 'flag':
                    return `<span style="font-size:${s.fontSize || 32}px;">${this.getFlag(c.country)}</span>`;
                case 'flag-pair':
                    return `<div style="display:flex;align-items:center;gap:12px;"><span style="font-size:32px;">${this.getFlag(c.from)}</span><span style="font-size:20px;color:rgba(255,255,255,0.5);">→</span><span style="font-size:32px;">${this.getFlag(c.to)}</span></div>`;
                case 'button-stay':
                    return `<button style="padding:${s.padding || 12}px 20px;background:${s.backgroundColor || '#2a2a2a'};color:${s.color || '#fff'};border:none;border-radius:${s.borderRadius || 8}px;font-size:${s.fontSize || 14}px;cursor:pointer;">${c.text || 'Hier bleiben'}</button>`;
                case 'button-switch':
                    return `<button style="padding:${s.padding || 12}px 20px;background:${s.backgroundColor || '#6366f1'};color:${s.color || '#fff'};border:none;border-radius:${s.borderRadius || 8}px;font-size:${s.fontSize || 14}px;cursor:pointer;">${c.text || 'Wechseln'}</button>`;
                case 'button-group':
                    return `<div style="display:flex;gap:${s.gap || 12}px;"><button style="padding:10px 16px;background:#2a2a2a;color:#fff;border:none;border-radius:8px;">${c.stayText || 'Hier bleiben'}</button><button style="padding:10px 16px;background:#6366f1;color:#fff;border:none;border-radius:8px;">${c.switchText || 'Wechseln'}</button></div>`;
                case 'country-select':
                    return `<select style="padding:10px;background:#2a2a2a;color:#fff;border:1px solid #444;border-radius:8px;min-width:150px;"><option>${c.label || 'Land wählen'}</option></select>`;
                case 'image':
                    return `<img src="${c.src || '/admin/assets/img/placeholder.png'}" alt="${c.alt || ''}" style="max-width:100%;border-radius:${s.borderRadius || 0}px;">`;
                case 'close-button':
                    return `<button style="width:32px;height:32px;background:rgba(255,255,255,0.1);border:none;border-radius:50%;color:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>`;
                case 'divider':
                    return `<hr style="width:100%;border:none;border-top:${c.thickness || 1}px solid ${c.color || '#333'};margin:0;">`;
                case 'spacer':
                    return `<div style="height:${c.height || 20}px;width:100%;background:repeating-linear-gradient(45deg,transparent,transparent 5px,rgba(255,255,255,0.03) 5px,rgba(255,255,255,0.03) 10px);"></div>`;
                default:
                    return `<div style="padding:10px;background:#2a2a2a;border-radius:4px;">${element.type}</div>`;
            }
        }

        getFlag(code) { return this.flags[code] || '🏳️'; }

        selectElement(id) {
            document.querySelectorAll('.popup-element').forEach(el => el.classList.remove('selected'));
            const el = document.getElementById(id);
            if (el) {
                el.classList.add('selected');
                this.selectedElement = this.elements.find(e => e.id === id);
                this.showSettings();
            }
        }

        deselectAll() {
            document.querySelectorAll('.popup-element').forEach(el => el.classList.remove('selected'));
            this.selectedElement = null;
            document.getElementById('settings-empty').style.display = 'flex';
            document.getElementById('settings-content').style.display = 'none';
        }

        showSettings() {
            if (!this.selectedElement) return;
            document.getElementById('settings-empty').style.display = 'none';
            document.getElementById('settings-content').style.display = 'block';
            const el = this.selectedElement;
            const typeLabels = { 'heading': 'Überschrift', 'text': 'Text', 'flag': 'Flagge', 'flag-pair': 'Flaggen-Paar', 'button-stay': 'Hier bleiben', 'button-switch': 'Wechseln', 'button-group': 'Button-Gruppe', 'country-select': 'Länder-Auswahl', 'image': 'Bild', 'close-button': 'Schliessen', 'divider': 'Trenner', 'spacer': 'Abstand' };
            document.getElementById('el-type-label').textContent = typeLabels[el.type] || el.type;
            document.getElementById('el-pos-x').value = Math.round(el.x);
            document.getElementById('el-pos-y').value = Math.round(el.y);
            const s = el.style || {};
            document.getElementById('el-bg-color').value = s.backgroundColor === 'transparent' ? '#1a1a1a' : (s.backgroundColor || '#1a1a1a');
            document.getElementById('el-text-color').value = s.color || '#ffffff';
            document.getElementById('el-font-size').value = s.fontSize || 14;
            document.getElementById('el-font-size-value').textContent = (s.fontSize || 14) + 'px';
            document.getElementById('el-border-radius').value = s.borderRadius || 0;
            document.getElementById('el-border-radius-value').textContent = (s.borderRadius || 0) + 'px';
            document.getElementById('el-padding').value = s.padding || 0;
            this.showContentFields(el);
        }

        showContentFields(element) {
            const container = document.getElementById('content-fields');
            const c = element.content || {};
            let html = '';
            switch (element.type) {
                case 'heading': case 'text':
                    html = `<div class="style-row"><label>Text</label><input type="text" id="content-text" value="${c.text || ''}" class="form-input" style="width:100%;"></div>`;
                    break;
                case 'flag':
                    html = `<div class="style-row"><label>Land</label><select id="content-country" class="style-select">${Object.keys(this.flags).map(k => `<option value="${k}" ${c.country === k ? 'selected' : ''}>${this.flags[k]} ${k}</option>`).join('')}</select></div>`;
                    break;
                case 'flag-pair':
                    html = `<div class="style-row"><label>Von</label><select id="content-from" class="style-select">${Object.keys(this.flags).map(k => `<option value="${k}" ${c.from === k ? 'selected' : ''}>${this.flags[k]} ${k}</option>`).join('')}</select></div>
                        <div class="style-row"><label>Nach</label><select id="content-to" class="style-select">${Object.keys(this.flags).map(k => `<option value="${k}" ${c.to === k ? 'selected' : ''}>${this.flags[k]} ${k}</option>`).join('')}</select></div>`;
                    break;
                case 'button-stay': case 'button-switch':
                    html = `<div class="style-row"><label>Button-Text</label><input type="text" id="content-text" value="${c.text || ''}" class="form-input" style="width:100%;"></div>`;
                    break;
                case 'spacer':
                    html = `<div class="style-row"><label>Höhe (px)</label><input type="number" id="content-height" value="${c.height || 20}" min="5" max="100" class="form-input" style="width:80px;"></div>`;
                    break;
            }
            container.innerHTML = html;
            // Bind content field events
            container.querySelectorAll('input, select').forEach(input => {
                input.addEventListener('change', () => this.updateContent(input));
                input.addEventListener('input', () => this.updateContent(input));
            });
        }

        updateContent(input) {
            if (!this.selectedElement) return;
            const field = input.id.replace('content-', '');
            this.selectedElement.content[field] = input.type === 'number' ? parseInt(input.value) : input.value;
            this.refreshElement(this.selectedElement);
            this.triggerAutoSave();
        }

        updateSelectedPosition(axis, value) {
            if (!this.selectedElement) return;
            this.selectedElement[axis] = value;
            const el = document.getElementById(this.selectedElement.id);
            if (el) el.style[axis === 'x' ? 'left' : 'top'] = value + 'px';
            this.triggerAutoSave();
        }

        updateSelectedStyle(prop, value) {
            if (!this.selectedElement) return;
            if (!this.selectedElement.style) this.selectedElement.style = {};
            this.selectedElement.style[prop] = value;
            this.refreshElement(this.selectedElement);
            this.triggerAutoSave();
        }

        refreshElement(element) {
            const el = document.getElementById(element.id);
            if (el) el.innerHTML = this.getElementHTML(element);
        }

        startDrag(e, element) {
            if (e.button !== 0) return;
            e.preventDefault();
            const el = document.getElementById(element.id);
            const startX = e.clientX - element.x;
            const startY = e.clientY - element.y;
            const onMove = (moveEvent) => {
                element.x = Math.max(0, moveEvent.clientX - startX);
                element.y = Math.max(0, moveEvent.clientY - startY);
                el.style.left = element.x + 'px';
                el.style.top = element.y + 'px';
                if (this.selectedElement?.id === element.id) {
                    document.getElementById('el-pos-x').value = Math.round(element.x);
                    document.getElementById('el-pos-y').value = Math.round(element.y);
                }
            };
            const onUp = () => {
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
                this.triggerAutoSave();
            };
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        }

        deleteSelected() {
            if (!this.selectedElement) return;
            const idx = this.elements.findIndex(e => e.id === this.selectedElement.id);
            if (idx > -1) {
                document.getElementById(this.selectedElement.id)?.remove();
                this.elements.splice(idx, 1);
                this.selectedElement = null;
                this.deselectAll();
                this.triggerAutoSave();
            }
        }

        duplicateSelected() {
            if (!this.selectedElement) return;
            const copy = JSON.parse(JSON.stringify(this.selectedElement));
            copy.id = 'el-' + Date.now();
            copy.x += 20;
            copy.y += 20;
            this.elements.push(copy);
            this.renderElement(copy);
            this.selectElement(copy.id);
            this.triggerAutoSave();
        }

        bringToFront() {
            if (!this.selectedElement) return;
            const idx = this.elements.findIndex(e => e.id === this.selectedElement.id);
            if (idx > -1) {
                const [el] = this.elements.splice(idx, 1);
                this.elements.push(el);
                this.renderElements();
                this.selectElement(el.id);
            }
        }

        sendToBack() {
            if (!this.selectedElement) return;
            const idx = this.elements.findIndex(e => e.id === this.selectedElement.id);
            if (idx > 0) {
                const [el] = this.elements.splice(idx, 1);
                this.elements.unshift(el);
                this.renderElements();
                this.selectElement(el.id);
            }
        }

        loadTemplate(name) {
            const templates = {
                'classic': [
                    { id: 'el-1', type: 'heading', x: 120, y: 40, content: { text: 'Falscher Shop?' }, style: { fontSize: 20, fontWeight: 600, color: '#ffffff' } },
                    { id: 'el-2', type: 'text', x: 60, y: 80, content: { text: 'Du befindest dich in der Schweiz.' }, style: { fontSize: 14, color: 'rgba(255,255,255,0.7)' } },
                    { id: 'el-3', type: 'button-stay', x: 70, y: 140, content: { text: 'Hier bleiben' }, style: { backgroundColor: '#2a2a2a', padding: 12, borderRadius: 8 } },
                    { id: 'el-4', type: 'button-switch', x: 200, y: 140, content: { text: 'Wechseln' }, style: { backgroundColor: '#6366f1', padding: 12, borderRadius: 8 } }
                ],
                'flags': [
                    { id: 'el-1', type: 'flag-pair', x: 140, y: 30, content: { from: 'US', to: 'CH' }, style: {} },
                    { id: 'el-2', type: 'heading', x: 110, y: 100, content: { text: 'Falscher Shop?' }, style: { fontSize: 18, fontWeight: 600 } },
                    { id: 'el-3', type: 'text', x: 50, y: 135, content: { text: 'Wechsle zur Schweizer Version.' }, style: { fontSize: 14, color: 'rgba(255,255,255,0.6)' } },
                    { id: 'el-4', type: 'button-switch', x: 110, y: 185, content: { text: 'Zur 🇨🇭 Version' }, style: { backgroundColor: '#6366f1', padding: 12, borderRadius: 8 } },
                    { id: 'el-5', type: 'close-button', x: 360, y: 10, content: {}, style: {} }
                ],
                'minimal': [
                    { id: 'el-1', type: 'text', x: 80, y: 60, content: { text: 'Du bist auf der falschen Seite.' }, style: { fontSize: 15 } },
                    { id: 'el-2', type: 'button-group', x: 100, y: 110, content: { stayText: 'Bleiben', switchText: 'Wechseln' }, style: { gap: 12 } }
                ],
                'dropdown': [
                    { id: 'el-1', type: 'heading', x: 100, y: 30, content: { text: 'Land wählen' }, style: { fontSize: 18, fontWeight: 600 } },
                    { id: 'el-2', type: 'country-select', x: 100, y: 80, content: { label: 'Dein Land' }, style: {} },
                    { id: 'el-3', type: 'button-switch', x: 130, y: 150, content: { text: 'Bestätigen' }, style: { backgroundColor: '#6366f1', padding: 12, borderRadius: 8 } }
                ]
            };
            this.elements = JSON.parse(JSON.stringify(templates[name] || []));
            this.renderElements();
            this.deselectAll();
            this.triggerAutoSave();
        }

        editElementContent(element) {
            if (['heading', 'text', 'button-stay', 'button-switch'].includes(element.type)) {
                const newText = prompt('Text bearbeiten:', element.content.text || '');
                if (newText !== null) {
                    element.content.text = newText;
                    this.refreshElement(element);
                    this.showSettings();
                    this.triggerAutoSave();
                }
            }
        }

        openPreview() {
            document.getElementById('preview-modal').style.display = 'flex';
            const preview = document.getElementById('preview-popup');
            const canvas = document.getElementById('popup-canvas');
            preview.style.background = canvas.style.background || '#1a1a1a';
            preview.style.borderRadius = canvas.style.borderRadius || '12px';
            preview.style.width = canvas.style.width;
            preview.style.height = canvas.style.height;
            preview.style.position = 'relative';
            preview.innerHTML = '';
            this.elements.forEach(el => {
                const div = document.createElement('div');
                div.style.cssText = `position:absolute;left:${el.x}px;top:${el.y}px;`;
                div.innerHTML = this.getElementHTML(el);
                preview.appendChild(div);
            });
        }

        closePreview() { document.getElementById('preview-modal').style.display = 'none'; }

        triggerAutoSave() {
            this.hasUnsavedChanges = true;
            this.updateSaveStatus('unsaved');
            clearTimeout(this.autoSaveTimeout);
            this.autoSaveTimeout = setTimeout(() => this.save(), 2000);
        }

        updateSaveStatus(status) {
            const el = document.getElementById('save-status');
            if (status === 'unsaved') {
                el.innerHTML = '<span class="material-symbols-rounded" style="color:#f59e0b;">pending</span><span class="status-text" style="color:#f59e0b;">Wird gespeichert...</span>';
            } else if (status === 'saving') {
                el.innerHTML = '<span class="material-symbols-rounded rotating" style="color:#6366f1;">sync</span><span class="status-text">Speichern...</span>';
            } else {
                el.innerHTML = '<span class="material-symbols-rounded" style="color:#22c55e;">check_circle</span><span class="status-text" style="color:#22c55e;">Gespeichert</span>';
            }
        }

        async save() {
            this.updateSaveStatus('saving');
            try {
                const response = await fetch('/admin/api/geo_popup.php?action=save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        shop_id: this.data.shopId,
                        elements: this.elements,
                        settings: {
                            backgroundColor: document.getElementById('popup-bg-color')?.value || '#1a1a1a',
                            borderRadius: parseInt(document.getElementById('popup-radius')?.value) || 12,
                            shadow: document.getElementById('popup-shadow')?.value || 'md'
                        }
                    })
                });
                const result = await response.json();
                if (result.success) {
                    this.hasUnsavedChanges = false;
                    this.updateSaveStatus('saved');
                } else throw new Error(result.error);
            } catch (error) {
                console.error('Save error:', error);
                document.getElementById('save-status').innerHTML = '<span class="material-symbols-rounded" style="color:#ef4444;">error</span><span class="status-text" style="color:#ef4444;">Fehler!</span>';
            }
        }
    }

    // Add selected element style
    const style = document.createElement('style');
    style.textContent = `.popup-element.selected{outline:2px solid #6366f1;outline-offset:2px;}.popup-canvas.drag-over{border-color:#6366f1!important;background:rgba(99,102,241,0.05)!important;}.rotating{animation:spin 1s linear infinite;}@keyframes spin{100%{transform:rotate(360deg);}}`;
    document.head.appendChild(style);

    // Initialize
    window.geoPopupEditor = new GeoPopupEditor();
</script>
