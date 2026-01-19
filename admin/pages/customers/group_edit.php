<?php
/** Kunden - Gruppe bearbeiten */
$groupId = (int) ($_GET['id'] ?? 0);
if ($groupId <= 0) {
    header('Location: ?page=customers/groups');
    exit;
}
?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=customers/groups">Kundengruppen</a> <span>›</span> <span
                id="breadcrumbName">Laden...</span></nav>
        <h1 id="pageTitle">Gruppe bearbeiten</h1>
        <p class="page-subtitle" id="pageSubtitle">Lade Gruppeninformationen...</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-danger-ghost" id="deleteBtn" onclick="GroupEdit.deleteGroup()" style="display:none;">
            <span class="material-symbols-rounded">delete</span> Löschen
        </button>
        <a href="?page=customers/groups" class="btn">Abbrechen</a>
        <button class="btn btn-primary" onclick="GroupEdit.save()"><span class="material-symbols-rounded">save</span>
            Speichern</button>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" id="statsGrid">
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-rounded">group</span></div>
        <div class="stat-content">
            <div class="stat-value" id="statMembers">-</div>
            <div class="stat-label">Mitglieder</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-rounded">payments</span></div>
        <div class="stat-content">
            <div class="stat-value" id="statRevenue">-</div>
            <div class="stat-label">Gesamtumsatz</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-rounded">shopping_cart</span></div>
        <div class="stat-content">
            <div class="stat-value" id="statAvgValue">-</div>
            <div class="stat-label">Ø Kundenwert</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-rounded">receipt_long</span></div>
        <div class="stat-content">
            <div class="stat-value" id="statAvgOrders">-</div>
            <div class="stat-label">Ø Bestellungen</div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs" id="groupTabs">
    <button class="tab active" data-tab="details" onclick="GroupEdit.switchTab('details')">Grunddaten</button>
    <button class="tab" data-tab="benefits" onclick="GroupEdit.switchTab('benefits')">Vorteile</button>
    <button class="tab" data-tab="automation" onclick="GroupEdit.switchTab('automation')">Automatik</button>
    <button class="tab" data-tab="members" onclick="GroupEdit.switchTab('members')">Mitglieder <span
            class="badge badge-default" id="memberBadge">0</span></button>
</div>

<!-- Tab: Details -->
<div class="tab-content" id="tab-details">
    <div class="card">
        <div class="card-header">
            <h3>Grunddaten</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Gruppenname <span style="color:var(--error)">*</span></label>
                    <input type="text" class="form-input" id="groupName" placeholder="z.B. Premium">
                </div>
                <div class="form-group">
                    <label class="form-label">Gruppen-Code</label>
                    <input type="text" class="form-input" id="groupCode" placeholder="z.B. premium">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Beschreibung</label>
                <textarea class="form-textarea" id="groupDescription" rows="3"
                    placeholder="Beschreibung der Gruppe..."></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Erstellt am</label>
                    <input type="text" class="form-input" id="createdAt" readonly disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Zuletzt geändert</label>
                    <input type="text" class="form-input" id="updatedAt" readonly disabled>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Benefits -->
<div class="tab-content" id="tab-benefits" style="display:none;">
    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h3>Rabatte</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Standardrabatt auf alle Produkte (%)</label>
                    <input type="number" class="form-input" id="discountPercent" min="0" max="100" value="0">
                    <p class="form-hint">Dieser Rabatt wird automatisch auf alle Produkte für Mitglieder dieser Gruppe
                        angewendet.</p>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3>Zusätzliche Vorteile</h3>
            </div>
            <div class="card-body">
                <div class="form-group toggle-row">
                    <label class="form-label">Kostenloser Versand</label>
                    <label class="toggle">
                        <input type="checkbox" id="freeShipping">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="form-group toggle-row">
                    <label class="form-label">Prioritärer Support</label>
                    <label class="toggle">
                        <input type="checkbox" id="prioritySupport">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="form-group toggle-row">
                    <label class="form-label">Frühzeitiger Sale-Zugang</label>
                    <label class="toggle">
                        <input type="checkbox" id="earlyAccess">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Automation -->
<div class="tab-content" id="tab-automation" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>Automatische Zuordnung</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Automatisch zuweisen wenn</label>
                    <select class="form-select" id="autoAssignType" onchange="GroupEdit.toggleThreshold()">
                        <option value="disabled">Deaktiviert</option>
                        <option value="min_spent">Mindestumsatz erreicht</option>
                        <option value="min_orders">Mindestbestellungen erreicht</option>
                    </select>
                </div>
                <div class="form-group" id="thresholdGroup" style="display:none;">
                    <label class="form-label" id="thresholdLabel">Schwellenwert</label>
                    <input type="number" class="form-input" id="autoAssignThreshold" placeholder="z.B. 500" min="0"
                        value="0">
                </div>
            </div>
            <p class="form-hint">Beim Speichern werden alle qualifizierten Kunden automatisch dieser Gruppe zugewiesen.
            </p>
            <button class="btn" id="runAutoAssignBtn" onclick="GroupEdit.runAutoAssign()"
                style="margin-top:16px;display:none;">
                <span class="material-symbols-rounded">auto_mode</span> Jetzt automatisch zuweisen
            </button>
        </div>
    </div>
</div>

<!-- Tab: Members -->
<div class="tab-content" id="tab-members" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>Mitglieder dieser Gruppe</h3>
        </div>
        <div class="card-body">
            <table class="table" id="membersTable">
                <thead>
                    <tr>
                        <th>Kunde</th>
                        <th>E-Mail</th>
                        <th>Bestellungen</th>
                        <th>Umsatz</th>
                        <th>Status</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody id="membersBody">
                    <tr>
                        <td colspan="6" class="loading-row">Lade Mitglieder...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Gruppe löschen</h3>
            <button class="modal-close" onclick="GroupEdit.closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="deleteModalMessage">Möchten Sie diese Gruppe wirklich löschen?</p>
            <p class="warning-text"><span class="material-symbols-rounded">warning</span> Alle Mitglieder werden zur
                Standardgruppe verschoben.</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="GroupEdit.closeDeleteModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="GroupEdit.confirmDelete()">Gruppe löschen</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .breadcrumb {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .breadcrumb a {
        color: var(--accent);
    }

    .form-hint {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(var(--accent-rgb), 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon .material-symbols-rounded {
        color: var(--accent);
        font-size: 24px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
    }

    .tabs {
        display: flex;
        gap: 4px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0;
    }

    .tab {
        padding: 12px 20px;
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -1px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tab.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
    }

    .tab:hover {
        color: var(--text-primary);
    }

    .toggle {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        cursor: pointer;
    }

    .toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--bg-tertiary);
        transition: .3s;
        border-radius: 26px;
        border: 1px solid var(--border-color);
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    .toggle input:checked+.toggle-slider {
        background-color: var(--success);
        border-color: var(--success);
    }

    .toggle input:checked+.toggle-slider:before {
        transform: translateX(22px);
    }

    .toggle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .toggle-row:last-child {
        border-bottom: none;
    }

    .loading-row {
        text-align: center;
        padding: 40px !important;
        color: var(--text-muted);
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        width: 90%;
        max-width: 450px;
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--text-muted);
    }

    .modal-body {
        padding: 24px;
    }

    .warning-text {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--warning);
        font-size: 13px;
        margin-top: 12px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--border);
    }

    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 16px 24px;
        border-radius: var(--radius-md);
        color: white;
        font-weight: 500;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s;
        z-index: 1001;
    }

    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .toast.success {
        background: var(--success);
    }

    .toast.error {
        background: var(--error);
    }

    @media (max-width: 1000px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    const GroupEdit = {
        apiBase: 'api/customers.php',
        productsApiBase: 'api/products.php',
        shopId: 1,
        groupId: <?php echo $groupId; ?>,
        group: null,
        currencies: [],
        currentCurrency: { code: 'EUR', symbol: '€', exchange_rate: 1.0 },
        currentTab: 'details',

        async init() {
            await this.loadCurrencies();
            await this.loadGroup();
        },

        async loadCurrencies() {
            try {
                const res = await fetch(`${this.productsApiBase}?action=get_shop_currency&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    this.currencies = data.currencies || [];
                    this.currentCurrency = data.default_currency || { code: 'EUR', symbol: '€', exchange_rate: 1.0 };
                }
            } catch (e) {
                console.error('Error loading currencies:', e);
            }
        },

        convertCurrency(amount) {
            if (!this.currentCurrency || !this.currentCurrency.exchange_rate) return amount;
            return parseFloat(amount || 0) * parseFloat(this.currentCurrency.exchange_rate);
        },

        formatPrice(amount) {
            const symbol = this.currentCurrency?.symbol || '€';
            return `${symbol}${parseFloat(amount).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        },

        async loadGroup() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_group&shop_id=${this.shopId}&id=${this.groupId}`);
                const data = await res.json();

                if (!data.success) {
                    this.showToast('Gruppe nicht gefunden', 'error');
                    setTimeout(() => window.location.href = '?page=customers/groups', 1500);
                    return;
                }

                this.group = data.group;
                this.renderGroup();
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        renderGroup() {
            const g = this.group;

            // Header
            document.getElementById('breadcrumbName').textContent = g.name;
            document.getElementById('pageTitle').textContent = g.name;
            document.getElementById('pageSubtitle').textContent = `Code: ${g.code}`;

            // Show delete button only for non-default groups
            if (!parseInt(g.is_default)) {
                document.getElementById('deleteBtn').style.display = 'flex';
            }

            // Stats
            document.getElementById('statMembers').textContent = parseInt(g.member_count).toLocaleString('de-DE');
            document.getElementById('statRevenue').textContent = this.formatPrice(this.convertCurrency(g.total_revenue || 0));
            document.getElementById('statAvgValue').textContent = this.formatPrice(this.convertCurrency(g.avg_customer_value || 0));
            document.getElementById('statAvgOrders').textContent = parseFloat(g.avg_orders_per_customer || 0).toFixed(1);

            // Member badge
            document.getElementById('memberBadge').textContent = g.member_count;

            // Form fields
            document.getElementById('groupName').value = g.name || '';
            document.getElementById('groupCode').value = g.code || '';
            document.getElementById('groupDescription').value = g.description || '';
            document.getElementById('discountPercent').value = g.discount_percent || 0;
            document.getElementById('freeShipping').checked = parseInt(g.free_shipping) === 1;
            document.getElementById('prioritySupport').checked = parseInt(g.priority_support) === 1;
            document.getElementById('earlyAccess').checked = parseInt(g.early_access) === 1;
            document.getElementById('autoAssignType').value = g.auto_assign_type || 'disabled';
            document.getElementById('autoAssignThreshold').value = g.auto_assign_threshold || 0;
            document.getElementById('createdAt').value = g.created_at ? new Date(g.created_at).toLocaleString('de-DE') : '-';
            document.getElementById('updatedAt').value = g.updated_at ? new Date(g.updated_at).toLocaleString('de-DE') : '-';

            this.toggleThreshold();
            this.renderMembers();
        },

        renderMembers() {
            const tbody = document.getElementById('membersBody');
            const members = this.group.members || [];

            if (members.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="loading-row">Keine Mitglieder in dieser Gruppe</td></tr>';
                return;
            }

            tbody.innerHTML = members.map(m => {
                const fullName = [m.first_name, m.last_name].filter(Boolean).join(' ') || m.email;
                const status = parseInt(m.is_active)
                    ? '<span class="badge badge-success">Aktiv</span>'
                    : '<span class="badge badge-danger">Inaktiv</span>';

                return `
                <tr>
                    <td><strong>${this.escapeHtml(fullName)}</strong></td>
                    <td>${this.escapeHtml(m.email)}</td>
                    <td>${m.orders_count}</td>
                    <td>${this.formatPrice(this.convertCurrency(m.total_spent || 0))}</td>
                    <td>${status}</td>
                    <td class="table-actions">
                        <a href="?page=customers/customer_edit&id=${m.id}" class="btn btn-sm" title="Bearbeiten">
                            <span class="material-symbols-rounded">edit</span>
                        </a>
                    </td>
                </tr>
            `;
            }).join('');
        },

        switchTab(tab) {
            this.currentTab = tab;

            // Update tab buttons
            document.querySelectorAll('#groupTabs .tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`#groupTabs .tab[data-tab="${tab}"]`).classList.add('active');

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
            document.getElementById(`tab-${tab}`).style.display = 'block';
        },

        toggleThreshold() {
            const type = document.getElementById('autoAssignType').value;
            const group = document.getElementById('thresholdGroup');
            const label = document.getElementById('thresholdLabel');
            const btn = document.getElementById('runAutoAssignBtn');

            if (type === 'disabled') {
                group.style.display = 'none';
                btn.style.display = 'none';
            } else {
                group.style.display = 'block';
                btn.style.display = 'inline-flex';
                label.textContent = type === 'min_spent' ? 'Mindestumsatz (€)' : 'Mindestbestellungen';
            }
        },

        async save() {
            const name = document.getElementById('groupName').value.trim();
            const code = document.getElementById('groupCode').value.trim();
            const description = document.getElementById('groupDescription').value.trim();
            const discountPercent = document.getElementById('discountPercent').value || 0;
            const freeShipping = document.getElementById('freeShipping').checked ? 1 : 0;
            const prioritySupport = document.getElementById('prioritySupport').checked ? 1 : 0;
            const earlyAccess = document.getElementById('earlyAccess').checked ? 1 : 0;
            const autoAssignType = document.getElementById('autoAssignType').value;
            const autoAssignThreshold = document.getElementById('autoAssignThreshold').value || 0;

            if (!name) {
                this.showToast('Bitte geben Sie einen Gruppennamen ein', 'error');
                return;
            }

            if (!code) {
                this.showToast('Bitte geben Sie einen Gruppen-Code ein', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'update_group');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.groupId);
            formData.append('name', name);
            formData.append('code', code);
            formData.append('description', description);
            formData.append('discount_percent', discountPercent);
            formData.append('free_shipping', freeShipping);
            formData.append('priority_support', prioritySupport);
            formData.append('early_access', earlyAccess);
            formData.append('auto_assign_type', autoAssignType);
            formData.append('auto_assign_threshold', autoAssignThreshold);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Änderungen gespeichert!', 'success');
                    await this.loadGroup(); // Reload to show updated stats
                } else {
                    this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        async runAutoAssign() {
            // Just save, which triggers auto-assign
            await this.save();
            this.showToast('Auto-Zuweisung ausgeführt!', 'success');
        },

        deleteGroup() {
            document.getElementById('deleteModalMessage').textContent =
                `Möchten Sie die Gruppe "${this.group.name}" wirklich löschen?`;
            document.getElementById('deleteModal').style.display = 'flex';
        },

        closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        },

        async confirmDelete() {
            const formData = new FormData();
            formData.append('action', 'delete_group');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.groupId);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.closeDeleteModal();
                    setTimeout(() => {
                        window.location.href = '?page=customers/groups';
                    }, 1500);
                } else {
                    this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        },

        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    document.addEventListener('DOMContentLoaded', () => GroupEdit.init());
</script>