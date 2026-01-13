<?php /** Reports - Kunden */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kunden-Report</h1>
        <p class="page-subtitle">Kundenanalyse und Segmentierung</p>
    </div>
    <div class="page-header-actions">
        <select class="form-select"><option>Letzte 30 Tage</option><option>Letzte 90 Tage</option><option>Dieses Jahr</option></select>
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Kunden gesamt</span></div>
        <div class="kpi-value">4.582</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+22,1%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Neukunden (30 Tage)</span></div>
        <div class="kpi-value">284</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+18,5%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Wiederkäufer-Rate</span></div>
        <div class="kpi-value">34,2%</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+5,3%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Ø Kundenwert (LTV)</span></div>
        <div class="kpi-value">€285,00</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+8,7%</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Kunden nach Gruppe</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Kundengruppe</th><th>Anzahl</th><th>Ø Bestellwert</th><th>Ø Bestellungen</th></tr></thead>
            <tbody>
                <tr><td>Standard</td><td>3.842</td><td>€72,50</td><td>1,8</td></tr>
                <tr><td>Premium</td><td>524</td><td>€145,00</td><td>4,2</td></tr>
                <tr><td>VIP</td><td>156</td><td>€320,00</td><td>8,5</td></tr>
                <tr><td>Großhandel</td><td>60</td><td>€1.250,00</td><td>12,3</td></tr>
            </tbody>
        </table>
    </div>
</div>
