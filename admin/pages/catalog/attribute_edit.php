<?php /** Katalog - Attribut bearbeiten */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/attributes">Attribute</a> <span>›</span> <a href="?page=catalog/attribute_detail&id=1">Farbe</a> <span>›</span> <span>Bearbeiten</span></nav>
        <h1>Attribut bearbeiten</h1>
        <p class="page-subtitle">Farbe</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/attribute_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grunddaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Attributname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" value="Farbe">
            </div>
            <div class="form-group">
                <label class="form-label">Attributcode <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" value="color" disabled style="background:var(--bg-tertiary);">
                <small style="color:var(--text-muted);">Der Code kann nach Erstellung nicht geändert werden</small>
            </div>
            <div class="form-group">
                <label class="form-label">Eingabetyp</label>
                <select class="form-select"><option>Text</option><option>Textarea</option><option selected>Auswahl (Dropdown)</option><option>Mehrfachauswahl</option><option>Ja/Nein</option><option>Datum</option><option>Preis</option></select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Konfiguration</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Pflichtfeld</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">In Filter anzeigen</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">Durchsuchbar</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">In Vergleich anzeigen</label>
                <div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Attributoptionen</h3><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">add</span> Option hinzufügen</button></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th style="width:40px;"></th><th>Wert</th><th>Label (DE)</th><th>Position</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr><td><span class="material-symbols-rounded" style="cursor:grab;color:var(--text-muted);">drag_indicator</span></td><td><input type="text" class="form-input" value="black" style="width:100px;"></td><td><input type="text" class="form-input" value="Schwarz"></td><td><input type="number" class="form-input" value="1" style="width:60px;"></td><td class="table-actions"><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">delete</span></button></td></tr>
                <tr><td><span class="material-symbols-rounded" style="cursor:grab;color:var(--text-muted);">drag_indicator</span></td><td><input type="text" class="form-input" value="white" style="width:100px;"></td><td><input type="text" class="form-input" value="Weiß"></td><td><input type="number" class="form-input" value="2" style="width:60px;"></td><td class="table-actions"><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">delete</span></button></td></tr>
                <tr><td><span class="material-symbols-rounded" style="cursor:grab;color:var(--text-muted);">drag_indicator</span></td><td><input type="text" class="form-input" value="blue" style="width:100px;"></td><td><input type="text" class="form-input" value="Blau"></td><td><input type="number" class="form-input" value="3" style="width:60px;"></td><td class="table-actions"><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">delete</span></button></td></tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <a href="?page=catalog/attribute_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Änderungen speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
