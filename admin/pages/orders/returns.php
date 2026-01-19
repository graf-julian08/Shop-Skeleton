<?php /** Bestellungen - Retouren */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Retouren</h1>
        <p class="page-subtitle">RMA-Verwaltung & Rückgaben</p>
    </div>
    <div class="page-header-actions">
        <select id="periodFilter" class="btn" onchange="Returns.loadData()">
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
        <div class="kpi-header"><span class="kpi-title">Offene Retouren</span></div>
        <div class="kpi-value" id="kpi-open" style="color:var(--warning);">0</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Im Zeitraum</span></div>
        <div class="kpi-value" id="kpi-period">0</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Retourenquote</span></div>
        <div class="kpi-value" id="kpi-rate">0%</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Erstattungen</span></div>
        <div class="kpi-value" id="kpi-refunds" style="color:var(--success);">€0</div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs-container" id="returnsTabs">
    <button class="tab active" data-view="active">Aktive Retouren <span class="badge"
            id="badge-active">0</span></button>
    <button class="tab" data-view="all">Alle Retouren <span class="badge" id="badge-all">0</span></button>
    <button class="tab" data-view="reasons">Retourengründe</button>
</div>

<!-- Filter Bar -->
<div class="filter-bar" id="filterBar">
    <div class="search-input-container">
        <span class="material-symbols-rounded search-icon">search</span>
        <input type="text" id="searchInput" class="search-input" placeholder="RMA, Bestellung, Kunde suchen...">
    </div>
    <select id="statusFilter" class="filter-select">
        <option value="">Alle Status</option>
        <option value="requested">Angefragt</option>
        <option value="approved">Genehmigt</option>
        <option value="shipped">Unterwegs</option>
        <option value="received">Eingegangen</option>
        <option value="refunded">Erstattet</option>
        <option value="rejected">Abgelehnt</option>
    </select>
    <select id="reasonFilter" class="filter-select">
        <option value="">Alle Gründe</option>
        <option value="wrong_size">Größe passt nicht</option>
        <option value="not_as_described">Nicht wie beschrieben</option>
        <option value="defective">Defekt/Beschädigt</option>
        <option value="changed_mind">Nicht gefallen</option>
        <option value="wrong_item">Falscher Artikel</option>
        <option value="other">Sonstiges</option>
    </select>
</div>

<!-- Active Returns View -->
<div class="card" id="activeView">
    <div class="card-header">
        <h3>Aktive Retouren</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>RMA</th>
                    <th>Bestellung</th>
                    <th>Kunde</th>
                    <th>Artikel</th>
                    <th>Grund</th>
                    <th>Status</th>
                    <th>Erstellt</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="activeBody"></tbody>
        </table>
        <div id="activePagination" class="pagination-container"></div>
    </div>
</div>

<!-- All Returns View -->
<div class="card" id="allView" style="display:none;">
    <div class="card-header">
        <h3>Alle Retouren</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>RMA</th>
                    <th>Bestellung</th>
                    <th>Kunde</th>
                    <th>Artikel</th>
                    <th>Grund</th>
                    <th>Status</th>
                    <th>Erstellt</th>
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
        <h3>Retourengründe (im ausgewählten Zeitraum)</h3>
    </div>
    <div class="card-body">
        <div class="reasons-grid" id="reasonsGrid"></div>
    </div>
</div>

<!-- Return Detail Modal -->
<div class="modal-overlay" id="returnModal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 id="returnModalTitle">Retoure Details</h3>
            <button class="modal-close" onclick="Returns.closeModal('returnModal')">&times;</button>
        </div>
        <div class="modal-body" id="returnModalBody"></div>
        <div class="modal-footer" id="returnModalFooter"></div>
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

    .status-requested {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .status-approved {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }

    .status-shipped {
        background: rgba(139, 92, 246, 0.15);
        color: #8b5cf6;
    }

    .status-received {
        background: rgba(6, 182, 212, 0.15);
        color: #06b6d4;
    }

    .status-refunded {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
    }

    .status-rejected {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .reasons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
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

    .loading-cell .material-symbols-rounded {
        font-size: 32px;
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
    const Returns = {
        apiBase: 'api/returns.php',
        shopId: 1,
        currentView: 'active',
        activePage: 1,
        allPage: 1,
        currentReturn: null,

        async init() {
            this.setupTabs();
            this.setupFilters();
            await this.loadData();
        },

        setupTabs() {
            document.querySelectorAll('#returnsTabs .tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    document.querySelectorAll('#returnsTabs .tab').forEach(t => t.classList.remove('active'));
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

            // Show/hide filter bar for reasons view
            document.getElementById('filterBar').style.display = view === 'reasons' ? 'none' : 'flex';

            if (view === 'active') this.loadActiveReturns();
            else if (view === 'all') this.loadAllReturns();
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
        },

        loadCurrentView() {
            if (this.currentView === 'active') this.loadActiveReturns();
            else if (this.currentView === 'all') this.loadAllReturns();
        },

        async loadData() {
            await this.loadStats();
            await this.loadActiveReturns();
        },

        async loadStats() {
            const period = document.getElementById('periodFilter').value;
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}&period=${period}`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('kpi-open').textContent = data.stats.open;
                    document.getElementById('kpi-period').textContent = data.stats.period_count;
                    document.getElementById('kpi-rate').textContent = data.stats.return_rate.toFixed(1) + '%';
                    document.getElementById('kpi-refunds').textContent = '€' + parseFloat(data.stats.total_refunds || 0).toFixed(2);
                    document.getElementById('badge-active').textContent = data.stats.active;
                    document.getElementById('badge-all').textContent = data.stats.total;
                }
            } catch (e) { console.error(e); }
        },

        async loadActiveReturns() {
            const tbody = document.getElementById('activeBody');
            tbody.innerHTML = '<tr><td colspan="8" class="loading-cell"><span class="material-symbols-rounded spinning">sync</span> Lade...</td></tr>';

            const params = new URLSearchParams({
                action: 'get_returns',
                shop_id: this.shopId,
                page: this.activePage,
                active_only: 1,
                search: document.getElementById('searchInput').value,
                status: document.getElementById('statusFilter').value,
                reason: document.getElementById('reasonFilter').value
            });

            try {
                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();

                if (data.success) {
                    if (data.returns.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" class="empty-cell"><span class="material-symbols-rounded">inbox</span><p>Keine aktiven Retouren</p></td></tr>';
                        return;
                    }
                    tbody.innerHTML = data.returns.map(r => this.renderReturnRow(r)).join('');
                    this.renderPagination('activePagination', data.pagination, 'active');
                }
            } catch (e) { console.error(e); tbody.innerHTML = '<tr><td colspan="8">Fehler beim Laden</td></tr>'; }
        },

        async loadAllReturns() {
            const tbody = document.getElementById('allBody');
            tbody.innerHTML = '<tr><td colspan="8" class="loading-cell"><span class="material-symbols-rounded spinning">sync</span> Lade...</td></tr>';

            const params = new URLSearchParams({
                action: 'get_returns',
                shop_id: this.shopId,
                page: this.allPage,
                search: document.getElementById('searchInput').value,
                status: document.getElementById('statusFilter').value,
                reason: document.getElementById('reasonFilter').value
            });

            try {
                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();

                if (data.success) {
                    if (data.returns.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" class="empty-cell"><span class="material-symbols-rounded">inbox</span><p>Keine Retouren gefunden</p></td></tr>';
                        return;
                    }
                    tbody.innerHTML = data.returns.map(r => this.renderReturnRow(r)).join('');
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
                            <div class="reason-count">${r.count} Retouren</div>
                        </div>
                    `).join('');
                }
            } catch (e) { console.error(e); grid.innerHTML = '<div>Fehler beim Laden</div>'; }
        },

        renderReturnRow(r) {
            return `
                <tr>
                    <td><strong>${r.return_number}</strong></td>
                    <td><a href="?page=orders/order_detail&id=${r.order_id}">${r.order_number || '#' + r.order_id}</a></td>
                    <td>${this.escapeHtml(r.customer_name || 'Gast')}</td>
                    <td>${r.item_count || 1} Artikel</td>
                    <td>${this.getReasonLabel(r.reason)}</td>
                    <td><span class="status-badge status-${r.status}">${this.getStatusLabel(r.status)}</span></td>
                    <td>${new Date(r.created_at).toLocaleDateString('de-DE')}</td>
                    <td class="table-actions">
                        <button class="btn btn-sm" onclick="Returns.viewReturn(${r.id})">
                            <span class="material-symbols-rounded">visibility</span>
                        </button>
                        ${this.getActionButtons(r)}
                    </td>
                </tr>
            `;
        },

        getActionButtons(r) {
            const buttons = [];
            if (r.status === 'requested') {
                buttons.push(`<button class="btn btn-sm btn-success" onclick="Returns.processReturn(${r.id}, 'approve')" title="Genehmigen"><span class="material-symbols-rounded">check</span></button>`);
                buttons.push(`<button class="btn btn-sm btn-danger" onclick="Returns.processReturn(${r.id}, 'reject')" title="Ablehnen"><span class="material-symbols-rounded">close</span></button>`);
            }
            if (r.status === 'approved') {
                buttons.push(`<button class="btn btn-sm btn-primary" onclick="Returns.processReturn(${r.id}, 'ship')" title="Als unterwegs markieren"><span class="material-symbols-rounded">local_shipping</span></button>`);
            }
            if (r.status === 'shipped') {
                buttons.push(`<button class="btn btn-sm btn-info" onclick="Returns.processReturn(${r.id}, 'receive')" title="Eingegangen"><span class="material-symbols-rounded">inventory</span></button>`);
            }
            if (r.status === 'received') {
                buttons.push(`<button class="btn btn-sm btn-success" onclick="Returns.processReturn(${r.id}, 'refund')" title="Erstatten"><span class="material-symbols-rounded">payments</span></button>`);
            }
            return buttons.join('');
        },

        getStatusLabel(status) {
            const labels = {
                requested: 'Angefragt',
                approved: 'Genehmigt',
                shipped: 'Unterwegs',
                received: 'Eingegangen',
                refunded: 'Erstattet',
                rejected: 'Abgelehnt'
            };
            return labels[status] || status;
        },

        getReasonLabel(reason) {
            const labels = {
                wrong_size: 'Größe passt nicht',
                not_as_described: 'Nicht wie beschrieben',
                defective: 'Defekt/Beschädigt',
                changed_mind: 'Nicht gefallen',
                wrong_item: 'Falscher Artikel',
                other: 'Sonstiges'
            };
            return labels[reason] || reason || 'Unbekannt';
        },

        getConditionLabel(condition) {
            const labels = {
                'new': 'Neu/unbenutzt',
                'used': 'Gebraucht',
                'opened': 'Geöffnet',
                'damaged': 'Beschädigt'
            };
            return labels[condition] || condition || '-';
        },

        async viewReturn(returnId) {
            try {
                const res = await fetch(`${this.apiBase}?action=get_return&shop_id=${this.shopId}&id=${returnId}`);
                const data = await res.json();

                if (data.success) {
                    this.currentReturn = data.return;
                    document.getElementById('returnModalTitle').textContent = data.return.return_number;

                    let itemsHtml = '';
                    if (data.return.items && data.return.items.length > 0) {
                        itemsHtml = `
                            <div class="items-list">
                                <strong>Artikel:</strong>
                                ${data.return.items.map(i => `
                                    <div class="item-row">
                                        <span>${this.escapeHtml(i.name || 'Produkt')} (${i.quantity}x)</span>
                                        <span>${i.item_condition ? 'Zustand: ' + this.getConditionLabel(i.item_condition) : ''}</span>
                                    </div>
                                `).join('')}
                            </div>
                        `;
                    }

                    document.getElementById('returnModalBody').innerHTML = `
                        <div class="detail-row">
                            <span class="detail-label">Bestellung</span>
                            <span class="detail-value"><a href="?page=orders/order_detail&id=${data.return.order_id}">${data.return.order_number || '#' + data.return.order_id}</a></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Kunde</span>
                            <span class="detail-value">${this.escapeHtml(data.return.customer_name || 'Gast')}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status</span>
                            <span class="detail-value"><span class="status-badge status-${data.return.status}">${this.getStatusLabel(data.return.status)}</span></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Grund</span>
                            <span class="detail-value">${this.getReasonLabel(data.return.reason)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Typ</span>
                            <span class="detail-value">${data.return.return_type === 'refund' ? 'Erstattung' : data.return.return_type === 'exchange' ? 'Umtausch' : 'Gutschrift'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Erstellt am</span>
                            <span class="detail-value">${new Date(data.return.created_at).toLocaleString('de-DE')}</span>
                        </div>
                        ${data.return.notes ? `<div class="detail-row"><span class="detail-label">Notizen</span><span class="detail-value">${this.escapeHtml(data.return.notes)}</span></div>` : ''}
                        ${itemsHtml}
                    `;

                    // Footer with action buttons
                    let footerHtml = '<button class="btn" onclick="Returns.closeModal(\'returnModal\')">Schliessen</button>';

                    if (data.return.status === 'requested') {
                        footerHtml = `
                            <button class="btn btn-danger" onclick="Returns.processReturn(${data.return.id}, 'reject')">Ablehnen</button>
                            <button class="btn btn-success" onclick="Returns.processReturn(${data.return.id}, 'approve')">Genehmigen</button>
                        `;
                    } else if (data.return.status === 'approved') {
                        footerHtml = `<button class="btn" onclick="Returns.closeModal('returnModal')">Schliessen</button>
                            <button class="btn btn-primary" onclick="Returns.processReturn(${data.return.id}, 'ship')">Als unterwegs markieren</button>`;
                    } else if (data.return.status === 'shipped') {
                        footerHtml = `<button class="btn" onclick="Returns.closeModal('returnModal')">Schliessen</button>
                            <button class="btn btn-info" onclick="Returns.processReturn(${data.return.id}, 'receive')">Eingang bestätigen</button>`;
                    } else if (data.return.status === 'received') {
                        footerHtml = `<button class="btn" onclick="Returns.closeModal('returnModal')">Schliessen</button>
                            <button class="btn btn-success" onclick="Returns.processReturn(${data.return.id}, 'refund')">Erstattung durchführen</button>`;
                    }

                    document.getElementById('returnModalFooter').innerHTML = footerHtml;
                    document.getElementById('returnModal').classList.add('show');
                }
            } catch (e) { console.error(e); this.showToast('Fehler beim Laden', 'error'); }
        },

        async processReturn(returnId, action) {
            const formData = new FormData();
            formData.append('action', 'process_return');
            formData.append('shop_id', this.shopId);
            formData.append('return_id', returnId);
            formData.append('process_action', action);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    const messages = {
                        approve: 'Retoure genehmigt',
                        reject: 'Retoure abgelehnt',
                        ship: 'Als unterwegs markiert',
                        receive: 'Eingang bestätigt',
                        refund: 'Erstattung durchgeführt'
                    };
                    this.showToast(messages[action] || 'Erfolgreich', 'success');
                    this.closeModal('returnModal');
                    await this.loadStats();
                    this.loadCurrentView();
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) { this.showToast('Fehler', 'error'); }
        },

        renderPagination(containerId, pagination, type) {
            const container = document.getElementById(containerId);
            if (!pagination || pagination.total_pages <= 1) { container.innerHTML = ''; return; }

            container.innerHTML = `
                <button ${pagination.page <= 1 ? 'disabled' : ''} onclick="Returns.goToPage('${type}', ${pagination.page - 1})">← Zurück</button>
                <span class="pagination-info">Seite ${pagination.page} von ${pagination.total_pages}</span>
                <button ${pagination.page >= pagination.total_pages ? 'disabled' : ''} onclick="Returns.goToPage('${type}', ${pagination.page + 1})">Weiter →</button>
            `;
        },

        goToPage(type, page) {
            if (type === 'active') { this.activePage = page; this.loadActiveReturns(); }
            else if (type === 'all') { this.allPage = page; this.loadAllReturns(); }
        },

        closeModal(id) {
            document.getElementById(id).classList.remove('show');
            this.currentReturn = null;
        },

        showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        },

        escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', () => Returns.init());
</script>