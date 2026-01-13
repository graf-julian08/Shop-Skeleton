<?php /** Commerce - Zahlungen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Zahlungen</h1>
        <p class="page-subtitle">Zahlungsmethoden konfigurieren</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/payment_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Zahlungsanbieter hinzufügen</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Aktive Zahlungsmethoden</h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            <a href="?page=commerce/payment_detail&id=1" class="payment-method-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <strong>Kreditkarte</strong>
                    <label class="toggle" onclick="event.stopPropagation();"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                </div>
                <p style="color:var(--text-muted);font-size:13px;margin-bottom:12px;">Stripe Payment Gateway</p>
                <span class="badge badge-success">Verbunden</span>
            </a>
            <a href="?page=commerce/payment_detail&id=2" class="payment-method-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <strong>PayPal</strong>
                    <label class="toggle" onclick="event.stopPropagation();"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                </div>
                <p style="color:var(--text-muted);font-size:13px;margin-bottom:12px;">PayPal Checkout</p>
                <span class="badge badge-success">Verbunden</span>
            </a>
            <a href="?page=commerce/payment_detail&id=3" class="payment-method-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <strong>Klarna</strong>
                    <label class="toggle" onclick="event.stopPropagation();"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                </div>
                <p style="color:var(--text-muted);font-size:13px;margin-bottom:12px;">Rechnung, Ratenzahlung</p>
                <span class="badge badge-success">Verbunden</span>
            </a>
            <a href="?page=commerce/payment_detail&id=4" class="payment-method-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <strong>Apple Pay</strong>
                    <label class="toggle" onclick="event.stopPropagation();"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                </div>
                <p style="color:var(--text-muted);font-size:13px;margin-bottom:12px;">Express Checkout</p>
                <span class="badge badge-success">Verbunden</span>
            </a>
            <a href="?page=commerce/payment_detail&id=5" class="payment-method-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <strong>SEPA Lastschrift</strong>
                    <label class="toggle" onclick="event.stopPropagation();"><input type="checkbox"><span class="toggle-slider"></span></label>
                </div>
                <p style="color:var(--text-muted);font-size:13px;margin-bottom:12px;">Bankeinzug</p>
                <span class="badge badge-warning">Nicht konfiguriert</span>
            </a>
            <a href="?page=commerce/payment_detail&id=6" class="payment-method-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <strong>Vorkasse</strong>
                    <label class="toggle" onclick="event.stopPropagation();"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                </div>
                <p style="color:var(--text-muted);font-size:13px;margin-bottom:12px;">Überweisung</p>
                <span class="badge badge-success">Aktiv</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Zahlungseinstellungen</h3></div>
    <div class="card-body">
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Testmodus für alle Zahlungen</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>3D Secure für Kreditkarten erzwingen</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>Automatische Captures aktivieren</span></label></div>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Einstellungen speichern</button>
    </div>
</div>

<style>
.payment-method-card {
    display:block;
    border:1px solid var(--border-color);
    border-radius:var(--radius-md);
    padding:20px;
    text-decoration:none;
    color:inherit;
    transition:border-color 0.2s, box-shadow 0.2s;
}
.payment-method-card:hover {
    border-color:var(--accent);
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
</style>
