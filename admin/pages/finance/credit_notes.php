<?php /** Finanzen - Gutschriften */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Gutschriften</h1>
        <p class="page-subtitle">Gutschriften und Erstattungen</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Gutschrift erstellen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Alle Gutschriften</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Gutschrift</th><th>Rechnung</th><th>Kunde</th><th>Datum</th><th>Betrag</th><th>Grund</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>CR-2026-0012</strong></td>
                    <td>INV-2026-0142</td>
                    <td>Lisa Müller</td>
                    <td>06.01.2026</td>
                    <td>-€67,80</td>
                    <td>Stornierung</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button></td>
                </tr>
                <tr>
                    <td><strong>CR-2026-0011</strong></td>
                    <td>INV-2026-0138</td>
                    <td>Thomas Koch</td>
                    <td>05.01.2026</td>
                    <td>-€129,00</td>
                    <td>Retoure</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button></td>
                </tr>
                <tr>
                    <td><strong>CR-2026-0010</strong></td>
                    <td>INV-2026-0135</td>
                    <td>Sarah Wagner</td>
                    <td>04.01.2026</td>
                    <td>-€45,50</td>
                    <td>Teilretoure</td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Gutschrift erstellen</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Bezugsrechnung</label>
                <select class="form-select"><option>Rechnung auswählen...</option><option>INV-2026-0156</option><option>INV-2026-0155</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Betrag</label>
                <input type="text" class="form-input" placeholder="€0,00">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Grund</label>
            <select class="form-select"><option>Retoure</option><option>Stornierung</option><option>Preisanpassung</option><option>Kulanz</option></select>
        </div>
        <div class="form-group">
            <label class="form-label">Bemerkung</label>
            <textarea class="form-textarea" placeholder="Interner Kommentar..."></textarea>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn">Abbrechen</button>
        <button class="btn btn-primary">Gutschrift erstellen</button>
    </div>
</div>
