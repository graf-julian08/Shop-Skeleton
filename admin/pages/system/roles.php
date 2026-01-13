<?php /** System - Rollen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Admin-Rollen</h1>
        <p class="page-subtitle">Rollen und Berechtigungen verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Rolle erstellen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Rollen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Rolle</th><th>Beschreibung</th><th>Benutzer</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Administrator</strong></td>
                    <td>Voller Zugriff auf alle Funktionen</td>
                    <td>1</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
                <tr>
                    <td><strong>Shop Manager</strong></td>
                    <td>Produkte, Bestellungen, Kunden verwalten</td>
                    <td>2</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><strong>Content Editor</strong></td>
                    <td>CMS, Design, Marketing-Inhalte</td>
                    <td>1</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><strong>Support</strong></td>
                    <td>Kundenanfragen, Bestellstatus</td>
                    <td>1</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Berechtigungen: Shop Manager</h3></div>
    <div class="card-body">
        <div class="permission-grid">
            <table class="permission-table">
                <thead><tr><th>Bereich</th><th>Lesen</th><th>Erstellen</th><th>Bearbeiten</th><th>Löschen</th></tr></thead>
                <tbody>
                    <tr><td>Dashboard</td><td><input type="checkbox" checked></td><td><input type="checkbox" disabled></td><td><input type="checkbox" disabled></td><td><input type="checkbox" disabled></td></tr>
                    <tr><td>Produkte</td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td></tr>
                    <tr><td>Bestellungen</td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td></tr>
                    <tr><td>Kunden</td><td><input type="checkbox" checked></td><td><input type="checkbox"></td><td><input type="checkbox" checked></td><td><input type="checkbox"></td></tr>
                    <tr><td>Finanzen</td><td><input type="checkbox" checked></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                    <tr><td>Einstellungen</td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td><td><input type="checkbox"></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn">Abbrechen</button>
        <button class="btn btn-primary">Speichern</button>
    </div>
</div>
