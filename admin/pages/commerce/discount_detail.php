<?php /** Commerce - Rabatt Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/discounts">Rabatte</a> <span>›</span> <span>Winterschlussverkauf</span></nav>
        <h1>Winterschlussverkauf</h1>
        <p class="page-subtitle">Prozentrabatt · <span class="badge badge-success">Aktiv</span></p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/discount_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">delete</span> Löschen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Rabattdetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">Winterschlussverkauf</span></div>
            <div class="detail-row"><span class="detail-label">Typ</span><span class="detail-value">Prozentrabatt</span></div>
            <div class="detail-row"><span class="detail-label">Wert</span><span class="detail-value" style="font-size:24px;font-weight:600;">-20%</span></div>
            <div class="detail-row"><span class="detail-label">Gültig von</span><span class="detail-value">01.01.2026</span></div>
            <div class="detail-row"><span class="detail-label">Gültig bis</span><span class="detail-value">31.01.2026</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Statistiken</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Nutzungen</span><span class="detail-value">245</span></div>
            <div class="detail-row"><span class="detail-label">Rabattierter Umsatz</span><span class="detail-value">€32.450</span></div>
            <div class="detail-row"><span class="detail-label">Rabattsumme</span><span class="detail-value">€8.112</span></div>
            <div class="detail-row"><span class="detail-label">Ø Bestellwert</span><span class="detail-value">€132,45</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bedingungen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Bedingung</th><th>Wert</th></tr></thead>
            <tbody>
                <tr><td>Mindestbestellwert</td><td>€50,00</td></tr>
                <tr><td>Gilt für Kategorien</td><td><a href="?page=catalog/category_detail&id=1">Kleidung</a>, <a href="?page=catalog/category_detail&id=2">Jacken</a></td></tr>
                <tr><td>Kundengruppen</td><td>Alle</td></tr>
                <tr><td>Kombinierbar</td><td><span class="badge badge-error">Nein</span></td></tr>
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
