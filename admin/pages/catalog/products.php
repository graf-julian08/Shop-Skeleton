<?php /** Katalog - Produkte */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Produkte</h1>
        <p class="page-subtitle">Alle Produkte verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn" onclick="Products.exportProducts()"><span class="material-symbols-rounded">download</span>
            Export</button>
        <a href="?page=catalog/product_create" class="btn btn-primary"><span class="material-symbols-rounded">add</span>
            Produkt hinzufügen</a>
    </div>
</div>

<div class="tabs" id="productTabs">
    <button class="tab active" data-tab="alle" onclick="Products.switchTab('alle')">Alle <span
            class="badge badge-default" id="badgeAll">0</span></button>
    <button class="tab" data-tab="active" onclick="Products.switchTab('active')">Aktiv <span class="badge badge-success"
            id="badgeActive">0</span></button>
    <button class="tab" data-tab="draft" onclick="Products.switchTab('draft')">Entwurf <span class="badge badge-warning"
            id="badgeDraft">0</span></button>
    <button class="tab" data-tab="archived" onclick="Products.switchTab('archived')">Archiviert <span
            class="badge badge-default" id="badgeArchived">0</span></button>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filters -->
        <div class="filters">
            <div class="filter-search">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Produkte durchsuchen..."
                    oninput="Products.debounceSearch()">
            </div>
            <select class="filter-select" id="categoryFilter" onchange="Products.loadProducts()">
                <option value="">Alle Kategorien</option>
            </select>
            <select class="filter-select" id="typeFilter" onchange="Products.loadProducts()">
                <option value="">Alle Typen</option>
                <option value="simple">Physisch</option>
                <option value="digital">Digital</option>
                <option value="configurable">Konfigurierbar</option>
                <option value="bundle">Bundle</option>
            </select>
            <select class="filter-select" id="availabilityFilter" onchange="Products.loadProducts()">
                <option value="">Verfügbarkeit</option>
                <option value="in_stock">Auf Lager</option>
                <option value="out_of_stock">Ausverkauft</option>
                <option value="low_stock">Niedriger Bestand</option>
            </select>
            <select class="filter-select" id="sortFilter" onchange="Products.loadProducts()">
                <option value="created_at-DESC">Neueste zuerst</option>
                <option value="created_at-ASC">Älteste zuerst</option>
                <option value="name-ASC">Name A-Z</option>
                <option value="name-DESC">Name Z-A</option>
                <option value="price-ASC">Preis aufsteigend</option>
                <option value="price-DESC">Preis absteigend</option>
                <option value="quantity-DESC">Bestand hoch-niedrig</option>
            </select>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions" style="display:none;">
            <span id="selectedCount">0 ausgewählt</span>
            <button class="btn btn-sm" onclick="Products.bulkAction('activate')"><span
                    class="material-symbols-rounded">check_circle</span> Aktivieren</button>
            <button class="btn btn-sm" onclick="Products.bulkAction('deactivate')"><span
                    class="material-symbols-rounded">pause_circle</span> Deaktivieren</button>
            <button class="btn btn-sm" onclick="Products.bulkAction('archive')"><span
                    class="material-symbols-rounded">archive</span> Archivieren</button>
            <button class="btn btn-sm btn-danger-ghost" onclick="Products.bulkAction('delete')"><span
                    class="material-symbols-rounded">delete</span> Löschen</button>
        </div>

        <!-- Table -->
        <table class="table" id="productsTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" onchange="Products.toggleSelectAll()"></th>
                    <th>Produkt</th>
                    <th>Status</th>
                    <th>Inventar</th>
                    <th>Typ</th>
                    <th class="price-header">
                        <div class="price-currency-selector">
                            <span>Preis</span>
                            <select id="currencySelect" onchange="Products.changeCurrency()" class="currency-dropdown">
                                <option value="">Laden...</option>
                            </select>
                        </div>
                    </th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="productsBody">
                <tr>
                    <td colspan="7" class="loading-row"><span class="material-symbols-rounded spinning">sync</span> Lade
                        Produkte...</td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination" id="pagination"></div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="confirmModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="confirmModalTitle">Bestätigung</h3>
            <button class="modal-close" onclick="Products.closeConfirmModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="confirmModalMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Products.closeConfirmModal()">Abbrechen</button>
            <button class="btn btn-danger" id="confirmModalBtn" onclick="Products.confirmAction()">Löschen</button>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal" id="exportModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Produkte exportieren</h3>
            <button class="modal-close" onclick="Products.closeExportModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p style="margin-bottom:16px;color:var(--text-muted);">Wählen Sie das Export-Format:</p>
            <div class="export-options">
                <label class="export-option" onclick="Products.doExport('json')">
                    <span class="material-symbols-rounded">code</span>
                    <strong>JSON</strong>
                    <small>Alle Produktdaten als JSON-Datei</small>
                </label>
                <label class="export-option" onclick="Products.doExport('sql')">
                    <span class="material-symbols-rounded">database</span>
                    <strong>SQL</strong>
                    <small>INSERT-Statements für Datenbank</small>
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Products.closeExportModal()">Abbrechen</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .product-image {
        width: 40px;
        height: 40px;
        background: var(--bg-lighter);
        border-radius: var(--radius-sm);
        object-fit: cover;
        flex-shrink: 0;
    }

    .price-currency-selector {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .currency-dropdown {
        padding: 4px 8px;
        font-size: 12px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        cursor: pointer;
        min-width: 70px;
    }

    .currency-dropdown:hover {
        border-color: var(--accent);
    }

    .currency-dropdown:focus {
        outline: none;
        border-color: var(--accent);
    }

    .product-image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
    }

    .product-image-placeholder .material-symbols-rounded {
        font-size: 20px;
    }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
    }

    .pagination>div {
        display: flex;
        gap: 4px;
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

    .bulk-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
        margin-bottom: 16px;
    }

    .bulk-actions span {
        color: var(--text-muted);
        font-size: 13px;
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
        max-width: 400px;
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

    .export-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .export-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 24px 16px;
        background: var(--bg-tertiary);
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }

    .export-option:hover {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.1);
    }

    .export-option .material-symbols-rounded {
        font-size: 32px;
        color: var(--accent);
    }

    .export-option small {
        color: var(--text-muted);
        font-size: 12px;
    }

    .type-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: var(--radius-sm);
    }

    .type-simple {
        background: var(--bg-lighter);
        color: var(--text-secondary);
    }

    .type-digital {
        background: rgba(var(--info-rgb), 0.2);
        color: var(--info);
    }

    .type-configurable {
        background: rgba(var(--accent-rgb), 0.2);
        color: var(--accent);
    }

    .type-bundle {
        background: rgba(var(--warning-rgb), 0.2);
        color: var(--warning);
    }
</style>

<script>
    const Products = {
        apiBase: 'api/products.php',
        shopId: 1,
        currentTab: 'alle',
        currentPage: 1,
        selectedIds: [],
        searchTimeout: null,
        confirmCallback: null,
        currentCurrency: { code: 'USD', symbol: '$' },
        currencies: [],
        selectedDisplayCurrency: null,

        async init() {
            await this.loadCurrencies();
            await this.loadCategories();
            await this.loadStats();
            await this.loadProducts();

            // Restore tab from localStorage
            const savedTab = localStorage.getItem('productsTab');
            if (savedTab) {
                this.switchTab(savedTab);
            }
        },

        async loadCurrencies() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_shop_currency&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    this.currencies = data.currencies || [];
                    this.currentCurrency = data.default_currency || { code: 'USD', symbol: '$' };
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
            this.loadProducts();
        },

        async loadStats() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('badgeAll').textContent = data.stats.all;
                    document.getElementById('badgeActive').textContent = data.stats.active;
                    document.getElementById('badgeDraft').textContent = data.stats.draft;
                    document.getElementById('badgeArchived').textContent = data.stats.archived;
                }
            } catch (e) {
                console.error('Error loading stats:', e);
            }
        },

        async loadCategories() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_categories&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    const select = document.getElementById('categoryFilter');
                    data.categories.forEach(cat => {
                        const opt = document.createElement('option');
                        opt.value = cat.id;
                        opt.textContent = cat.name;
                        select.appendChild(opt);
                    });
                }
            } catch (e) {
                console.error('Error loading categories:', e);
            }
        },

        async loadProducts() {
            const tbody = document.getElementById('productsBody');
            tbody.innerHTML = '<tr><td colspan="7" class="loading-row"><span class="material-symbols-rounded spinning">sync</span> Lade Produkte...</td></tr>';

            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const type = document.getElementById('typeFilter').value;
            const availability = document.getElementById('availabilityFilter').value;
            const sortParts = document.getElementById('sortFilter').value.split('-');

            let status = '';
            if (this.currentTab === 'active') status = 'active';
            else if (this.currentTab === 'draft') status = 'draft';
            else if (this.currentTab === 'archived') status = 'archived';

            const params = new URLSearchParams({
                action: 'get_products',
                shop_id: this.shopId,
                page: this.currentPage,
                per_page: 20,
                search: search,
                status: status,
                type: type,
                category_id: category,
                availability: availability,
                sort_by: sortParts[0],
                sort_dir: sortParts[1] || 'DESC',
                display_currency: this.selectedDisplayCurrency || ''
            });

            try {
                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();

                if (data.success) {
                    // Store currency info
                    if (data.currency) {
                        this.currentCurrency = data.currency;
                    }
                    this.renderProducts(data.products);
                    this.renderPagination(data.pagination);
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="loading-row">Fehler beim Laden</td></tr>';
                }
            } catch (e) {
                tbody.innerHTML = '<tr><td colspan="7" class="loading-row">Fehler: ' + e.message + '</td></tr>';
            }
        },

        renderProducts(products) {
            const tbody = document.getElementById('productsBody');

            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="loading-row">Keine Produkte gefunden</td></tr>';
                return;
            }

            const typeLabels = {
                simple: 'Physisch',
                digital: 'Digital',
                configurable: 'Konfigurierbar',
                bundle: 'Bundle',
                grouped: 'Gruppiert'
            };

            const statusBadges = {
                active: '<span class="badge badge-success">Aktiv</span>',
                draft: '<span class="badge badge-warning">Entwurf</span>',
                archived: '<span class="badge badge-default">Archiviert</span>'
            };

            tbody.innerHTML = products.map(p => {
                const price = parseFloat(p.price).toFixed(2).replace('.', ',');
                const inventory = p.type === 'digital' && p.manage_stock == 0
                    ? '∞ Digital'
                    : (p.manage_stock == 0 ? '∞ Unbegrenzt' : `${p.quantity} auf Lager`);

                const inventoryClass = p.manage_stock == 1 && p.quantity == 0 ? 'style="color:var(--error)"' :
                    (p.manage_stock == 1 && p.quantity <= p.low_stock_threshold ? 'style="color:var(--warning)"' : '');

                // Thumbnail with fallback
                const thumbnailHtml = p.thumbnail
                    ? `<img src="${p.thumbnail}" alt="${this.escapeHtml(p.name)}" class="product-image">`
                    : `<div class="product-image product-image-placeholder"><span class="material-symbols-rounded">image</span></div>`;

                return `
                <tr data-id="${p.id}">
                    <td><input type="checkbox" class="product-checkbox" value="${p.id}" onchange="Products.updateSelection()"></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            ${thumbnailHtml}
                            <div>
                                <a href="?page=catalog/product_detail&id=${p.id}"><strong>${this.escapeHtml(p.name)}</strong></a><br>
                                <small style="color:var(--text-muted);">SKU: ${this.escapeHtml(p.sku)}</small>
                            </div>
                        </div>
                    </td>
                    <td>${statusBadges[p.status] || p.status}</td>
                    <td ${inventoryClass}>${inventory}</td>
                    <td><span class="type-badge type-${p.type}">${typeLabels[p.type] || p.type}</span></td>
                    <td>${p.display_symbol || this.currentCurrency.symbol}${parseFloat(p.display_price || p.price).toFixed(2).replace('.', ',')}</td>
                    <td class="table-actions">
                        <a href="?page=catalog/product_edit&id=${p.id}" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a>
                        <a href="?page=catalog/product_detail&id=${p.id}" class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></a>
                        <button class="btn btn-sm btn-danger-ghost" onclick="Products.deleteProduct(${p.id}, '${this.escapeHtml(p.name)}')"><span class="material-symbols-rounded">delete</span></button>
                    </td>
                </tr>
            `;
            }).join('');
        },

        renderPagination(pagination) {
            const container = document.getElementById('pagination');
            const from = (pagination.page - 1) * pagination.per_page + 1;
            const to = Math.min(pagination.page * pagination.per_page, pagination.total);

            let buttons = '';
            if (pagination.total_pages > 1) {
                buttons += `<button class="btn btn-sm" ${pagination.page === 1 ? 'disabled' : ''} onclick="Products.goToPage(${pagination.page - 1})">←</button>`;

                for (let i = 1; i <= Math.min(pagination.total_pages, 5); i++) {
                    const active = i === pagination.page ? 'style="background:var(--accent);"' : '';
                    buttons += `<button class="btn btn-sm" ${active} onclick="Products.goToPage(${i})">${i}</button>`;
                }

                buttons += `<button class="btn btn-sm" ${pagination.page === pagination.total_pages ? 'disabled' : ''} onclick="Products.goToPage(${pagination.page + 1})">→</button>`;
            }

            container.innerHTML = `
            <span>${pagination.total > 0 ? `${from}-${to} von ${pagination.total} Produkten` : 'Keine Produkte'}</span>
            <div>${buttons}</div>
        `;
        },

        switchTab(tab) {
            this.currentTab = tab;
            this.currentPage = 1;
            localStorage.setItem('productsTab', tab);

            document.querySelectorAll('#productTabs .tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`#productTabs .tab[data-tab="${tab}"]`).classList.add('active');

            this.loadProducts();
        },

        goToPage(page) {
            this.currentPage = page;
            this.loadProducts();
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentPage = 1;
                this.loadProducts();
            }, 300);
        },

        toggleSelectAll() {
            const checked = document.getElementById('selectAll').checked;
            document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = checked);
            this.updateSelection();
        },

        updateSelection() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            this.selectedIds = Array.from(checkboxes).map(cb => cb.value);

            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            if (this.selectedIds.length > 0) {
                bulkActions.style.display = 'flex';
                selectedCount.textContent = `${this.selectedIds.length} ausgewählt`;
            } else {
                bulkActions.style.display = 'none';
            }
        },

        async bulkAction(action) {
            if (this.selectedIds.length === 0) return;

            if (action === 'delete') {
                this.showConfirmModal(
                    'Produkte löschen',
                    `Möchten Sie ${this.selectedIds.length} Produkte wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.`,
                    async () => {
                        await this.executeBulkAction('delete');
                    }
                );
                return;
            }

            await this.executeBulkAction(action);
        },

        async executeBulkAction(action) {
            const formData = new FormData();
            formData.append('action', 'bulk_action');
            formData.append('shop_id', this.shopId);
            formData.append('bulk_action', action);
            formData.append('ids', JSON.stringify(this.selectedIds));

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.selectedIds = [];
                    document.getElementById('selectAll').checked = false;
                    document.getElementById('bulkActions').style.display = 'none';
                    await this.loadStats();
                    await this.loadProducts();
                } else {
                    this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        deleteProduct(id, name) {
            this.showConfirmModal(
                'Produkt löschen',
                `Möchten Sie "${name}" wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.`,
                async () => {
                    const formData = new FormData();
                    formData.append('action', 'delete_product');
                    formData.append('shop_id', this.shopId);
                    formData.append('id', id);

                    try {
                        const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                        const data = await res.json();

                        if (data.success) {
                            this.showToast('Produkt gelöscht', 'success');
                            await this.loadStats();
                            await this.loadProducts();
                        } else {
                            this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
                        }
                    } catch (e) {
                        this.showToast('Fehler: ' + e.message, 'error');
                    }
                }
            );
        },

        showConfirmModal(title, message, callback) {
            document.getElementById('confirmModalTitle').textContent = title;
            document.getElementById('confirmModalMessage').textContent = message;
            this.confirmCallback = callback;
            document.getElementById('confirmModal').style.display = 'flex';
        },

        closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
            this.confirmCallback = null;
        },

        async confirmAction() {
            if (this.confirmCallback) {
                await this.confirmCallback();
            }
            this.closeConfirmModal();
        },

        exportProducts() {
            document.getElementById('exportModal').style.display = 'flex';
        },

        closeExportModal() {
            document.getElementById('exportModal').style.display = 'none';
        },

        doExport(format) {
            this.closeExportModal();
            this.showToast('Export wird vorbereitet...', 'success');
            window.location.href = `${this.apiBase}?action=export_products&shop_id=${this.shopId}&format=${format}`;
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    document.addEventListener('DOMContentLoaded', () => Products.init());
</script>