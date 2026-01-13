<?php /** Kunden - Gruppendetail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=customers/groups">Kundengruppen</a> <span>›</span> <span>VIP</span></nav>
        <h1>VIP</h1>
        <p class="page-subtitle">Kundengruppe · 156 Mitglieder</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=customers/group_edit&id=3" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">delete</span> Löschen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Gruppendetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">VIP</span></div>
            <div class="detail-row"><span class="detail-label">Code</span><span class="detail-value">vip</span></div>
            <div class="detail-row"><span class="detail-label">Beschreibung</span><span class="detail-value">Treue Kunden mit hohem Bestellwert</span></div>
            <div class="detail-row"><span class="detail-label">Rabatt</span><span class="detail-value"><span class="badge badge-success">10%</span></span></div>
            <div class="detail-row"><span class="detail-label">Kostenloser Versand</span><span class="detail-value"><span class="badge badge-success">Ja</span></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Statistiken</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Mitglieder</span><span class="detail-value" style="font-size:24px;font-weight:600;">156</span></div>
            <div class="detail-row"><span class="detail-label">Ø Bestellwert</span><span class="detail-value">€320,00</span></div>
            <div class="detail-row"><span class="detail-label">Ø Bestellungen/Kunde</span><span class="detail-value">8,5</span></div>
            <div class="detail-row"><span class="detail-label">Gesamtumsatz</span><span class="detail-value">€425.280</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Gruppenmitglieder</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Kunde</th><th>E-Mail</th><th>Bestellungen</th><th>Umsatz</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr><td><a href="?page=customers/customer_detail&id=1">Max Mustermann</a></td><td>max.mustermann@email.de</td><td>12</td><td>€2.458</td><td class="table-actions"><a href="?page=customers/customer_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=customers/customer_detail&id=5">Anna Schmidt</a></td><td>anna.schmidt@email.de</td><td>18</td><td>€4.250</td><td class="table-actions"><a href="?page=customers/customer_detail&id=5" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                <tr><td><a href="?page=customers/customer_detail&id=8">Peter Weber</a></td><td>peter.weber@email.de</td><td>9</td><td>€3.120</td><td class="table-actions"><a href="?page=customers/customer_detail&id=8" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
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
