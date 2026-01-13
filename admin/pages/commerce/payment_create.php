<?php /** Commerce - Neuen Zahlungsanbieter hinzufügen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=commerce/payments">Zahlungen</a> <span>›</span> <span>Neuer Zahlungsanbieter</span></nav>
        <h1>Zahlungsanbieter hinzufügen</h1>
        <p class="page-subtitle">Konfigurieren Sie eine neue Zahlungsmethode</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/payments" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Zahlungsanbieter wählen</h3></div>
    <div class="card-body">
        <div class="payment-provider-grid">
            <label class="payment-provider-option">
                <input type="radio" name="provider" value="paypal" checked>
                <div class="provider-card">
                    <span class="material-symbols-rounded" style="font-size:32px;">account_balance_wallet</span>
                    <strong>PayPal</strong>
                    <small>PayPal Commerce Platform</small>
                </div>
            </label>
            <label class="payment-provider-option">
                <input type="radio" name="provider" value="stripe">
                <div class="provider-card">
                    <span class="material-symbols-rounded" style="font-size:32px;">credit_card</span>
                    <strong>Stripe</strong>
                    <small>Kreditkarte & mehr</small>
                </div>
            </label>
            <label class="payment-provider-option">
                <input type="radio" name="provider" value="klarna">
                <div class="provider-card">
                    <span class="material-symbols-rounded" style="font-size:32px;">payments</span>
                    <strong>Klarna</strong>
                    <small>Rechnung, Ratenkauf</small>
                </div>
            </label>
            <label class="payment-provider-option">
                <input type="radio" name="provider" value="bank">
                <div class="provider-card">
                    <span class="material-symbols-rounded" style="font-size:32px;">account_balance</span>
                    <strong>Banküberweisung</strong>
                    <small>Vorkasse</small>
                </div>
            </label>
            <label class="payment-provider-option">
                <input type="radio" name="provider" value="cod">
                <div class="provider-card">
                    <span class="material-symbols-rounded" style="font-size:32px;">local_shipping</span>
                    <strong>Nachnahme</strong>
                    <small>Zahlung bei Lieferung</small>
                </div>
            </label>
            <label class="payment-provider-option">
                <input type="radio" name="provider" value="invoice">
                <div class="provider-card">
                    <span class="material-symbols-rounded" style="font-size:32px;">receipt</span>
                    <strong>Rechnung</strong>
                    <small>Zahlung nach Erhalt</small>
                </div>
            </label>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Konfiguration</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Anzeigename <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" placeholder="z.B. Mit PayPal bezahlen">
            </div>
            <div class="form-group">
                <label class="form-label">Beschreibung für Kunden</label>
                <textarea class="form-textarea" rows="2" placeholder="Wird beim Checkout angezeigt..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option>Inaktiv</option>
                    <option selected>Aktiv</option>
                    <option>Test-Modus</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>API-Zugangsdaten</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Client-ID / API-Key</label>
                <input type="text" class="form-input" placeholder="Ihre API-ID...">
            </div>
            <div class="form-group">
                <label class="form-label">Secret Key</label>
                <input type="password" class="form-input" placeholder="••••••••">
            </div>
            <div class="form-group">
                <label class="form-label">Sandbox/Test-Modus</label>
                <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
                <small style="color:var(--text-muted);">Aktivieren für Testumgebung</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Einschränkungen</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Min. Bestellwert (€)</label>
                <input type="number" class="form-input" placeholder="0.00" step="0.01">
            </div>
            <div class="form-group">
                <label class="form-label">Max. Bestellwert (€)</label>
                <input type="number" class="form-input" placeholder="10000.00" step="0.01">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Verfügbar für Länder</label>
            <select class="form-select" multiple style="height:100px;">
                <option selected>Deutschland</option>
                <option selected>Österreich</option>
                <option selected>Schweiz</option>
            </select>
        </div>
    </div>
    <div class="card-footer">
        <a href="?page=commerce/payments" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Zahlungsanbieter speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.payment-provider-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; }
.payment-provider-option input { display:none; }
.provider-card { padding:20px; border:2px solid var(--border); border-radius:var(--radius-md); text-align:center; cursor:pointer; transition:all 0.2s; }
.provider-card:hover { border-color:var(--accent); }
.payment-provider-option input:checked + .provider-card { border-color:var(--accent); background:var(--accent-subtle); }
.provider-card strong { display:block; margin-top:8px; }
.provider-card small { color:var(--text-muted); }
.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; }
</style>
