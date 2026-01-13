<?php /** Katalog - Produkte */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Produkte</h1>
        <p class="page-subtitle">Alle Produkte verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">upload</span> Import</button>
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
        <a href="?page=catalog/product_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Produkt hinzufügen</a>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="alle">Alle <span class="badge badge-default" style="margin-left:4px;">156</span></button>
    <button class="tab" data-tab="aktiv">Aktiv <span class="badge badge-success" style="margin-left:4px;">140</span></button>
    <button class="tab" data-tab="entwurf">Entwurf <span class="badge badge-warning" style="margin-left:4px;">12</span></button>
    <button class="tab" data-tab="archiviert">Archiviert <span class="badge badge-default" style="margin-left:4px;">4</span></button>
</div>

<!-- Tab: Alle -->
<div data-tab-content="alle">
    <div class="card">
        <div class="card-body">
            <div class="filters">
                <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Produkte durchsuchen..."></div>
                <select class="filter-select"><option>Alle Kategorien</option><option>Kleidung</option><option>Accessoires</option><option>Schuhe</option></select>
                <select class="filter-select"><option>Alle Typen</option><option>Physisch</option><option>Digital</option><option>Abo</option></select>
                <select class="filter-select"><option>Verfügbarkeit</option><option>Auf Lager</option><option>Ausverkauft</option><option>Niedriger Bestand</option></select>
            </div>
            <table class="table">
                <thead><tr><th><input type="checkbox" class="select-all"></th><th>Produkt</th><th>Status</th><th>Inventar</th><th>Typ</th><th>Preis</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><div style="display:flex;align-items:center;gap:12px;"><div class="product-image"></div><div><a href="?page=catalog/product_detail&id=1"><strong>Premium Lederjacke</strong></a><br><small style="color:var(--text-muted);">SKU: LJ-001</small></div></div></td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td>45 auf Lager</td>
                        <td>Physisch</td>
                        <td>€299,00</td>
                        <td class="table-actions"><a href="?page=catalog/product_edit&id=1" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/product_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><div style="display:flex;align-items:center;gap:12px;"><div class="product-image"></div><div><a href="?page=catalog/product_detail&id=2"><strong>Designer Sneaker Pro</strong></a><br><small style="color:var(--text-muted);">SKU: DS-023</small></div></div></td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td>128 auf Lager</td>
                        <td>Physisch</td>
                        <td>€189,00</td>
                        <td class="table-actions"><a href="?page=catalog/product_edit&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/product_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><div style="display:flex;align-items:center;gap:12px;"><div class="product-image"></div><div><a href="?page=catalog/product_detail&id=3"><strong>E-Book: Marketing Guide</strong></a><br><small style="color:var(--text-muted);">SKU: EB-045</small></div></div></td>
                        <td><span class="badge badge-success">Aktiv</span></td>
                        <td>∞ Digital</td>
                        <td><span class="badge badge-info">Digital</span></td>
                        <td>€29,99</td>
                        <td class="table-actions"><a href="?page=catalog/product_edit&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/product_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><div style="display:flex;align-items:center;gap:12px;"><div class="product-image"></div><div><a href="?page=catalog/product_detail&id=5"><strong>Cashmere Pullover</strong></a><br><small style="color:var(--text-muted);">SKU: CP-112</small></div></div></td>
                        <td><span class="badge badge-warning">Entwurf</span></td>
                        <td>0</td>
                        <td>Physisch</td>
                        <td>€159,00</td>
                        <td class="table-actions"><a href="?page=catalog/product_edit&id=5" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/product_detail&id=5" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                </tbody>
            </table>
            <div class="pagination"><span>1-4 von 156 Produkten</span><div><button class="btn btn-sm" disabled>←</button><button class="btn btn-sm" style="background:var(--accent);">1</button><button class="btn btn-sm">2</button><button class="btn btn-sm">3</button><button class="btn btn-sm">→</button></div></div>
        </div>
    </div>
</div>

<!-- Tab: Aktiv -->
<div data-tab-content="aktiv" style="display:none;">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead><tr><th><input type="checkbox"></th><th>Produkt</th><th>Inventar</th><th>Typ</th><th>Preis</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><div style="display:flex;align-items:center;gap:12px;"><div class="product-image"></div><div><a href="?page=catalog/product_detail&id=1"><strong>Premium Lederjacke</strong></a><br><small style="color:var(--text-muted);">SKU: LJ-001</small></div></div></td>
                        <td>45 auf Lager</td>
                        <td>Physisch</td>
                        <td>€299,00</td>
                        <td class="table-actions"><a href="?page=catalog/product_edit&id=1" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><div style="display:flex;align-items:center;gap:12px;"><div class="product-image"></div><div><a href="?page=catalog/product_detail&id=2"><strong>Designer Sneaker Pro</strong></a><br><small style="color:var(--text-muted);">SKU: DS-023</small></div></div></td>
                        <td>128 auf Lager</td>
                        <td>Physisch</td>
                        <td>€189,00</td>
                        <td class="table-actions"><a href="?page=catalog/product_edit&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><div style="display:flex;align-items:center;gap:12px;"><div class="product-image"></div><div><a href="?page=catalog/product_detail&id=3"><strong>E-Book: Marketing Guide</strong></a><br><small style="color:var(--text-muted);">SKU: EB-045</small></div></div></td>
                        <td>∞ Digital</td>
                        <td><span class="badge badge-info">Digital</span></td>
                        <td>€29,99</td>
                        <td class="table-actions"><a href="?page=catalog/product_edit&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a></td>
                    </tr>
                </tbody>
            </table>
            <p style="color:var(--text-muted);margin-top:16px;">140 aktive Produkte</p>
        </div>
    </div>
</div>

<!-- Tab: Entwurf -->
<div data-tab-content="entwurf" style="display:none;">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead><tr><th><input type="checkbox"></th><th>Produkt</th><th>Erstellt</th><th>Preis</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><div style="display:flex;align-items:center;gap:12px;"><div class="product-image"></div><div><a href="?page=catalog/product_detail&id=5"><strong>Cashmere Pullover</strong></a><br><small style="color:var(--text-muted);">SKU: CP-112</small></div></div></td>
                        <td>05.01.2026</td>
                        <td>€159,00</td>
                        <td class="table-actions"><a href="?page=catalog/product_edit&id=5" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><button class="btn btn-sm btn-primary">Veröffentlichen</button></td>
                    </tr>
                </tbody>
            </table>
            <p style="color:var(--text-muted);margin-top:16px;">12 Entwürfe</p>
        </div>
    </div>
</div>

<!-- Tab: Archiviert -->
<div data-tab-content="archiviert" style="display:none;">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead><tr><th><input type="checkbox"></th><th>Produkt</th><th>Archiviert am</th><th>Preis</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><div style="display:flex;align-items:center;gap:12px;"><div class="product-image"></div><div><strong>Altes Produkt XY</strong><br><small style="color:var(--text-muted);">SKU: OLD-001</small></div></div></td>
                        <td>01.12.2025</td>
                        <td>€49,00</td>
                        <td class="table-actions"><button class="btn btn-sm">Wiederherstellen</button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                </tbody>
            </table>
            <p style="color:var(--text-muted);margin-top:16px;">4 archivierte Produkte</p>
        </div>
    </div>
</div>

<style>
.product-image { width:40px; height:40px; background:var(--bg-lighter); border-radius:var(--radius-sm); }
.pagination { display:flex; justify-content:space-between; align-items:center; padding-top:16px; }
.pagination > div { display:flex; gap:4px; }
</style>
