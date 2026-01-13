<?php /** Marketing - Analysen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Marketing Analytics</h1>
        <p class="page-subtitle">Traffic und Conversion-Analysen</p>
    </div>
    <div class="page-header-actions">
        <select class="filter-select"><option>Letzte 7 Tage</option><option selected>Letzte 30 Tage</option><option>Dieses Jahr</option></select>
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Besucher</span></div>
        <div class="kpi-value">45.678</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+12,4%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Conversion Rate</span></div>
        <div class="kpi-value">2,8%</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+0,3%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Avg. Warenkorbwert</span></div>
        <div class="kpi-value">€89,50</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Bounce Rate</span></div>
        <div class="kpi-value">42%</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Traffic-Übersicht</h3></div>
    <div class="card-body" style="height:300px;display:flex;align-items:center;justify-content:center;background:var(--bg-tertiary);border-radius:var(--radius-md);">
        <span style="color:var(--text-muted);">📈 Traffic-Chart Placeholder</span>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Traffic-Quellen</h3></div>
        <div class="card-body">
            <table class="table">
                <tbody>
                    <tr><td>Organic Search</td><td>35%</td><td style="color:var(--success);">+5%</td></tr>
                    <tr><td>Direct</td><td>28%</td><td style="color:var(--success);">+2%</td></tr>
                    <tr><td>Social</td><td>18%</td><td style="color:var(--success);">+8%</td></tr>
                    <tr><td>Paid Ads</td><td>12%</td><td style="color:var(--error);">-3%</td></tr>
                    <tr><td>Email</td><td>7%</td><td style="color:var(--success);">+1%</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Top Landing Pages</h3></div>
        <div class="card-body">
            <table class="table">
                <tbody>
                    <tr><td>/</td><td>28%</td></tr>
                    <tr><td>/produkte</td><td>22%</td></tr>
                    <tr><td>/winter-sale</td><td>18%</td></tr>
                    <tr><td>/lederjacke-premium</td><td>8%</td></tr>
                    <tr><td>/sneaker</td><td>6%</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
