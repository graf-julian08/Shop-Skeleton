<?php /** Commerce - Versandmethode Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/shipping">Versand</a> <span>›</span> <span>DHL Express</span></nav>
        <h1>DHL Express</h1>
        <p class="page-subtitle">Versandmethode · <span class="badge badge-success">Aktiv</span></p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/shipping_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">power_off</span> Deaktivieren</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Versanddetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">DHL Express</span></div>
            <div class="detail-row"><span class="detail-label">Anbieter</span><span class="detail-value">DHL</span></div>
            <div class="detail-row"><span class="detail-label">Lieferzeit</span><span class="detail-value">1-2 Werktage</span></div>
            <div class="detail-row"><span class="detail-label">Tracking</span><span class="detail-value"><span class="badge badge-success">Aktiviert</span></span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Statistiken (30 Tage)</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Sendungen</span><span class="detail-value">156</span></div>
            <div class="detail-row"><span class="detail-label">Versandkosten-Einnahmen</span><span class="detail-value">€1.950</span></div>
            <div class="detail-row"><span class="detail-label">Ø Lieferzeit</span><span class="detail-value">1,4 Tage</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Preisgestaltung</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Zone</th><th>Gewicht bis</th><th>Preis</th></tr></thead>
            <tbody>
                <tr><td>Deutschland</td><td>2 kg</td><td>€9,99</td></tr>
                <tr><td>Deutschland</td><td>5 kg</td><td>€12,99</td></tr>
                <tr><td>Deutschland</td><td>10 kg</td><td>€15,99</td></tr>
                <tr><td>EU</td><td>2 kg</td><td>€14,99</td></tr>
                <tr><td>EU</td><td>5 kg</td><td>€19,99</td></tr>
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
