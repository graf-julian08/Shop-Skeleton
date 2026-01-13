<?php /** Kunden - Kunde bearbeiten */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=customers/customers">Kunden</a> <span>›</span> <a href="?page=customers/customer_detail&id=1">Max Mustermann</a> <span>›</span> <span>Bearbeiten</span></nav>
        <h1>Kunde bearbeiten</h1>
        <p class="page-subtitle">Max Mustermann</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=customers/customer_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Persönliche Daten</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Vorname <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" value="Max">
                </div>
                <div class="form-group">
                    <label class="form-label">Nachname <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" value="Mustermann">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">E-Mail <span style="color:var(--error)">*</span></label>
                <input type="email" class="form-input" value="max.mustermann@email.de">
            </div>
            <div class="form-group">
                <label class="form-label">Telefon</label>
                <input type="text" class="form-input" value="+49 170 1234567">
            </div>
            <div class="form-group">
                <label class="form-label">Geburtsdatum</label>
                <input type="date" class="form-input" value="1985-06-15">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Kontoeinstellungen</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Kundengruppe</label>
                <select class="form-select"><option>Standard</option><option>Premium</option><option selected>VIP</option><option>Großhandel</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select"><option selected>Aktiv</option><option>Gesperrt</option><option>Gelöscht</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Newsletter abonniert</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
            <div class="form-group">
                <label class="form-label">Marketing E-Mails</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Interne Notizen</h3></div>
    <div class="card-body">
        <div class="form-group">
            <textarea class="form-textarea" rows="4" placeholder="Interne Notizen zu diesem Kunden...">VIP-Kunde seit 2024. Bevorzugt Express-Versand. Regelmäßiger Käufer von Premium-Produkten.</textarea>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=customers/customer_detail&id=1" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Änderungen speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
