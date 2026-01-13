<?php /** Commerce - Neues Abo-Produkt erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/subscriptions">Abonnements</a> <span>›</span> <span>Neues Abo-Produkt</span></nav>
        <h1>Neues Abo-Produkt erstellen</h1>
        <p class="page-subtitle">Erstellen Sie ein Produkt mit wiederkehrender Zahlung</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/subscriptions" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Abo-Produkt erstellen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Produktinformationen</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Produktname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. Premium Mitgliedschaft">
            </div>
            <div class="form-group">
                <label class="form-label">URL-Slug</label>
                <input type="text" class="form-input" placeholder="z.B. premium-mitgliedschaft">
            </div>
            <div class="form-group">
                <label class="form-label">Beschreibung</label>
                <textarea class="form-textarea" rows="3" placeholder="Was ist in diesem Abo enthalten..."></textarea>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Abrechnungsdetails</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Preis (€) <span style="color:var(--error)">*</span></label>
                <input type="number" class="form-input" placeholder="19.99" step="0.01">
            </div>
            <div class="form-group">
                <label class="form-label">Abrechnungsintervall</label>
                <select class="form-select">
                    <option>Wöchentlich</option>
                    <option selected>Monatlich</option>
                    <option>Vierteljährlich</option>
                    <option>Halbjährlich</option>
                    <option>Jährlich</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Probezeit (Tage)</label>
                <input type="number" class="form-input" placeholder="0" value="0">
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
</div>

<div class="card">
    <div class="card-header"><h3>Abo-Optionen</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Mindestlaufzeit</label>
            <select class="form-select">
                <option selected>Keine</option>
                <option>1 Monat</option>
                <option>3 Monate</option>
                <option>6 Monate</option>
                <option>12 Monate</option>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Kündigungsfrist (Tage)</label>
                <input type="number" class="form-input" value="0">
            </div>
            <div class="form-group">
                <label class="form-label">Max. Verlängerungen</label>
                <input type="number" class="form-input" placeholder="Unbegrenzt = leer">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Kunde kann pausieren</label>
            <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
        </div>
        <div class="form-group">
            <label class="form-label">Kunde kann kündigen</label>
            <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Inhalt des Abonnements</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Art des Abos</label>
            <select class="form-select">
                <option selected>Digitaler Zugang/Mitgliedschaft</option>
                <option>Wiederkehrende Produktlieferung</option>
                <option>Service-Abo</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Enthaltene Produkte/Leistungen</label>
            <div style="border:2px dashed var(--border-color);border-radius:var(--radius-md);padding:24px;text-align:center;color:var(--text-muted);">
                <span class="material-symbols-rounded" style="font-size:32px;margin-bottom:8px;">add_shopping_cart</span>
                <p>Produkte oder Leistungen hinzufügen</p>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=commerce/subscriptions" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Abo-Produkt erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; }
</style>
