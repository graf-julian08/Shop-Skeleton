<?php /** System - Einstellungen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Systemeinstellungen</h1>
        <p class="page-subtitle">Allgemeine Systemkonfiguration</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Admin-Panel</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Admin-Sprache</label>
                <select class="form-select"><option selected>Deutsch</option><option>English</option><option>Français</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Zeitzone</label>
                <select class="form-select"><option selected>Europe/Berlin</option><option>Europe/London</option><option>America/New_York</option></select>
            </div>
        </div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Dark Mode aktivieren</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Sidebar eingeklappt merken</span></label></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Performance</h3></div>
    <div class="card-body">
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Caching aktivieren</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Asset-Minifizierung</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>Debug-Modus</span></label></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Wartungsmodus</h3></div>
    <div class="card-body">
        <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>Wartungsmodus aktivieren</span></label></div>
        <div class="form-group">
            <label class="form-label">Wartungsnachricht</label>
            <textarea class="form-textarea">Wir führen gerade Wartungsarbeiten durch. Bitte versuchen Sie es später erneut.</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Erlaubte IPs (eine pro Zeile)</label>
            <textarea class="form-textarea" placeholder="192.168.1.1"></textarea>
        </div>
    </div>
</div>
