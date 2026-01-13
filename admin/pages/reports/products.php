<?php /** Reports - Produkte */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Produkt-Report</h1>
        <p class="page-subtitle">Produktperformance und Verkaufsanalyse</p>
    </div>
    <div class="page-header-actions">
        <select class="form-select"><option>Letzte 30 Tage</option><option>Letzte 90 Tage</option><option>Dieses Jahr</option></select>
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Top 10 Produkte</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Produkt</th><th>Verkäufe</th><th>Umsatz</th><th>Marge</th><th>Trend</th></tr></thead>
            <tbody>
                <tr><td><strong>Premium Lederjacke</strong><br><small style="color:var(--text-muted);">SKU: LJ-001</small></td><td>156</td><td>€46.644</td><td>45%</td><td><span class="badge badge-success">+25%</span></td></tr>
                <tr><td><strong>Designer Sneaker Pro</strong><br><small style="color:var(--text-muted);">SKU: DS-023</small></td><td>142</td><td>€26.838</td><td>38%</td><td><span class="badge badge-success">+18%</span></td></tr>
                <tr><td><strong>Cashmere Pullover</strong><br><small style="color:var(--text-muted);">SKU: CP-112</small></td><td>98</td><td>€15.582</td><td>52%</td><td><span class="badge badge-success">+12%</span></td></tr>
                <tr><td><strong>E-Book: Marketing Guide</strong><br><small style="color:var(--text-muted);">SKU: EB-045</small></td><td>245</td><td>€7.350</td><td>95%</td><td><span class="badge badge-success">+45%</span></td></tr>
                <tr><td><strong>Premium Mitgliedschaft</strong><br><small style="color:var(--text-muted);">SKU: SUB-001</small></td><td>89</td><td>€1.779/mo</td><td>85%</td><td><span class="badge badge-success">+32%</span></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Schwache Performance</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Produkt</th><th>Lagerbestand</th><th>Verkäufe (30 Tage)</th><th>Empfehlung</th></tr></thead>
            <tbody>
                <tr><td><strong>Winter Strickjacke</strong></td><td>245</td><td>3</td><td><span class="badge badge-warning">Rabattaktion</span></td></tr>
                <tr><td><strong>Sommer Sandalen</strong></td><td>180</td><td>0</td><td><span class="badge badge-error">Auslistung prüfen</span></td></tr>
            </tbody>
        </table>
    </div>
</div>
