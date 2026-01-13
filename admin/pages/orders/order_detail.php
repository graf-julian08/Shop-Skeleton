<?php /** Bestellungen - Bestelldetail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=orders/orders">Bestellungen</a> <span>›</span> <span>#10045</span></nav>
        <h1>Bestellung #10045</h1>
        <p class="page-subtitle">03.01.2026, 14:32 · <span class="badge badge-warning">Ausstehend</span></p>
    </div>
    <div class="page-header-actions">
        <a href="?page=orders/order_edit&id=10045" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <a href="?page=finance/invoice_detail&id=10045" class="btn"><span class="material-symbols-rounded">receipt</span> Rechnung</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">local_shipping</span> Versenden</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Bestelldetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Bestellnummer</span><span class="detail-value">#10045</span></div>
            <div class="detail-row"><span class="detail-label">Datum</span><span class="detail-value">03.01.2026, 14:32</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-warning">Ausstehend</span></span></div>
            <div class="detail-row"><span class="detail-label">Zahlungsstatus</span><span class="detail-value"><span class="badge badge-success">Bezahlt</span></span></div>
            <div class="detail-row"><span class="detail-label">Zahlungsart</span><span class="detail-value">PayPal</span></div>
            <div class="detail-row"><span class="detail-label">Versandart</span><span class="detail-value">DHL Express</span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Kunde</h3><a href="?page=customers/customer_detail&id=1" class="btn btn-sm">Profil öffnen</a></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value"><a href="?page=customers/customer_detail&id=1">Max Mustermann</a></span></div>
            <div class="detail-row"><span class="detail-label">E-Mail</span><span class="detail-value">max.mustermann@email.de</span></div>
            <div class="detail-row"><span class="detail-label">Telefon</span><span class="detail-value">+49 170 1234567</span></div>
            <div class="detail-row"><span class="detail-label">Kundengruppe</span><span class="detail-value"><a href="?page=customers/group_detail&id=3">VIP</a></span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bestellpositionen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Produkt</th><th>SKU</th><th>Preis</th><th>Menge</th><th>Gesamt</th></tr></thead>
            <tbody>
                <tr><td><a href="?page=catalog/product_detail&id=1">Premium Lederjacke</a><br><small style="color:var(--text-muted);">Schwarz / M</small></td><td>LJ-001-BK-M</td><td>€299,00</td><td>1</td><td>€299,00</td></tr>
                <tr><td><a href="?page=catalog/product_detail&id=15">Designer Gürtel</a><br><small style="color:var(--text-muted);">Schwarz / 85cm</small></td><td>DG-085</td><td>€89,00</td><td>1</td><td>€89,00</td></tr>
            </tbody>
            <tfoot>
                <tr><td colspan="4" style="text-align:right;">Zwischensumme</td><td>€388,00</td></tr>
                <tr><td colspan="4" style="text-align:right;">Versand (DHL Express)</td><td>€12,99</td></tr>
                <tr><td colspan="4" style="text-align:right;">MwSt. (19%)</td><td>€62,00</td></tr>
                <tr><td colspan="4" style="text-align:right;"><strong>Gesamtsumme</strong></td><td style="font-size:18px;font-weight:600;">€400,99</td></tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Rechnungsadresse</h3></div>
        <div class="card-body">
            <p><strong>Max Mustermann</strong><br>Musterstraße 123<br>12345 Musterstadt<br>Deutschland</p>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Lieferadresse</h3></div>
        <div class="card-body">
            <p><strong>Max Mustermann</strong><br>Musterstraße 123<br>12345 Musterstadt<br>Deutschland</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bestellverlauf</h3></div>
    <div class="card-body">
        <div class="timeline">
            <div class="timeline-item"><div class="timeline-dot success"></div><div class="timeline-content"><strong>Bezahlung erhalten</strong><br><small style="color:var(--text-muted);">03.01.2026, 14:35</small></div></div>
            <div class="timeline-item"><div class="timeline-dot"></div><div class="timeline-content"><strong>Bestellung aufgegeben</strong><br><small style="color:var(--text-muted);">03.01.2026, 14:32</small></div></div>
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
