<?php /** System - E-Mail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>E-Mail-Einstellungen</h1>
        <p class="page-subtitle">E-Mail-Versand konfigurieren</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">send</span> Test-E-Mail</button>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>SMTP-Einstellungen</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">SMTP-Host</label>
                <input type="text" class="form-input" value="smtp.example.com">
            </div>
            <div class="form-group">
                <label class="form-label">Port</label>
                <input type="text" class="form-input" value="587">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Benutzername</label>
                <input type="text" class="form-input" value="noreply@example.com">
            </div>
            <div class="form-group">
                <label class="form-label">Passwort</label>
                <input type="password" class="form-input" value="••••••••">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Verschlüsselung</label>
            <select class="form-select"><option>TLS</option><option>SSL</option><option>Keine</option></select>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Absender</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Absender-Name</label>
                <input type="text" class="form-input" value="Mein Online Shop">
            </div>
            <div class="form-group">
                <label class="form-label">Absender-E-Mail</label>
                <input type="text" class="form-input" value="noreply@meinshop.de">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Antwort-an E-Mail</label>
                <input type="text" class="form-input" value="support@meinshop.de">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>E-Mail-Vorlagen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Vorlage</th><th>Betreff</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr><td>Bestellbestätigung</td><td>Ihre Bestellung #{order_id}</td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td></tr>
                <tr><td>Versandbenachrichtigung</td><td>Ihre Bestellung wurde versendet</td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td></tr>
                <tr><td>Passwort zurücksetzen</td><td>Passwort zurücksetzen</td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td></tr>
                <tr><td>Willkommens-E-Mail</td><td>Willkommen bei Mein Online Shop</td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td></tr>
            </tbody>
        </table>
    </div>
</div>
