<?php /** Bestellungen - Retoure Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=orders/returns">Retouren</a> <span>›</span> <span>RET-2026-0012</span></nav>
        <h1>Retoure RET-2026-0012</h1>
        <p class="page-subtitle">Bestellung <a href="?page=orders/order_detail&id=10032">#10032</a> · <span class="badge badge-warning">Warte auf Eingang</span></p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">print</span> Retourenschein</button>
        <button class="btn btn-primary"><span class="material-symbols-rounded">check</span> Eingang bestätigen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Retourendetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Retourennummer</span><span class="detail-value">RET-2026-0012</span></div>
            <div class="detail-row"><span class="detail-label">Bestellung</span><span class="detail-value"><a href="?page=orders/order_detail&id=10032">#10032</a></span></div>
            <div class="detail-row"><span class="detail-label">Erstellt am</span><span class="detail-value">05.01.2026</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-warning">Warte auf Eingang</span></span></div>
            <div class="detail-row"><span class="detail-label">Erstattungsbetrag</span><span class="detail-value" style="font-size:18px;font-weight:600;">€89,00</span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Kunde</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value"><a href="?page=customers/customer_detail&id=1">Max Mustermann</a></span></div>
            <div class="detail-row"><span class="detail-label">E-Mail</span><span class="detail-value">max.mustermann@email.de</span></div>
            <div class="detail-row"><span class="detail-label">Retourengrund</span><span class="detail-value">Passt nicht</span></div>
            <div class="detail-row"><span class="detail-label">Kundenkommentar</span><span class="detail-value">Größe M ist zu klein, brauche L</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Retourenpositionen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Produkt</th><th>SKU</th><th>Menge</th><th>Zustand</th><th>Erstattung</th></tr></thead>
            <tbody>
                <tr><td><a href="?page=catalog/product_detail&id=15">Designer Gürtel</a><br><small style="color:var(--text-muted);">Schwarz / 85cm</small></td><td>DG-085</td><td>1</td><td><select class="form-select" style="width:120px;"><option>Neu</option><option selected>Geöffnet</option><option>Beschädigt</option></select></td><td>€89,00</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Retoure bearbeiten</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label">Retourenstatus</label>
            <select class="form-select"><option>Angefordert</option><option selected>Warte auf Eingang</option><option>Eingegangen</option><option>Geprüft</option><option>Erstattet</option><option>Abgelehnt</option></select>
        </div>
        <div class="form-group">
            <label class="form-label">Erstattungsmethode</label>
            <select class="form-select"><option selected>Original-Zahlungsart</option><option>Shop-Guthaben</option><option>Banküberweisung</option></select>
        </div>
        <div class="form-group">
            <label class="form-label">Interne Notiz</label>
            <textarea class="form-textarea" rows="2" placeholder="Notiz zur Retoure..."></textarea>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-danger"><span class="material-symbols-rounded">close</span> Ablehnen</button>
        <button class="btn btn-primary"><span class="material-symbols-rounded">check</span> Erstattung durchführen</button>
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
