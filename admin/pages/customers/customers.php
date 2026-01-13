<?php /** Kunden - Kundenliste */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kunden</h1>
        <p class="page-subtitle">Alle Kunden verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Gesamt Kunden</span></div>
        <div class="kpi-value">1.284</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+89 diesen Monat</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Aktive Kunden</span></div>
        <div class="kpi-value">956</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Durchschn. Bestellwert</span></div>
        <div class="kpi-value">€145,00</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Wiederkaufrate</span></div>
        <div class="kpi-value">34%</div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="filters">
            <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Name oder E-Mail suchen..."></div>
            <select class="filter-select"><option>Alle Gruppen</option><option>Standard</option><option>VIP</option><option>Großhandel</option></select>
            <select class="filter-select"><option>Alle Status</option><option>Aktiv</option><option>Inaktiv</option></select>
        </div>
        <table class="table">
            <thead><tr><th><input type="checkbox" class="select-all"></th><th>Kunde</th><th>E-Mail</th><th>Bestellungen</th><th>Umsatz</th><th>Gruppe</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><a href="?page=customers/customer_detail&id=1"><strong>Max Mustermann</strong></a></td>
                    <td>max@example.com</td>
                    <td>12</td>
                    <td>€1.890,00</td>
                    <td><a href="?page=customers/group_detail&id=3"><span class="badge badge-info">VIP</span></a></td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=customers/customer_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=customers/customer_edit&id=1" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><a href="?page=customers/customer_detail&id=2"><strong>Anna Schmidt</strong></a></td>
                    <td>anna.schmidt@example.com</td>
                    <td>8</td>
                    <td>€1.245,00</td>
                    <td><a href="?page=customers/group_detail&id=1"><span class="badge badge-default">Standard</span></a></td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=customers/customer_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=customers/customer_edit&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><a href="?page=customers/customer_detail&id=3"><strong>Peter Weber</strong></a></td>
                    <td>p.weber@company.de</td>
                    <td>45</td>
                    <td>€12.450,00</td>
                    <td><a href="?page=customers/group_detail&id=4"><span class="badge badge-warning">Großhandel</span></a></td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=customers/customer_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=customers/customer_edit&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><a href="?page=customers/customer_detail&id=4"><strong>Lisa Müller</strong></a></td>
                    <td>lisa.m@email.de</td>
                    <td>3</td>
                    <td>€289,00</td>
                    <td><a href="?page=customers/group_detail&id=1"><span class="badge badge-default">Standard</span></a></td>
                    <td><span class="badge badge-default">Inaktiv</span></td>
                    <td class="table-actions"><a href="?page=customers/customer_detail&id=4" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=customers/customer_edit&id=4" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
