<?php /** Finanzen - Gutschrift Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=finance/credit_notes">Gutschriften</a> <span>›</span> <span>CN-2026-0008</span></nav>
        <h1>Gutschrift CN-2026-0008</h1>
        <p class="page-subtitle">Rechnung <a href="?page=finance/invoice_detail&id=32">INV-2026-0032</a> · <span class="badge badge-success">Erstattet</span></p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">print</span> Drucken</button>
        <button class="btn"><span class="material-symbols-rounded">download</span> PDF</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Gutschriftdetails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Gutschriftnr.</span><span class="detail-value">CN-2026-0008</span></div>
            <div class="detail-row"><span class="detail-label">Datum</span><span class="detail-value">06.01.2026</span></div>
            <div class="detail-row"><span class="detail-label">Originalrechnung</span><span class="detail-value"><a href="?page=finance/invoice_detail&id=32">INV-2026-0032</a></span></div>
            <div class="detail-row"><span class="detail-label">Retoure</span><span class="detail-value"><a href="?page=orders/return_detail&id=12">RET-2026-0012</a></span></div>
            <div class="detail-row"><span class="detail-label">Betrag</span><span class="detail-value" style="font-size:18px;font-weight:600;color:var(--success);">-€89,00</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="badge badge-success">Erstattet</span></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Kunde</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value"><a href="?page=customers/customer_detail&id=1">Max Mustermann</a></span></div>
            <div class="detail-row"><span class="detail-label">E-Mail</span><span class="detail-value">max.mustermann@email.de</span></div>
            <div class="detail-row"><span class="detail-label">Erstattung via</span><span class="detail-value">PayPal</span></div>
            <div class="detail-row"><span class="detail-label">Erstattet am</span><span class="detail-value">06.01.2026</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Gutschriftpositionen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Produkt</th><th>SKU</th><th>Menge</th><th>Einzelpreis</th><th>Gesamt</th></tr></thead>
            <tbody>
                <tr><td><a href="?page=catalog/product_detail&id=15">Designer Gürtel</a><br><small style="color:var(--text-muted);">Schwarz / 85cm</small></td><td>DG-085</td><td>1</td><td>€89,00</td><td style="color:var(--success);">-€89,00</td></tr>
            </tbody>
            <tfoot>
                <tr><td colspan="4" style="text-align:right;"><strong>Gutschriftbetrag</strong></td><td style="font-size:18px;font-weight:600;color:var(--success);"><strong>-€89,00</strong></td></tr>
            </tfoot>
        </table>
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
