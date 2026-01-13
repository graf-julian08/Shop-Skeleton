<?php /** Commerce - Rabatt bearbeiten */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/discounts">Rabatte</a> <span>›</span> <a href="?page=commerce/discount_detail&id=1">Winterschlussverkauf</a> <span>›</span> <span>Bearbeiten</span></nav>
        <h1>Rabatt bearbeiten</h1>
        <p class="page-subtitle">Winterschlussverkauf</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/discount_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grunddaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Rabattname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" value="Winterschlussverkauf">
            </div>
            <div class="form-group">
                <label class="form-label">Rabatttyp</label>
                <select class="form-select"><option selected>Prozentrabatt</option><option>Festbetrag</option><option>Kostenloser Versand</option><option>Kaufe X bekomme Y</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Rabattwert (%)</label>
                <input type="number" class="form-input" value="20">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select"><option selected>Aktiv</option><option>Inaktiv</option><option>Geplant</option></select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Gültigkeit</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Gültig von</label>
                <input type="date" class="form-input" value="2026-01-01">
            </div>
            <div class="form-group">
                <label class="form-label">Gültig bis</label>
                <input type="date" class="form-input" value="2026-01-31">
            </div>
            <div class="form-group">
                <label class="form-label">Max. Nutzungen gesamt</label>
                <input type="number" class="form-input" value="0" placeholder="0 = unbegrenzt">
            </div>
            <div class="form-group">
                <label class="form-label">Max. Nutzungen pro Kunde</label>
                <input type="number" class="form-input" value="1">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bedingungen</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Mindestbestellwert (€)</label>
            <input type="number" class="form-input" value="50">
        </div>
        <div class="form-group">
            <label class="form-label">Gilt für Kategorien</label>
            <select class="form-select" multiple style="height:100px;"><option selected>Kleidung</option><option selected>Jacken</option><option>Accessoires</option><option>Schuhe</option></select>
        </div>
        <div class="form-group">
            <label class="form-label">Mit anderen Rabatten kombinierbar</label>
            <div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=commerce/discount_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Änderungen speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
