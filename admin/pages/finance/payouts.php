<?php /** Finanzen - Auszahlungen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Auszahlungen</h1>
        <p class="page-subtitle">Zahlungseingänge und Auszahlungen</p>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Verfügbar</span></div>
        <div class="kpi-value" style="color:var(--success);">€12.450</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Ausstehend</span></div>
        <div class="kpi-value">€3.280</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">In Transit</span></div>
        <div class="kpi-value">€5.600</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Nächste Auszahlung</span></div>
        <div class="kpi-value">10.01.2026</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Auszahlungsverlauf</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Datum</th><th>Betrag</th><th>Bankkonto</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td>03.01.2026</td>
                    <td>€8.450,00</td>
                    <td>DE89 3704 0044 **** 1234</td>
                    <td><span class="badge badge-success">Abgeschlossen</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
                <tr>
                    <td>27.12.2025</td>
                    <td>€12.890,00</td>
                    <td>DE89 3704 0044 **** 1234</td>
                    <td><span class="badge badge-success">Abgeschlossen</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
                <tr>
                    <td>20.12.2025</td>
                    <td>€9.120,00</td>
                    <td>DE89 3704 0044 **** 1234</td>
                    <td><span class="badge badge-success">Abgeschlossen</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Bankkonten</h3><button class="btn btn-sm"><span class="material-symbols-rounded">add</span> Konto</button></div>
    <div class="card-body">
        <div style="display:flex;gap:16px;">
            <div style="flex:1;border:2px solid var(--accent);border-radius:var(--radius-md);padding:20px;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <strong>Hauptkonto</strong>
                    <span class="badge badge-success">Standard</span>
                </div>
                <p style="color:var(--text-muted);margin-top:8px;">DE89 3704 0044 0532 0130 00</p>
                <p style="color:var(--text-muted);">Commerzbank</p>
            </div>
            <div style="flex:1;border:1px solid var(--border-color);border-radius:var(--radius-md);padding:20px;">
                <strong>Reservekonto</strong>
                <p style="color:var(--text-muted);margin-top:8px;">DE45 1234 5678 9012 3456 78</p>
                <p style="color:var(--text-muted);">Deutsche Bank</p>
            </div>
        </div>
    </div>
</div>
