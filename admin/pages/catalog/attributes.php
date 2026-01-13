<?php /** Katalog - Attribute */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Attribute</h1>
        <p class="page-subtitle">Produktattribute und Varianten verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/attribute_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Attribut erstellen</a>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="attribute">Attribute</button>
    <button class="tab" data-tab="gruppen">Attributgruppen</button>
</div>

<!-- Tab: Attribute -->
<div data-tab-content="attribute">
    <div class="card">
        <div class="card-header"><h3>Alle Attribute</h3></div>
        <div class="card-body">
            <div class="filters">
                <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Attribut suchen..."></div>
                <select class="filter-select"><option>Alle Typen</option><option>Text</option><option>Dropdown</option><option>Mehrfachauswahl</option><option>Farbe</option></select>
            </div>
            <table class="table">
                <thead><tr><th>Attribut</th><th>Code</th><th>Typ</th><th>Werte</th><th>Verwendet in</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><a href="?page=catalog/attribute_detail&id=1"><strong>Farbe</strong></a></td>
                        <td><code>color</code></td>
                        <td><span class="badge badge-info">Farbe</span></td>
                        <td>12 Werte</td>
                        <td>89 Produkte</td>
                        <td class="table-actions"><a href="?page=catalog/attribute_edit&id=1" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/attribute_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                    <tr>
                        <td><a href="?page=catalog/attribute_detail&id=2"><strong>Größe</strong></a></td>
                        <td><code>size</code></td>
                        <td><span class="badge badge-default">Dropdown</span></td>
                        <td>8 Werte</td>
                        <td>124 Produkte</td>
                        <td class="table-actions"><a href="?page=catalog/attribute_edit&id=2" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/attribute_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                    <tr>
                        <td><a href="?page=catalog/attribute_detail&id=3"><strong>Material</strong></a></td>
                        <td><code>material</code></td>
                        <td><span class="badge badge-default">Dropdown</span></td>
                        <td>15 Werte</td>
                        <td>67 Produkte</td>
                        <td class="table-actions"><a href="?page=catalog/attribute_edit&id=3" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/attribute_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                    <tr>
                        <td><a href="?page=catalog/attribute_detail&id=4"><strong>Marke</strong></a></td>
                        <td><code>brand</code></td>
                        <td><span class="badge badge-default">Dropdown</span></td>
                        <td>23 Werte</td>
                        <td>156 Produkte</td>
                        <td class="table-actions"><a href="?page=catalog/attribute_edit&id=4" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a><a href="?page=catalog/attribute_detail&id=4" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Attributgruppen -->
<div data-tab-content="gruppen" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Attributgruppen</h3><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">add</span> Gruppe erstellen</button></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Gruppe</th><th>Attribute</th><th>Verwendung</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>Allgemein</strong></td>
                        <td>Name, Beschreibung, SKU, Preis</td>
                        <td><span class="badge badge-success">Alle Produkte</span></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Kleidung</strong></td>
                        <td>Farbe, Größe, Material</td>
                        <td>89 Produkte</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Elektronik</strong></td>
                        <td>Marke, Garantie, Stromverbrauch</td>
                        <td>45 Produkte</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Lebensmittel</strong></td>
                        <td>Zutaten, Allergene, MHD</td>
                        <td>12 Produkte</td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
