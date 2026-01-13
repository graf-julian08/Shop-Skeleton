<?php 
/** 
 * Shop - CMS Einstellungen
 * Drag-and-drop sorting of CMS pages and global CMS settings
 */

// Check permission
Auth::requirePermission('cms.manage');

// Get shop ID
$shop = Shop::getDefault();
$shopId = $shop['id'] ?? 1;

// Handle AJAX sort order update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'update_order') {
        $order = json_decode($_POST['order'] ?? '[]', true);
        if (is_array($order)) {
            $position = 0;
            foreach ($order as $pageId) {
                CmsPage::update(intval($pageId), ['sort_order' => $position]);
                $position++;
            }
            echo json_encode(['success' => true, 'message' => 'Reihenfolge gespeichert']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ungültige Daten']);
        }
        exit;
    }
}

// Get all pages sorted by sort_order
$pages = CmsPage::allForShop($shopId, [], 'sort_order ASC');
?>

<div class="page-header">
    <div class="page-header-content">
        <h1>CMS Einstellungen</h1>
        <p class="page-subtitle">Reihenfolge und allgemeine Einstellungen für CMS-Seiten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=shop/cms" class="btn">
            <span class="material-symbols-rounded">arrow_back</span> Zurück zu CMS
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 350px;gap:20px;">
    <div>
        <!-- Sorting -->
        <div class="card">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <h3>Reihenfolge der Seiten</h3>
                <span class="badge badge-info" id="save-status" style="display:none;">Gespeichert!</span>
            </div>
            <div class="card-body">
                <p style="color:var(--text-muted);font-size:13px;margin-bottom:16px;">
                    Ziehen Sie die Seiten per Drag & Drop in die gewünschte Reihenfolge. 
                    Die Reihenfolge bestimmt, wie die Seiten z.B. im Footer-Menü angezeigt werden.
                </p>
                
                <?php if (empty($pages)): ?>
                    <div class="empty-state" style="text-align:center;padding:40px 20px;">
                        <span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);margin-bottom:12px;display:block;">article</span>
                        <p style="color:var(--text-muted);">Keine CMS-Seiten vorhanden.</p>
                        <a href="?page=shop/cms_create" class="btn btn-primary" style="margin-top:12px;">
                            <span class="material-symbols-rounded">add</span> Erste Seite erstellen
                        </a>
                    </div>
                <?php else: ?>
                    <ul id="sortable-pages" class="sortable-list">
                        <?php foreach ($pages as $index => $cmsPage): ?>
                        <li class="sortable-item" data-id="<?= $cmsPage['id'] ?>">
                            <div class="sortable-handle">
                                <span class="material-symbols-rounded">drag_indicator</span>
                            </div>
                            <div class="sortable-content">
                                <span class="sortable-position"><?= $index + 1 ?></span>
                                <strong><?= htmlspecialchars($cmsPage['title']) ?></strong>
                                <span class="sortable-slug">/<?= htmlspecialchars($cmsPage['slug']) ?></span>
                            </div>
                            <div class="sortable-status">
                                <?php if ($cmsPage['is_active']): ?>
                                    <span class="badge badge-success">Aktiv</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Entwurf</span>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar with info -->
    <div>
        <div class="card" style="background:var(--bg-lighter);">
            <div class="card-header"><h3>Info zur Reihenfolge</h3></div>
            <div class="card-body">
                <div style="font-size:13px;color:var(--text-muted);line-height:1.7;">
                    <p style="margin-bottom:12px;">
                        <strong>Was bewirkt die Reihenfolge?</strong><br>
                        Die Position bestimmt, in welcher Reihenfolge die CMS-Seiten im Shop angezeigt werden, z.B.:
                    </p>
                    <ul style="padding-left:20px;margin-bottom:12px;">
                        <li>Im Footer-Menü</li>
                        <li>In der Sitemap</li>
                        <li>In Navigation-Listen</li>
                    </ul>
                    <p>
                        <strong>Tipp:</strong> Wichtige Seiten wie Impressum und AGB sollten weiter oben stehen.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header"><h3>Schnellaktionen</h3></div>
            <div class="card-body">
                <a href="?page=shop/cms_create" class="btn" style="width:100%;justify-content:center;margin-bottom:8px;">
                    <span class="material-symbols-rounded">add</span> Neue Seite erstellen
                </a>
                <a href="?page=shop/cms" class="btn" style="width:100%;justify-content:center;">
                    <span class="material-symbols-rounded">list</span> Alle Seiten anzeigen
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.sortable-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sortable-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: var(--bg-lighter);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: grab;
    transition: all 0.15s ease;
}

.sortable-item:hover {
    background: var(--bg-card);
    border-color: var(--primary);
}

.sortable-item.dragging {
    opacity: 0.5;
    transform: scale(1.02);
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}

.sortable-item.drag-over {
    border-color: var(--primary);
    border-style: dashed;
}

.sortable-handle {
    color: var(--text-muted);
    cursor: grab;
}

.sortable-handle:active {
    cursor: grabbing;
}

.sortable-content {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.sortable-position {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    font-size: 12px;
    font-weight: 600;
}

.sortable-slug {
    color: var(--text-muted);
    font-size: 12px;
    font-family: monospace;
}

.sortable-status {
    margin-left: auto;
}
</style>

<script>
// Simple drag and drop
const sortableList = document.getElementById('sortable-pages');
if (sortableList) {
    let draggedItem = null;
    
    sortableList.querySelectorAll('.sortable-item').forEach(item => {
        item.draggable = true;
        
        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        
        item.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            sortableList.querySelectorAll('.sortable-item').forEach(i => i.classList.remove('drag-over'));
            updatePositions();
            saveOrder();
        });
        
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            if (draggedItem !== this) {
                this.classList.add('drag-over');
            }
        });
        
        item.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        
        item.addEventListener('drop', function(e) {
            e.preventDefault();
            if (draggedItem !== this) {
                const allItems = [...sortableList.querySelectorAll('.sortable-item')];
                const draggedIndex = allItems.indexOf(draggedItem);
                const droppedIndex = allItems.indexOf(this);
                
                if (draggedIndex < droppedIndex) {
                    this.after(draggedItem);
                } else {
                    this.before(draggedItem);
                }
            }
            this.classList.remove('drag-over');
        });
    });
}

function updatePositions() {
    const items = document.querySelectorAll('.sortable-item');
    items.forEach((item, index) => {
        item.querySelector('.sortable-position').textContent = index + 1;
    });
}

function saveOrder() {
    const items = document.querySelectorAll('.sortable-item');
    const order = [...items].map(item => item.dataset.id);
    
    const formData = new FormData();
    formData.append('action', 'update_order');
    formData.append('order', JSON.stringify(order));
    
    fetch('?page=shop/cms_settings', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        // Try to extract JSON even if there's a PHP warning in the response
        const jsonMatch = text.match(/\{"success":(true|false).*\}/);
        if (jsonMatch) {
            const data = JSON.parse(jsonMatch[0]);
            if (data.success) {
                const status = document.getElementById('save-status');
                status.style.display = 'inline-flex';
                setTimeout(() => {
                    status.style.display = 'none';
                }, 2000);
            }
        } else {
            // Fallback: if text contains 'success' assume it worked
            if (text.includes('"success":true')) {
                const status = document.getElementById('save-status');
                status.style.display = 'inline-flex';
                setTimeout(() => {
                    status.style.display = 'none';
                }, 2000);
            }
        }
    })
    .catch(error => {
        console.error('Error saving order:', error);
    });
}
</script>
