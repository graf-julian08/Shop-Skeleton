<?php /** Katalog - Attribut erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/attributes">Attribute</a> <span>›</span> <span>Neues Attribut</span></nav>
        <h1>Neues Attribut erstellen</h1>
        <p class="page-subtitle">Definieren Sie ein neues Produktattribut</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/attributes" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Attribut erstellen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grunddaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Attributname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. Material">
            </div>
            <div class="form-group">
                <label class="form-label">Attributcode <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. material">
                <small style="color:var(--text-muted);">Nur Kleinbuchstaben und Unterstriche</small>
            </div>
            <div class="form-group">
                <label class="form-label">Eingabetyp <span style="color:var(--error)">*</span></label>
                <select class="form-select"><option>Text</option><option>Textarea</option><option selected>Auswahl (Dropdown)</option><option>Mehrfachauswahl</option><option>Ja/Nein</option><option>Datum</option><option>Preis</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Attributgruppe</label>
                <select class="form-select"><option selected>Produkteigenschaften</option><option>Technische Daten</option><option>Versandinformationen</option></select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Konfiguration</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Pflichtfeld</label>
                <div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div>
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
                <label class="form-label">Position</label>
                <input type="number" class="form-input" value="0" min="0">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Attributoptionen</h3><small style="color:var(--text-muted);">Nur für Auswahl-Typen</small></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th style="width:40px;"></th><th>Wert</th><th>Label (DE)</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr><td><span class="material-symbols-rounded" style="cursor:grab;color:var(--text-muted);">drag_indicator</span></td><td><input type="text" class="form-input" placeholder="option_1" style="width:120px;"></td><td><input type="text" class="form-input" placeholder="Option 1"></td><td class="table-actions"><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">delete</span></button></td></tr>
            </tbody>
        </table>
        <button class="btn btn-sm" style="margin-top:12px;"><span class="material-symbols-rounded">add</span> Option hinzufügen</button>
    </div>
    <div class="card-footer">
        <a href="?page=catalog/attributes" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Attribut erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
