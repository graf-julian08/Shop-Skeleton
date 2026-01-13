<?php /** Bestellungen - Übersicht */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Bestellungen</h1>
        <p class="page-subtitle">Alle Bestellungen verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="alle">Alle <span class="badge badge-default" style="margin-left:4px;">4</span></button>
    <button class="tab" data-tab="offen">Offen <span class="badge badge-warning" style="margin-left:4px;">1</span></button>
    <button class="tab" data-tab="bezahlt">Bezahlt <span class="badge badge-success" style="margin-left:4px;">2</span></button>
    <button class="tab" data-tab="versendet">Versendet <span class="badge badge-info" style="margin-left:4px;">1</span></button>
    <button class="tab" data-tab="abgeschlossen">Abgeschlossen <span class="badge badge-default" style="margin-left:4px;">1</span></button>
</div>

<!-- Tab: Alle -->
<div data-tab-content="alle">
    <div class="card">
        <div class="card-body">
            <div class="filters">
                <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Bestellung suchen..."></div>
                <select class="filter-select"><option>Alle Status</option><option>Ausstehend</option><option>Bezahlt</option><option>Versendet</option><option>Zugestellt</option></select>
                <select class="filter-select"><option>Heute</option><option>7 Tage</option><option>30 Tage</option><option>Alle</option></select>
            </div>
            <table class="table">
                <thead><tr><th><input type="checkbox" class="select-all"></th><th>Bestellung</th><th>Datum</th><th>Kunde</th><th>Betrag</th><th>Zahlung</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="?page=orders/order_detail&id=10045"><strong>#10045</strong></a></td>
                        <td>07.01.2026</td>
                        <td><a href="?page=customers/customer_detail&id=1">Max Mustermann</a></td>
                        <td>€129,99</td>
                        <td><span class="badge badge-success">Bezahlt</span></td>
                        <td><span class="badge badge-warning">Ausstehend</span></td>
                        <td class="table-actions"><a href="?page=orders/order_detail&id=10045" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=orders/order_edit&id=10045" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="?page=orders/order_detail&id=10044"><strong>#10044</strong></a></td>
                        <td>07.01.2026</td>
                        <td><a href="?page=customers/customer_detail&id=2">Anna Schmidt</a></td>
                        <td>€89,50</td>
                        <td><span class="badge badge-success">Bezahlt</span></td>
                        <td><span class="badge badge-info">Versendet</span></td>
                        <td class="table-actions"><a href="?page=orders/order_detail&id=10044" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=orders/order_edit&id=10044" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="?page=orders/order_detail&id=10043"><strong>#10043</strong></a></td>
                        <td>06.01.2026</td>
                        <td><a href="?page=customers/customer_detail&id=3">Peter Weber</a></td>
                        <td>€2.450,00</td>
                        <td><span class="badge badge-warning">Rechnung</span></td>
                        <td><span class="badge badge-success">Zugestellt</span></td>
                        <td class="table-actions"><a href="?page=orders/order_detail&id=10043" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=orders/order_edit&id=10043" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="?page=orders/order_detail&id=10042"><strong>#10042</strong></a></td>
                        <td>06.01.2026</td>
                        <td><a href="?page=customers/customer_detail&id=4">Lisa Müller</a></td>
                        <td>€67,80</td>
                        <td><span class="badge badge-error">Fehlgeschlagen</span></td>
                        <td><span class="badge badge-error">Storniert</span></td>
                        <td class="table-actions"><a href="?page=orders/order_detail&id=10042" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=orders/order_edit&id=10042" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Offen -->
<div data-tab-content="offen" style="display:none;">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead><tr><th><input type="checkbox"></th><th>Bestellung</th><th>Datum</th><th>Kunde</th><th>Betrag</th><th>Zahlung</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="?page=orders/order_detail&id=10045"><strong>#10045</strong></a></td>
                        <td>07.01.2026</td>
                        <td><a href="?page=customers/customer_detail&id=1">Max Mustermann</a></td>
                        <td>€129,99</td>
                        <td><span class="badge badge-success">Bezahlt</span></td>
                        <td><span class="badge badge-warning">Ausstehend</span></td>
                        <td class="table-actions"><a href="?page=orders/order_detail&id=10045" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=orders/order_edit&id=10045" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Bezahlt -->
<div data-tab-content="bezahlt" style="display:none;">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead><tr><th><input type="checkbox"></th><th>Bestellung</th><th>Datum</th><th>Kunde</th><th>Betrag</th><th>Zahlung</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="?page=orders/order_detail&id=10045"><strong>#10045</strong></a></td>
                        <td>07.01.2026</td>
                        <td><a href="?page=customers/customer_detail&id=1">Max Mustermann</a></td>
                        <td>€129,99</td>
                        <td><span class="badge badge-success">Bezahlt</span></td>
                        <td><span class="badge badge-warning">Ausstehend</span></td>
                        <td class="table-actions"><a href="?page=orders/order_detail&id=10045" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="?page=orders/order_detail&id=10044"><strong>#10044</strong></a></td>
                        <td>07.01.2026</td>
                        <td><a href="?page=customers/customer_detail&id=2">Anna Schmidt</a></td>
                        <td>€89,50</td>
                        <td><span class="badge badge-success">Bezahlt</span></td>
                        <td><span class="badge badge-info">Versendet</span></td>
                        <td class="table-actions"><a href="?page=orders/order_detail&id=10044" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Versendet -->
<div data-tab-content="versendet" style="display:none;">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead><tr><th><input type="checkbox"></th><th>Bestellung</th><th>Datum</th><th>Kunde</th><th>Betrag</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="?page=orders/order_detail&id=10044"><strong>#10044</strong></a></td>
                        <td>07.01.2026</td>
                        <td><a href="?page=customers/customer_detail&id=2">Anna Schmidt</a></td>
                        <td>€89,50</td>
                        <td><span class="badge badge-info">Versendet</span></td>
                        <td class="table-actions"><a href="?page=orders/order_detail&id=10044" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=orders/fulfillment_detail&id=10044" class="btn btn-sm"><span class="material-symbols-rounded">local_shipping</span></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Abgeschlossen -->
<div data-tab-content="abgeschlossen" style="display:none;">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead><tr><th><input type="checkbox"></th><th>Bestellung</th><th>Datum</th><th>Kunde</th><th>Betrag</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><a href="?page=orders/order_detail&id=10043"><strong>#10043</strong></a></td>
                        <td>06.01.2026</td>
                        <td><a href="?page=customers/customer_detail&id=3">Peter Weber</a></td>
                        <td>€2.450,00</td>
                        <td><span class="badge badge-success">Zugestellt</span></td>
                        <td class="table-actions"><a href="?page=orders/order_detail&id=10043" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><a href="?page=finance/invoice_detail&id=10043" class="btn btn-sm"><span class="material-symbols-rounded">receipt</span></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
