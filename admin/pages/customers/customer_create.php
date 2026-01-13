<?php /** Kunden - Neuen Kunden erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=customers/customers">Kunden</a> <span>›</span> <span>Neuer Kunde</span></nav>
        <h1>Neuen Kunden erstellen</h1>
        <p class="page-subtitle">Manuell einen neuen Kunden anlegen</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=customers/customers" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Kunde erstellen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Persönliche Daten</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Vorname <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" placeholder="Vorname">
                </div>
                <div class="form-group">
                    <label class="form-label">Nachname <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" placeholder="Nachname">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">E-Mail <span style="color:var(--error)">*</span></label>
                <input type="email" class="form-input" placeholder="email@beispiel.de">
            </div>
            <div class="form-group">
                <label class="form-label">Telefon</label>
                <input type="text" class="form-input" placeholder="+49 170 1234567">
            </div>
            <div class="form-group">
                <label class="form-label">Geburtsdatum</label>
                <input type="date" class="form-input">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Kontoeinstellungen</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Kundengruppe</label>
                <select class="form-select">
                    <option selected>Standard</option>
                    <option>Premium</option>
                    <option>VIP</option>
                    <option>Großhandel</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option selected>Aktiv</option>
                    <option>Inaktiv</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Passwort</label>
                <input type="password" class="form-input" placeholder="Initiales Passwort">
            </div>
            <div class="form-group">
                <label class="form-label">Newsletter abonnieren</label>
                <div class="toggle"><input type="checkbox"><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">Willkommens-E-Mail senden</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Adresse (optional)</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Straße und Hausnummer</label>
            <input type="text" class="form-input" placeholder="Musterstraße 123">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">PLZ</label>
                <input type="text" class="form-input" placeholder="12345">
            </div>
            <div class="form-group">
                <label class="form-label">Stadt</label>
                <input type="text" class="form-input" placeholder="Musterstadt">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Land</label>
            <select class="form-select">
                <option selected>Deutschland</option>
                <option>Österreich</option>
                <option>Schweiz</option>
            </select>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=customers/customers" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Kunde erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; }
</style>
