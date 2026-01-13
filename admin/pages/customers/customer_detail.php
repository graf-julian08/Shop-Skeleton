<?php /** Kunden - Kundendetail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=customers/customers">Kunden</a> <span>›</span> <span>Max Mustermann</span></nav>
        <h1>Max Mustermann</h1>
        <p class="page-subtitle">Kunde seit 15.01.2024 · VIP-Kunde</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=customers/customer_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">block</span> Sperren</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active">Übersicht</button>
    <button class="tab">Bestellungen</button>
    <button class="tab">Adressen</button>
    <button class="tab">Notizen</button>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Kundendaten</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">Max Mustermann</span></div>
            <div class="detail-row"><span class="detail-label">E-Mail</span><span class="detail-value">max.mustermann@email.de</span></div>
            <div class="detail-row"><span class="detail-label">Telefon</span><span class="detail-value">+49 170 1234567</span></div>
            <div class="detail-row"><span class="detail-label">Kundengruppe</span><span class="detail-value"><a href="?page=customers/group_detail&id=3">VIP</a></span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
            <div class="detail-row"><span class="detail-label">Newsletter</span><span class="detail-value"><span class="badge badge-success">Abonniert</span></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Statistiken</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Gesamtumsatz</span><span class="detail-value" style="font-size:24px;font-weight:600;">€2.458,00</span></div>
            <div class="detail-row"><span class="detail-label">Bestellungen</span><span class="detail-value">12</span></div>
            <div class="detail-row"><span class="detail-label">Durchschn. Bestellwert</span><span class="detail-value">€204,83</span></div>
            <div class="detail-row"><span class="detail-label">Letzte Bestellung</span><span class="detail-value">03.01.2026</span></div>
            <div class="detail-row"><span class="detail-label">Treuepunkte</span><span class="detail-value">2.458</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Letzte Bestellungen</h3><a href="?page=orders/orders&customer=1" class="btn btn-sm">Alle anzeigen</a></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Bestellung</th><th>Datum</th><th>Betrag</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr><td><a href="?page=orders/order_detail&id=10045">#10045</a></td><td>03.01.2026</td><td>€245,00</td><td><span class="badge badge-success">Abgeschlossen</span></td><td class="table-actions"><a href="?page=orders/order_detail&id=10045" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=orders/order_detail&id=10032">#10032</a></td><td>15.12.2025</td><td>€189,00</td><td><span class="badge badge-success">Abgeschlossen</span></td><td class="table-actions"><a href="?page=orders/order_detail&id=10032" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=orders/order_detail&id=10018">#10018</a></td><td>28.11.2025</td><td>€420,00</td><td><span class="badge badge-success">Abgeschlossen</span></td><td class="table-actions"><a href="?page=orders/order_detail&id=10018" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Adressen</h3><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">add</span> Adresse hinzufügen</button></div>
    <div class="card-body">
        <div class="address-grid">
            <div class="address-card">
                <div class="address-type"><span class="badge badge-info">Rechnungsadresse</span></div>
                <p><strong>Max Mustermann</strong><br>Musterstraße 123<br>12345 Musterstadt<br>Deutschland</p>
            </div>
            <div class="address-card">
                <div class="address-type"><span class="badge badge-warning">Lieferadresse</span></div>
                <p><strong>Max Mustermann</strong><br>Bürogebäude 4, Etage 2<br>Arbeitsweg 45<br>12345 Musterstadt<br>Deutschland</p>
            </div>
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
.address-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:16px; }
.address-card { padding:16px; background:var(--bg-tertiary); border-radius:var(--radius-md); }
.address-type { margin-bottom:8px; }
</style>
