<?php /** Finanzen - Rechnung Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=finance/invoices">Rechnungen</a> <span>›</span> <span>INV-2026-0045</span></nav>
        <h1>Rechnung INV-2026-0045</h1>
        <p class="page-subtitle">Bestellung <a href="?page=orders/order_detail&id=10045">#10045</a> · <span class="badge badge-success">Bezahlt</span></p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">print</span> Drucken</button>
        <button class="btn"><span class="material-symbols-rounded">download</span> PDF</button>
        <button class="btn"><span class="material-symbols-rounded">email</span> Senden</button>
    </div>
</div>

<div class="card invoice-preview">
    <div class="card-body">
        <div class="invoice-header" style="display:flex;justify-content:space-between;margin-bottom:32px;">
            <div>
                <h2 style="margin:0;font-size:24px;">RECHNUNG</h2>
                <p style="color:var(--text-muted);margin:4px 0;">INV-2026-0045</p>
            </div>
            <div style="text-align:right;">
                <strong>Mein Online Shop</strong><br>
                <span style="color:var(--text-muted);">Shopstraße 1<br>12345 Shopstadt<br>Deutschland</span>
            </div>
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
            <div>
                <p style="color:var(--text-muted);margin:0 0 8px;">Rechnungsadresse</p>
                <p style="margin:0;"><strong>Max Mustermann</strong><br>Musterstraße 123<br>12345 Musterstadt<br>Deutschland</p>
            </div>
            <div style="text-align:right;">
                <p style="margin:0;"><strong>Rechnungsnr.:</strong> INV-2026-0045</p>
                <p style="margin:4px 0;"><strong>Rechnungsdatum:</strong> 03.01.2026</p>
                <p style="margin:4px 0;"><strong>Fällig:</strong> 03.01.2026</p>
                <p style="margin:4px 0;"><strong>Bestellung:</strong> <a href="?page=orders/order_detail&id=10045">#10045</a></p>
            </div>
        </div>
        
        <table class="table" style="margin-bottom:24px;">
            <thead><tr><th>Pos.</th><th>Beschreibung</th><th style="text-align:center;">Menge</th><th style="text-align:right;">Einzelpreis</th><th style="text-align:right;">Gesamt</th></tr></thead>
            <tbody>
                <tr><td>1</td><td>Premium Lederjacke (Schwarz / M)</td><td style="text-align:center;">1</td><td style="text-align:right;">€299,00</td><td style="text-align:right;">€299,00</td></tr>
                <tr><td>2</td><td>Designer Gürtel (Schwarz / 85cm)</td><td style="text-align:center;">1</td><td style="text-align:right;">€89,00</td><td style="text-align:right;">€89,00</td></tr>
            </tbody>
        </table>
        
        <div style="display:flex;justify-content:flex-end;">
            <table style="width:250px;">
                <tr><td>Zwischensumme</td><td style="text-align:right;">€388,00</td></tr>
                <tr><td>Versand</td><td style="text-align:right;">€12,99</td></tr>
                <tr><td>MwSt. (19%)</td><td style="text-align:right;">€62,00</td></tr>
                <tr style="font-size:18px;font-weight:600;"><td><strong>Gesamtbetrag</strong></td><td style="text-align:right;"><strong>€400,99</strong></td></tr>
            </table>
        </div>
        
        <div style="margin-top:32px;padding-top:24px;border-top:1px solid var(--border-subtle);">
            <p style="color:var(--text-muted);font-size:12px;margin:0;">Zahlbar innerhalb von 14 Tagen. Vielen Dank für Ihren Einkauf!</p>
        </div>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.invoice-preview { max-width:800px; }
</style>
