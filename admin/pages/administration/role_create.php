<?php /** Administration - Neue Rolle erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=administration/roles">Rollen</a> <span>›</span> <span>Neue Rolle</span></nav>
        <h1>Neue Rolle erstellen</h1>
        <p class="page-subtitle">Definieren Sie eine neue Benutzerrolle mit spezifischen Berechtigungen</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=administration/roles" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Rolle erstellen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Rolleninformationen</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Rollenname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. Content-Manager">
            </div>
            <div class="form-group">
                <label class="form-label">Rollen-Code</label>
                <input type="text" class="form-input" placeholder="z.B. content_manager">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Beschreibung</label>
            <textarea class="form-textarea" rows="2" placeholder="Beschreibung der Rolle..."></textarea>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Berechtigungen</h3><button class="btn btn-sm">Alle auswählen</button></div>
    <div class="card-body">
        <table class="table permission-table">
            <thead><tr><th>Bereich</th><th>Lesen</th><th>Erstellen</th><th>Bearbeiten</th><th>Löschen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Dashboard</strong></td>
                    <td><input type="checkbox" checked></td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td><strong>Katalog</strong></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td><strong>Bestellungen</strong></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td><strong>Kunden</strong></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td><strong>Marketing</strong></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td><strong>Reports</strong></td>
                    <td><input type="checkbox"></td>
                    <td>—</td>
                    <td>—</td>
                    <td>—</td>
                </tr>
                <tr>
                    <td><strong>Finanzen</strong></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td><strong>System</strong></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td><strong>Administration</strong></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <a href="?page=administration/roles" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Rolle erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; }
.permission-table td { text-align:center; }
.permission-table td:first-child { text-align:left; }
</style>
