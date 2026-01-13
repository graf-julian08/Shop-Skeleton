<?php /** Katalog - Bundles */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Bundles</h1>
        <p class="page-subtitle">Produktsets und Bundle-Angebote</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/bundle_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Bundle erstellen</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Alle Bundles</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Bundle</th><th>Produkte</th><th>Rabatt</th><th>Verkauft</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><a href="?page=catalog/bundle_detail&id=1"><strong>Starter Kit</strong></a><br><small style="color:var(--text-muted);">3 Produkte kombiniert</small></td>
                    <td>3</td>
                    <td><span class="badge badge-success">-15%</span></td>
                    <td>89</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=catalog/bundle_edit&id=1" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/bundle_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
                <tr>
                    <td><a href="?page=catalog/bundle_detail&id=2"><strong>Premium Collection</strong></a><br><small style="color:var(--text-muted);">5 Produkte kombiniert</small></td>
                    <td>5</td>
                    <td><span class="badge badge-success">-20%</span></td>
                    <td>45</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=catalog/bundle_edit&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/bundle_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
                <tr>
                    <td><a href="?page=catalog/bundle_detail&id=3"><strong>Herbst Outfit</strong></a><br><small style="color:var(--text-muted);">4 Produkte kombiniert</small></td>
                    <td>4</td>
                    <td><span class="badge badge-success">-10%</span></td>
                    <td>23</td>
                    <td><span class="badge badge-warning">Entwurf</span></td>
                    <td class="table-actions"><a href="?page=catalog/bundle_edit&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/bundle_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Schnelle Bundle-Erstellung</h3></div>
    <div class="card-body">
        <p style="color:var(--text-muted);margin-bottom:20px;">Für erweiterte Optionen nutzen Sie die <a href="?page=catalog/bundle_create">vollständige Bundle-Erstellung</a>.</p>
        <div class="form-group">
            <label class="form-label">Bundle-Name</label>
            <input type="text" class="form-input" placeholder="z.B. Starter Kit">
        </div>
        <div class="form-group">
            <label class="form-label">Produkte hinzufügen</label>
            <div style="border:2px dashed var(--border-color);border-radius:var(--radius-md);padding:24px;text-align:center;color:var(--text-muted);cursor:pointer;">
                <span class="material-symbols-rounded" style="font-size:32px;margin-bottom:8px;">add_shopping_cart</span>
                <p>Produkte hierher ziehen oder klicken zum Auswählen</p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Preistyp</label>
                <select class="form-select">
                    <option>Fester Preis</option>
                    <option selected>Prozentrabatt</option>
                    <option>Fester Rabatt</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Rabatt</label>
                <input type="text" class="form-input" value="15%">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=catalog/bundles" class="btn">Zurücksetzen</a>
        <button class="btn btn-primary">Schnell erstellen</button>
    </div>
</div>

<style>.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; }</style>
