<?php
/**
 * Mega Menu Settings Page
 * 
 * Settings for mega menu appearance, triggers, and animations
 */

// Load navigation settings to know menu type
$navSettings = Database::fetch(
    "SELECT * FROM navigation_settings WHERE shop_id = ?",
    [1]
);

// Load mega menu settings
$megaSettings = Database::fetch(
    "SELECT * FROM mega_menu_settings WHERE shop_id = ?",
    [1]
);

// Defaults if not found
if (!$megaSettings) {
    $megaSettings = [
        'header_mega_trigger' => 'hover',
        'header_mega_animation' => 'fade',
        'header_mega_animation_speed' => 200,
        'header_mega_delay' => 100,
        'side_mega_trigger' => 'hover',
        'side_mega_animation' => 'slide',
        'side_mega_animation_speed' => 250,
        'mega_background_color' => '#ffffff',
        'mega_text_color' => '#333333',
        'mega_border_radius' => 0,
        'mega_shadow' => 1
    ];
}

$menuType = $navSettings['menu_type'] ?? 'header_links';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_mega_settings'])) {
    $updateData = [
        'header_mega_trigger' => $_POST['header_mega_trigger'] ?? 'hover',
        'header_mega_animation' => $_POST['header_mega_animation'] ?? 'fade',
        'header_mega_animation_speed' => intval($_POST['header_mega_animation_speed'] ?? 200),
        'header_mega_delay' => intval($_POST['header_mega_delay'] ?? 100),
        'side_mega_trigger' => $_POST['side_mega_trigger'] ?? 'hover',
        'side_mega_animation' => $_POST['side_mega_animation'] ?? 'slide',
        'side_mega_animation_speed' => intval($_POST['side_mega_animation_speed'] ?? 250),
        'mega_background_color' => $_POST['mega_background_color'] ?? '#ffffff',
        'mega_text_color' => $_POST['mega_text_color'] ?? '#333333',
        'mega_border_radius' => intval($_POST['mega_border_radius'] ?? 0),
        'mega_shadow' => isset($_POST['mega_shadow']) ? 1 : 0
    ];

    // Check if record exists
    $existing = Database::fetch("SELECT id FROM mega_menu_settings WHERE shop_id = 1");

    if ($existing) {
        Database::update('mega_menu_settings', $updateData, 'shop_id = ?', [1]);
    } else {
        $updateData['shop_id'] = 1;
        Database::insert('mega_menu_settings', $updateData);
    }

    // Reload
    $megaSettings = Database::fetch("SELECT * FROM mega_menu_settings WHERE shop_id = ?", [1]);

    $successMessage = 'Mega-Menu Einstellungen wurden gespeichert.';
}
?>

<div class="page-content">
    <div class="content-header">
        <div class="header-title">
            <h1>Mega-Menu Einstellungen</h1>
            <span class="header-subtitle">Erscheinung und Animationen der Mega-Menüs</span>
        </div>
        <div class="header-actions">
            <a href="?page=shop/navigation_settings" class="btn">
                <span class="material-symbols-rounded">settings</span>
                Navigation Einstellungen
            </a>
            <a href="?page=shop/navigation" class="btn">
                <span class="material-symbols-rounded">menu</span>
                Navigation
            </a>
        </div>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <span class="material-symbols-rounded">check_circle</span>
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <!-- Current Menu Type Info -->
    <div class="content-card info-card">
        <div class="card-body">
            <div class="current-type-info">
                <span class="material-symbols-rounded">
                    <?= $menuType === 'header_links' ? 'view_headline' : 'menu_open' ?>
                </span>
                <div>
                    <strong>Aktueller Menü-Typ:</strong>
                    <span>
                        <?= $menuType === 'header_links' ? 'Header-Links (Prada/Ralph Lauren)' : 'Side-Menu (Louis Vuitton/Gucci)' ?>
                    </span>
                </div>
                <a href="?page=shop/navigation_settings" class="btn btn-sm">Ändern</a>
            </div>
        </div>
    </div>

    <form method="POST" class="settings-form">
        <input type="hidden" name="save_mega_settings" value="1">

        <?php if ($menuType === 'header_links'): ?>
            <!-- Header Links Mega Menu Settings -->
            <div class="content-card">
                <div class="card-header">
                    <h2>
                        <span class="material-symbols-rounded">expand_more</span>
                        Mega-Menu unter Header
                    </h2>
                </div>
                <div class="card-body">
                    <p class="form-hint" style="margin-bottom: 20px;">
                        Bei Header-Links erscheint das Mega-Menu als Dropdown unter dem Header, wenn man über einen Link
                        fährt.
                    </p>

                    <!-- Trigger -->
                    <div class="form-group">
                        <label class="form-label">Mega-Menu erscheint bei</label>
                        <div class="trigger-selector">
                            <label
                                class="trigger-option <?= $megaSettings['header_mega_trigger'] === 'hover' ? 'selected' : '' ?>">
                                <input type="radio" name="header_mega_trigger" value="hover"
                                    <?= $megaSettings['header_mega_trigger'] === 'hover' ? 'checked' : '' ?>>
                                <span class="material-symbols-rounded">touch_app</span>
                                <div>
                                    <strong>Hover</strong>
                                    <span>Beim Drüberfahren</span>
                                </div>
                            </label>
                            <label
                                class="trigger-option <?= $megaSettings['header_mega_trigger'] === 'click' ? 'selected' : '' ?>">
                                <input type="radio" name="header_mega_trigger" value="click"
                                    <?= $megaSettings['header_mega_trigger'] === 'click' ? 'checked' : '' ?>>
                                <span class="material-symbols-rounded">mouse</span>
                                <div>
                                    <strong>Klick</strong>
                                    <span>Beim Anklicken</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <hr class="form-divider">

                    <!-- Animation -->
                    <div class="form-group">
                        <label class="form-label">Animation</label>
                        <div class="animation-selector">
                            <label
                                class="animation-option <?= $megaSettings['header_mega_animation'] === 'none' ? 'selected' : '' ?>">
                                <input type="radio" name="header_mega_animation" value="none"
                                    <?= $megaSettings['header_mega_animation'] === 'none' ? 'checked' : '' ?>>
                                <div class="anim-preview anim-none">
                                    <div class="anim-box"></div>
                                </div>
                                <span>Keine</span>
                                <span class="anim-example">Prada</span>
                            </label>
                            <label
                                class="animation-option <?= $megaSettings['header_mega_animation'] === 'fade' ? 'selected' : '' ?>">
                                <input type="radio" name="header_mega_animation" value="fade"
                                    <?= $megaSettings['header_mega_animation'] === 'fade' ? 'checked' : '' ?>>
                                <div class="anim-preview anim-fade">
                                    <div class="anim-box"></div>
                                </div>
                                <span>Einblenden</span>
                                <span class="anim-example">Fade</span>
                            </label>
                            <label
                                class="animation-option <?= $megaSettings['header_mega_animation'] === 'slide_down' ? 'selected' : '' ?>">
                                <input type="radio" name="header_mega_animation" value="slide_down"
                                    <?= $megaSettings['header_mega_animation'] === 'slide_down' ? 'checked' : '' ?>>
                                <div class="anim-preview anim-slide">
                                    <div class="anim-box"></div>
                                </div>
                                <span>Herunterfahren</span>
                                <span class="anim-example">Ralph Lauren</span>
                            </label>
                        </div>
                    </div>

                    <!-- Speed & Delay -->
                    <div class="form-row two-col">
                        <div class="form-field">
                            <label class="form-label">Animations-Geschwindigkeit</label>
                            <div class="speed-input">
                                <input type="range" name="header_mega_animation_speed"
                                    value="<?= $megaSettings['header_mega_animation_speed'] ?>" min="100" max="500"
                                    step="50" oninput="this.nextElementSibling.textContent = this.value + 'ms'">
                                <span class="speed-value">
                                    <?= $megaSettings['header_mega_animation_speed'] ?>ms
                                </span>
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-label">Verzögerung (Hover)</label>
                            <div class="speed-input">
                                <input type="range" name="header_mega_delay"
                                    value="<?= $megaSettings['header_mega_delay'] ?>" min="0" max="300" step="50"
                                    oninput="this.nextElementSibling.textContent = this.value + 'ms'">
                                <span class="speed-value">
                                    <?= $megaSettings['header_mega_delay'] ?>ms
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Side Menu Mega Settings -->
            <div class="content-card">
                <div class="card-header">
                    <h2>
                        <span class="material-symbols-rounded">menu_open</span>
                        Unter-Navigation im Side-Menu
                    </h2>
                </div>
                <div class="card-body">
                    <p class="form-hint" style="margin-bottom: 20px;">
                        Bei Side-Menu erscheint die Unter-Navigation als zweites Panel (nebeneinander oder übereinander, je
                        nach Stil).
                    </p>

                    <!-- Trigger -->
                    <div class="form-group">
                        <label class="form-label">Unter-Navigation erscheint bei</label>
                        <div class="trigger-selector">
                            <label
                                class="trigger-option <?= $megaSettings['side_mega_trigger'] === 'hover' ? 'selected' : '' ?>">
                                <input type="radio" name="side_mega_trigger" value="hover"
                                    <?= $megaSettings['side_mega_trigger'] === 'hover' ? 'checked' : '' ?>>
                                <span class="material-symbols-rounded">touch_app</span>
                                <div>
                                    <strong>Hover</strong>
                                    <span>Beim Drüberfahren über Kategorie</span>
                                </div>
                            </label>
                            <label
                                class="trigger-option <?= $megaSettings['side_mega_trigger'] === 'click' ? 'selected' : '' ?>">
                                <input type="radio" name="side_mega_trigger" value="click"
                                    <?= $megaSettings['side_mega_trigger'] === 'click' ? 'checked' : '' ?>>
                                <span class="material-symbols-rounded">mouse</span>
                                <div>
                                    <strong>Klick</strong>
                                    <span>Beim Anklicken der Kategorie</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <hr class="form-divider">

                    <!-- Animation -->
                    <div class="form-group">
                        <label class="form-label">Panel-Animation</label>
                        <div class="animation-selector">
                            <label
                                class="animation-option <?= $megaSettings['side_mega_animation'] === 'slide' ? 'selected' : '' ?>">
                                <input type="radio" name="side_mega_animation" value="slide"
                                    <?= $megaSettings['side_mega_animation'] === 'slide' ? 'checked' : '' ?>>
                                <div class="anim-preview anim-slide-h">
                                    <div class="anim-box"></div>
                                </div>
                                <span>Einschieben</span>
                            </label>
                            <label
                                class="animation-option <?= $megaSettings['side_mega_animation'] === 'fade' ? 'selected' : '' ?>">
                                <input type="radio" name="side_mega_animation" value="fade"
                                    <?= $megaSettings['side_mega_animation'] === 'fade' ? 'checked' : '' ?>>
                                <div class="anim-preview anim-fade">
                                    <div class="anim-box"></div>
                                </div>
                                <span>Einblenden</span>
                            </label>
                            <label
                                class="animation-option <?= $megaSettings['side_mega_animation'] === 'none' ? 'selected' : '' ?>">
                                <input type="radio" name="side_mega_animation" value="none"
                                    <?= $megaSettings['side_mega_animation'] === 'none' ? 'checked' : '' ?>>
                                <div class="anim-preview anim-none">
                                    <div class="anim-box"></div>
                                </div>
                                <span>Keine</span>
                            </label>
                        </div>
                    </div>

                    <!-- Speed -->
                    <div class="form-field" style="max-width: 400px;">
                        <label class="form-label">Animations-Geschwindigkeit</label>
                        <div class="speed-input">
                            <input type="range" name="side_mega_animation_speed"
                                value="<?= $megaSettings['side_mega_animation_speed'] ?>" min="100" max="500" step="50"
                                oninput="this.nextElementSibling.textContent = this.value + 'ms'">
                            <span class="speed-value">
                                <?= $megaSettings['side_mega_animation_speed'] ?>ms
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Appearance Settings (for both types) -->
        <div class="content-card">
            <div class="card-header">
                <h2>
                    <span class="material-symbols-rounded">palette</span>
                    Erscheinungsbild
                </h2>
            </div>
            <div class="card-body">
                <div class="form-row three-col">
                    <div class="form-field">
                        <label class="form-label">Hintergrundfarbe</label>
                        <input type="color" name="mega_background_color" class="form-input form-color"
                            value="<?= $megaSettings['mega_background_color'] ?>">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Textfarbe</label>
                        <input type="color" name="mega_text_color" class="form-input form-color"
                            value="<?= $megaSettings['mega_text_color'] ?>">
                    </div>
                    <div class="form-field">
                        <label class="form-label">Ecken-Radius (px)</label>
                        <input type="number" name="mega_border_radius" class="form-input"
                            value="<?= $megaSettings['mega_border_radius'] ?>" min="0" max="24" step="2">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 16px;">
                    <label class="form-checkbox">
                        <input type="checkbox" name="mega_shadow" value="1" <?= $megaSettings['mega_shadow'] ? 'checked' : '' ?>>
                        <span class="checkbox-label">Schatten anzeigen</span>
                    </label>
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
    .info-card {
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    .current-type-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .current-type-info .material-symbols-rounded {
        font-size: 32px;
        color: var(--accent, #6366f1);
    }

    .current-type-info strong {
        display: block;
        font-size: 12px;
        color: var(--text-muted, #888);
        margin-bottom: 2px;
    }

    .current-type-info>div span {
        font-size: 15px;
    }

    .current-type-info .btn {
        margin-left: auto;
    }

    /* Trigger Selector */
    .trigger-selector {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    .trigger-option {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        padding: 16px 20px;
        border: 2px solid var(--border-color, #333);
        border-radius: 8px;
        transition: all 0.2s;
        flex: 1;
    }

    .trigger-option input {
        display: none;
    }

    .trigger-option.selected,
    .trigger-option:has(input:checked) {
        border-color: var(--accent, #6366f1);
        background: rgba(99, 102, 241, 0.1);
    }

    .trigger-option .material-symbols-rounded {
        font-size: 28px;
        color: var(--accent, #6366f1);
    }

    .trigger-option strong {
        display: block;
        font-size: 14px;
    }

    .trigger-option span {
        font-size: 11px;
        color: var(--text-muted, #888);
    }

    /* Animation Selector */
    .animation-selector {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    .animation-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        padding: 16px;
        border: 2px solid var(--border-color, #333);
        border-radius: 8px;
        transition: all 0.2s;
        flex: 1;
    }

    .animation-option input {
        display: none;
    }

    .animation-option.selected,
    .animation-option:has(input:checked) {
        border-color: var(--accent, #6366f1);
        background: rgba(99, 102, 241, 0.1);
    }

    .anim-preview {
        width: 60px;
        height: 40px;
        background: var(--bg-card, #222);
        border-radius: 4px;
        margin-bottom: 10px;
        position: relative;
        overflow: hidden;
    }

    .anim-preview .anim-box {
        position: absolute;
        bottom: 0;
        left: 10%;
        right: 10%;
        height: 60%;
        background: var(--accent, #6366f1);
        border-radius: 2px 2px 0 0;
    }

    /* Animation previews on hover */
    .animation-option:hover .anim-fade .anim-box {
        animation: fadeIn 0.3s ease;
    }

    .animation-option:hover .anim-slide .anim-box {
        animation: slideDown 0.3s ease;
    }

    .animation-option:hover .anim-slide-h .anim-box {
        animation: slideRight 0.3s ease;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    @keyframes slideDown {
        0% {
            transform: translateY(-100%);
        }

        100% {
            transform: translateY(0);
        }
    }

    @keyframes slideRight {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(0);
        }
    }

    .animation-option span {
        font-size: 12px;
    }

    .animation-option .anim-example {
        color: var(--text-muted, #666);
        font-size: 10px;
    }

    /* Speed slider */
    .speed-input {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .speed-input input[type="range"] {
        flex: 1;
        -webkit-appearance: none;
        height: 6px;
        background: var(--border-color, #333);
        border-radius: 3px;
    }

    .speed-input input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        background: var(--accent, #6366f1);
        border-radius: 50%;
        cursor: pointer;
    }

    .speed-value {
        min-width: 60px;
        text-align: right;
        font-family: 'SF Mono', monospace;
        color: var(--text-muted, #888);
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
        gap: 24px;
    }

    .form-row.three-col {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .form-color {
        height: 44px;
        padding: 4px;
        cursor: pointer;
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

    /* Update selected states */
    .trigger-option:has(input:checked),
    .animation-option:has(input:checked) {
        border-color: var(--accent, #6366f1);
        background: rgba(99, 102, 241, 0.1);
    }
</style>

<script>
    // Update selected states on radio change
    document.querySelectorAll('.trigger-option input, .animation-option input').forEach(input => {
        input.addEventListener('change', function () {
            const container = this.closest('.trigger-selector, .animation-selector');
            container.querySelectorAll('.trigger-option, .animation-option').forEach(opt => {
                opt.classList.toggle('selected', opt.querySelector('input').checked);
            });
        });
    });
</script>