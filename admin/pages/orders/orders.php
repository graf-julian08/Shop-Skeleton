<?php /** Bestellungen - Übersicht */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Bestellungen</h1>
        <p class="page-subtitle">Alle Bestellungen verwalten</p>
    </div>
    <div class="page-header-actions">
        <div class="export-dropdown">
            <button class="btn" onclick="OrderManager.toggleExportMenu()"><span
                    class="material-symbols-rounded">download</span> Export</button>
            <div class="export-menu" id="exportMenu">
                <button onclick="OrderManager.exportAs('json')"><span
                        class="material-symbols-rounded">data_object</span> Als JSON</button>
                <button onclick="OrderManager.exportAs('sql')"><span class="material-symbols-rounded">database</span>
                    Als SQL</button>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs" id="orderTabs">
    <button class="tab active" data-status="all">Alle <span class="badge" id="badge-all">0</span></button>
    <button class="tab" data-status="pending">Offen <span class="badge" id="badge-pending">0</span></button>
    <button class="tab" data-status="paid">Bezahlt <span class="badge" id="badge-paid">0</span></button>
    <button class="tab" data-status="shipped">Versendet <span class="badge" id="badge-shipped">0</span></button>
    <button class="tab" data-status="delivered">Abgeschlossen <span class="badge" id="badge-delivered">0</span></button>
</div>

<!-- Filters & Table -->
<div class="card">
    <div class="card-body">
        <div class="filters">
            <div class="filter-search">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Bestellung oder Kunde suchen..."
                    oninput="OrderManager.debounceSearch()">
            </div>
            <select class="filter-select" id="statusFilter" onchange="OrderManager.loadOrders()">
                <option value="all">Alle Status</option>
                <option value="pending">Ausstehend</option>
                <option value="processing">In Bearbeitung</option>
                <option value="shipped">Versendet</option>
                <option value="delivered">Zugestellt</option>
                <option value="cancelled">Storniert</option>
            </select>
            <select class="filter-select" id="paymentFilter" onchange="OrderManager.loadOrders()">
                <option value="all">Alle Zahlungen</option>
                <option value="paid">Bezahlt</option>
                <option value="pending">Ausstehend</option>
                <option value="failed">Fehlgeschlagen</option>
            </select>
            <select class="filter-select" id="periodFilter" onchange="OrderManager.loadOrders()">
                <option value="7d">Letzte 7 Tage</option>
                <option value="today">Heute</option>
                <option value="30d">Letzte 30 Tage</option>
                <option value="year">Dieses Jahr</option>
                <option value="all">Alle</option>
            </select>
            <select class="filter-select" id="currencySelect" onchange="OrderManager.loadOrders()">
                <option value="">Lädt...</option>
            </select>
        </div>

        <table class="table" id="ordersTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" onclick="OrderManager.toggleSelectAll()"></th>
                    <th>Bestellung</th>
                    <th>Datum</th>
                    <th>Kunde</th>
                    <th>Betrag</th>
                    <th>Zahlung</th>
                    <th>Status</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="ordersBody">
                <tr>
                    <td colspan="8" class="loading-cell"><span class="material-symbols-rounded spinning">sync</span>
                        Lade Bestellungen...</td>
                </tr>
            </tbody>
        </table>

        <div class="pagination" id="pagination"></div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .tab {
        padding: 10px 16px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        cursor: pointer;
        font-size: 14px;
        color: var(--text-secondary);
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tab:hover {
        background: var(--bg-tertiary);
    }

    .tab.active {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }

    .tab .badge {
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.2);
    }

    .tab.active .badge {
        background: rgba(255, 255, 255, 0.3);
    }

    .loading-cell {
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

    .order-number {
        font-weight: 600;
        color: var(--accent);
    }

    .customer-link {
        color: var(--accent);
    }

    .customer-link:hover {
        text-decoration: underline;
    }

    .badge-payment {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 10px;
        font-weight: 500;
    }

    .badge-payment.paid {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .badge-payment.pending {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .badge-payment.failed {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .badge-payment.refunded {
        background: rgba(107, 114, 128, 0.15);
        color: #6b7280;
    }

    .badge-status {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 10px;
        font-weight: 500;
    }

    .badge-status.pending {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .badge-status.processing {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }

    .badge-status.shipped {
        background: rgba(139, 92, 246, 0.15);
        color: #8b5cf6;
    }

    .badge-status.delivered {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .badge-status.cancelled {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .badge-status.refunded {
        background: rgba(107, 114, 128, 0.15);
        color: #6b7280;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
    }

    .pagination button {
        padding: 8px 12px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        cursor: pointer;
        color: var(--text-primary);
    }

    .pagination button:hover:not(:disabled) {
        background: var(--bg-tertiary);
    }

    .pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination button.active {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }

    .pagination-info {
        padding: 8px 12px;
        color: var(--text-muted);
        font-size: 13px;
    }

    .export-dropdown {
        position: relative;
    }

    .export-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        padding: 8px 0;
        display: none;
        z-index: 100;
        min-width: 150px;
    }

    .export-menu.show {
        display: block;
    }

    .export-menu button {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 10px 16px;
        background: none;
        border: none;
        color: var(--text-primary);
        cursor: pointer;
        font-size: 14px;
    }

    .export-menu button:hover {
        background: var(--bg-tertiary);
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

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state .material-symbols-rounded {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>

<script>
    const OrderManager = {
        apiBase: 'api/orders.php',
        shopId: 1,
        orders: [],
        currencies: [],
        currentCurrency: 'EUR',
        currentPage: 1,
        totalPages: 1,
        total: 0,
        currentStatus: 'all',
        searchTimeout: null,

        async init() {
            await this.loadCurrencies();
            await this.loadStats();
            await this.generateTestDataIfNeeded();
            await this.loadOrders();
            this.setupTabs();
        },

        async loadCurrencies() {
            try {
                const res = await fetch(`api/products.php?action=get_currencies&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    this.currencies = data.currencies || [];
                    const defaultCurrency = this.currencies.find(c => c.is_default) || this.currencies[0];
                    this.currentCurrency = defaultCurrency?.code || 'EUR';
                    this.populateCurrencyDropdown();
                }
            } catch (e) {
                console.error('Error loading currencies:', e);
            }
        },

        populateCurrencyDropdown() {
            const select = document.getElementById('currencySelect');
            select.innerHTML = this.currencies.map(c =>
                `<option value="${c.code}" ${c.code === this.currentCurrency ? 'selected' : ''}>${c.code} (${c.symbol})</option>`
            ).join('');
        },

        async loadStats() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('badge-all').textContent = data.stats.all || 0;
                    document.getElementById('badge-pending').textContent = data.stats.pending || 0;
                    document.getElementById('badge-paid').textContent = data.stats.payment_paid || 0;
                    document.getElementById('badge-shipped').textContent = data.stats.shipped || 0;
                    document.getElementById('badge-delivered').textContent = data.stats.delivered || 0;
                }
            } catch (e) { console.error('Error loading stats:', e); }
        },

        async generateTestDataIfNeeded() {
            try {
                await fetch(`${this.apiBase}?action=generate_test_data&shop_id=${this.shopId}`);
            } catch (e) { console.error('Error generating test data:', e); }
        },

        setupTabs() {
            document.querySelectorAll('#orderTabs .tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    document.querySelectorAll('#orderTabs .tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    this.currentStatus = tab.dataset.status;
                    this.currentPage = 1;

                    // Sync dropdowns with tab selection
                    const statusFilter = document.getElementById('statusFilter');
                    const paymentFilter = document.getElementById('paymentFilter');

                    if (this.currentStatus === 'paid') {
                        // "Bezahlt" tab = payment filter
                        statusFilter.value = 'all';
                        paymentFilter.value = 'paid';
                    } else if (this.currentStatus === 'all') {
                        statusFilter.value = 'all';
                        paymentFilter.value = 'all';
                    } else {
                        statusFilter.value = this.currentStatus;
                        paymentFilter.value = 'all';
                    }

                    this.loadOrders();
                });
            });

            // When status filter changes, highlight matching tab
            document.getElementById('statusFilter').addEventListener('change', () => {
                const val = document.getElementById('statusFilter').value;
                this.highlightMatchingTab(val);
            });
        },

        highlightMatchingTab(status) {
            document.querySelectorAll('#orderTabs .tab').forEach(t => t.classList.remove('active'));
            const matchingTab = document.querySelector(`#orderTabs .tab[data-status="${status}"]`);
            if (matchingTab) {
                matchingTab.classList.add('active');
                this.currentStatus = status;
            } else {
                // Default to "Alle" if no match
                document.querySelector('#orderTabs .tab[data-status="all"]').classList.add('active');
                this.currentStatus = 'all';
            }
        },

        async loadOrders() {
            const tbody = document.getElementById('ordersBody');
            tbody.innerHTML = '<tr><td colspan="8" class="loading-cell"><span class="material-symbols-rounded spinning">sync</span> Lade Bestellungen...</td></tr>';

            const search = document.getElementById('searchInput').value.trim();
            const statusFilterDropdown = document.getElementById('statusFilter').value;
            const paymentStatus = document.getElementById('paymentFilter').value;
            const period = document.getElementById('periodFilter').value;
            const currency = document.getElementById('currencySelect').value;

            // Build URL with all filters
            let url = `${this.apiBase}?action=get_orders&shop_id=${this.shopId}&page=${this.currentPage}&per_page=20`;
            url += `&search=${encodeURIComponent(search)}`;
            url += `&period=${period}`;
            url += `&display_currency=${currency}`;

            // Status filter: prioritize dropdown if not "all", otherwise use tab
            if (statusFilterDropdown !== 'all') {
                url += `&status=${statusFilterDropdown}`;
            } else if (this.currentStatus === 'paid') {
                // "Bezahlt" tab = payment_status filter
                url += `&payment_status=paid`;
            } else if (this.currentStatus !== 'all') {
                url += `&status=${this.currentStatus}`;
            }

            // Payment filter from dropdown (only if not already set by tab)
            if (paymentStatus !== 'all' && this.currentStatus !== 'paid') {
                url += `&payment_status=${paymentStatus}`;
            }

            try {
                const res = await fetch(url);
                const data = await res.json();

                if (data.success) {
                    this.orders = data.orders;
                    this.totalPages = data.pagination.total_pages;
                    this.total = data.pagination.total;
                    this.renderOrders();
                    this.renderPagination();
                    this.loadStats();
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" class="loading-cell">Fehler beim Laden</td></tr>';
                }
            } catch (e) {
                tbody.innerHTML = '<tr><td colspan="8" class="loading-cell">Fehler beim Laden</td></tr>';
            }
        },

        renderOrders() {
            const tbody = document.getElementById('ordersBody');

            if (this.orders.length === 0) {
                tbody.innerHTML = `
                <tr><td colspan="8">
                    <div class="empty-state">
                        <span class="material-symbols-rounded">inbox</span>
                        <p>Keine Bestellungen gefunden</p>
                    </div>
                </td></tr>`;
                return;
            }

            const statusLabels = {
                'pending': 'Ausstehend', 'processing': 'In Bearbeitung',
                'shipped': 'Versendet', 'delivered': 'Zugestellt',
                'cancelled': 'Storniert', 'refunded': 'Erstattet'
            };

            const paymentLabels = {
                'pending': 'Ausstehend', 'paid': 'Bezahlt',
                'failed': 'Fehlgeschlagen', 'refunded': 'Erstattet'
            };

            tbody.innerHTML = this.orders.map(order => {
                const date = new Date(order.created_at).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
                const amount = this.formatPrice(order.display_total, order.display_symbol);
                const customerLink = order.customer_id
                    ? `<a href="?page=customers/customer_edit&id=${order.customer_id}" class="customer-link">${this.escapeHtml(order.customer_name)}</a>`
                    : `<span style="color: var(--text-muted);">${this.escapeHtml(order.customer_name)}</span>`;

                return `
                <tr>
                    <td><input type="checkbox" data-id="${order.id}"></td>
                    <td><a href="?page=orders/order_detail&id=${order.id}" class="order-number">${this.escapeHtml(order.order_number)}</a></td>
                    <td>${date}</td>
                    <td>${customerLink}</td>
                    <td><strong>${amount}</strong></td>
                    <td><span class="badge-payment ${order.payment_status}">${paymentLabels[order.payment_status] || order.payment_status}</span></td>
                    <td><span class="badge-status ${order.status}">${statusLabels[order.status] || order.status}</span></td>
                    <td class="table-actions">
                        <a href="?page=orders/order_detail&id=${order.id}" class="btn btn-sm btn-primary" title="Details anzeigen"><span class="material-symbols-rounded">open_in_new</span></a>
                    </td>
                </tr>
            `;
            }).join('');
        },

        renderPagination() {
            const container = document.getElementById('pagination');
            if (this.totalPages <= 1) {
                container.innerHTML = `<span class="pagination-info">${this.total} Bestellung${this.total !== 1 ? 'en' : ''}</span>`;
                return;
            }

            let html = `<button onclick="OrderManager.goToPage(1)" ${this.currentPage === 1 ? 'disabled' : ''}>«</button>`;
            html += `<button onclick="OrderManager.goToPage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''}>‹</button>`;

            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, this.currentPage + 2);

            for (let i = start; i <= end; i++) {
                html += `<button onclick="OrderManager.goToPage(${i})" class="${i === this.currentPage ? 'active' : ''}">${i}</button>`;
            }

            html += `<button onclick="OrderManager.goToPage(${this.currentPage + 1})" ${this.currentPage === this.totalPages ? 'disabled' : ''}>›</button>`;
            html += `<button onclick="OrderManager.goToPage(${this.totalPages})" ${this.currentPage === this.totalPages ? 'disabled' : ''}>»</button>`;
            html += `<span class="pagination-info">${this.total} Bestellung${this.total !== 1 ? 'en' : ''}</span>`;

            container.innerHTML = html;
        },

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
            this.loadOrders();
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentPage = 1;
                this.loadOrders();
            }, 300);
        },

        toggleSelectAll() {
            const checked = document.getElementById('selectAll').checked;
            document.querySelectorAll('#ordersBody input[type="checkbox"]').forEach(cb => cb.checked = checked);
        },

        toggleExportMenu() {
            document.getElementById('exportMenu').classList.toggle('show');
        },

        exportAs(format) {
            const status = this.currentStatus !== 'all' ? this.currentStatus : 'all';
            window.location.href = `${this.apiBase}?action=export_orders&shop_id=${this.shopId}&format=${format}&status=${status}`;
            document.getElementById('exportMenu').classList.remove('show');
        },

        formatPrice(amount, symbol = '€') {
            return symbol + parseFloat(amount).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        }
    };

    // Close export menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.export-dropdown')) {
            document.getElementById('exportMenu').classList.remove('show');
        }
    });

    document.addEventListener('DOMContentLoaded', () => OrderManager.init());
</script>