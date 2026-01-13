<?php /** Developer - API */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>API</h1>
        <p class="page-subtitle">REST API Zugang und Dokumentation</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> API-Schlüssel erstellen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>API-Schlüssel</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Name</th><th>Schlüssel</th><th>Berechtigungen</th><th>Erstellt</th><th>Letzter Zugriff</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Mobile App</strong></td>
                    <td><code style="background:var(--bg-tertiary);padding:4px 8px;border-radius:4px;">sk_live_abc...xyz</code></td>
                    <td>Lesen, Schreiben</td>
                    <td>01.01.2026</td>
                    <td>Vor 5 Minuten</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><strong>Warehouse Integration</strong></td>
                    <td><code style="background:var(--bg-tertiary);padding:4px 8px;border-radius:4px;">sk_live_def...uvw</code></td>
                    <td>Nur Lesen</td>
                    <td>15.12.2025</td>
                    <td>Gestern</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>API-Nutzung</h3></div>
    <div class="card-body">
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-header"><span class="kpi-title">Requests (heute)</span></div>
                <div class="kpi-value">12.450</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-header"><span class="kpi-title">Rate Limit</span></div>
                <div class="kpi-value">1.000/min</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-header"><span class="kpi-title">Fehlerrate</span></div>
                <div class="kpi-value" style="color:var(--success);">0,2%</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>API-Endpunkte</h3></div>
    <div class="card-body">
        <div style="font-family:monospace;font-size:13px;">
            <div style="padding:8px 0;border-bottom:1px solid var(--border-color);"><span class="badge badge-success" style="width:50px;text-align:center;">GET</span> <span style="margin-left:12px;">/api/v1/products</span></div>
            <div style="padding:8px 0;border-bottom:1px solid var(--border-color);"><span class="badge badge-info" style="width:50px;text-align:center;">POST</span> <span style="margin-left:12px;">/api/v1/products</span></div>
            <div style="padding:8px 0;border-bottom:1px solid var(--border-color);"><span class="badge badge-success" style="width:50px;text-align:center;">GET</span> <span style="margin-left:12px;">/api/v1/orders</span></div>
            <div style="padding:8px 0;border-bottom:1px solid var(--border-color);"><span class="badge badge-success" style="width:50px;text-align:center;">GET</span> <span style="margin-left:12px;">/api/v1/customers</span></div>
            <div style="padding:8px 0;"><span class="badge badge-warning" style="width:50px;text-align:center;">PUT</span> <span style="margin-left:12px;">/api/v1/inventory/{id}</span></div>
        </div>
        <button class="btn" style="margin-top:16px;"><span class="material-symbols-rounded">description</span> Vollständige Dokumentation</button>
    </div>
</div>
