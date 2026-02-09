<?php /** Katalog - Inventar */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Inventar</h1>
        <p class="page-subtitle">Lagerbestand verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn" onclick="Inventory.exportInventory()">
            <span class="material-symbols-rounded">download</span> Export
        </button>
        <button class="btn" onclick="Inventory.openBulkUpdateModal()">
            <span class="material-symbols-rounded">upload</span> Bestand aktualisieren
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid" id="kpiGrid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Gesamtbestand</span></div>
        <div class="kpi-value" id="kpiTotalStock">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Niedriger Bestand</span></div>
        <div class="kpi-value" id="kpiLowStock" style="color:var(--warning);">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Ausverkauft</span></div>
        <div class="kpi-value" id="kpiOutOfStock" style="color:var(--error);">-</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Reserviert</span></div>
        <div class="kpi-value" id="kpiReserved" style="color:var(--info);">-</div>
    </div>
</div>

<!-- Low Stock Alert -->
<div class="alert alert-warning" id="lowStockAlert" style="display:none;">
    <span class="material-symbols-rounded">warning</span>
    <div class="alert-content"><strong id="lowStockCount">0</strong> Produkte haben niedrigen Lagerbestand und sollten
        nachbestellt werden.</div>
</div>

<!-- Inventory Table Card -->
<div class="card">
    <div class="card-header">
        <h3>Bestandsübersicht</h3>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div class="filters">
            <div class="filter-search">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Produkt oder SKU suchen..."
                    oninput="Inventory.debounceSearch()">
            </div>
            <select class="filter-select" id="statusFilter" onchange="Inventory.loadProducts()">
                <option value="">Alle Status</option>
                <option value="in_stock">Auf Lager</option>
                <option value="low_stock">Niedriger Bestand</option>
                <option value="out_of_stock">Ausverkauft</option>
            </select>
            <select class="filter-select" id="sortFilter" onchange="Inventory.loadProducts()">
                <option value="availability-ASC">Verfügbarkeit (kritisch zuerst)</option>
                <option value="availability-DESC">Verfügbarkeit (auf Lager zuerst)</option>
                <option value="name-ASC">Name A-Z</option>
                <option value="name-DESC">Name Z-A</option>
                <option value="quantity-ASC">Bestand aufsteigend</option>
                <option value="quantity-DESC">Bestand absteigend</option>
                <option value="sku-ASC">SKU A-Z</option>
            </select>
        </div>

        <!-- Table -->
        <div id="loadingState" class="loading-state">
            <span class="material-symbols-rounded spinning">sync</span>
            <p>Lade Bestandsdaten...</p>
        </div>

        <div id="emptyState" class="empty-state" style="display:none;">
            <span class="material-symbols-rounded">inventory_2</span>
            <p>Keine Produkte gefunden</p>
        </div>

        <table class="table" id="inventoryTable" style="display:none;">
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>SKU</th>
                    <th style="width:100px;">Bestand</th>
                    <th style="width:100px;">Reserviert</th>
                    <th style="width:100px;">Verfügbar</th>
                    <th style="width:120px;">Status</th>
                    <th style="width:150px;">Aktionen</th>
                </tr>
            </thead>
            <tbody id="inventoryTableBody"></tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination" id="pagination"></div>
    </div>
</div>

<!-- Warehouses Card -->
<div class="card">
    <div class="card-header">
        <h3>Lager</h3>
        <button class="btn btn-sm" onclick="Inventory.openWarehouseModal()">
            <span class="material-symbols-rounded">add</span> Lager hinzufügen
        </button>
    </div>
    <div class="card-body">
        <div class="stats-grid" id="warehouseGrid">
            <div class="loading-state">
                <span class="material-symbols-rounded spinning">sync</span>
                <p>Lade Lager...</p>
            </div>
        </div>
    </div>
</div>

<!-- Stock Update Modal -->
<div class="modal-overlay" id="stockUpdateModal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h2>Bestand anpassen</h2>
            <button class="btn btn-icon" onclick="Inventory.closeStockModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="stockProductId">
            <p>Produkt: <strong id="stockProductName"></strong></p>
            <p>Aktueller Bestand: <strong id="stockCurrentQty"></strong></p>

            <div class="form-group" style="margin-top:16px;">
                <label class="form-label">Anpassungsart</label>
                <select class="form-input" id="stockAdjustmentType">
                    <option value="set">Auf Wert setzen</option>
                    <option value="add">Hinzufügen</option>
                    <option value="subtract">Abziehen</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Menge</label>
                <input type="number" class="form-input" id="stockQuantity" min="0" value="0">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Inventory.closeStockModal()">Abbrechen</button>
            <button class="btn btn-primary" onclick="Inventory.saveStock()">
                <span class="material-symbols-rounded">save</span> Speichern
            </button>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal-overlay" id="bulkUpdateModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Massenaktualisierung</h2>
            <button class="btn btn-icon" onclick="Inventory.closeBulkUpdateModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Fügen Sie SKU und Menge als CSV-Format hinzu (eine Zeile pro Produkt):</p>
            <textarea class="form-input" id="bulkUpdateData" rows="10" placeholder="SKU;Menge
PROD-001;50
PROD-002;25
..."></textarea>
            <p class="form-hint" style="margin-top:8px;">Format: SKU;Menge (Semikolon-getrennt)</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Inventory.closeBulkUpdateModal()">Abbrechen</button>
            <button class="btn btn-primary" onclick="Inventory.processBulkUpdate()">
                <span class="material-symbols-rounded">upload</span> Importieren
            </button>
        </div>
    </div>
</div>

<!-- Warehouse Modal -->
<div class="modal-overlay" id="warehouseModal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h2 id="warehouseModalTitle">Neues Lager</h2>
            <button class="btn btn-icon" onclick="Inventory.closeWarehouseModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="warehouseId">
            <div class="form-group">
                <label class="form-label">Lagername <span class="required">*</span></label>
                <input type="text" class="form-input" id="warehouseName" placeholder="z.B. Hauptlager Berlin">
            </div>
            <div class="form-group">
                <label class="form-label">Standort</label>
                <input type="text" class="form-input" id="warehouseLocation"
                    placeholder="z.B. Musterstraße 1, 12345 Berlin">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Inventory.closeWarehouseModal()">Abbrechen</button>
            <button class="btn btn-primary" onclick="Inventory.saveWarehouse()">
                <span class="material-symbols-rounded">save</span> Speichern
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteConfirmModal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h2>Lager löschen</h2>
            <button class="btn btn-icon" onclick="Inventory.closeDeleteModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body">
            <div style="text-align: center; padding: 20px 0;">
                <span class="material-symbols-rounded"
                    style="font-size: 48px; color: var(--error); margin-bottom: 16px; display: block;">warning</span>
                <p style="font-size: 16px; margin-bottom: 8px;">Möchten Sie dieses Lager wirklich löschen?</p>
                <p style="color: var(--text-muted);" id="deleteWarehouseName"></p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Inventory.closeDeleteModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="Inventory.confirmDeleteWarehouse()"
                style="background: var(--error); color: white;">
                <span class="material-symbols-rounded">delete</span> Löschen
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<style>
    /* KPI Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: var(--card-bg);
        border-radius: var(--radius-md);
        padding: 20px;
        border: 1px solid var(--border-color);
    }

    .kpi-header {
        margin-bottom: 8px;
    }

    .kpi-title {
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 500;
    }

    .kpi-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
    }

    /* Filters */
    .filters {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-search {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-tertiary);
        padding: 8px 16px;
        border-radius: var(--radius-md);
        flex: 1;
        min-width: 200px;
    }

    .filter-search input {
        background: none;
        border: none;
        color: var(--text);
        outline: none;
        width: 100%;
    }

    .filter-select {
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        color: var(--text);
        padding: 8px 16px;
        border-radius: var(--radius-md);
        cursor: pointer;
    }

    /* Loading/Empty States */
    .loading-state,
    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--text-muted);
    }

    .loading-state .material-symbols-rounded,
    .empty-state .material-symbols-rounded {
        font-size: 48px;
        margin-bottom: 12px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .spinning {
        animation: spin 1s linear infinite;
    }

    /* Table Row Highlights */
    .row-out-of-stock {
        background: rgba(239, 68, 68, 0.1) !important;
    }

    .row-low-stock {
        background: rgba(245, 158, 11, 0.1) !important;
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: var(--card-bg);
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    }

    .modal-content.modal-sm {
        max-width: 400px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-header h2 {
        margin: 0;
        font-size: 18px;
    }

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
    }

    /* Form */
    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: var(--text);
    }

    .form-input {
        width: 100%;
        padding: 10px 14px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text);
    }

    .form-input:focus {
        outline: none;
        border-color: var(--accent);
    }

    .form-hint {
        font-size: 12px;
        color: var(--text-muted);
    }

    .required {
        color: var(--error);
    }

    /* Stats Grid (Warehouses) */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
    }

    .stat-card {
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-card-content {
        flex: 1;
    }

    .stat-card-label {
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }

    .stat-card-value {
        color: var(--text-muted);
        font-size: 14px;
    }

    .stat-card-actions {
        display: flex;
        gap: 4px;
    }

    /* Product thumbnail */
    .product-thumb {
        width: 40px;
        height: 40px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-sm);
        object-fit: cover;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Toast */
    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 16px 24px;
        border-radius: var(--radius-md);
        color: white;
        font-weight: 500;
        z-index: 2000;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s;
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

    /* Pagination */
    .pagination {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination button {
        padding: 8px 12px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text);
        cursor: pointer;
    }

    .pagination button:hover {
        background: var(--bg-secondary);
    }

    .pagination button.active {
        background: var(--accent);
        border-color: var(--accent);
        color: white;
    }

    .pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Table actions */
    .table-actions {
        display: flex;
        gap: 4px;
    }

    .btn-icon {
        padding: 8px;
        min-width: auto;
    }

    /* Alert */
    .alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-radius: var(--radius-md);
        margin-bottom: 24px;
    }

    .alert-warning {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .alert-content strong {
        color: inherit;
    }
</style>

<script>
    const Inventory = {
        shopId: 1,
        products: [],
        currentPage: 1,
        totalPages: 1,
        searchTimeout: null,

        init() {
            this.loadStats();
            this.loadProducts();
            this.loadWarehouses();
            this.setupEventListeners();
        },

        setupEventListeners() {
            // Modal close on overlay click
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                    }
                });
            });

            // ESC key to close modals
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
                }
            });
        },

        // ========================
        // STATS
        // ========================
        async loadStats() {
            try {
                const res = await fetch(`api/inventory.php?action=get_inventory_stats&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    document.getElementById('kpiTotalStock').textContent = this.formatNumber(data.stats.total_stock);
                    document.getElementById('kpiLowStock').textContent = data.stats.low_stock;
                    document.getElementById('kpiOutOfStock').textContent = data.stats.out_of_stock;
                    document.getElementById('kpiReserved').textContent = data.stats.reserved;

                    // Show/hide low stock alert
                    const alertEl = document.getElementById('lowStockAlert');
                    if (data.stats.low_stock > 0) {
                        document.getElementById('lowStockCount').textContent = data.stats.low_stock;
                        alertEl.style.display = 'flex';
                    } else {
                        alertEl.style.display = 'none';
                    }
                }
            } catch (e) {
                console.error('Error loading stats:', e);
            }
        },

        // ========================
        // PRODUCTS
        // ========================
        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentPage = 1;
                this.loadProducts();
            }, 300);
        },

        async loadProducts(page = 1) {
            this.currentPage = page;

            const loading = document.getElementById('loadingState');
            const empty = document.getElementById('emptyState');
            const table = document.getElementById('inventoryTable');

            loading.style.display = 'block';
            empty.style.display = 'none';
            table.style.display = 'none';

            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const sortParts = document.getElementById('sortFilter').value.split('-');
            const sortBy = sortParts[0];
            const sortDir = sortParts[1];

            try {
                const res = await fetch(`api/inventory.php?action=get_inventory_products&shop_id=${this.shopId}&page=${page}&search=${encodeURIComponent(search)}&stock_status=${status}&sort_by=${sortBy}&sort_dir=${sortDir}`);
                const data = await res.json();

                if (data.success) {
                    this.products = data.products;
                    this.totalPages = data.pagination.total_pages;
                    this.renderProducts();
                    this.renderPagination(data.pagination);
                } else {
                    this.showToast(data.error || 'Fehler beim Laden', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler', 'error');
            }

            loading.style.display = 'none';
        },

        renderProducts() {
            const tbody = document.getElementById('inventoryTableBody');
            const table = document.getElementById('inventoryTable');
            const empty = document.getElementById('emptyState');

            if (this.products.length === 0) {
                empty.style.display = 'block';
                table.style.display = 'none';
                return;
            }

            table.style.display = 'table';
            empty.style.display = 'none';

            tbody.innerHTML = this.products.map(product => {
                let rowClass = '';
                let statusBadge = '';

                if (product.stock_status === 'out_of_stock') {
                    rowClass = 'row-out-of-stock';
                    statusBadge = '<span class="badge badge-error">Ausverkauft</span>';
                } else if (product.stock_status === 'low_stock') {
                    rowClass = 'row-low-stock';
                    statusBadge = '<span class="badge badge-warning">Niedriger Bestand</span>';
                } else if (product.stock_status === 'unlimited') {
                    statusBadge = '<span class="badge badge-info">Unbegrenzt</span>';
                } else {
                    statusBadge = '<span class="badge badge-success">Auf Lager</span>';
                }

                const thumbnail = product.thumbnail
                    ? `<img src="${product.thumbnail}" class="product-thumb" alt="" onerror="this.style.display='none'">`
                    : `<div class="product-thumb" style="display:flex;align-items:center;justify-content:center;"><span class="material-symbols-rounded" style="color:var(--text-muted);">inventory_2</span></div>`;

                return `
                <tr class="${rowClass}">
                    <td>
                        <div class="product-info">
                            ${thumbnail}
                            <div>
                                <strong>${this.escapeHtml(product.name)}</strong>
                                <br><small style="color:var(--text-muted);">${product.type}</small>
                            </div>
                        </div>
                    </td>
                    <td>${this.escapeHtml(product.sku)}</td>
                    <td>${product.manage_stock ? product.quantity : '∞'}</td>
                    <td>${product.reserved || 0}</td>
                    <td>${product.manage_stock ? product.available : '∞'}</td>
                    <td>${statusBadge}</td>
                    <td class="table-actions">
                        <button class="btn btn-sm btn-icon" onclick="Inventory.openStockModal(${product.id}, '${this.escapeHtml(product.name)}', ${product.quantity})" title="Bestand anpassen">
                            <span class="material-symbols-rounded">edit</span>
                        </button>
                        <a href="?page=catalog/product_edit&id=${product.id}" class="btn btn-sm btn-icon" title="Produkt bearbeiten">
                            <span class="material-symbols-rounded">open_in_new</span>
                        </a>
                    </td>
                </tr>
            `;
            }).join('');
        },

        renderPagination(pagination) {
            const container = document.getElementById('pagination');

            if (pagination.total_pages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';

            // Previous button
            html += `<button ${pagination.page === 1 ? 'disabled' : ''} onclick="Inventory.loadProducts(${pagination.page - 1})">
            <span class="material-symbols-rounded">chevron_left</span>
        </button>`;

            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === 1 || i === pagination.total_pages || (i >= pagination.page - 2 && i <= pagination.page + 2)) {
                    html += `<button class="${i === pagination.page ? 'active' : ''}" onclick="Inventory.loadProducts(${i})">${i}</button>`;
                } else if (i === pagination.page - 3 || i === pagination.page + 3) {
                    html += `<button disabled>...</button>`;
                }
            }

            // Next button
            html += `<button ${pagination.page === pagination.total_pages ? 'disabled' : ''} onclick="Inventory.loadProducts(${pagination.page + 1})">
            <span class="material-symbols-rounded">chevron_right</span>
        </button>`;

            container.innerHTML = html;
        },

        // ========================
        // STOCK UPDATE MODAL
        // ========================
        openStockModal(productId, productName, currentQty) {
            document.getElementById('stockProductId').value = productId;
            document.getElementById('stockProductName').textContent = productName;
            document.getElementById('stockCurrentQty').textContent = currentQty;
            document.getElementById('stockQuantity').value = currentQty;
            document.getElementById('stockAdjustmentType').value = 'set';
            document.getElementById('stockUpdateModal').classList.add('active');
        },

        closeStockModal() {
            document.getElementById('stockUpdateModal').classList.remove('active');
        },

        async saveStock() {
            const productId = document.getElementById('stockProductId').value;
            const quantity = document.getElementById('stockQuantity').value;
            const adjustmentType = document.getElementById('stockAdjustmentType').value;

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            formData.append('adjustment_type', adjustmentType);

            try {
                const res = await fetch(`api/inventory.php?action=update_stock&shop_id=${this.shopId}`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.closeStockModal();
                    this.loadProducts(this.currentPage);
                    this.loadStats();
                } else {
                    this.showToast(data.error || 'Fehler beim Speichern', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler', 'error');
            }
        },

        // ========================
        // BULK UPDATE
        // ========================
        openBulkUpdateModal() {
            document.getElementById('bulkUpdateData').value = '';
            document.getElementById('bulkUpdateModal').classList.add('active');
        },

        closeBulkUpdateModal() {
            document.getElementById('bulkUpdateModal').classList.remove('active');
        },

        async processBulkUpdate() {
            const rawData = document.getElementById('bulkUpdateData').value.trim();

            if (!rawData) {
                this.showToast('Bitte Daten eingeben', 'error');
                return;
            }

            // Parse CSV-like data
            const lines = rawData.split('\n');
            const updates = [];

            for (const line of lines) {
                const parts = line.split(';');
                if (parts.length >= 2) {
                    const sku = parts[0].trim();
                    const quantity = parseInt(parts[1].trim(), 10);

                    if (sku && !isNaN(quantity)) {
                        updates.push({ sku, quantity });
                    }
                }
            }

            if (updates.length === 0) {
                this.showToast('Keine gültigen Daten gefunden', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('updates', JSON.stringify(updates));

            try {
                const res = await fetch(`api/inventory.php?action=bulk_update_stock&shop_id=${this.shopId}`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.closeBulkUpdateModal();
                    this.loadProducts();
                    this.loadStats();
                } else {
                    this.showToast(data.error || 'Fehler beim Import', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler', 'error');
            }
        },

        // ========================
        // EXPORT
        // ========================
        async exportInventory() {
            try {
                const res = await fetch(`api/inventory.php?action=export_inventory&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    // Convert to CSV
                    const csvContent = data.data.map(row => row.join(';')).join('\n');

                    // Create download
                    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = data.filename;
                    link.click();

                    this.showToast('Export heruntergeladen', 'success');
                } else {
                    this.showToast(data.error || 'Export fehlgeschlagen', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler', 'error');
            }
        },

        // ========================
        // WAREHOUSES
        // ========================
        async loadWarehouses() {
            try {
                const res = await fetch(`api/inventory.php?action=get_warehouses&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    this.renderWarehouses(data.warehouses);
                }
            } catch (e) {
                console.error('Error loading warehouses:', e);
            }
        },

        renderWarehouses(warehouses) {
            const container = document.getElementById('warehouseGrid');

            if (warehouses.length === 0) {
                container.innerHTML = '<p style="color:var(--text-muted);">Keine Lager vorhanden</p>';
                return;
            }

            container.innerHTML = warehouses.map(wh => `
            <div class="stat-card">
                <div class="stat-card-content">
                    <div class="stat-card-label">${this.escapeHtml(wh.name)}</div>
                    <div class="stat-card-value">${this.formatNumber(wh.item_count)} Artikel${wh.location ? ' • ' + this.escapeHtml(wh.location) : ''}</div>
                </div>
                <div class="stat-card-actions">
                    <button class="btn btn-sm btn-icon" onclick="Inventory.editWarehouse(${wh.id}, '${this.escapeHtml(wh.name)}', '${this.escapeHtml(wh.location || '')}')" title="Bearbeiten">
                        <span class="material-symbols-rounded">edit</span>
                    </button>
                    ${!wh.is_default ? `
                        <button class="btn btn-sm btn-icon" onclick="Inventory.deleteWarehouse(${wh.id}, '${this.escapeHtml(wh.name)}')" title="Löschen">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    ` : ''}
                </div>
            </div>
        `).join('');
        },

        openWarehouseModal() {
            document.getElementById('warehouseModalTitle').textContent = 'Neues Lager';
            document.getElementById('warehouseId').value = '';
            document.getElementById('warehouseName').value = '';
            document.getElementById('warehouseLocation').value = '';
            document.getElementById('warehouseModal').classList.add('active');
        },

        editWarehouse(id, name, location) {
            document.getElementById('warehouseModalTitle').textContent = 'Lager bearbeiten';
            document.getElementById('warehouseId').value = id;
            document.getElementById('warehouseName').value = name;
            document.getElementById('warehouseLocation').value = location;
            document.getElementById('warehouseModal').classList.add('active');
        },

        closeWarehouseModal() {
            document.getElementById('warehouseModal').classList.remove('active');
        },

        async saveWarehouse() {
            const id = document.getElementById('warehouseId').value;
            const name = document.getElementById('warehouseName').value.trim();
            const location = document.getElementById('warehouseLocation').value.trim();

            if (!name) {
                this.showToast('Lagername erforderlich', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('name', name);
            formData.append('location', location);

            const action = id ? 'update_warehouse' : 'add_warehouse';
            if (id) formData.append('id', id);

            try {
                const res = await fetch(`api/inventory.php?action=${action}&shop_id=${this.shopId}`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.closeWarehouseModal();
                    this.loadWarehouses();
                } else {
                    this.showToast(data.error || 'Fehler beim Speichern', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler', 'error');
            }
        },

        deleteWarehouseId: null,

        deleteWarehouse(id, name) {
            this.deleteWarehouseId = id;
            document.getElementById('deleteWarehouseName').textContent = name || 'Lager #' + id;
            document.getElementById('deleteConfirmModal').classList.add('active');
        },

        closeDeleteModal() {
            document.getElementById('deleteConfirmModal').classList.remove('active');
            this.deleteWarehouseId = null;
        },

        async confirmDeleteWarehouse() {
            if (!this.deleteWarehouseId) return;

            const formData = new FormData();
            formData.append('id', this.deleteWarehouseId);

            try {
                const res = await fetch(`api/inventory.php?action=delete_warehouse&shop_id=${this.shopId}`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.loadWarehouses();
                } else {
                    this.showToast(data.error || 'Fehler beim Löschen', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler', 'error');
            }

            this.closeDeleteModal();
        },

        // ========================
        // UTILS
        // ========================
        formatNumber(num) {
            return new Intl.NumberFormat('de-DE').format(num);
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type + ' show';
            setTimeout(() => toast.classList.remove('show'), 4000);
        }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => Inventory.init());
</script>