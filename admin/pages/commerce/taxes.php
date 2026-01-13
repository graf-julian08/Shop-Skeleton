<?php /** Commerce - Steuern */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Steuern</h1>
        <p class="page-subtitle">Steuerklassen und -sätze verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="klassen">Steuerklassen</button>
    <button class="tab" data-tab="zonen">Steuerzonen</button>
    <button class="tab" data-tab="einstellungen">Einstellungen</button>
</div>

<!-- Tab: Steuerklassen -->
<div data-tab-content="klassen">
    <div class="card">
        <div class="card-header"><h3>Steuerklassen</h3><button class="btn btn-sm"><span class="material-symbols-rounded">add</span> Klasse hinzufügen</button></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Steuerklasse</th><th>DE</th><th>AT</th><th>CH</th><th>Produkte</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>Standard</strong></td>
                        <td>19%</td>
                        <td>20%</td>
                        <td>8,1%</td>
                        <td>145</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Ermäßigt</strong></td>
                        <td>7%</td>
                        <td>10%</td>
                        <td>2,6%</td>
                        <td>23</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Steuerfrei</strong></td>
                        <td>0%</td>
                        <td>0%</td>
                        <td>0%</td>
                        <td>8</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Steuerzonen -->
<div data-tab-content="zonen" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Steuerzonen</h3><button class="btn btn-sm"><span class="material-symbols-rounded">add</span> Zone hinzufügen</button></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Zone</th><th>Länder</th><th>Standard-Satz</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>Deutschland</strong></td>
                        <td>DE</td>
                        <td>19%</td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Österreich</strong></td>
                        <td>AT</td>
                        <td>20%</td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Schweiz</strong></td>
                        <td>CH</td>
                        <td>8,1%</td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>EU (Restliche)</strong></td>
                        <td>FR, IT, ES, NL, BE, ...</td>
                        <td>20%</td>
                        <td><span class="badge badge-warning">Teilweise</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Einstellungen -->
<div data-tab-content="einstellungen" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Steuereinstellungen</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Steuerberechnung basiert auf</label>
                    <select class="form-select"><option selected>Lieferadresse</option><option>Rechnungsadresse</option><option>Shop-Standort</option></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Standard-Steuerland</label>
                    <select class="form-select"><option selected>Deutschland</option><option>Österreich</option><option>Schweiz</option></select>
                </div>
            </div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Preise inkl. Steuern eingeben</span></label></div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Steuern im Warenkorb anzeigen</span></label></div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>B2B-Modus: Nettopreise für Geschäftskunden</span></label></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>USt-ID Verifizierung</h3></div>
        <div class="card-body">
            <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>USt-ID bei Checkout abfragen</span></label></div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Automatische VIES-Validierung</span></label></div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>Steuerbefreiung für gültige EU USt-IDs</span></label></div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
        </div>
    </div>
</div>

<style>.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; }</style>
