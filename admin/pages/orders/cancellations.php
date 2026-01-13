<?php /** Bestellungen - Stornierungen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Stornierungen</h1>
        <p class="page-subtitle">Stornierte Bestellungen</p>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Stornierungen (Monat)</span></div>
        <div class="kpi-value">23</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Stornoquote</span></div>
        <div class="kpi-value">1,8%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Erstattungen</span></div>
        <div class="kpi-value">€2.890</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Stornierte Bestellungen</h3></div>
    <div class="card-body">
        <div class="filters">
            <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Suchen..."></div>
            <select class="filter-select"><option>Alle Gründe</option><option>Kunde</option><option>Zahlung fehlgeschlagen</option><option>Betrug</option><option>Nicht lieferbar</option></select>
        </div>
        <table class="table">
            <thead><tr><th>Bestellung</th><th>Datum</th><th>Kunde</th><th>Betrag</th><th>Grund</th><th>Erstattung</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>#10042</strong></td>
                    <td>06.01.2026</td>
                    <td>Lisa Müller</td>
                    <td>€67,80</td>
                    <td>Zahlung fehlgeschlagen</td>
                    <td><span class="badge badge-default">Keine</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
                <tr>
                    <td><strong>#10039</strong></td>
                    <td>05.01.2026</td>
                    <td>Frank Schmidt</td>
                    <td>€189,00</td>
                    <td>Kunde wünscht Storno</td>
                    <td><span class="badge badge-success">Erstattet</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
                <tr>
                    <td><strong>#10036</strong></td>
                    <td>04.01.2026</td>
                    <td>Maria Bauer</td>
                    <td>€245,50</td>
                    <td>Nicht lieferbar</td>
                    <td><span class="badge badge-success">Erstattet</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
