<?php /** Katalog - Kategorie Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/categories">Kategorien</a> <span>›</span> <span>Kleidung</span></nav>
        <h1>Kleidung</h1>
        <p class="page-subtitle">Hauptkategorie · 4 Unterkategorien · 156 Produkte</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/category_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">delete</span> Löschen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Kategorie-Details</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">Kleidung</span></div>
            <div class="detail-row"><span class="detail-label">Slug</span><span class="detail-value">/kleidung</span></div>
            <div class="detail-row"><span class="detail-label">Übergeordnet</span><span class="detail-value">— (Hauptkategorie)</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
            <div class="detail-row"><span class="detail-label">Position</span><span class="detail-value">1</span></div>
            <div class="detail-row"><span class="detail-label">In Navigation</span><span class="detail-value"><span class="badge badge-success">Ja</span></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Statistiken</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Produkte gesamt</span><span class="detail-value" style="font-size:24px;font-weight:600;">156</span></div>
            <div class="detail-row"><span class="detail-label">Aktive Produkte</span><span class="detail-value">142</span></div>
            <div class="detail-row"><span class="detail-label">Umsatz (30 Tage)</span><span class="detail-value">€24.580</span></div>
            <div class="detail-row"><span class="detail-label">Aufrufe (30 Tage)</span><span class="detail-value">8.420</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Unterkategorien</h3><a href="?page=catalog/category_create" class="btn btn-sm btn-primary"><span class="material-symbols-rounded">add</span> Hinzufügen</a></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Name</th><th>Produkte</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr><td><a href="?page=catalog/category_detail&id=2">Jacken</a></td><td>45</td><td><span class="badge badge-success">Aktiv</span></td><td class="table-actions"><a href="?page=catalog/category_edit&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/category_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=catalog/category_detail&id=3">Hosen</a></td><td>38</td><td><span class="badge badge-success">Aktiv</span></td><td class="table-actions"><a href="?page=catalog/category_edit&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/category_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=catalog/category_detail&id=4">Pullover</a></td><td>42</td><td><span class="badge badge-success">Aktiv</span></td><td class="table-actions"><a href="?page=catalog/category_edit&id=4" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/category_detail&id=4" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=catalog/category_detail&id=5">T-Shirts</a></td><td>31</td><td><span class="badge badge-warning">Entwurf</span></td><td class="table-actions"><a href="?page=catalog/category_edit&id=5" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/category_detail&id=5" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
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
