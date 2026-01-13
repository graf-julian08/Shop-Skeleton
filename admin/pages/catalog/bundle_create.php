<?php /** Katalog - Neues Bundle erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/bundles">Bundles</a> <span>›</span> <span>Neues Bundle</span></nav>
        <h1>Neues Bundle erstellen</h1>
        <p class="page-subtitle">Erstellen Sie ein Produktbundle</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/bundles" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Bundle erstellen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Bundle-Informationen</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Bundle-Name <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. Starter-Set">
            </div>
            <div class="form-group">
                <label class="form-label">URL-Slug</label>
                <input type="text" class="form-input" placeholder="z.B. starter-set">
            </div>
            <div class="form-group">
                <label class="form-label">Beschreibung</label>
                <textarea class="form-textarea" rows="3" placeholder="Bundle-Beschreibung..."></textarea>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Preis & Status</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Bundle-Typ</label>
                <select class="form-select">
                    <option selected>Fixes Bundle</option>
                    <option>Konfigurierbares Bundle</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Preisberechnung</label>
                <select class="form-select">
                    <option>Fester Preis</option>
                    <option selected>Dynamisch (Summe der Produkte)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Bundle-Rabatt (%)</label>
                <input type="number" class="form-input" placeholder="z.B. 15">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option>Entwurf</option>
                    <option selected>Aktiv</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bundle-Produkte</h3><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">add</span> Produkt hinzufügen</button></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Produkt</th><th>SKU</th><th>Menge</th><th>Optional</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;">
                        <span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);">inventory_2</span>
                        <p style="color:var(--text-muted);margin-top:8px;">Noch keine Produkte hinzugefügt</p>
                        <button class="btn btn-primary" style="margin-top:12px;"><span class="material-symbols-rounded">add</span> Erstes Produkt hinzufügen</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <a href="?page=catalog/bundles" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Bundle erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
