<?php /** Administration - Neuen Admin-Benutzer erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=administration/users">Admin-Benutzer</a> <span>›</span> <span>Neuer Benutzer</span></nav>
        <h1>Neuen Admin-Benutzer erstellen</h1>
        <p class="page-subtitle">Fügen Sie einen neuen Administrator hinzu</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=administration/users" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Benutzer erstellen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Persönliche Daten</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Vorname <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" placeholder="Vorname">
                </div>
                <div class="form-group">
                    <label class="form-label">Nachname <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" placeholder="Nachname">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">E-Mail <span style="color:var(--error)">*</span></label>
                <input type="email" class="form-input" placeholder="admin@meinshop.de">
            </div>
            <div class="form-group">
                <label class="form-label">Benutzername</label>
                <input type="text" class="form-input" placeholder="benutzername">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Zugangsdaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Passwort <span style="color:var(--error)">*</span></label>
                <input type="password" class="form-input" placeholder="Mindestens 8 Zeichen">
            </div>
            <div class="form-group">
                <label class="form-label">Passwort bestätigen <span style="color:var(--error)">*</span></label>
                <input type="password" class="form-input" placeholder="Passwort wiederholen">
            </div>
            <div class="form-group">
                <label class="form-label">Rolle <span style="color:var(--error)">*</span></label>
                <select class="form-select">
                    <option>Rolle wählen...</option>
                    <option>Administrator</option>
                    <option>Manager</option>
                    <option>Editor</option>
                    <option>Support</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option selected>Aktiv</option>
                    <option>Inaktiv</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Sicherheit</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">2-Faktor-Authentifizierung erzwingen</label>
            <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
        </div>
        <div class="form-group">
            <label class="form-label">Willkommens-E-Mail mit Zugangsdaten senden</label>
            <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=administration/users" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Benutzer erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; }
</style>
