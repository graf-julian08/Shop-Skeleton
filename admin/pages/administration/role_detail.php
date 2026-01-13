<?php /** Administration - Rolle Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=administration/roles">Admin-Rollen</a> <span>›</span> <span>Administrator</span></nav>
        <h1>Administrator</h1>
        <p class="page-subtitle">Rolle · 2 Benutzer</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=administration/role_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger" disabled><span class="material-symbols-rounded">delete</span> Löschen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Rollendetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">Administrator</span></div>
            <div class="detail-row"><span class="detail-label">Code</span><span class="detail-value">admin</span></div>
            <div class="detail-row"><span class="detail-label">Beschreibung</span><span class="detail-value">Vollzugriff auf alle Bereiche</span></div>
            <div class="detail-row"><span class="detail-label">Benutzer</span><span class="detail-value">2</span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Zugewiesene Benutzer</h3></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Benutzer</th><th>E-Mail</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr><td><a href="?page=administration/user_detail&id=1">Julian Graf</a></td><td>julian@meinshop.de</td><td class="table-actions"><a href="?page=administration/user_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                    <tr><td><a href="?page=administration/user_detail&id=2">Maria Müller</a></td><td>maria@meinshop.de</td><td class="table-actions"><a href="?page=administration/user_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Berechtigungen</h3></div>
    <div class="card-body">
        <table class="table permission-table">
            <thead><tr><th>Bereich</th><th>Lesen</th><th>Erstellen</th><th>Bearbeiten</th><th>Löschen</th></tr></thead>
            <tbody>
                <tr><td><strong>Dashboard</strong></td><td><span class="badge badge-success">✓</span></td><td>—</td><td>—</td><td>—</td></tr>
                <tr><td><strong>Katalog</strong></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
                <tr><td><strong>Bestellungen</strong></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
                <tr><td><strong>Kunden</strong></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
                <tr><td><strong>Marketing</strong></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
                <tr><td><strong>Reports</strong></td><td><span class="badge badge-success">✓</span></td><td>—</td><td>—</td><td>—</td></tr>
                <tr><td><strong>Finanzen</strong></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
                <tr><td><strong>System</strong></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
                <tr><td><strong>Administration</strong></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.detail-row { display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--border-subtle); }
.detail-row:last-child { border-bottom:none; }
.detail-label { color:var(--text-muted); }
.detail-value { font-weight:500; }
</style>
