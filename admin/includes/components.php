<?php
/**
 * ============================================
 * ADMIN PANEL - UI KOMPONENTEN
 * ============================================
 * Wiederverwendbare UI-Bausteine als Funktionen
 * Alle Seiten MÜSSEN diese Komponenten verwenden
 * ============================================
 */

/**
 * Page Header
 * Standardisierter Kopfbereich für jede Seite
 * 
 * @param string $title Seitentitel
 * @param string $subtitle Beschreibung (optional)
 * @param array $actions Action-Buttons (optional)
 */
function renderPageHeader($title, $subtitle = '', $actions = []) {
    ?>
    <div class="page-header">
        <div class="page-header-content">
            <h1><?php echo htmlspecialchars($title); ?></h1>
            <?php if ($subtitle): ?>
            <p class="page-subtitle"><?php echo htmlspecialchars($subtitle); ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($actions)): ?>
        <div class="page-header-actions">
            <?php foreach ($actions as $action): ?>
            <?php renderButton($action); ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Action Bar
 * Sekundäre Aktionsleiste unter dem Header
 * 
 * @param array $tabs Tab-Buttons (optional)
 * @param array $filters Filter-Elemente (optional)
 */
function renderActionBar($tabs = [], $filters = []) {
    if (!empty($tabs)): ?>
    <div class="tabs">
        <?php foreach ($tabs as $index => $tab): ?>
        <button class="tab<?php echo $index === 0 ? ' active' : ''; ?>"><?php echo htmlspecialchars($tab); ?></button>
        <?php endforeach; ?>
    </div>
    <?php endif;
    
    if (!empty($filters)): ?>
    <div class="filters">
        <?php foreach ($filters as $filter): ?>
            <?php if ($filter['type'] === 'search'): ?>
            <div class="filter-search">
                <span class="material-symbols-rounded">search</span>
                <input type="text" placeholder="<?php echo htmlspecialchars($filter['placeholder'] ?? 'Suchen...'); ?>">
            </div>
            <?php elseif ($filter['type'] === 'select'): ?>
            <select class="filter-select">
                <?php foreach ($filter['options'] as $option): ?>
                <option><?php echo htmlspecialchars($option); ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif;
}

/**
 * Button
 * Standardisierter Button
 * 
 * @param array $config Button-Konfiguration
 */
function renderButton($config) {
    $label = $config['label'] ?? '';
    $icon = $config['icon'] ?? '';
    $type = $config['type'] ?? 'default';
    $size = $config['size'] ?? '';
    $disabled = $config['disabled'] ?? false;
    $href = $config['href'] ?? '';
    
    $classes = ['btn'];
    if ($type !== 'default') $classes[] = 'btn-' . $type;
    if ($size) $classes[] = 'btn-' . $size;
    
    $tag = $href ? 'a' : 'button';
    $hrefAttr = $href ? ' href="' . htmlspecialchars($href) . '"' : '';
    $disabledAttr = $disabled ? ' disabled' : '';
    ?>
    <<?php echo $tag; ?> class="<?php echo implode(' ', $classes); ?>"<?php echo $hrefAttr . $disabledAttr; ?>>
        <?php if ($icon): ?><span class="material-symbols-rounded"><?php echo htmlspecialchars($icon); ?></span><?php endif; ?>
        <?php if ($label): echo htmlspecialchars($label); endif; ?>
    </<?php echo $tag; ?>>
    <?php
}

/**
 * Card
 * Container-Komponente mit Header/Body/Footer
 * 
 * @param string $title Card-Titel (optional)
 * @param array $headerActions Header-Aktionen (optional)
 * @param callable $bodyContent Callback für Body-Inhalt
 * @param callable $footerContent Callback für Footer-Inhalt (optional)
 */
function renderCard($title = '', $headerActions = [], $bodyContent = null, $footerContent = null) {
    ?>
    <div class="card">
        <?php if ($title || !empty($headerActions)): ?>
        <div class="card-header">
            <?php if ($title): ?><h3><?php echo htmlspecialchars($title); ?></h3><?php endif; ?>
            <?php if (!empty($headerActions)): ?>
            <div class="card-header-actions">
                <?php foreach ($headerActions as $action): ?>
                <?php renderButton($action); ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="card-body">
            <?php if (is_callable($bodyContent)) $bodyContent(); ?>
        </div>
        <?php if (is_callable($footerContent)): ?>
        <div class="card-footer">
            <?php $footerContent(); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Table
 * Standardisierte Tabelle mit optionaler Selektion
 * 
 * @param array $columns Spalten-Definitionen
 * @param array $rows Zeilen-Daten
 * @param bool $selectable Zeilen selektierbar
 */
function renderTable($columns, $rows, $selectable = false) {
    ?>
    <table class="table">
        <thead>
            <tr>
                <?php if ($selectable): ?>
                <th><input type="checkbox" class="select-all"></th>
                <?php endif; ?>
                <?php foreach ($columns as $column): ?>
                <th><?php echo htmlspecialchars($column['label']); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
            <tr>
                <?php if ($selectable): ?>
                <td><input type="checkbox"></td>
                <?php endif; ?>
                <?php foreach ($columns as $column): ?>
                <td>
                    <?php 
                    $key = $column['key'];
                    $value = $row[$key] ?? '';
                    
                    if (isset($column['render']) && is_callable($column['render'])) {
                        echo $column['render']($value, $row);
                    } else {
                        echo htmlspecialchars($value);
                    }
                    ?>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * Badge
 * Status-Markierung
 * 
 * @param string $text Badge-Text
 * @param string $type Typ (success, warning, error, info, default)
 */
function renderBadge($text, $type = 'default') {
    ?>
    <span class="badge badge-<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($text); ?></span>
    <?php
}

/**
 * Alert
 * Hinweis-Box
 * 
 * @param string $message Nachricht
 * @param string $type Typ (info, success, warning, error)
 * @param bool $dismissible Schließbar
 */
function renderAlert($message, $type = 'info', $dismissible = true) {
    $icons = [
        'info' => 'info',
        'success' => 'check_circle',
        'warning' => 'warning',
        'error' => 'error',
    ];
    $icon = $icons[$type] ?? 'info';
    ?>
    <div class="alert alert-<?php echo htmlspecialchars($type); ?>">
        <span class="material-symbols-rounded"><?php echo $icon; ?></span>
        <div class="alert-content"><?php echo $message; ?></div>
        <?php if ($dismissible): ?>
        <button class="alert-close"><span class="material-symbols-rounded">close</span></button>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Empty State
 * Leerer Zustand mit Illustration
 * 
 * @param string $icon Material Icon
 * @param string $title Überschrift
 * @param string $description Beschreibung
 * @param array $action Primäre Aktion (optional)
 */
function renderEmptyState($icon, $title, $description = '', $action = []) {
    ?>
    <div class="empty-state">
        <span class="material-symbols-rounded empty-state-icon"><?php echo htmlspecialchars($icon); ?></span>
        <h3 class="empty-state-title"><?php echo htmlspecialchars($title); ?></h3>
        <?php if ($description): ?>
        <p class="empty-state-description"><?php echo htmlspecialchars($description); ?></p>
        <?php endif; ?>
        <?php if (!empty($action)): ?>
        <div class="empty-state-action">
            <?php renderButton($action); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Form Group
 * Formular-Gruppe mit Label
 * 
 * @param string $label Label-Text
 * @param string $type Input-Typ (text, email, select, textarea, checkbox)
 * @param array $config Zusätzliche Konfiguration
 */
function renderFormGroup($label, $type = 'text', $config = []) {
    $name = $config['name'] ?? '';
    $value = $config['value'] ?? '';
    $placeholder = $config['placeholder'] ?? '';
    $required = $config['required'] ?? false;
    $hint = $config['hint'] ?? '';
    $options = $config['options'] ?? [];
    $checked = $config['checked'] ?? false;
    ?>
    <div class="form-group">
        <?php if ($type === 'checkbox'): ?>
        <label class="form-checkbox">
            <input type="checkbox" name="<?php echo htmlspecialchars($name); ?>"<?php echo $checked ? ' checked' : ''; ?>>
            <span><?php echo htmlspecialchars($label); ?></span>
        </label>
        <?php else: ?>
        <label class="form-label"><?php echo htmlspecialchars($label); ?><?php if ($required): ?> <span style="color:var(--error)">*</span><?php endif; ?></label>
        
        <?php if ($type === 'select'): ?>
        <select class="form-select" name="<?php echo htmlspecialchars($name); ?>"<?php echo $required ? ' required' : ''; ?>>
            <?php foreach ($options as $optValue => $optLabel): ?>
            <option value="<?php echo htmlspecialchars($optValue); ?>"<?php echo $value == $optValue ? ' selected' : ''; ?>><?php echo htmlspecialchars($optLabel); ?></option>
            <?php endforeach; ?>
        </select>
        
        <?php elseif ($type === 'textarea'): ?>
        <textarea class="form-textarea" name="<?php echo htmlspecialchars($name); ?>" placeholder="<?php echo htmlspecialchars($placeholder); ?>"<?php echo $required ? ' required' : ''; ?>><?php echo htmlspecialchars($value); ?></textarea>
        
        <?php else: ?>
        <input type="<?php echo htmlspecialchars($type); ?>" class="form-input" name="<?php echo htmlspecialchars($name); ?>" value="<?php echo htmlspecialchars($value); ?>" placeholder="<?php echo htmlspecialchars($placeholder); ?>"<?php echo $required ? ' required' : ''; ?>>
        <?php endif; ?>
        
        <?php if ($hint): ?>
        <p class="form-hint"><?php echo htmlspecialchars($hint); ?></p>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Toggle Switch
 * Ein/Aus-Schalter
 * 
 * @param string $name Name des Inputs
 * @param bool $checked Initial-Status
 */
function renderToggle($name, $checked = false) {
    ?>
    <label class="toggle">
        <input type="checkbox" name="<?php echo htmlspecialchars($name); ?>"<?php echo $checked ? ' checked' : ''; ?>>
        <span class="toggle-slider"></span>
    </label>
    <?php
}

/**
 * Pagination
 * Seitennavigation
 * 
 * @param int $current Aktuelle Seite
 * @param int $total Gesamtanzahl Seiten
 * @param int $perPage Einträge pro Seite
 * @param int $totalItems Gesamtanzahl Einträge
 */
function renderPagination($current, $total, $perPage = 10, $totalItems = 0) {
    $start = (($current - 1) * $perPage) + 1;
    $end = min($current * $perPage, $totalItems);
    ?>
    <div class="pagination">
        <span class="pagination-info"><?php echo $start; ?>-<?php echo $end; ?> von <?php echo $totalItems; ?></span>
        <div class="pagination-buttons">
            <button class="btn btn-sm"<?php echo $current <= 1 ? ' disabled' : ''; ?>>
                <span class="material-symbols-rounded">chevron_left</span>
            </button>
            <?php for ($i = 1; $i <= min($total, 5); $i++): ?>
            <button class="btn btn-sm<?php echo $i === $current ? ' btn-primary' : ''; ?>"><?php echo $i; ?></button>
            <?php endfor; ?>
            <button class="btn btn-sm"<?php echo $current >= $total ? ' disabled' : ''; ?>>
                <span class="material-symbols-rounded">chevron_right</span>
            </button>
        </div>
    </div>
    <?php
}

/**
 * Stats Grid
 * Statistik-Karten Raster
 * 
 * @param array $stats Array von Statistiken mit label, value, change (optional)
 */
function renderStatsGrid($stats) {
    ?>
    <div class="stats-grid">
        <?php foreach ($stats as $stat): ?>
        <div class="stat-card">
            <div class="stat-card-label"><?php echo htmlspecialchars($stat['label']); ?></div>
            <div class="stat-card-value"><?php echo htmlspecialchars($stat['value']); ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * KPI Card
 * Key Performance Indicator Karte
 * 
 * @param string $title Titel
 * @param string $value Wert
 * @param string $change Veränderung (z.B. "+12,5%")
 * @param bool $positive Positive Veränderung
 * @param string $link Link-URL (optional)
 * @param string $linkText Link-Text (optional)
 */
function renderKPICard($title, $value, $change = '', $positive = true, $link = '', $linkText = 'Details') {
    ?>
    <div class="kpi-card">
        <div class="kpi-header">
            <span class="kpi-title"><?php echo htmlspecialchars($title); ?></span>
            <?php if ($link): ?>
            <a href="<?php echo htmlspecialchars($link); ?>" class="kpi-link"><?php echo htmlspecialchars($linkText); ?></a>
            <?php endif; ?>
        </div>
        <div class="kpi-value"><?php echo htmlspecialchars($value); ?></div>
        <?php if ($change): ?>
        <div class="kpi-change <?php echo $positive ? 'positive' : 'negative'; ?>">
            <span class="material-symbols-rounded"><?php echo $positive ? 'trending_up' : 'trending_down'; ?></span>
            <?php echo htmlspecialchars($change); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}
