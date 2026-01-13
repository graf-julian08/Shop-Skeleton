<?php /** Commerce - Zahlungsanbieter Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/payments">Zahlungen</a> <span>›</span> <span>PayPal</span></nav>
        <h1>PayPal</h1>
        <p class="page-subtitle">Zahlungsanbieter · <span class="badge badge-success">Aktiv</span></p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/payment_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">power_off</span> Deaktivieren</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Anbieterdetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">PayPal</span></div>
            <div class="detail-row"><span class="detail-label">Typ</span><span class="detail-value">PayPal Commerce Platform</span></div>
            <div class="detail-row"><span class="detail-label">Modus</span><span class="detail-value"><span class="badge badge-warning">Sandbox</span></span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
            <div class="detail-row"><span class="detail-label">Position</span><span class="detail-value">1</span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Statistiken (30 Tage)</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Transaktionen</span><span class="detail-value">324</span></div>
            <div class="detail-row"><span class="detail-label">Umsatz</span><span class="detail-value" style="font-size:18px;font-weight:600;">€28.450</span></div>
            <div class="detail-row"><span class="detail-label">Erfolgsrate</span><span class="detail-value"><span class="badge badge-success">98,2%</span></span></div>
            <div class="detail-row"><span class="detail-label">Gebühren</span><span class="detail-value">€512,10</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Konfiguration</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Einstellung</th><th>Wert</th></tr></thead>
            <tbody>
                <tr><td>Client-ID</td><td>AaBbCc***</td></tr>
                <tr><td>Zahlungsarten</td><td>PayPal, Kreditkarte, Lastschrift</td></tr>
                <tr><td>Express-Checkout</td><td><span class="badge badge-success">Aktiviert</span></td></tr>
                <tr><td>Pay Later</td><td><span class="badge badge-success">Aktiviert</span></td></tr>
                <tr><td>Min. Bestellwert</td><td>€5,00</td></tr>
                <tr><td>Max. Bestellwert</td><td>€10.000,00</td></tr>
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
