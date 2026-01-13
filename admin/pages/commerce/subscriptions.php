<?php /** Commerce - Abonnements */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Abonnements</h1>
        <p class="page-subtitle">Abo-Produkte und wiederkehrende Zahlungen</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=commerce/subscription_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span> Abo-Produkt erstellen</a>
    </div>
</div>

<div class="kpi-grid">
    <a href="?page=commerce/subscriptions" class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Aktive Abonnements</span></div>
        <div class="kpi-value">234</div>
        <div class="kpi-change positive"><span class="material-symbols-rounded">trending_up</span>+18 diesen Monat</div>
    </a>
    <a href="?page=reports/revenue" class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">MRR</span></div>
        <div class="kpi-value">€4.680</div>
    </a>
    <a href="?page=commerce/subscriptions" class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Churn Rate</span></div>
        <div class="kpi-value">2,3%</div>
    </a>
    <a href="?page=commerce/subscriptions" class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Avg. Abo-Dauer</span></div>
        <div class="kpi-value">8,4 Monate</div>
    </a>
</div>

<div class="card">
    <div class="card-header"><h3>Abo-Produkte</h3></div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>Produkt</th><th>Preis</th><th>Intervall</th><th>Abonnenten</th><th>Status</th><th>Aktionen</th></tr></thead>
            <tbody>
                <tr>
                    <td><a href="?page=commerce/subscription_detail&id=1"><strong>Premium Mitgliedschaft</strong></a></td>
                    <td>€19,99</td>
                    <td>Monatlich</td>
                    <td>156</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=commerce/subscription_detail&id=1" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=commerce/subscription_detail&id=2"><strong>Beauty Box</strong></a></td>
                    <td>€29,99</td>
                    <td>Monatlich</td>
                    <td>48</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=commerce/subscription_detail&id=2" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
                <tr>
                    <td><a href="?page=commerce/subscription_detail&id=3"><strong>Jahres-Abo</strong></a></td>
                    <td>€199,00</td>
                    <td>Jährlich</td>
                    <td>30</td>
                    <td><span class="badge badge-success">Aktiv</span></td>
                    <td class="table-actions"><a href="?page=commerce/subscription_detail&id=3" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Abo-Einstellungen</h3></div>
    <div class="card-body">
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Kunden können Abo pausieren</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>Kunden können Abo kündigen</span></label></div>
        <div class="form-group"><label class="form-checkbox"><input type="checkbox" checked><span>E-Mail vor Verlängerung senden</span></label></div>
        <div class="form-group">
            <label class="form-label">Tage vor Verlängerung für E-Mail</label>
            <input type="number" class="form-input" value="3" style="width:100px;">
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Einstellungen speichern</button>
    </div>
</div>

<style>
.kpi-card { text-decoration:none; color:inherit; }
.kpi-card:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,0.15); }
</style>
