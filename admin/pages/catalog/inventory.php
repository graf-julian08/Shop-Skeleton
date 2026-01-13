<?php /** Katalog - Inventar */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Inventar</h1>
        <p class="page-subtitle">Lagerbestand verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
        <button class="btn"><span class="material-symbols-rounded">upload</span> Bestand aktualisieren</button>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Gesamtbestand</span></div>
        <div class="kpi-value">4.582</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Niedriger Bestand</span></div>
        <div class="kpi-value" style="color:var(--warning);">12</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Ausverkauft</span></div>
        <div class="kpi-value" style="color:var(--error);">5</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Bestellte Ware</span></div>
        <div class="kpi-value" style="color:var(--info);">234</div>
    </div>
</div>

<div class="alert alert-warning">
    <span class="material-symbols-rounded">warning</span>
    <div class="alert-content"><strong>12 Produkte</strong> haben niedrigen Lagerbestand und sollten nachbestellt werden.</div>
</div>

<div class="card">
    <div class="card-header"><h3>Bestandsübersicht</h3></div>
    <div class="card-body">
        <div class="filters">
            <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Produkt oder SKU suchen..."></div>
            <select class="filter-select"><option>Alle Status</option><option>Auf Lager</option><option>Niedriger Bestand</option><option>Ausverkauft</option></select>
            <select class="filter-select"><option>Alle Lager</option><option>Hauptlager</option><option>Außenlager</option></select>
        </div>
        <table class="table">
            <thead><tr><th>Produkt</th><th>SKU</th><th>Bestand</th><th>Reserviert</th><th>Verfügbar</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Premium Lederjacke</strong></td>
                    <td>LJ-001</td>
                    <td>45</td>
                    <td>3</td>
                    <td>42</td>
                    <td><span class="badge badge-success">Auf Lager</span></td>
                    <td><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><strong>Designer Sneaker</strong></td>
                    <td>DS-023</td>
                    <td>128</td>
                    <td>12</td>
                    <td>116</td>
                    <td><span class="badge badge-success">Auf Lager</span></td>
                    <td><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr style="background:rgba(245,158,11,0.1);">
                    <td><strong>Cashmere Pullover (M)</strong></td>
                    <td>CP-112-M</td>
                    <td>3</td>
                    <td>1</td>
                    <td>2</td>
                    <td><span class="badge badge-warning">Niedriger Bestand</span></td>
                    <td><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr style="background:rgba(245,158,11,0.1);">
                    <td><strong>Seiden Schal</strong></td>
                    <td>SS-089</td>
                    <td>5</td>
                    <td>2</td>
                    <td>3</td>
                    <td><span class="badge badge-warning">Niedriger Bestand</span></td>
                    <td><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr style="background:rgba(239,68,68,0.1);">
                    <td><strong>Limited Edition Tasche</strong></td>
                    <td>LET-001</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td><span class="badge badge-error">Ausverkauft</span></td>
                    <td><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Lager</h3><button class="btn btn-sm"><span class="material-symbols-rounded">add</span> Lager</button></div>
    <div class="card-body">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-label">Hauptlager Berlin</div>
                <div class="stat-card-value">3.845 Artikel</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-label">Außenlager München</div>
                <div class="stat-card-value">737 Artikel</div>
            </div>
        </div>
    </div>
</div>
