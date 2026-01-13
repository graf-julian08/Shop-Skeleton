<?php /** Reports - Umsatz */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Umsatz-Report</h1>
        <p class="page-subtitle">Umsatzentwicklung und Erlösanalyse</p>
    </div>
    <div class="page-header-actions">
        <select class="form-select"><option>Letzte 30 Tage</option><option>Letzte 90 Tage</option><option>Dieses Jahr</option><option>Letztes Jahr</option></select>
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Gesamtumsatz</span></div>
        <div class="kpi-value">€124.580,00</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+18,5%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Durchschn. Bestellwert</span></div>
        <div class="kpi-value">€89,50</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+5,2%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Bruttomarge</span></div>
        <div class="kpi-value">42,3%</div>
        <div class="kpi-change negative"><span class="material-symbols-rounded">trending_down</span>-1,8%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Erstattungen</span></div>
        <div class="kpi-value">€2.340,00</div>
        <div class="kpi-change negative"><span class="material-symbols-rounded">trending_up</span>+12,0%</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Umsatz nach Kategorie</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Kategorie</th><th>Umsatz</th><th>Anteil</th><th>Trend</th></tr></thead>
            <tbody>
                <tr><td>Kleidung</td><td>€52.340,00</td><td>42%</td><td><span class="badge badge-success">+15%</span></td></tr>
                <tr><td>Accessoires</td><td>€31.200,00</td><td>25%</td><td><span class="badge badge-success">+8%</span></td></tr>
                <tr><td>Schuhe</td><td>€24.800,00</td><td>20%</td><td><span class="badge badge-warning">+2%</span></td></tr>
                <tr><td>Digitale Produkte</td><td>€16.240,00</td><td>13%</td><td><span class="badge badge-success">+25%</span></td></tr>
            </tbody>
        </table>
    </div>
</div>
