<?php /** Commerce - Neuen Rabatt erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/discounts">Rabatte</a> <span>›</span> <span>Neuer Rabatt</span></nav>
        <h1>Neuen Rabatt erstellen</h1>
        <p class="page-subtitle">Erstellen Sie eine neue Rabattaktion</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/discounts" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Rabatt erstellen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grundeinstellungen</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Rabattname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. Sommerschlussverkauf">
            </div>
            <div class="form-group">
                <label class="form-label">Rabatt-Code</label>
                <div class="input-with-button">
                    <input type="text" class="form-input" placeholder="z.B. SOMMER20">
                    <button class="btn btn-sm">Generieren</button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Rabatttyp</label>
                <select class="form-select">
                    <option selected>Prozentrabatt</option>
                    <option>Fester Betrag</option>
                    <option>Kostenloser Versand</option>
                    <option>Kaufe X bekomme Y</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Rabattwert <span style="color:var(--error)">*</span></label>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="number" class="form-input" placeholder="20" style="flex:1;">
                    <span>%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Gültigkeit</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option>Entwurf</option>
                    <option selected>Aktiv</option>
                    <option>Geplant</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Gültig von</label>
                <input type="datetime-local" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Gültig bis</label>
                <input type="datetime-local" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Max. Nutzungen gesamt</label>
                <input type="number" class="form-input" placeholder="Unbegrenzt = leer lassen">
            </div>
            <div class="form-group">
                <label class="form-label">Max. Nutzungen pro Kunde</label>
                <input type="number" class="form-input" placeholder="1">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bedingungen</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Mindestbestellwert (€)</label>
                <input type="number" class="form-input" placeholder="0.00" step="0.01">
            </div>
            <div class="form-group">
                <label class="form-label">Mindestmenge</label>
                <input type="number" class="form-input" placeholder="1">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Gilt für Kategorien</label>
            <select class="form-select" multiple style="height:100px;">
                <option>Alle Kategorien</option>
                <option>Kleidung</option>
                <option>Accessoires</option>
                <option>Schuhe</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Gilt für Kundengruppen</label>
            <select class="form-select" multiple style="height:80px;">
                <option selected>Alle Kunden</option>
                <option>Standard</option>
                <option>VIP</option>
                <option>Großhandel</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Mit anderen Rabatten kombinierbar</label>
            <div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=commerce/discounts" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Rabatt erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.input-with-button { display:flex; gap:8px; }
.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; }
</style>
