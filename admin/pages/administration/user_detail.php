<?php /** Administration - Benutzer Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=administration/users">Admin-Benutzer</a> <span>›</span> <span>Julian Graf</span></nav>
        <h1>Julian Graf</h1>
        <p class="page-subtitle">Administrator · Letzter Login: Heute, 14:32</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=administration/user_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">block</span> Deaktivieren</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Benutzerdetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">Julian Graf</span></div>
            <div class="detail-row"><span class="detail-label">E-Mail</span><span class="detail-value">julian@meinshop.de</span></div>
            <div class="detail-row"><span class="detail-label">Benutzername</span><span class="detail-value">julian.graf</span></div>
            <div class="detail-row"><span class="detail-label">Rolle</span><span class="detail-value"><a href="?page=administration/role_detail&id=1">Administrator</a></span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Aktiv</span></span></div>
            <div class="detail-row"><span class="detail-label">2FA</span><span class="detail-value"><span class="badge badge-success">Aktiviert</span></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Aktivität</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Erstellt am</span><span class="detail-value">01.01.2025</span></div>
            <div class="detail-row"><span class="detail-label">Letzter Login</span><span class="detail-value">Heute, 14:32</span></div>
            <div class="detail-row"><span class="detail-label">Logins (30 Tage)</span><span class="detail-value">45</span></div>
            <div class="detail-row"><span class="detail-label">Letzte IP</span><span class="detail-value">192.168.1.xxx</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Berechtigungen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Bereich</th><th>Lesen</th><th>Erstellen</th><th>Bearbeiten</th><th>Löschen</th></tr></thead>
            <tbody>
                <tr><td>Katalog</td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
                <tr><td>Bestellungen</td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
                <tr><td>Kunden</td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
                <tr><td>System</td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td><td><span class="badge badge-success">✓</span></td></tr>
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
