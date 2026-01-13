<?php /** Developer - Webhooks */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Webhooks</h1>
        <p class="page-subtitle">Event-basierte Benachrichtigungen</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Webhook erstellen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Alle Webhooks</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Endpoint</th><th>Events</th><th>Erfolgsrate</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><code>https://api.example.com/orders</code></td>
                    <td>order.created, order.updated</td>
                    <td><span style="color:var(--success);">99,8%</span></td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">history</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><code>https://slack.com/services/xxx</code></td>
                    <td>order.created</td>
                    <td><span style="color:var(--success);">100%</span></td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">history</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Verfügbare Events</h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;">
            <div style="padding:8px;background:var(--bg-tertiary);border-radius:var(--radius-sm);font-size:13px;"><code>order.created</code></div>
            <div style="padding:8px;background:var(--bg-tertiary);border-radius:var(--radius-sm);font-size:13px;"><code>order.updated</code></div>
            <div style="padding:8px;background:var(--bg-tertiary);border-radius:var(--radius-sm);font-size:13px;"><code>order.cancelled</code></div>
            <div style="padding:8px;background:var(--bg-tertiary);border-radius:var(--radius-sm);font-size:13px;"><code>customer.created</code></div>
            <div style="padding:8px;background:var(--bg-tertiary);border-radius:var(--radius-sm);font-size:13px;"><code>customer.updated</code></div>
            <div style="padding:8px;background:var(--bg-tertiary);border-radius:var(--radius-sm);font-size:13px;"><code>product.created</code></div>
            <div style="padding:8px;background:var(--bg-tertiary);border-radius:var(--radius-sm);font-size:13px;"><code>product.updated</code></div>
            <div style="padding:8px;background:var(--bg-tertiary);border-radius:var(--radius-sm);font-size:13px;"><code>inventory.low</code></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Letzte Auslieferungen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Zeit</th><th>Event</th><th>Endpoint</th><th>Status</th><th>Response</th></tr></thead>
            <tbody>
                <tr><td>09:15:23</td><td>order.created</td><td>api.example.com</td><td><span class="badge badge-success">200</span></td><td>45ms</td></tr>
                <tr><td>09:14:58</td><td>order.created</td><td>slack.com</td><td><span class="badge badge-success">200</span></td><td>120ms</td></tr>
                <tr><td>09:12:45</td><td>order.updated</td><td>api.example.com</td><td><span class="badge badge-success">200</span></td><td>52ms</td></tr>
            </tbody>
        </table>
    </div>
</div>
