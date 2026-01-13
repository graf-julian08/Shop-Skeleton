<?php /** Katalog - Attribut Detail */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/attributes">Attribute</a> <span>›</span> <span>Farbe</span></nav>
        <h1>Farbe</h1>
        <p class="page-subtitle">Auswahlattribut · 12 Optionen · 245 Produkte</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/attribute_edit&id=1" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger"><span class="material-symbols-rounded">delete</span> Löschen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header"><h3>Attribut-Details</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">Farbe</span></div>
            <div class="detail-row"><span class="detail-label">Code</span><span class="detail-value">color</span></div>
            <div class="detail-row"><span class="detail-label">Typ</span><span class="detail-value">Auswahl (Dropdown)</span></div>
            <div class="detail-row"><span class="detail-label">Erforderlich</span><span class="detail-value"><span class="badge badge-success">Ja</span></span></div>
            <div class="detail-row"><span class="detail-label">Filterbar</span><span class="detail-value"><span class="badge badge-success">Ja</span></span></div>
            <div class="detail-row"><span class="detail-label">Suchbar</span><span class="detail-value"><span class="badge badge-success">Ja</span></span></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Verwendung</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Zugewiesene Produkte</span><span class="detail-value" style="font-size:24px;font-weight:600;">245</span></div>
            <div class="detail-row"><span class="detail-label">Attributgruppe</span><span class="detail-value">Produkteigenschaften</span></div>
            <div class="detail-row"><span class="detail-label">Position</span><span class="detail-value">1</span></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Attributoptionen</h3><button class="btn btn-sm btn-primary"><span class="material-symbols-rounded">add</span> Option hinzufügen</button></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Wert</th><th>Label</th><th>Position</th><th>Produkte</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr><td><span style="display:inline-block;width:16px;height:16px;background:#000;border-radius:50%;vertical-align:middle;margin-right:8px;"></span> black</td><td>Schwarz</td><td>1</td><td>89</td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td></tr>
                <tr><td><span style="display:inline-block;width:16px;height:16px;background:#fff;border:1px solid #ccc;border-radius:50%;vertical-align:middle;margin-right:8px;"></span> white</td><td>Weiß</td><td>2</td><td>67</td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td></tr>
                <tr><td><span style="display:inline-block;width:16px;height:16px;background:#3b82f6;border-radius:50%;vertical-align:middle;margin-right:8px;"></span> blue</td><td>Blau</td><td>3</td><td>45</td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td></tr>
                <tr><td><span style="display:inline-block;width:16px;height:16px;background:#ef4444;border-radius:50%;vertical-align:middle;margin-right:8px;"></span> red</td><td>Rot</td><td>4</td><td>28</td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td></tr>
                <tr><td><span style="display:inline-block;width:16px;height:16px;background:#22c55e;border-radius:50%;vertical-align:middle;margin-right:8px;"></span> green</td><td>Grün</td><td>5</td><td>16</td><td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td></tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.detail-row { display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid var(--border-subtle); }
.detail-row:last-child { border-bottom:none; }
.detail-label { color:var(--text-muted); }
.detail-value { font-weight:500; }
</style>
