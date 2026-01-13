<?php 
/** 
 * Shop - CMS Edit Page
 * Edit existing CMS page (legal pages like Impressum, AGB, etc.)
 */

// Check permission
Auth::requirePermission('cms.manage');

// Get shop ID
$shop = Shop::getDefault();
$shopId = $shop['id'] ?? 1;

// Get page ID from URL
$pageId = intval($_GET['id'] ?? 0);

if ($pageId <= 0) {
    echo '<script>window.location.href = "?page=shop/cms";</script>';
    return;
}

// Load page (shop-scoped)
$cmsPage = CmsPage::findForShop($pageId, $shopId);

if (!$cmsPage) {
    echo '<script>window.location.href = "?page=shop/cms";</script>';
    return;
}

// Handle form submission
$result = null;

// Check for created success message
if (isset($_GET['created'])) {
    $result = ['success' => true, 'message' => 'Seite wurde erfolgreich erstellt.', 'errors' => []];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = CmsController::handleUpdate($pageId);
    
    if ($result['success']) {
        // Reload page data after successful update
        $cmsPage = CmsPage::findForShop($pageId, $shopId);
    }
}

// Build form data
// After successful save: use fresh database data
// After failed save: use POST data to preserve user input
// Initial load (not POST): use database data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $result && !$result['success']) {
    // Failed save - preserve user input including unchecked checkbox (which means is_active=0)
    $formData = [
        'title' => $_POST['title'] ?? '',
        'slug' => $_POST['slug'] ?? '',
        'content' => $_POST['content'] ?? '',
        'meta_title' => $_POST['meta_title'] ?? '',
        'meta_description' => $_POST['meta_description'] ?? '',
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'sort_order' => intval($_POST['sort_order'] ?? 0),
    ];
} else {
    // Initial load or successful save - use database data
    $formData = [
        'title' => $cmsPage['title'],
        'slug' => $cmsPage['slug'],
        'content' => $cmsPage['content'],
        'meta_title' => $cmsPage['meta_title'],
        'meta_description' => $cmsPage['meta_description'],
        'is_active' => $cmsPage['is_active'],
        'sort_order' => $cmsPage['sort_order'],
    ];
}
?>

<form method="POST" action="">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Seite bearbeiten</h1>
            <p class="page-subtitle"><?= htmlspecialchars($cmsPage['title']) ?></p>
        </div>
        <div class="page-header-actions">
            <a href="?page=shop/cms" class="btn">
                <span class="material-symbols-rounded">arrow_back</span> Zurück
            </a>
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

    <!-- Main Content -->
    <div style="display:grid;grid-template-columns:1fr 350px;gap:20px;">
        <div>
            <!-- Title & Content -->
            <div class="card">
                <div class="card-header"><h3>Inhalt</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="title">Titel <span style="color:var(--error)">*</span></label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-input <?= isset($result['errors']['title']) ? 'is-invalid' : '' ?>" 
                               value="<?= htmlspecialchars($formData['title']) ?>"
                               placeholder="z.B. Impressum, AGB, Datenschutz"
                               required>
                        <?php if (isset($result['errors']['title'])): ?>
                            <small class="form-error"><?= htmlspecialchars($result['errors']['title']) ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="slug">URL-Slug</label>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="color:var(--text-muted);">/</span>
                            <input type="text" 
                                   id="slug" 
                                   name="slug" 
                                   class="form-input <?= isset($result['errors']['slug']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($formData['slug']) ?>"
                                   placeholder="wird-automatisch-generiert"
                                   style="font-family:monospace;">
                        </div>
                        <small style="color:var(--text-muted);font-size:11px;display:block;margin-top:4px;">
                            Leer lassen für automatische Generierung aus dem Titel
                        </small>
                        <?php if (isset($result['errors']['slug'])): ?>
                            <small class="form-error"><?= htmlspecialchars($result['errors']['slug']) ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="content">Seiteninhalt</label>
                        <textarea id="content" 
                                  name="content" 
                                  class="form-textarea" 
                                  rows="18"
                                  style="line-height:1.6;"
                                  placeholder="Geben Sie hier den Inhalt der Seite ein."><?= htmlspecialchars($formData['content']) ?></textarea>
                        <small style="color:var(--text-muted);font-size:11px;display:block;margin-top:4px;">
                            Text wird automatisch formatiert. Leerzeilen erzeugen Absätze.
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- SEO -->
            <div class="card">
                <div class="card-header"><h3>SEO-Einstellungen</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="meta_title">Meta-Titel</label>
                        <input type="text" 
                               id="meta_title" 
                               name="meta_title" 
                               class="form-input" 
                               value="<?= htmlspecialchars($formData['meta_title']) ?>"
                               placeholder="Titel für Suchmaschinen (max. 60 Zeichen)"
                               maxlength="60">
                        <small style="color:var(--text-muted);font-size:11px;display:block;margin-top:4px;">
                            Leer lassen, um den Seitentitel zu verwenden
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="meta_description">Meta-Beschreibung</label>
                        <textarea id="meta_description" 
                                  name="meta_description" 
                                  class="form-textarea" 
                                  rows="3"
                                  placeholder="Beschreibung für Suchmaschinen (max. 160 Zeichen)"
                                  maxlength="160"><?= htmlspecialchars($formData['meta_description']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div>
            <!-- Publish Settings -->
            <div class="card">
                <div class="card-header"><h3>Veröffentlichung</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" name="is_active" value="1" <?= $formData['is_active'] ? 'checked' : '' ?>>
                            <span>Seite veröffentlicht</span>
                        </label>
                        <p class="form-hint">Wenn deaktiviert, ist die Seite als Entwurf gespeichert</p>
                    </div>
                    
                    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border-color);font-size:12px;color:var(--text-muted);">
                        <p><strong>Erstellt:</strong> <?= date('d.m.Y H:i', strtotime($cmsPage['created_at'])) ?></p>
                        <p style="margin-top:4px;"><strong>Aktualisiert:</strong> <?= date('d.m.Y H:i', strtotime($cmsPage['updated_at'])) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Danger Zone -->
            <div class="card" style="border-color:var(--error);">
                <div class="card-header" style="background:rgba(239,68,68,0.1);"><h3 style="color:var(--error);">Gefahrenzone</h3></div>
                <div class="card-body">
                    <p style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">Diese Aktion kann nicht rückgängig gemacht werden.</p>
                    <button type="button" 
                            class="btn" 
                            style="width:100%;justify-content:center;color:var(--error);border-color:var(--error);"
                            onclick="confirmDelete()">
                        <span class="material-symbols-rounded">delete</span> Seite löschen
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#1a1a2e;border-radius:12px;padding:24px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.5);border:1px solid var(--border-color);">
        <h3 style="margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <span class="material-symbols-rounded" style="color:var(--error);">warning</span>
            Seite löschen?
        </h3>
        <p style="color:var(--text-muted);margin-bottom:24px;">
            Möchten Sie die Seite <strong>"<?= htmlspecialchars($cmsPage['title']) ?>"</strong> wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.
        </p>
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <button type="button" class="btn" onclick="closeDeleteModal()">Abbrechen</button>
            <a href="?page=shop/cms&action=delete&id=<?= $pageId ?>" class="btn" style="background:var(--error);color:white;border-color:var(--error);">
                <span class="material-symbols-rounded">delete</span> Löschen
            </a>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    document.getElementById('delete-modal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('delete-modal').style.display = 'none';
}

// Close modal on outside click
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
