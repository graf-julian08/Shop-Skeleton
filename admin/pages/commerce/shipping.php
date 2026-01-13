<?php /** Commerce - Versand */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Versand</h1>
        <p class="page-subtitle">Versandarten und Preise konfigurieren</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/shipping_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Versandart hinzufügen</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Versandarten</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Versandart</th><th>Anbieter</th><th>Preis</th><th>Lieferzeit</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><a href="?page=commerce/shipping_detail&id=1"><strong>Standardversand</strong></a></td>
                    <td>DHL</td>
                    <td>€4,99</td>
                    <td>3-5 Werktage</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td class="table-actions"><a href="?page=commerce/shipping_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=commerce/shipping_detail&id=2"><strong>Expressversand</strong></a></td>
                    <td>DHL Express</td>
                    <td>€9,99</td>
                    <td>1-2 Werktage</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td class="table-actions"><a href="?page=commerce/shipping_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=commerce/shipping_detail&id=3"><strong>Same-Day Delivery</strong></a></td>
                    <td>Lokaler Kurier</td>
                    <td>€19,99</td>
                    <td>Heute</td>
                    <td><label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label></td>
                    <td class="table-actions"><a href="?page=commerce/shipping_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=commerce/shipping_detail&id=4"><strong>Abholung</strong></a></td>
                    <td>-</td>
                    <td>€0,00</td>
                    <td>Sofort</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td class="table-actions"><a href="?page=commerce/shipping_detail&id=4" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Versandzonen</h3><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">add</span> Zone hinzufügen</button></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Zone</th><th>Länder</th><th>Standard</th><th>Express</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Deutschland</strong></td>
                    <td>DE</td>
                    <td>€4,99</td>
                    <td>€9,99</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><strong>EU</strong></td>
                    <td>AT, FR, IT, NL, BE, ...</td>
                    <td>€9,99</td>
                    <td>€19,99</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><strong>Schweiz</strong></td>
                    <td>CH, LI</td>
                    <td>€14,99</td>
                    <td>€24,99</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Kostenloser Versand</h3></div>
    <div class="card-body">
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Kostenloser Versand aktivieren</span></label></div>
        <div class="form-group">
            <label class="form-label">Ab Bestellwert</label>
            <input type="text" class="form-input" value="75" style="width:150px;"> €
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary">Einstellungen speichern</button>
    </div>
</div>
