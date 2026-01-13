<?php /** Marketing - Gutscheincodes */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Gutscheincodes</h1>
        <p class="page-subtitle">Rabattcodes erstellen und verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">upload</span> Import</button>
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Code erstellen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Aktive Codes</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Code</th><th>Rabatt</th><th>Verwendungen</th><th>Limit</th><th>Gültig bis</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><code style="background:var(--bg-tertiary);padding:4px 8px;border-radius:4px;">WINTER20</code></td>
                    <td>20%</td>
                    <td>234</td>
                    <td>Unbegrenzt</td>
                    <td>31.01.2026</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">content_copy</span></button></td>
                </tr>
                <tr>
                    <td><code style="background:var(--bg-tertiary);padding:4px 8px;border-radius:4px;">WELCOME10</code></td>
                    <td>10%</td>
                    <td>567</td>
                    <td>1x / Kunde</td>
                    <td>-</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">content_copy</span></button></td>
                </tr>
                <tr>
                    <td><code style="background:var(--bg-tertiary);padding:4px 8px;border-radius:4px;">FREESHIP</code></td>
                    <td>Versand €0</td>
                    <td>89</td>
                    <td>200</td>
                    <td>15.01.2026</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">content_copy</span></button></td>
                </tr>
                <tr>
                    <td><code style="background:var(--bg-tertiary);padding:4px 8px;border-radius:4px;">VIP50</code></td>
                    <td>€50</td>
                    <td>12</td>
                    <td>50</td>
                    <td>-</td>
                    <td><span class="badge badge-warning">Pausiert</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">content_copy</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Code Generator</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Code</label>
                <input type="text" class="form-input" placeholder="z.B. SPRING25">
            </div>
            <div class="form-group">
                <label class="form-label">Rabatttyp</label>
                <select class="form-select"><option>Prozent</option><option>Fester Betrag</option><option>Kostenloser Versand</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Wert</label>
                <input type="text" class="form-input" placeholder="z.B. 25">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Verwendungslimit</label>
                <input type="number" class="form-input" placeholder="Unbegrenzt">
            </div>
            <div class="form-group">
                <label class="form-label">Pro Kunde</label>
                <input type="number" class="form-input" value="1">
            </div>
            <div class="form-group">
                <label class="form-label">Mindestbestellwert</label>
                <input type="text" class="form-input" placeholder="€0">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn">Massenimport</button>
        <button class="btn btn-primary">Code erstellen</button>
    </div>
</div>
