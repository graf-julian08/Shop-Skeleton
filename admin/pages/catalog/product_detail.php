<?php /** Katalog - Produktdetail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/products">Produkte</a> <span>›</span> <span>Premium Lederjacke</span></nav>
        <h1>Premium Lederjacke</h1>
        <p class="page-subtitle">SKU: LJ-001 · Erstellt: 15.03.2026</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/product_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">delete</span> Löschen</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active">Übersicht</button>
    <button class="tab">Varianten</button>
    <button class="tab">Inventar</button>
    <button class="tab">Preise</button>
    <button class="tab">SEO</button>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Produktdaten</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">Premium Lederjacke</span></div>
            <div class="detail-row"><span class="detail-label">SKU</span><span class="detail-value">LJ-001</span></div>
            <div class="detail-row"><span class="detail-label">Typ</span><span class="detail-value">Physisch</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
            <div class="detail-row"><span class="detail-label">Kategorie</span><span class="detail-value"><a href="?page=catalog/category_detail&id=1">Kleidung › Jacken</a></span></div>
            <div class="detail-row"><span class="detail-label">Hersteller</span><span class="detail-value">Leather Works GmbH</span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Preisgestaltung</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Verkaufspreis</span><span class="detail-value" style="font-size:24px;font-weight:600;">€299,00</span></div>
            <div class="detail-row"><span class="detail-label">Streichpreis</span><span class="detail-value" style="text-decoration:line-through;color:var(--text-muted);">€349,00</span></div>
            <div class="detail-row"><span class="detail-label">Einkaufspreis</span><span class="detail-value">€145,00</span></div>
            <div class="detail-row"><span class="detail-label">Marge</span><span class="detail-value"><span class="badge badge-success">51,5%</span></span></div>
            <div class="detail-row"><span class="detail-label">Steuer</span><span class="detail-value">19% MwSt.</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Beschreibung</h3></div>
    <div class="card-body">
        <p>Hochwertige Lederjacke aus 100% echtem Rindsleder. Handgefertigt in Italien mit besonderer Sorgfalt für Details. Die Jacke verfügt über einen klassischen Biker-Schnitt mit asymmetrischem Reißverschluss.</p>
        <p style="margin-top:12px;"><strong>Materialien:</strong> 100% Rindsleder, Polyester-Futter</p>
        <p><strong>Pflegehinweise:</strong> Professionelle Lederreinigung empfohlen</p>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Inventar</h3><a href="?page=catalog/inventory" class="btn btn-sm">Inventar verwalten</a></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Variante</th><th>SKU</th><th>Bestand</th><th>Reserviert</th><th>Verfügbar</th></tr></thead>
            <tbody>
                <tr><td>Schwarz / S</td><td>LJ-001-BK-S</td><td>12</td><td>2</td><td><span class="badge badge-success">10</span></td></tr>
                <tr><td>Schwarz / M</td><td>LJ-001-BK-M</td><td>18</td><td>0</td><td><span class="badge badge-success">18</span></td></tr>
                <tr><td>Schwarz / L</td><td>LJ-001-BK-L</td><td>8</td><td>1</td><td><span class="badge badge-success">7</span></td></tr>
                <tr><td>Braun / M</td><td>LJ-001-BR-M</td><td>3</td><td>0</td><td><span class="badge badge-warning">3</span></td></tr>
                <tr><td>Braun / L</td><td>LJ-001-BR-L</td><td>0</td><td>0</td><td><span class="badge badge-error">0</span></td></tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.detail-row { display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--border-subtle); }
.detail-row:last-child { border-bottom:none; }
.detail-label { color:var(--text-muted); }
.detail-value { font-weight:500; }
</style>
