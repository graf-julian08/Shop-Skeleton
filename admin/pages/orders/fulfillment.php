<?php /** Bestellungen - Fulfillment */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Fulfillment</h1>
        <p class="page-subtitle">Versand, Logistik & Sendungsverfolgung</p>
    </div>
    <div class="page-header-actions">
        <button class="btn" onclick="Fulfillment.openCarrierSettings()">
            <span class="material-symbols-rounded">local_shipping</span> Carrier
        </button>
        <button class="btn btn-primary" onclick="Fulfillment.generatePicklist()">
            <span class="material-symbols-rounded">checklist</span> Picklist erstellen
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid" id="kpiGrid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Zu versenden</span></div>
        <div class="kpi-value" id="kpi-pending" style="color:var(--warning);">0</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Heute versendet</span></div>
        <div class="kpi-value" id="kpi-shipped-today">0</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">In Zustellung</span></div>
        <div class="kpi-value" id="kpi-in-transit" style="color:var(--accent);">0</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Zugestellt (heute)</span></div>
        <div class="kpi-value" id="kpi-delivered" style="color:var(--success);">0</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Probleme</span></div>
        <div class="kpi-value" id="kpi-problems" style="color:var(--error);">0</div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs-container" id="fulfillmentTabs">
    <button class="tab active" data-view="pending">Ausstehend <span class="badge" id="badge-pending">0</span></button>
    <button class="tab" data-view="shipments">Sendungen <span class="badge" id="badge-shipments">0</span></button>
    <button class="tab" data-view="carriers">Carrier</button>
</div>

<!-- Filters -->
<div class="filter-bar" id="filterBar">
    <div class="search-input-container">
        <span class="material-symbols-rounded search-icon">search</span>
        <input type="text" id="searchInput" class="search-input" placeholder="Bestellung, Tracking suchen...">
    </div>
    <select id="warehouseFilter" class="filter-select">
        <option value="">Alle Lager</option>
    </select>
    <select id="carrierFilter" class="filter-select" style="display:none;">
        <option value="">Alle Carrier</option>
    </select>
    <select id="statusFilter" class="filter-select" style="display:none;">
        <option value="">Alle Status</option>
        <option value="pending">Ausstehend</option>
        <option value="picking">Kommissionierung</option>
        <option value="packed">Verpackt</option>
        <option value="shipped">Versendet</option>
        <option value="in_transit">Im Transit</option>
        <option value="delivered">Zugestellt</option>
    </select>
</div>

<!-- Pending Orders View -->
<div class="card" id="pendingView">
    <div class="card-header">
        <h3>Ausstehende Bestellungen</h3>
        <div>
            <button class="btn btn-sm" onclick="Fulfillment.bulkCreateShipments()" id="bulkShipBtn" disabled>
                <span class="material-symbols-rounded">package_2</span> Sendungen erstellen
            </button>
        </div>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllPending" onchange="Fulfillment.toggleSelectAll(this)"></th>
                    <th>Bestellung</th>
                    <th>Kunde</th>
                    <th>Artikel</th>
                    <th>Versandart</th>
                    <th>Adresse</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="pendingBody"></tbody>
        </table>
        <div id="pendingPagination" class="pagination-container"></div>
    </div>
</div>

<!-- Shipments View -->
<div class="card" id="shipmentsView" style="display:none;">
    <div class="card-header">
        <h3>Sendungen</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Sendung</th>
                    <th>Bestellung</th>
                    <th>Carrier</th>
                    <th>Tracking</th>
                    <th>Status</th>
                    <th>Datum</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="shipmentsBody"></tbody>
        </table>
        <div id="shipmentsPagination" class="pagination-container"></div>
    </div>
</div>

<!-- Carriers View -->
<div class="card" id="carriersView" style="display:none;">
    <div class="card-header">
        <h3>Versanddienstleister</h3>
        <p class="card-subtitle">Konfigurieren Sie Ihre Carrier-API-Zugangsdaten</p>
    </div>
    <div class="card-body">
        <div class="carriers-grid" id="carriersGrid"></div>
    </div>
</div>

<!-- Create Shipment Modal -->
<div class="modal-overlay" id="shipmentModal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3>Sendung erstellen</h3>
            <button class="modal-close" onclick="Fulfillment.closeModal('shipmentModal')">&times;</button>
        </div>
        <div class="modal-body" id="shipmentModalBody"></div>
        <div class="modal-footer">
            <button class="btn" onclick="Fulfillment.closeModal('shipmentModal')">Abbrechen</button>
            <button class="btn btn-primary" onclick="Fulfillment.saveShipment()">Sendung erstellen</button>
        </div>
    </div>
</div>

<!-- Tracking Modal -->
<div class="modal-overlay" id="trackingModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Tracking-Nummer zuweisen</h3>
            <button class="modal-close" onclick="Fulfillment.closeModal('trackingModal')">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="trackingShipmentId">
            <div class="form-group">
                <label>Tracking-Nummer</label>
                <input type="text" id="trackingNumberInput" class="form-control" placeholder="z.B. JJD000390012345678">
            </div>
            <div class="form-group">
                <label>Carrier</label>
                <select id="trackingCarrierSelect" class="form-control"></select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Fulfillment.closeModal('trackingModal')">Abbrechen</button>
            <button class="btn btn-primary" onclick="Fulfillment.saveTracking()">Speichern & Versenden</button>
        </div>
    </div>
</div>

<!-- Carrier Settings Modal -->
<div class="modal-overlay" id="carrierModal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 id="carrierModalTitle">Carrier Einstellungen</h3>
            <button class="modal-close" onclick="Fulfillment.closeModal('carrierModal')">&times;</button>
        </div>
        <div class="modal-body" id="carrierModalBody"></div>
        <div class="modal-footer">
            <button class="btn" onclick="Fulfillment.closeModal('carrierModal')">Schliessen</button>
            <button class="btn btn-primary" onclick="Fulfillment.saveCarrierSettings()">Speichern</button>
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

    .carriers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }

    .carrier-card {
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 20px;
        position: relative;
    }

    .carrier-card.inactive {
        opacity: 0.5;
    }

    .carrier-card.default {
        border-color: var(--accent);
    }

    .carrier-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .carrier-logo {
        width: 48px;
        height: 48px;
        object-fit: contain;
        background: white;
        border-radius: 8px;
        padding: 4px;
    }

    .carrier-name {
        font-weight: 600;
        font-size: 16px;
    }

    .carrier-badge {
        position: absolute;
        top: 12px;
        right: 12px;
    }

    .carrier-info {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .carrier-actions {
        display: flex;
        gap: 8px;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .status-picking {
        background: rgba(139, 92, 246, 0.15);
        color: #8b5cf6;
    }

    .status-packed {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }

    .status-shipped {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .status-in_transit {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }

    .status-out_for_delivery {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .status-delivered {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
    }

    .status-failed {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .status-returned {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .tracking-link {
        color: var(--accent);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .tracking-link:hover {
        text-decoration: underline;
    }

    .modal-lg {
        max-width: 700px;
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

    .item-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
        margin-bottom: 8px;
    }

    .item-row input[type="checkbox"] {
        width: 18px;
        height: 18px;
    }

    .item-info {
        flex: 1;
    }

    .item-name {
        font-weight: 500;
    }

    .item-sku {
        font-size: 12px;
        color: var(--text-muted);
    }

    .item-qty {
        width: 80px;
        text-align: center;
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

    .address-cell {
        font-size: 13px;
        line-height: 1.4;
    }

    .address-name {
        font-weight: 500;
    }
</style>

<script>
    const Fulfillment = {
        apiBase: 'api/fulfillment.php',
        shopId: 1,
        currentView: 'pending',
        pendingPage: 1,
        shipmentsPage: 1,
        selectedOrders: [],
        carriers: [],
        warehouses: [],
        currentShipmentData: null,
        editingCarrierId: null,

        async init() {
            await this.loadWarehouses();
            await this.loadCarriers();
            await this.generateTestDataIfNeeded();
            this.setupTabs();
            this.setupFilters();
            this.loadStats();
            this.loadPendingOrders();
        },

        setupTabs() {
            document.querySelectorAll('#fulfillmentTabs .tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    document.querySelectorAll('#fulfillmentTabs .tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    this.currentView = tab.dataset.view;
                    this.showView(this.currentView);
                });
            });
        },

        showView(view) {
            document.getElementById('pendingView').style.display = view === 'pending' ? '' : 'none';
            document.getElementById('shipmentsView').style.display = view === 'shipments' ? '' : 'none';
            document.getElementById('carriersView').style.display = view === 'carriers' ? '' : 'none';

            document.getElementById('carrierFilter').style.display = view === 'shipments' ? '' : 'none';
            document.getElementById('statusFilter').style.display = view === 'shipments' ? '' : 'none';
            document.getElementById('warehouseFilter').style.display = view !== 'carriers' ? '' : 'none';

            if (view === 'pending') this.loadPendingOrders();
            else if (view === 'shipments') this.loadShipments();
            else if (view === 'carriers') this.loadCarriersView();
        },

        setupFilters() {
            document.getElementById('searchInput').addEventListener('input', () => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    if (this.currentView === 'pending') this.loadPendingOrders();
                    else if (this.currentView === 'shipments') this.loadShipments();
                }, 300);
            });

            ['warehouseFilter', 'carrierFilter', 'statusFilter'].forEach(id => {
                document.getElementById(id).addEventListener('change', () => {
                    if (this.currentView === 'pending') this.loadPendingOrders();
                    else if (this.currentView === 'shipments') this.loadShipments();
                });
            });
        },

        async loadStats() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('kpi-pending').textContent = data.stats.pending;
                    document.getElementById('kpi-shipped-today').textContent = data.stats.shipped_today;
                    document.getElementById('kpi-in-transit').textContent = data.stats.in_transit;
                    document.getElementById('kpi-delivered').textContent = data.stats.delivered_today;
                    document.getElementById('kpi-problems').textContent = data.stats.problems;
                    document.getElementById('badge-pending').textContent = data.stats.pending;
                    // Also load shipments count for badge
                    document.getElementById('badge-shipments').textContent = data.stats.total_shipments || 0;
                }
            } catch (e) { console.error(e); }
        },

        async loadWarehouses() {
            try {
                const res = await fetch(`api/inventory.php?action=get_warehouses&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    this.warehouses = data.warehouses;
                    const select = document.getElementById('warehouseFilter');
                    select.innerHTML = '<option value="">Alle Lager</option>' +
                        data.warehouses.map(w => `<option value="${w.id}">${w.name}</option>`).join('');
                }
            } catch (e) { }
        },

        async loadCarriers() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_carriers&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    this.carriers = data.carriers;
                    const select = document.getElementById('carrierFilter');
                    select.innerHTML = '<option value="">Alle Carrier</option>' +
                        data.carriers.filter(c => c.is_active).map(c => `<option value="${c.id}">${c.name}</option>`).join('');

                    const trackingSelect = document.getElementById('trackingCarrierSelect');
                    trackingSelect.innerHTML = data.carriers.filter(c => c.is_active).map(c =>
                        `<option value="${c.id}" ${c.is_default ? 'selected' : ''}>${c.name}</option>`
                    ).join('');
                }
            } catch (e) { }
        },

        async generateTestDataIfNeeded() {
            try { await fetch(`${this.apiBase}?action=generate_test_data&shop_id=${this.shopId}`); } catch (e) { }
        },

        async loadPendingOrders() {
            const tbody = document.getElementById('pendingBody');
            tbody.innerHTML = '<tr><td colspan="7" class="loading-cell"><span class="material-symbols-rounded spinning">sync</span> Lade...</td></tr>';

            const params = new URLSearchParams({
                action: 'get_pending_orders',
                shop_id: this.shopId,
                page: this.pendingPage,
                search: document.getElementById('searchInput').value,
                warehouse_id: document.getElementById('warehouseFilter').value
            });

            try {
                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();

                if (data.success) {
                    this.selectedOrders = [];
                    this.updateBulkButton();

                    if (data.orders.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="empty-cell"><span class="material-symbols-rounded">inbox</span><p>Keine ausstehenden Bestellungen</p></td></tr>';
                        return;
                    }

                    tbody.innerHTML = data.orders.map(o => {
                        const addr = o.shipping_address || {};
                        return `
                    <tr>
                        <td><input type="checkbox" data-order-id="${o.id}" onchange="Fulfillment.toggleOrderSelection(${o.id})"></td>
                        <td><strong><a href="?page=orders/order_detail&id=${o.id}">${o.order_number}</a></strong></td>
                        <td>${this.escapeHtml(o.customer_name || 'Gast')}</td>
                        <td>${o.total_items || o.item_count} Artikel</td>
                        <td>${o.shipping_method || 'Standard'}</td>
                        <td class="address-cell">
                            <div class="address-name">${this.escapeHtml(addr.name || '')}</div>
                            ${this.escapeHtml(addr.street || '')}, ${this.escapeHtml(addr.zip || '')} ${this.escapeHtml(addr.city || '')}
                        </td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-primary" onclick="Fulfillment.openShipmentModal(${o.id})">
                                <span class="material-symbols-rounded">local_shipping</span> Versenden
                            </button>
                        </td>
                    </tr>`;
                    }).join('');

                    this.renderPagination('pendingPagination', data.pagination, 'pending');
                }
            } catch (e) { console.error(e); tbody.innerHTML = '<tr><td colspan="7">Fehler beim Laden</td></tr>'; }
        },

        async loadShipments() {
            const tbody = document.getElementById('shipmentsBody');
            tbody.innerHTML = '<tr><td colspan="7" class="loading-cell"><span class="material-symbols-rounded spinning">sync</span> Lade...</td></tr>';

            const params = new URLSearchParams({
                action: 'get_shipments',
                shop_id: this.shopId,
                page: this.shipmentsPage,
                search: document.getElementById('searchInput').value,
                carrier_id: document.getElementById('carrierFilter').value,
                status: document.getElementById('statusFilter').value
            });

            try {
                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();

                if (data.success) {
                    document.getElementById('badge-shipments').textContent = data.pagination.total;

                    if (data.shipments.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="empty-cell"><span class="material-symbols-rounded">package_2</span><p>Keine Sendungen</p></td></tr>';
                        return;
                    }

                    tbody.innerHTML = data.shipments.map(s => `
                    <tr>
                        <td><strong>${s.shipment_number}</strong></td>
                        <td><a href="?page=orders/order_detail&id=${s.order_id}">${s.order_number}</a></td>
                        <td>${s.carrier_name || '-'}</td>
                        <td>${s.tracking_number ? `<a href="${s.tracking_url}" target="_blank" class="tracking-link">${s.tracking_number} <span class="material-symbols-rounded" style="font-size:14px;">open_in_new</span></a>` : '-'}</td>
                        <td><span class="status-badge status-${s.status}">${this.getStatusLabel(s.status)}</span></td>
                        <td>${new Date(s.created_at).toLocaleDateString('de-DE')}</td>
                        <td class="table-actions">
                            ${s.status === 'pending' || s.status === 'packed' ? `<button class="btn btn-sm btn-primary" onclick="Fulfillment.openTrackingModal(${s.id})"><span class="material-symbols-rounded">qr_code</span></button>` : ''}
                            <a href="?page=orders/order_detail&id=${s.order_id}" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a>
                        </td>
                    </tr>
                `).join('');

                    this.renderPagination('shipmentsPagination', data.pagination, 'shipments');
                }
            } catch (e) { console.error(e); }
        },

        async loadCarriersView() {
            const grid = document.getElementById('carriersGrid');

            grid.innerHTML = this.carriers.map(c => `
            <div class="carrier-card ${c.is_active ? '' : 'inactive'} ${c.is_default ? 'default' : ''}">
                ${c.is_default ? '<span class="carrier-badge status-badge status-shipped">Standard</span>' : ''}
                <div class="carrier-header">
                    <img src="${c.logo_url}" alt="${c.name}" class="carrier-logo" onerror="this.style.display='none'">
                    <div>
                        <div class="carrier-name">${c.name}</div>
                        <div style="font-size:12px;color:var(--text-muted);">${c.code.toUpperCase()}</div>
                    </div>
                </div>
                <div class="carrier-info">
                    ${c.has_api_key ? '<span style="color:var(--success);">✓ API konfiguriert</span>' : '<span style="color:var(--warning);">⚠ API nicht konfiguriert</span>'}
                    <br>${c.shipment_count} Sendungen
                </div>
                <div class="carrier-actions">
                    <button class="btn btn-sm" onclick="Fulfillment.editCarrier(${c.id})">
                        <span class="material-symbols-rounded">settings</span> Einstellungen
                    </button>
                    <button class="btn btn-sm" onclick="Fulfillment.toggleCarrier(${c.id})">
                        ${c.is_active ? 'Deaktivieren' : 'Aktivieren'}
                    </button>
                    ${!c.is_default ? `<button class="btn btn-sm" onclick="Fulfillment.setDefaultCarrier(${c.id})">Standard</button>` : ''}
                </div>
            </div>
        `).join('');
        },

        getStatusLabel(status) {
            const labels = {
                pending: 'Ausstehend', picking: 'Kommissionierung', packed: 'Verpackt',
                shipped: 'Versendet', in_transit: 'Im Transit', out_for_delivery: 'In Zustellung',
                delivered: 'Zugestellt', failed: 'Fehlgeschlagen', returned: 'Zurückgesendet'
            };
            return labels[status] || status;
        },

        toggleOrderSelection(orderId) {
            const idx = this.selectedOrders.indexOf(orderId);
            if (idx === -1) this.selectedOrders.push(orderId);
            else this.selectedOrders.splice(idx, 1);
            this.updateBulkButton();
        },

        toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('#pendingBody input[type="checkbox"]');
            this.selectedOrders = [];
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
                if (checkbox.checked) this.selectedOrders.push(parseInt(cb.dataset.orderId));
            });
            this.updateBulkButton();
        },

        updateBulkButton() {
            const btn = document.getElementById('bulkShipBtn');
            btn.disabled = this.selectedOrders.length === 0;
            btn.innerHTML = `<span class="material-symbols-rounded">package_2</span> ${this.selectedOrders.length > 0 ? `(${this.selectedOrders.length}) ` : ''}Sendungen erstellen`;
        },

        async openShipmentModal(orderId) {
            const res = await fetch(`api/orders.php?action=get_order&shop_id=${this.shopId}&id=${orderId}`);
            const data = await res.json();

            if (!data.success) { this.showToast('Bestellung nicht gefunden', 'error'); return; }

            const order = data.order;
            this.currentShipmentData = { orderId, items: order.items };

            const warehouseOptions = this.warehouses.map(w => `<option value="${w.id}" ${w.is_default ? 'selected' : ''}>${w.name}</option>`).join('');
            const carrierOptions = this.carriers.filter(c => c.is_active).map(c => `<option value="${c.id}" ${c.is_default ? 'selected' : ''}>${c.name}</option>`).join('');

            document.getElementById('shipmentModalBody').innerHTML = `
            <p style="margin-bottom:16px;"><strong>Bestellung ${order.order_number}</strong> - ${order.items.length} Artikel</p>
            <div class="form-group">
                <label>Lager</label>
                <select id="shipmentWarehouse" class="form-control">${warehouseOptions}</select>
            </div>
            <div class="form-group">
                <label>Carrier</label>
                <select id="shipmentCarrier" class="form-control">${carrierOptions}</select>
            </div>
            <div class="form-group">
                <label>Artikel auswählen</label>
                <div id="shipmentItems">
                    ${order.items.map(item => `
                        <div class="item-row">
                            <input type="checkbox" checked data-item-id="${item.id}" data-max="${item.quantity}">
                            <div class="item-info">
                                <div class="item-name">${this.escapeHtml(item.name)}</div>
                                <div class="item-sku">${item.sku || '-'}</div>
                            </div>
                            <input type="number" class="item-qty form-control" value="${item.quantity}" min="1" max="${item.quantity}" data-item-id="${item.id}">
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

            document.getElementById('shipmentModal').classList.add('show');
        },

        async saveShipment() {
            if (!this.currentShipmentData) return;

            const items = [];
            document.querySelectorAll('#shipmentItems .item-row').forEach(row => {
                const checkbox = row.querySelector('input[type="checkbox"]');
                const qtyInput = row.querySelector('input[type="number"]');
                if (checkbox.checked) {
                    items.push({ order_item_id: parseInt(checkbox.dataset.itemId), quantity: parseInt(qtyInput.value) });
                }
            });

            const formData = new FormData();
            formData.append('action', 'create_shipment');
            formData.append('shop_id', this.shopId);
            formData.append('order_id', this.currentShipmentData.orderId);
            formData.append('warehouse_id', document.getElementById('shipmentWarehouse').value);
            formData.append('carrier_id', document.getElementById('shipmentCarrier').value);
            formData.append('items', JSON.stringify(items));

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Sendung erstellt: ' + data.shipment_number, 'success');
                    this.closeModal('shipmentModal');
                    this.loadStats();
                    this.loadPendingOrders();
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) { this.showToast('Fehler beim Erstellen', 'error'); }
        },

        async bulkCreateShipments() {
            for (const orderId of this.selectedOrders) {
                const formData = new FormData();
                formData.append('action', 'create_shipment');
                formData.append('shop_id', this.shopId);
                formData.append('order_id', orderId);

                try { await fetch(this.apiBase, { method: 'POST', body: formData }); } catch (e) { }
            }

            this.showToast(`${this.selectedOrders.length} Sendungen erstellt`, 'success');
            this.selectedOrders = [];
            this.loadStats();
            this.loadPendingOrders();
        },

        openTrackingModal(shipmentId) {
            document.getElementById('trackingShipmentId').value = shipmentId;
            document.getElementById('trackingNumberInput').value = '';
            document.getElementById('trackingModal').classList.add('show');
        },

        async saveTracking() {
            const shipmentId = document.getElementById('trackingShipmentId').value;
            const trackingNumber = document.getElementById('trackingNumberInput').value.trim();

            if (!trackingNumber) { this.showToast('Tracking-Nummer erforderlich', 'error'); return; }

            const formData = new FormData();
            formData.append('action', 'mark_shipped');
            formData.append('shop_id', this.shopId);
            formData.append('shipment_id', shipmentId);
            formData.append('tracking_number', trackingNumber);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Versandt!', 'success');
                    this.closeModal('trackingModal');
                    this.loadStats();
                    if (this.currentView === 'shipments') this.loadShipments();
                    else this.loadPendingOrders();
                } else {
                    this.showToast(data.error, 'error');
                }
            } catch (e) { this.showToast('Fehler', 'error'); }
        },

        openCarrierSettings() {
            document.querySelectorAll('#fulfillmentTabs .tab').forEach(t => t.classList.remove('active'));
            document.querySelector('#fulfillmentTabs .tab[data-view="carriers"]').classList.add('active');
            this.currentView = 'carriers';
            this.showView('carriers');
        },

        editCarrier(carrierId) {
            const carrier = this.carriers.find(c => c.id === carrierId);
            if (!carrier) return;

            this.editingCarrierId = carrierId;
            document.getElementById('carrierModalTitle').textContent = carrier.name + ' Einstellungen';

            document.getElementById('carrierModalBody').innerHTML = `
            <div class="form-group">
                <label>API Key</label>
                <input type="password" id="carrierApiKey" class="form-control" placeholder="Ihr ${carrier.name} API Key" value="${carrier.api_key || ''}">
            </div>
            <div class="form-group">
                <label>API Secret</label>
                <input type="password" id="carrierApiSecret" class="form-control" placeholder="API Secret" value="${carrier.api_secret || ''}">
            </div>
            <div class="form-group">
                <label>Kundennummer</label>
                <input type="text" id="carrierAccountNumber" class="form-control" placeholder="Ihre Kundennummer bei ${carrier.name}" value="${carrier.account_number || ''}">
            </div>
            <div class="form-group">
                <label><input type="checkbox" id="carrierTestMode" ${carrier.settings?.test_mode ? 'checked' : ''}> Testmodus</label>
            </div>
            <p style="font-size:13px;color:var(--text-muted);margin-top:16px;">
                <strong>Hinweis:</strong> Sie benötigen ein Geschäftskonto bei ${carrier.name}, um Labels zu erstellen.<br>
                <a href="${carrier.api_endpoint}" target="_blank">→ ${carrier.name} Developer Portal</a>
            </p>
        `;

            document.getElementById('carrierModal').classList.add('show');
        },

        async saveCarrierSettings() {
            if (!this.editingCarrierId) return;

            const formData = new FormData();
            formData.append('action', 'update_carrier');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.editingCarrierId);
            formData.append('api_key', document.getElementById('carrierApiKey').value);
            formData.append('api_secret', document.getElementById('carrierApiSecret').value);
            formData.append('account_number', document.getElementById('carrierAccountNumber').value);
            formData.append('settings', JSON.stringify({ test_mode: document.getElementById('carrierTestMode').checked }));

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Gespeichert', 'success');
                    this.closeModal('carrierModal');
                    await this.loadCarriers();
                    this.loadCarriersView();
                } else {
                    this.showToast(data.error, 'error');
                }
            } catch (e) { this.showToast('Fehler', 'error'); }
        },

        async toggleCarrier(carrierId) {
            const formData = new FormData();
            formData.append('action', 'toggle_carrier');
            formData.append('shop_id', this.shopId);
            formData.append('id', carrierId);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                await this.loadCarriers();
                this.loadCarriersView();
            } catch (e) { }
        },

        async setDefaultCarrier(carrierId) {
            const formData = new FormData();
            formData.append('action', 'set_default_carrier');
            formData.append('shop_id', this.shopId);
            formData.append('id', carrierId);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                await this.loadCarriers();
                this.loadCarriersView();
                this.showToast('Standard-Carrier gesetzt', 'success');
            } catch (e) { }
        },

        async generatePicklist() {
            if (this.selectedOrders.length === 0) {
                this.showToast('Bitte Bestellungen auswählen', 'error');
                return;
            }

            // First create shipments, then generate picklist
            const shipmentIds = [];
            for (const orderId of this.selectedOrders) {
                const formData = new FormData();
                formData.append('action', 'create_shipment');
                formData.append('shop_id', this.shopId);
                formData.append('order_id', orderId);

                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) shipmentIds.push(data.shipment_id);
            }

            if (shipmentIds.length === 0) {
                this.showToast('Keine Sendungen erstellt', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'generate_picklist');
            formData.append('shop_id', this.shopId);
            formData.append('shipment_ids', JSON.stringify(shipmentIds));

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Picklist erstellt: ' + data.picklist_number, 'success');
                    this.openPicklistPrint(data.picklist_id);
                    this.loadStats();
                    this.loadPendingOrders();
                }
            } catch (e) { this.showToast('Fehler', 'error'); }
        },

        async openPicklistPrint(picklistId) {
            const res = await fetch(`${this.apiBase}?action=get_picklist&shop_id=${this.shopId}&id=${picklistId}`);
            const data = await res.json();

            if (!data.success) return;

            const pl = data.picklist;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Picklist ${pl.picklist_number}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1 { font-size: 24px; margin-bottom: 5px; }
                    .meta { color: #666; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                    th { background: #f5f5f5; }
                    .checkbox { width: 24px; height: 24px; border: 2px solid #000; display: inline-block; }
                    @media print { .no-print { display: none; } }
                </style>
            </head>
            <body>
                <button onclick="window.print()" class="no-print" style="padding:10px 20px;margin-bottom:20px;">Drucken</button>
                <h1>Picklist ${pl.picklist_number}</h1>
                <div class="meta">Erstellt: ${new Date(pl.created_at).toLocaleString('de-DE')} | ${pl.items.length} Positionen</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width:40px;">✓</th>
                            <th>Lagerort</th>
                            <th>SKU</th>
                            <th>Produkt</th>
                            <th>Bestellung</th>
                            <th>Menge</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${pl.items.map(item => `
                            <tr>
                                <td><span class="checkbox"></span></td>
                                <td><strong>${item.location}</strong></td>
                                <td>${item.sku}</td>
                                <td>${item.product_name}</td>
                                <td>${item.order_number}</td>
                                <td><strong>${item.quantity}</strong></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </body>
            </html>
        `);
            printWindow.document.close();
        },

        async viewShipment(shipmentId) {
            // Get shipment data to find order_id
            try {
                const res = await fetch(`${this.apiBase}?action=get_shipment&shop_id=${this.shopId}&id=${shipmentId}`);
                const data = await res.json();
                if (data.success && data.shipment) {
                    window.location.href = `?page=orders/order_detail&id=${data.shipment.order_id}`;
                } else {
                    this.showToast('Sendung nicht gefunden', 'error');
                }
            } catch (e) {
                this.showToast('Fehler beim Laden', 'error');
            }
        },

        renderPagination(containerId, pagination, type) {
            const container = document.getElementById(containerId);
            if (pagination.total_pages <= 1) { container.innerHTML = ''; return; }

            container.innerHTML = `
            <button ${pagination.page <= 1 ? 'disabled' : ''} onclick="Fulfillment.goToPage('${type}', ${pagination.page - 1})">← Zurück</button>
            <span class="pagination-info">Seite ${pagination.page} von ${pagination.total_pages}</span>
            <button ${pagination.page >= pagination.total_pages ? 'disabled' : ''} onclick="Fulfillment.goToPage('${type}', ${pagination.page + 1})">Weiter →</button>
        `;
        },

        goToPage(type, page) {
            if (type === 'pending') { this.pendingPage = page; this.loadPendingOrders(); }
            else if (type === 'shipments') { this.shipmentsPage = page; this.loadShipments(); }
        },

        closeModal(id) {
            document.getElementById(id).classList.remove('show');
            this.currentShipmentData = null;
            this.editingCarrierId = null;
        },

        showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
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

    document.addEventListener('DOMContentLoaded', () => Fulfillment.init());
</script>