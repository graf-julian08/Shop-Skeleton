<?php
/**
 * Mega Menu Editor - Fullpage Visual Editor
 * 
 * Grid-based drag-and-drop editor for mega menus
 * Similar to Figma - with component library, templates, and styling
 */

// Get navigation item ID from URL
$navItemId = intval($_GET['id'] ?? 0);

if (!$navItemId) {
    header('Location: ?page=shop/navigation');
    exit;
}

// Get navigation item details
require_once __DIR__ . '/../../models/NavigationItem.php';
$navItem = NavigationItem::find($navItemId);

if (!$navItem) {
    header('Location: ?page=shop/navigation');
    exit;
}

// Get existing elements for this menu
require_once __DIR__ . '/../../models/MegaMenu.php';

// Get navigation settings to detect menu type
$navSettings = Database::fetch(
    "SELECT * FROM navigation_settings WHERE shop_id = ?",
    [1]
);
$menuType = $navSettings['menu_type'] ?? 'header_links';

// Determine if this is a side menu or header menu
// 'side_menu' is the value used in navigation_settings.php
$isSideMenu = ($menuType === 'side_menu');

// Set canvas defaults based on menu type
if ($isSideMenu) {
    // Side menu: tall vertical layout (like a sidebar flyout)
    // Width: 400px default, max 600px
    // Height: Reasonable height that fits in viewport without cutoff
    $defaultCanvasWidth = 400;
    $defaultCanvasHeight = 550; // Fits in most viewports
} else {
    // Header menu: wide horizontal dropdown
    // Width: 1100px default, Height: ~400px
    $defaultCanvasWidth = 1100;
    $defaultCanvasHeight = 400;
}

// Get templates
$templates = Database::fetchAll("SELECT * FROM mega_menu_templates ORDER BY is_system DESC, name ASC");

// Get elements for this nav item
$elements = Database::fetchAll(
    "SELECT * FROM mega_menu_elements WHERE navigation_item_id = ? ORDER BY z_index",
    [$navItemId]
);

// Decode JSON for each element
foreach ($elements as &$el) {
    $el['content'] = json_decode($el['content_json'] ?? '{}', true);
    $el['style'] = json_decode($el['style_json'] ?? '{}', true);
}
?>

<div class="mega-editor-fullpage">
    <!-- Header -->
    <header class="mega-editor-header">
        <div class="header-left">
            <a href="?page=shop/navigation&tab=main&edit=<?= $navItemId ?>" class="back-link">
                <span class="material-symbols-rounded">arrow_back</span>
                Zurück
            </a>
            <h1>Mega-Menu Editor: <span class="nav-item-label">
                    <?= htmlspecialchars($navItem['label']) ?>
                </span></h1>
        </div>
        <div class="header-center">
            <!-- Empty - breakpoint controls moved to preview toolbar -->
        </div>
        <div class="header-right">
            <!-- Autosave Status -->
            <span class="save-status" id="save-status">
                <span class="material-symbols-rounded">check_circle</span>
                <span class="status-text">Gespeichert</span>
            </span>
            <button type="button" class="btn" id="btn-code" title="Code Editor">
                <span class="material-symbols-rounded">code</span>
                Code
            </button>
            <button type="button" class="btn" id="btn-preview">
                <span class="material-symbols-rounded">visibility</span>
                Vorschau
            </button>
            <button type="button" class="btn" id="btn-save-template">
                <span class="material-symbols-rounded">bookmark_add</span>
                Als Vorlage
            </button>
        </div>
    </header>

    <div class="mega-editor-body">
        <!-- Left Sidebar: Templates & Components -->
        <aside class="mega-editor-sidebar">
            <!-- Templates Section -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">
                    <span class="material-symbols-rounded">dashboard</span>
                    Vorlagen
                </h3>
                <div class="templates-grid">
                    <?php foreach ($templates as $template): ?>
                        <div class="template-card" data-template-id="<?= $template['id'] ?>">
                            <div class="template-preview">
                                <?php if ($template['is_system']): ?>
                                    <span class="template-badge">System</span>
                                <?php endif; ?>
                                <div class="template-thumb">
                                    <span class="material-symbols-rounded">grid_view</span>
                                </div>
                            </div>
                            <span class="template-name">
                                <?= htmlspecialchars($template['name']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($templates) > 6): ?>
                    <button type="button" class="btn btn-sm sidebar-btn load-more-btn" id="btn-load-more-templates">
                        <span class="material-symbols-rounded">expand_more</span>
                        Mehr laden
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-sm sidebar-btn" id="btn-new-template">
                    <span class="material-symbols-rounded">add</span>
                    Leer starten
                </button>
            </div>

            <!-- Components Section -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">
                    <span class="material-symbols-rounded">widgets</span>
                    Komponenten
                </h3>
                <div class="components-grid">
                    <div class="component-item" draggable="true" data-component="text">
                        <span class="material-symbols-rounded">text_fields</span>
                        <span>Text</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="heading">
                        <span class="material-symbols-rounded">title</span>
                        <span>Überschrift</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="image">
                        <span class="material-symbols-rounded">image</span>
                        <span>Bild</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="button">
                        <span class="material-symbols-rounded">smart_button</span>
                        <span>Button</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="linkgroup">
                        <span class="material-symbols-rounded">list</span>
                        <span>Link-Gruppe</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="link">
                        <span class="material-symbols-rounded">link</span>
                        <span>Einzelner Link</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="icon">
                        <span class="material-symbols-rounded">star</span>
                        <span>Icon</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="divider">
                        <span class="material-symbols-rounded">horizontal_rule</span>
                        <span>Trenner</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="spacer">
                        <span class="material-symbols-rounded">height</span>
                        <span>Abstand</span>
                    </div>
                    <div class="component-item" draggable="true" data-component="container">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span>Container</span>
                    </div>
                </div>

            </div>

            <!-- Unplaced Elements Section (for strict breakpoint mode) -->
            <div class="sidebar-section unplaced-section">
                <h3 class="sidebar-title">
                    <span class="material-symbols-rounded">drag_indicator</span>
                    Nicht platziert
                    <span class="breakpoint-badge" id="current-breakpoint-badge">Desktop</span>
                </h3>
                <div class="unplaced-hint">
                    Elemente die auf diesem Breakpoint noch nicht platziert wurden.
                    Ziehe sie auf die Canvas um sie zu platzieren.
                </div>
                <div id="unplaced-elements-tray" class="unplaced-tray">
                    <div class="unplaced-empty">Alle Elemente platziert</div>
                </div>
            </div>
        </aside>

        <!-- Main Canvas -->
        <main class="mega-editor-canvas-wrapper">
            <div class="canvas-toolbar">
                <!-- Responsive Preview Controls -->
                <div class="responsive-controls">
                    <span class="responsive-label">Vorschau:</span>
                    <div class="device-presets">
                        <button type="button" class="device-btn active" data-device="desktop" title="Desktop">
                            <span class="material-symbols-rounded">desktop_windows</span>
                        </button>
                        <button type="button" class="device-btn" data-device="tablet" title="Tablet">
                            <span class="material-symbols-rounded">tablet</span>
                        </button>
                        <button type="button" class="device-btn" data-device="mobile" title="Mobile">
                            <span class="material-symbols-rounded">smartphone</span>
                        </button>
                    </div>
                    <div class="responsive-slider-wrap">
                        <input type="range" id="responsive-slider" min="200" max="<?= $isSideMenu ? '600' : '1920' ?>"
                            value="<?= $defaultCanvasWidth ?>" step="10">
                        <span class="slider-value" id="slider-value"><?= $defaultCanvasWidth ?>px</span>
                    </div>
                </div>
                <div class="canvas-actions">
                    <button type="button" class="icon-btn" id="btn-grid-toggle" title="Raster ein/aus">
                        <span class="material-symbols-rounded">grid_on</span>
                    </button>
                    <button type="button" class="icon-btn" id="btn-snap-toggle" title="Einrasten ein/aus">
                        <span class="material-symbols-rounded">grid_4x4</span>
                    </button>
                    <button type="button" class="icon-btn" id="btn-clear" title="Alles löschen">
                        <span class="material-symbols-rounded">delete_sweep</span>
                    </button>
                </div>
            </div>
            <div class="canvas-scroll">
                <div class="mega-canvas responsive-canvas" id="mega-canvas"
                    data-menu-type="<?= $isSideMenu ? 'side' : 'header' ?>"
                    style="width: <?= $defaultCanvasWidth ?>px; height: <?= $defaultCanvasHeight ?>px;">
                    <div class="canvas-grid"></div>
                    <div class="canvas-elements" id="canvas-elements">
                        <!-- Elements rendered here -->
                    </div>
                </div>
            </div>
        </main>

        <!-- Right Sidebar: Element Settings -->
        <aside class="mega-editor-settings">
            <div class="settings-empty" id="settings-empty">
                <span class="material-symbols-rounded">touch_app</span>
                <p>Wählen Sie ein Element aus, um es zu bearbeiten</p>
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

                <!-- Position Section with Figma-Style Constraints -->
                <div class="settings-section">
                    <div class="section-header">
                        <span class="material-symbols-rounded">open_with</span>
                        <span>Position & Constraints</span>
                    </div>

                    <!-- X/Y Position Inputs -->
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

                    <!-- Constraint System (Figma-Style) -->
                    <div class="constraints-container">
                        <div class="constraint-visual" id="constraint-visual">
                            <!-- Visual constraint preview box -->
                            <div class="constraint-box">
                                <div class="constraint-line constraint-top" data-active="false"></div>
                                <div class="constraint-line constraint-right" data-active="false"></div>
                                <div class="constraint-line constraint-bottom" data-active="false"></div>
                                <div class="constraint-line constraint-left" data-active="false"></div>
                                <div class="constraint-element"></div>
                            </div>
                        </div>
                        <div class="constraint-selects">
                            <div class="constraint-row">
                                <label>Horizontal</label>
                                <select id="el-constraint-h" class="constraint-select">
                                    <option value="left">Links fixiert</option>
                                    <option value="right">Rechts fixiert</option>
                                    <option value="center" selected>Zentriert</option>
                                    <option value="stretch">Dehnen</option>
                                    <option value="scale">Skalieren</option>
                                </select>
                            </div>
                            <div class="constraint-row">
                                <label>Vertikal</label>
                                <select id="el-constraint-v" class="constraint-select">
                                    <option value="top" selected>Oben fixiert</option>
                                    <option value="bottom">Unten fixiert</option>
                                    <option value="center">Zentriert</option>
                                    <option value="stretch">Dehnen</option>
                                    <option value="scale">Skalieren</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Margin from edges (when using fixed constraints) -->
                    <div class="margin-controls" id="margin-controls">
                        <div class="margin-row">
                            <div class="margin-field" id="margin-left-field">
                                <span class="material-symbols-rounded">west</span>
                                <input type="number" id="el-margin-left" value="0" min="0">
                            </div>
                            <div class="margin-field" id="margin-right-field">
                                <span class="material-symbols-rounded">east</span>
                                <input type="number" id="el-margin-right" value="0" min="0">
                            </div>
                        </div>
                        <div class="margin-row">
                            <div class="margin-field" id="margin-top-field">
                                <span class="material-symbols-rounded">north</span>
                                <input type="number" id="el-margin-top" value="0" min="0">
                            </div>
                            <div class="margin-field" id="margin-bottom-field">
                                <span class="material-symbols-rounded">south</span>
                                <input type="number" id="el-margin-bottom" value="0" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Lock Toggle -->
                    <div class="lock-control">
                        <label class="toggle-label">
                            <input type="checkbox" id="el-lock-position">
                            <span class="toggle-switch"></span>
                            <span class="material-symbols-rounded">lock</span>
                            <span>Position sperren</span>
                        </label>
                    </div>
                </div>

                <!-- Size Section -->
                <div class="settings-section">
                    <div class="section-header">
                        <span class="material-symbols-rounded">aspect_ratio</span>
                        <span>Grösse</span>
                    </div>

                    <!-- Width Control -->
                    <div class="size-control">
                        <div class="size-header">
                            <label>Breite</label>
                            <div class="unit-toggle" id="width-unit-toggle">
                                <button type="button" class="unit-btn" data-unit="px">px</button>
                                <button type="button" class="unit-btn active" data-unit="%">%</button>
                                <button type="button" class="unit-btn" data-unit="auto">auto</button>
                            </div>
                        </div>
                        <div class="slider-row" id="width-slider-row">
                            <input type="range" id="el-width-slider" min="10" max="100" value="50"
                                class="modern-slider">
                            <input type="number" id="el-width-value" value="50" min="10" max="100" class="value-input">
                        </div>
                    </div>

                    <!-- Height Control -->
                    <div class="size-control">
                        <div class="size-header">
                            <label>Höhe</label>
                            <div class="unit-toggle" id="height-unit-toggle">
                                <button type="button" class="unit-btn" data-unit="px">px</button>
                                <button type="button" class="unit-btn" data-unit="%">%</button>
                                <button type="button" class="unit-btn active" data-unit="auto">auto</button>
                            </div>
                        </div>
                        <div class="slider-row" id="height-slider-row">
                            <input type="range" id="el-height-slider" min="20" max="500" value="60"
                                class="modern-slider">
                            <input type="number" id="el-height-value" value="60" min="20" max="500" class="value-input">
                        </div>
                    </div>
                </div>

                <!-- Content Section (dynamic) -->
                <div class="settings-section" id="content-settings">
                    <!-- Filled dynamically based on element type -->
                </div>

                <!-- Styling Section - Expanded -->
                <div class="settings-section">
                    <div class="section-header">
                        <span class="material-symbols-rounded">palette</span>
                        <span>Styling</span>
                    </div>

                    <!-- Colors -->
                    <div class="style-subsection">
                        <div class="subsection-label">Farben</div>
                        <div class="style-grid">
                            <div class="style-item">
                                <label>Hintergrund</label>
                                <div class="color-input-wrap">
                                    <input type="color" id="el-bg-color" value="#ffffff">
                                    <input type="text" id="bg-hex" class="color-text" value="#ffffff" maxlength="7">
                                </div>
                            </div>
                            <div class="style-item">
                                <label>Textfarbe</label>
                                <div class="color-input-wrap">
                                    <input type="color" id="el-text-color" value="#333333">
                                    <input type="text" id="text-hex" class="color-text" value="#333333" maxlength="7">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Typography -->
                    <div class="style-subsection">
                        <div class="subsection-label">Typografie</div>
                        <div class="style-grid">
                            <div class="style-item full">
                                <label>Schriftgrösse</label>
                                <div class="slider-input-combo">
                                    <input type="range" id="el-font-size" min="10" max="72" value="14" class="modern-slider">
                                    <input type="number" id="el-font-size-value" value="14" min="10" max="72" class="value-input-sm">
                                    <span class="unit">px</span>
                                </div>
                            </div>
                            <div class="style-item">
                                <label>Gewicht</label>
                                <select id="el-font-weight" class="style-select">
                                    <option value="300">Light</option>
                                    <option value="400" selected>Regular</option>
                                    <option value="500">Medium</option>
                                    <option value="600">Semibold</option>
                                    <option value="700">Bold</option>
                                </select>
                            </div>
                            <div class="style-item">
                                <label>Ausrichtung</label>
                                <div class="icon-toggle-group" id="el-text-align">
                                    <button type="button" class="icon-toggle active" data-value="left" title="Links">
                                        <span class="material-symbols-rounded">format_align_left</span>
                                    </button>
                                    <button type="button" class="icon-toggle" data-value="center" title="Zentriert">
                                        <span class="material-symbols-rounded">format_align_center</span>
                                    </button>
                                    <button type="button" class="icon-toggle" data-value="right" title="Rechts">
                                        <span class="material-symbols-rounded">format_align_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Spacing -->
                    <div class="style-subsection">
                        <div class="subsection-label">Abstände</div>
                        <div class="spacing-grid">
                            <div class="spacing-item">
                                <label>Padding</label>
                                <div class="spacing-inputs">
                                    <input type="number" id="el-padding-top" value="10" min="0" placeholder="↑">
                                    <input type="number" id="el-padding-right" value="10" min="0" placeholder="→">
                                    <input type="number" id="el-padding-bottom" value="10" min="0" placeholder="↓">
                                    <input type="number" id="el-padding-left" value="10" min="0" placeholder="←">
                                </div>
                                <label class="toggle-label link-values">
                                    <input type="checkbox" id="el-padding-link" checked>
                                    <span class="material-symbols-rounded">link</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Border -->
                    <div class="style-subsection">
                        <div class="subsection-label">Rahmen</div>
                        <div class="style-grid">
                            <div class="style-item">
                                <label>Breite</label>
                                <div class="slider-input-combo">
                                    <input type="range" id="el-border-width" min="0" max="10" value="0" class="modern-slider">
                                    <input type="number" id="el-border-width-value" value="0" min="0" max="10" class="value-input-sm">
                                    <span class="unit">px</span>
                                </div>
                            </div>
                            <div class="style-item">
                                <label>Farbe</label>
                                <div class="color-input-wrap">
                                    <input type="color" id="el-border-color" value="#e5e7eb">
                                    <input type="text" id="border-hex" class="color-text" value="#e5e7eb" maxlength="7">
                                </div>
                            </div>
                            <div class="style-item">
                                <label>Stil</label>
                                <select id="el-border-style" class="style-select">
                                    <option value="solid" selected>Durchgezogen</option>
                                    <option value="dashed">Gestrichelt</option>
                                    <option value="dotted">Gepunktet</option>
                                    <option value="none">Keine</option>
                                </select>
                            </div>
                            <div class="style-item full">
                                <label>Radius</label>
                                <div class="slider-input-combo">
                                    <input type="range" id="el-border-radius" min="0" max="50" value="4" class="modern-slider">
                                    <input type="number" id="el-border-radius-value" value="4" min="0" max="100" class="value-input-sm">
                                    <span class="unit">px</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shadow -->
                    <div class="style-subsection">
                        <div class="subsection-label">Schatten</div>
                        <div class="shadow-presets" id="shadow-presets">
                            <button type="button" class="shadow-preset active" data-shadow="none" title="Kein Schatten">
                                <div class="shadow-preview no-shadow"></div>
                            </button>
                            <button type="button" class="shadow-preset" data-shadow="sm" title="Klein">
                                <div class="shadow-preview shadow-sm"></div>
                            </button>
                            <button type="button" class="shadow-preset" data-shadow="md" title="Mittel">
                                <div class="shadow-preview shadow-md"></div>
                            </button>
                            <button type="button" class="shadow-preset" data-shadow="lg" title="Gross">
                                <div class="shadow-preview shadow-lg"></div>
                            </button>
                            <button type="button" class="shadow-preset" data-shadow="xl" title="Extra Gross">
                                <div class="shadow-preview shadow-xl"></div>
                            </button>
                        </div>
                        <input type="hidden" id="el-box-shadow" value="none">
                    </div>

                    <!-- Opacity & Z-Index -->
                    <div class="style-subsection">
                        <div class="subsection-label">Effekte</div>
                        <div class="style-grid">
                            <div class="style-item full">
                                <label>Deckkraft</label>
                                <div class="slider-input-combo">
                                    <input type="range" id="el-opacity" min="0" max="100" value="100" class="modern-slider">
                                    <input type="number" id="el-opacity-value" value="100" min="0" max="100" class="value-input-sm">
                                    <span class="unit">%</span>
                                </div>
                            </div>
                            <div class="style-item">
                                <label>Z-Index</label>
                                <input type="number" id="el-z-index" value="0" min="-100" max="100" class="style-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
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
        </aside>

    </div>

    <!-- Keyboard hints (hover near bottom edge) -->
    <div class="keyboard-hints-zone">
        <div class="keyboard-hints-bar">
            <span><kbd>Del</kbd> Löschen</span>
            <span><kbd>⌘D</kbd> Duplizieren</span>
            <span><kbd>⌘C</kbd> Kopieren</span>
            <span><kbd>⌘V</kbd> Einfügen</span>
            <span><kbd>⌘Z</kbd> Rückgängig</span>
            <span><kbd>⌘S</kbd> Speichern</span>
            <span><kbd>Esc</kbd> Abwählen</span>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="preview-modal" style="display: none;">
        <div class="preview-modal-backdrop" onclick="window.megaEditor.closePreview()"></div>
        <div class="preview-modal-content">
            <div class="preview-modal-header">
                <h3>
                    <span class="material-symbols-rounded">visibility</span>
                    Vorschau: Mega-Menu für "<?= htmlspecialchars($navItem['label']) ?>"
                </h3>
                <button type="button" class="modal-close" onclick="window.megaEditor.closePreview()">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
            <div class="preview-modal-body">
                <div class="preview-container" id="preview-container">
                    <!-- Preview rendered here -->
                </div>
            </div>
            <div class="preview-modal-footer">
                <span class="preview-hint">
                    <span class="material-symbols-rounded">info</span>
                    So wird das Mega-Menu im Shop angezeigt, wenn der Benutzer über
                    "<?= htmlspecialchars($navItem['label']) ?>" hovert.
                </span>
                <button type="button" class="btn" onclick="window.megaEditor.closePreview()">Schließen</button>
            </div>
        </div>
    </div>

    <!-- Save as Template Modal -->
    <div id="save-template-modal" class="modal-overlay" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3>
                    <span class="material-symbols-rounded">bookmark_add</span>
                    Als Vorlage speichern
                </h3>
                <button type="button" class="modal-close" id="close-template-modal">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="template-name-input">Vorlagenname</label>
                    <input type="text" id="template-name-input" class="form-control"
                        placeholder="z.B. Mein Mega-Menu Design" autocomplete="off">
                    <span class="form-hint">Geben Sie einen eindeutigen Namen für diese Vorlage ein</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" id="cancel-template-save">Abbrechen</button>
                <button type="button" class="btn btn-primary" id="confirm-template-save">
                    <span class="material-symbols-rounded">save</span>
                    Vorlage speichern
                </button>
            </div>
        </div>
    </div>

    <!-- Save Feedback Toast -->
    <div id="save-toast" class="save-toast">
        <span class="toast-icon material-symbols-rounded">check_circle</span>
        <span class="toast-text">Mega-Menu gespeichert!</span>
    </div>

    <!-- Code Editor Panel -->
    <div id="code-editor-panel" class="code-editor-panel">
        <div class="code-editor-header">
            <div class="code-tabs">
                <button type="button" class="code-tab active" data-tab="html">HTML</button>
                <button type="button" class="code-tab" data-tab="css">CSS</button>
            </div>
            <div class="code-actions">
                <button type="button" class="btn btn-sm" id="btn-apply-code">
                    <span class="material-symbols-rounded">play_arrow</span>
                    Anwenden
                </button>
                <button type="button" class="btn btn-sm" id="btn-close-code">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
        </div>
        <div class="code-editor-body">
            <textarea id="code-html" class="code-textarea" placeholder="<!-- HTML wird hier angezeigt -->"
                spellcheck="false"></textarea>
            <textarea id="code-css" class="code-textarea" style="display:none;"
                placeholder="/* CSS wird hier angezeigt */" spellcheck="false"></textarea>
        </div>
    </div>
</div>

<?php
// Get CMS pages for link selector (handle missing columns gracefully)
try {
    $cmsPages = Database::fetchAll("SELECT id, title, slug FROM cms_pages ORDER BY title ASC");
} catch (Exception $e) {
    $cmsPages = [];
}

// Get categories for link selector (handle missing table gracefully)
try {
    $categories = Database::fetchAll("SELECT id, name, slug FROM categories ORDER BY name ASC");
} catch (Exception $e) {
    $categories = [];
}
?>

<!-- Hidden data for JavaScript -->
<script>
    window.MEGA_EDITOR_DATA = {
        navItemId: <?= $navItemId ?>,
        navItemLabel: <?= json_encode($navItem['label']) ?>,
        elements: <?= json_encode($elements) ?>,
        templates: <?= json_encode($templates) ?>,
        pages: <?= json_encode($cmsPages) ?>,
        categories: <?= json_encode($categories) ?>,
        menuType: <?= json_encode($menuType) ?>,
        isSideMenu: <?= $isSideMenu ? 'true' : 'false' ?>,
        defaultCanvasWidth: <?= $defaultCanvasWidth ?>,
        defaultCanvasHeight: <?= $defaultCanvasHeight ?>
    };
</script>

<style>
    /* ========== FULLPAGE EDITOR STYLES ========== */

    .mega-editor-fullpage {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        background: var(--bg-main, #0d0d0d);
    }

    /* Header */
    .mega-editor-header {
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
    }

    .back-link:hover {
        color: var(--text, #fff);
    }

    .mega-editor-header h1 {
        margin: 0;
        font-size: 16px;
        font-weight: 500;
    }

    .nav-item-label {
        color: var(--accent, #6366f1);
    }

    .menu-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 6px;
        margin-left: 16px;
    }

    .menu-type-badge .material-symbols-rounded {
        font-size: 16px;
    }

    .menu-type-badge.badge-header {
        background: rgba(99, 102, 241, 0.15);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    .menu-type-badge.badge-side {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .header-center {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .breakpoint-tabs {
        display: flex;
        gap: 4px;
        background: var(--bg-lighter, #222);
        padding: 4px;
        border-radius: 8px;
    }

    .breakpoint-tab {
        padding: 8px 12px;
        background: transparent;
        border: none;
        color: var(--text-muted, #888);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .breakpoint-tab:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary, #fff);
    }

    .breakpoint-tab.active {
        background: var(--accent, #6366f1);
        color: white;
    }

    .breakpoint-tab .material-symbols-rounded {
        font-size: 20px;
    }

    .header-right {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .save-status {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border-radius: 6px;
        font-size: 12px;
        transition: all 0.3s ease;
    }

    .save-status.saving {
        background: rgba(251, 191, 36, 0.15);
        color: #fbbf24;
    }

    .save-status.saving .material-symbols-rounded {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .save-status .material-symbols-rounded {
        font-size: 16px;
    }

    /* Body Layout */
    .mega-editor-body {
        flex: 1;
        display: flex;
        overflow: hidden;
    }

    /* Left Sidebar */
    .mega-editor-sidebar {
        width: 240px;
        background: var(--bg-card, #1a1a1a);
        border-right: 1px solid var(--border-color, #333);
        overflow-y: auto;
        flex-shrink: 0;
    }

    .sidebar-section {
        padding: 16px;
        border-bottom: 1px solid var(--border-color, #333);
    }

    .sidebar-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 12px 0;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted, #888);
    }

    .sidebar-title .material-symbols-rounded {
        font-size: 18px;
    }

    /* Templates Grid */
    .templates-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }

    .template-card {
        cursor: pointer;
        border: 2px solid var(--border-color, #333);
        border-radius: 6px;
        overflow: hidden;
        transition: all 0.15s;
    }

    .template-card:hover {
        border-color: var(--accent, #6366f1);
    }

    .template-preview {
        position: relative;
        aspect-ratio: 4/3;
        background: var(--bg-lighter, #222);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .template-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        padding: 2px 6px;
        font-size: 9px;
        background: var(--accent, #6366f1);
        color: white;
        border-radius: 3px;
    }

    .template-thumb .material-symbols-rounded {
        font-size: 24px;
        color: var(--text-muted, #666);
    }

    .template-name {
        display: block;
        padding: 6px;
        font-size: 10px;
        text-align: center;
        color: var(--text-secondary, #aaa);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-btn {
        width: 100%;
    }

    /* Components Grid */
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
        gap: 4px;
        padding: 12px 8px;
        background: var(--bg-lighter, #222);
        border: 1px solid var(--border-color, #333);
        border-radius: 6px;
        cursor: grab;
        transition: all 0.15s;
    }

    .component-item:hover {
        background: var(--bg-hover, #2a2a2a);
        border-color: var(--accent, #6366f1);
    }

    .component-item .material-symbols-rounded {
        font-size: 24px;
        color: var(--accent, #6366f1);
    }

    .component-item span:last-child {
        font-size: 10px;
        color: var(--text-muted, #888);
    }

    /* Unplaced Elements Tray */
    .unplaced-section {
        background: rgba(251, 191, 36, 0.05);
        border-top: 1px solid rgba(251, 191, 36, 0.2);
    }

    .breakpoint-badge {
        margin-left: auto;
        padding: 2px 8px;
        font-size: 9px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: var(--accent, #6366f1);
        color: white;
        border-radius: 4px;
    }

    .unplaced-hint {
        font-size: 11px;
        color: var(--text-muted, #666);
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .unplaced-tray {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-height: 60px;
    }

    .unplaced-empty {
        padding: 16px;
        text-align: center;
        font-size: 11px;
        color: var(--text-muted, #555);
        background: var(--bg-lighter, #222);
        border: 1px dashed var(--border-color, #333);
        border-radius: 6px;
    }

    .unplaced-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        background: var(--bg-lighter, #222);
        border: 1px solid var(--border-color, #333);
        border-radius: 6px;
        cursor: grab;
        transition: all 0.15s;
    }

    .unplaced-item:hover {
        background: var(--bg-hover, #2a2a2a);
        border-color: var(--accent, #6366f1);
    }

    .unplaced-item .material-symbols-rounded {
        font-size: 18px;
        color: var(--accent, #6366f1);
    }

    .unplaced-label {
        font-size: 12px;
        color: var(--text-secondary, #aaa);
    }

    /* Canvas Wrapper */
    .mega-editor-canvas-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .canvas-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        background: var(--bg-lighter, #151515);
        border-bottom: 1px solid var(--border-color, #333);
    }

    /* Responsive Controls */
    .responsive-controls {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .responsive-label {
        font-size: 12px;
        color: var(--text-muted, #888);
        font-weight: 500;
    }

    .device-presets {
        display: flex;
        gap: 4px;
        background: var(--bg-card, #1a1a1a);
        padding: 4px;
        border-radius: 8px;
    }

    .device-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 32px;
        background: transparent;
        border: none;
        border-radius: 6px;
        color: var(--text-muted, #888);
        cursor: pointer;
        transition: all 0.15s;
    }

    .device-btn:hover {
        background: var(--bg-lighter, #222);
        color: var(--text, #fff);
    }

    .device-btn.active {
        background: #10b981;
        color: #fff;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
    }

    .device-btn .material-symbols-rounded {
        font-size: 20px;
    }

    .responsive-slider-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #responsive-slider {
        width: 180px;
        height: 4px;
        -webkit-appearance: none;
        appearance: none;
        background: var(--bg-card, #1a1a1a);
        border-radius: 4px;
        cursor: pointer;
    }

    #responsive-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 16px;
        height: 16px;
        background: var(--accent, #6366f1);
        border-radius: 50%;
        cursor: grab;
        transition: transform 0.1s;
    }

    #responsive-slider::-webkit-slider-thumb:hover {
        transform: scale(1.2);
    }

    .slider-value {
        font-size: 12px;
        font-weight: 600;
        color: var(--accent, #6366f1);
        min-width: 70px;
        text-align: center;
        background: var(--bg-card, #1a1a1a);
        padding: 4px 8px;
        border-radius: 4px;
    }

    .zoom-indicator {
        font-size: 11px;
        font-weight: 600;
        color: #f59e0b;
        background: rgba(245, 158, 11, 0.15);
        padding: 4px 8px;
        border-radius: 4px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .zoom-indicator.visible {
        opacity: 1;
    }

    .canvas-actions {
        display: flex;
        gap: 4px;
    }

    .canvas-scroll {
        flex: 1;
        overflow: hidden;
        /* NO horizontal scrollbar */
        overflow-y: auto;
        /* Allow vertical scroll if needed */
        padding: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(circle at center, #1a1a1a 0%, #0d0d0d 100%);
    }

    /* Canvas */
    .mega-canvas {
        position: relative;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        flex-shrink: 0;
        /* Smooth transitions for size changes */
        transition: width 0.3s ease, height 0.3s ease, min-height 0.3s ease;
    }

    .canvas-grid {
        position: absolute;
        inset: 0;
        pointer-events: none;
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
        background-size: 20px 20px;
    }

    .canvas-elements {
        position: absolute;
        inset: 0;
    }

    /* Responsive Canvas Behavior - FIGMA-STYLE ABSOLUTE POSITIONING */
    .responsive-canvas {
        /* No transition for instant zoom response */
    }

    /* Canvas elements container - allows absolute positioning of children */
    .responsive-canvas .canvas-elements {
        position: absolute;
        inset: 0;
        /* NO flexbox - elements are positioned absolutely via left/top */
    }

    /* Canvas Element - ABSOLUTE POSITIONING for pixel-precise placement */
    /* These rules intentionally do NOT override left/top/width/height set by JS */
    .responsive-canvas .canvas-element {
        /* Inherit absolute positioning from .canvas-element base rule */
        /* Allow JavaScript to control left, top, width, height directly */
    }

    /* Canvas Element */
    .canvas-element {
        position: absolute;
        border: 2px solid transparent;
        cursor: move;
        transition: border-color 0.15s;
        box-sizing: border-box;
    }

    .canvas-element:hover {
        border-color: rgba(99, 102, 241, 0.5);
    }

    .canvas-element.selected {
        border-color: var(--accent, #6366f1);
    }

    .canvas-element.selected::before {
        content: '';
        position: absolute;
        inset: -6px;
        border: 1px dashed var(--accent, #6366f1);
        pointer-events: none;
    }

    /* Resize handles - all 8 directions */
    .resize-handle {
        position: absolute;
        background: var(--accent, #6366f1);
        border: 2px solid white;
        border-radius: 2px;
        z-index: 10;
        opacity: 0;
        transition: opacity 0.15s ease;
    }

    .canvas-element:hover .resize-handle,
    .canvas-element.selected .resize-handle {
        opacity: 1;
    }

    /* Corner handles - 10x10px squares */
    .resize-handle.nw,
    .resize-handle.ne,
    .resize-handle.sw,
    .resize-handle.se {
        width: 10px;
        height: 10px;
    }

    .resize-handle.nw {
        top: -5px;
        left: -5px;
        cursor: nw-resize;
    }

    .resize-handle.ne {
        top: -5px;
        right: -5px;
        cursor: ne-resize;
    }

    .resize-handle.sw {
        bottom: -5px;
        left: -5px;
        cursor: sw-resize;
    }

    .resize-handle.se {
        bottom: -5px;
        right: -5px;
        cursor: se-resize;
    }

    /* Edge handles - thin bars */
    .resize-handle.n {
        top: -4px;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 8px;
        cursor: n-resize;
    }

    .resize-handle.s {
        bottom: -4px;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 8px;
        cursor: s-resize;
    }

    .resize-handle.e {
        right: -4px;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 30px;
        cursor: e-resize;
    }

    .resize-handle.w {
        left: -4px;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 30px;
        cursor: w-resize;
    }

    /* Element type styles */
    .element-text {
        padding: 10px;
        font-size: 14px;
        color: #333;
    }

    .element-image {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f5;
    }

    .element-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }

    .element-image-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: #999;
    }

    .element-image-placeholder .material-symbols-rounded {
        font-size: 32px;
    }

    .element-linkgroup {
        padding: 10px;
    }

    .element-linkgroup-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
        color: #333;
    }

    .element-linkgroup-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .element-linkgroup-links li {
        padding: 4px 0;
    }

    .element-linkgroup-links a {
        color: #666;
        text-decoration: none;
        font-size: 13px;
    }

    .element-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .element-icon .material-symbols-rounded {
        font-size: 32px;
        color: #333;
    }

    .element-heading {
        padding: 10px;
    }

    .element-heading h1,
    .element-heading h2,
    .element-heading h3,
    .element-heading h4 {
        margin: 0;
        color: #333;
        font-weight: 700;
        line-height: 1.2;
    }

    .element-heading h2 {
        font-size: 20px;
    }

    .element-button {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
    }

    .element-button button {
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .element-button-primary button {
        background: #10b981;
        color: #fff;
    }

    .element-button-secondary button {
        background: #6366f1;
        color: #fff;
    }

    .element-button-outline button {
        background: transparent;
        border: 2px solid #333;
        color: #333;
    }

    .element-spacer {
        width: 100%;
        height: 100%;
        background: repeating-linear-gradient(45deg,
                #f0f0f0,
                #f0f0f0 5px,
                transparent 5px,
                transparent 10px);
        border: 1px dashed #ccc;
        border-radius: 4px;
    }

    .element-container {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 16px;
        background: rgba(99, 102, 241, 0.05);
    }

    .element-container-inner {
        color: #888;
        text-align: center;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Right Settings Sidebar */
    .mega-editor-settings {
        width: 280px;
        background: var(--bg-card, #1a1a1a);
        border-left: 1px solid var(--border-color, #333);
        overflow-y: auto;
        flex-shrink: 0;
    }

    .settings-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 300px;
        text-align: center;
        color: var(--text-muted, #666);
        padding: 20px;
    }

    .settings-empty .material-symbols-rounded {
        font-size: 48px;
        opacity: 0.4;
        margin-bottom: 12px;
    }

    .settings-content {
        padding: 16px;
    }

    .settings-group {
        margin-bottom: 20px;
    }

    .settings-label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted, #888);
        margin-bottom: 8px;
    }

    .settings-row {
        display: flex;
        gap: 8px;
        margin-bottom: 8px;
    }

    .settings-field {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .settings-field.full {
        flex-basis: 100%;
    }

    .settings-field label {
        font-size: 11px;
        color: var(--text-muted, #888);
        min-width: 16px;
    }

    .settings-field input[type="number"],
    .settings-field input[type="text"] {
        flex: 1;
        padding: 6px 8px;
        background: var(--bg-lighter, #222);
        border: 1px solid var(--border-color, #333);
        border-radius: 4px;
        color: var(--text, #fff);
        font-size: 12px;
    }

    .settings-field input[type="color"] {
        width: 100%;
        height: 32px;
        padding: 2px;
        border: 1px solid var(--border-color, #333);
        border-radius: 4px;
        background: var(--bg-lighter, #222);
        cursor: pointer;
    }

    .settings-field span {
        font-size: 11px;
        color: var(--text-muted, #666);
    }

    .settings-field .unit-select {
        padding: 4px 6px;
        background: var(--bg-lighter, #222);
        border: 1px solid var(--border-color, #333);
        border-radius: 4px;
        color: var(--text, #fff);
        font-size: 11px;
        cursor: pointer;
        min-width: 50px;
    }

    .settings-field .unit-select:hover {
        border-color: var(--accent, #6366f1);
    }

    .settings-field .unit-select:focus {
        outline: none;
        border-color: var(--accent, #6366f1);
    }

    /* ========== PREMIUM SETTINGS PANEL ========== */

    .settings-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.1));
        border-bottom: 1px solid rgba(99, 102, 241, 0.3);
        margin: -16px -16px 16px -16px;
    }

    .element-type-icon {
        font-size: 24px;
        color: var(--accent, #6366f1);
    }

    .element-type-label {
        flex: 1;
        font-size: 14px;
        font-weight: 600;
        color: var(--text, #fff);
    }

    .icon-btn-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(239, 68, 68, 0.1);
        border: none;
        border-radius: 6px;
        color: #ef4444;
        cursor: pointer;
        transition: all 0.2s;
    }

    .icon-btn-sm:hover {
        background: rgba(239, 68, 68, 0.2);
    }

    .icon-btn-sm .material-symbols-rounded {
        font-size: 18px;
    }

    /* Section Headers */
    .settings-section {
        margin-bottom: 20px;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color, #333);
    }

    .section-header .material-symbols-rounded {
        font-size: 18px;
        color: var(--accent, #6366f1);
    }

    .section-header span:last-child {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted, #888);
    }

    /* ========== FIGMA-STYLE CONSTRAINT SYSTEM ========== */

    /* Position Inputs */
    .position-inputs {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }

    .position-field {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-lighter, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-radius: 6px;
        padding: 8px 10px;
    }

    .position-field label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted, #888);
        text-transform: uppercase;
    }

    .pos-input {
        flex: 1;
        background: transparent;
        border: none;
        color: var(--text, #fff);
        font-size: 13px;
        font-weight: 500;
        outline: none;
        width: 60px;
    }

    .pos-input::-webkit-inner-spin-button,
    .pos-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
    }

    .position-field .unit {
        font-size: 11px;
        color: var(--text-muted, #666);
    }

    /* Constraint Container */
    .constraints-container {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
        padding: 12px;
        background: var(--bg-lighter, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-radius: 8px;
    }

    /* Constraint Visual Preview (Figma-style) */
    .constraint-visual {
        width: 80px;
        height: 80px;
        position: relative;
    }

    .constraint-box {
        width: 100%;
        height: 100%;
        border: 2px dashed var(--border-color, #444);
        border-radius: 6px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .constraint-element {
        width: 24px;
        height: 16px;
        background: var(--accent, #6366f1);
        border-radius: 3px;
        position: relative;
        z-index: 1;
        transition: all 0.2s ease;
    }

    .constraint-line {
        position: absolute;
        background: transparent;
        transition: all 0.2s ease;
    }

    .constraint-line.constraint-top {
        top: 0;
        left: 50%;
        width: 2px;
        height: calc(50% - 8px);
        transform: translateX(-50%);
    }

    .constraint-line.constraint-bottom {
        bottom: 0;
        left: 50%;
        width: 2px;
        height: calc(50% - 8px);
        transform: translateX(-50%);
    }

    .constraint-line.constraint-left {
        left: 0;
        top: 50%;
        width: calc(50% - 12px);
        height: 2px;
        transform: translateY(-50%);
    }

    .constraint-line.constraint-right {
        right: 0;
        top: 50%;
        width: calc(50% - 12px);
        height: 2px;
        transform: translateY(-50%);
    }

    .constraint-line[data-active="true"] {
        background: var(--accent, #6366f1);
    }

    /* Constraint Selects */
    .constraint-selects {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .constraint-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .constraint-row label {
        font-size: 11px;
        color: var(--text-muted, #888);
        width: 60px;
        flex-shrink: 0;
    }

    .constraint-select {
        flex: 1;
        background: var(--bg-main, #0d0d0d);
        border: 1px solid var(--border-color, #444);
        border-radius: 4px;
        color: var(--text, #fff);
        font-size: 12px;
        padding: 6px 8px;
        cursor: pointer;
        outline: none;
    }

    .constraint-select:hover {
        border-color: var(--accent, #6366f1);
    }

    .constraint-select:focus {
        border-color: var(--accent, #6366f1);
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }

    /* Margin Controls */
    .margin-controls {
        margin-bottom: 16px;
        padding: 12px;
        background: var(--bg-lighter, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-radius: 8px;
    }

    .margin-row {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .margin-row:last-child {
        margin-bottom: 0;
    }

    .margin-field {
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--bg-main, #0d0d0d);
        border: 1px solid var(--border-color, #444);
        border-radius: 4px;
        padding: 4px 8px;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .margin-field.active {
        opacity: 1;
        border-color: var(--accent, #6366f1);
    }

    .margin-field .material-symbols-rounded {
        font-size: 14px;
        color: var(--text-muted, #666);
    }

    .margin-field input {
        width: 40px;
        background: transparent;
        border: none;
        color: var(--text, #fff);
        font-size: 12px;
        text-align: center;
        outline: none;
    }

    .margin-field input::-webkit-inner-spin-button,
    .margin-field input::-webkit-outer-spin-button {
        -webkit-appearance: none;
    }

    /* ========== EXPANDED STYLING SECTION ========== */

    .style-subsection {
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color, #333);
    }

    .style-subsection:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .subsection-label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted, #666);
        margin-bottom: 10px;
    }

    /* Slider + Input Combo */
    .slider-input-combo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .slider-input-combo .modern-slider {
        flex: 1;
    }

    .value-input-sm {
        width: 50px;
        background: var(--bg-lighter, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-radius: 4px;
        color: var(--text, #fff);
        font-size: 12px;
        padding: 5px 6px;
        text-align: center;
        outline: none;
    }

    .value-input-sm:focus {
        border-color: var(--accent, #6366f1);
    }

    .slider-input-combo .unit {
        font-size: 11px;
        color: var(--text-muted, #666);
        width: 20px;
    }

    /* Style Select */
    .style-select {
        width: 100%;
        background: var(--bg-lighter, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-radius: 4px;
        color: var(--text, #fff);
        font-size: 12px;
        padding: 7px 10px;
        cursor: pointer;
        outline: none;
    }

    .style-select:hover {
        border-color: rgba(99, 102, 241, 0.5);
    }

    .style-select:focus {
        border-color: var(--accent, #6366f1);
    }

    /* Style Input */
    .style-input {
        width: 100%;
        background: var(--bg-lighter, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-radius: 4px;
        color: var(--text, #fff);
        font-size: 12px;
        padding: 7px 10px;
        outline: none;
    }

    .style-input:focus {
        border-color: var(--accent, #6366f1);
    }

    /* Color Input with Text */
    .color-input-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .color-input-wrap input[type="color"] {
        width: 28px;
        height: 28px;
        border: 2px solid var(--border-color, #444);
        border-radius: 4px;
        cursor: pointer;
        padding: 0;
        background: transparent;
    }

    .color-text {
        flex: 1;
        background: var(--bg-lighter, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-radius: 4px;
        color: var(--text, #fff);
        font-size: 11px;
        font-family: 'Monaco', 'Menlo', monospace;
        padding: 5px 8px;
        text-transform: uppercase;
        outline: none;
    }

    .color-text:focus {
        border-color: var(--accent, #6366f1);
    }

    /* Icon Toggle Group */
    .icon-toggle-group {
        display: flex;
        gap: 2px;
        background: var(--bg-lighter, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-radius: 6px;
        padding: 3px;
    }

    .icon-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 26px;
        background: transparent;
        border: none;
        border-radius: 4px;
        color: var(--text-muted, #666);
        cursor: pointer;
        transition: all 0.15s;
    }

    .icon-toggle:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text, #fff);
    }

    .icon-toggle.active {
        background: var(--accent, #6366f1);
        color: white;
    }

    .icon-toggle .material-symbols-rounded {
        font-size: 16px;
    }

    /* Spacing Inputs */
    .spacing-grid {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .spacing-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .spacing-item > label {
        font-size: 11px;
        color: var(--text-muted, #888);
        width: 50px;
        flex-shrink: 0;
    }

    .spacing-inputs {
        display: flex;
        gap: 4px;
        flex: 1;
    }

    .spacing-inputs input {
        width: 40px;
        background: var(--bg-lighter, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-radius: 4px;
        color: var(--text, #fff);
        font-size: 11px;
        padding: 6px 4px;
        text-align: center;
        outline: none;
    }

    .spacing-inputs input:focus {
        border-color: var(--accent, #6366f1);
    }

    .link-values {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .link-values input {
        display: none;
    }

    .link-values .material-symbols-rounded {
        font-size: 18px;
        color: var(--text-muted, #666);
        transition: color 0.2s;
    }

    .link-values input:checked + .material-symbols-rounded {
        color: var(--accent, #6366f1);
    }

    /* Shadow Presets */
    .shadow-presets {
        display: flex;
        gap: 8px;
    }

    .shadow-preset {
        flex: 1;
        background: var(--bg-lighter, #1a1a1a);
        border: 2px solid var(--border-color, #333);
        border-radius: 6px;
        padding: 8px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .shadow-preset:hover {
        border-color: rgba(99, 102, 241, 0.5);
    }

    .shadow-preset.active {
        border-color: var(--accent, #6366f1);
        background: rgba(99, 102, 241, 0.1);
    }

    .shadow-preview {
        width: 100%;
        height: 24px;
        background: var(--bg-main, #0d0d0d);
        border-radius: 3px;
    }

    .shadow-preview.shadow-sm {
        box-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }

    .shadow-preview.shadow-md {
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.4);
    }

    .shadow-preview.shadow-lg {
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.4);
    }

    .shadow-preview.shadow-xl {
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.4);
    }

    /* Style Grid Improvements */
    .style-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .style-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .style-item.full {
        grid-column: 1 / -1;
    }

    .style-item label {
        font-size: 11px;
        color: var(--text-muted, #888);
    }

    /* Offset Slider */
    .offset-control {
        margin-bottom: 12px;
    }

    .offset-control label {
        display: block;
        font-size: 11px;
        color: var(--text-muted, #888);
        margin-bottom: 6px;
    }

    .slider-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .slider-value {
        font-size: 12px;
        color: var(--text, #fff);
        min-width: 50px;
        text-align: right;
    }

    /* Modern Slider */
    .modern-slider {
        flex: 1;
        -webkit-appearance: none;
        height: 6px;
        background: var(--bg-lighter, #222);
        border-radius: 3px;
        outline: none;
    }

    .modern-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 16px;
        height: 16px;
        background: var(--accent, #6366f1);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.4);
        transition: transform 0.15s;
    }

    .modern-slider::-webkit-slider-thumb:hover {
        transform: scale(1.15);
    }

    .modern-slider::-moz-range-thumb {
        width: 16px;
        height: 16px;
        background: var(--accent, #6366f1);
        border-radius: 50%;
        cursor: pointer;
        border: none;
    }

    /* Lock Toggle */
    .lock-control {
        padding: 10px 12px;
        background: rgba(251, 191, 36, 0.05);
        border: 1px solid rgba(251, 191, 36, 0.2);
        border-radius: 8px;
    }

    .toggle-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 12px;
        color: var(--text, #fff);
    }

    .toggle-label input[type="checkbox"] {
        display: none;
    }

    .toggle-switch {
        width: 36px;
        height: 20px;
        background: var(--bg-lighter, #333);
        border-radius: 10px;
        position: relative;
        transition: background 0.2s;
    }

    .toggle-switch::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 16px;
        height: 16px;
        background: white;
        border-radius: 50%;
        transition: transform 0.2s;
    }

    .toggle-label input:checked+.toggle-switch {
        background: #fbbf24;
    }

    .toggle-label input:checked+.toggle-switch::after {
        transform: translateX(16px);
    }

    .lock-control .hint {
        display: block;
        font-size: 10px;
        color: var(--text-muted, #666);
        margin-top: 4px;
        margin-left: 44px;
    }

    /* Size Controls */
    .size-control {
        margin-bottom: 14px;
    }

    .size-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .size-header label {
        font-size: 12px;
        color: var(--text-muted, #888);
    }

    /* Unit Toggle Buttons */
    .unit-toggle {
        display: flex;
        gap: 2px;
        background: var(--bg-lighter, #1a1a1a);
        padding: 2px;
        border-radius: 6px;
    }

    .unit-btn {
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 500;
        background: transparent;
        border: none;
        color: var(--text-muted, #666);
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .unit-btn:hover {
        color: var(--text, #fff);
        background: rgba(255, 255, 255, 0.05);
    }

    .unit-btn.active {
        background: var(--accent, #6366f1);
        color: white;
    }

    /* Value Input */
    .value-input {
        width: 60px;
        padding: 6px 8px;
        background: var(--bg-lighter, #222);
        border: 1px solid var(--border-color, #333);
        border-radius: 6px;
        color: var(--text, #fff);
        font-size: 12px;
        text-align: center;
    }

    .value-input:focus {
        outline: none;
        border-color: var(--accent, #6366f1);
    }

    /* Styling Grid */
    .style-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .style-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .style-item label {
        font-size: 11px;
        color: var(--text-muted, #888);
    }

    .color-input-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .color-input-wrap input[type="color"] {
        width: 32px;
        height: 32px;
        padding: 2px;
        border: 1px solid var(--border-color, #333);
        border-radius: 6px;
        background: transparent;
        cursor: pointer;
    }

    .color-hex {
        font-size: 11px;
        font-family: monospace;
        color: var(--text-muted, #888);
    }

    .slider-mini {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .slider-mini input[type="range"] {
        flex: 1;
        -webkit-appearance: none;
        height: 4px;
        background: var(--bg-lighter, #222);
        border-radius: 2px;
    }

    .slider-mini input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 12px;
        height: 12px;
        background: var(--accent, #6366f1);
        border-radius: 50%;
        cursor: pointer;
    }

    .slider-mini span {
        font-size: 11px;
        color: var(--text, #fff);
        min-width: 35px;
        text-align: right;
    }

    /* Action Buttons */
    .settings-actions {
        display: flex;
        gap: 8px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color, #333);
        justify-content: center;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-lighter, #222);
        border: 1px solid var(--border-color, #333);
        border-radius: 8px;
        color: var(--text-muted, #888);
        cursor: pointer;
        transition: all 0.15s;
    }

    .action-btn:hover {
        background: rgba(99, 102, 241, 0.15);
        border-color: var(--accent, #6366f1);
        color: var(--accent, #6366f1);
    }

    .action-btn .material-symbols-rounded {
        font-size: 20px;
    }


    /* Icon Grid for Icon Library */
    .icon-tabs {
        display: flex;
        gap: 4px;
        margin-bottom: 12px;
    }

    .icon-tab {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 8px;
        background: var(--bg-lighter, #222);
        border: 1px solid var(--border-color, #333);
        border-radius: 6px;
        color: var(--text-muted, #888);
        font-size: 11px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .icon-tab:hover,
    .icon-tab.active {
        background: rgba(99, 102, 241, 0.15);
        border-color: var(--accent, #6366f1);
        color: var(--text, #fff);
    }

    .icon-tab .material-symbols-rounded {
        font-size: 16px;
    }

    .icon-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 4px;
        max-height: 200px;
        overflow-y: auto;
        padding: 4px;
        background: var(--bg-lighter, #222);
        border-radius: 6px;
    }

    .icon-grid-item {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .icon-grid-item .material-symbols-rounded {
        font-size: 20px;
        color: var(--text-secondary, #aaa);
    }

    .icon-grid-item:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .icon-grid-item:hover .material-symbols-rounded {
        color: var(--text, #fff);
    }

    .icon-grid-item.selected {
        background: rgba(99, 102, 241, 0.2);
        border-color: var(--accent, #6366f1);
    }

    .icon-grid-item.selected .material-symbols-rounded {
        color: var(--accent, #6366f1);
    }

    /* Form Select */
    .form-select {
        width: 100%;
        padding: 8px 10px;
        background: var(--bg-lighter, #222);
        border: 1px solid var(--border-color, #333);
        border-radius: 6px;
        color: var(--text, #fff);
        font-size: 12px;
        cursor: pointer;
    }

    /* Keyboard shortcuts hover zone (invisible trigger at bottom) */
    .keyboard-hints-zone {
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        max-width: 800px;
        height: 40px;
        z-index: 100;
    }

    .keyboard-hints-bar {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%) translateY(100%);
        display: flex;
        gap: 20px;
        padding: 10px 20px;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 10px 10px 0 0;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
        white-space: nowrap;
        opacity: 0;
        transition: all 0.25s ease;
    }

    .keyboard-hints-zone:hover .keyboard-hints-bar {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .keyboard-hints-bar kbd {
        display: inline-block;
        padding: 2px 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        font-family: -apple-system, 'SF Mono', monospace;
        font-size: 11px;
        margin-right: 4px;
        color: rgba(255, 255, 255, 0.8);
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

    .preview-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(4px);
    }

    .preview-modal-content {
        position: relative;
        width: 90%;
        max-width: 1200px;
        max-height: 90vh;
        background: var(--bg-card, #1a1a1a);
        border-radius: 16px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .preview-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-color, #333);
    }

    .preview-modal-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .preview-modal-header h3 .material-symbols-rounded {
        font-size: 22px;
        color: var(--accent, #6366f1);
    }

    .preview-modal-body {
        flex: 1;
        overflow: auto;
        padding: 24px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        background: #ffffff;
    }

    .preview-container {
        width: 100%;
        max-width: 900px;
        min-height: 200px;
        position: relative;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .preview-modal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        border-top: 1px solid var(--border-color, #333);
        background: var(--bg-lighter, #151515);
    }

    .preview-hint {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-muted, #888);
    }

    .preview-hint .material-symbols-rounded {
        font-size: 18px;
        color: var(--accent, #6366f1);
    }

    /* Modal Overlay */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s, visibility 0.2s;
    }

    .modal-overlay[style*="display: flex"],
    .modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-dialog {
        background: var(--bg-card, #1a1a1a);
        border: 1px solid var(--border, rgba(255, 255, 255, 0.1));
        border-radius: 12px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border, rgba(255, 255, 255, 0.1));
    }

    .modal-header h3 {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--text, #fff);
    }

    .modal-header h3 .material-symbols-rounded {
        font-size: 20px;
        color: var(--accent, #6366f1);
    }

    .modal-body {
        padding: 20px;
    }

    .modal-body .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .modal-body label {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-muted, #a1a1a1);
    }

    .modal-body .form-control {
        padding: 12px 16px;
        background: var(--bg, #0a0a0a);
        border: 1px solid var(--border, rgba(255, 255, 255, 0.1));
        border-radius: 8px;
        color: var(--text, #fff);
        font-size: 14px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .modal-body .form-control:focus {
        outline: none;
        border-color: var(--accent, #6366f1);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .modal-body .form-hint {
        font-size: 12px;
        color: var(--text-muted, #888);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 20px;
        border-top: 1px solid var(--border, rgba(255, 255, 255, 0.1));
    }

    /* Load More Button */
    .load-more-btn {
        width: 100%;
        justify-content: center;
        opacity: 0.7;
        margin-top: 4px;
    }

    .load-more-btn:hover {
        opacity: 1;
    }

    /* Hide templates beyond first 6 initially */
    .templates-grid .template-card:nth-child(n+7) {
        display: none;
    }

    .templates-grid.show-all .template-card {
        display: flex !important;
    }

    /* Save Toast */
    .save-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 24px;
        background: var(--bg-card, #1a1a1a);
        border: 1px solid var(--border-color, #333);
        border-left: 4px solid #10b981;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        z-index: 10001;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .save-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    .save-toast .toast-icon {
        font-size: 24px;
        color: #10b981;
    }

    .save-toast .toast-text {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary, #fff);
    }

    /* Preview Element Styles (for preview mode) */
    .preview-element {
        position: absolute;
        box-sizing: border-box;
    }

    /* Code Editor Panel */
    .code-editor-panel {
        position: fixed;
        top: 60px;
        right: 0;
        left: auto !important;
        width: 500px;
        height: calc(100vh - 60px);
        background: var(--bg-card, #1a1a1a);
        border-left: 1px solid var(--border-color, #333);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }

    .code-editor-panel.open {
        transform: translateX(0) !important;
    }

    .code-editor-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color, #333);
        background: var(--bg-lighter, #222);
    }

    .code-tabs {
        display: flex;
        gap: 4px;
    }

    .code-tab {
        padding: 8px 16px;
        background: transparent;
        border: none;
        color: var(--text-muted, #888);
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .code-tab:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary, #fff);
    }

    .code-tab.active {
        background: var(--accent, #6366f1);
        color: white;
    }

    .code-actions {
        display: flex;
        gap: 8px;
    }

    .code-editor-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .code-textarea {
        flex: 1;
        width: 100%;
        padding: 16px;
        background: var(--bg, #0a0a0a);
        color: #e0e0e0;
        border: none;
        font-family: 'SF Mono', 'Fira Code', 'Monaco', 'Consolas', monospace;
        font-size: 13px;
        line-height: 1.6;
        resize: none;
        white-space: pre-wrap;
        word-break: break-all;
    }

    .code-textarea:focus {
        outline: none;
    }

    .code-textarea::placeholder {
        color: var(--text-muted, #555);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-sm .material-symbols-rounded {
        font-size: 16px;
    }

    padding: 10px;
    }

    .preview-element-linkgroup {
        padding: 10px;
    }

    .preview-element-linkgroup h4 {
        margin: 0 0 8px 0;
        font-size: 14px;
        font-weight: 600;
    }

    .preview-element-linkgroup ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .preview-element-linkgroup li {
        padding: 4px 0;
    }

    .preview-element-linkgroup a {
        color: #555;
        text-decoration: none;
        font-size: 13px;
        transition: color 0.15s;
    }

    .preview-element-linkgroup a:hover {
        color: var(--accent, #6366f1);
    }

    .preview-element-image {
        width: 100%;
        height: 100%;
    }

    .preview-element-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<script src="<?php echo asset('js/mega-menu-editor.js'); ?>"></script>