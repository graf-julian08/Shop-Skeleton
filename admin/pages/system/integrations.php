<?php /** System - Integrationen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Integrationen</h1>
        <p class="page-subtitle">Drittanbieter-Dienste verbinden</p>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Verbundene Dienste</h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;">
                <strong>Google Analytics</strong>
                <p style="color:var(--text-muted);font-size:13px;margin:8px 0;">Traffic-Analyse und Reporting</p>
                <span class="badge badge-success">Verbunden</span>
                <button class="btn btn-sm" style="margin-left:8px;"><span class="material-symbols-rounded">settings</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;">
                <strong>Mailchimp</strong>
                <p style="color:var(--text-muted);font-size:13px;margin:8px 0;">E-Mail-Marketing</p>
                <span class="badge badge-success">Verbunden</span>
                <button class="btn btn-sm" style="margin-left:8px;"><span class="material-symbols-rounded">settings</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;">
                <strong>Zapier</strong>
                <p style="color:var(--text-muted);font-size:13px;margin:8px 0;">Automatisierungen</p>
                <span class="badge badge-success">Verbunden</span>
                <button class="btn btn-sm" style="margin-left:8px;"><span class="material-symbols-rounded">settings</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;">
                <strong>Slack</strong>
                <p style="color:var(--text-muted);font-size:13px;margin:8px 0;">Benachrichtigungen</p>
                <span class="badge badge-default">Nicht verbunden</span>
                <button class="btn btn-sm" style="margin-left:8px;"><span class="material-symbols-rounded">add</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;">
                <strong>Facebook Pixel</strong>
                <p style="color:var(--text-muted);font-size:13px;margin:8px 0;">Conversion Tracking</p>
                <span class="badge badge-success">Verbunden</span>
                <button class="btn btn-sm" style="margin-left:8px;"><span class="material-symbols-rounded">settings</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;">
                <strong>Zendesk</strong>
                <p style="color:var(--text-muted);font-size:13px;margin:8px 0;">Kundensupport</p>
                <span class="badge badge-default">Nicht verbunden</span>
                <button class="btn btn-sm" style="margin-left:8px;"><span class="material-symbols-rounded">add</span></button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Webhooks</h3><button class="btn btn-sm"><span class="material-symbols-rounded">add</span> Webhook</button></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>URL</th><th>Events</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td>https://api.example.com/orders</td>
                    <td>order.created, order.updated</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td>https://zapier.com/hooks/xxx</td>
                    <td>customer.created</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
