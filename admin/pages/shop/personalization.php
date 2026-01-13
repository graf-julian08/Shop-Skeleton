<?php /** Shop - Personalisierung */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Personalisierung</h1>
        <p class="page-subtitle">Empfehlungen und personalisierte Inhalte</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Empfehlungs-Klicks</span></div>
        <div class="kpi-value">2.847</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+18,3% diese Woche</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Conversion durch Empfehlungen</span></div>
        <div class="kpi-value">4,2%</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+0,8%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Personalisierte Segmente</span></div>
        <div class="kpi-value">8</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Empfehlungsregeln</h3><button class="btn btn-sm"><span class="material-symbols-rounded">add</span> Regel</button></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Regel</th><th>Typ</th><th>Position</th><th>Status</th><th>Performance</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Ähnliche Produkte</strong></td>
                    <td><span class="badge badge-default">Produkt-basiert</span></td>
                    <td>Produktseite</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td><span style="color:var(--success);">+12% CTR</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><strong>Kürzlich angesehen</strong></td>
                    <td><span class="badge badge-default">Verhalten</span></td>
                    <td>Homepage</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td><span style="color:var(--success);">+8% CTR</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><strong>Kunden kauften auch</strong></td>
                    <td><span class="badge badge-default">Kollaborativ</span></td>
                    <td>Warenkorb</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td><span style="color:var(--success);">+15% AOV</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><strong>Trending jetzt</strong></td>
                    <td><span class="badge badge-default">Popularität</span></td>
                    <td>Homepage</td>
                    <td><label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label></td>
                    <td><span style="color:var(--text-muted);">-</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Kundensegmente</h3><button class="btn btn-sm"><span class="material-symbols-rounded">add</span> Segment</button></div>
    <div class="card-body">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-label">VIP Kunden</div>
                <div class="stat-card-value">156 Kunden</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Wiederkehrer</div>
                <div class="stat-card-value">423 Kunden</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Inaktiv (90+ Tage)</div>
                <div class="stat-card-value">89 Kunden</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Newsletter</div>
                <div class="stat-card-value">1.284 Abonnenten</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>A/B Test Empfehlungen</h3></div>
    <div class="card-body">
        <div class="alert alert-info" style="margin:0;">
            <span class="material-symbols-rounded">science</span>
            <div class="alert-content">
                <strong>Test aktiv:</strong> "Ähnliche Produkte" Widget - 4 Produkte vs. 6 Produkte
                <br><small>Läuft seit 5 Tagen • 2.340 Impressionen • Variante B führt mit +3,2% CTR</small>
            </div>
        </div>
    </div>
</div>
