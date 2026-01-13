<?php /** Bestellungen - Fulfillment */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Fulfillment</h1>
        <p class="page-subtitle">Versand und Logistik</p>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Zu versenden</span></div>
        <div class="kpi-value" style="color:var(--warning);">12</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Heute versendet</span></div>
        <div class="kpi-value">8</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">In Zustellung</span></div>
        <div class="kpi-value">24</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Zugestellt (heute)</span></div>
        <div class="kpi-value" style="color:var(--success);">15</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Zu versenden</h3><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">print</span> Alle drucken</button></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th><input type="checkbox" class="select-all"></th><th>Bestellung</th><th>Kunde</th><th>Artikel</th><th>Versandart</th><th>Adresse</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><strong>#10045</strong></td>
                    <td>Max Mustermann</td>
                    <td>3 Artikel</td>
                    <td>DHL Standard</td>
                    <td>Musterstr. 1, 12345 Berlin</td>
                    <td class="table-actions"><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">local_shipping</span> Versenden</button></td>
                </tr>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><strong>#10043</strong></td>
                    <td>Peter Weber</td>
                    <td>5 Artikel</td>
                    <td>DHL Express</td>
                    <td>Hauptstr. 45, 80331 München</td>
                    <td class="table-actions"><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">local_shipping</span> Versenden</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>In Zustellung</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Bestellung</th><th>Tracking</th><th>Carrier</th><th>Status</th><th>Voraussichtlich</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>#10044</strong></td>
                    <td><a href="#">JJD000390012345678</a></td>
                    <td>DHL</td>
                    <td><span class="badge badge-info">Im Transit</span></td>
                    <td>08.01.2026</td>
                </tr>
                <tr>
                    <td><strong>#10041</strong></td>
                    <td><a href="#">JJD000390012345679</a></td>
                    <td>DHL</td>
                    <td><span class="badge badge-warning">Zustellversuch</span></td>
                    <td>Heute</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
