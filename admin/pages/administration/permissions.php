<?php /** Administration - Berechtigungen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Berechtigungen</h1>
        <p class="page-subtitle">Granulare Zugriffsrechte für Rollen konfigurieren</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Rolle: Manager</h3>
        <select class="form-select" style="width:200px;">
            <option>Manager</option>
            <option>Editor</option>
            <option>Support</option>
        </select>
    </div>
    <div class="card-body">
        <div class="permission-grid">
            <table class="permission-table">
                <thead>
                    <tr><th>Modul</th><th>Anzeigen</th><th>Erstellen</th><th>Bearbeiten</th><th>Löschen</th></tr>
                </thead>
                <tbody>
                    <tr><td><strong>Produkte</strong></td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td></tr>
                    <tr><td><strong>Kategorien</strong></td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td></tr>
                    <tr><td><strong>Bestellungen</strong></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td></tr>
                    <tr><td><strong>Kunden</strong></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td></tr>
                    <tr><td><strong>Marketing</strong></td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td></tr>
                    <tr><td><strong>Reports</strong></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                    <tr><td><strong>System</strong></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
