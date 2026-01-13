<?php /** Commerce - Digitale Lieferung */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Digitale Lieferung</h1>
        <p class="page-subtitle">Downloads und Lizenzschlüssel verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="downloads">Downloads</button>
    <button class="tab" data-tab="lizenzen">Lizenzschlüssel</button>
    <button class="tab" data-tab="einstellungen">Einstellungen</button>
</div>

<!-- Tab: Downloads -->
<div data-tab-content="downloads">
    <div class="card">
        <div class="card-header"><h3>Digitale Produkte</h3></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Produkt</th><th>Dateien</th><th>Downloads</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>E-Book: Marketing Guide</strong></td>
                        <td>PDF, EPUB (2 Dateien)</td>
                        <td>456</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">cloud_upload</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Software License Pro</strong></td>
                        <td>ZIP (1 Datei)</td>
                        <td>89</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">cloud_upload</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Template Pack</strong></td>
                        <td>ZIP (1 Datei)</td>
                        <td>234</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">cloud_upload</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Lizenzschlüssel -->
<div data-tab-content="lizenzen" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Lizenzschlüssel-Pools</h3><button class="btn btn-sm"><span class="material-symbols-rounded">add</span> Keys importieren</button></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Produkt</th><th>Verfügbare Keys</th><th>Verteilt</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>Software License Pro</strong></td>
                        <td><span class="badge badge-success">45 verfügbar</span></td>
                        <td>89</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">add</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Plugin Activation</strong></td>
                        <td><span class="badge badge-warning">5 verfügbar</span></td>
                        <td>45</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">add</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Schlüssel hinzufügen</h3></div>
        <div class="card-body">
            <div class="form-group"><label class="form-label">Produkt</label><select class="form-select"><option>Software License Pro</option><option>Plugin Activation</option></select></div>
            <div class="form-group"><label class="form-label">Lizenzschlüssel (einer pro Zeile)</label><textarea class="form-textarea" rows="4" placeholder="XXXX-XXXX-XXXX-XXXX"></textarea></div>
        </div>
        <div class="card-footer"><button class="btn btn-primary">Keys hinzufügen</button></div>
    </div>
</div>

<!-- Tab: Einstellungen -->
<div data-tab-content="einstellungen" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Download-Einstellungen</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group"><label class="form-label">Download-Limit pro Kauf</label><input type="number" class="form-input" value="5"></div>
                <div class="form-group"><label class="form-label">Link-Gültigkeit (Tage)</label><input type="number" class="form-input" value="30"></div>
            </div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Sofortiger Download nach Bezahlung</span></label></div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Download-Link per E-Mail senden</span></label></div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>IP-Adresse bei Download protokollieren</span></label></div>
        </div>
        <div class="card-footer"><button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button></div>
    </div>
</div>

<style>.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; }</style>
