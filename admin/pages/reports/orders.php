<?php /** Reports - Bestellungen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Bestellungs-Report</h1>
        <p class="page-subtitle">Bestellstatistiken und Trends</p>
    </div>
    <div class="page-header-actions">
        <select class="form-select"><option>Letzte 30 Tage</option><option>Letzte 90 Tage</option><option>Dieses Jahr</option></select>
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Bestellungen gesamt</span></div>
        <div class="kpi-value">1.392</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+12,3%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Abgeschlossen</span></div>
        <div class="kpi-value">1.284</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>92,2%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Storniert</span></div>
        <div class="kpi-value">58</div>
        <div class="kpi-change negative"><span class="material-symbols-rounded">trending_down</span>4,2%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Retouren</span></div>
        <div class="kpi-value">50</div>
        <div class="kpi-change negative"><span class="material-symbols-rounded">trending_down</span>3,6%</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bestellungen nach Status</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Status</th><th>Anzahl</th><th>Anteil</th><th>Ø Bearbeitungszeit</th></tr></thead>
            <tbody>
                <tr><td><span class="badge badge-success">Abgeschlossen</span></td><td>1.284</td><td>92,2%</td><td>2,3 Tage</td></tr>
                <tr><td><span class="badge badge-info">Versendet</span></td><td>45</td><td>3,2%</td><td>-</td></tr>
                <tr><td><span class="badge badge-warning">In Bearbeitung</span></td><td>25</td><td>1,8%</td><td>-</td></tr>
                <tr><td><span class="badge badge-error">Storniert</span></td><td>38</td><td>2,8%</td><td>-</td></tr>
            </tbody>
        </table>
    </div>
</div>
