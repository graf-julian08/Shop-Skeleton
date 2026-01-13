<?php /** Kunden - Rollen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Rollen & Rechte</h1>
        <p class="page-subtitle">Kundenrollen und Berechtigungen verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Rolle erstellen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Kundenrollen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Rolle</th><th>Benutzer</th><th>Berechtigungen</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Kunde</strong></td>
                    <td>1.127</td>
                    <td>Bestellen, Profil verwalten, Bestellhistorie</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><strong>B2B-Kunde</strong></td>
                    <td>45</td>
                    <td>+ Mengenrabatte, Rechnungskauf, Sub-Accounts</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
                <tr>
                    <td><strong>Abonnent</strong></td>
                    <td>112</td>
                    <td>+ Abo verwalten, Pausieren, Kündigen</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Berechtigungsmatrix</h3></div>
    <div class="card-body">
        <div class="permission-grid">
            <table class="permission-table">
                <thead>
                    <tr><th>Berechtigung</th><th>Kunde</th><th>B2B</th><th>Abonnent</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Produkte kaufen</td>
                        <td><input type="checkbox" checked disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                    </tr>
                    <tr>
                        <td>Profil bearbeiten</td>
                        <td><input type="checkbox" checked disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                    </tr>
                    <tr>
                        <td>Bestellhistorie</td>
                        <td><input type="checkbox" checked disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                    </tr>
                    <tr>
                        <td>Rechnungskauf</td>
                        <td><input type="checkbox" disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                        <td><input type="checkbox" disabled></td>
                    </tr>
                    <tr>
                        <td>Mengenrabatte</td>
                        <td><input type="checkbox" disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                        <td><input type="checkbox" disabled></td>
                    </tr>
                    <tr>
                        <td>Sub-Accounts</td>
                        <td><input type="checkbox" disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                        <td><input type="checkbox" disabled></td>
                    </tr>
                    <tr>
                        <td>Abo verwalten</td>
                        <td><input type="checkbox" disabled></td>
                        <td><input type="checkbox" disabled></td>
                        <td><input type="checkbox" checked disabled></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
