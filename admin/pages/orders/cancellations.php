<?php /** Bestellungen - Stornierungen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Stornierungen</h1>
        <p class="page-subtitle">Stornierte Bestellungen & Erstattungen</p>
    </div>
    <div class="page-header-actions">
        <select id="currencySelector" class="btn" title="Währung für Anzeige">
            <option value="">Lade...</option>
        </select>
        <select id="periodFilter" class="btn">
            <option value="week">Diese Woche</option>
            <option value="month">Diesen Monat</option>
            <option value="year">Dieses Jahr</option>
            <option value="all">Gesamt</option>
        </select>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid" id="kpiGrid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Stornierungen</span></div>
        <div class="kpi-value" id="kpi-count">0</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Stornoquote</span></div>
        <div class="kpi-value" id="kpi-rate">0%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Erstattungen</span></div>
        <div class="kpi-value" id="kpi-refunds" style="color:var(--success);">€0</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Ausstehend</span></div>
        <div class="kpi-value" id="kpi-pending" style="color:var(--warning);">€0</div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs-container" id="cancelTabs">
    <button class="tab active" data-view="active">Aktive Stornos <span class="badge" id="badge-active">0</span></button>
    <button class="tab" data-view="all">Alle Stornierungen <span class="badge" id="badge-all">0</span></button>
    <button class="tab" data-view="reasons">Stornogründe</button>
</div>

<!-- Filter Bar -->
<div class="filter-bar" id="filterBar">
    <div class="search-input-container">
        <span class="material-symbols-rounded search-icon">search</span>
        <input type="text" id="searchInput" class="search-input" placeholder="Bestellung, Kunde, Storno-Nr. suchen...">
    </div>
    <select id="statusFilter" class="filter-select">
        <option value="">Alle Status</option>
        <option value="pending">Ausstehend</option>
        <option value="approved">Genehmigt</option>
        <option value="refunded">Erstattet</option>
        <option value="rejected">Abgelehnt</option>
    </select>
    <select id="reasonFilter" class="filter-select">
        <option value="">Alle Gründe</option>
        <option value="customer_request">Kundenwunsch</option>
        <option value="payment_failed">Zahlung fehlgeschlagen</option>
        <option value="fraud">Betrugsverdacht</option>
        <option value="out_of_stock">Nicht lieferbar</option>
        <option value="duplicate">Doppelte Bestellung</option>
        <option value="other">Sonstiges</option>
    </select>
</div>

<!-- Active Cancellations View -->
<div class="card" id="activeView">
    <div class="card-header">
        <h3>Aktive Stornierungen</h3>
    </div>
    <div class="card-body">
        <table class="table" id="activeTable">
            <thead>
                <tr>
                    <th class="sortable" data-sort="cancellation_number">Storno-Nr.</th>
                    <th>Bestellung</th>
                    <th>Kunde</th>
                    <th class="sortable" data-sort="original_total">Betrag <span class="sort-icon"></span></th>
                    <th>Grund</th>
                    <th class="sortable" data-sort="status">Status</th>
                    <th class="sortable active-sort desc" data-sort="created_at">Datum <span class="sort-icon">↓</span>
                    </th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="activeBody"></tbody>
        </table>
        <div id="activePagination" class="pagination-container"></div>
    </div>
</div>

<!-- All Cancellations View -->
<div class="card" id="allView" style="display:none;">
    <div class="card-header">
        <h3>Alle Stornierungen</h3>
    </div>
    <div class="card-body">
        <table class="table" id="allTable">
            <thead>
                <tr>
                    <th class="sortable" data-sort="cancellation_number">Storno-Nr.</th>
                    <th>Bestellung</th>
                    <th>Kunde</th>
                    <th class="sortable" data-sort="original_total">Betrag</th>
                    <th>Grund</th>
                    <th class="sortable" data-sort="status">Status</th>
                    <th>Erstattung</th>
                    <th class="sortable active-sort desc" data-sort="created_at">Datum</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="allBody"></tbody>
        </table>
        <div id="allPagination" class="pagination-container"></div>
    </div>
</div>

<!-- Reasons View -->
<div class="card" id="reasonsView" style="display:none;">
    <div class="card-header">
        <h3>Stornogründe (im ausgewählten Zeitraum)</h3>
    </div>
    <div class="card-body">
        <div class="reasons-grid" id="reasonsGrid"></div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal-overlay" id="cancelModal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 id="cancelModalTitle">Stornierung Details</h3>
            <button class="modal-close" onclick="Cancellations.closeModal('cancelModal')">&times;</button>
        </div>
        <div class="modal-body" id="cancelModalBody"></div>
        <div class="modal-footer" id="cancelModalFooter"></div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal-overlay" id="refundModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Erstattung durchführen</h3>
            <button class="modal-close" onclick="Cancellations.closeModal('refundModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Originalbetrag</label>
                <div class="info-value" id="refundOriginalAmount">€0.00</div>
            </div>
            <div class="form-group">
                <label for="refundAmountInput">Erstattungsbetrag</label>
                <input type="number" id="refundAmountInput" class="form-control" step="0.01" min="0">
                <small class="form-hint">Für vollständige Erstattung Originalbetrag verwenden</small>
            </div>
            <div class="form-group">
                <label for="refundNotes">Notizen (optional)</label>
                <textarea id="refundNotes" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Cancellations.closeModal('refundModal')">Abbrechen</button>
            <button class="btn btn-success" onclick="Cancellations.submitRefund()">Erstattung durchführen</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .tabs-container {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .tab {
        padding: 10px 20px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .tab:hover {
        background: var(--bg-tertiary);
    }

    .tab.active {
        background: var(--accent);
        border-color: var(--accent);
        color: white;
    }

    .tab .badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
    }

    .tab.active .badge {
        background: rgba(255, 255, 255, 0.3);
    }

    .filter-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .search-input-container {
        position: relative;
        flex: 1;
        min-width: 200px;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    .search-input {
        width: 100%;
        padding: 10px 12px 10px 40px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
    }

    .filter-select {
        padding: 10px 12px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
        min-width: 150px;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .status-approved {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }

    .status-refunded {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
    }

    .status-rejected {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sortable:hover {
        color: var(--accent);
    }

    .sortable .sort-icon {
        font-size: 12px;
        margin-left: 4px;
    }

    .sortable.active-sort {
        color: var(--accent);
    }

    .reasons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 16px;
    }

    .reason-card {
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 20px;
        text-align: center;
    }

    .reason-name {
        font-size: 14px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .reason-percent {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .reason-count {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .modal-lg {
        max-width: 700px;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--text-muted);
        cursor: pointer;
    }

    .modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }

    .modal-footer {
        padding: 16px 20px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: var(--text-muted);
    }

    .detail-value {
        font-weight: 500;
    }

    .items-list {
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
        padding: 12px;
        margin-top: 16px;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .item-row:last-child {
        border-bottom: none;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
    }

    .form-hint {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
        display: block;
    }

    .info-value {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
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

    .pagination-container {
        display: flex;
        justify-content: center;
        gap: 8px;
        padding: 16px 0;
    }

    .pagination-container button {
        padding: 8px 16px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        cursor: pointer;
        color: var(--text-primary);
    }

    .pagination-container button:hover:not(:disabled) {
        background: var(--accent);
        color: white;
    }

    .pagination-container button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-info {
        display: flex;
        align-items: center;
        color: var(--text-muted);
    }

    .loading-cell,
    .empty-cell {
        text-align: center;
        padding: 40px !important;
        color: var(--text-muted);
    }

    .empty-cell .material-symbols-rounded {
        font-size: 48px;
        display: block;
        margin-bottom: 8px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .spinning {
        animation: spin 1s linear infinite;
    }
</style>

<script>
    const Cancellations = {
        apiBase: 'api/cancellations.php',
        shopId: 1,
        currentView: 'active',
        activePage: 1,
        allPage: 1,
        currentSort: { column: 'created_at', dir: 'DESC' },
        currentCancellation: null,
        displayCurrency: null,
        currencySymbol: '€',

        async init() {
            await this.loadCurrencies();
            this.setupTabs();
            this.setupFilters();
            this.setupSorting();
            await this.loadData();
        },

        async loadCurrencies() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success && data.available_currencies) {
                    const select = document.getElementById('currencySelector');
                    const defaultCode = data.currency.default_code || 'EUR';
                    select.innerHTML = data.available_currencies.map(c =>
                        `<option value="${c.code}" ${c.code === defaultCode ? 'selected' : ''}>${c.code} (${c.symbol})</option>`
                    ).join('');
                    this.displayCurrency = defaultCode;
                    this.currencySymbol = data.currency.symbol;
                    select.value = defaultCode;
                }
            } catch (e) { console.error(e); }
        },

        setupTabs() {
            document.querySelectorAll('#cancelTabs .tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    document.querySelectorAll('#cancelTabs .tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    this.currentView = tab.dataset.view;
                    this.showView(this.currentView);
                });
            });
        },

        showView(view) {
            document.getElementById('activeView').style.display = view === 'active' ? '' : 'none';
            document.getElementById('allView').style.display = view === 'all' ? '' : 'none';
            document.getElementById('reasonsView').style.display = view === 'reasons' ? '' : 'none';
            document.getElementById('filterBar').style.display = view === 'reasons' ? 'none' : 'flex';

            if (view === 'active') this.loadActiveCancellations();
            else if (view === 'all') this.loadAllCancellations();
            else if (view === 'reasons') this.loadReasons();
        },

        setupFilters() {
            document.getElementById('searchInput').addEventListener('input', () => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => this.loadCurrentView(), 300);
            });

            ['statusFilter', 'reasonFilter'].forEach(id => {
                document.getElementById(id).addEventListener('change', () => this.loadCurrentView());
            });

            document.getElementById('periodFilter').addEventListener('change', () => this.loadData());
            document.getElementById('currencySelector').addEventListener('change', () => {
                this.displayCurrency = document.getElementById('currencySelector').value;
                this.loadData();
            });
        },

        setupSorting() {
            document.querySelectorAll('.sortable').forEach(th => {
                th.addEventListener('click', () => {
                    const column = th.dataset.sort;
                    if (this.currentSort.column === column) {
                        this.currentSort.dir = this.currentSort.dir === 'DESC' ? 'ASC' : 'DESC';
                    } else {
                        this.currentSort.column = column;
                        this.currentSort.dir = 'DESC';
                    }
                    // Update UI
                    document.querySelectorAll('.sortable').forEach(t => {
                        t.classList.remove('active-sort', 'asc', 'desc');
                        t.querySelector('.sort-icon')?.remove();
                    });
                    th.classList.add('active-sort', this.currentSort.dir.toLowerCase());
                    th.insertAdjacentHTML('beforeend', `<span class="sort-icon">${this.currentSort.dir === 'DESC' ? '↓' : '↑'}</span>`);
                    this.loadCurrentView();
                });
            });
        },

        loadCurrentView() {
            if (this.currentView === 'active') this.loadActiveCancellations();
            else if (this.currentView === 'all') this.loadAllCancellations();
        },

        async loadData() {
            await this.loadStats();
            await this.loadActiveCancellations();
        },

        async loadStats() {
            const period = document.getElementById('periodFilter').value;
            const currency = this.displayCurrency || '';
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}&period=${period}&display_currency=${currency}`);
                const data = await res.json();
                if (data.success) {
                    const sym = data.currency.symbol;
                    this.currencySymbol = sym;
                    document.getElementById('kpi-count').textContent = data.stats.period_count;
                    document.getElementById('kpi-rate').textContent = data.stats.cancel_rate.toFixed(1) + '%';
                    document.getElementById('kpi-refunds').textContent = sym + this.formatNumber(data.stats.total_refunds);
                    document.getElementById('kpi-pending').textContent = sym + this.formatNumber(data.stats.pending_refunds);
                    document.getElementById('badge-active').textContent = data.stats.active;
                    document.getElementById('badge-all').textContent = data.stats.total;
                }
            } catch (e) { console.error(e); }
        },

        async loadActiveCancellations() {
            const tbody = document.getElementById('activeBody');
            tbody.innerHTML = '<tr><td colspan="8" class="loading-cell"><span class="material-symbols-rounded spinning">sync</span> Lade...</td></tr>';

            const params = new URLSearchParams({
                action: 'get_cancellations',
                shop_id: this.shopId,
                page: this.activePage,
                active_only: 1,
                search: document.getElementById('searchInput').value,
                status: document.getElementById('statusFilter').value,
                reason: document.getElementById('reasonFilter').value,
                sort_by: this.currentSort.column,
                sort_dir: this.currentSort.dir,
                display_currency: this.displayCurrency || ''
            });

            try {
                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();

                if (data.success) {
                    this.currencySymbol = data.currency.symbol;
                    if (data.cancellations.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" class="empty-cell"><span class="material-symbols-rounded">inbox</span><p>Keine aktiven Stornierungen</p></td></tr>';
                        return;
                    }
                    tbody.innerHTML = data.cancellations.map(c => this.renderCancellationRow(c, false)).join('');
                    this.renderPagination('activePagination', data.pagination, 'active');
                }
            } catch (e) { console.error(e); tbody.innerHTML = '<tr><td colspan="8">Fehler beim Laden</td></tr>'; }
        },

        async loadAllCancellations() {
            const tbody = document.getElementById('allBody');
            tbody.innerHTML = '<tr><td colspan="9" class="loading-cell"><span class="material-symbols-rounded spinning">sync</span> Lade...</td></tr>';

            const params = new URLSearchParams({
                action: 'get_cancellations',
                shop_id: this.shopId,
                page: this.allPage,
                search: document.getElementById('searchInput').value,
                status: document.getElementById('statusFilter').value,
                reason: document.getElementById('reasonFilter').value,
                sort_by: this.currentSort.column,
                sort_dir: this.currentSort.dir,
                display_currency: this.displayCurrency || ''
            });

            try {
                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();

                if (data.success) {
                    this.currencySymbol = data.currency.symbol;
                    if (data.cancellations.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="9" class="empty-cell"><span class="material-symbols-rounded">inbox</span><p>Keine Stornierungen gefunden</p></td></tr>';
                        return;
                    }
                    tbody.innerHTML = data.cancellations.map(c => this.renderCancellationRow(c, true)).join('');
                    this.renderPagination('allPagination', data.pagination, 'all');
                }
            } catch (e) { console.error(e); }
        },

        async loadReasons() {
            const grid = document.getElementById('reasonsGrid');
            grid.innerHTML = '<div class="loading-cell"><span class="material-symbols-rounded spinning">sync</span> Lade...</div>';

            const period = document.getElementById('periodFilter').value;
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}&period=${period}`);
                const data = await res.json();

                if (data.success && data.stats.reasons) {
                    if (data.stats.reasons.length === 0) {
                        grid.innerHTML = '<div class="empty-cell">Keine Daten im ausgewählten Zeitraum</div>';
                        return;
                    }
                    grid.innerHTML = data.stats.reasons.map(r => `
                    <div class="reason-card">
                        <div class="reason-name">${this.escapeHtml(this.getReasonLabel(r.reason))}</div>
                        <div class="reason-percent">${r.percent.toFixed(0)}%</div>
                        <div class="reason-count">${r.count} Stornierungen</div>
                    </div>
                `).join('');
                }
            } catch (e) { console.error(e); grid.innerHTML = '<div>Fehler beim Laden</div>'; }
        },

        renderCancellationRow(c, showRefund) {
            const sym = c.display_symbol || this.currencySymbol;
            const refundCell = showRefund ? `<td>${c.refund_status !== 'none' ?
                `<span class="badge badge-success">${sym}${this.formatNumber(c.display_refund)}</span>` :
                '<span class="badge badge-default">Keine</span>'}</td>` : '';

            return `
            <tr>
                <td><strong>${c.cancellation_number}</strong></td>
                <td><a href="?page=orders/order_detail&id=${c.order_id}">${c.order_number || '#' + c.order_id}</a></td>
                <td>${this.escapeHtml(c.customer_name)}</td>
                <td>${sym}${this.formatNumber(c.display_original)}</td>
                <td>${this.getReasonLabel(c.reason)}</td>
                <td><span class="status-badge status-${c.status}">${this.getStatusLabel(c.status)}</span></td>
                ${refundCell}
                <td>${new Date(c.created_at).toLocaleDateString('de-DE')}</td>
                <td class="table-actions">
                    <button class="btn btn-sm" onclick="Cancellations.viewCancellation(${c.id})" title="Details">
                        <span class="material-symbols-rounded">visibility</span>
                    </button>
                    ${this.getActionButtons(c)}
                </td>
            </tr>
        `;
        },

        getActionButtons(c) {
            const buttons = [];
            if (c.status === 'pending') {
                buttons.push(`<button class="btn btn-sm btn-success" onclick="Cancellations.processCancellation(${c.id}, 'approve')" title="Genehmigen"><span class="material-symbols-rounded">check</span></button>`);
                buttons.push(`<button class="btn btn-sm btn-danger" onclick="Cancellations.processCancellation(${c.id}, 'reject')" title="Ablehnen"><span class="material-symbols-rounded">close</span></button>`);
            }
            if (c.status === 'pending' || c.status === 'approved') {
                buttons.push(`<button class="btn btn-sm btn-primary" onclick="Cancellations.openRefundModal(${c.id})" title="Erstatten"><span class="material-symbols-rounded">payments</span></button>`);
            }
            return buttons.join('');
        },

        getStatusLabel(status) {
            const labels = { pending: 'Ausstehend', approved: 'Genehmigt', refunded: 'Erstattet', rejected: 'Abgelehnt' };
            return labels[status] || status;
        },

        getReasonLabel(reason) {
            const labels = {
                customer_request: 'Kundenwunsch',
                payment_failed: 'Zahlung fehlgeschlagen',
                fraud: 'Betrugsverdacht',
                out_of_stock: 'Nicht lieferbar',
                duplicate: 'Doppelte Bestellung',
                other: 'Sonstiges'
            };
            return labels[reason] || reason || 'Unbekannt';
        },

        async viewCancellation(cancelId) {
            try {
                const res = await fetch(`${this.apiBase}?action=get_cancellation&shop_id=${this.shopId}&id=${cancelId}`);
                const data = await res.json();

                if (data.success) {
                    this.currentCancellation = data.cancellation;
                    const c = data.cancellation;
                    // Use selected display currency, not original order currency
                    const sym = this.currencySymbol || '€';

                    document.getElementById('cancelModalTitle').textContent = c.cancellation_number;

                    let itemsHtml = '';
                    if (c.items && c.items.length > 0) {
                        itemsHtml = `
                        <div class="items-list">
                            <strong>Bestellpositionen:</strong>
                            ${c.items.map(i => `
                                <div class="item-row">
                                    <span>${this.escapeHtml(i.name || i.product_name || 'Produkt')} (${i.quantity}x)</span>
                                    <span>${sym}${this.formatNumber(i.total_price || i.unit_price * i.quantity)}</span>
                                </div>
                            `).join('')}
                        </div>
                    `;
                    }

                    document.getElementById('cancelModalBody').innerHTML = `
                    <div class="detail-row">
                        <span class="detail-label">Bestellung</span>
                        <span class="detail-value"><a href="?page=orders/order_detail&id=${c.order_id}">${c.order_number || '#' + c.order_id}</a></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Kunde</span>
                        <span class="detail-value">${this.escapeHtml(c.customer_name)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="detail-value"><span class="status-badge status-${c.status}">${this.getStatusLabel(c.status)}</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Grund</span>
                        <span class="detail-value">${this.getReasonLabel(c.reason)}</span>
                    </div>
                    ${c.reason_details ? `<div class="detail-row"><span class="detail-label">Details</span><span class="detail-value">${this.escapeHtml(c.reason_details)}</span></div>` : ''}
                    <div class="detail-row">
                        <span class="detail-label">Ursprünglicher Betrag</span>
                        <span class="detail-value">${sym}${this.formatNumber(c.original_total)}</span>
                    </div>
                    ${c.refund_amount > 0 ? `<div class="detail-row"><span class="detail-label">Erstattung</span><span class="detail-value" style="color:var(--success)">${sym}${this.formatNumber(c.refund_amount)} (${c.refund_status === 'full' ? 'Vollständig' : 'Teilweise'})</span></div>` : ''}
                    <div class="detail-row">
                        <span class="detail-label">Storniert von</span>
                        <span class="detail-value">${c.cancelled_by === 'customer' ? 'Kunde' : 'Admin'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Erstellt am</span>
                        <span class="detail-value">${new Date(c.created_at).toLocaleString('de-DE')}</span>
                    </div>
                    ${c.processed_at ? `<div class="detail-row"><span class="detail-label">Bearbeitet am</span><span class="detail-value">${new Date(c.processed_at).toLocaleString('de-DE')}</span></div>` : ''}
                    ${c.notes ? `<div class="detail-row"><span class="detail-label">Notizen</span><span class="detail-value">${this.escapeHtml(c.notes)}</span></div>` : ''}
                    ${itemsHtml}
                `;

                    // Footer buttons based on status
                    let footerHtml = '<button class="btn" onclick="Cancellations.closeModal(\'cancelModal\')">Schließen</button>';
                    if (c.status === 'pending') {
                        footerHtml = `
                        <button class="btn btn-danger" onclick="Cancellations.processCancellation(${c.id}, 'reject')">Ablehnen</button>
                        <button class="btn btn-success" onclick="Cancellations.processCancellation(${c.id}, 'approve')">Genehmigen</button>
                        <button class="btn btn-primary" onclick="Cancellations.openRefundModal(${c.id})">Direkt erstatten</button>
                    `;
                    } else if (c.status === 'approved') {
                        footerHtml = `
                        <button class="btn" onclick="Cancellations.closeModal('cancelModal')">Schließen</button>
                        <button class="btn btn-success" onclick="Cancellations.openRefundModal(${c.id})">Erstattung durchführen</button>
                    `;
                    }

                    document.getElementById('cancelModalFooter').innerHTML = footerHtml;
                    document.getElementById('cancelModal').classList.add('show');
                }
            } catch (e) { console.error(e); this.showToast('Fehler beim Laden', 'error'); }
        },

        openRefundModal(cancelId) {
            if (!this.currentCancellation || this.currentCancellation.id !== cancelId) {
                // Need to fetch first
                this.viewCancellation(cancelId).then(() => this.openRefundModal(cancelId));
                return;
            }

            const c = this.currentCancellation;
            // Use selected display currency for consistency
            const sym = this.currencySymbol || '€';
            document.getElementById('refundOriginalAmount').textContent = sym + this.formatNumber(c.original_total);
            document.getElementById('refundAmountInput').value = c.original_total;
            document.getElementById('refundAmountInput').max = c.original_total;
            document.getElementById('refundNotes').value = '';

            this.closeModal('cancelModal');
            document.getElementById('refundModal').classList.add('show');
        },

        async submitRefund() {
            if (!this.currentCancellation) return;

            const refundAmount = parseFloat(document.getElementById('refundAmountInput').value) || 0;
            const notes = document.getElementById('refundNotes').value;

            if (refundAmount <= 0) {
                this.showToast('Bitte gültigen Erstattungsbetrag eingeben', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'process_cancellation');
            formData.append('shop_id', this.shopId);
            formData.append('cancellation_id', this.currentCancellation.id);
            formData.append('process_action', 'refund');
            formData.append('refund_amount', refundAmount);
            formData.append('notes', notes);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.closeModal('refundModal');
                    this.showToast('Erstattung durchgeführt', 'success');
                    await this.loadStats();
                    this.loadCurrentView();
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) { console.error(e); this.showToast('Fehler', 'error'); }
        },

        async processCancellation(cancelId, action) {
            const formData = new FormData();
            formData.append('action', 'process_cancellation');
            formData.append('shop_id', this.shopId);
            formData.append('cancellation_id', cancelId);
            formData.append('process_action', action);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.closeModal('cancelModal');
                    const messages = { approve: 'Stornierung genehmigt', reject: 'Stornierung abgelehnt', refund: 'Erstattung durchgeführt' };
                    this.showToast(messages[action] || 'Erfolgreich', 'success');
                    await this.loadStats();
                    this.loadCurrentView();
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) { console.error(e); this.showToast('Fehler', 'error'); }
        },

        renderPagination(containerId, pagination, view) {
            const container = document.getElementById(containerId);
            if (pagination.total_pages <= 1) {
                container.innerHTML = '';
                return;
            }

            container.innerHTML = `
            <button ${pagination.page === 1 ? 'disabled' : ''} onclick="Cancellations.goToPage(${pagination.page - 1}, '${view}')">←</button>
            <span class="pagination-info">Seite ${pagination.page} von ${pagination.total_pages}</span>
            <button ${pagination.page === pagination.total_pages ? 'disabled' : ''} onclick="Cancellations.goToPage(${pagination.page + 1}, '${view}')">→</button>
        `;
        },

        goToPage(page, view) {
            if (view === 'active') {
                this.activePage = page;
                this.loadActiveCancellations();
            } else {
                this.allPage = page;
                this.loadAllCancellations();
            }
        },

        closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.classList.remove('show'), 3000);
        },

        formatNumber(num) {
            return parseFloat(num || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => Cancellations.init());
</script>