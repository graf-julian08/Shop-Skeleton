<?php /** Finanzen - Buchhaltung */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Buchhaltung</h1>
        <p class="page-subtitle">Buchhaltungsexporte und Integrationen</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>DATEV-Export</h3></div>
    <div class="card-body">
        <p style="color:var(--text-secondary);margin-bottom:20px;">Exportieren Sie Buchungsdaten im DATEV-Format für Ihren Steuerberater.</p>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Zeitraum</label>
                <select class="form-select"><option>Januar 2026</option><option>Dezember 2025</option><option>Q4 2025</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Format</label>
                <select class="form-select"><option>DATEV CSV</option><option>DATEV XML</option></select>
            </div>
        </div>
        <button class="btn btn-primary"><span class="material-symbols-rounded">download</span> Export erstellen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Buchhaltungssoftware</h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px;">
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;text-align:center;">
                <strong style="display:block;margin-bottom:12px;">Lexoffice</strong>
                <span class="badge badge-success">Verbunden</span>
                <button class="btn btn-sm" style="margin-top:12px;"><span class="material-symbols-rounded">settings</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;text-align:center;">
                <strong style="display:block;margin-bottom:12px;">sevDesk</strong>
                <span class="badge badge-default">Nicht verbunden</span>
                <button class="btn btn-sm" style="margin-top:12px;"><span class="material-symbols-rounded">add</span> Verbinden</button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;text-align:center;">
                <strong style="display:block;margin-bottom:12px;">DATEV</strong>
                <span class="badge badge-default">Nicht verbunden</span>
                <button class="btn btn-sm" style="margin-top:12px;"><span class="material-symbols-rounded">add</span> Verbinden</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Kontenrahmen</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Erlöskonto (19%)</label>
            <input type="text" class="form-input" value="8400">
        </div>
        <div class="form-group">
            <label class="form-label">Erlöskonto (7%)</label>
            <input type="text" class="form-input" value="8300">
        </div>
        <div class="form-group">
            <label class="form-label">Versanderlöse</label>
            <input type="text" class="form-input" value="8120">
        </div>
    </div>
</div>
