<?php /** Marketing - Newsletter */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Newsletter</h1>
        <p class="page-subtitle">E-Mail-Listen und Newsletter</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">send</span> Newsletter senden</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Abonnenten</span></div>
        <div class="kpi-value">4.567</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+89 diese Woche</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Öffnungsrate</span></div>
        <div class="kpi-value">24,5%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Klickrate</span></div>
        <div class="kpi-value">3,2%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Abmeldungen</span></div>
        <div class="kpi-value">12</div>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="abonnenten">Abonnenten</button>
    <button class="tab" data-tab="gesendet">Gesendete Newsletter</button>
    <button class="tab" data-tab="vorlagen">Vorlagen</button>
</div>

<!-- Tab: Abonnenten -->
<div data-tab-content="abonnenten">
    <div class="card">
        <div class="card-header"><h3>Abonnenten</h3><button class="btn btn-sm"><span class="material-symbols-rounded">download</span> Export</button></div>
        <div class="card-body">
            <div class="filters">
                <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="E-Mail suchen..."></div>
                <select class="filter-select"><option>Alle</option><option>Aktiv</option><option>Abgemeldet</option></select>
            </div>
            <table class="table">
                <thead><tr><th>E-Mail</th><th>Anmeldedatum</th><th>Quelle</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr><td>max@example.com</td><td>05.01.2026</td><td>Checkout</td><td><span class="badge badge-success">Aktiv</span></td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td></tr>
                    <tr><td>anna@example.com</td><td>03.01.2026</td><td>Footer</td><td><span class="badge badge-success">Aktiv</span></td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td></tr>
                    <tr><td>peter@company.de</td><td>28.12.2025</td><td>Popup</td><td><span class="badge badge-default">Abgemeldet</span></td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Gesendete Newsletter -->
<div data-tab-content="gesendet" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Gesendete Newsletter</h3></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Betreff</th><th>Gesendet</th><th>Empfänger</th><th>Öffnungen</th><th>Klicks</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>Winter Sale - Bis zu 50% Rabatt!</strong></td>
                        <td>03.01.2026 10:00</td>
                        <td>4.234</td>
                        <td>1.056 (25%)</td>
                        <td>156 (3,7%)</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">bar_chart</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Frohes neues Jahr 2026!</strong></td>
                        <td>01.01.2026 00:01</td>
                        <td>4.198</td>
                        <td>987 (23,5%)</td>
                        <td>112 (2,7%)</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">bar_chart</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Weihnachts-Countdown: Letzte Chance!</strong></td>
                        <td>20.12.2025 09:00</td>
                        <td>4.023</td>
                        <td>1.234 (30,7%)</td>
                        <td>289 (7,2%)</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">bar_chart</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Vorlagen -->
<div data-tab-content="vorlagen" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Newsletter-Vorlagen</h3><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">add</span> Neue Vorlage</button></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(250px, 1fr));gap:16px;">
                <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;text-align:center;">
                    <div style="background:var(--bg-lighter);height:120px;border-radius:var(--radius-sm);margin-bottom:12px;display:flex;align-items:center;justify-content:center;"><span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);">mail</span></div>
                    <strong>Produkt-Ankündigung</strong>
                    <p style="color:var(--text-muted);font-size:12px;margin-top:4px;">Für neue Produkte</p>
                    <button class="btn btn-sm" style="margin-top:8px;">Verwenden</button>
                </div>
                <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;text-align:center;">
                    <div style="background:var(--bg-lighter);height:120px;border-radius:var(--radius-sm);margin-bottom:12px;display:flex;align-items:center;justify-content:center;"><span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);">local_offer</span></div>
                    <strong>Sale-Ankündigung</strong>
                    <p style="color:var(--text-muted);font-size:12px;margin-top:4px;">Für Rabattaktionen</p>
                    <button class="btn btn-sm" style="margin-top:8px;">Verwenden</button>
                </div>
                <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;text-align:center;">
                    <div style="background:var(--bg-lighter);height:120px;border-radius:var(--radius-sm);margin-bottom:12px;display:flex;align-items:center;justify-content:center;"><span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);">celebration</span></div>
                    <strong>Feiertage</strong>
                    <p style="color:var(--text-muted);font-size:12px;margin-top:4px;">Saisonale Newsletter</p>
                    <button class="btn btn-sm" style="margin-top:8px;">Verwenden</button>
                </div>
            </div>
        </div>
    </div>
</div>
