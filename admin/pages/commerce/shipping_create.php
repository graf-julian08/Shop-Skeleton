<?php /** Commerce - Neue Versandmethode erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/shipping">Versand</a> <span>›</span> <span>Neue Versandmethode</span></nav>
        <h1>Neue Versandmethode erstellen</h1>
        <p class="page-subtitle">Fügen Sie eine neue Versandoption hinzu</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/shipping" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Grundeinstellungen</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Name der Versandmethode <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. DHL Express">
            </div>
            <div class="form-group">
                <label class="form-label">Versanddienstleister</label>
                <select class="form-select">
                    <option>Keiner (Eigener Versand)</option>
                    <option selected>DHL</option>
                    <option>UPS</option>
                    <option>DPD</option>
                    <option>Hermes</option>
                    <option>GLS</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Beschreibung</label>
                <textarea class="form-textarea" rows="2" placeholder="Beschreibung für Kunden..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Geschätzte Lieferzeit</label>
                <input type="text" class="form-input" placeholder="z.B. 1-2 Werktage">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Status & Tracking</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option>Inaktiv</option>
                    <option selected>Aktiv</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tracking aktivieren</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">Tracking-URL Vorlage</label>
                <input type="text" class="form-input" placeholder="https://tracking.dhl.de/?code={tracking_number}">
            </div>
            <div class="form-group">
                <label class="form-label">Sortierung/Position</label>
                <input type="number" class="form-input" value="10">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Preisgestaltung</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Berechnungsart</label>
            <select class="form-select">
                <option selected>Festpreis</option>
                <option>Nach Gewicht</option>
                <option>Nach Bestellwert</option>
                <option>Kostenlos</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Versandkosten (€)</label>
            <input type="number" class="form-input" placeholder="4.99" step="0.01">
        </div>
        <div class="form-group">
            <label class="form-label">Kostenlos ab Bestellwert (€)</label>
            <input type="number" class="form-input" placeholder="z.B. 50.00" step="0.01">
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Verfügbarkeit</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Verfügbare Länder</label>
            <select class="form-select" multiple style="height:120px;">
                <option selected>Deutschland</option>
                <option selected>Österreich</option>
                <option selected>Schweiz</option>
                <option>Frankreich</option>
                <option>Niederlande</option>
                <option>Belgien</option>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Min. Gewicht (kg)</label>
                <input type="number" class="form-input" placeholder="0" step="0.1">
            </div>
            <div class="form-group">
                <label class="form-label">Max. Gewicht (kg)</label>
                <input type="number" class="form-input" placeholder="31.5" step="0.1">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=commerce/shipping" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Versandmethode erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; }
</style>
