<?php /** Commerce - Preisregel Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/pricing">Preisregeln</a> <span>›</span> <span>VIP Kundenrabatt</span></nav>
        <h1>VIP Kundenrabatt</h1>
        <p class="page-subtitle">Katalogpreisregel · <span class="badge badge-success">Aktiv</span></p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/pricing_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">delete</span> Löschen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Regeldetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">VIP Kundenrabatt</span></div>
            <div class="detail-row"><span class="detail-label">Typ</span><span class="detail-value">Katalogpreisregel</span></div>
            <div class="detail-row"><span class="detail-label">Aktion</span><span class="detail-value">Prozent vom Preis abziehen</span></div>
            <div class="detail-row"><span class="detail-label">Wert</span><span class="detail-value" style="font-size:18px;font-weight:600;">-10%</span></div>
            <div class="detail-row"><span class="detail-label">Priorität</span><span class="detail-value">10</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Bedingungen</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Kundengruppe</span><span class="detail-value"><a href="?page=customers/group_detail&id=3">VIP</a></span></div>
            <div class="detail-row"><span class="detail-label">Kategorien</span><span class="detail-value">Alle</span></div>
            <div class="detail-row"><span class="detail-label">Produkte</span><span class="detail-value">Alle</span></div>
            <div class="detail-row"><span class="detail-label">Zeitraum</span><span class="detail-value">Unbegrenzt</span></div>
        </div>
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
