<?php /** Commerce - Warenkörbe */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Warenkörbe</h1>
        <p class="page-subtitle">Aktive und abgebrochene Warenkörbe verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">email</span> Erinnerungen senden</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="abgebrochen">Abgebrochen <span class="badge badge-warning" style="margin-left:4px;">156</span></button>
    <button class="tab" data-tab="aktiv">Aktiv <span class="badge badge-success" style="margin-left:4px;">23</span></button>
    <button class="tab" data-tab="alle">Alle</button>
</div>

<!-- Tab: Abgebrochen -->
<div data-tab-content="abgebrochen">
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-header"><span class="kpi-title">Abgebrochene Warenkörbe</span></div>
            <div class="kpi-value">156</div>
            <div class="kpi-change negative"><span class="material-symbols-rounded">trending_up</span>+8,3%</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-header"><span class="kpi-title">Potentieller Umsatz</span></div>
            <div class="kpi-value">€18.450</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-header"><span class="kpi-title">Recovery Rate</span></div>
            <div class="kpi-value">12,5%</div>
            <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+2,1%</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-header"><span class="kpi-title">Ø Warenkorbwert</span></div>
            <div class="kpi-value">€118,27</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="filters">
                <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Warenkorb suchen..."></div>
                <select class="filter-select"><option>Letzte 24h</option><option>Letzte 7 Tage</option><option>Letzte 30 Tage</option></select>
                <select class="filter-select"><option>Alle Werte</option><option>> €50</option><option>> €100</option><option>> €200</option></select>
            </div>
            <table class="table">
                <thead><tr><th>Kunde</th><th>Produkte</th><th>Wert</th><th>Abbruch</th><th>Erinnerungen</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>max.mustermann@email.de</strong></td>
                        <td>3 Artikel</td>
                        <td>€245,00</td>
                        <td>Vor 2 Stunden</td>
                        <td><span class="badge badge-warning">1 gesendet</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">email</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>anna.schmidt@email.de</strong></td>
                        <td>1 Artikel</td>
                        <td>€89,00</td>
                        <td>Vor 5 Stunden</td>
                        <td><span class="badge badge-default">Keine</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">email</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>peter.weber@email.de</strong></td>
                        <td>5 Artikel</td>
                        <td>€420,00</td>
                        <td>Gestern</td>
                        <td><span class="badge badge-error">2 gesendet</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">email</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Aktiv -->
<div data-tab-content="aktiv" style="display:none;">
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-header"><span class="kpi-title">Aktive Warenkörbe</span></div>
            <div class="kpi-value">23</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-header"><span class="kpi-title">Erwarteter Umsatz</span></div>
            <div class="kpi-value">€3.450</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-header"><span class="kpi-title">Ø Warenkorbwert</span></div>
            <div class="kpi-value">€150,00</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Kunde</th><th>Produkte</th><th>Wert</th><th>Letzte Aktivität</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>julia.becker@email.de</strong></td>
                        <td>2 Artikel</td>
                        <td>€180,00</td>
                        <td>Vor 5 Minuten</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>markus.klein@email.de</strong></td>
                        <td>4 Artikel</td>
                        <td>€320,00</td>
                        <td>Vor 12 Minuten</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>sara.wagner@email.de</strong></td>
                        <td>1 Artikel</td>
                        <td>€89,00</td>
                        <td>Vor 25 Minuten</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Alle -->
<div data-tab-content="alle" style="display:none;">
    <div class="card">
        <div class="card-body">
            <div class="filters">
                <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Warenkorb suchen..."></div>
                <select class="filter-select"><option>Alle Status</option><option>Aktiv</option><option>Abgebrochen</option><option>Konvertiert</option></select>
                <select class="filter-select"><option>Letzte 24h</option><option>Letzte 7 Tage</option><option>Letzte 30 Tage</option><option>Alle</option></select>
            </div>
            <table class="table">
                <thead><tr><th>Kunde</th><th>Produkte</th><th>Wert</th><th>Status</th><th>Erstellt</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>julia.becker@email.de</strong></td>
                        <td>2 Artikel</td>
                        <td>€180,00</td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td>Heute 14:30</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>max.mustermann@email.de</strong></td>
                        <td>3 Artikel</td>
                        <td>€245,00</td>
                        <td><span class="badge badge-warning">Abgebrochen</span></td>
                        <td>Heute 12:15</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">email</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>thomas.schulz@email.de</strong></td>
                        <td>2 Artikel</td>
                        <td>€156,00</td>
                        <td><span class="badge badge-info">Konvertiert</span></td>
                        <td>Gestern 16:45</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
