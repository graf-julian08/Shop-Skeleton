<?php /** Finanzen - Abstimmung */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Abstimmung</h1>
        <p class="page-subtitle">Zahlungsabstimmung und -abgleich</p>
    </div>
</div>

<div class="alert alert-info">
    <span class="material-symbols-rounded">info</span>
    <div class="alert-content">Die automatische Abstimmung gleicht eingehende Zahlungen mit offenen Rechnungen ab.</div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Abgestimmt</span></div>
        <div class="kpi-value" style="color:var(--success);">98,2%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Offen</span></div>
        <div class="kpi-value" style="color:var(--warning);">5</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Differenzen</span></div>
        <div class="kpi-value" style="color:var(--error);">€45,20</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Offene Abstimmungen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Zahlung</th><th>Datum</th><th>Betrag</th><th>Zuordnung</th><th>Differenz</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td>Stripe #pi_3xyz</td>
                    <td>07.01.2026</td>
                    <td>€129,00</td>
                    <td><span class="badge badge-warning">Nicht zugeordnet</span></td>
                    <td>-</td>
                    <td class="table-actions"><button class="btn btn-sm btn-primary">Zuordnen</button></td>
                </tr>
                <tr>
                    <td>PayPal #8AB123</td>
                    <td>06.01.2026</td>
                    <td>€88,50</td>
                    <td>INV-2026-0155</td>
                    <td style="color:var(--error);">-€1,00</td>
                    <td class="table-actions"><button class="btn btn-sm">Prüfen</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Letzte Abstimmungen</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Zahlung</th><th>Rechnung</th><th>Betrag</th><th>Abgestimmt am</th></tr></thead>
            <tbody>
                <tr><td>Stripe #pi_2abc</td><td>INV-2026-0156</td><td>€129,99</td><td>07.01.2026 14:32</td></tr>
                <tr><td>Klarna #K9872</td><td>INV-2026-0154</td><td>€245,00</td><td>07.01.2026 12:15</td></tr>
                <tr><td>Stripe #pi_1def</td><td>INV-2026-0153</td><td>€89,99</td><td>06.01.2026 18:45</td></tr>
            </tbody>
        </table>
    </div>
</div>
