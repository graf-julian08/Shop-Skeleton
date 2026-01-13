<?php /** Administration - Benutzer */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Admin-Benutzer</h1>
        <p class="page-subtitle">Benutzer des Admin-Panels verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=administration/user_create" class="btn btn-primary"><span class="material-symbols-rounded">person_add</span> Benutzer hinzufügen</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="filters">
            <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Benutzer suchen..."></div>
            <select class="filter-select"><option>Alle Rollen</option><option>Super Admin</option><option>Manager</option><option>Editor</option></select>
            <select class="filter-select"><option>Alle Status</option><option>Aktiv</option><option>Inaktiv</option></select>
        </div>
        <table class="table">
            <thead><tr><th><input type="checkbox" class="select-all"></th><th>Benutzer</th><th>E-Mail</th><th>Rolle</th><th>Letzter Login</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><div style="display:flex;align-items:center;gap:12px;"><div class="user-avatar">AD</div><div><a href="?page=administration/user_detail&id=1"><strong>Administrator</strong></a><br><small style="color:var(--text-muted);">Erstellt: 01.01.2026</small></div></div></td>
                    <td>admin@meinshop.de</td>
                    <td><a href="?page=administration/role_detail&id=1"><span class="badge badge-info">Super Admin</span></a></td>
                    <td>Heute, 14:32</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=administration/user_edit&id=1" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=administration/user_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><div style="display:flex;align-items:center;gap:12px;"><div class="user-avatar" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">MK</div><div><a href="?page=administration/user_detail&id=2"><strong>Max König</strong></a><br><small style="color:var(--text-muted);">Erstellt: 15.03.2026</small></div></div></td>
                    <td>max.koenig@meinshop.de</td>
                    <td><a href="?page=administration/role_detail&id=2"><span class="badge badge-warning">Manager</span></a></td>
                    <td>Gestern, 09:15</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=administration/user_edit&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=administration/user_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><div style="display:flex;align-items:center;gap:12px;"><div class="user-avatar" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">LM</div><div><a href="?page=administration/user_detail&id=3"><strong>Lisa Müller</strong></a><br><small style="color:var(--text-muted);">Erstellt: 20.05.2026</small></div></div></td>
                    <td>lisa.mueller@meinshop.de</td>
                    <td><a href="?page=administration/role_detail&id=3"><span class="badge badge-default">Editor</span></a></td>
                    <td>03.01.2026</td>
                    <td><span class="badge badge-error">Inaktiv</span></td>
                    <td class="table-actions"><a href="?page=administration/user_edit&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=administration/user_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
