<?php 
/** 
 * Shop - CMS Seiten (Liste)
 * Full CRUD: List with delete/toggle/bulk actions
 */

// Check permission
Auth::requirePermission('cms.manage');

// Get shop ID
$shop = Shop::getDefault();
$shopId = $shop['id'] ?? 1;

// Handle actions
$result = null;
$action = $_GET['action'] ?? '';
$pageId = intval($_GET['id'] ?? 0);

// Handle bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    if ($_POST['bulk_action'] === 'delete' && !empty($_POST['selected_ids'])) {
        $ids = array_map('intval', $_POST['selected_ids']);
        $deletedCount = 0;
        foreach ($ids as $id) {
            $deleteResult = CmsController::handleDelete($id);
            if ($deleteResult['success']) $deletedCount++;
        }
        // Redirect to clean URL with success message in session
        $_SESSION['cms_flash_message'] = ['success' => true, 'message' => $deletedCount . ' Seite(n) wurden gelöscht.'];
        echo '<script>window.location.href = "?page=shop/cms";</script>';
        return;
    }
} elseif (!empty($action) && $pageId > 0) {
    if ($action === 'delete') {
        $result = CmsController::handleDelete($pageId);
        // Redirect to clean URL
        $_SESSION['cms_flash_message'] = $result;
        echo '<script>window.location.href = "?page=shop/cms";</script>';
        return;
    } elseif ($action === 'toggle') {
        $result = CmsController::handleToggleStatus($pageId);
        // Redirect to clean URL
        $_SESSION['cms_flash_message'] = $result;
        echo '<script>window.location.href = "?page=shop/cms";</script>';
        return;
    }
}

// Check for flash message from redirect
if (isset($_SESSION['cms_flash_message'])) {
    $result = $_SESSION['cms_flash_message'];
    unset($_SESSION['cms_flash_message']);
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$searchQuery = trim($_GET['search'] ?? '');

// Build filters
$filters = [];
if (!empty($statusFilter)) {
    $filters['status'] = $statusFilter;
}
if (!empty($searchQuery)) {
    $filters['search'] = $searchQuery;
}

// Get pages from database
$pages = CmsPage::allForShop($shopId, $filters);
$totalPages = CmsPage::countForShop($shopId);
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>CMS Seiten</h1>
        <p class="page-subtitle">Rechtliche Seiten und Informationsseiten verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=shop/cms_settings" class="btn" title="Reihenfolge & Einstellungen">
            <span class="material-symbols-rounded">tune</span> Einstellungen
        </a>
        <a href="?page=shop/cms_create" class="btn btn-primary">
            <span class="material-symbols-rounded">add</span> Neue Seite
        </a>
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

<!-- Info Box (dismissible, saved in localStorage) -->
<div class="alert alert-info" id="cms-info-box" style="margin-bottom:20px;display:none;">
    <span class="material-symbols-rounded">info</span>
    <div class="alert-content">
        <strong>CMS-Seiten</strong> sind für rechtliche und informative Inhalte gedacht: Impressum, AGB, Datenschutz, Widerrufsbelehrung, Über uns, Kontakt, FAQ. 
        Produktseiten werden im <a href="?page=catalog/products" style="color:inherit;text-decoration:underline;">Katalog</a> verwaltet.
    </div>
    <button type="button" class="alert-close" onclick="dismissInfoBox()"><span class="material-symbols-rounded">close</span></button>
</div>

<form method="POST" action="?page=shop/cms" id="cms-list-form">
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <h3>Alle Seiten (<?= $totalPages ?>)</h3>
            <!-- Bulk Actions -->
            <div class="bulk-actions" id="bulk-actions" style="display:none;">
                <span id="selected-count">0</span> ausgewählt
                <button type="button" class="btn btn-sm" style="color:var(--error);" onclick="confirmBulkDelete()">
                    <span class="material-symbols-rounded">delete</span> Ausgewählte löschen
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="filters" style="margin-bottom:20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                <div class="filter-search">
                    <span class="material-symbols-rounded">search</span>
                    <input type="text" 
                           id="search-input"
                           placeholder="Seiten durchsuchen..." 
                           value="<?= htmlspecialchars($searchQuery) ?>">
                </div>
                <select id="status-filter" class="filter-select">
                    <option value="">Alle Status</option>
                    <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Veröffentlicht</option>
                    <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>Entwurf</option>
                </select>
                <?php if (!empty($searchQuery) || !empty($statusFilter)): ?>
                    <a href="?page=shop/cms" class="btn btn-sm">Filter zurücksetzen</a>
                <?php endif; ?>
            </div>
            
            <?php if (empty($pages)): ?>
                <!-- Empty State -->
                <div class="empty-state" style="text-align:center;padding:60px 20px;">
                    <span class="material-symbols-rounded" style="font-size:64px;color:var(--text-muted);margin-bottom:16px;display:block;">article</span>
                    <?php if (!empty($filters)): ?>
                        <h3 style="margin-bottom:8px;">Keine Seiten gefunden</h3>
                        <p style="color:var(--text-muted);margin-bottom:20px;">Keine Seiten entsprechen Ihren Filterkriterien.</p>
                        <a href="?page=shop/cms" class="btn">Filter zurücksetzen</a>
                    <?php else: ?>
                        <h3 style="margin-bottom:8px;">Noch keine Seiten vorhanden</h3>
                        <p style="color:var(--text-muted);margin-bottom:20px;">Erstellen Sie rechtliche Seiten wie Impressum, AGB oder Datenschutz.</p>
                        <a href="?page=shop/cms_create" class="btn btn-primary">
                            <span class="material-symbols-rounded">add</span> Erste Seite erstellen
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Pages Table -->
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:40px;"><input type="checkbox" id="select-all" onchange="toggleSelectAll(this)"></th>
                            <th>Titel</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th>Aktualisiert</th>
                            <th style="width:150px;">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pages as $cmsPage): ?>
                        <tr data-id="<?= $cmsPage['id'] ?>">
                            <td><input type="checkbox" name="selected_ids[]" value="<?= $cmsPage['id'] ?>" class="row-checkbox" onchange="updateBulkActions()"></td>
                            <td>
                                <strong>
                                    <a href="?page=shop/cms_edit&id=<?= $cmsPage['id'] ?>">
                                        <?= htmlspecialchars($cmsPage['title']) ?>
                                    </a>
                                </strong>
                            </td>
                            <td style="color:var(--text-muted);font-family:monospace;font-size:12px;">
                                /<?= htmlspecialchars($cmsPage['slug']) ?>
                            </td>
                            <td>
                                <?php if ($cmsPage['is_active']): ?>
                                    <span class="badge badge-success">Veröffentlicht</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Entwurf</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:12px;color:var(--text-muted);">
                                <?= date('d.m.Y H:i', strtotime($cmsPage['updated_at'])) ?>
                            </td>
                            <td class="table-actions">
                                <a href="?page=shop/cms_edit&id=<?= $cmsPage['id'] ?>" class="btn btn-sm" title="Bearbeiten">
                                    <span class="material-symbols-rounded">edit</span>
                                </a>
                                <a href="?page=shop/cms&action=toggle&id=<?= $cmsPage['id'] ?>" 
                                   class="btn btn-sm" 
                                   title="<?= $cmsPage['is_active'] ? 'Als Entwurf speichern' : 'Veröffentlichen' ?>">
                                    <span class="material-symbols-rounded"><?= $cmsPage['is_active'] ? 'unpublished' : 'publish' ?></span>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm" 
                                        title="Löschen"
                                        onclick="confirmDelete(<?= $cmsPage['id'] ?>, '<?= htmlspecialchars(addslashes($cmsPage['title'])) ?>')">
                                    <span class="material-symbols-rounded">delete</span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <input type="hidden" name="bulk_action" id="bulk-action-input" value="">
</form>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#1a1a2e;border-radius:12px;padding:24px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.5);border:1px solid var(--border-color);">
        <h3 style="margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <span class="material-symbols-rounded" style="color:var(--error);">warning</span>
            Seite löschen?
        </h3>
        <p id="delete-modal-text" style="color:var(--text-muted);margin-bottom:24px;">
            Möchten Sie diese Seite wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.
        </p>
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <button type="button" class="btn" onclick="closeDeleteModal()">Abbrechen</button>
            <a href="#" id="delete-confirm-btn" class="btn" style="background:var(--error);color:white;border-color:var(--error);">
                <span class="material-symbols-rounded">delete</span> Löschen
            </a>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div id="bulk-delete-modal" class="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#1a1a2e;border-radius:12px;padding:24px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.5);border:1px solid var(--border-color);">
        <h3 style="margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <span class="material-symbols-rounded" style="color:var(--error);">warning</span>
            Mehrere Seiten löschen?
        </h3>
        <p id="bulk-delete-modal-text" style="color:var(--text-muted);margin-bottom:24px;">
            Möchten Sie die ausgewählten Seiten wirklich löschen?
        </p>
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <button type="button" class="btn" onclick="closeBulkDeleteModal()">Abbrechen</button>
            <button type="button" class="btn" style="background:var(--error);color:white;border-color:var(--error);" onclick="executeBulkDelete()">
                <span class="material-symbols-rounded">delete</span> Alle löschen
            </button>
        </div>
    </div>
</div>

<script>
// Live search with debounce
let searchTimeout;
const searchInput = document.getElementById('search-input');
const statusFilter = document.getElementById('status-filter');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    });
}

if (statusFilter) {
    statusFilter.addEventListener('change', function() {
        applyFilters();
    });
}

function applyFilters() {
    const search = searchInput ? searchInput.value : '';
    const status = statusFilter ? statusFilter.value : '';
    
    // Save cursor position for focus restoration
    if (searchInput) {
        sessionStorage.setItem('cms_search_cursor', searchInput.selectionStart);
        sessionStorage.setItem('cms_search_focus', 'true');
    }
    
    let url = '?page=shop/cms';
    if (search) url += '&search=' + encodeURIComponent(search);
    if (status) url += '&status=' + encodeURIComponent(status);
    window.location.href = url;
}

// Select all / bulk actions
function toggleSelectAll(checkbox) {
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    rowCheckboxes.forEach(cb => cb.checked = checkbox.checked);
    updateBulkActions();
}

function updateBulkActions() {
    const selected = document.querySelectorAll('.row-checkbox:checked');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (selected.length > 0) {
        bulkActions.style.display = 'flex';
        bulkActions.style.alignItems = 'center';
        bulkActions.style.gap = '12px';
        selectedCount.textContent = selected.length;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Single delete modal
function confirmDelete(id, title) {
    const modal = document.getElementById('delete-modal');
    const confirmBtn = document.getElementById('delete-confirm-btn');
    const modalText = document.getElementById('delete-modal-text');
    
    modalText.innerHTML = 'Möchten Sie die Seite <strong>"' + title + '"</strong> wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.';
    confirmBtn.href = '?page=shop/cms&action=delete&id=' + id;
    modal.style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('delete-modal').style.display = 'none';
}

// Bulk delete modal
function confirmBulkDelete() {
    const selected = document.querySelectorAll('.row-checkbox:checked');
    const modal = document.getElementById('bulk-delete-modal');
    const modalText = document.getElementById('bulk-delete-modal-text');
    
    modalText.innerHTML = 'Möchten Sie <strong>' + selected.length + ' Seite(n)</strong> wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.';
    modal.style.display = 'flex';
}

function closeBulkDeleteModal() {
    document.getElementById('bulk-delete-modal').style.display = 'none';
}

function executeBulkDelete() {
    document.getElementById('bulk-action-input').value = 'delete';
    document.getElementById('cms-list-form').submit();
}

// Close modals on outside click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.style.display = 'none';
        });
    }
});

// Info box dismiss persistence
function dismissInfoBox() {
    const infoBox = document.getElementById('cms-info-box');
    if (infoBox) {
        infoBox.style.display = 'none';
        localStorage.setItem('cms_info_dismissed', 'true');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show info box only if not dismissed
    const infoBox = document.getElementById('cms-info-box');
    if (infoBox && localStorage.getItem('cms_info_dismissed') !== 'true') {
        infoBox.style.display = 'flex';
    }
    
    // Restore search focus after filter
    const searchInput = document.getElementById('search-input');
    if (searchInput && sessionStorage.getItem('cms_search_focus') === 'true') {
        searchInput.focus();
        const cursorPos = parseInt(sessionStorage.getItem('cms_search_cursor') || '0');
        searchInput.setSelectionRange(cursorPos, cursorPos);
        sessionStorage.removeItem('cms_search_focus');
        sessionStorage.removeItem('cms_search_cursor');
    }
});
</script>
