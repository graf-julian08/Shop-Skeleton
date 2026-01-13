<?php /** Bestellungen - Retouren */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Retouren</h1>
        <p class="page-subtitle">Rückgaben und RMA verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Retoure anlegen</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Offene Retouren</span></div>
        <div class="kpi-value" style="color:var(--warning);">8</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Diese Woche</span></div>
        <div class="kpi-value">15</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Retourenquote</span></div>
        <div class="kpi-value">4,2%</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Aktive Retouren</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>RMA</th><th>Bestellung</th><th>Kunde</th><th>Produkte</th><th>Grund</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>#RET-089</strong></td>
                    <td>#10038</td>
                    <td>Thomas Koch</td>
                    <td>1 Artikel</td>
                    <td>Größe passt nicht</td>
                    <td><span class="badge badge-warning">Unterwegs</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
                <tr>
                    <td><strong>#RET-088</strong></td>
                    <td>#10035</td>
                    <td>Sarah Wagner</td>
                    <td>2 Artikel</td>
                    <td>Nicht gefallen</td>
                    <td><span class="badge badge-info">Eingegangen</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm btn-success"><span class="material-symbols-rounded">check</span></button></td>
                </tr>
                <tr>
                    <td><strong>#RET-087</strong></td>
                    <td>#10032</td>
                    <td>Michael Braun</td>
                    <td>1 Artikel</td>
                    <td>Defekt</td>
                    <td><span class="badge badge-success">Erstattet</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Retourengründe (letzte 30 Tage)</h3></div>
    <div class="card-body">
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-card-label">Größe passt nicht</div><div class="stat-card-value">42%</div></div>
            <div class="stat-card"><div class="stat-card-label">Nicht gefallen</div><div class="stat-card-value">28%</div></div>
            <div class="stat-card"><div class="stat-card-label">Defekt / Beschädigt</div><div class="stat-card-value">18%</div></div>
            <div class="stat-card"><div class="stat-card-label">Sonstiges</div><div class="stat-card-value">12%</div></div>
        </div>
    </div>
</div>
