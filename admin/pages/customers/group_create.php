<?php /** Kunden - Neue Kundengruppe erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=customers/groups">Kundengruppen</a> <span>›</span> <span>Neue Gruppe</span></nav>
        <h1>Neue Kundengruppe erstellen</h1>
        <p class="page-subtitle">Erstellen Sie eine neue Kundengruppe mit speziellen Vorteilen</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=customers/groups" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Gruppe erstellen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grunddaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Gruppenname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. Premium">
            </div>
            <div class="form-group">
                <label class="form-label">Gruppen-Code</label>
                <input type="text" class="form-input" placeholder="z.B. premium">
            </div>
            <div class="form-group">
                <label class="form-label">Beschreibung</label>
                <textarea class="form-textarea" rows="2" placeholder="Beschreibung der Gruppe..."></textarea>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Vorteile</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Standardrabatt (%)</label>
                <input type="number" class="form-input" placeholder="0">
            </div>
            <div class="form-group">
                <label class="form-label">Kostenloser Versand</label>
                <div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">Prioritärer Support</label>
                <div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">Frühzeitiger Sale-Zugang</label>
                <div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Automatische Zuordnung</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Automatisch zuweisen wenn</label>
            <select class="form-select">
                <option selected>Deaktiviert</option>
                <option>Mindestumsatz erreicht</option>
                <option>Mindestbestellungen erreicht</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Schwellenwert</label>
            <input type="number" class="form-input" placeholder="z.B. 500 (€ oder Bestellungen)">
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=customers/groups" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Gruppe erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
