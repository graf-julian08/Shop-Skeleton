<?php /** Administration - Benutzer bearbeiten */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=administration/users">Admin-Benutzer</a> <span>›</span> <a href="?page=administration/user_detail&id=1">Julian Graf</a> <span>›</span> <span>Bearbeiten</span></nav>
        <h1>Benutzer bearbeiten</h1>
        <p class="page-subtitle">Julian Graf</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=administration/user_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Persönliche Daten</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Vorname <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" value="Julian">
                </div>
                <div class="form-group">
                    <label class="form-label">Nachname <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" value="Graf">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">E-Mail <span style="color:var(--error)">*</span></label>
                <input type="email" class="form-input" value="julian@meinshop.de">
            </div>
            <div class="form-group">
                <label class="form-label">Benutzername</label>
                <input type="text" class="form-input" value="julian.graf">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Kontoeinstellungen</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Rolle <span style="color:var(--error)">*</span></label>
                <select class="form-select"><option selected>Administrator</option><option>Manager</option><option>Editor</option><option>Support</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select"><option selected>Aktiv</option><option>Deaktiviert</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">2-Faktor-Authentifizierung</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Passwort ändern</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Neues Passwort</label>
                <input type="password" class="form-input" placeholder="Leer = nicht ändern">
            </div>
            <div class="form-group">
                <label class="form-label">Passwort bestätigen</label>
                <input type="password" class="form-input" placeholder="Passwort wiederholen">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=administration/user_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Änderungen speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
