<?php /** Commerce - Rabatte */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Rabatte</h1>
        <p class="page-subtitle">Rabattregeln und automatische Rabatte</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/discount_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Rabatt erstellen</a>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="automatisch">Automatische Rabatte</button>
    <button class="tab" data-tab="gutscheine">Gutscheincodes</button>
</div>

<!-- Tab: Automatische Rabatte -->
<div data-tab-content="automatisch">
    <div class="card">
        <div class="card-header"><h3>Aktive automatische Rabatte</h3></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Rabatt</th><th>Typ</th><th>Wert</th><th>Bedingungen</th><th>Gültig bis</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><a href="?page=commerce/discount_detail&id=1"><strong>Winter Sale</strong></a></td>
                        <td>Automatisch</td>
                        <td><span class="badge badge-success">-20%</span></td>
                        <td>Kategorie: Winter</td>
                        <td>31.01.2026</td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td class="table-actions"><a href="?page=commerce/discount_edit&id=1" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><a href="?page=commerce/discount_detail&id=2"><strong>3 für 2</strong></a></td>
                        <td>Automatisch</td>
                        <td>Günstigstes gratis</td>
                        <td>Mind. 3 Artikel</td>
                        <td>-</td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td class="table-actions"><a href="?page=commerce/discount_edit&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><a href="?page=commerce/discount_detail&id=3"><strong>Kostenloser Versand</strong></a></td>
                        <td>Automatisch</td>
                        <td>Versand €0</td>
                        <td>Ab €75 Bestellwert</td>
                        <td>-</td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td class="table-actions"><a href="?page=commerce/discount_edit&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Gutscheincodes -->
<div data-tab-content="gutscheine" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Gutscheincodes</h3><a href="?page=commerce/discount_create" class="btn btn-sm"><span class="material-symbols-rounded">add</span> Neuer Code</a></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Code</th><th>Rabatt</th><th>Verwendet</th><th>Limit</th><th>Gültig bis</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><code><strong>NEUKUNDE10</strong></code></td>
                        <td><span class="badge badge-success">-10%</span></td>
                        <td>234</td>
                        <td>Unbegrenzt</td>
                        <td>-</td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">content_copy</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                    </tr>
                    <tr>
                        <td><code><strong>WINTER25</strong></code></td>
                        <td><span class="badge badge-success">-€25</span></td>
                        <td>89</td>
                        <td>100</td>
                        <td>31.01.2026</td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">content_copy</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                    </tr>
                    <tr>
                        <td><code><strong>VIP2025</strong></code></td>
                        <td><span class="badge badge-success">-15%</span></td>
                        <td>56</td>
                        <td>50</td>
                        <td>31.12.2025</td>
                        <td><span class="badge badge-error">Abgelaufen</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">content_copy</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                    </tr>
                    <tr>
                        <td><code><strong>FRUEHJAHR</strong></code></td>
                        <td>Kostenloser Versand</td>
                        <td>0</td>
                        <td>200</td>
                        <td>01.04.2026</td>
                        <td><span class="badge badge-warning">Geplant</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">content_copy</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Code generieren</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group"><label class="form-label">Anzahl Codes</label><input type="number" class="form-input" value="10"></div>
                <div class="form-group"><label class="form-label">Präfix</label><input type="text" class="form-input" placeholder="z.B. SPRING"></div>
                <div class="form-group"><label class="form-label">Rabatt</label><select class="form-select"><option>10%</option><option>15%</option><option>20%</option><option>€10</option><option>€25</option></select></div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary"><span class="material-symbols-rounded">auto_awesome</span> Codes generieren</button>
        </div>
    </div>
</div>

<style>.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; }</style>
