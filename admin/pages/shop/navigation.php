<?php
/** 
 * Shop - Navigation
 * Full navigation menu management with drag-and-drop
 * Version: 2.0 - Polished
 */

// Check permission
Auth::requirePermission('navigation.manage');

// Get shop ID
$shop = Shop::getDefault();
$shopId = $shop['id'] ?? 1;

// Ensure default menus exist
NavigationMenu::ensureDefaults($shopId);

// Get all menus
$menus = NavigationMenu::allForShop($shopId);

// Current tab (menu code)
$currentTab = $_GET['tab'] ?? 'main';
$currentMenu = NavigationMenu::findByCode($currentTab, $shopId);

// Handle actions
$result = null;
$action = $_GET['action'] ?? '';
$itemId = intval($_GET['item_id'] ?? 0);

// Handle AJAX order update - must be before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    // Clear any output buffers
    while (ob_get_level())
        ob_end_clean();

    if ($_POST['ajax_action'] === 'update_order') {
        $result = NavigationController::handleUpdateOrder();
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['form_action'])) {
    if ($_POST['form_action'] === 'create' && $currentMenu) {
        $_POST['menu_id'] = $currentMenu['id'];
        $result = NavigationController::handleCreateItem();
        if ($result['success']) {
            $_SESSION['nav_flash_message'] = $result;
            echo '<script>window.location.href = "?page=shop/navigation&tab=' . $currentTab . '";</script>';
            return;
        }
    } elseif ($_POST['form_action'] === 'update' && $itemId > 0) {
        $result = NavigationController::handleUpdateItem($itemId);
        if ($result['success']) {
            $_SESSION['nav_flash_message'] = $result;
            echo '<script>window.location.href = "?page=shop/navigation&tab=' . $currentTab . '";</script>';
            return;
        }
    }
} elseif (!empty($action) && $itemId > 0) {
    if ($action === 'delete') {
        $result = NavigationController::handleDeleteItem($itemId);
        $_SESSION['nav_flash_message'] = $result;
        echo '<script>window.location.href = "?page=shop/navigation&tab=' . $currentTab . '";</script>';
        return;
    } elseif ($action === 'toggle') {
        $result = NavigationController::handleToggleStatus($itemId);
        $_SESSION['nav_flash_message'] = $result;
        echo '<script>window.location.href = "?page=shop/navigation&tab=' . $currentTab . '";</script>';
        return;
    }
}

// Check for flash message
if (isset($_SESSION['nav_flash_message'])) {
    $result = $_SESSION['nav_flash_message'];
    unset($_SESSION['nav_flash_message']);
}

// Get items for current menu
$menuItems = $currentMenu ? NavigationItem::treeForMenu($currentMenu['id']) : [];
$flatItems = $currentMenu ? NavigationItem::allForMenu($currentMenu['id']) : [];

// Build a map of which items have children
$hasChildrenMap = [];
foreach ($flatItems as $item) {
    if (!empty($item['parent_id'])) {
        $hasChildrenMap[$item['parent_id']] = true;
    }
}

// Get CMS pages for dropdown
$cmsPages = CmsPage::allForShop($shopId);

// Get categories for dropdown
$categories = Category::activeForShop($shopId);
$categoriesFlat = Category::buildFlatList($categories);

// Edit mode?
$editItem = null;
$editMode = isset($_GET['edit']) && intval($_GET['edit']) > 0;
if ($editMode) {
    $editItem = NavigationItem::find(intval($_GET['edit']));
}
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>Navigation</h1>
        <p class="page-subtitle">Menüs und Navigationsstruktur verwalten</p>
    </div>
    <div class="page-header-actions">
        <div class="dropdown">
            <button type="button" class="btn dropdown-toggle" onclick="this.parentElement.classList.toggle('open')">
                <span class="material-symbols-rounded">settings</span> Einstellungen
                <span class="material-symbols-rounded">expand_more</span>
            </button>
            <div class="dropdown-menu">
                <a href="?page=shop/navigation_settings" class="dropdown-item">
                    <span class="material-symbols-rounded">tune</span>
                    Navigation Einstellungen
                </a>
                <a href="?page=shop/mega_menu_settings" class="dropdown-item">
                    <span class="material-symbols-rounded">view_column</span>
                    Mega-Menu Einstellungen
                </a>
            </div>
        </div>
        <button type="button" class="btn btn-primary" onclick="openAddModal()">
            <span class="material-symbols-rounded">add</span> Menüpunkt hinzufügen
        </button>
    </div>
</div>

<?php if ($result): ?>
    <?php if ($result['success']): ?>
        <div class="alert alert-success">
            <span class="material-symbols-rounded">check_circle</span>
            <?= htmlspecialchars($result['message']) ?>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()"><span
                    class="material-symbols-rounded">close</span></button>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <span class="material-symbols-rounded">error</span>
            <?= htmlspecialchars($result['message'] ?? 'Ein Fehler ist aufgetreten.') ?>
            <?php if (!empty($result['errors'])): ?>
                <ul style="margin:8px 0 0 20px;">
                    <?php foreach ($result['errors'] as $field => $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <button type="button" class="alert-close" onclick="this.parentElement.remove()"><span
                    class="material-symbols-rounded">close</span></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Tabs -->
<div class="nav-tabs-container">
    <?php foreach ($menus as $menu): ?>
        <a href="?page=shop/navigation&tab=<?= $menu['code'] ?>"
            class="nav-tab <?= $currentTab === $menu['code'] ? 'active' : '' ?>">
            <span
                class="material-symbols-rounded"><?= $menu['code'] === 'main' ? 'menu' : ($menu['code'] === 'footer' ? 'dock_to_bottom' : 'smartphone') ?></span>
            <?= htmlspecialchars($menu['name']) ?>
            <span class="nav-tab-count"><?= NavigationItem::countForMenu($menu['id']) ?></span>
        </a>
    <?php endforeach; ?>
</div>

<div class="nav-layout">
    <!-- Left: Menu Structure -->
    <div class="nav-structure-card">
        <div class="nav-structure-header">
            <h3><?= htmlspecialchars($currentMenu['name'] ?? 'Menü') ?>-Struktur</h3>
            <div class="nav-structure-actions">
                <span class="save-indicator" id="save-status">
                    <span class="material-symbols-rounded">check</span> Gespeichert
                </span>
            </div>
        </div>
        <div class="nav-structure-body">
            <style>
                /* Drag & Drop Styles - CMS Style (1:1) */
                .nav-tree-item.dragging {
                    opacity: 0.5;
                    background: var(--bg-lighter);
                }

                .nav-tree-row {
                    border: 1px solid transparent;
                    /* Prevent jump on border add */
                    transition: all 0.15s ease;
                }

                .nav-tree-row.drag-over {
                    border-color: var(--primary);
                    border-style: dashed;
                    background: var(--bg-active);
                }
            </style>
            <?php if (empty($flatItems)): ?>
                <div class="nav-empty-state">
                    <div class="empty-icon-wrapper">
                        <span class="material-symbols-rounded">menu</span>
                    </div>
                    <h4>Keine Menüpunkte</h4>
                    <p>Füge deinen ersten Menüpunkt hinzu.</p>
                    <button type="button" class="btn-clean-add" onclick="openAddModal()">
                        <span class="plus-icon">+</span> Menüpunkt hinzufügen
                    </button>
                    <style>
                        .empty-icon-wrapper {
                            width: 64px;
                            height: 64px;
                            background: var(--bg-lighter);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 16px;
                        }

                        .btn-clean-add {
                            background: transparent;
                            border: 1px dashed var(--border-color);
                            padding: 10px 20px;
                            border-radius: 8px;
                            color: var(--text-color);
                            font-size: 14px;
                            font-weight: 500;
                            cursor: pointer;
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                            transition: all 0.2s;
                        }

                        .btn-clean-add:hover {
                            border-color: var(--primary);
                            background: var(--bg-lighter);
                            color: var(--primary);
                        }

                        .plus-icon {
                            color: #22c55e;
                            /* Green as requested */
                            font-size: 18px;
                            font-weight: bold;
                            line-height: 1;
                        }
                    </style>
                </div>
            <?php else: ?>
                <div class="nav-help-text">
                    <span class="material-symbols-rounded">info</span>
                    Ziehe Einträge um sie neu zu ordnen. Klicke auf <span class="material-symbols-rounded"
                        style="font-size:14px;vertical-align:middle;">edit</span> zum Bearbeiten.
                </div>
                <ul id="sortable-nav" class="nav-tree" data-menu-id="<?= $currentMenu['id'] ?>">
                    <?php
                    function renderNavItem($item, $level, $hasChildrenMap)
                    {
                        $hasChildren = isset($hasChildrenMap[$item['id']]);
                        $isActive = $item['is_active'];
                        $typeLabels = [
                            'category' => 'Kategorie',
                            'product' => 'Produkt',
                            'page' => 'CMS',
                            'url' => 'URL',
                            'custom' => 'Anker',
                        ];
                        $typeLabel = $typeLabels[$item['type']] ?? $item['type'];
                        $url = NavigationItem::getResolvedUrl($item);
                        // Check if this item is being edited
                        $isEditing = isset($_GET['edit']) && intval($_GET['edit']) == $item['id'];
                        ?>
                        <li class="nav-tree-item <?= !$isActive ? 'is-draft' : '' ?> <?= $hasChildren ? 'has-children' : '' ?> <?= $isEditing ? 'is-editing' : '' ?>"
                            data-id="<?= $item['id'] ?>" data-parent="<?= $item['parent_id'] ?? '' ?>"
                            data-level="<?= $level ?>">
                            <div class="nav-tree-row" style="--level: <?= $level ?>;">
                                <div class="nav-tree-indent">
                                    <?php for ($i = 0; $i < $level; $i++): ?>
                                        <span class="indent-line"></span>
                                    <?php endfor; ?>
                                </div>
                                <span class="drag-handle" title="Ziehen zum Verschieben">
                                    <span class="material-symbols-rounded">drag_indicator</span>
                                </span>
                                <?php if ($hasChildren): ?>
                                    <span class="expand-toggle">
                                        <span class="material-symbols-rounded">chevron_right</span>
                                    </span>
                                <?php else: ?>
                                    <span class="expand-placeholder"></span>
                                <?php endif; ?>
                                <div class="nav-tree-content">
                                    <span class="nav-tree-label"><?= htmlspecialchars($item['label']) ?></span>
                                    <span class="nav-tree-status <?= $isActive ? 'status-active' : 'status-draft' ?>">
                                        <?= $isActive ? 'Aktiv' : 'Entwurf' ?>
                                    </span>
                                    <span class="nav-tree-type"><?= $typeLabel ?></span>
                                    <span class="nav-tree-url" title="<?= htmlspecialchars($url) ?>">
                                        <?= htmlspecialchars(mb_strlen($url) > 30 ? mb_substr($url, 0, 30) . '…' : $url) ?>
                                    </span>
                                </div>
                                <div class="nav-tree-actions">
                                    <a href="?page=shop/navigation&tab=<?= $_GET['tab'] ?? 'main' ?>&edit=<?= $item['id'] ?>"
                                        class="nav-action-btn" title="Bearbeiten">
                                        <span class="material-symbols-rounded">edit</span>
                                    </a>
                                    <button type="button" class="nav-action-btn" onclick="toggleItemStatus(<?= $item['id'] ?>)"
                                        title="<?= $isActive ? 'Deaktivieren' : 'Aktivieren' ?>">
                                        <span
                                            class="material-symbols-rounded"><?= $isActive ? 'visibility_off' : 'visibility' ?></span>
                                    </button>
                                    <button type="button" class="nav-action-btn nav-action-delete"
                                        onclick="confirmDelete(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['label'])) ?>')"
                                        title="Löschen">
                                        <span class="material-symbols-rounded">delete</span>
                                    </button>
                                </div>
                            </div>
                            <?php if (!empty($item['children'])): ?>
                                <ul class="nav-tree-children">
                                    <?php foreach ($item['children'] as $child): ?>
                                        <?php renderNavItem($child, $level + 1, $hasChildrenMap); ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                        <?php
                    }

                    foreach ($menuItems as $item) {
                        renderNavItem($item, 0, $hasChildrenMap);
                    }
                    ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Edit Panel or Info -->
    <div class="nav-sidebar">
        <?php if ($editItem): ?>
            <!-- Edit existing item -->
            <div class="nav-edit-card">
                <div class="nav-edit-header">
                    <h3>Bearbeiten</h3>
                    <span class="<?= $editItem['is_active'] ? 'badge-active' : 'badge-draft' ?>">
                        <?= $editItem['is_active'] ? 'Aktiv' : 'Entwurf' ?>
                    </span>
                </div>
                <form method="POST" action="?page=shop/navigation&tab=<?= $currentTab ?>&item_id=<?= $editItem['id'] ?>"
                    id="edit-form">
                    <input type="hidden" name="form_action" value="update">
                    <div class="nav-edit-body">
                        <div class="form-group">
                            <label class="form-label" for="label">
                                Label <span class="required">*</span>
                            </label>
                            <input type="text" id="label" name="label" class="form-input"
                                value="<?= htmlspecialchars($editItem['label']) ?>" required maxlength="250"
                                placeholder="z.B. Über uns">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="type">Link-Typ</label>
                            <select id="type" name="type" class="form-select" onchange="toggleEditFields(this.value)">
                                <option value="url" <?= $editItem['type'] === 'url' ? 'selected' : '' ?>>Externe URL</option>
                                <option value="page" <?= $editItem['type'] === 'page' ? 'selected' : '' ?>>CMS-Seite</option>
                                <option value="category" <?= $editItem['type'] === 'category' ? 'selected' : '' ?>>Kategorie
                                </option>
                                <option value="custom" <?= $editItem['type'] === 'custom' ? 'selected' : '' ?>>Anker-Link (#)
                                </option>
                            </select>
                        </div>

                        <!-- URL Field -->
                        <div class="form-group dynamic-field" id="edit-url-field">
                            <label class="form-label" for="url">URL <span class="required">*</span></label>
                            <input type="text" id="url" name="url" class="form-input"
                                value="<?= htmlspecialchars($editItem['url'] ?? '') ?>"
                                placeholder="https://example.com oder /pfad oder #anker">
                            <span class="form-hint">Externe Links beginnen mit https://</span>
                        </div>

                        <!-- CMS Page Field -->
                        <div class="form-group dynamic-field" id="edit-page-field" style="display:none;">
                            <label class="form-label" for="reference_id">CMS-Seite <span class="required">*</span></label>
                            <?php if (empty($cmsPages)): ?>
                                <div class="field-empty-state">
                                    <p>Keine CMS-Seiten vorhanden.</p>
                                    <a href="?page=shop/cms_create" class="btn btn-sm">
                                        <span class="material-symbols-rounded">add</span> Seite erstellen
                                    </a>
                                </div>
                            <?php else: ?>
                                <select id="reference_id" name="reference_id" class="form-select">
                                    <option value="">-- Bitte wählen --</option>
                                    <?php foreach ($cmsPages as $page): ?>
                                        <option value="<?= $page['id'] ?>" <?= $editItem['reference_id'] == $page['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($page['title']) ?>
                                            <?= !$page['is_active'] ? ' (Entwurf)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <!-- Category Field - Real DB Dropdown -->
                        <div class="form-group dynamic-field" id="edit-category-field" style="display:none;">
                            <label class="form-label">Kategorie <span class="required">*</span></label>
                            <?php if (empty($categoriesFlat)): ?>
                                <div class="field-empty-state">
                                    <p>Keine Kategorien vorhanden.</p>
                                    <a href="?page=catalog/category_create" class="btn btn-sm" target="_blank">
                                        <span class="material-symbols-rounded">add</span> Kategorie erstellen
                                    </a>
                                </div>
                            <?php else: ?>
                                <select name="reference_id" class="form-select">
                                    <option value="">-- Kategorie wählen --</option>
                                    <?php foreach ($categoriesFlat as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $editItem['reference_id'] == $cat['id'] ? 'selected' : '' ?>>
                                            <?= $cat['_indent'] ?>             <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="parent_id">Übergeordnet</label>
                            <select id="parent_id" name="parent_id" class="form-select">
                                <option value="">– Hauptebene –</option>
                                <?php foreach ($flatItems as $item): ?>
                                    <?php if ($item['id'] != $editItem['id']): ?>
                                        <option value="<?= $item['id'] ?>" <?= $editItem['parent_id'] == $item['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($item['label']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="target">Öffnen in</label>
                            <select id="target" name="target" class="form-select">
                                <option value="_self" <?= ($editItem['target'] ?? '_self') === '_self' ? 'selected' : '' ?>>
                                    Gleiches Fenster</option>
                                <option value="_blank" <?= ($editItem['target'] ?? '') === '_blank' ? 'selected' : '' ?>>Neues
                                    Fenster</option>
                            </select>
                        </div>

                        <!-- Styling Section (Accordion) -->
                        <div class="styling-accordion">
                            <button type="button" class="styling-accordion-header" onclick="toggleStylingAccordion()">
                                <span class="material-symbols-rounded">palette</span>
                                <span>Styling & Design</span>
                                <span class="material-symbols-rounded accordion-arrow">expand_more</span>
                            </button>
                            <div class="styling-accordion-content" id="styling-content" style="display: none;">
                                <!-- Text Color -->
                                <div class="form-group form-group-inline">
                                    <label class="form-label">Textfarbe</label>
                                    <div class="color-picker-row">
                                        <input type="color" name="custom_color" id="custom_color"
                                            value="<?= htmlspecialchars($editItem['custom_color'] ?? '#000000') ?>"
                                            class="color-input"
                                            oninput="syncColorToHex('custom_color', 'custom_color_hex')">
                                        <input type="text" id="custom_color_hex" class="form-input color-hex-input"
                                            value="<?= htmlspecialchars($editItem['custom_color'] ?? '') ?>"
                                            placeholder="#000000"
                                            oninput="syncHexToColor('custom_color_hex', 'custom_color')">
                                        <button type="button" class="btn btn-sm"
                                            onclick="clearColorField('custom_color', 'custom_color_hex')">
                                            <span class="material-symbols-rounded">close</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Background Color -->
                                <div class="form-group form-group-inline">
                                    <label class="form-label">Hintergrund</label>
                                    <div class="color-picker-row">
                                        <input type="color" name="bg_color" id="bg_color"
                                            value="<?= htmlspecialchars($editItem['bg_color'] ?? '#ffffff') ?>"
                                            class="color-input" oninput="syncColorToHex('bg_color', 'bg_color_hex')">
                                        <input type="text" id="bg_color_hex" class="form-input color-hex-input"
                                            value="<?= htmlspecialchars($editItem['bg_color'] ?? '') ?>"
                                            placeholder="transparent" oninput="syncHexToColor('bg_color_hex', 'bg_color')">
                                        <button type="button" class="btn btn-sm"
                                            onclick="clearColorField('bg_color', 'bg_color_hex')">
                                            <span class="material-symbols-rounded">close</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Font Weight & Text Decoration -->
                                <div class="form-row">
                                    <div class="form-group form-group-half">
                                        <label class="form-label">Schriftstärke</label>
                                        <select name="font_weight" class="form-select">
                                            <option value="" <?= empty($editItem['font_weight']) ? 'selected' : '' ?>>Normal
                                            </option>
                                            <option value="bold" <?= ($editItem['font_weight'] ?? '') === 'bold' ? 'selected' : '' ?>>Fett</option>
                                        </select>
                                    </div>
                                    <div class="form-group form-group-half">
                                        <label class="form-label">Text-Dekoration</label>
                                        <select name="text_decoration" class="form-select">
                                            <option value="" <?= empty($editItem['text_decoration']) ? 'selected' : '' ?>>
                                                Keine</option>
                                            <option value="underline" <?= ($editItem['text_decoration'] ?? '') === 'underline' ? 'selected' : '' ?>>Unterstrichen</option>
                                            <option value="line-through" <?= ($editItem['text_decoration'] ?? '') === 'line-through' ? 'selected' : '' ?>>Durchgestrichen</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Badge -->
                                <div class="form-group">
                                    <label class="form-label">Badge / Tag</label>
                                    <div class="badge-input-row">
                                        <input type="text" name="badge_text" class="form-input"
                                            value="<?= htmlspecialchars($editItem['badge_text'] ?? '') ?>"
                                            placeholder="z.B. NEU, SALE, -20%" maxlength="20">
                                        <input type="color" name="badge_color" id="badge_color"
                                            value="<?= htmlspecialchars($editItem['badge_color'] ?? '#ef4444') ?>"
                                            class="color-input color-input-sm" title="Badge-Farbe">
                                    </div>
                                    <span class="form-hint">Kleines Label neben dem Menüpunkt</span>
                                </div>

                                <!-- Icon Picker -->
                                <div class="form-group icon-picker-group">
                                    <label class="form-label">Icon</label>
                                    <div class="icon-picker-row">
                                        <input type="hidden" name="icon" id="edit_icon"
                                            value="<?= htmlspecialchars($editItem['icon'] ?? '') ?>">
                                        <input type="hidden" name="custom_icon_url" id="custom_icon_url"
                                            value="<?= htmlspecialchars($editItem['custom_icon_url'] ?? '') ?>">
                                        <button type="button" class="btn icon-picker-btn" onclick="openIconPicker()">
                                            <?php if (!empty($editItem['custom_icon_url'])): ?>
                                                <img src="<?= htmlspecialchars($editItem['custom_icon_url']) ?>"
                                                    class="custom-icon-preview" alt="">
                                            <?php elseif (!empty($editItem['icon'])): ?>
                                                <span
                                                    class="material-symbols-rounded"><?= htmlspecialchars($editItem['icon']) ?></span>
                                            <?php else: ?>
                                                <span class="material-symbols-rounded">add</span>
                                            <?php endif; ?>
                                            <span class="icon-picker-label">
                                                <?php
                                                if (!empty($editItem['custom_icon_url']))
                                                    echo 'Eigenes Icon';
                                                elseif (!empty($editItem['icon']))
                                                    echo htmlspecialchars($editItem['icon']);
                                                else
                                                    echo 'Icon wählen';
                                                ?>
                                            </span>
                                        </button>
                                        <?php if (!empty($editItem['icon']) || !empty($editItem['custom_icon_url'])): ?>
                                            <button type="button" class="btn btn-sm" onclick="clearIcon()"
                                                title="Icon entfernen">
                                                <span class="material-symbols-rounded">close</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Custom Icon Upload -->
                                    <div class="custom-icon-upload" style="margin-top: 10px;">
                                        <label class="upload-label">
                                            <input type="file" id="icon_upload" accept="image/*"
                                                onchange="handleIconUpload(this)" style="display:none;">
                                            <span class="btn btn-sm upload-btn">
                                                <span class="material-symbols-rounded">upload</span>
                                                Eigenes Icon hochladen
                                            </span>
                                        </label>
                                        <span class="form-hint">PNG, SVG oder JPG (max. 100KB)</span>
                                    </div>
                                </div>

                                <!-- Icon Position -->
                                <div class="form-group" id="icon-position-group"
                                    style="<?= empty($editItem['icon']) ? 'display:none;' : '' ?>">
                                    <label class="form-label">Icon Position</label>
                                    <div class="icon-position-options">
                                        <label class="radio-option">
                                            <input type="radio" name="icon_position" value="left"
                                                <?= ($editItem['icon_position'] ?? 'left') === 'left' ? 'checked' : '' ?>>
                                            <span>Links</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="icon_position" value="right"
                                                <?= ($editItem['icon_position'] ?? '') === 'right' ? 'checked' : '' ?>>
                                            <span>Rechts</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="icon_position" value="only"
                                                <?= ($editItem['icon_position'] ?? '') === 'only' ? 'checked' : '' ?>>
                                            <span>Nur Icon</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mega Menu Accordion (only for root-level items in main menu, NOT footer) -->
                    <?php if (empty($editItem['parent_id']) && $currentTab !== 'footer'): ?>
                        <div class="styling-accordion" id="mega-accordion">
                            <div class="styling-accordion-header" onclick="toggleStylingAccordion('mega-accordion')">
                                <span class="material-symbols-rounded">view_column</span>
                                <span>Mega-Menu</span>
                                <span class="material-symbols-rounded accordion-arrow">expand_more</span>
                            </div>
                            <div class="styling-accordion-content">
                                <!-- Mega Menu Intro -->
                                <div class="mega-intro-box">
                                    <span class="material-symbols-rounded">lightbulb</span>
                                    <p>Ein <strong>Mega-Menu</strong> ist ein großes Dropdown mit Bildern, Link-Spalten und
                                        Promo-Inhalten – wie bei großen Fashion-Shops (Gucci, Prada).</p>
                                </div>

                                <!-- Mega Enable Toggle -->
                                <div class="form-group">
                                    <label class="form-checkbox">
                                        <input type="checkbox" name="mega_enabled" id="mega_enabled" value="1"
                                            <?= ($editItem['mega_enabled'] ?? 0) ? 'checked' : '' ?>
                                            onchange="toggleMegaOptions(this.checked)">
                                        <span class="checkbox-label">Als Mega-Menu aktivieren</span>
                                    </label>
                                </div>

                                <div id="mega-options" style="<?= ($editItem['mega_enabled'] ?? 0) ? '' : 'display:none;' ?>">
                                    <!-- Click Behavior -->
                                    <div class="form-group">
                                        <label class="form-label">Bei Klick auf diesen Link</label>
                                        <select name="click_behavior" class="form-select">
                                            <option value="navigate" <?= ($editItem['click_behavior'] ?? 'navigate') === 'navigate' ? 'selected' : '' ?>>Zur URL navigieren</option>
                                            <option value="nothing" <?= ($editItem['click_behavior'] ?? '') === 'nothing' ? 'selected' : '' ?>>Nur Mega-Menu öffnen</option>
                                        </select>
                                        <span class="form-hint">Das Mega-Menu erscheint immer bei Hover (Desktop)</span>
                                    </div>

                                    <!-- Open Fullpage Editor Button -->
                                    <div class="form-group mega-editor-section">
                                        <a href="?page=shop/mega_menu_editor&id=<?= $editItem['id'] ?>"
                                            class="btn btn-primary btn-block mega-editor-btn">
                                            <span class="material-symbols-rounded">dashboard_customize</span>
                                            Mega-Menu im visuellen Editor gestalten
                                        </a>
                                        <p class="form-hint">Drag & Drop Editor für Bilder, Links, Texte und Buttons</p>
                                    </div>

                                    <!-- Link to Global Settings -->
                                    <div class="mega-settings-link">
                                        <span class="material-symbols-rounded">tune</span>
                                        <a href="?page=shop/mega_menu_settings">Globale Mega-Menu Einstellungen</a>
                                        <span class="hint">(Animation, Farben, Trigger)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="is_active" value="1" <?= $editItem['is_active'] ? 'checked' : '' ?>>
                            <span class="checkbox-label">Aktiv (im Menü sichtbar)</span>
                        </label>
                    </div>
            </div>
            <div class="nav-edit-footer">
                <a href="?page=shop/navigation&tab=<?= $currentTab ?>" class="btn">Abbrechen</a>
                <div class="nav-edit-footer-right">
                    <button type="button" class="btn btn-delete"
                        onclick="confirmDelete(<?= $editItem['id'] ?>, '<?= htmlspecialchars(addslashes($editItem['label'])) ?>')">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </div>
            </form>
        </div>

        <script>
            function toggleEditFields(type) {
                document.querySelectorAll('#edit-form .dynamic-field').forEach(el => el.style.display = 'none');
                if (type === 'url' || type === 'custom') {
                    document.getElementById('edit-url-field').style.display = 'block';
                } else if (type === 'page') {
                    document.getElementById('edit-page-field').style.display = 'block';
                } else if (type === 'category') {
                    document.getElementById('edit-category-field').style.display = 'block';
                }
            }
            toggleEditFields('<?= $editItem['type'] ?>');
        </script>

    <?php else: ?>
        <!-- Info Panel (Comprehensive) -->
        <div class="nav-info-card">
            <div class="nav-info-header">
                <span class="material-symbols-rounded">help</span>
                <h4>Hilfe & Schnellstart</h4>
            </div>
            <div class="nav-info-body">
                <!-- Quick Start -->
                <div class="info-section info-quickstart">
                    <div class="quickstart-icon">
                        <span class="material-symbols-rounded">rocket_launch</span>
                    </div>
                    <p>Klicke auf <strong>"Menüpunkt hinzufügen"</strong> oder wähle einen bestehenden Eintrag zum
                        Bearbeiten.</p>
                </div>

                <!-- Menu Types -->
                <div class="info-section">
                    <h5>Menü-Bereiche</h5>
                    <ul>
                        <li><strong>Hauptmenü</strong> – Navigation in deinem Shop-Header</li>
                        <li><strong>Footer</strong> – Links im unteren Seitenbereich</li>
                    </ul>
                </div>

                <!-- Link Types -->
                <div class="info-section">
                    <h5>Link-Typen</h5>
                    <ul>
                        <li><strong>URL</strong> – Beliebige externe/interne Links</li>
                        <li><strong>CMS</strong> – Verlinke zu CMS-Seiten (AGB, Impressum...)</li>
                        <li><strong>Kategorie</strong> – Direkt zu Shop-Kategorien</li>
                        <li><strong>Anker</strong> – Sprungmarken auf der Seite (#kontakt)</li>
                    </ul>
                </div>

                <!-- Mega Menu Explanation -->
                <div class="info-section info-mega">
                    <h5><span class="material-symbols-rounded">view_column</span> Was ist ein Mega-Menu?</h5>
                    <p class="mega-desc">Ein Mega-Menu ist ein großes Dropdown, das bei Hover über einen Menüpunkt
                        erscheint. Es kann Bilder, mehrere Link-Spalten und Promo-Inhalte enthalten – wie bei großen
                        Fashion-Shops (Gucci, Prada).</p>
                    <div class="mega-tip">
                        <span class="material-symbols-rounded">lightbulb</span>
                        <span>Aktiviere "Als Mega-Menu" im Edit-Panel eines Hauptmenü-Eintrags.</span>
                    </div>
                </div>

                <!-- Drag & Drop -->
                <div class="info-section">
                    <h5>Reihenfolge ändern</h5>
                    <p>Ziehe Einträge per Drag & Drop um sie neu zu ordnen. Änderungen werden automatisch gespeichert.</p>
                </div>
            </div>
        </div>

        <?php if ($currentTab === 'footer' && !empty($flatItems)): ?>
            <div class="nav-quick-card">
                <a href="?page=shop/cms_settings" class="quick-link">
                    <span class="material-symbols-rounded">article</span>
                    <span>CMS-Seiten verwalten</span>
                    <span class="material-symbols-rounded">chevron_right</span>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</div>

<!-- Save Status Toast -->
<div id="save-status" class="save-status">
    <span class="material-symbols-rounded">check_circle</span>
    <span>Reihenfolge gespeichert</span>
</div>

<!-- Add Item Modal -->
<div id="add-modal" class="modal-overlay" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Neuer Menüpunkt</h3>
            <button type="button" class="modal-close" onclick="closeAddModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <form method="POST" action="?page=shop/navigation&tab=<?= $currentTab ?>" id="add-form">
            <input type="hidden" name="form_action" value="create">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="new_label">
                        Label <span class="required">*</span>
                    </label>
                    <input type="text" id="new_label" name="label" class="form-input" required
                        placeholder="z.B. Über uns, Kontakt, Shop" maxlength="250">
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_type">Link-Typ</label>
                    <select id="new_type" name="type" class="form-select" onchange="toggleAddFields(this.value)">
                        <option value="url">Externe URL</option>
                        <option value="page">CMS-Seite</option>
                        <option value="category">Kategorie</option>
                        <option value="custom">Anker-Link (#)</option>
                    </select>
                </div>

                <!-- URL Field -->
                <div class="form-group dynamic-field" id="add-url-field">
                    <label class="form-label" for="new_url">URL <span class="required">*</span></label>
                    <input type="text" id="new_url" name="url" class="form-input"
                        placeholder="https://example.com oder /pfad">
                </div>

                <!-- CMS Page Field -->
                <div class="form-group dynamic-field" id="add-page-field" style="display:none;">
                    <label class="form-label" for="new_reference_id">CMS-Seite <span class="required">*</span></label>
                    <?php if (empty($cmsPages)): ?>
                        <div class="field-empty-state">
                            <p>Keine CMS-Seiten vorhanden.</p>
                            <a href="?page=shop/cms_create" class="btn btn-sm" target="_blank">
                                <span class="material-symbols-rounded">add</span> Seite erstellen
                            </a>
                        </div>
                        <input type="hidden" name="reference_id" value="">
                    <?php else: ?>
                        <select id="new_reference_id" name="reference_id" class="form-select">
                            <option value="">-- Bitte wählen --</option>
                            <?php foreach ($cmsPages as $page): ?>
                                <option value="<?= $page['id'] ?>">
                                    <?= htmlspecialchars($page['title']) ?>
                                    <?= !$page['is_active'] ? ' (Entwurf)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Category Field - Real DB Dropdown -->
                <div class="form-group dynamic-field" id="add-category-field" style="display:none;">
                    <label class="form-label">Kategorie <span class="required">*</span></label>
                    <?php if (empty($categoriesFlat)): ?>
                        <div class="field-empty-state">
                            <p>Keine Kategorien vorhanden.</p>
                            <a href="?page=catalog/category_create" class="btn btn-sm" target="_blank">
                                <span class="material-symbols-rounded">add</span> Kategorie erstellen
                            </a>
                        </div>
                    <?php else: ?>
                        <select id="new_category_id" name="reference_id" class="form-select">
                            <option value="">-- Kategorie wählen --</option>
                            <?php foreach ($categoriesFlat as $cat): ?>
                                <option value="<?= $cat['id'] ?>">
                                    <?= $cat['_indent'] ?>         <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Anchor Field -->
                <div class="form-group dynamic-field" id="add-anchor-field" style="display:none;">
                    <label class="form-label" for="new_anchor">Anker-Link <span class="required">*</span></label>
                    <select id="new_anchor" class="form-select"
                        onchange="document.getElementById('new_url').value = this.value">
                        <option value="">-- Anker wählen --</option>
                        <option value="#top">Nach oben (#top)</option>
                        <option value="#kontakt">Kontakt (#kontakt)</option>
                        <option value="#footer">Footer (#footer)</option>
                        <option value="#newsletter">Newsletter (#newsletter)</option>
                        <option value="#about">Über uns (#about)</option>
                        <option value="#products">Produkte (#products)</option>
                    </select>
                    <span class="form-hint">Oder gib einen eigenen Anker ein:</span>
                    <input type="text" id="new_custom_anchor" class="form-input" style="margin-top:8px;"
                        placeholder="#mein-anker" onchange="document.getElementById('new_url').value = this.value">
                </div>


                <div class="form-group">
                    <label class="form-label" for="new_parent_id">Übergeordneter Punkt</label>
                    <select id="new_parent_id" name="parent_id" class="form-select">
                        <option value="">– Hauptebene –</option>
                        <?php foreach ($flatItems as $item): ?>
                            <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span class="checkbox-label">Sofort aktivieren</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeAddModal()">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal-overlay" style="display:none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-header">
            <h3>Löschen bestätigen</h3>
        </div>
        <div class="modal-body">
            <p>Möchtest du <strong id="delete-item-name"></strong> wirklich löschen?</p>
            <p class="modal-hint">Untergeordnete Einträge werden auf die Hauptebene verschoben.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" onclick="closeDeleteModal()">Abbrechen</button>
            <a id="delete-confirm-link" href="#" class="btn btn-danger">Löschen</a>
        </div>
    </div>
</div>

<!-- Icon Picker Modal -->
<div id="icon-modal" class="modal-overlay" style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-header">
            <h3>Icon auswählen</h3>
            <button type="button" class="modal-close" onclick="closeIconPicker()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body icon-picker-body">
            <div class="icon-search-box">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="icon-search" placeholder="Icon suchen..." oninput="filterIcons(this.value)">
            </div>
            <div class="icon-grid" id="icon-grid">
                <!-- Navigation & Menu -->
                <button type="button" class="icon-option" data-icon="home" onclick="selectIcon('home')"><span
                        class="material-symbols-rounded">home</span></button>
                <button type="button" class="icon-option" data-icon="menu" onclick="selectIcon('menu')"><span
                        class="material-symbols-rounded">menu</span></button>
                <button type="button" class="icon-option" data-icon="dashboard" onclick="selectIcon('dashboard')"><span
                        class="material-symbols-rounded">dashboard</span></button>
                <button type="button" class="icon-option" data-icon="apps" onclick="selectIcon('apps')"><span
                        class="material-symbols-rounded">apps</span></button>
                <button type="button" class="icon-option" data-icon="widgets" onclick="selectIcon('widgets')"><span
                        class="material-symbols-rounded">widgets</span></button>
                <button type="button" class="icon-option" data-icon="view_list" onclick="selectIcon('view_list')"><span
                        class="material-symbols-rounded">view_list</span></button>
                <button type="button" class="icon-option" data-icon="grid_view" onclick="selectIcon('grid_view')"><span
                        class="material-symbols-rounded">grid_view</span></button>
                <button type="button" class="icon-option" data-icon="link" onclick="selectIcon('link')"><span
                        class="material-symbols-rounded">link</span></button>
                <button type="button" class="icon-option" data-icon="open_in_new"
                    onclick="selectIcon('open_in_new')"><span
                        class="material-symbols-rounded">open_in_new</span></button>
                <!-- E-Commerce -->
                <button type="button" class="icon-option" data-icon="shopping_cart"
                    onclick="selectIcon('shopping_cart')"><span
                        class="material-symbols-rounded">shopping_cart</span></button>
                <button type="button" class="icon-option" data-icon="shopping_bag"
                    onclick="selectIcon('shopping_bag')"><span
                        class="material-symbols-rounded">shopping_bag</span></button>
                <button type="button" class="icon-option" data-icon="storefront"
                    onclick="selectIcon('storefront')"><span class="material-symbols-rounded">storefront</span></button>
                <button type="button" class="icon-option" data-icon="store" onclick="selectIcon('store')"><span
                        class="material-symbols-rounded">store</span></button>
                <button type="button" class="icon-option" data-icon="sell" onclick="selectIcon('sell')"><span
                        class="material-symbols-rounded">sell</span></button>
                <button type="button" class="icon-option" data-icon="local_offer"
                    onclick="selectIcon('local_offer')"><span
                        class="material-symbols-rounded">local_offer</span></button>
                <button type="button" class="icon-option" data-icon="payments" onclick="selectIcon('payments')"><span
                        class="material-symbols-rounded">payments</span></button>
                <button type="button" class="icon-option" data-icon="credit_card"
                    onclick="selectIcon('credit_card')"><span
                        class="material-symbols-rounded">credit_card</span></button>
                <button type="button" class="icon-option" data-icon="receipt" onclick="selectIcon('receipt')"><span
                        class="material-symbols-rounded">receipt</span></button>
                <button type="button" class="icon-option" data-icon="wallet" onclick="selectIcon('wallet')"><span
                        class="material-symbols-rounded">wallet</span></button>
                <button type="button" class="icon-option" data-icon="loyalty" onclick="selectIcon('loyalty')"><span
                        class="material-symbols-rounded">loyalty</span></button>
                <button type="button" class="icon-option" data-icon="card_giftcard"
                    onclick="selectIcon('card_giftcard')"><span
                        class="material-symbols-rounded">card_giftcard</span></button>
                <button type="button" class="icon-option" data-icon="redeem" onclick="selectIcon('redeem')"><span
                        class="material-symbols-rounded">redeem</span></button>
                <!-- Fashion & Accessories -->
                <button type="button" class="icon-option" data-icon="checkroom" onclick="selectIcon('checkroom')"><span
                        class="material-symbols-rounded">checkroom</span></button>
                <button type="button" class="icon-option" data-icon="diamond" onclick="selectIcon('diamond')"><span
                        class="material-symbols-rounded">diamond</span></button>
                <button type="button" class="icon-option" data-icon="watch" onclick="selectIcon('watch')"><span
                        class="material-symbols-rounded">watch</span></button>
                <button type="button" class="icon-option" data-icon="eyeglasses"
                    onclick="selectIcon('eyeglasses')"><span class="material-symbols-rounded">eyeglasses</span></button>
                <button type="button" class="icon-option" data-icon="backpack" onclick="selectIcon('backpack')"><span
                        class="material-symbols-rounded">backpack</span></button>
                <button type="button" class="icon-option" data-icon="luggage" onclick="selectIcon('luggage')"><span
                        class="material-symbols-rounded">luggage</span></button>
                <button type="button" class="icon-option" data-icon="dry_cleaning"
                    onclick="selectIcon('dry_cleaning')"><span
                        class="material-symbols-rounded">dry_cleaning</span></button>
                <!-- Beauty & Personal Care -->
                <button type="button" class="icon-option" data-icon="spa" onclick="selectIcon('spa')"><span
                        class="material-symbols-rounded">spa</span></button>
                <button type="button" class="icon-option" data-icon="face" onclick="selectIcon('face')"><span
                        class="material-symbols-rounded">face</span></button>
                <button type="button" class="icon-option" data-icon="self_improvement"
                    onclick="selectIcon('self_improvement')"><span
                        class="material-symbols-rounded">self_improvement</span></button>
                <button type="button" class="icon-option" data-icon="brush" onclick="selectIcon('brush')"><span
                        class="material-symbols-rounded">brush</span></button>
                <button type="button" class="icon-option" data-icon="palette" onclick="selectIcon('palette')"><span
                        class="material-symbols-rounded">palette</span></button>
                <!-- Food & Drink -->
                <button type="button" class="icon-option" data-icon="restaurant"
                    onclick="selectIcon('restaurant')"><span class="material-symbols-rounded">restaurant</span></button>
                <button type="button" class="icon-option" data-icon="restaurant_menu"
                    onclick="selectIcon('restaurant_menu')"><span
                        class="material-symbols-rounded">restaurant_menu</span></button>
                <button type="button" class="icon-option" data-icon="local_cafe"
                    onclick="selectIcon('local_cafe')"><span class="material-symbols-rounded">local_cafe</span></button>
                <button type="button" class="icon-option" data-icon="coffee" onclick="selectIcon('coffee')"><span
                        class="material-symbols-rounded">coffee</span></button>
                <button type="button" class="icon-option" data-icon="cake" onclick="selectIcon('cake')"><span
                        class="material-symbols-rounded">cake</span></button>
                <button type="button" class="icon-option" data-icon="bakery_dining"
                    onclick="selectIcon('bakery_dining')"><span
                        class="material-symbols-rounded">bakery_dining</span></button>
                <button type="button" class="icon-option" data-icon="local_pizza"
                    onclick="selectIcon('local_pizza')"><span
                        class="material-symbols-rounded">local_pizza</span></button>
                <button type="button" class="icon-option" data-icon="lunch_dining"
                    onclick="selectIcon('lunch_dining')"><span
                        class="material-symbols-rounded">lunch_dining</span></button>
                <button type="button" class="icon-option" data-icon="ramen_dining"
                    onclick="selectIcon('ramen_dining')"><span
                        class="material-symbols-rounded">ramen_dining</span></button>
                <button type="button" class="icon-option" data-icon="wine_bar" onclick="selectIcon('wine_bar')"><span
                        class="material-symbols-rounded">wine_bar</span></button>
                <button type="button" class="icon-option" data-icon="local_bar" onclick="selectIcon('local_bar')"><span
                        class="material-symbols-rounded">local_bar</span></button>
                <button type="button" class="icon-option" data-icon="liquor" onclick="selectIcon('liquor')"><span
                        class="material-symbols-rounded">liquor</span></button>
                <button type="button" class="icon-option" data-icon="icecream" onclick="selectIcon('icecream')"><span
                        class="material-symbols-rounded">icecream</span></button>
                <!-- Sports & Fitness -->
                <button type="button" class="icon-option" data-icon="fitness_center"
                    onclick="selectIcon('fitness_center')"><span
                        class="material-symbols-rounded">fitness_center</span></button>
                <button type="button" class="icon-option" data-icon="sports" onclick="selectIcon('sports')"><span
                        class="material-symbols-rounded">sports</span></button>
                <button type="button" class="icon-option" data-icon="sports_soccer"
                    onclick="selectIcon('sports_soccer')"><span
                        class="material-symbols-rounded">sports_soccer</span></button>
                <button type="button" class="icon-option" data-icon="sports_basketball"
                    onclick="selectIcon('sports_basketball')"><span
                        class="material-symbols-rounded">sports_basketball</span></button>
                <button type="button" class="icon-option" data-icon="sports_tennis"
                    onclick="selectIcon('sports_tennis')"><span
                        class="material-symbols-rounded">sports_tennis</span></button>
                <button type="button" class="icon-option" data-icon="pool" onclick="selectIcon('pool')"><span
                        class="material-symbols-rounded">pool</span></button>
                <button type="button" class="icon-option" data-icon="hiking" onclick="selectIcon('hiking')"><span
                        class="material-symbols-rounded">hiking</span></button>
                <button type="button" class="icon-option" data-icon="kayaking" onclick="selectIcon('kayaking')"><span
                        class="material-symbols-rounded">kayaking</span></button>
                <button type="button" class="icon-option" data-icon="surfing" onclick="selectIcon('surfing')"><span
                        class="material-symbols-rounded">surfing</span></button>
                <button type="button" class="icon-option" data-icon="skateboarding"
                    onclick="selectIcon('skateboarding')"><span
                        class="material-symbols-rounded">skateboarding</span></button>
                <!-- Technology -->
                <button type="button" class="icon-option" data-icon="devices" onclick="selectIcon('devices')"><span
                        class="material-symbols-rounded">devices</span></button>
                <button type="button" class="icon-option" data-icon="phone_iphone"
                    onclick="selectIcon('phone_iphone')"><span
                        class="material-symbols-rounded">phone_iphone</span></button>
                <button type="button" class="icon-option" data-icon="laptop" onclick="selectIcon('laptop')"><span
                        class="material-symbols-rounded">laptop</span></button>
                <button type="button" class="icon-option" data-icon="desktop_windows"
                    onclick="selectIcon('desktop_windows')"><span
                        class="material-symbols-rounded">desktop_windows</span></button>
                <button type="button" class="icon-option" data-icon="tablet" onclick="selectIcon('tablet')"><span
                        class="material-symbols-rounded">tablet</span></button>
                <button type="button" class="icon-option" data-icon="headphones"
                    onclick="selectIcon('headphones')"><span class="material-symbols-rounded">headphones</span></button>
                <button type="button" class="icon-option" data-icon="speaker" onclick="selectIcon('speaker')"><span
                        class="material-symbols-rounded">speaker</span></button>
                <button type="button" class="icon-option" data-icon="tv" onclick="selectIcon('tv')"><span
                        class="material-symbols-rounded">tv</span></button>
                <button type="button" class="icon-option" data-icon="videogame_asset"
                    onclick="selectIcon('videogame_asset')"><span
                        class="material-symbols-rounded">videogame_asset</span></button>
                <button type="button" class="icon-option" data-icon="camera" onclick="selectIcon('camera')"><span
                        class="material-symbols-rounded">camera</span></button>
                <button type="button" class="icon-option" data-icon="photo_camera"
                    onclick="selectIcon('photo_camera')"><span
                        class="material-symbols-rounded">photo_camera</span></button>
                <!-- Travel & Places -->
                <button type="button" class="icon-option" data-icon="flight" onclick="selectIcon('flight')"><span
                        class="material-symbols-rounded">flight</span></button>
                <button type="button" class="icon-option" data-icon="hotel" onclick="selectIcon('hotel')"><span
                        class="material-symbols-rounded">hotel</span></button>
                <button type="button" class="icon-option" data-icon="beach_access"
                    onclick="selectIcon('beach_access')"><span
                        class="material-symbols-rounded">beach_access</span></button>
                <button type="button" class="icon-option" data-icon="landscape" onclick="selectIcon('landscape')"><span
                        class="material-symbols-rounded">landscape</span></button>
                <button type="button" class="icon-option" data-icon="explore" onclick="selectIcon('explore')"><span
                        class="material-symbols-rounded">explore</span></button>
                <button type="button" class="icon-option" data-icon="map" onclick="selectIcon('map')"><span
                        class="material-symbols-rounded">map</span></button>
                <button type="button" class="icon-option" data-icon="location_on"
                    onclick="selectIcon('location_on')"><span
                        class="material-symbols-rounded">location_on</span></button>
                <button type="button" class="icon-option" data-icon="directions_car"
                    onclick="selectIcon('directions_car')"><span
                        class="material-symbols-rounded">directions_car</span></button>
                <button type="button" class="icon-option" data-icon="local_shipping"
                    onclick="selectIcon('local_shipping')"><span
                        class="material-symbols-rounded">local_shipping</span></button>
                <button type="button" class="icon-option" data-icon="train" onclick="selectIcon('train')"><span
                        class="material-symbols-rounded">train</span></button>
                <!-- Home & Living -->
                <button type="button" class="icon-option" data-icon="home" onclick="selectIcon('home')"><span
                        class="material-symbols-rounded">home</span></button>
                <button type="button" class="icon-option" data-icon="chair" onclick="selectIcon('chair')"><span
                        class="material-symbols-rounded">chair</span></button>
                <button type="button" class="icon-option" data-icon="bed" onclick="selectIcon('bed')"><span
                        class="material-symbols-rounded">bed</span></button>
                <button type="button" class="icon-option" data-icon="bathtub" onclick="selectIcon('bathtub')"><span
                        class="material-symbols-rounded">bathtub</span></button>
                <button type="button" class="icon-option" data-icon="kitchen" onclick="selectIcon('kitchen')"><span
                        class="material-symbols-rounded">kitchen</span></button>
                <button type="button" class="icon-option" data-icon="light" onclick="selectIcon('light')"><span
                        class="material-symbols-rounded">light</span></button>
                <button type="button" class="icon-option" data-icon="yard" onclick="selectIcon('yard')"><span
                        class="material-symbols-rounded">yard</span></button>
                <button type="button" class="icon-option" data-icon="local_florist"
                    onclick="selectIcon('local_florist')"><span
                        class="material-symbols-rounded">local_florist</span></button>
                <!-- Pets & Animals -->
                <button type="button" class="icon-option" data-icon="pets" onclick="selectIcon('pets')"><span
                        class="material-symbols-rounded">pets</span></button>
                <button type="button" class="icon-option" data-icon="cruelty_free"
                    onclick="selectIcon('cruelty_free')"><span
                        class="material-symbols-rounded">cruelty_free</span></button>
                <!-- Health & Medical -->
                <button type="button" class="icon-option" data-icon="health_and_safety"
                    onclick="selectIcon('health_and_safety')"><span
                        class="material-symbols-rounded">health_and_safety</span></button>
                <button type="button" class="icon-option" data-icon="medical_services"
                    onclick="selectIcon('medical_services')"><span
                        class="material-symbols-rounded">medical_services</span></button>
                <button type="button" class="icon-option" data-icon="medication"
                    onclick="selectIcon('medication')"><span class="material-symbols-rounded">medication</span></button>
                <button type="button" class="icon-option" data-icon="vaccines" onclick="selectIcon('vaccines')"><span
                        class="material-symbols-rounded">vaccines</span></button>
                <button type="button" class="icon-option" data-icon="monitor_heart"
                    onclick="selectIcon('monitor_heart')"><span
                        class="material-symbols-rounded">monitor_heart</span></button>
                <!-- Kids & Baby -->
                <button type="button" class="icon-option" data-icon="child_care"
                    onclick="selectIcon('child_care')"><span class="material-symbols-rounded">child_care</span></button>
                <button type="button" class="icon-option" data-icon="toys" onclick="selectIcon('toys')"><span
                        class="material-symbols-rounded">toys</span></button>
                <button type="button" class="icon-option" data-icon="stroller" onclick="selectIcon('stroller')"><span
                        class="material-symbols-rounded">stroller</span></button>
                <!-- Entertainment -->
                <button type="button" class="icon-option" data-icon="movie" onclick="selectIcon('movie')"><span
                        class="material-symbols-rounded">movie</span></button>
                <button type="button" class="icon-option" data-icon="music_note"
                    onclick="selectIcon('music_note')"><span class="material-symbols-rounded">music_note</span></button>
                <button type="button" class="icon-option" data-icon="library_music"
                    onclick="selectIcon('library_music')"><span
                        class="material-symbols-rounded">library_music</span></button>
                <button type="button" class="icon-option" data-icon="album" onclick="selectIcon('album')"><span
                        class="material-symbols-rounded">album</span></button>
                <button type="button" class="icon-option" data-icon="mic" onclick="selectIcon('mic')"><span
                        class="material-symbols-rounded">mic</span></button>
                <button type="button" class="icon-option" data-icon="celebration"
                    onclick="selectIcon('celebration')"><span
                        class="material-symbols-rounded">celebration</span></button>
                <button type="button" class="icon-option" data-icon="theater_comedy"
                    onclick="selectIcon('theater_comedy')"><span
                        class="material-symbols-rounded">theater_comedy</span></button>
                <!-- Business & Work -->
                <button type="button" class="icon-option" data-icon="work" onclick="selectIcon('work')"><span
                        class="material-symbols-rounded">work</span></button>
                <button type="button" class="icon-option" data-icon="business" onclick="selectIcon('business')"><span
                        class="material-symbols-rounded">business</span></button>
                <button type="button" class="icon-option" data-icon="apartment" onclick="selectIcon('apartment')"><span
                        class="material-symbols-rounded">apartment</span></button>
                <button type="button" class="icon-option" data-icon="corporate_fare"
                    onclick="selectIcon('corporate_fare')"><span
                        class="material-symbols-rounded">corporate_fare</span></button>
                <button type="button" class="icon-option" data-icon="handshake" onclick="selectIcon('handshake')"><span
                        class="material-symbols-rounded">handshake</span></button>
                <button type="button" class="icon-option" data-icon="analytics" onclick="selectIcon('analytics')"><span
                        class="material-symbols-rounded">analytics</span></button>
                <button type="button" class="icon-option" data-icon="insights" onclick="selectIcon('insights')"><span
                        class="material-symbols-rounded">insights</span></button>
                <!-- User & Account -->
                <button type="button" class="icon-option" data-icon="person" onclick="selectIcon('person')"><span
                        class="material-symbols-rounded">person</span></button>
                <button type="button" class="icon-option" data-icon="account_circle"
                    onclick="selectIcon('account_circle')"><span
                        class="material-symbols-rounded">account_circle</span></button>
                <button type="button" class="icon-option" data-icon="group" onclick="selectIcon('group')"><span
                        class="material-symbols-rounded">group</span></button>
                <button type="button" class="icon-option" data-icon="groups" onclick="selectIcon('groups')"><span
                        class="material-symbols-rounded">groups</span></button>
                <button type="button" class="icon-option" data-icon="person_add"
                    onclick="selectIcon('person_add')"><span class="material-symbols-rounded">person_add</span></button>
                <button type="button" class="icon-option" data-icon="login" onclick="selectIcon('login')"><span
                        class="material-symbols-rounded">login</span></button>
                <button type="button" class="icon-option" data-icon="logout" onclick="selectIcon('logout')"><span
                        class="material-symbols-rounded">logout</span></button>
                <!-- Social & Engagement -->
                <button type="button" class="icon-option" data-icon="favorite" onclick="selectIcon('favorite')"><span
                        class="material-symbols-rounded">favorite</span></button>
                <button type="button" class="icon-option" data-icon="thumb_up" onclick="selectIcon('thumb_up')"><span
                        class="material-symbols-rounded">thumb_up</span></button>
                <button type="button" class="icon-option" data-icon="star" onclick="selectIcon('star')"><span
                        class="material-symbols-rounded">star</span></button>
                <button type="button" class="icon-option" data-icon="bookmark" onclick="selectIcon('bookmark')"><span
                        class="material-symbols-rounded">bookmark</span></button>
                <button type="button" class="icon-option" data-icon="share" onclick="selectIcon('share')"><span
                        class="material-symbols-rounded">share</span></button>
                <button type="button" class="icon-option" data-icon="chat" onclick="selectIcon('chat')"><span
                        class="material-symbols-rounded">chat</span></button>
                <button type="button" class="icon-option" data-icon="forum" onclick="selectIcon('forum')"><span
                        class="material-symbols-rounded">forum</span></button>
                <button type="button" class="icon-option" data-icon="comment" onclick="selectIcon('comment')"><span
                        class="material-symbols-rounded">comment</span></button>
                <!-- Contact -->
                <button type="button" class="icon-option" data-icon="email" onclick="selectIcon('email')"><span
                        class="material-symbols-rounded">email</span></button>
                <button type="button" class="icon-option" data-icon="phone" onclick="selectIcon('phone')"><span
                        class="material-symbols-rounded">phone</span></button>
                <button type="button" class="icon-option" data-icon="call" onclick="selectIcon('call')"><span
                        class="material-symbols-rounded">call</span></button>
                <button type="button" class="icon-option" data-icon="contact_mail"
                    onclick="selectIcon('contact_mail')"><span
                        class="material-symbols-rounded">contact_mail</span></button>
                <button type="button" class="icon-option" data-icon="contact_support"
                    onclick="selectIcon('contact_support')"><span
                        class="material-symbols-rounded">contact_support</span></button>
                <button type="button" class="icon-option" data-icon="support_agent"
                    onclick="selectIcon('support_agent')"><span
                        class="material-symbols-rounded">support_agent</span></button>
                <button type="button" class="icon-option" data-icon="help" onclick="selectIcon('help')"><span
                        class="material-symbols-rounded">help</span></button>
                <button type="button" class="icon-option" data-icon="info" onclick="selectIcon('info')"><span
                        class="material-symbols-rounded">info</span></button>
                <!-- Content -->
                <button type="button" class="icon-option" data-icon="article" onclick="selectIcon('article')"><span
                        class="material-symbols-rounded">article</span></button>
                <button type="button" class="icon-option" data-icon="description"
                    onclick="selectIcon('description')"><span
                        class="material-symbols-rounded">description</span></button>
                <button type="button" class="icon-option" data-icon="library_books"
                    onclick="selectIcon('library_books')"><span
                        class="material-symbols-rounded">library_books</span></button>
                <button type="button" class="icon-option" data-icon="menu_book" onclick="selectIcon('menu_book')"><span
                        class="material-symbols-rounded">menu_book</span></button>
                <button type="button" class="icon-option" data-icon="image" onclick="selectIcon('image')"><span
                        class="material-symbols-rounded">image</span></button>
                <button type="button" class="icon-option" data-icon="photo_library"
                    onclick="selectIcon('photo_library')"><span
                        class="material-symbols-rounded">photo_library</span></button>
                <button type="button" class="icon-option" data-icon="play_circle"
                    onclick="selectIcon('play_circle')"><span
                        class="material-symbols-rounded">play_circle</span></button>
                <button type="button" class="icon-option" data-icon="videocam" onclick="selectIcon('videocam')"><span
                        class="material-symbols-rounded">videocam</span></button>
                <button type="button" class="icon-option" data-icon="podcasts" onclick="selectIcon('podcasts')"><span
                        class="material-symbols-rounded">podcasts</span></button>
                <!-- Status & Badges -->
                <button type="button" class="icon-option" data-icon="new_releases"
                    onclick="selectIcon('new_releases')"><span
                        class="material-symbols-rounded">new_releases</span></button>
                <button type="button" class="icon-option" data-icon="verified" onclick="selectIcon('verified')"><span
                        class="material-symbols-rounded">verified</span></button>
                <button type="button" class="icon-option" data-icon="workspace_premium"
                    onclick="selectIcon('workspace_premium')"><span
                        class="material-symbols-rounded">workspace_premium</span></button>
                <button type="button" class="icon-option" data-icon="grade" onclick="selectIcon('grade')"><span
                        class="material-symbols-rounded">grade</span></button>
                <button type="button" class="icon-option" data-icon="trending_up"
                    onclick="selectIcon('trending_up')"><span
                        class="material-symbols-rounded">trending_up</span></button>
                <button type="button" class="icon-option" data-icon="trending_down"
                    onclick="selectIcon('trending_down')"><span
                        class="material-symbols-rounded">trending_down</span></button>
                <button type="button" class="icon-option" data-icon="percent" onclick="selectIcon('percent')"><span
                        class="material-symbols-rounded">percent</span></button>
                <button type="button" class="icon-option" data-icon="local_fire_department"
                    onclick="selectIcon('local_fire_department')"><span
                        class="material-symbols-rounded">local_fire_department</span></button>
                <button type="button" class="icon-option" data-icon="bolt" onclick="selectIcon('bolt')"><span
                        class="material-symbols-rounded">bolt</span></button>
                <button type="button" class="icon-option" data-icon="eco" onclick="selectIcon('eco')"><span
                        class="material-symbols-rounded">eco</span></button>
                <button type="button" class="icon-option" data-icon="recycling" onclick="selectIcon('recycling')"><span
                        class="material-symbols-rounded">recycling</span></button>
                <!-- Actions -->
                <button type="button" class="icon-option" data-icon="add" onclick="selectIcon('add')"><span
                        class="material-symbols-rounded">add</span></button>
                <button type="button" class="icon-option" data-icon="remove" onclick="selectIcon('remove')"><span
                        class="material-symbols-rounded">remove</span></button>
                <button type="button" class="icon-option" data-icon="check" onclick="selectIcon('check')"><span
                        class="material-symbols-rounded">check</span></button>
                <button type="button" class="icon-option" data-icon="close" onclick="selectIcon('close')"><span
                        class="material-symbols-rounded">close</span></button>
                <button type="button" class="icon-option" data-icon="search" onclick="selectIcon('search')"><span
                        class="material-symbols-rounded">search</span></button>
                <button type="button" class="icon-option" data-icon="filter_list"
                    onclick="selectIcon('filter_list')"><span
                        class="material-symbols-rounded">filter_list</span></button>
                <button type="button" class="icon-option" data-icon="sort" onclick="selectIcon('sort')"><span
                        class="material-symbols-rounded">sort</span></button>
                <button type="button" class="icon-option" data-icon="download" onclick="selectIcon('download')"><span
                        class="material-symbols-rounded">download</span></button>
                <button type="button" class="icon-option" data-icon="upload" onclick="selectIcon('upload')"><span
                        class="material-symbols-rounded">upload</span></button>
                <button type="button" class="icon-option" data-icon="edit" onclick="selectIcon('edit')"><span
                        class="material-symbols-rounded">edit</span></button>
                <button type="button" class="icon-option" data-icon="delete" onclick="selectIcon('delete')"><span
                        class="material-symbols-rounded">delete</span></button>
                <button type="button" class="icon-option" data-icon="save" onclick="selectIcon('save')"><span
                        class="material-symbols-rounded">save</span></button>
                <button type="button" class="icon-option" data-icon="refresh" onclick="selectIcon('refresh')"><span
                        class="material-symbols-rounded">refresh</span></button>
                <!-- Settings & System -->
                <button type="button" class="icon-option" data-icon="settings" onclick="selectIcon('settings')"><span
                        class="material-symbols-rounded">settings</span></button>
                <button type="button" class="icon-option" data-icon="tune" onclick="selectIcon('tune')"><span
                        class="material-symbols-rounded">tune</span></button>
                <button type="button" class="icon-option" data-icon="build" onclick="selectIcon('build')"><span
                        class="material-symbols-rounded">build</span></button>
                <button type="button" class="icon-option" data-icon="schedule" onclick="selectIcon('schedule')"><span
                        class="material-symbols-rounded">schedule</span></button>
                <button type="button" class="icon-option" data-icon="calendar_today"
                    onclick="selectIcon('calendar_today')"><span
                        class="material-symbols-rounded">calendar_today</span></button>
                <button type="button" class="icon-option" data-icon="event" onclick="selectIcon('event')"><span
                        class="material-symbols-rounded">event</span></button>
                <button type="button" class="icon-option" data-icon="lock" onclick="selectIcon('lock')"><span
                        class="material-symbols-rounded">lock</span></button>
                <button type="button" class="icon-option" data-icon="lock_open" onclick="selectIcon('lock_open')"><span
                        class="material-symbols-rounded">lock_open</span></button>
                <button type="button" class="icon-option" data-icon="visibility"
                    onclick="selectIcon('visibility')"><span class="material-symbols-rounded">visibility</span></button>
                <button type="button" class="icon-option" data-icon="visibility_off"
                    onclick="selectIcon('visibility_off')"><span
                        class="material-symbols-rounded">visibility_off</span></button>
                <button type="button" class="icon-option" data-icon="notifications"
                    onclick="selectIcon('notifications')"><span
                        class="material-symbols-rounded">notifications</span></button>
                <!-- Arrows & Direction -->
                <button type="button" class="icon-option" data-icon="arrow_forward"
                    onclick="selectIcon('arrow_forward')"><span
                        class="material-symbols-rounded">arrow_forward</span></button>
                <button type="button" class="icon-option" data-icon="arrow_back"
                    onclick="selectIcon('arrow_back')"><span class="material-symbols-rounded">arrow_back</span></button>
                <button type="button" class="icon-option" data-icon="arrow_upward"
                    onclick="selectIcon('arrow_upward')"><span
                        class="material-symbols-rounded">arrow_upward</span></button>
                <button type="button" class="icon-option" data-icon="arrow_downward"
                    onclick="selectIcon('arrow_downward')"><span
                        class="material-symbols-rounded">arrow_downward</span></button>
                <button type="button" class="icon-option" data-icon="chevron_right"
                    onclick="selectIcon('chevron_right')"><span
                        class="material-symbols-rounded">chevron_right</span></button>
                <button type="button" class="icon-option" data-icon="chevron_left"
                    onclick="selectIcon('chevron_left')"><span
                        class="material-symbols-rounded">chevron_left</span></button>
                <button type="button" class="icon-option" data-icon="expand_more"
                    onclick="selectIcon('expand_more')"><span
                        class="material-symbols-rounded">expand_more</span></button>
                <button type="button" class="icon-option" data-icon="expand_less"
                    onclick="selectIcon('expand_less')"><span
                        class="material-symbols-rounded">expand_less</span></button>
                <!-- Symbols -->
                <button type="button" class="icon-option" data-icon="category" onclick="selectIcon('category')"><span
                        class="material-symbols-rounded">category</span></button>
                <button type="button" class="icon-option" data-icon="inventory_2"
                    onclick="selectIcon('inventory_2')"><span
                        class="material-symbols-rounded">inventory_2</span></button>
                <button type="button" class="icon-option" data-icon="style" onclick="selectIcon('style')"><span
                        class="material-symbols-rounded">style</span></button>
                <button type="button" class="icon-option" data-icon="label" onclick="selectIcon('label')"><span
                        class="material-symbols-rounded">label</span></button>
                <button type="button" class="icon-option" data-icon="flag" onclick="selectIcon('flag')"><span
                        class="material-symbols-rounded">flag</span></button>
                <button type="button" class="icon-option" data-icon="language" onclick="selectIcon('language')"><span
                        class="material-symbols-rounded">language</span></button>
                <button type="button" class="icon-option" data-icon="public" onclick="selectIcon('public')"><span
                        class="material-symbols-rounded">public</span></button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Navigation Tabs */
    .nav-tabs-container {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
    }

    .nav-tab {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-secondary);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.15s ease;
    }

    .nav-tab:hover {
        border-color: var(--primary);
        color: var(--text-primary);
    }

    .nav-tab.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .nav-tab .material-symbols-rounded {
        font-size: 20px;
    }

    .nav-tab-count {
        font-size: 11px;
        padding: 2px 7px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        font-weight: 600;
    }

    /* Main Layout */
    .nav-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
    }

    /* Structure Card */
    .nav-structure-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .nav-structure-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .nav-structure-header h3 {
        margin: 0;
        font-size: 16px;
    }

    .save-indicator {
        display: none;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        color: #10b981;
        background: rgba(16, 185, 129, 0.1);
        padding: 4px 10px;
        border-radius: 6px;
    }

    .save-indicator.show {
        display: flex;
    }

    .save-indicator .material-symbols-rounded {
        font-size: 16px;
    }

    .nav-structure-body {
        padding: 16px;
    }

    /* Empty State */
    .nav-empty-state {
        text-align: center;
        padding: 48px 24px;
    }

    .nav-empty-state .material-symbols-rounded {
        font-size: 56px;
        color: var(--text-muted);
        margin-bottom: 16px;
    }

    .nav-empty-state h4 {
        margin: 0 0 8px;
        font-size: 18px;
    }

    .nav-empty-state p {
        color: var(--text-muted);
        margin: 0 0 20px;
    }

    /* Help Text */
    .nav-help-text {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-muted);
        padding: 10px 14px;
        background: var(--bg-lighter);
        border-radius: 8px;
        margin-bottom: 16px;
    }

    .nav-help-text .material-symbols-rounded {
        font-size: 18px;
        color: var(--primary);
    }

    /* Navigation Tree */
    .nav-tree {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-tree-item {
        margin-bottom: 4px;
    }

    .nav-tree-item.is-draft>.nav-tree-row {
        opacity: 0.6;
    }

    .nav-tree-item.is-editing>.nav-tree-row {
        border-color: #fff;
        background: var(--bg-card);
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
    }

    .nav-tree-row {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        background: var(--bg-lighter);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        transition: all 0.15s ease;
        cursor: grab;
    }

    .nav-tree-row:hover {
        border-color: var(--primary);
        background: var(--bg-card);
    }

    .nav-tree-item.dragging>.nav-tree-row {
        opacity: 0.5;
        transform: scale(1.02);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }

    /* Indentation */
    .nav-tree-indent {
        display: flex;
        gap: 0;
    }

    .indent-line {
        width: 20px;
        height: 100%;
        border-left: 2px solid var(--border-color);
        margin-left: 8px;
    }

    .nav-tree-children {
        list-style: none;
        padding: 0;
        margin: 4px 0 0 28px;
    }

    /* Drag Handle */
    .drag-handle {
        color: var(--text-muted);
        cursor: grab;
        padding: 2px;
        border-radius: 4px;
        transition: color 0.15s;
    }

    .drag-handle:hover {
        color: var(--primary);
    }

    /* Expand Toggle */
    .expand-toggle,
    .expand-placeholder {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .expand-toggle {
        cursor: pointer;
        color: var(--text-muted);
        transition: transform 0.2s;
    }

    .expand-toggle .material-symbols-rounded {
        font-size: 18px;
    }

    .nav-tree-item.expanded>.nav-tree-row .expand-toggle {
        transform: rotate(90deg);
    }

    /* Tree Content */
    .nav-tree-content {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
        padding-right: 12px;
    }

    .nav-tree-label {
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    .nav-tree-status {
        font-size: 10px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }

    .status-draft {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
    }

    .nav-tree-type {
        font-size: 10px;
        padding: 3px 8px;
        background: var(--bg-card);
        border-radius: 4px;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .nav-tree-url {
        font-family: 'SF Mono', Monaco, monospace;
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Drag & Drop Enhanced Feedback */
    .nav-tree-row {
        position: relative;
    }

    .nav-tree-item.dragging>.nav-tree-row {
        opacity: 0.4;
        background: var(--bg-lighter);
        border: 2px dashed var(--primary);
        border-radius: 8px;
    }

    .nav-tree-row.drag-over-above::before {
        content: '';
        position: absolute;
        top: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary);
        border-radius: 2px;
        box-shadow: 0 0 8px rgba(99, 102, 241, 0.5);
        z-index: 10;
    }

    .nav-tree-row.drag-over-below::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary);
        border-radius: 2px;
        box-shadow: 0 0 8px rgba(99, 102, 241, 0.5);
        z-index: 10;
    }

    .nav-tree-row.drag-over {
        background: rgba(99, 102, 241, 0.08);
    }

    /* Save Status Toast */
    .save-status {
        position: fixed;
        bottom: 24px;
        right: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 20px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        z-index: 1000;
        pointer-events: none;
    }

    .save-status.show {
        opacity: 1;
        transform: translateY(0);
    }

    .save-status .material-symbols-rounded {
        font-size: 20px;
        color: #10b981;
    }

    .save-status span {
        font-size: 14px;
        color: var(--text-primary);
    }

    /* Actions */
    .nav-tree-actions {
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.15s;
    }

    .nav-tree-row:hover .nav-tree-actions {
        opacity: 1;
    }

    .nav-action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        border-radius: 6px;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }

    .nav-action-btn:hover {
        background: var(--bg-lighter);
        color: var(--primary);
    }

    .nav-action-btn.nav-action-delete:hover {
        color: var(--error);
    }

    .nav-action-btn .material-symbols-rounded {
        font-size: 18px;
    }

    /* Sidebar */
    .nav-sidebar {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Edit Card */
    .nav-edit-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .nav-edit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-color);
    }

    .nav-edit-header h3 {
        margin: 0;
        font-size: 15px;
    }

    .badge-active,
    .badge-draft {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .badge-active {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }

    .badge-draft {
        background: rgba(245, 158, 11, 0.12);
        color: #f59e0b;
    }

    .nav-edit-body {
        padding: 18px;
    }

    .nav-edit-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 18px;
        border-top: 1px solid var(--border-color);
        background: var(--bg-lighter);
    }

    .nav-edit-footer-right {
        display: flex;
        gap: 8px;
    }

    .btn-delete {
        color: var(--error) !important;
        border-color: transparent !important;
    }

    .btn-delete:hover {
        background: rgba(239, 68, 68, 0.1) !important;
    }

    /* Styling Accordion */
    .styling-accordion {
        margin: 16px 0;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
    }

    .styling-accordion-header {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 14px;
        background: var(--bg-lighter);
        border: none;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-primary);
        transition: background 0.15s;
    }

    .styling-accordion-header:hover {
        background: var(--bg-hover);
    }

    .styling-accordion-header .accordion-arrow {
        margin-left: auto;
        transition: transform 0.2s;
    }

    .styling-accordion.open .accordion-arrow {
        transform: rotate(180deg);
    }

    .styling-accordion-content {
        padding: 14px;
        border-top: 1px solid var(--border-color);
        background: var(--bg-card);
    }

    /* Color Picker Row */
    .color-picker-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .color-input {
        width: 36px;
        height: 36px;
        padding: 2px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        cursor: pointer;
        background: var(--bg-card);
    }

    .color-input::-webkit-color-swatch-wrapper {
        padding: 2px;
    }

    .color-input::-webkit-color-swatch {
        border-radius: 4px;
        border: none;
    }

    .color-hex-input {
        width: 90px;
        font-family: monospace;
        font-size: 12px;
    }

    .color-input-sm {
        width: 32px;
        height: 32px;
    }

    /* Form Row (side by side) */
    .form-row {
        display: flex;
        gap: 12px;
    }

    .form-group-half {
        flex: 1;
    }

    .form-group-inline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .form-group-inline .form-label {
        margin-bottom: 0;
        min-width: 90px;
    }

    /* Badge Input Row */
    .badge-input-row {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .badge-input-row .form-input {
        flex: 1;
    }

    /* Icon Picker */
    .icon-picker-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .icon-picker-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: var(--bg-lighter);
        border: 1px dashed var(--border-color);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
        flex: 1;
    }

    .icon-picker-btn:hover {
        border-style: solid;
        border-color: var(--accent);
        background: var(--bg-hover);
    }

    .icon-picker-btn .material-symbols-rounded {
        font-size: 20px;
        color: var(--accent);
    }

    .icon-picker-label {
        font-size: 13px;
        color: var(--text-secondary);
    }

    .custom-icon-preview {
        width: 20px;
        height: 20px;
        object-fit: contain;
        border-radius: 4px;
    }

    .custom-icon-upload {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .upload-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .upload-btn:hover {
        background: var(--bg-hover);
    }

    .upload-label {
        cursor: pointer;
    }

    /* Icon Position Options */
    .icon-position-options {
        display: flex;
        gap: 12px;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        font-size: 13px;
    }

    .radio-option input[type="radio"] {
        accent-color: var(--accent);
    }

    /* Icon Picker Modal */
    .modal-lg {
        max-width: 640px;
    }

    .icon-picker-body {
        padding: 0;
    }

    .icon-search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-lighter);
    }

    .icon-search-box input {
        flex: 1;
        border: none;
        background: transparent;
        font-size: 14px;
        color: var(--text-primary);
        outline: none;
    }

    .icon-search-box .material-symbols-rounded {
        color: var(--text-muted);
    }

    .icon-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 4px;
        padding: 14px;
        max-height: 400px;
        overflow-y: auto;
    }

    .icon-option {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border: 1px solid transparent;
        border-radius: 8px;
        background: transparent;
        cursor: pointer;
        transition: all 0.15s;
    }

    .icon-option:hover {
        background: var(--bg-hover);
        border-color: var(--border-color);
    }

    .icon-option.selected {
        background: rgba(124, 58, 237, 0.15);
        border-color: var(--accent);
    }

    .icon-option .material-symbols-rounded {
        font-size: 24px;
        color: var(--text-primary);
    }

    .icon-option:hover .material-symbols-rounded {
        color: var(--accent);
    }

    /* Live Preview Panel */
    /* Info/Help Card */
    .nav-info-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .nav-info-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-lighter);
    }

    .nav-info-header .material-symbols-rounded {
        font-size: 20px;
        color: var(--primary);
    }

    .nav-info-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }

    .nav-info-body {
        padding: 16px;
    }

    /* Info Section Styles */
    .info-section {
        margin-bottom: 16px;
    }

    .info-section:last-child {
        margin-bottom: 0;
    }

    .info-section h5 {
        margin: 0 0 8px 0;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .info-section h5 .material-symbols-rounded {
        font-size: 16px;
        color: var(--accent);
    }

    .info-section ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .info-section li {
        padding: 4px 0;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .info-section li strong {
        color: var(--text-primary);
    }

    .info-section p {
        margin: 0;
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    /* Quickstart Section */
    .info-quickstart {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);
        border-radius: 10px;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .quickstart-icon {
        width: 40px;
        height: 40px;
        background: var(--accent);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .quickstart-icon .material-symbols-rounded {
        font-size: 22px;
        color: white;
    }

    .info-quickstart p {
        margin: 0;
        font-size: 13px;
        line-height: 1.5;
    }

    /* Mega Menu Info Section */
    .info-mega {
        padding: 14px;
        background: var(--bg-lighter);
        border-radius: 10px;
        border: 1px solid var(--border-color);
    }

    .info-mega h5 {
        color: var(--accent);
        margin-bottom: 10px;
    }

    .mega-desc {
        margin-bottom: 12px !important;
    }

    .mega-tip {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 10px 12px;
        background: rgba(16, 185, 129, 0.1);
        border-radius: 8px;
        border-left: 3px solid #10b981;
    }

    .mega-tip .material-symbols-rounded {
        font-size: 18px;
        color: #10b981;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .mega-tip span {
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    /* Mega Menu Intro Box (in Edit Panel) */
    .mega-intro-box {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);
        border-radius: 8px;
        border: 1px solid rgba(99, 102, 241, 0.2);
        margin-bottom: 14px;
    }

    .mega-intro-box .material-symbols-rounded {
        font-size: 20px;
        color: var(--accent);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .mega-intro-box p {
        margin: 0;
        font-size: 12px;
        line-height: 1.5;
        color: var(--text-secondary);
    }

    .mega-intro-box p strong {
        color: var(--text-primary);
    }

    /* Mega Editor Button */
    .mega-editor-section {
        margin-top: 4px;
    }

    .mega-editor-btn {
        width: 100%;
        justify-content: center;
        padding: 12px 16px;
    }

    .mega-editor-btn .material-symbols-rounded {
        font-size: 20px;
    }

    /* Mega Settings Link */
    .mega-settings-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px;
        margin-top: 12px;
        background: var(--bg-lighter);
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .mega-settings-link .material-symbols-rounded {
        font-size: 18px;
        color: var(--text-muted);
    }

    .mega-settings-link a {
        color: var(--accent);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
    }

    .mega-settings-link a:hover {
        text-decoration: underline;
    }

    .mega-settings-link .hint {
        font-size: 11px;
        color: var(--text-muted);
    }

    /* Status Legend */
    .status-legend {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .status-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot.active {
        background: #10b981;
    }

    .dot.draft {
        background: #f59e0b;
    }

    /* Quick Card */
    .nav-quick-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        margin-top: 16px;
    }

    .quick-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        color: var(--text-primary);
        text-decoration: none;
        transition: background 0.15s;
    }

    .quick-link:hover {
        background: var(--bg-lighter);
    }

    .quick-link .material-symbols-rounded:first-child {
        color: var(--primary);
    }

    .quick-link span:nth-child(2) {
        flex: 1;
        font-size: 14px;
    }

    .quick-link .material-symbols-rounded:last-child {
        color: var(--text-muted);
        font-size: 18px;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .required {
        color: var(--error);
    }

    .form-hint {
        display: block;
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .field-info {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0;
        padding: 12px;
        background: var(--bg-lighter);
        border-radius: 8px;
        border-left: 3px solid var(--primary);
    }

    /* Button icon styling */
    .btn .material-symbols-rounded {
        font-size: 18px;
        margin-right: 4px;
    }

    .btn-sm .material-symbols-rounded {
        font-size: 16px;
    }

    .form-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .checkbox-label {
        font-size: 14px;
    }

    .field-empty-state {
        padding: 12px;
        background: var(--bg-lighter);
        border-radius: 8px;
        text-align: center;
    }

    .field-empty-state p {
        margin: 0 0 10px;
        font-size: 13px;
        color: var(--text-muted);
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal-dialog {
        background: var(--bg-card);
        border-radius: 16px;
        width: 100%;
        max-width: 480px;
        overflow: hidden;
        box-shadow: 0 24px 48px rgba(0, 0, 0, 0.3);
    }

    .modal-sm {
        max-width: 380px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-header h3 {
        margin: 0;
        font-size: 17px;
    }

    .modal-close {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        border-radius: 8px;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.15s;
    }

    .modal-close:hover {
        background: var(--bg-lighter);
        color: var(--text-primary);
    }

    .modal-body {
        padding: 22px;
    }

    .modal-hint {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 8px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 22px;
        border-top: 1px solid var(--border-color);
        background: var(--bg-lighter);
    }

    .btn-danger {
        background: var(--error) !important;
        border-color: var(--error) !important;
        color: #fff !important;
    }

    .btn-danger:hover {
        opacity: 0.9;
    }

    /* Alert box tweaks */
    .alert {
        position: relative;
    }

    .alert-close {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: inherit;
        cursor: pointer;
        opacity: 0.6;
    }

    .alert-close:hover {
        opacity: 1;
    }
</style>

<script>
    // ============================================
    // NAVIGATION PAGE - COMPLETE JAVASCRIPT
    // All functions exposed to window for onclick
    // ============================================

    // === STYLING ACCORDION ===
    window.toggleStylingAccordion = function (accordionId = null) {
        let accordion, content;

        if (accordionId) {
            // Specific accordion by ID (e.g., mega-accordion)
            accordion = document.getElementById(accordionId);
            if (accordion) {
                content = accordion.querySelector('.styling-accordion-content');
            }
        } else {
            // Default styling accordion
            accordion = document.querySelector('.styling-accordion:not(#mega-accordion)');
            content = document.getElementById('styling-content');
        }

        if (accordion && content) {
            const isOpen = accordion.classList.contains('open');
            accordion.classList.toggle('open');
            content.style.display = isOpen ? 'none' : 'block';
        }
    };
    // === COLOR PICKER BIDIRECTIONAL SYNC ===
    window.syncColorToHex = function (colorId, hexId) {
        const colorInput = document.getElementById(colorId);
        const hexInput = document.getElementById(hexId);
        if (colorInput && hexInput) {
            hexInput.value = colorInput.value.toUpperCase();
        }
    };

    window.syncHexToColor = function (hexId, colorId) {
        const hexInput = document.getElementById(hexId);
        const colorInput = document.getElementById(colorId);
        if (hexInput && colorInput) {
            let hex = hexInput.value.trim();
            // Add # if missing
            if (hex && !hex.startsWith('#')) hex = '#' + hex;
            // Validate hex format
            if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                colorInput.value = hex;
            }
        }
    };

    window.clearColorField = function (colorId, hexId) {
        const colorInput = document.getElementById(colorId);
        const hexInput = document.getElementById(hexId);
        if (colorInput) colorInput.value = colorId.includes('bg') ? '#ffffff' : '#000000';
        if (hexInput) hexInput.value = '';
    };

    // === CUSTOM ICON UPLOAD ===
    window.handleIconUpload = function (input) {
        const file = input.files[0];
        if (!file) return;

        // Validate size (100KB max)
        if (file.size > 100 * 1024) {
            adminModal.error('Datei zu groß. Maximal 100KB erlaubt.', { title: 'Dateigröße überschritten' });
            input.value = '';
            return;
        }

        // Read as data URL
        const reader = new FileReader();
        reader.onload = function (e) {
            const dataUrl = e.target.result;

            // Update hidden input
            document.getElementById('custom_icon_url').value = dataUrl;
            document.getElementById('edit_icon').value = ''; // Clear material icon

            // Update picker button
            const pickerBtn = document.querySelector('.icon-picker-btn');
            if (pickerBtn) {
                pickerBtn.innerHTML = `
                    <img src="${dataUrl}" class="custom-icon-preview" alt="">
                    <span class="icon-picker-label">Eigenes Icon</span>
                `;
            }

            // Show position options
            const positionGroup = document.getElementById('icon-position-group');
            if (positionGroup) positionGroup.style.display = 'block';
        };
        reader.readAsDataURL(file);
    };

    // === ICON PICKER FUNCTIONS ===
    window.openIconPicker = function () {
        const modal = document.getElementById('icon-modal');
        if (modal) {
            modal.style.display = 'flex';
            // Clear search
            const searchInput = document.getElementById('icon-search');
            if (searchInput) {
                searchInput.value = '';
                filterIcons('');
            }
            // Highlight current selection
            const currentIcon = document.getElementById('edit_icon')?.value;
            document.querySelectorAll('.icon-option').forEach(btn => {
                btn.classList.toggle('selected', btn.dataset.icon === currentIcon);
            });
        }
    };

    window.closeIconPicker = function () {
        const modal = document.getElementById('icon-modal');
        if (modal) modal.style.display = 'none';
    };

    window.selectIcon = function (iconName) {
        // Update hidden input
        const iconInput = document.getElementById('edit_icon');
        if (iconInput) iconInput.value = iconName;

        // Update the picker button display
        const pickerBtn = document.querySelector('.icon-picker-btn');
        if (pickerBtn) {
            pickerBtn.innerHTML = `
                <span class="material-symbols-rounded">${iconName}</span>
                <span class="icon-picker-label">${iconName}</span>
            `;
        }

        // Show icon position options
        const positionGroup = document.getElementById('icon-position-group');
        if (positionGroup) positionGroup.style.display = 'block';

        // Close modal
        closeIconPicker();
    };

    window.clearIcon = function () {
        const iconInput = document.getElementById('edit_icon');
        if (iconInput) iconInput.value = '';

        // Update picker button
        const pickerBtn = document.querySelector('.icon-picker-btn');
        if (pickerBtn) {
            pickerBtn.innerHTML = `
                <span class="material-symbols-rounded">add</span>
                <span class="icon-picker-label">Icon wählen</span>
            `;
        }

        // Hide position options
        const positionGroup = document.getElementById('icon-position-group');
        if (positionGroup) positionGroup.style.display = 'none';
    };

    // === MEGA MENU FUNCTIONS ===
    window.toggleMegaOptions = function (enabled) {
        const megaOptions = document.getElementById('mega-options');
        if (megaOptions) {
            megaOptions.style.display = enabled ? 'block' : 'none';
        }
    };

    window.filterIcons = function (query) {
        const normalizedQuery = query.toLowerCase().trim();
        document.querySelectorAll('.icon-option').forEach(btn => {
            const iconName = btn.dataset.icon || '';
            const matches = iconName.includes(normalizedQuery);
            btn.style.display = matches ? 'flex' : 'none';
        });
    };

    // === MODAL FUNCTIONS ===
    window.openAddModal = function () {
        const modal = document.getElementById('add-modal');
        if (modal) {
            modal.style.display = 'flex';
            const labelInput = document.getElementById('new_label');
            if (labelInput) labelInput.focus();
        }
    };

    window.closeAddModal = function () {
        const modal = document.getElementById('add-modal');
        if (modal) modal.style.display = 'none';
    };

    window.confirmDelete = function (id, name) {
        const modal = document.getElementById('delete-modal');
        const nameEl = document.getElementById('delete-item-name');
        const linkEl = document.getElementById('delete-confirm-link');
        if (modal && nameEl && linkEl) {
            nameEl.textContent = name;
            linkEl.href = '?page=shop/navigation&tab=<?= $currentTab ?>&action=delete&item_id=' + id;
            modal.style.display = 'flex';
        }
    };

    window.closeDeleteModal = function () {
        const modal = document.getElementById('delete-modal');
        if (modal) modal.style.display = 'none';
    };

    window.toggleItemStatus = function (id) {
        window.location.href = '?page=shop/navigation&tab=<?= $currentTab ?>&action=toggle&item_id=' + id;
    };

    // === FIELD TOGGLE FUNCTIONS ===
    window.toggleAddFields = function (type) {
        document.querySelectorAll('#add-form .dynamic-field').forEach(el => el.style.display = 'none');
        if (type === 'url') {
            document.getElementById('add-url-field').style.display = 'block';
        } else if (type === 'page') {
            document.getElementById('add-page-field').style.display = 'block';
        } else if (type === 'category') {
            document.getElementById('add-category-field').style.display = 'block';
        } else if (type === 'custom') {
            document.getElementById('add-anchor-field').style.display = 'block';
        }
    };

    window.toggleEditFields = function (type) {
        document.querySelectorAll('#edit-form .dynamic-field').forEach(el => el.style.display = 'none');
        if (type === 'url' || type === 'custom') {
            const field = document.getElementById('edit-url-field');
            if (field) field.style.display = 'block';
        } else if (type === 'page') {
            const field = document.getElementById('edit-page-field');
            if (field) field.style.display = 'block';
        } else if (type === 'category') {
            const field = document.getElementById('edit-category-field');
            if (field) field.style.display = 'block';
        }
    };

    // === FORM SUBMIT HANDLERS ===
    const addForm = document.getElementById('add-form');
    if (addForm) {
        addForm.addEventListener('submit', function (e) {
            const type = document.getElementById('new_type');
            if (type && type.value === 'category') {
                const categoryUrl = document.getElementById('new_category_url');
                const urlField = document.getElementById('new_url');
                if (categoryUrl && urlField) {
                    urlField.value = categoryUrl.value;
                }
            }
        });
    }

    const editForm = document.getElementById('edit-form');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            const type = document.getElementById('type');
            if (type && type.value === 'category') {
                const categoryUrl = document.getElementById('edit_category_url');
                const urlField = document.getElementById('url');
                if (categoryUrl && urlField) {
                    urlField.value = categoryUrl.value;
                }
            }
        });
        // Initialize edit fields based on current type
        const typeSelect = document.getElementById('type');
        if (typeSelect) {
            toggleEditFields(typeSelect.value);
        }
    }

    // === MODAL CLOSE ON CLICK OUTSIDE ===
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });

    // === ESC KEY TO CLOSE MODALS ===
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
        }
    });

    // === EXPAND/COLLAPSE CHILDREN ===
    document.querySelectorAll('.expand-toggle').forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            const item = this.closest('.nav-tree-item');
            if (!item) return;
            item.classList.toggle('expanded');
            const children = item.querySelector('.nav-tree-children');
            if (children) {
                children.style.display = item.classList.contains('expanded') ? 'block' : 'none';
            }
        });
    });

    // === INITIALIZE EXPANDED STATE ===
    document.querySelectorAll('.nav-tree-item.has-children').forEach(item => {
        item.classList.add('expanded');
        const children = item.querySelector('.nav-tree-children');
        if (children) children.style.display = 'block';
    });

    // ============================================
    // DRAG & DROP - CMS STYLE (1:1)
    // ============================================
    const sortableList = document.getElementById('sortable-nav');

    if (sortableList) {
        let draggedItem = null;

        // Helper to clear all drag indicators
        function clearDragIndicators() {
            document.querySelectorAll('.nav-tree-row.drag-over, .nav-tree-row.drag-over-above, .nav-tree-row.drag-over-below').forEach(r => {
                r.classList.remove('drag-over', 'drag-over-above', 'drag-over-below');
            });
        }

        sortableList.querySelectorAll('.nav-tree-item').forEach(item => {
            const row = item.querySelector('.nav-tree-row');
            if (!row) return;

            row.draggable = true;

            row.addEventListener('dragstart', function (e) {
                draggedItem = item;
                item.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', item.dataset.id);

                // Slightly delay to show ghost properly
                setTimeout(() => {
                    item.style.opacity = '0.4';
                }, 0);
            });

            row.addEventListener('dragend', function () {
                item.classList.remove('dragging');
                item.style.opacity = '';
                clearDragIndicators();
                updateParentVisuals();
                saveOrder();
            });

            row.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';

                if (!draggedItem || draggedItem === item) return;

                // Only allow within same parent (hierarchy lock for safety)
                if (draggedItem.parentElement !== item.parentElement) return;

                // Clear previous indicators
                clearDragIndicators();

                // Determine if dropping above or below based on mouse position
                const rect = this.getBoundingClientRect();
                const midY = rect.top + rect.height / 2;

                if (e.clientY < midY) {
                    this.classList.add('drag-over-above');
                } else {
                    this.classList.add('drag-over-below');
                }
                this.classList.add('drag-over');
            });

            row.addEventListener('dragleave', function (e) {
                // Only clear if actually leaving this element
                const rect = this.getBoundingClientRect();
                if (e.clientX < rect.left || e.clientX > rect.right ||
                    e.clientY < rect.top || e.clientY > rect.bottom) {
                    this.classList.remove('drag-over', 'drag-over-above', 'drag-over-below');
                }
            });

            row.addEventListener('drop', function (e) {
                e.preventDefault();

                const wasAbove = this.classList.contains('drag-over-above');
                clearDragIndicators();

                if (!draggedItem || draggedItem === item) return;

                // Only allow within same parent
                if (draggedItem.parentElement !== item.parentElement) return;

                // Insert based on where the indicator was
                if (wasAbove) {
                    item.before(draggedItem);
                } else {
                    item.after(draggedItem);
                }
            });
        });
    }

    // === UPDATE PARENT VISUALS (arrows) ===
    function updateParentVisuals() {
        document.querySelectorAll('.nav-tree-item').forEach(item => {
            const childrenList = item.querySelector('.nav-tree-children');
            const hasChildren = childrenList && childrenList.querySelectorAll('.nav-tree-item').length > 0;

            if (hasChildren) {
                item.classList.add('has-children');
            } else {
                item.classList.remove('has-children');
                item.classList.remove('expanded');
            }
        });
    }

    // === SAVE ORDER TO SERVER ===
    function saveOrder() {
        const allUpdates = [];

        function traverse(list, parentId = null) {
            if (!list) return;
            Array.from(list.children).forEach((li, index) => {
                if (!li.classList.contains('nav-tree-item')) return;

                allUpdates.push({
                    id: li.dataset.id,
                    parent_id: parentId,
                    sort_order: index
                });

                const childrenUl = li.querySelector('.nav-tree-children');
                if (childrenUl) {
                    traverse(childrenUl, li.dataset.id);
                }
            });
        }

        traverse(document.getElementById('sortable-nav'));

        const formData = new FormData();
        formData.append('ajax_action', 'update_order');
        formData.append('order', JSON.stringify(allUpdates));

        fetch('?page=shop/navigation&tab=<?= $currentTab ?>', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const status = document.getElementById('save-status');
                    if (status) {
                        status.classList.add('show');
                        setTimeout(() => status.classList.remove('show'), 2000);
                    }
                }
            })
            .catch(error => {
                console.error('Error saving order:', error);
            });
    }
</script>