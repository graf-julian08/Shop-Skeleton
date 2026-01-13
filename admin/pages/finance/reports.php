<?php /** Finanzen - Berichte */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Finanzberichte</h1>
        <p class="page-subtitle">Umsatz- und Finanzanalysen</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">download</span> PDF Export</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="umsatz">Umsatz</button>
    <button class="tab" data-tab="gewinn">Gewinn & Verlust</button>
    <button class="tab" data-tab="steuern">Steuern</button>
    <button class="tab" data-tab="zahlungen">Zahlungen</button>
</div>

<!-- Tab: Umsatz -->
<div data-tab-content="umsatz">
    <div class="kpi-grid">
        <div class="kpi-card"><div class="kpi-header"><span class="kpi-title">Umsatz (Monat)</span></div><div class="kpi-value">€48.560</div><div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+12,4%</div></div>
        <div class="kpi-card"><div class="kpi-header"><span class="kpi-title">Gewinn</span></div><div class="kpi-value">€14.568</div><div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+8,2%</div></div>
        <div class="kpi-card"><div class="kpi-header"><span class="kpi-title">Marge</span></div><div class="kpi-value">30%</div></div>
        <div class="kpi-card"><div class="kpi-header"><span class="kpi-title">MwSt. Schuld</span></div><div class="kpi-value">€7.890</div></div>
    </div>
    <div class="card"><div class="card-header"><h3>Umsatzentwicklung</h3></div><div class="card-body" style="height:200px;display:flex;align-items:center;justify-content:center;background:var(--bg-tertiary);border-radius:var(--radius-md);"><span style="color:var(--text-muted);">📊 Umsatz-Chart</span></div></div>
    <div class="dashboard-grid">
        <div class="card"><div class="card-header"><h3>Top Produkte</h3></div><div class="card-body"><table class="table"><tbody><tr><td>Premium Lederjacke</td><td style="text-align:right;">€8.970</td></tr><tr><td>Designer Sneaker</td><td style="text-align:right;">€5.670</td></tr><tr><td>Cashmere Pullover</td><td style="text-align:right;">€4.770</td></tr></tbody></table></div></div>
        <div class="card"><div class="card-header"><h3>Zahlungsarten</h3></div><div class="card-body"><table class="table"><tbody><tr><td>Kreditkarte</td><td style="text-align:right;">42%</td></tr><tr><td>PayPal</td><td style="text-align:right;">28%</td></tr><tr><td>Klarna</td><td style="text-align:right;">18%</td></tr></tbody></table></div></div>
    </div>
</div>

<!-- Tab: Gewinn & Verlust -->
<div data-tab-content="gewinn" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Gewinn- und Verlustrechnung</h3><select class="filter-select"><option>Januar 2026</option><option>Dezember 2025</option><option>Q4 2025</option></select></div>
        <div class="card-body">
            <table class="table">
                <tbody>
                    <tr style="font-weight:600;"><td>Umsatzerlöse</td><td style="text-align:right;">€48.560,00</td></tr>
                    <tr><td style="padding-left:20px;">Produktverkäufe</td><td style="text-align:right;">€45.230,00</td></tr>
                    <tr><td style="padding-left:20px;">Digitale Produkte</td><td style="text-align:right;">€2.890,00</td></tr>
                    <tr><td style="padding-left:20px;">Abonnements</td><td style="text-align:right;">€440,00</td></tr>
                    <tr style="font-weight:600;border-top:1px solid var(--border-color);"><td>Herstellungskosten</td><td style="text-align:right;color:var(--error);">-€28.120,00</td></tr>
                    <tr style="font-weight:600;background:var(--bg-lighter);"><td>Bruttogewinn</td><td style="text-align:right;">€20.440,00</td></tr>
                    <tr style="font-weight:600;"><td>Betriebskosten</td><td style="text-align:right;color:var(--error);">-€5.872,00</td></tr>
                    <tr><td style="padding-left:20px;">Marketing</td><td style="text-align:right;">€2.450,00</td></tr>
                    <tr><td style="padding-left:20px;">Versand</td><td style="text-align:right;">€1.890,00</td></tr>
                    <tr><td style="padding-left:20px;">Zahlungsgebühren</td><td style="text-align:right;">€1.532,00</td></tr>
                    <tr style="font-weight:600;background:var(--success);color:white;"><td>Nettogewinn</td><td style="text-align:right;">€14.568,00</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Steuern -->
<div data-tab-content="steuern" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Steuerübersicht</h3></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Steuerzone</th><th>Steuersatz</th><th>Nettoumsatz</th><th>Erhobene MwSt.</th></tr></thead>
                <tbody>
                    <tr><td><strong>Deutschland</strong></td><td>19%</td><td>€32.450,00</td><td>€6.165,50</td></tr>
                    <tr><td><strong>Österreich</strong></td><td>20%</td><td>€8.230,00</td><td>€1.646,00</td></tr>
                    <tr><td><strong>Schweiz</strong></td><td>8,1%</td><td>€980,00</td><td>€79,38</td></tr>
                    <tr style="font-weight:600;background:var(--bg-lighter);"><td>Gesamt</td><td>-</td><td>€41.660,00</td><td>€7.890,88</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Zahlungen -->
<div data-tab-content="zahlungen" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Zahlungsübersicht</h3></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Zahlungsart</th><th>Transaktionen</th><th>Volumen</th><th>Gebühren</th><th>Netto</th></tr></thead>
                <tbody>
                    <tr><td><strong>Kreditkarte</strong></td><td>156</td><td>€20.395,20</td><td>€509,88</td><td>€19.885,32</td></tr>
                    <tr><td><strong>PayPal</strong></td><td>98</td><td>€13.596,80</td><td>€421,50</td><td>€13.175,30</td></tr>
                    <tr><td><strong>Klarna</strong></td><td>67</td><td>€8.740,80</td><td>€262,22</td><td>€8.478,58</td></tr>
                    <tr><td><strong>Überweisung</strong></td><td>34</td><td>€5.827,20</td><td>€0,00</td><td>€5.827,20</td></tr>
                    <tr style="font-weight:600;background:var(--bg-lighter);"><td>Gesamt</td><td>355</td><td>€48.560,00</td><td>€1.193,60</td><td>€47.366,40</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
