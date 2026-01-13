<?php /** Katalog - Kategorie bearbeiten */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/categories">Kategorien</a> <span>›</span> <a href="?page=catalog/category_detail&id=1">Kleidung</a> <span>›</span> <span>Bearbeiten</span></nav>
        <h1>Kategorie bearbeiten</h1>
        <p class="page-subtitle">Kleidung</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/category_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grunddaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Kategoriename <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" value="Kleidung">
            </div>
            <div class="form-group">
                <label class="form-label">URL-Slug</label>
                <input type="text" class="form-input" value="kleidung">
            </div>
            <div class="form-group">
                <label class="form-label">Übergeordnete Kategorie</label>
                <select class="form-select"><option selected>— Hauptkategorie —</option><option>Kleidung</option><option>Accessoires</option><option>Schuhe</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select"><option selected>Aktiv</option><option>Entwurf</option><option>Archiviert</option></select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Darstellung</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Position</label>
                <input type="number" class="form-input" value="1" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">In Navigation anzeigen</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">In Suche einschließen</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Beschreibung</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Kategoriebeschreibung</label>
            <textarea class="form-textarea" rows="4">Entdecken Sie unsere Kollektion hochwertiger Kleidung für jeden Anlass.</textarea>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>SEO</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Meta-Titel</label>
            <input type="text" class="form-input" value="Kleidung kaufen | Mein Online Shop">
        </div>
        <div class="form-group">
            <label class="form-label">Meta-Beschreibung</label>
            <textarea class="form-textarea" rows="2">Hochwertige Kleidung online kaufen. Große Auswahl an Jacken, Hosen und mehr.</textarea>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=catalog/category_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Änderungen speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
