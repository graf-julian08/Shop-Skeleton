<?php /** Commerce - Neue Preisregel erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/pricing">Preisregeln</a> <span>›</span> <span>Neue Regel</span></nav>
        <h1>Neue Preisregel erstellen</h1>
        <p class="page-subtitle">Erstellen Sie eine dynamische Preisregel</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/pricing" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Regel erstellen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Regelinformationen</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Regelname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. Mengenrabatt ab 10 Stück">
            </div>
            <div class="form-group">
                <label class="form-label">Regeltyp</label>
                <select class="form-select">
                    <option selected>Staffelpreis (Menge)</option>
                    <option>Zeitbasierter Preis</option>
                    <option>Kundengruppen-Preis</option>
                    <option>Dynamischer Preis</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Beschreibung</label>
                <textarea class="form-textarea" rows="2" placeholder="Interne Notizen..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option>Entwurf</option>
                    <option selected>Aktiv</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Gültigkeit</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Priorität</label>
                <input type="number" class="form-input" value="10">
                <small style="color:var(--text-muted);">Höhere Priorität = wird zuerst angewendet</small>
            </div>
            <div class="form-group">
                <label class="form-label">Gültig ab</label>
                <input type="datetime-local" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Gültig bis</label>
                <input type="datetime-local" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Mit anderen Regeln kombinierbar</label>
                <div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Preisanpassung</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Ab Menge</label>
                <input type="number" class="form-input" placeholder="10">
            </div>
            <div class="form-group">
                <label class="form-label">Anpassungstyp</label>
                <select class="form-select">
                    <option selected>Prozentrabatt</option>
                    <option>Fester Betrag</option>
                    <option>Neuer Preis</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Wert</label>
                <input type="number" class="form-input" placeholder="10" step="0.01">
            </div>
        </div>
        <button class="btn btn-sm"><span class="material-symbols-rounded">add</span> Weitere Staffel hinzufügen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Gilt für</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Produkte</label>
            <select class="form-select" multiple style="height:100px;">
                <option>Alle Produkte</option>
                <option>Bestimmte Kategorien</option>
                <option>Bestimmte Produkte</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Kundengruppen</label>
            <select class="form-select" multiple style="height:80px;">
                <option selected>Alle Kunden</option>
                <option>Standard</option>
                <option>VIP</option>
                <option>Großhandel</option>
            </select>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=commerce/pricing" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Preisregel erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; }
</style>
