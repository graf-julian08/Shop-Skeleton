<?php /** Commerce - Preise */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Preise & Margen</h1>
        <p class="page-subtitle">Preisstrategien und Regeln</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/pricing_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Preisregel erstellen</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Preisregeln</h3><a href="?page=commerce/pricing_create" class="btn btn-sm"><span class="material-symbols-rounded">add</span> Neue Regel</a></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Regel</th><th>Anwendung</th><th>Anpassung</th><th>Priorität</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><a href="?page=commerce/pricing_detail&id=1"><strong>VIP-Rabatt</strong></a></td>
                    <td>Kundengruppe: VIP</td>
                    <td><span class="badge badge-success">-10%</span></td>
                    <td>1</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td class="table-actions"><a href="?page=commerce/pricing_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=commerce/pricing_detail&id=2"><strong>Großhandel</strong></a></td>
                    <td>Kundengruppe: B2B</td>
                    <td><span class="badge badge-success">-20%</span></td>
                    <td>2</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td class="table-actions"><a href="?page=commerce/pricing_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=commerce/pricing_detail&id=3"><strong>Mengenrabatt 5+</strong></a></td>
                    <td>Ab 5 Artikel</td>
                    <td><span class="badge badge-success">-5%</span></td>
                    <td>3</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td class="table-actions"><a href="?page=commerce/pricing_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=commerce/pricing_detail&id=4"><strong>Mengenrabatt 10+</strong></a></td>
                    <td>Ab 10 Artikel</td>
                    <td><span class="badge badge-success">-10%</span></td>
                    <td>4</td>
                    <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                    <td class="table-actions"><a href="?page=commerce/pricing_detail&id=4" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Preisanzeige</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Preise anzeigen</label>
                <select class="form-select"><option selected>Inkl. MwSt.</option><option>Exkl. MwSt.</option><option>Beide</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Dezimalstellen</label>
                <select class="form-select"><option selected>2</option><option>0</option><option>3</option></select>
            </div>
        </div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Streichpreise anzeigen</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Prozentuale Ersparnis anzeigen</span></label></div>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Einstellungen speichern</button>
    </div>
</div>

<style>.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; }</style>
