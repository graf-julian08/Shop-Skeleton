<?php /** System - Backups */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Backups</h1>
        <p class="page-subtitle">Datensicherungen verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">backup</span> Backup erstellen</button>
    </div>
</div>

<div class="alert alert-success">
    <span class="material-symbols-rounded">check_circle</span>
    <div class="alert-content"><strong>Automatische Backups aktiv</strong><br>Tägliche Sicherung um 03:00 Uhr</div>
</div>

<div class="card">
    <div class="card-header"><h3>Backup-Einstellungen</h3></div>
    <div class="card-body">
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Automatische Backups aktivieren</span></label></div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Häufigkeit</label>
                <select class="form-select"><option selected>Täglich</option><option>Wöchentlich</option><option>Monatlich</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Aufbewahrung</label>
                <select class="form-select"><option>7 Tage</option><option selected>30 Tage</option><option>90 Tage</option></select>
            </div>
        </div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Datenbank sichern</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Dateien sichern</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>Backup an externen Speicher senden</span></label></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Vorhandene Backups</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Backup</th><th>Datum</th><th>Größe</th><th>Typ</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>backup_2026-01-07.zip</strong></td>
                    <td>07.01.2026 03:00</td>
                    <td>245 MB</td>
                    <td><span class="badge badge-default">Automatisch</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">restore</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><strong>backup_2026-01-06.zip</strong></td>
                    <td>06.01.2026 03:00</td>
                    <td>244 MB</td>
                    <td><span class="badge badge-default">Automatisch</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">restore</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><strong>backup_manual_2026-01-05.zip</strong></td>
                    <td>05.01.2026 14:30</td>
                    <td>243 MB</td>
                    <td><span class="badge badge-info">Manuell</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">restore</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
