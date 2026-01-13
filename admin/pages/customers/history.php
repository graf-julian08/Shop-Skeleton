<?php /** Kunden - Historie */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kundenhistorie</h1>
        <p class="page-subtitle">Aktivitäten und Bestellhistorie</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="alle">Alle Aktivitäten</button>
    <button class="tab" data-tab="bestellungen">Bestellungen</button>
    <button class="tab" data-tab="logins">Logins</button>
    <button class="tab" data-tab="support">Support</button>
</div>

<!-- Tab: Alle Aktivitäten -->
<div data-tab-content="alle">
    <div class="card">
        <div class="card-body">
            <div class="filters">
                <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Kunde suchen..."></div>
                <select class="filter-select"><option>Alle Aktivitäten</option><option>Bestellung</option><option>Login</option><option>Profil</option><option>Support</option></select>
                <select class="filter-select"><option>Letzte 7 Tage</option><option>Letzte 30 Tage</option><option>Dieses Jahr</option><option>Alle</option></select>
            </div>
            <div class="timeline" style="margin-top:20px;">
                <div class="timeline-item"><div class="timeline-time">Heute, 14:32</div><div class="timeline-content"><strong>Max Mustermann</strong> hat Bestellung <a href="?page=orders/order_detail&id=10045">#10045</a> aufgegeben<br><small style="color:var(--text-muted);">Betrag: €129,99 • 3 Artikel</small></div></div>
                <div class="timeline-item"><div class="timeline-time">Heute, 12:15</div><div class="timeline-content"><strong>Anna Schmidt</strong> hat sich eingeloggt<br><small style="color:var(--text-muted);">IP: 192.168.1.x • Chrome, macOS</small></div></div>
                <div class="timeline-item"><div class="timeline-time">Heute, 11:42</div><div class="timeline-content"><strong>Peter Weber</strong> hat Bestellung <a href="?page=orders/order_detail&id=10044">#10044</a> aufgegeben<br><small style="color:var(--text-muted);">Betrag: €2.450,00 • B2B-Bestellung</small></div></div>
                <div class="timeline-item"><div class="timeline-time">Gestern, 18:20</div><div class="timeline-content"><strong>Lisa Müller</strong> hat ihr Passwort zurückgesetzt</div></div>
                <div class="timeline-item"><div class="timeline-time">Gestern, 16:45</div><div class="timeline-content"><strong>Thomas Koch</strong> hat eine Support-Anfrage erstellt<br><small style="color:var(--text-muted);">Betreff: Frage zur Retoure #RET-089</small></div></div>
            </div>
            <div style="text-align:center;padding-top:20px;"><button class="btn">Mehr laden</button></div>
        </div>
    </div>
</div>

<!-- Tab: Bestellungen -->
<div data-tab-content="bestellungen" style="display:none;">
    <div class="card">
        <div class="card-body">
            <div class="timeline">
                <div class="timeline-item"><div class="timeline-time">Heute, 14:32</div><div class="timeline-content"><strong>Max Mustermann</strong> - Bestellung <a href="?page=orders/order_detail&id=10045">#10045</a><br><small style="color:var(--text-muted);">€129,99 • 3 Artikel • Bezahlt</small></div></div>
                <div class="timeline-item"><div class="timeline-time">Heute, 11:42</div><div class="timeline-content"><strong>Peter Weber</strong> - Bestellung <a href="?page=orders/order_detail&id=10044">#10044</a><br><small style="color:var(--text-muted);">€2.450,00 • B2B • Auf Rechnung</small></div></div>
                <div class="timeline-item"><div class="timeline-time">Gestern, 09:15</div><div class="timeline-content"><strong>Sarah Wagner</strong> - Bestellung <a href="?page=orders/order_detail&id=10043">#10043</a><br><small style="color:var(--text-muted);">€89,00 • 1 Artikel • Versendet</small></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Logins -->
<div data-tab-content="logins" style="display:none;">
    <div class="card">
        <div class="card-body">
            <div class="timeline">
                <div class="timeline-item"><div class="timeline-time">Heute, 12:15</div><div class="timeline-content"><strong>Anna Schmidt</strong><br><small style="color:var(--text-muted);">IP: 192.168.1.x • Chrome, macOS</small></div></div>
                <div class="timeline-item"><div class="timeline-time">Heute, 09:30</div><div class="timeline-content"><strong>Max Mustermann</strong><br><small style="color:var(--text-muted);">IP: 192.168.2.x • Safari, iOS</small></div></div>
                <div class="timeline-item"><div class="timeline-time">Gestern, 16:00</div><div class="timeline-content"><strong>Peter Weber</strong><br><small style="color:var(--text-muted);">IP: 10.0.0.x • Firefox, Windows</small></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Support -->
<div data-tab-content="support" style="display:none;">
    <div class="card">
        <div class="card-body">
            <div class="timeline">
                <div class="timeline-item"><div class="timeline-time">Gestern, 16:45</div><div class="timeline-content"><strong>Thomas Koch</strong> - Neue Anfrage<br><small style="color:var(--text-muted);">Betreff: Frage zur Retoure #RET-089</small><br><span class="badge badge-warning" style="margin-top:8px;">Offen</span></div></div>
                <div class="timeline-item"><div class="timeline-time">05.01.2026</div><div class="timeline-content"><strong>Lisa Müller</strong> - Anfrage beantwortet<br><small style="color:var(--text-muted);">Betreff: Lieferstatus anfragen</small><br><span class="badge badge-success" style="margin-top:8px;">Gelöst</span></div></div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-item { border-left:2px solid var(--border-color); padding:0 0 20px 20px; margin-left:10px; position:relative; }
.timeline-item::before { content:''; position:absolute; left:-5px; top:0; width:8px; height:8px; background:var(--accent); border-radius:50%; }
.timeline-time { font-size:12px; color:var(--text-muted); margin-bottom:4px; }
</style>
