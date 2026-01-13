<?php /** Administration - Rollen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Admin-Rollen</h1>
        <p class="page-subtitle">Benutzerrollen und Berechtigungsgruppen definieren</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=administration/role_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Rolle erstellen</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Rolle</th><th>Beschreibung</th><th>Benutzer</th><th>Berechtigungen</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><a href="?page=administration/role_detail&id=1"><strong>Super Admin</strong></a></td>
                    <td>Vollzugriff auf alle Funktionen</td>
                    <td><span class="badge badge-default">1 Benutzer</span></td>
                    <td><span class="badge badge-info">Alle (52)</span></td>
                    <td class="table-actions"><a href="?page=administration/role_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
                <tr>
                    <td><a href="?page=administration/role_detail&id=2"><strong>Manager</strong></a></td>
                    <td>Verwaltung von Produkten, Bestellungen, Kunden</td>
                    <td><span class="badge badge-default">3 Benutzer</span></td>
                    <td><span class="badge badge-warning">Eingeschränkt (28)</span></td>
                    <td class="table-actions"><a href="?page=administration/role_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=administration/role_detail&id=3"><strong>Editor</strong></a></td>
                    <td>Bearbeitung von Inhalten und CMS</td>
                    <td><span class="badge badge-default">5 Benutzer</span></td>
                    <td><span class="badge badge-warning">Eingeschränkt (12)</span></td>
                    <td class="table-actions"><a href="?page=administration/role_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=administration/role_detail&id=4"><strong>Support</strong></a></td>
                    <td>Kundenservice und Bestellbearbeitung</td>
                    <td><span class="badge badge-default">2 Benutzer</span></td>
                    <td><span class="badge badge-warning">Eingeschränkt (8)</span></td>
                    <td class="table-actions"><a href="?page=administration/role_detail&id=4" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
