<?php /** Katalog - Kategorien */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kategorien</h1>
        <p class="page-subtitle">Produktkategorien verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/category_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Kategorie erstellen</a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Kategoriestruktur</h3></div>
        <div class="card-body">
            <a href="?page=catalog/category_detail&id=1" class="category-row" style="background:rgba(124,58,237,0.1);">
                <span class="material-symbols-rounded">folder</span>
                <span style="flex:1;"><strong>Kleidung</strong></span>
                <span class="badge badge-default">45 Produkte</span>
                <span class="material-symbols-rounded">chevron_right</span>
            </a>
            <a href="?page=catalog/category_detail&id=2" class="category-row" style="margin-left:24px;">
                <span class="material-symbols-rounded">folder_open</span>
                <span style="flex:1;">Jacken</span>
                <span class="badge badge-default">12 Produkte</span>
            </a>
            <a href="?page=catalog/category_detail&id=3" class="category-row" style="margin-left:24px;">
                <span class="material-symbols-rounded">folder_open</span>
                <span style="flex:1;">Pullover</span>
                <span class="badge badge-default">18 Produkte</span>
            </a>
            <a href="?page=catalog/category_detail&id=4" class="category-row" style="margin-left:24px;">
                <span class="material-symbols-rounded">folder_open</span>
                <span style="flex:1;">T-Shirts</span>
                <span class="badge badge-default">15 Produkte</span>
            </a>
            <a href="?page=catalog/category_detail&id=5" class="category-row">
                <span class="material-symbols-rounded">folder</span>
                <span style="flex:1;"><strong>Schuhe</strong></span>
                <span class="badge badge-default">32 Produkte</span>
                <span class="material-symbols-rounded">chevron_right</span>
            </a>
            <a href="?page=catalog/category_detail&id=6" class="category-row">
                <span class="material-symbols-rounded">folder</span>
                <span style="flex:1;"><strong>Accessoires</strong></span>
                <span class="badge badge-default">28 Produkte</span>
                <span class="material-symbols-rounded">chevron_right</span>
            </a>
            <a href="?page=catalog/category_detail&id=7" class="category-row">
                <span class="material-symbols-rounded">folder</span>
                <span style="flex:1;"><strong>Digital</strong></span>
                <span class="badge badge-default">8 Produkte</span>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Schnellbearbeitung</h3></div>
        <div class="card-body">
            <p style="color:var(--text-muted);margin-bottom:20px;">Wählen Sie eine Kategorie links aus, um sie hier zu bearbeiten, oder nutzen Sie die <a href="?page=catalog/category_create">vollständige Kategorie-Erstellung</a>.</p>
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" class="form-input" value="Kleidung">
            </div>
            <div class="form-group">
                <label class="form-label">URL-Slug</label>
                <input type="text" class="form-input" value="kleidung">
            </div>
            <div class="form-group">
                <label class="form-label">Übergeordnete Kategorie</label>
                <select class="form-select">
                    <option selected>Keine (Hauptkategorie)</option>
                    <option>Kleidung</option>
                    <option>Schuhe</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-checkbox"><input type="checkbox" checked><span>Sichtbar im Shop</span></label>
            </div>
            <div class="form-group">
                <label class="form-checkbox"><input type="checkbox" checked><span>In Navigation anzeigen</span></label>
            </div>
        </div>
        <div class="card-footer">
            <a href="?page=catalog/category_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Vollständig bearbeiten</a>
            <button class="btn btn-primary">Schnell speichern</button>
        </div>
    </div>
</div>

<style>
.category-row {
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px;
    border:1px solid var(--border-color);
    border-radius:var(--radius-md);
    margin-bottom:8px;
    text-decoration:none;
    color:inherit;
    cursor:pointer;
    transition:border-color 0.2s;
}
.category-row:hover {
    border-color:var(--accent);
}
</style>
