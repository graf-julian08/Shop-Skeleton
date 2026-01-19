<?php /** Katalog - Bundles Liste */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Bundles</h1>
        <p class="page-subtitle">Produktsets und Bundle-Angebote verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/bundle_create" class="btn btn-primary">
            <span class="material-symbols-rounded">add</span> Bundle erstellen
        </a>
    </div>
</div>

<!-- Stats Tabs -->
<div class="bundle-tabs" id="bundleTabs">
    <button class="tab-btn active" data-status="all">Alle <span class="badge" id="countAll">0</span></button>
    <button class="tab-btn" data-status="active">Aktiv <span class="badge" id="countActive">0</span></button>
    <button class="tab-btn" data-status="draft">Entwurf <span class="badge" id="countDraft">0</span></button>
    <button class="tab-btn" data-status="archived">Archiviert <span class="badge" id="countArchived">0</span></button>
</div>

<!-- Bundle List -->
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Alle Bundles</h3>
        <div class="search-box">
            <span class="material-symbols-rounded">search</span>
            <input type="text" id="bundleSearch" placeholder="Suchen..." oninput="BundleManager.debounceSearch()">
        </div>
    </div>
    <div class="card-body">
        <div id="bundleLoading" class="loading-state">
            <span class="material-symbols-rounded spinning">sync</span>
            <p>Lade Bundles...</p>
        </div>
        <div id="bundleEmpty" class="empty-state" style="display:none;">
            <span class="material-symbols-rounded">inventory_2</span>
            <p>Keine Bundles gefunden</p>
            <a href="?page=catalog/bundle_create" class="btn btn-primary">Erstes Bundle erstellen</a>
        </div>
        <table class="table" id="bundleTable" style="display:none;">
            <thead>
                <tr>
                    <th>Bundle</th>
                    <th style="width:100px;">Produkte</th>
                    <th style="width:150px;">Preisvorteil</th>
                    <th style="width:120px;">Gültigkeit</th>
                    <th style="width:100px;">Verkauft</th>
                    <th style="width:100px;">Status</th>
                    <th style="width:120px;">Aktionen</th>
                </tr>
            </thead>
            <tbody id="bundleTableBody"></tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h2>Bundle löschen</h2>
            <button class="btn btn-icon" onclick="BundleManager.closeDeleteModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body" style="text-align:center; padding:24px;">
            <span class="material-symbols-rounded" style="font-size:48px; color:var(--error);">warning</span>
            <p style="margin-top:16px;">Sind Sie sicher, dass Sie das Bundle <strong id="deleteBundleName"></strong>
                löschen möchten?</p>
            <p style="color:var(--text-muted); font-size:13px;">Diese Aktion kann nicht rückgängig gemacht werden.</p>
        </div>
        <div class="modal-footer" style="justify-content:center;">
            <button class="btn" onclick="BundleManager.closeDeleteModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="BundleManager.confirmDelete()">
                <span class="material-symbols-rounded">delete</span> Löschen
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<style>
    /* Tabs */
    .bundle-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 10px 20px;
        background: var(--bg-secondary);
        border: none;
        border-radius: var(--radius-md);
        color: var(--text);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .tab-btn:hover {
        background: var(--bg-tertiary);
    }

    .tab-btn.active {
        background: var(--accent);
        color: white;
    }

    .tab-btn .badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
    }

    /* Search Box */
    .search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-tertiary);
        padding: 8px 16px;
        border-radius: var(--radius-md);
    }

    .search-box input {
        background: none;
        border: none;
        color: var(--text);
        outline: none;
        width: 200px;
    }

    /* Loading/Empty States */
    .loading-state,
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .loading-state .spinning,
    .empty-state .material-symbols-rounded {
        font-size: 48px;
        margin-bottom: 16px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .spinning {
        animation: spin 1s linear infinite;
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
        max-width: 400px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
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

    /* Table */
    .table-actions {
        display: flex;
        gap: 4px;
    }

    .btn-icon {
        padding: 8px;
    }

    .btn-danger {
        background: var(--error);
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    /* Badges */
    .badge-info {
        background: var(--accent);
        color: white;
    }

    /* Bundle Type Badge */
    .bundle-type-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
        background: var(--bg-tertiary);
        color: var(--text-muted);
    }

    .bundle-type-badge.limited {
        background: rgba(251, 191, 36, 0.2);
        color: #f59e0b;
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
</style>

<script>
    const BundleManager = {
        bundles: [],
        currentStatus: 'all',
        searchTimeout: null,
        deleteTargetId: null,
        shopId: 1,

        init() {
            this.loadBundles();
            this.setupEventListeners();
        },

        setupEventListeners() {
            // Tab clicks
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    this.currentStatus = btn.dataset.status;
                    this.loadBundles();
                });
            });

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

        async loadBundles() {
            const tableBody = document.getElementById('bundleTableBody');
            const table = document.getElementById('bundleTable');
            const loading = document.getElementById('bundleLoading');
            const empty = document.getElementById('bundleEmpty');
            const search = document.getElementById('bundleSearch').value;

            loading.style.display = 'block';
            table.style.display = 'none';
            empty.style.display = 'none';

            try {
                const res = await fetch(`api/bundles.php?action=get_bundles&shop_id=${this.shopId}&status=${this.currentStatus}&search=${encodeURIComponent(search)}`);
                const data = await res.json();

                if (data.success) {
                    this.bundles = data.bundles;
                    this.updateStats(data.stats);
                    this.renderBundles();
                } else {
                    this.showToast(data.error || 'Fehler beim Laden', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler', 'error');
            }

            loading.style.display = 'none';
        },

        updateStats(stats) {
            document.getElementById('countAll').textContent = stats.total || 0;
            document.getElementById('countActive').textContent = stats.active || 0;
            document.getElementById('countDraft').textContent = stats.draft || 0;
            document.getElementById('countArchived').textContent = stats.archived || 0;
        },

        renderBundles() {
            const tableBody = document.getElementById('bundleTableBody');
            const table = document.getElementById('bundleTable');
            const empty = document.getElementById('bundleEmpty');

            if (this.bundles.length === 0) {
                empty.style.display = 'block';
                table.style.display = 'none';
                return;
            }

            table.style.display = 'table';
            empty.style.display = 'none';

            tableBody.innerHTML = this.bundles.map(bundle => {
                const statusBadge = bundle.status === 'active'
                    ? '<span class="badge badge-success">Aktiv</span>'
                    : bundle.status === 'draft'
                        ? '<span class="badge badge-warning">Entwurf</span>'
                        : '<span class="badge badge-secondary">Archiviert</span>';

                const discount = this.formatDiscount(bundle);
                const validity = this.formatValidity(bundle);
                const typeBadge = bundle.bundle_type === 'limited'
                    ? '<span class="bundle-type-badge limited">Zeitlich begrenzt</span>'
                    : '';

                return `
            <tr>
                <td>
                    <a href="?page=catalog/bundle_edit&id=${bundle.id}">
                        <strong>${this.escapeHtml(bundle.name)}</strong>
                    </a>
                    <br><small style="color:var(--text-muted);">${this.escapeHtml(bundle.slug)}</small>
                    ${typeBadge ? '<br>' + typeBadge : ''}
                </td>
                <td>${bundle.product_count} <small style="color:var(--text-muted);">(${bundle.total_items || bundle.product_count} Stück)</small></td>
                <td>${discount}</td>
                <td>${validity}</td>
                <td>${bundle.sold_count}</td>
                <td>${statusBadge}</td>
                <td class="table-actions">
                    <a href="?page=catalog/bundle_edit&id=${bundle.id}" class="btn btn-sm btn-icon" title="Bearbeiten">
                        <span class="material-symbols-rounded">edit</span>
                    </a>
                    <button class="btn btn-sm btn-icon" onclick="BundleManager.toggleStatus(${bundle.id})" title="Status ändern">
                        <span class="material-symbols-rounded">${bundle.status === 'active' ? 'visibility_off' : 'visibility'}</span>
                    </button>
                    <button class="btn btn-sm btn-icon" onclick="BundleManager.openDeleteModal(${bundle.id}, '${this.escapeHtml(bundle.name)}')" title="Löschen">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </td>
            </tr>
        `;
            }).join('');
        },

        formatDiscount(bundle) {
            const value = parseFloat(bundle.discount_value) || 0;
            const currency = bundle.base_currency || 'EUR';

            switch (bundle.price_type) {
                case 'percentage':
                    // Always show as positive discount percentage
                    return `<span class="badge badge-success">-${Math.abs(value)}%</span>`;
                case 'fixed_price':
                    // Show as "Festpreis" to indicate it's the final price, not a discount amount
                    return `<span class="badge badge-info" title="Fester Bundle-Preis">${Math.abs(value).toFixed(2)} ${currency}</span>
                            <small style="color:var(--text-muted);display:block;font-size:10px;">Festpreis</small>`;
                case 'fixed_discount':
                    // Always show as positive discount amount
                    return `<span class="badge badge-success">-${Math.abs(value).toFixed(2)} ${currency}</span>`;
                default:
                    return `<span class="badge badge-success">-${Math.abs(value)}%</span>`;
            }
        },

        formatValidity(bundle) {
            if (bundle.bundle_type !== 'limited' || !bundle.valid_from) {
                return '<span style="color:var(--text-muted);">Dauerhaft</span>';
            }

            const from = new Date(bundle.valid_from).toLocaleDateString('de-DE');
            const to = bundle.valid_to ? new Date(bundle.valid_to).toLocaleDateString('de-DE') : '∞';

            // Check if currently active
            const now = new Date();
            const fromDate = new Date(bundle.valid_from);
            const toDate = bundle.valid_to ? new Date(bundle.valid_to) : null;

            let statusIcon = '';
            if (now < fromDate) {
                statusIcon = '<span class="material-symbols-rounded" style="font-size:14px; color:var(--warning);" title="Noch nicht gestartet">schedule</span> ';
            } else if (toDate && now > toDate) {
                statusIcon = '<span class="material-symbols-rounded" style="font-size:14px; color:var(--error);" title="Abgelaufen">event_busy</span> ';
            } else {
                statusIcon = '<span class="material-symbols-rounded" style="font-size:14px; color:var(--success);" title="Aktiv">event_available</span> ';
            }

            return `${statusIcon}<small>${from} - ${to}</small>`;
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.loadBundles(), 300);
        },

        // Delete
        openDeleteModal(bundleId, bundleName) {
            this.deleteTargetId = bundleId;
            document.getElementById('deleteBundleName').textContent = bundleName;
            document.getElementById('deleteModal').classList.add('active');
        },

        closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            this.deleteTargetId = null;
        },

        async confirmDelete() {
            if (!this.deleteTargetId) return;

            try {
                const res = await fetch(`api/bundles.php?action=delete_bundle&shop_id=${this.shopId}&id=${this.deleteTargetId}`, {
                    method: 'POST'
                });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.closeDeleteModal();
                    this.loadBundles();
                } else {
                    this.showToast(data.error || 'Löschen fehlgeschlagen', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler', 'error');
            }
        },

        // Toggle Status
        async toggleStatus(bundleId) {
            try {
                const formData = new FormData();
                formData.append('id', bundleId);

                const res = await fetch(`api/bundles.php?action=toggle_status&shop_id=${this.shopId}`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.loadBundles();
                } else {
                    this.showToast(data.error || 'Status ändern fehlgeschlagen', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler', 'error');
            }
        },

        // Utils
        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type + ' show';
            setTimeout(() => toast.classList.remove('show'), 4000);
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => BundleManager.init());
</script>