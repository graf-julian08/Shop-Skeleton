<?php /** Katalog - Bundle bearbeiten */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/bundles">Bundles</a> <span>›</span> <a href="?page=catalog/bundle_detail&id=1">Outdoor Komplett-Set</a> <span>›</span> <span>Bearbeiten</span></nav>
        <h1>Bundle bearbeiten</h1>
        <p class="page-subtitle">Outdoor Komplett-Set</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/bundle_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grunddaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Bundle-Name <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" value="Outdoor Komplett-Set">
            </div>
            <div class="form-group">
                <label class="form-label">SKU</label>
                <input type="text" class="form-input" value="BDL-OUTDOOR-01">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select"><option selected>Aktiv</option><option>Entwurf</option><option>Archiviert</option></select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Preisgestaltung</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Preistyp</label>
                <select class="form-select"><option selected>Fester Preis</option><option>Dynamisch (Summe der Produkte)</option><option>Prozentual Rabatt</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Bundle-Preis</label>
                <input type="text" class="form-input" value="449,00" style="font-size:18px;font-weight:600;">
            </div>
            <div class="form-group">
                <label class="form-label">Ersparnis anzeigen</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bundle-Produkte</h3><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">add</span> Produkt hinzufügen</button></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th style="width:40px;"></th><th>Produkt</th><th>Menge</th><th>Optional</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr><td><span class="material-symbols-rounded" style="cursor:grab;color:var(--text-muted);">drag_indicator</span></td><td><select class="form-select"><option selected>Wanderrucksack Pro</option></select></td><td><input type="number" class="form-input" value="1" style="width:70px;"></td><td><div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div></td><td class="table-actions"><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">delete</span></button></td></tr>
                <tr><td><span class="material-symbols-rounded" style="cursor:grab;color:var(--text-muted);">drag_indicator</span></td><td><select class="form-select"><option selected>Wanderschuhe Trail</option></select></td><td><input type="number" class="form-input" value="1" style="width:70px;"></td><td><div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div></td><td class="table-actions"><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">delete</span></button></td></tr>
                <tr><td><span class="material-symbols-rounded" style="cursor:grab;color:var(--text-muted);">drag_indicator</span></td><td><select class="form-select"><option selected>Regenjacke Waterproof</option></select></td><td><input type="number" class="form-input" value="1" style="width:70px;"></td><td><div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div></td><td class="table-actions"><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">delete</span></button></td></tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <a href="?page=catalog/bundle_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Änderungen speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
