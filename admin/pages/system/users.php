<?php /** System - Benutzer */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Benutzer</h1>
        <p class="page-subtitle">Admin-Benutzer verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">person_add</span> Benutzer einladen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Alle Benutzer</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Benutzer</th><th>E-Mail</th><th>Rolle</th><th>Letzter Login</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Julian Graf</strong></td>
                    <td>julian@example.com</td>
                    <td><span class="badge badge-info">Administrator</span></td>
                    <td>Gerade online</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><strong>Anna Schmidt</strong></td>
                    <td>anna@example.com</td>
                    <td><span class="badge badge-default">Shop Manager</span></td>
                    <td>Vor 2 Stunden</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><strong>Max Müller</strong></td>
                    <td>max@example.com</td>
                    <td><span class="badge badge-default">Content Editor</span></td>
                    <td>Gestern</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><strong>Lisa Weber</strong></td>
                    <td>lisa@example.com</td>
                    <td><span class="badge badge-default">Support</span></td>
                    <td>Vor 5 Tagen</td>
                    <td><span class="badge badge-default">Inaktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Benutzer einladen</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">E-Mail</label>
                <input type="email" class="form-input" placeholder="name@example.com">
            </div>
            <div class="form-group">
                <label class="form-label">Rolle</label>
                <select class="form-select"><option>Shop Manager</option><option>Content Editor</option><option>Support</option><option>Administrator</option></select>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary">Einladung senden</button>
    </div>
</div>
