<?php /** Bestellungen - Bestellung bearbeiten */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=orders/orders">Bestellungen</a> <span>›</span> <a href="?page=orders/order_detail&id=10045">#10045</a> <span>›</span> <span>Bearbeiten</span></nav>
        <h1>Bestellung bearbeiten</h1>
        <p class="page-subtitle">#10045</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=orders/order_detail&id=10045" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Status</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Bestellstatus</label>
                <select class="form-select"><option>Neu</option><option selected>Ausstehend</option><option>In Bearbeitung</option><option>Versendet</option><option>Abgeschlossen</option><option>Storniert</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Zahlungsstatus</label>
                <select class="form-select"><option>Ausstehend</option><option selected>Bezahlt</option><option>Teilweise bezahlt</option><option>Erstattet</option></select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Versand</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Versandart</label>
                <select class="form-select"><option>DHL Standard</option><option selected>DHL Express</option><option>UPS</option><option>Abholung</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Tracking-Nummer</label>
                <input type="text" class="form-input" placeholder="z.B. 1234567890123">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Lieferadresse</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" class="form-input" value="Max Mustermann">
            </div>
            <div class="form-group">
                <label class="form-label">Telefon</label>
                <input type="text" class="form-input" value="+49 170 1234567">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Straße</label>
            <input type="text" class="form-input" value="Musterstraße 123">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">PLZ</label>
                <input type="text" class="form-input" value="12345">
            </div>
            <div class="form-group">
                <label class="form-label">Stadt</label>
                <input type="text" class="form-input" value="Musterstadt">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Land</label>
            <select class="form-select"><option selected>Deutschland</option><option>Österreich</option><option>Schweiz</option></select>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Interne Notiz hinzufügen</h3></div>
    <div class="card-body">
        <div class="form-group">
            <textarea class="form-textarea" rows="3" placeholder="Notiz zu dieser Bestellung..."></textarea>
        </div>
        <div class="form-group">
            <label class="form-label"><input type="checkbox"> Kunden per E-Mail benachrichtigen</label>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=orders/order_detail&id=10045" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Änderungen speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
</style>
