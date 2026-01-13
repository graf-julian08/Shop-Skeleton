<?php /** Bestellungen - Fulfillment Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=orders/fulfillment">Fulfillment</a> <span>›</span> <span>Sendung FUL-2026-0045</span></nav>
        <h1>Sendung FUL-2026-0045</h1>
        <p class="page-subtitle">Bestellung <a href="?page=orders/order_detail&id=10045">#10045</a> · <span class="badge badge-info">In Bearbeitung</span></p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">print</span> Lieferschein</button>
        <button class="btn btn-primary"><span class="material-symbols-rounded">check</span> Als versendet markieren</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Sendungsdetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Sendungsnummer</span><span class="detail-value">FUL-2026-0045</span></div>
            <div class="detail-row"><span class="detail-label">Bestellung</span><span class="detail-value"><a href="?page=orders/order_detail&id=10045">#10045</a></span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-info">In Bearbeitung</span></span></div>
            <div class="detail-row"><span class="detail-label">Versandart</span><span class="detail-value">DHL Express</span></div>
            <div class="detail-row"><span class="detail-label">Tracking-Nr.</span><span class="detail-value">—</span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Lieferadresse</h3></div>
        <div class="card-body">
            <p><strong>Max Mustermann</strong><br>Musterstraße 123<br>12345 Musterstadt<br>Deutschland<br><br><span class="material-symbols-rounded" style="font-size:16px;">phone</span> +49 170 1234567</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Zu versendende Artikel</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th><input type="checkbox" class="select-all" checked></th><th>Produkt</th><th>SKU</th><th>Bestellt</th><th>Zu versenden</th><th>Lagerort</th></tr></thead>
            <tbody>
                <tr><td><input type="checkbox" checked></td><td><a href="?page=catalog/product_detail&id=1">Premium Lederjacke</a><br><small style="color:var(--text-muted);">Schwarz / M</small></td><td>LJ-001-BK-M</td><td>1</td><td><input type="number" class="form-input" value="1" style="width:60px;"></td><td>Lager A, Regal 12</td></tr>
                <tr><td><input type="checkbox" checked></td><td><a href="?page=catalog/product_detail&id=15">Designer Gürtel</a><br><small style="color:var(--text-muted);">Schwarz / 85cm</small></td><td>DG-085</td><td>1</td><td><input type="number" class="form-input" value="1" style="width:60px;"></td><td>Lager A, Regal 8</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Versand-Label erstellen</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Versanddienstleister</label>
                <select class="form-select"><option selected>DHL Express</option><option>DHL Standard</option><option>UPS</option><option>DPD</option></select>
            </div>
            <div class="form-group">
                <label class="form-label">Gewicht (kg)</label>
                <input type="text" class="form-input" value="1.5">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Maße (cm)</label>
                <input type="text" class="form-input" value="40 x 30 x 10">
            </div>
            <div class="form-group">
                <label class="form-label">Versicherungswert</label>
                <input type="text" class="form-input" value="400,99">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn"><span class="material-symbols-rounded">print</span> Label drucken</button>
        <button class="btn btn-primary"><span class="material-symbols-rounded">check</span> Sendung abschließen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.detail-row { display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--border-subtle); }
.detail-row:last-child { border-bottom:none; }
.detail-label { color:var(--text-muted); }
.detail-value { font-weight:500; }
</style>
