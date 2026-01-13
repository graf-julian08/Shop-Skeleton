<?php /** Commerce - Abonnement Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/subscriptions">Abonnements</a> <span>›</span> <span>SUB-2026-0089</span></nav>
        <h1>Abonnement SUB-2026-0089</h1>
        <p class="page-subtitle">Premium Mitgliedschaft · <span class="badge badge-success">Aktiv</span></p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">pause</span> Pausieren</button>
        <button class="btn btn-danger"><span class="material-symbols-rounded">cancel</span> Kündigen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Abonnementdetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Abo-Nummer</span><span class="detail-value">SUB-2026-0089</span></div>
            <div class="detail-row"><span class="detail-label">Produkt</span><span class="detail-value"><a href="?page=catalog/product_detail&id=50">Premium Mitgliedschaft</a></span></div>
            <div class="detail-row"><span class="detail-label">Interval</span><span class="detail-value">Monatlich</span></div>
            <div class="detail-row"><span class="detail-label">Preis</span><span class="detail-value" style="font-size:18px;font-weight:600;">€19,99/Monat</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
            <div class="detail-row"><span class="detail-label">Nächste Abbuchung</span><span class="detail-value">01.02.2026</span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Kunde</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value"><a href="?page=customers/customer_detail&id=1">Max Mustermann</a></span></div>
            <div class="detail-row"><span class="detail-label">E-Mail</span><span class="detail-value">max.mustermann@email.de</span></div>
            <div class="detail-row"><span class="detail-label">Gestartet</span><span class="detail-value">01.06.2025</span></div>
            <div class="detail-row"><span class="detail-label">Laufzeit</span><span class="detail-value">7 Monate</span></div>
            <div class="detail-row"><span class="detail-label">Gesamtumsatz</span><span class="detail-value">€139,93</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Zahlungshistorie</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Datum</th><th>Betrag</th><th>Status</th><th>Rechnung</th></tr></thead>
            <tbody>
                <tr><td>01.01.2026</td><td>€19,99</td><td><span class="badge badge-success">Bezahlt</span></td><td><a href="?page=finance/invoice_detail&id=501">INV-501</a></td></tr>
                <tr><td>01.12.2025</td><td>€19,99</td><td><span class="badge badge-success">Bezahlt</span></td><td><a href="?page=finance/invoice_detail&id=445">INV-445</a></td></tr>
                <tr><td>01.11.2025</td><td>€19,99</td><td><span class="badge badge-success">Bezahlt</span></td><td><a href="?page=finance/invoice_detail&id=398">INV-398</a></td></tr>
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
