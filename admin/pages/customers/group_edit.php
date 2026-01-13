<?php /** Kunden - Gruppe bearbeiten */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=customers/groups">Kundengruppen</a> <span>›</span> <a href="?page=customers/group_detail&id=3">VIP</a> <span>›</span> <span>Bearbeiten</span></nav>
        <h1>Kundengruppe bearbeiten</h1>
        <p class="page-subtitle">VIP</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=customers/group_detail&id=3" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grunddaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Gruppenname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" value="VIP">
            </div>
            <div class="form-group">
                <label class="form-label">Code</label>
                <input type="text" class="form-input" value="vip">
            </div>
            <div class="form-group">
                <label class="form-label">Beschreibung</label>
                <textarea class="form-textarea" rows="2">Treue Kunden mit hohem Bestellwert</textarea>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Vorteile</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Rabatt (%)</label>
                <input type="number" class="form-input" value="10" min="0" max="100">
            </div>
            <div class="form-group">
                <label class="form-label">Kostenloser Versand</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">Prioritäts-Support</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">Frühzeitiger Zugang zu Sales</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Automatische Zuordnung</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Automatisch zuweisen wenn</label>
            <select class="form-select"><option>Deaktiviert</option><option selected>Umsatz erreicht</option><option>Bestellanzahl erreicht</option></select>
        </div>
        <div class="form-group">
            <label class="form-label">Mindestumsatz (€)</label>
            <input type="number" class="form-input" value="2000">
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=customers/group_detail&id=3" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Änderungen speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
