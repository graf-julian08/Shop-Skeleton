<?php /** Katalog - Konfigurator */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Produkt-Konfigurator</h1>
        <p class="page-subtitle">Konfigurierbare Produkte verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">add</span> Konfigurator erstellen</button>
    </div>
</div>

<div class="alert alert-info">
    <span class="material-symbols-rounded">info</span>
    <div class="alert-content">Der Produkt-Konfigurator ermöglicht es Kunden, Produkte Schritt für Schritt nach ihren Wünschen zusammenzustellen.</div>
</div>

<div class="card">
    <div class="card-header"><h3>Aktive Konfiguratoren</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Produkt</th><th>Schritte</th><th>Optionen</th><th>Konfiguriert</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Custom Sneaker</strong></td>
                    <td>5 Schritte</td>
                    <td>24 Optionen</td>
                    <td>156 mal</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
                <tr>
                    <td><strong>Personalisierte Handtasche</strong></td>
                    <td>4 Schritte</td>
                    <td>18 Optionen</td>
                    <td>89 mal</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
                <tr>
                    <td><strong>Build Your Box</strong></td>
                    <td>3 Schritte</td>
                    <td>32 Optionen</td>
                    <td>234 mal</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Konfigurator: Custom Sneaker</h3></div>
    <div class="card-body">
        <h4 style="margin-bottom:16px;">Konfigurationsschritte</h4>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;display:flex;align-items:center;gap:12px;">
                <span class="material-symbols-rounded" style="color:var(--text-muted);">drag_indicator</span>
                <span style="background:var(--accent);color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;">1</span>
                <div style="flex:1;"><strong>Modell wählen</strong><br><small style="color:var(--text-muted);">3 Optionen: Classic, Sport, Premium</small></div>
                <button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;display:flex;align-items:center;gap:12px;">
                <span class="material-symbols-rounded" style="color:var(--text-muted);">drag_indicator</span>
                <span style="background:var(--accent);color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;">2</span>
                <div style="flex:1;"><strong>Farbe wählen</strong><br><small style="color:var(--text-muted);">12 Farboptionen</small></div>
                <button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;display:flex;align-items:center;gap:12px;">
                <span class="material-symbols-rounded" style="color:var(--text-muted);">drag_indicator</span>
                <span style="background:var(--accent);color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;">3</span>
                <div style="flex:1;"><strong>Material</strong><br><small style="color:var(--text-muted);">4 Optionen: Leder, Kunstleder, Canvas, Mesh</small></div>
                <button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;display:flex;align-items:center;gap:12px;">
                <span class="material-symbols-rounded" style="color:var(--text-muted);">drag_indicator</span>
                <span style="background:var(--accent);color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;">4</span>
                <div style="flex:1;"><strong>Sohle</strong><br><small style="color:var(--text-muted);">3 Optionen</small></div>
                <button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;display:flex;align-items:center;gap:12px;">
                <span class="material-symbols-rounded" style="color:var(--text-muted);">drag_indicator</span>
                <span style="background:var(--accent);color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;">5</span>
                <div style="flex:1;"><strong>Personalisierung</strong><br><small style="color:var(--text-muted);">Text, Initialen, Grafik</small></div>
                <button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button>
            </div>
            <button class="btn" style="align-self:flex-start;"><span class="material-symbols-rounded">add</span> Schritt hinzufügen</button>
        </div>
    </div>
</div>
