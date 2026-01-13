<?php /** Developer - Debug */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Debug</h1>
        <p class="page-subtitle">Entwickler-Tools und Diagnose</p>
    </div>
</div>

<div class="alert alert-warning">
    <span class="material-symbols-rounded">warning</span>
    <div class="alert-content"><strong>Entwicklermodus aktiv</strong><br>Dieser Bereich ist nur für Entwickler bestimmt.</div>
</div>

<div class="card">
    <div class="card-header"><h3>System-Information</h3></div>
    <div class="card-body">
        <table class="table">
            <tbody>
                <tr><td><strong>PHP Version</strong></td><td>8.2.12</td></tr>
                <tr><td><strong>Shop Version</strong></td><td>2.1.0</td></tr>
                <tr><td><strong>Server</strong></td><td>Apache/2.4.54</td></tr>
                <tr><td><strong>Datenbank</strong></td><td>MySQL 8.0.32</td></tr>
                <tr><td><strong>Memory Limit</strong></td><td>256M</td></tr>
                <tr><td><strong>Max Upload</strong></td><td>50M</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Cache</h3></div>
    <div class="card-body">
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn"><span class="material-symbols-rounded">cached</span> Alle Caches löschen</button>
            <button class="btn"><span class="material-symbols-rounded">image</span> Bild-Cache löschen</button>
            <button class="btn"><span class="material-symbols-rounded">code</span> View-Cache löschen</button>
            <button class="btn"><span class="material-symbols-rounded">dns</span> Config-Cache löschen</button>
        </div>
        <div class="stats-grid" style="margin-top:20px;">
            <div class="stat-card"><div class="stat-card-label">Cache-Größe</div><div class="stat-card-value">45 MB</div></div>
            <div class="stat-card"><div class="stat-card-label">Cache-Einträge</div><div class="stat-card-value">1.234</div></div>
            <div class="stat-card"><div class="stat-card-label">Hit-Rate</div><div class="stat-card-value">94%</div></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Diagnose-Tools</h3></div>
    <div class="card-body">
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn btn-primary"><span class="material-symbols-rounded">check_circle</span> System-Check</button>
            <button class="btn"><span class="material-symbols-rounded">speed</span> Performance-Test</button>
            <button class="btn"><span class="material-symbols-rounded">bug_report</span> Debug-Log</button>
            <button class="btn"><span class="material-symbols-rounded">download</span> Diagnose-Bericht</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Queue & Jobs</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Queue</th><th>Ausstehend</th><th>Verarbeitet</th><th>Fehlgeschlagen</th></tr></thead>
            <tbody>
                <tr><td>default</td><td>0</td><td>4.567</td><td>2</td></tr>
                <tr><td>emails</td><td>3</td><td>12.345</td><td>0</td></tr>
                <tr><td>exports</td><td>0</td><td>89</td><td>0</td></tr>
            </tbody>
        </table>
    </div>
</div>
