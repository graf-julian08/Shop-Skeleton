<?php /** 
  * ============================================
  * Kunden - Kundenliste (100% Funktional)
  * ============================================
  * Dynamische Kundenverwaltung mit Datenbankanbindung
  * Features: KPIs, Search, Filter, Export, Edit, Delete
  * ============================================
  */

// Load shop currency from config
global $database;
require_once __DIR__ . '/../../includes/Database.php';

if (is_array($database)) {
    Database::configure($database);
}

// Get shop default currency
$shopCurrency = Database::fetch("SELECT default_currency FROM shops WHERE id = 1");
$currencyCode = $shopCurrency['default_currency'] ?? 'EUR';

// Get currency symbol
$currencySymbols = [
    'EUR' => '€',
    'USD' => '$',
    'GBP' => '£',
    'CHF' => 'CHF',
    'JPY' => '¥',
    'CNY' => '¥',
    'AUD' => 'A$',
    'CAD' => 'C$'
];
$currencySymbol = $currencySymbols[$currencyCode] ?? $currencyCode;
?>
<style>
    .customer-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }

    .customer-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .customer-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .customer-email {
        color: var(--text-muted);
        font-size: 12px;
    }

    /* Use global .filters from admin.css instead of .filter-row */

    /* Currency dropdown in KPI card */
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

    .currency-dropdown:hover {
        border-color: var(--accent);
    }

    .currency-dropdown:focus {
        outline: none;
        border-color: var(--accent);
    }

    .export-dropdown {
        position: relative;
        display: inline-block;
    }

    .export-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        z-index: 100;
        min-width: 160px;
        display: none;
    }

    .export-menu.show {
        display: block;
    }

    .export-menu a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        color: var(--text-primary);
        text-decoration: none;
        transition: background 0.2s;
    }

    .export-menu a:hover {
        background: var(--hover-bg);
    }

    .bulk-actions {
        display: none;
        gap: 8px;
        align-items: center;
    }

    .bulk-actions.show {
        display: flex;
    }

    .selected-count {
        font-size: 13px;
        color: var(--text-muted);
    }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
    }

    .pagination-info {
        color: var(--text-muted);
        font-size: 13px;
    }

    .pagination-buttons {
        display: flex;
        gap: 8px;
    }

    .pagination-buttons button {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: var(--card-bg);
        color: var(--text-primary);
        cursor: pointer;
    }

    .pagination-buttons button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-buttons button:hover:not(:disabled) {
        background: var(--hover-bg);
    }

    /* Customer Detail Modal */
    .customer-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .customer-modal.show {
        display: flex;
    }

    .customer-modal-content {
        background: var(--card-bg);
        border-radius: 16px;
        width: 100%;
        max-width: 700px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    }

    .customer-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .customer-modal-header h2 {
        margin: 0;
        font-size: 20px;
    }

    .customer-modal-body {
        padding: 24px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        color: var(--text-muted);
        font-size: 13px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--input-bg);
        color: var(--text-primary);
        box-sizing: border-box;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .toggle-switch {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toggle-switch input[type="checkbox"] {
        width: 48px;
        height: 24px;
        appearance: none;
        background: var(--border-color);
        border-radius: 12px;
        cursor: pointer;
        position: relative;
        transition: background 0.2s;
    }

    .toggle-switch input[type="checkbox"]:checked {
        background: var(--success);
    }

    .toggle-switch input[type="checkbox"]::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: transform 0.2s;
    }

    .toggle-switch input[type="checkbox"]:checked::before {
        transform: translateX(24px);
    }

    .customer-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
    }

    .danger-zone {
        margin-top: 24px;
        padding: 16px;
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid var(--danger);
        border-radius: 8px;
    }

    .danger-zone h4 {
        color: var(--danger);
        margin: 0 0 12px 0;
        font-size: 14px;
    }

    .notes-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
</style>

<div class="page-header">
    <div class="page-header-content">
        <h1>Kunden</h1>
        <p class="page-subtitle">Alle Kunden verwalten</p>
    </div>
    <div class="page-header-actions">
        <div class="bulk-actions" id="bulkActions">
            <span class="selected-count"><span id="selectedCount">0</span> ausgewählt</span>
            <button class="btn btn-danger" onclick="CustomerManager.bulkDelete()">
                <span class="material-symbols-rounded">delete</span> Löschen
            </button>
        </div>
        <div class="export-dropdown">
            <button class="btn" onclick="CustomerManager.toggleExportMenu()">
                <span class="material-symbols-rounded">download</span> Export
            </button>
            <div class="export-menu" id="exportMenu">
                <a href="#" onclick="CustomerManager.exportAll('json'); return false;">
                    <span class="material-symbols-rounded">code</span> JSON Export
                </a>
                <a href="#" onclick="CustomerManager.exportAll('sql'); return false;">
                    <span class="material-symbols-rounded">database</span> SQL Export
                </a>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Gesamt Kunden</span></div>
        <div class="kpi-value" id="kpiTotal">-</div>
        <div class="kpi-change" id="kpiChange">Lädt...</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Aktive Kunden</span></div>
        <div class="kpi-value" id="kpiActive">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header">
            <span class="kpi-title">Durchschn. Kundenwert</span>
            <select id="currencySelect" onchange="CustomerManager.changeCurrency()" class="currency-dropdown">
                <option value="">Laden...</option>
            </select>
        </div>
        <div class="kpi-value" id="kpiAvgValue">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Wiederkaufrate</span></div>
        <div class="kpi-value" id="kpiRepeat">-</div>
    </div>
</div>

<!-- Customer Table -->
<div class="card">
    <div class="card-body">
        <div class="filters">
            <div class="filter-search">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Name oder E-Mail suchen..."
                    oninput="CustomerManager.debounceSearch()">
            </div>
            <select class="filter-select" id="groupFilter" onchange="CustomerManager.loadCustomers()">
                <option value="">Alle Gruppen</option>
            </select>
            <select class="filter-select" id="statusFilter" onchange="CustomerManager.loadCustomers()">
                <option value="">Alle Status</option>
                <option value="active">Aktiv</option>
                <option value="inactive">Inaktiv</option>
            </select>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th style="width:40px;"><input type="checkbox" id="selectAll"
                            onchange="CustomerManager.toggleSelectAll()"></th>
                    <th>Kunde</th>
                    <th>E-Mail</th>
                    <th>Bestellungen</th>
                    <th>Umsatz</th>
                    <th>Gruppe</th>
                    <th>Status</th>
                    <th style="width:80px;">Aktionen</th>
                </tr>
            </thead>
            <tbody id="customersBody">
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px;">
                        <span class="material-symbols-rounded"
                            style="font-size:48px; color:var(--text-muted);">hourglass_top</span>
                        <p style="color:var(--text-muted);">Lade Kunden...</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <div class="pagination-info" id="paginationInfo">-</div>
            <div class="pagination-buttons">
                <button id="prevPage" onclick="CustomerManager.prevPage()" disabled>
                    <span class="material-symbols-rounded">chevron_left</span>
                </button>
                <button id="nextPage" onclick="CustomerManager.nextPage()" disabled>
                    <span class="material-symbols-rounded">chevron_right</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Edit Modal -->
<div class="customer-modal" id="customerModal">
    <div class="customer-modal-content">
        <div class="customer-modal-header">
            <h2 id="modalTitle">Kunde bearbeiten</h2>
            <button class="btn btn-sm" onclick="CustomerManager.closeModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="customer-modal-body">
            <input type="hidden" id="customerId">

            <!-- Stats Row -->
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px;">
                <div style="background:var(--hover-bg); padding:12px; border-radius:8px; text-align:center;">
                    <div style="font-size:20px; font-weight:600;" id="statOrders">0</div>
                    <div style="font-size:12px; color:var(--text-muted);">Bestellungen</div>
                </div>
                <div style="background:var(--hover-bg); padding:12px; border-radius:8px; text-align:center;">
                    <div style="font-size:20px; font-weight:600;" id="statSpent">-</div>
                    <div style="font-size:12px; color:var(--text-muted);">Umsatz</div>
                </div>
                <div style="background:var(--hover-bg); padding:12px; border-radius:8px; text-align:center;">
                    <div style="font-size:20px; font-weight:600;" id="statSince">-</div>
                    <div style="font-size:12px; color:var(--text-muted);">Kunde seit</div>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Vorname</label>
                    <input type="text" id="editFirstName">
                </div>
                <div class="form-group">
                    <label>Nachname</label>
                    <input type="text" id="editLastName">
                </div>
                <div class="form-group">
                    <label>E-Mail</label>
                    <input type="email" id="editEmail">
                </div>
                <div class="form-group">
                    <label>Telefon</label>
                    <input type="text" id="editPhone">
                </div>
                <div class="form-group">
                    <label>Kundengruppe</label>
                    <select id="editGroup"></select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="editStatus">
                        <option value="1">Aktiv</option>
                        <option value="0">Gesperrt</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="toggle-switch">
                    <input type="checkbox" id="editNewsletter">
                    <span>Newsletter abonniert</span>
                </label>
            </div>

            <!-- Notes Section -->
            <div class="notes-section">
                <div class="form-group">
                    <label>Interne Notizen (nur für Admins sichtbar)</label>
                    <textarea id="editNotes" rows="3"
                        placeholder="z.B. Treuer Kunde, bevorzugt Express-Versand..."></textarea>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="danger-zone">
                <h4><span class="material-symbols-rounded" style="vertical-align:middle; font-size:18px;">warning</span>
                    Gefahrenbereich</h4>
                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <button class="btn" id="toggleStatusBtn" onclick="CustomerManager.toggleStatus()">
                        <span class="material-symbols-rounded">block</span> <span id="toggleStatusText">Sperren</span>
                    </button>
                    <button class="btn btn-danger" onclick="CustomerManager.deleteCustomer()">
                        <span class="material-symbols-rounded">delete</span> Kunde löschen
                    </button>
                </div>
            </div>
        </div>
        <div class="customer-modal-footer">
            <button class="btn" onclick="CustomerManager.closeModal()">Abbrechen</button>
            <button class="btn btn-primary" onclick="CustomerManager.saveCustomer()">
                <span class="material-symbols-rounded">save</span> Speichern
            </button>
        </div>
    </div>
</div>

<script>
    const CustomerManager = {
        apiBase: 'api/customers.php',
        productsApiBase: 'api/products.php',
        shopId: 1,
        currencies: [],
        currentCurrency: { code: 'EUR', symbol: '€', exchange_rate: 1.0 },
        selectedDisplayCurrency: null,
        customers: [],
        groups: [],
        currentCustomer: null,
        selectedIds: [],

        // Pagination
        currentPage: 1,
        perPage: 25,
        totalPages: 1,
        total: 0,

        // Search debounce
        searchTimeout: null,

        async init() {
            await this.loadCurrencies();
            await this.loadGroups();
            await this.loadStats();
            await this.loadCustomers();

            // Close modals on outside click
            document.getElementById('customerModal').addEventListener('click', (e) => {
                if (e.target.id === 'customerModal') this.closeModal();
            });

            // Escape key to close modals
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') this.closeModal();
            });

            // Close export menu on outside click
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.export-dropdown')) {
                    document.getElementById('exportMenu').classList.remove('show');
                }
            });
        },

        async loadStats() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    const s = data.stats;
                    document.getElementById('kpiTotal').textContent = s.total_customers.toLocaleString('de-DE');
                    document.getElementById('kpiActive').textContent = s.active_customers.toLocaleString('de-DE');

                    // Average customer value - convert to selected currency
                    const avgValue = this.convertCurrency(s.avg_customer_value);
                    document.getElementById('kpiAvgValue').textContent = this.formatPrice(avgValue);

                    document.getElementById('kpiRepeat').textContent = `${s.repeat_rate}%`;

                    // Monthly change
                    const changeEl = document.getElementById('kpiChange');
                    if (s.monthly_change >= 0) {
                        changeEl.className = 'kpi-change positive';
                        changeEl.innerHTML = `<span class="material-symbols-rounded">trending_up</span>+${s.new_this_month} diesen Monat`;
                    } else {
                        changeEl.className = 'kpi-change negative';
                        changeEl.innerHTML = `<span class="material-symbols-rounded">trending_down</span>${s.monthly_change} diesen Monat`;
                    }
                }
            } catch (e) {
                console.error('Stats load error:', e);
            }
        },

        async loadCurrencies() {
            try {
                const res = await fetch(`${this.productsApiBase}?action=get_shop_currency&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    this.currencies = data.currencies || [];
                    this.currentCurrency = data.default_currency || { code: 'EUR', symbol: '€', exchange_rate: 1.0 };
                    this.selectedDisplayCurrency = this.currentCurrency.code;
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
            this.selectedDisplayCurrency = select.value;
            // Update the current currency object
            const selected = this.currencies.find(c => c.code === select.value);
            if (selected) {
                this.currentCurrency = selected;
            }
            // Reload data with new currency
            this.loadStats();
            this.loadCustomers();
        },

        // Convert price from base currency (EUR) to selected currency
        convertCurrency(amount) {
            if (!this.currentCurrency || !this.currentCurrency.exchange_rate) return amount;
            return parseFloat(amount) * parseFloat(this.currentCurrency.exchange_rate);
        },

        // Format price with current currency symbol
        formatPrice(amount) {
            const symbol = this.currentCurrency?.symbol || '€';
            return `${symbol}${parseFloat(amount).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        },

        async loadGroups() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_groups&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    this.groups = data.groups;

                    // Populate filter dropdown
                    const filterSelect = document.getElementById('groupFilter');
                    filterSelect.innerHTML = '<option value="">Alle Gruppen</option>' +
                        this.groups.map(g => `<option value="${g.id}">${g.name}</option>`).join('');

                    // Populate edit dropdown
                    const editSelect = document.getElementById('editGroup');
                    editSelect.innerHTML = '<option value="">Keine Gruppe</option>' +
                        this.groups.map(g => `<option value="${g.id}">${g.name}</option>`).join('');
                }
            } catch (e) {
                console.error('Groups load error:', e);
            }
        },

        async loadCustomers() {
            const search = document.getElementById('searchInput').value;
            const groupId = document.getElementById('groupFilter').value;
            const status = document.getElementById('statusFilter').value;

            const params = new URLSearchParams({
                action: 'get_customers',
                shop_id: this.shopId,
                search,
                group_id: groupId,
                status,
                page: this.currentPage,
                per_page: this.perPage
            });

            try {
                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();

                if (data.success) {
                    this.customers = data.customers;
                    this.total = data.pagination.total;
                    this.totalPages = data.pagination.total_pages;
                    this.renderCustomers();
                    this.updatePagination();
                }
            } catch (e) {
                console.error('Customers load error:', e);
                this.showToast('Fehler beim Laden', 'error');
            }
        },

        renderCustomers() {
            const tbody = document.getElementById('customersBody');

            if (this.customers.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:40px;">
                <span class="material-symbols-rounded" style="font-size:48px; color:var(--text-muted);">person_off</span>
                <p style="color:var(--text-muted);">Keine Kunden gefunden</p>
            </td></tr>`;
                return;
            }

            tbody.innerHTML = this.customers.map(c => {
                const initials = this.getInitials(c.full_name);
                const isChecked = this.selectedIds.includes(c.id) ? 'checked' : '';

                // Color-coded group badges
                let groupBadge;
                if (c.group_code === 'vip') {
                    groupBadge = `<span class="badge" style="background:linear-gradient(135deg, #FFD700, #FFA500); color:#333;">${c.group_name}</span>`;
                } else if (c.group_code === 'wholesale') {
                    groupBadge = `<span class="badge" style="background:linear-gradient(135deg, #9B59B6, #8E44AD); color:#fff;">${c.group_name}</span>`;
                } else if (c.group_name) {
                    groupBadge = `<span class="badge badge-info">${c.group_name}</span>`;
                } else {
                    groupBadge = '<span class="badge badge-default">Standard</span>';
                }

                const statusBadge = c.is_active
                    ? '<span class="badge badge-success">Aktiv</span>'
                    : '<span class="badge badge-danger">Gesperrt</span>';

                return `
                <tr>
                    <td><input type="checkbox" data-id="${c.id}" ${isChecked} onchange="CustomerManager.toggleSelect(${c.id})"></td>
                    <td>
                        <div class="customer-info">
                            <div class="customer-avatar">${initials}</div>
                            <div>
                                <div class="customer-name">${this.escapeHtml(c.full_name)}</div>
                                <div class="customer-email">${this.escapeHtml(c.email)}</div>
                            </div>
                        </div>
                    </td>
                    <td>${this.escapeHtml(c.email)}</td>
                    <td>${c.orders_count}</td>
                    <td>${this.formatPrice(this.convertCurrency(c.total_spent))}</td>
                    <td>${groupBadge}</td>
                    <td>${statusBadge}</td>
                    <td class="table-actions">
                        <a href="?page=customers/customer_edit&id=${c.id}" class="btn btn-sm" title="Bearbeiten">
                            <span class="material-symbols-rounded">edit</span>
                        </a>
                    </td>
                </tr>
            `;
            }).join('');
        },

        getInitials(name) {
            if (!name) return '?';
            const parts = name.split(' ').filter(p => p.length > 0);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        },

        updatePagination() {
            const start = (this.currentPage - 1) * this.perPage + 1;
            const end = Math.min(this.currentPage * this.perPage, this.total);

            document.getElementById('paginationInfo').textContent =
                this.total > 0 ? `${start}-${end} von ${this.total} Kunden` : 'Keine Kunden';

            document.getElementById('prevPage').disabled = this.currentPage <= 1;
            document.getElementById('nextPage').disabled = this.currentPage >= this.totalPages;
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadCustomers();
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.loadCustomers();
            }
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentPage = 1;
                this.loadCustomers();
            }, 300);
        },

        toggleSelect(id) {
            const idx = this.selectedIds.indexOf(id);
            if (idx >= 0) {
                this.selectedIds.splice(idx, 1);
            } else {
                this.selectedIds.push(id);
            }
            this.updateBulkActions();
        },

        toggleSelectAll() {
            const selectAll = document.getElementById('selectAll').checked;
            if (selectAll) {
                this.selectedIds = this.customers.map(c => c.id);
            } else {
                this.selectedIds = [];
            }
            this.renderCustomers();
            this.updateBulkActions();
        },

        updateBulkActions() {
            const bulkActions = document.getElementById('bulkActions');
            const count = this.selectedIds.length;

            if (count > 0) {
                bulkActions.classList.add('show');
                document.getElementById('selectedCount').textContent = count;
            } else {
                bulkActions.classList.remove('show');
            }
        },

        toggleExportMenu() {
            document.getElementById('exportMenu').classList.toggle('show');
        },

        exportAll(format) {
            window.location.href = `${this.apiBase}?action=export&shop_id=${this.shopId}&format=${format}`;
            document.getElementById('exportMenu').classList.remove('show');
        },

        async editCustomer(id) {
            try {
                const res = await fetch(`${this.apiBase}?action=get_customer&shop_id=${this.shopId}&id=${id}`);
                const data = await res.json();

                if (!data.success) {
                    this.showToast(data.error || 'Fehler beim Laden', 'error');
                    return;
                }

                this.currentCustomer = data.customer;
                const c = data.customer;

                // Fill form
                document.getElementById('customerId').value = c.id;
                document.getElementById('editFirstName').value = c.first_name || '';
                document.getElementById('editLastName').value = c.last_name || '';
                document.getElementById('editEmail').value = c.email || '';
                document.getElementById('editPhone').value = c.phone || '';
                document.getElementById('editGroup').value = c.customer_group_id || '';
                document.getElementById('editStatus').value = c.is_active ? '1' : '0';
                document.getElementById('editNewsletter').checked = c.subscribed_to_newsletter == 1;
                document.getElementById('editNotes').value = c.admin_notes || '';

                // Stats
                document.getElementById('statOrders').textContent = c.orders_count || 0;
                document.getElementById('statSpent').textContent =
                    `${this.currencySymbol}${parseFloat(c.total_spent || 0).toLocaleString('de-DE', { minimumFractionDigits: 2 })}`;
                document.getElementById('statSince').textContent =
                    c.created_at ? new Date(c.created_at).toLocaleDateString('de-DE') : '-';

                // Toggle status button text
                document.getElementById('toggleStatusText').textContent = c.is_active ? 'Sperren' : 'Aktivieren';

                // Modal title
                document.getElementById('modalTitle').textContent = c.full_name || 'Kunde bearbeiten';

                // Show modal
                document.getElementById('customerModal').classList.add('show');

            } catch (e) {
                console.error('Customer load error:', e);
                this.showToast('Fehler beim Laden', 'error');
            }
        },

        closeModal() {
            document.getElementById('customerModal').classList.remove('show');
            this.currentCustomer = null;
        },

        async saveCustomer() {
            const id = document.getElementById('customerId').value;
            const notes = document.getElementById('editNotes').value;

            const formData = new FormData();
            formData.append('action', 'update_customer');
            formData.append('shop_id', this.shopId);
            formData.append('id', id);
            formData.append('first_name', document.getElementById('editFirstName').value);
            formData.append('last_name', document.getElementById('editLastName').value);
            formData.append('email', document.getElementById('editEmail').value);
            formData.append('phone', document.getElementById('editPhone').value);
            formData.append('customer_group_id', document.getElementById('editGroup').value);
            formData.append('is_active', document.getElementById('editStatus').value);
            formData.append('subscribed_to_newsletter', document.getElementById('editNewsletter').checked ? 1 : 0);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    // Also save notes
                    if (notes !== (this.currentCustomer?.admin_notes || '')) {
                        await this.saveNotes(id, notes);
                    }
                    this.showToast('Kunde aktualisiert', 'success');
                    this.closeModal();
                    this.loadCustomers();
                    this.loadStats();
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) {
                console.error('Save error:', e);
                this.showToast('Fehler beim Speichern', 'error');
            }
        },

        async saveNotes(id, notes) {
            const formData = new FormData();
            formData.append('action', 'update_notes');
            formData.append('shop_id', this.shopId);
            formData.append('id', id);
            formData.append('notes', notes);

            try {
                await fetch(this.apiBase, { method: 'POST', body: formData });
            } catch (e) {
                console.error('Notes save error:', e);
            }
        },

        async toggleStatus() {
            const id = document.getElementById('customerId').value;
            const isCurrentlyActive = this.currentCustomer?.is_active == 1;
            const action = isCurrentlyActive ? 'sperren' : 'aktivieren';

            // Use custom modal instead of confirm
            if (!await this.confirmAction(`Kunde wirklich ${action}?`)) return;

            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('shop_id', this.shopId);
            formData.append('id', id);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.closeModal();
                    this.loadCustomers();
                    this.loadStats();
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) {
                console.error('Toggle status error:', e);
                this.showToast('Fehler', 'error');
            }
        },

        async deleteCustomer() {
            const id = document.getElementById('customerId').value;
            const name = this.currentCustomer?.full_name || 'Dieser Kunde';

            if (!await this.confirmAction(`"${name}" wirklich löschen?\n\nDiese Aktion kann nicht rückgängig gemacht werden.`)) return;

            const formData = new FormData();
            formData.append('action', 'delete_customer');
            formData.append('shop_id', this.shopId);
            formData.append('id', id);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Kunde gelöscht', 'success');
                    this.closeModal();
                    this.loadCustomers();
                    this.loadStats();
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) {
                console.error('Delete error:', e);
                this.showToast('Fehler beim Löschen', 'error');
            }
        },

        async bulkDelete() {
            if (this.selectedIds.length === 0) return;

            if (!await this.confirmAction(`${this.selectedIds.length} Kunden wirklich löschen?\n\nDiese Aktion kann nicht rückgängig gemacht werden.`)) return;

            let successCount = 0;
            for (const id of this.selectedIds) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'delete_customer');
                    formData.append('shop_id', this.shopId);
                    formData.append('id', id);

                    const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                    const data = await res.json();
                    if (data.success) successCount++;
                } catch (e) {
                    console.error('Bulk delete error:', e);
                }
            }

            this.showToast(`${successCount} Kunden gelöscht`, 'success');
            this.selectedIds = [];
            this.updateBulkActions();
            this.loadCustomers();
            this.loadStats();
        },

        // Custom confirm dialog (replaces native confirm which was buggy)
        confirmAction(message) {
            return new Promise((resolve) => {
                const overlay = document.createElement('div');
                overlay.style.cssText = `
                position: fixed; inset: 0; background: rgba(0,0,0,0.6);
                backdrop-filter: blur(4px); z-index: 2000;
                display: flex; align-items: center; justify-content: center;
            `;

                const modal = document.createElement('div');
                modal.style.cssText = `
                background: var(--card-bg); border-radius: 12px; padding: 24px;
                max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            `;

                modal.innerHTML = `
                <p style="margin:0 0 20px; white-space:pre-line;">${message}</p>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button class="btn" id="confirmCancel">Abbrechen</button>
                    <button class="btn btn-danger" id="confirmOk">Bestätigen</button>
                </div>
            `;

                overlay.appendChild(modal);
                document.body.appendChild(overlay);

                modal.querySelector('#confirmCancel').onclick = () => {
                    document.body.removeChild(overlay);
                    resolve(false);
                };

                modal.querySelector('#confirmOk').onclick = () => {
                    document.body.removeChild(overlay);
                    resolve(true);
                };

                overlay.onclick = (e) => {
                    if (e.target === overlay) {
                        document.body.removeChild(overlay);
                        resolve(false);
                    }
                };
            });
        },

        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        showToast(message, type = 'info') {
            // Use existing toast system if available
            if (typeof showToast === 'function') {
                showToast(message, type);
                return;
            }

            // Fallback toast
            const toast = document.createElement('div');
            toast.style.cssText = `
            position: fixed; bottom: 20px; right: 20px;
            padding: 12px 20px; border-radius: 8px;
            color: white; font-weight: 500; z-index: 9999;
            animation: slideIn 0.3s ease;
            background: ${type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : 'var(--primary)'};
        `;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => toast.remove(), 3000);
        }
    };

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => CustomerManager.init());
</script>