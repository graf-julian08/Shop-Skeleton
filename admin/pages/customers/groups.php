<?php /** Kunden - Gruppen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kundengruppen</h1>
        <p class="page-subtitle">Gruppen und Preisregeln verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=customers/group_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span>
            Gruppe erstellen</a>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Aktive Gruppen</span></div>
        <div class="kpi-value" id="kpiGroups">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Kunden in Gruppen</span></div>
        <div class="kpi-value" id="kpiCustomers">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Gesamtumsatz</span>
            <select id="currencySelect" onchange="GroupManager.changeCurrency()" class="currency-dropdown">
                <option value="">Laden...</option>
            </select>
        </div>
        <div class="kpi-value" id="kpiRevenue">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Ø Rabatt</span></div>
        <div class="kpi-value" id="kpiDiscount">-</div>
    </div>
</div>

<!-- Groups Table -->
<div class="card">
    <div class="card-header">
        <h3>Alle Gruppen</h3>
    </div>
    <div class="card-body">
        <table class="table" id="groupsTable">
            <thead>
                <tr>
                    <th>Gruppe</th>
                    <th>Mitglieder</th>
                    <th>Preisregel</th>
                    <th>Umsatz</th>
                    <th>Vorteile</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="groupsBody">
                <tr>
                    <td colspan="6" class="loading-row"><span class="material-symbols-rounded spinning">sync</span> Lade
                        Gruppen...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Gruppe löschen</h3>
            <button class="modal-close" onclick="GroupManager.closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="deleteModalMessage"></p>
            <p class="warning-text"><span class="material-symbols-rounded">warning</span> Alle Mitglieder werden zur
                Standardgruppe verschoben.</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="GroupManager.closeDeleteModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="GroupManager.confirmDelete()">Gruppe löschen</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .kpi-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .currency-dropdown {
        padding: 4px 8px;
        font-size: 11px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        cursor: pointer;
        min-width: 60px;
    }

    .currency-dropdown:hover,
    .currency-dropdown:focus {
        border-color: var(--accent);
        outline: none;
    }

    .loading-row {
        text-align: center;
        padding: 40px !important;
        color: var(--text-muted);
    }

    .spinning {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .group-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .group-code {
        font-size: 12px;
        color: var(--text-muted);
    }

    .group-description {
        font-size: 13px;
        color: var(--text-secondary);
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .benefit-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .benefit-tag {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        background: var(--bg-tertiary);
        color: var(--text-muted);
    }

    .benefit-tag.active {
        background: rgba(var(--success-rgb), 0.2);
        color: var(--success);
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
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<script>
    const GroupManager = {
        apiBase: 'api/customers.php',
        productsApiBase: 'api/products.php',
        shopId: 1,
        groups: [],
        currencies: [],
        currentCurrency: { code: 'EUR', symbol: '€', exchange_rate: 1.0 },
        deleteGroupId: null,
        deleteGroupName: '',

        async init() {
            await this.loadCurrencies();
            await this.loadStats();
            await this.loadGroups();
        },

        async loadCurrencies() {
            try {
                const res = await fetch(`${this.productsApiBase}?action=get_shop_currency&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    this.currencies = data.currencies || [];
                    this.currentCurrency = data.default_currency || { code: 'EUR', symbol: '€', exchange_rate: 1.0 };
                    this.populateCurrencyDropdown();
                }
            } catch (e) {
                console.error('Error loading currencies:', e);
            }
        },

        populateCurrencyDropdown() {
            const select = document.getElementById('currencySelect');
            if (!select || !this.currencies.length) return;

            select.innerHTML = this.currencies.map(c => `
            <option value="${c.code}" ${c.code === this.currentCurrency.code ? 'selected' : ''}>
                ${c.symbol} ${c.code}
            </option>
        `).join('');
        },

        changeCurrency() {
            const select = document.getElementById('currencySelect');
            const selected = this.currencies.find(c => c.code === select.value);
            if (selected) {
                this.currentCurrency = selected;
            }
            this.loadStats();
            this.loadGroups();
        },

        convertCurrency(amount) {
            if (!this.currentCurrency || !this.currentCurrency.exchange_rate) return amount;
            return parseFloat(amount) * parseFloat(this.currentCurrency.exchange_rate);
        },

        formatPrice(amount) {
            const symbol = this.currentCurrency?.symbol || '€';
            return `${symbol}${parseFloat(amount).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        },

        async loadStats() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_group_stats&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    const s = data.stats;
                    document.getElementById('kpiGroups').textContent = s.total_groups;
                    document.getElementById('kpiCustomers').textContent = s.customers_in_groups.toLocaleString('de-DE');
                    document.getElementById('kpiRevenue').textContent = this.formatPrice(this.convertCurrency(s.total_revenue));
                    document.getElementById('kpiDiscount').textContent = s.avg_discount > 0 ? `${s.avg_discount}%` : '-';
                }
            } catch (e) {
                console.error('Stats error:', e);
            }
        },

        async loadGroups() {
            const tbody = document.getElementById('groupsBody');
            tbody.innerHTML = '<tr><td colspan="6" class="loading-row"><span class="material-symbols-rounded spinning">sync</span> Lade Gruppen...</td></tr>';

            try {
                const res = await fetch(`${this.apiBase}?action=get_all_groups&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    this.groups = data.groups;
                    this.renderGroups();
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="loading-row">Fehler beim Laden</td></tr>';
                }
            } catch (e) {
                tbody.innerHTML = '<tr><td colspan="6" class="loading-row">Fehler: ' + e.message + '</td></tr>';
            }
        },

        renderGroups() {
            const tbody = document.getElementById('groupsBody');

            if (this.groups.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="loading-row">Keine Gruppen gefunden</td></tr>';
                return;
            }

            tbody.innerHTML = this.groups.map(g => {
                const discount = parseFloat(g.discount_percent);
                const priceRule = discount > 0
                    ? `<span class="badge badge-success">-${discount}% auf alles</span>`
                    : '<span class="badge badge-default">Standardpreis</span>';

                const benefits = [];
                if (parseInt(g.free_shipping)) benefits.push('<span class="benefit-tag active">Gratis Versand</span>');
                if (parseInt(g.priority_support)) benefits.push('<span class="benefit-tag active">Prio-Support</span>');
                if (parseInt(g.early_access)) benefits.push('<span class="benefit-tag active">Früher Zugang</span>');
                if (benefits.length === 0) benefits.push('<span class="benefit-tag">Keine</span>');

                const revenue = this.formatPrice(this.convertCurrency(g.total_revenue || 0));
                const isDefault = parseInt(g.is_default);

                return `
                <tr>
                    <td>
                        <a href="?page=customers/group_edit&id=${g.id}">
                            <div class="group-name">${this.escapeHtml(g.name)} ${isDefault ? '<span class="badge badge-info">Standard</span>' : ''}</div>
                            <div class="group-code">${this.escapeHtml(g.code)}</div>
                        </a>
                    </td>
                    <td>${parseInt(g.member_count).toLocaleString('de-DE')}</td>
                    <td>${priceRule}</td>
                    <td>${revenue}</td>
                    <td><div class="benefit-tags">${benefits.join('')}</div></td>
                    <td class="table-actions">
                        <a href="?page=customers/group_edit&id=${g.id}" class="btn btn-sm" title="Bearbeiten">
                            <span class="material-symbols-rounded">edit</span>
                        </a>
                        ${!isDefault ? `<button class="btn btn-sm btn-danger-ghost" onclick="GroupManager.deleteGroup(${g.id}, '${this.escapeHtml(g.name)}')" title="Löschen">
                            <span class="material-symbols-rounded">delete</span>
                        </button>` : ''}
                    </td>
                </tr>
            `;
            }).join('');
        },

        deleteGroup(id, name) {
            this.deleteGroupId = id;
            this.deleteGroupName = name;
            document.getElementById('deleteModalMessage').textContent =
                `Möchten Sie die Gruppe "${name}" wirklich löschen?`;
            document.getElementById('deleteModal').style.display = 'flex';
        },

        closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            this.deleteGroupId = null;
            this.deleteGroupName = '';
        },

        async confirmDelete() {
            if (!this.deleteGroupId) return;

            const formData = new FormData();
            formData.append('action', 'delete_group');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.deleteGroupId);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.closeDeleteModal();
                    await this.loadStats();
                    await this.loadGroups();
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

    document.addEventListener('DOMContentLoaded', () => GroupManager.init());
</script>