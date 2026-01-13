<?php /** Shop - Personalisierung - Vollständig funktionsfähig */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Personalisierung</h1>
        <p class="page-subtitle">Empfehlungen und personalisierte Inhalte verwalten</p>
    </div>
</div>

<!-- KPI Cards - Echte Daten aus DB -->
<div class="kpi-grid" id="kpiGrid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Empfehlungs-Klicks (7 Tage)</span></div>
        <div class="kpi-value" id="kpiClicks">-</div>
        <div class="kpi-change" id="kpiClicksTrend">Lade...</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Conversion-Rate</span></div>
        <div class="kpi-value" id="kpiConversion">-</div>
        <div class="kpi-change" id="kpiConversions">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Aktive Regeln</span></div>
        <div class="kpi-value" id="kpiActiveRules">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Produkt-Views (7 Tage)</span></div>
        <div class="kpi-value" id="kpiViews">-</div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs">
    <button class="tab active" data-tab="rules">Empfehlungsregeln</button>
    <button class="tab" data-tab="settings">Einstellungen</button>
</div>

<!-- Tab: Empfehlungsregeln -->
<div data-tab-content="rules">
    <div class="card">
        <div class="card-header">
            <h3>Empfehlungsregeln</h3>
            <button class="btn btn-sm btn-primary" onclick="Personalization.showRuleModal()">
                <span class="material-symbols-rounded">add</span> Regel hinzufügen
            </button>
        </div>
        <div class="card-body">
            <table class="table" id="rulesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Typ</th>
                        <th>Position</th>
                        <th>Produkte</th>
                        <th>Klicks (7T)</th>
                        <th>Status</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody id="rulesTableBody">
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">
                            <span class="material-symbols-rounded spinning">sync</span> Lade Regeln...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Einstellungen -->
<div data-tab-content="settings" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>Personalisierungs-Einstellungen</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="settingTrackingEnabled" checked>
                    <span>Tracking aktivieren</span>
                </label>
                <p class="form-hint" style="margin-left:28px;">Erfasst Produktaufrufe und Klicks auf Empfehlungen für
                    Analysen.</p>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="settingShowOnMobile" checked>
                    <span>Empfehlungen auf Mobile anzeigen</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="settingLazyLoad" checked>
                    <span>Lazy-Loading für Empfehlungen</span>
                </label>
                <p class="form-hint" style="margin-left:28px;">Lädt Empfehlungen erst wenn sie im sichtbaren Bereich
                    sind.</p>
            </div>
            <div class="form-row" style="margin-top:20px;">
                <div class="form-group">
                    <label class="form-label">Cookie-Lebensdauer (Tage)</label>
                    <input type="number" class="form-input" id="settingCookieLifetime" value="30" min="1" max="365"
                        style="max-width:150px;">
                    <p class="form-hint">Wie lange "Kürzlich angesehen" Daten gespeichert werden.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Standard-Produktanzahl</label>
                    <input type="number" class="form-input" id="settingDefaultCount" value="4" min="1" max="20"
                        style="max-width:150px;">
                    <p class="form-hint">Standardanzahl angezeigter Produkte pro Widget.</p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn" onclick="Personalization.confirmClearTracking()">
                <span class="material-symbols-rounded">delete_sweep</span> Tracking-Daten löschen
            </button>
            <button class="btn btn-primary" onclick="Personalization.saveSettings()">
                <span class="material-symbols-rounded">save</span> Einstellungen speichern
            </button>
        </div>
    </div>
</div>

<!-- Rule Modal -->
<div class="modal-backdrop" id="ruleModal" style="display:none;">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header">
            <h3 id="ruleModalTitle">Regel hinzufügen</h3>
            <button class="modal-close" onclick="Personalization.closeRuleModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="ruleId">
            <div class="form-group">
                <label class="form-label">Name *</label>
                <input type="text" class="form-input" id="ruleName" placeholder="z.B. Ähnliche Produkte">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Regel-Typ</label>
                    <select class="form-select" id="ruleType" onchange="Personalization.validateRuleTypePosition()">
                        <option value="similar">Ähnliche Produkte</option>
                        <option value="recently_viewed">Kürzlich angesehen</option>
                        <option value="bought_together">Kunden kauften auch</option>
                        <option value="trending">Trending</option>
                        <option value="bestseller">Bestseller</option>
                    </select>
                    <p class="form-hint" id="ruleTypeHint"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Position</label>
                    <select class="form-select" id="rulePosition" onchange="Personalization.validateRuleTypePosition()">
                        <option value="homepage">Homepage</option>
                        <option value="product_page">Produktseite</option>
                        <option value="cart">Warenkorb</option>
                        <option value="category">Kategorieseite</option>
                    </select>
                    <p class="form-hint" id="rulePositionHint"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Anzahl Produkte</label>
                <input type="number" class="form-input" id="ruleProductCount" value="4" min="1" max="12"
                    style="max-width:120px;">
                <p class="form-hint">Wie viele Produkte angezeigt werden (1-12)</p>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="ruleActive" checked>
                    <span>Regel aktiv</span>
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Personalization.closeRuleModal()">Abbrechen</button>
            <button class="btn btn-primary" onclick="Personalization.saveRule()">
                <span class="material-symbols-rounded">save</span> Speichern
            </button>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal-backdrop" id="confirmModal" style="display:none;">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <h3 id="confirmModalTitle">Bestätigung</h3>
            <button class="modal-close" onclick="Personalization.closeConfirmModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body">
            <div style="text-align:center;padding:20px 0;">
                <span class="material-symbols-rounded" id="confirmModalIcon"
                    style="font-size:48px;color:var(--warning);margin-bottom:16px;display:block;">warning</span>
                <p id="confirmModalMessage" style="font-size:16px;margin:0;">Sind Sie sicher?</p>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:12px;">
            <button class="btn" onclick="Personalization.closeConfirmModal()">Abbrechen</button>
            <button class="btn btn-danger" id="confirmModalBtn" onclick="Personalization.executeConfirm()">
                <span class="material-symbols-rounded">delete</span> Löschen
            </button>
        </div>
    </div>
</div>

<script>
    const Personalization = {
        shopId: 1,
        apiBase: 'api/personalization.php',
        rulesData: [],
        confirmCallback: null,

        async init() {
            await Promise.all([
                this.loadStats(),
                this.loadRules(),
                this.loadSettings()
            ]);
            this.initTabs();
        },

        initTabs() {
            const savedTab = localStorage.getItem('personalization_active_tab');
            if (savedTab) {
                const tabBtn = document.querySelector(`.tab[data-tab="${savedTab}"]`);
                if (tabBtn) {
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('[data-tab-content]').forEach(c => c.style.display = 'none');
                    tabBtn.classList.add('active');
                    document.querySelector(`[data-tab-content="${savedTab}"]`).style.display = 'block';
                }
            }

            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('[data-tab-content]').forEach(c => c.style.display = 'none');
                    tab.classList.add('active');
                    document.querySelector(`[data-tab-content="${tab.dataset.tab}"]`).style.display = 'block';
                    localStorage.setItem('personalization_active_tab', tab.dataset.tab);
                });
            });
        },

        // ===== STATS =====
        async loadStats() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success && data.stats) {
                    const s = data.stats;
                    document.getElementById('kpiClicks').textContent = s.clicks_7d.toLocaleString();

                    const trend = s.clicks_trend;
                    const trendEl = document.getElementById('kpiClicksTrend');
                    if (trend > 0) {
                        trendEl.className = 'kpi-change positive';
                        trendEl.innerHTML = `<span class="material-symbols-rounded">trending_up</span>+${trend}% vs. Vorwoche`;
                    } else if (trend < 0) {
                        trendEl.className = 'kpi-change negative';
                        trendEl.innerHTML = `<span class="material-symbols-rounded">trending_down</span>${trend}% vs. Vorwoche`;
                    } else {
                        trendEl.className = 'kpi-change';
                        trendEl.textContent = 'Keine Änderung';
                    }

                    document.getElementById('kpiConversion').textContent = s.conversion_rate + '%';
                    document.getElementById('kpiConversions').textContent = s.conversions_7d + ' Conversions';
                    document.getElementById('kpiActiveRules').textContent = s.active_rules;
                    document.getElementById('kpiViews').textContent = s.product_views_7d.toLocaleString();
                }
            } catch (e) {
                console.error('Error loading stats:', e);
            }
        },

        // ===== RULES =====
        async loadRules() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_rules&shop_id=${this.shopId}`);
                const data = await res.json();

                this.rulesData = data.rules || [];
                const tbody = document.getElementById('rulesTableBody');

                if (data.success && this.rulesData.length > 0) {
                    tbody.innerHTML = this.rulesData.map(r => `
                    <tr data-id="${r.id}">
                        <td><strong>${this.escapeHtml(r.name)}</strong></td>
                        <td><span class="badge badge-default">${this.getRuleTypeLabel(r.rule_type)}</span></td>
                        <td>${this.getPositionLabel(r.position)}</td>
                        <td>${r.product_count}</td>
                        <td>${r.clicks_7d || 0}</td>
                        <td>
                            <span class="status-badge ${parseInt(r.is_active) === 1 ? 'status-active' : 'status-inactive'}" 
                                  onclick="Personalization.toggleRule(${r.id}, ${parseInt(r.is_active) === 1 ? 0 : 1})">
                                ${parseInt(r.is_active) === 1 ? 'Aktiv' : 'Inaktiv'}
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <button class="btn btn-sm btn-icon" onclick="Personalization.editRule(${r.id})" title="Bearbeiten">
                                    <span class="material-symbols-rounded">edit</span>
                                </button>
                                <button class="btn btn-sm btn-icon btn-danger-ghost" onclick="Personalization.confirmDeleteRule(${r.id})" title="Löschen">
                                    <span class="material-symbols-rounded">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
                } else {
                    tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">
                            <span class="material-symbols-rounded" style="font-size:48px;display:block;margin-bottom:12px;">recommend</span>
                            Keine Regeln vorhanden. Erstellen Sie Ihre erste Empfehlungsregel.
                        </td>
                    </tr>
                `;
                }
            } catch (e) {
                console.error('Error loading rules:', e);
            }
        },

        getRuleTypeLabel(type) {
            const labels = {
                'similar': 'Ähnlich',
                'recently_viewed': 'Angesehen',
                'bought_together': 'Gekauft',
                'trending': 'Trending',
                'bestseller': 'Bestseller',
                'custom': 'Custom'
            };
            return labels[type] || type;
        },

        getPositionLabel(pos) {
            const labels = {
                'homepage': 'Homepage',
                'product_page': 'Produktseite',
                'cart': 'Warenkorb',
                'checkout': 'Checkout',
                'category': 'Kategorie'
            };
            return labels[pos] || pos;
        },

        showRuleModal(id = null) {
            document.getElementById('ruleId').value = id || '';
            document.getElementById('ruleModalTitle').textContent = id ? 'Regel bearbeiten' : 'Regel hinzufügen';
            document.getElementById('ruleName').value = '';
            document.getElementById('ruleType').value = 'similar';
            document.getElementById('rulePosition').value = 'product_page';
            document.getElementById('ruleProductCount').value = 4;
            document.getElementById('ruleActive').checked = true;
            document.getElementById('ruleModal').style.display = 'flex';
        },

        closeRuleModal() {
            document.getElementById('ruleModal').style.display = 'none';
        },

        editRule(id) {
            const r = this.rulesData.find(x => x.id == id);
            if (r) {
                document.getElementById('ruleId').value = r.id;
                document.getElementById('ruleModalTitle').textContent = 'Regel bearbeiten';
                document.getElementById('ruleName').value = r.name;
                document.getElementById('ruleType').value = r.rule_type;
                document.getElementById('rulePosition').value = r.position;
                document.getElementById('ruleProductCount').value = r.product_count;
                document.getElementById('ruleActive').checked = parseInt(r.is_active) === 1;
                document.getElementById('ruleModal').style.display = 'flex';
                this.validateRuleTypePosition();
            }
        },

        // Validiert Regel-Typ und Position Kombination
        validateRuleTypePosition() {
            const ruleType = document.getElementById('ruleType').value;
            const position = document.getElementById('rulePosition').value;
            const typeHint = document.getElementById('ruleTypeHint');
            const posHint = document.getElementById('rulePositionHint');
            
            // Reset hints
            typeHint.textContent = '';
            typeHint.style.color = '';
            posHint.textContent = '';
            posHint.style.color = '';
            
            // "Ähnliche Produkte" braucht einen Referenzprodukt - nicht auf Homepage/Kategorie
            if (ruleType === 'similar') {
                if (position === 'homepage' || position === 'category') {
                    posHint.textContent = '⚠️ "Ähnliche Produkte" braucht ein Referenzprodukt - nur auf Produktseite oder Warenkorb sinnvoll';
                    posHint.style.color = 'var(--warning)';
                    return false;
                }
                typeHint.textContent = 'Zeigt Produkte aus derselben Kategorie';
            }
            
            // "Kürzlich angesehen" Hinweis
            if (ruleType === 'recently_viewed') {
                typeHint.textContent = 'Zeigt nur wenn User bereits Produkte angeschaut hat';
            }
            
            // "Kunden kauften auch" nur auf Produktseite/Warenkorb
            if (ruleType === 'bought_together') {
                if (position === 'homepage') {
                    posHint.textContent = '⚠️ "Kunden kauften auch" funktioniert besser auf Produktseite oder Warenkorb';
                    posHint.style.color = 'var(--warning)';
                }
            }
            
            return true;
        },

        async saveRule() {
            const id = document.getElementById('ruleId').value;
            const name = document.getElementById('ruleName').value.trim();
            const ruleType = document.getElementById('ruleType').value;
            const position = document.getElementById('rulePosition').value;

            if (!name) {
                this.showToast('Bitte Name eingeben!', 'error');
                return;
            }

            // Validiere Typ + Position Kombination
            if (ruleType === 'similar' && (position === 'homepage' || position === 'category')) {
                this.showToast('"Ähnliche Produkte" ist auf Homepage/Kategorie nicht möglich!', 'error');
                return;
            }

            // Produktanzahl validieren (1-12)
            let productCount = parseInt(document.getElementById('ruleProductCount').value);
            if (isNaN(productCount) || productCount < 1) productCount = 1;
            if (productCount > 12) productCount = 12;
            document.getElementById('ruleProductCount').value = productCount;

            const formData = new FormData();
            formData.append('action', 'save_rule');
            formData.append('shop_id', this.shopId);
            if (id) formData.append('id', id);
            formData.append('name', name);
            formData.append('rule_type', ruleType);
            formData.append('position', position);
            formData.append('product_count', productCount);
            formData.append('is_active', document.getElementById('ruleActive').checked ? 1 : 0);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.closeRuleModal();
                    await this.loadRules();
                    await this.loadStats();
                } else {
                    this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        async toggleRule(id, active) {
            const formData = new FormData();
            formData.append('action', 'toggle_rule');
            formData.append('shop_id', this.shopId);
            formData.append('id', id);
            formData.append('is_active', active);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                this.showToast(data.message, data.success ? 'success' : 'error');
                await this.loadRules();
                await this.loadStats();
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        confirmDeleteRule(id) {
            this.showConfirmModal(
                'Regel löschen',
                'Möchten Sie diese Regel wirklich löschen? Alle zugehörigen Tracking-Daten werden ebenfalls gelöscht.',
                'delete',
                'Löschen',
                () => this.deleteRule(id)
            );
        },

        async deleteRule(id) {
            const formData = new FormData();
            formData.append('action', 'delete_rule');
            formData.append('shop_id', this.shopId);
            formData.append('id', id);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Regel gelöscht!', 'success');
                    await this.loadRules();
                    await this.loadStats();
                } else {
                    this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        // ===== SETTINGS =====
        async loadSettings() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_settings&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success && data.settings) {
                    const s = data.settings;
                    document.getElementById('settingTrackingEnabled').checked = s.tracking_enabled;
                    document.getElementById('settingShowOnMobile').checked = s.show_on_mobile;
                    document.getElementById('settingLazyLoad').checked = s.lazy_load;
                    document.getElementById('settingCookieLifetime').value = s.cookie_lifetime_days;
                    document.getElementById('settingDefaultCount').value = s.default_product_count;
                }
            } catch (e) {
                console.error('Error loading settings:', e);
            }
        },

        async saveSettings() {
            const formData = new FormData();
            formData.append('action', 'save_settings');
            formData.append('shop_id', this.shopId);
            formData.append('tracking_enabled', document.getElementById('settingTrackingEnabled').checked ? '1' : '0');
            formData.append('show_on_mobile', document.getElementById('settingShowOnMobile').checked ? '1' : '0');
            formData.append('lazy_load', document.getElementById('settingLazyLoad').checked ? '1' : '0');
            formData.append('cookie_lifetime_days', document.getElementById('settingCookieLifetime').value);
            formData.append('default_product_count', document.getElementById('settingDefaultCount').value);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Einstellungen gespeichert!', 'success');
                } else {
                    this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        confirmClearTracking() {
            this.showConfirmModal(
                'Tracking-Daten löschen',
                'Möchten Sie alle Tracking-Daten (Produkt-Views und Klicks) unwiderruflich löschen? Die Statistiken werden auf 0 zurückgesetzt.',
                'delete_sweep',
                'Daten löschen',
                () => this.clearTracking()
            );
        },

        async clearTracking() {
            const formData = new FormData();
            formData.append('action', 'clear_tracking');
            formData.append('shop_id', this.shopId);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    await this.loadStats();
                } else {
                    this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        // ===== CONFIRM MODAL =====
        showConfirmModal(title, message, icon, btnText, callback) {
            document.getElementById('confirmModalTitle').textContent = title;
            document.getElementById('confirmModalMessage').textContent = message;
            document.getElementById('confirmModalIcon').textContent = icon;
            document.getElementById('confirmModalBtn').innerHTML = `<span class="material-symbols-rounded">${icon}</span> ${btnText}`;
            this.confirmCallback = callback;
            document.getElementById('confirmModal').style.display = 'flex';
        },

        closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
            this.confirmCallback = null;
        },

        executeConfirm() {
            if (this.confirmCallback) {
                this.confirmCallback();
            }
            this.closeConfirmModal();
        },

        // ===== HELPERS =====
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        showToast(message, type = 'info') {
            document.querySelectorAll('.pers-toast').forEach(t => t.remove());

            const toast = document.createElement('div');
            toast.className = `pers-toast pers-toast-${type}`;
            toast.innerHTML = `
            <span class="material-symbols-rounded">${type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info'}</span>
            <span>${message}</span>
        `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('pers-toast-hide');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', () => Personalization.init());
</script>

<style>
    /* ===== MODAL STYLES ===== */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: modalFadeIn 0.2s ease;
    }

    .modal {
        background: var(--bg-secondary, #1a1a1a);
        border: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
        border-radius: 16px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
        animation: modalSlideIn 0.25s ease;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
    }

    .modal-header h3 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .modal-close {
        background: transparent;
        border: none;
        color: var(--text-muted, #888);
        cursor: pointer;
        padding: 6px;
        border-radius: 8px;
        display: flex;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-primary, #fff);
    }

    .modal-body {
        padding: 24px;
        overflow-y: auto;
        max-height: 60vh;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
        background: rgba(0, 0, 0, 0.2);
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .status-active:hover {
        background: rgba(16, 185, 129, 0.25);
    }

    .status-inactive {
        background: rgba(107, 114, 128, 0.15);
        color: #6b7280;
    }

    .status-inactive:hover {
        background: rgba(107, 114, 128, 0.25);
    }

    /* Table Actions */
    .table-actions {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-icon .material-symbols-rounded {
        font-size: 18px;
    }

    .btn-danger-ghost {
        color: var(--error);
        background: transparent;
    }

    .btn-danger-ghost:hover {
        background: rgba(239, 68, 68, 0.1);
    }

    .btn-danger {
        background: var(--error);
        color: white;
        border: none;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    /* Toast */
    .pers-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 14px 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        z-index: 100000;
        animation: persSlideIn 0.3s ease;
        font-weight: 500;
    }

    .pers-toast-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .pers-toast-error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .pers-toast-info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .pers-toast-hide {
        animation: persSlideOut 0.3s ease forwards;
    }

    @keyframes persSlideIn {
        from {
            transform: translateX(120%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes persSlideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(120%);
            opacity: 0;
        }
    }

    @keyframes spinning {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .spinning {
        animation: spinning 1s linear infinite;
    }

    /* Form Row */
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>