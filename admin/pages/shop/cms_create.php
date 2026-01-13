<?php 
/** 
 * Shop - CMS Create Page
 * Create new CMS page (legal pages like Impressum, AGB, etc.)
 */

// Check permission
Auth::requirePermission('cms.manage');

// Get shop ID
$shop = Shop::getDefault();
$shopId = $shop['id'] ?? 1;

// Handle form submission
$result = null;
$formData = [
    'title' => '',
    'slug' => '',
    'content' => '',
    'meta_title' => '',
    'meta_description' => '',
    'is_active' => 0,
    'sort_order' => 0,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = CmsController::handleCreate();
    
    if ($result['success']) {
        // Use JavaScript redirect since HTML is already output by index.php
        $redirectUrl = '?page=shop/cms_edit&id=' . $result['page_id'] . '&created=1';
        echo '<script>window.location.href = "' . $redirectUrl . '";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . $redirectUrl . '"></noscript>';
        return;
    }
    
    // Keep form data on error
    $formData = [
        'title' => $_POST['title'] ?? '',
        'slug' => $_POST['slug'] ?? '',
        'content' => $_POST['content'] ?? '',
        'meta_title' => $_POST['meta_title'] ?? '',
        'meta_description' => $_POST['meta_description'] ?? '',
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'sort_order' => intval($_POST['sort_order'] ?? 0),
    ];
}
?>

<form method="POST" action="">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Neue Seite erstellen</h1>
            <p class="page-subtitle">Rechtliche oder informative Seite (z.B. Impressum, AGB, Datenschutz)</p>
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

    <?php if ($result && !$result['success']): ?>
        <div class="alert alert-error">
            <span class="material-symbols-rounded">error</span>
            <?= htmlspecialchars($result['message']) ?>
            <button type="button" class="alert-close"><span class="material-symbols-rounded">close</span></button>
        </div>
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
                                  placeholder="Geben Sie hier den Inhalt der Seite ein.

Jeder Absatz wird automatisch formatiert. Leerzeilen erzeugen neue Absätze.

Für rechtliche Seiten wie Impressum oder AGB können Sie den Text einfach hier einfügen."><?= htmlspecialchars($formData['content']) ?></textarea>
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
                            <span>Sofort veröffentlichen</span>
                        </label>
                        <p class="form-hint">Wenn deaktiviert, wird die Seite als Entwurf gespeichert</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="confirmed_unusual" id="confirmed_unusual" value="0">
</form>

<!-- Unusual Title Warning Modal -->
<div id="unusual-title-modal" class="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#1a1a2e;border-radius:12px;padding:24px;max-width:450px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.5);border:1px solid var(--border-color);">
        <h3 style="margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <span class="material-symbols-rounded" style="color:var(--warning);">info</span>
            Ungewöhnlicher Seitenname
        </h3>
        <p style="color:var(--text-muted);margin-bottom:12px;">
            Der eingegebene Titel "<strong id="unusual-title-name"></strong>" entspricht keiner typischen CMS-Seite.
        </p>
        <p style="color:var(--text-muted);margin-bottom:16px;font-size:13px;">
            <strong>Typische CMS-Seiten:</strong><br>
            Impressum, AGB, Datenschutz, Widerrufsbelehrung, Über uns, Kontakt, FAQ, Versand, Zahlung, Lieferung
        </p>
        <p style="color:var(--text-muted);margin-bottom:24px;font-size:13px;">
            Möchten Sie diese Seite trotzdem erstellen?
        </p>
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <button type="button" class="btn" onclick="closeUnusualModal()">Abbrechen</button>
            <button type="button" class="btn btn-primary" onclick="confirmUnusualTitle()">
                <span class="material-symbols-rounded">check</span> Trotzdem erstellen
            </button>
        </div>
    </div>
</div>

<script>
// Typical CMS page names (lowercase for comparison)
const typicalPageNames = [
    'impressum', 'agb', 'datenschutz', 'datenschutzerklärung', 'datenschutzerklaerung',
    'widerrufsbelehrung', 'widerruf', 'über uns', 'ueber uns', 'about', 'about us',
    'kontakt', 'contact', 'faq', 'häufige fragen', 'versand', 'versandkosten',
    'zahlung', 'zahlungsarten', 'lieferung', 'lieferbedingungen', 'agbs',
    'allgemeine geschäftsbedingungen', 'allgemeine geschaeftsbedingungen',
    'nutzungsbedingungen', 'terms', 'privacy', 'legal', 'rechtliches',
    'rückgabe', 'retour', 'garantie', 'hilfe', 'help', 'support'
];

function isTypicalPageName(title) {
    const normalizedTitle = title.toLowerCase().trim();
    return typicalPageNames.some(name => 
        normalizedTitle.includes(name) || name.includes(normalizedTitle)
    );
}

// Intercept form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    const confirmed = document.getElementById('confirmed_unusual').value === '1';
    
    if (title && !isTypicalPageName(title) && !confirmed) {
        e.preventDefault();
        document.getElementById('unusual-title-name').textContent = title;
        document.getElementById('unusual-title-modal').style.display = 'flex';
        return false;
    }
});

function closeUnusualModal() {
    document.getElementById('unusual-title-modal').style.display = 'none';
}

function confirmUnusualTitle() {
    document.getElementById('confirmed_unusual').value = '1';
    document.getElementById('unusual-title-modal').style.display = 'none';
    document.querySelector('form').submit();
}

// Close modal on outside click
document.getElementById('unusual-title-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUnusualModal();
    }
});

// Close modal on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUnusualModal();
    }
});
</script>

