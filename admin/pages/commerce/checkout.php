<?php /** Commerce - Checkout */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Checkout</h1>
        <p class="page-subtitle">Checkout-Prozess konfigurieren</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Checkout-Typ</h3></div>
    <div class="card-body">
        <div class="stats-grid">
            <div class="stat-card" style="border:2px solid var(--accent);cursor:pointer;">
                <div class="stat-card-label">Aktiv</div>
                <div class="stat-card-value">One-Page Checkout</div>
            </div>
            <div class="stat-card" style="cursor:pointer;">
                <div class="stat-card-label">Alternative</div>
                <div class="stat-card-value">Multi-Step Checkout</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Checkout-Schritte</h3></div>
    <div class="card-body">
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <div style="flex:1;min-width:150px;padding:16px;background:var(--bg-tertiary);border-radius:var(--radius-md);text-align:center;">
                <span class="material-symbols-rounded" style="font-size:32px;color:var(--accent);">shopping_cart</span>
                <p style="margin-top:8px;"><strong>1. Warenkorb</strong></p>
            </div>
            <div style="display:flex;align-items:center;"><span class="material-symbols-rounded">arrow_forward</span></div>
            <div style="flex:1;min-width:150px;padding:16px;background:var(--bg-tertiary);border-radius:var(--radius-md);text-align:center;">
                <span class="material-symbols-rounded" style="font-size:32px;color:var(--accent);">person</span>
                <p style="margin-top:8px;"><strong>2. Kundendaten</strong></p>
            </div>
            <div style="display:flex;align-items:center;"><span class="material-symbols-rounded">arrow_forward</span></div>
            <div style="flex:1;min-width:150px;padding:16px;background:var(--bg-tertiary);border-radius:var(--radius-md);text-align:center;">
                <span class="material-symbols-rounded" style="font-size:32px;color:var(--accent);">local_shipping</span>
                <p style="margin-top:8px;"><strong>3. Versand</strong></p>
            </div>
            <div style="display:flex;align-items:center;"><span class="material-symbols-rounded">arrow_forward</span></div>
            <div style="flex:1;min-width:150px;padding:16px;background:var(--bg-tertiary);border-radius:var(--radius-md);text-align:center;">
                <span class="material-symbols-rounded" style="font-size:32px;color:var(--accent);">payment</span>
                <p style="margin-top:8px;"><strong>4. Zahlung</strong></p>
            </div>
            <div style="display:flex;align-items:center;"><span class="material-symbols-rounded">arrow_forward</span></div>
            <div style="flex:1;min-width:150px;padding:16px;background:var(--bg-tertiary);border-radius:var(--radius-md);text-align:center;">
                <span class="material-symbols-rounded" style="font-size:32px;color:var(--success);">check_circle</span>
                <p style="margin-top:8px;"><strong>5. Bestätigung</strong></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Checkout-Optionen</h3></div>
    <div class="card-body">
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Gast-Checkout erlauben</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Express-Checkout (Apple Pay, Google Pay)</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Lieferadresse als Rechnungsadresse</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>Geschenkverpackung anbieten</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Bestellnotizen erlauben</span></label></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Pflichtfelder</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Telefonnummer</span></label></div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>Firma</span></label></div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>USt-ID</span></label></div>
            <div class="form-group"><label class="form-checkbox"><input type="checkbox"><span>Geburtsdatum</span></label></div>
        </div>
    </div>
</div>
