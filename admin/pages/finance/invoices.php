<?php /** Finanzen - Rechnungen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Rechnungen</h1>
        <p class="page-subtitle">Alle Rechnungen verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Rechnung erstellen</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Rechnungen (Monat)</span></div>
        <div class="kpi-value">156</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Gesamtbetrag</span></div>
        <div class="kpi-value">€24.580</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Offene Rechnungen</span></div>
        <div class="kpi-value" style="color:var(--warning);">8</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Überfällig</span></div>
        <div class="kpi-value" style="color:var(--error);">2</div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="filters">
            <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Rechnung suchen..."></div>
            <select class="filter-select"><option>Alle Status</option><option>Bezahlt</option><option>Offen</option><option>Überfällig</option></select>
            <select class="filter-select"><option>Diesen Monat</option><option>Letzter Monat</option><option>Dieses Jahr</option></select>
        </div>
        <table class="table">
            <thead><tr><th>Rechnung</th><th>Bestellung</th><th>Kunde</th><th>Datum</th><th>Betrag</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>INV-2026-0156</strong></td>
                    <td>#10045</td>
                    <td>Max Mustermann</td>
                    <td>07.01.2026</td>
                    <td>€129,99</td>
                    <td><span class="badge badge-success">Bezahlt</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button></td>
                </tr>
                <tr>
                    <td><strong>INV-2026-0155</strong></td>
                    <td>#10044</td>
                    <td>Anna Schmidt</td>
                    <td>07.01.2026</td>
                    <td>€89,50</td>
                    <td><span class="badge badge-success">Bezahlt</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button></td>
                </tr>
                <tr>
                    <td><strong>INV-2026-0154</strong></td>
                    <td>#10043</td>
                    <td>Peter Weber</td>
                    <td>06.01.2026</td>
                    <td>€2.450,00</td>
                    <td><span class="badge badge-warning">Offen</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">mail</span></button></td>
                </tr>
                <tr>
                    <td><strong>INV-2026-0148</strong></td>
                    <td>#10037</td>
                    <td>Klaus Richter</td>
                    <td>28.12.2025</td>
                    <td>€345,00</td>
                    <td><span class="badge badge-error">Überfällig</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">download</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">mail</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
