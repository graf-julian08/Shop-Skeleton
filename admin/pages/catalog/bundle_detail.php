<?php /** Katalog - Bundle Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/bundles">Bundles</a> <span>›</span> <span>Outdoor Komplett-Set</span></nav>
        <h1>Outdoor Komplett-Set</h1>
        <p class="page-subtitle">Bundle · 4 Produkte · Aktiv</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/bundle_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">delete</span> Löschen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Bundle-Details</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">Outdoor Komplett-Set</span></div>
            <div class="detail-row"><span class="detail-label">SKU</span><span class="detail-value">BDL-OUTDOOR-01</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
            <div class="detail-row"><span class="detail-label">Typ</span><span class="detail-value">Festes Bundle</span></div>
            <div class="detail-row"><span class="detail-label">Kategorie</span><span class="detail-value"><a href="?page=catalog/category_detail&id=6">Outdoor</a></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Preisgestaltung</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Bundle-Preis</span><span class="detail-value" style="font-size:24px;font-weight:600;">€449,00</span></div>
            <div class="detail-row"><span class="detail-label">Einzelpreis-Summe</span><span class="detail-value" style="text-decoration:line-through;color:var(--text-muted);">€520,00</span></div>
            <div class="detail-row"><span class="detail-label">Ersparnis</span><span class="detail-value"><span class="badge badge-success">-14%</span></span></div>
            <div class="detail-row"><span class="detail-label">Verkäufe (30 Tage)</span><span class="detail-value">28</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bundle-Produkte</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Produkt</th><th>SKU</th><th>Einzelpreis</th><th>Menge</th><th>Optional</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr><td><a href="?page=catalog/product_detail&id=10">Wanderrucksack Pro</a></td><td>WR-PRO-01</td><td>€149,00</td><td>1</td><td><span class="badge badge-default">Nein</span></td><td class="table-actions"><a href="?page=catalog/product_detail&id=10" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=catalog/product_detail&id=11">Wanderschuhe Trail</a></td><td>WS-TRAIL-42</td><td>€189,00</td><td>1</td><td><span class="badge badge-default">Nein</span></td><td class="table-actions"><a href="?page=catalog/product_detail&id=11" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=catalog/product_detail&id=12">Regenjacke Waterproof</a></td><td>RJ-WP-M</td><td>€129,00</td><td>1</td><td><span class="badge badge-success">Ja</span></td><td class="table-actions"><a href="?page=catalog/product_detail&id=12" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=catalog/product_detail&id=13">Trinkflasche 1L</a></td><td>TF-1L</td><td>€29,00</td><td>2</td><td><span class="badge badge-default">Nein</span></td><td class="table-actions"><a href="?page=catalog/product_detail&id=13" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
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
