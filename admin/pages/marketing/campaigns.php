<?php /** Marketing - Kampagnen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kampagnen</h1>
        <p class="page-subtitle">Marketing-Kampagnen verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Kampagne erstellen</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Aktive Kampagnen</span></div>
        <div class="kpi-value">4</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Reichweite (Woche)</span></div>
        <div class="kpi-value">12.450</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Conversions</span></div>
        <div class="kpi-value">234</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">ROI</span></div>
        <div class="kpi-value" style="color:var(--success);">4,2x</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Alle Kampagnen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Kampagne</th><th>Typ</th><th>Zeitraum</th><th>Budget</th><th>Performance</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Winter Sale 2026</strong></td>
                    <td><span class="badge badge-default">Sale</span></td>
                    <td>01.01. - 31.01.</td>
                    <td>€2.500</td>
                    <td style="color:var(--success);">+156% Umsatz</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">bar_chart</span></button></td>
                </tr>
                <tr>
                    <td><strong>Newsletter-Neukunden</strong></td>
                    <td><span class="badge badge-info">E-Mail</span></td>
                    <td>Dauerhaft</td>
                    <td>-</td>
                    <td>3,2% CTR</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">bar_chart</span></button></td>
                </tr>
                <tr>
                    <td><strong>Retargeting Q1</strong></td>
                    <td><span class="badge badge-warning">Ads</span></td>
                    <td>01.01. - 31.03.</td>
                    <td>€5.000</td>
                    <td>2,8% Conv.</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">bar_chart</span></button></td>
                </tr>
                <tr>
                    <td><strong>Black Friday 2025</strong></td>
                    <td><span class="badge badge-default">Sale</span></td>
                    <td>24.11. - 30.11.</td>
                    <td>€8.000</td>
                    <td style="color:var(--success);">+420% Umsatz</td>
                    <td><span class="badge badge-default">Beendet</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">bar_chart</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
