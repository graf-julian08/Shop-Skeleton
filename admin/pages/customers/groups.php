<?php /** Kunden - Gruppen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kundengruppen</h1>
        <p class="page-subtitle">Gruppen und Preisregeln verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=customers/group_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Gruppe erstellen</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Alle Gruppen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Gruppe</th><th>Kunden</th><th>Preisregel</th><th>Beschreibung</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><a href="?page=customers/group_detail&id=1"><strong>Standard</strong></a></td>
                    <td>892</td>
                    <td>Standardpreis</td>
                    <td>Standardgruppe für alle neuen Kunden</td>
                    <td class="table-actions"><a href="?page=customers/group_edit&id=1" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=customers/group_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
                <tr>
                    <td><a href="?page=customers/group_detail&id=2"><strong>VIP</strong></a></td>
                    <td>156</td>
                    <td><span class="badge badge-success">-10% auf alles</span></td>
                    <td>Treue Kunden mit über €1.000 Umsatz</td>
                    <td class="table-actions"><a href="?page=customers/group_edit&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=customers/group_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
                <tr>
                    <td><a href="?page=customers/group_detail&id=3"><strong>Großhandel</strong></a></td>
                    <td>45</td>
                    <td><span class="badge badge-success">-20% auf alles</span></td>
                    <td>B2B-Kunden und Wiederverkäufer</td>
                    <td class="table-actions"><a href="?page=customers/group_edit&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=customers/group_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
                <tr>
                    <td><a href="?page=customers/group_detail&id=4"><strong>Mitarbeiter</strong></a></td>
                    <td>12</td>
                    <td><span class="badge badge-success">-30% auf alles</span></td>
                    <td>Interne Mitarbeiter</td>
                    <td class="table-actions"><a href="?page=customers/group_edit&id=4" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=customers/group_detail&id=4" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
