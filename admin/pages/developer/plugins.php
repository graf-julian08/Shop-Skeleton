<?php /** Developer - Plugins */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Plugins</h1>
        <p class="page-subtitle">Erweiterungen installieren und verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">upload</span> Plugin hochladen</button>
        <button class="btn btn-primary"><span class="material-symbols-rounded">storefront</span> Marketplace</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="installiert">Installiert <span class="badge badge-default" style="margin-left:4px;">4</span></button>
    <button class="tab" data-tab="verfuegbar">Verfügbar</button>
    <button class="tab" data-tab="updates">Updates <span class="badge badge-warning" style="margin-left:4px;">2</span></button>
</div>

<!-- Tab: Installiert -->
<div data-tab-content="installiert">
    <div class="card">
        <div class="card-header"><h3>Installierte Plugins</h3></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Plugin</th><th>Version</th><th>Autor</th><th>Status</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>Advanced SEO</strong><br><small style="color:var(--text-muted);">Erweiterte SEO-Funktionen</small></td>
                        <td>2.1.0</td>
                        <td>Official</td>
                        <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">settings</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Multi-Currency</strong><br><small style="color:var(--text-muted);">Mehrere Währungen anbieten</small></td>
                        <td>1.5.2</td>
                        <td>Official</td>
                        <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">settings</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>Social Login</strong><br><small style="color:var(--text-muted);">Login via Google, Facebook, Apple</small></td>
                        <td>3.0.1</td>
                        <td>ThirdParty</td>
                        <td><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">settings</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                    <tr>
                        <td><strong>PDF Rechnungen</strong><br><small style="color:var(--text-muted);">Automatische Rechnungsgenerierung</small></td>
                        <td>1.2.0</td>
                        <td>Official</td>
                        <td><label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label></td>
                        <td class="table-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">settings</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Verfügbar -->
<div data-tab-content="verfuegbar" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Verfügbare Plugins aus dem Marketplace</h3></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:16px;">
                <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;">
                    <strong>Live Chat</strong><br>
                    <small style="color:var(--text-muted);">Echtzeit-Kundensupport</small>
                    <p style="margin:12px 0;font-size:13px;">Integrieren Sie einen Live-Chat für direkten Kundenkontakt.</p>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:var(--success);font-weight:600;">Kostenlos</span>
                        <button class="btn btn-sm btn-primary">Installieren</button>
                    </div>
                </div>
                <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;">
                    <strong>Wishlist Pro</strong><br>
                    <small style="color:var(--text-muted);">Erweiterte Wunschlisten</small>
                    <p style="margin:12px 0;font-size:13px;">Lassen Sie Kunden Produkte auf Wunschlisten speichern und teilen.</p>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-weight:600;">€29</span>
                        <button class="btn btn-sm btn-primary">Installieren</button>
                    </div>
                </div>
                <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);padding:16px;">
                    <strong>Affiliate System</strong><br>
                    <small style="color:var(--text-muted);">Partner-Programm</small>
                    <p style="margin:12px 0;font-size:13px;">Aufbau eines Affiliate-Programms mit Tracking und Provisionen.</p>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-weight:600;">€49</span>
                        <button class="btn btn-sm btn-primary">Installieren</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Updates -->
<div data-tab-content="updates" style="display:none;">
    <div class="alert alert-info">
        <span class="material-symbols-rounded">update</span>
        <div class="alert-content"><strong>2 Updates verfügbar</strong><br>Es stehen Updates für Ihre installierten Plugins bereit.</div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Verfügbare Updates</h3><button class="btn btn-sm btn-primary">Alle aktualisieren</button></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Plugin</th><th>Aktuelle Version</th><th>Neue Version</th><th>Änderungen</th><th>Aktionen</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>Advanced SEO</strong></td>
                        <td>2.1.0</td>
                        <td><span class="badge badge-success">2.2.0</span></td>
                        <td>Neue Schema-Typen, Performance-Verbesserungen</td>
                        <td class="table-actions"><button class="btn btn-sm btn-primary">Update</button></td>
                    </tr>
                    <tr>
                        <td><strong>Multi-Currency</strong></td>
                        <td>1.5.2</td>
                        <td><span class="badge badge-success">1.6.0</span></td>
                        <td>5 neue Währungen, Bugfixes</td>
                        <td class="table-actions"><button class="btn btn-sm btn-primary">Update</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
