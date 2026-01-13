<?php /** Katalog - Produkt bearbeiten */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/products">Produkte</a> <span>›</span> <a href="?page=catalog/product_detail&id=1">Premium Lederjacke</a> <span>›</span> <span>Bearbeiten</span></nav>
        <h1>Produkt bearbeiten</h1>
        <p class="page-subtitle">Premium Lederjacke · SKU: LJ-001</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/product_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active">Allgemein</button>
    <button class="tab">Preise</button>
    <button class="tab">Inventar</button>
    <button class="tab">Bilder</button>
    <button class="tab">SEO</button>
    <button class="tab">Erweitert</button>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grunddaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Produktname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" value="Premium Lederjacke">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">SKU <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" value="LJ-001">
                </div>
                <div class="form-group">
                    <label class="form-label">Produkttyp</label>
                    <select class="form-select"><option selected>Physisch</option><option>Digital</option><option>Abonnement</option><option>Bundle</option></select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select"><option selected>Aktiv</option><option>Entwurf</option><option>Archiviert</option></select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Kategorisierung</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Kategorie</label>
                <select class="form-select"><option>Kleidung</option><option selected>Kleidung › Jacken</option><option>Kleidung › Mäntel</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Hersteller</label>
                <input type="text" class="form-input" value="Leather Works GmbH">
            </div>
            <div class="form-group">
                <label class="form-label">Tags</label>
                <input type="text" class="form-input" value="leder, jacke, premium, herren" placeholder="Kommagetrennt">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Beschreibung</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Kurzbeschreibung</label>
            <textarea class="form-textarea" rows="2">Hochwertige Lederjacke aus 100% echtem Rindsleder.</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Ausführliche Beschreibung</label>
            <textarea class="form-textarea" rows="5">Hochwertige Lederjacke aus 100% echtem Rindsleder. Handgefertigt in Italien mit besonderer Sorgfalt für Details. Die Jacke verfügt über einen klassischen Biker-Schnitt mit asymmetrischem Reißverschluss.</textarea>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Preise</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Verkaufspreis <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" value="299,00" style="font-size:18px;font-weight:600;">
            </div>
            <div class="form-group">
                <label class="form-label">Streichpreis</label>
                <input type="text" class="form-input" value="349,00">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Einkaufspreis</label>
                <input type="text" class="form-input" value="145,00">
            </div>
            <div class="form-group">
                <label class="form-label">Steuerklasse</label>
                <select class="form-select"><option selected>Standard (19%)</option><option>Ermäßigt (7%)</option><option>Steuerfrei</option></select>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=catalog/product_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Änderungen speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
