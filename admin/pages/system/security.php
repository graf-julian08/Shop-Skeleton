<?php /** System - Sicherheit */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Sicherheit</h1>
        <p class="page-subtitle">Sicherheitseinstellungen und Protokolle</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="alert alert-success">
    <span class="material-symbols-rounded">verified_user</span>
    <div class="alert-content"><strong>Sicherheitsstatus: Gut</strong><br>Alle Sicherheitsprüfungen bestanden.</div>
</div>

<div class="card">
    <div class="card-header"><h3>Authentifizierung</h3></div>
    <div class="card-body">
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Zwei-Faktor-Authentifizierung erforderlich</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Starke Passwörter erzwingen (mind. 12 Zeichen)</span></label></div>
        <div class="form-group">
            <label class="form-label">Session-Timeout (Minuten)</label>
            <input type="number" class="form-input" value="60" style="width:120px;">
        </div>
        <div class="form-group">
            <label class="form-label">Max. fehlgeschlagene Login-Versuche</label>
            <input type="number" class="form-input" value="5" style="width:120px;">
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>SSL & HTTPS</h3></div>
    <div class="card-body">
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>HTTPS erzwingen</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>HSTS aktivieren</span></label></div>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-card-label">SSL-Zertifikat</div><div class="stat-card-value" style="color:var(--success);">Gültig</div></div>
            <div class="stat-card"><div class="stat-card-label">Läuft ab</div><div class="stat-card-value">15.06.2026</div></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Login-Protokoll</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Benutzer</th><th>IP-Adresse</th><th>Zeitpunkt</th><th>Status</th></tr></thead>
            <tbody>
                <tr><td>julian@example.com</td><td>192.168.1.100</td><td>07.01.2026 09:15</td><td><span class="badge badge-success">Erfolgreich</span></td></tr>
                <tr><td>anna@example.com</td><td>192.168.1.105</td><td>07.01.2026 07:30</td><td><span class="badge badge-success">Erfolgreich</span></td></tr>
                <tr><td>unknown@hacker.com</td><td>45.33.32.156</td><td>06.01.2026 23:45</td><td><span class="badge badge-error">Fehlgeschlagen</span></td></tr>
            </tbody>
        </table>
    </div>
</div>
