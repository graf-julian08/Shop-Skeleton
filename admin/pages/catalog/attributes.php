<?php /** Katalog - Attribute */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Attribute</h1>
        <p class="page-subtitle">Produktattribute und Varianten verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/attribute_create" class="btn btn-primary"><span
                class="material-symbols-rounded">add</span> Attribut erstellen</a>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid" id="statsGrid">
    <div class="stat-card">
        <div class="stat-value" id="statTotal">0</div>
        <div class="stat-label">Attribute</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="statGroups">0</div>
        <div class="stat-label">Gruppen</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="statVariants">0</div>
        <div class="stat-label">Für Varianten</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="statFilterable">0</div>
        <div class="stat-label">Filterbar</div>
    </div>
</div>



<!-- Tab: Attribute -->
<div data-tab-content="attribute">
    <div class="card">
        <div class="card-header">
            <h3>Alle Attribute</h3>
        </div>
        <div class="card-body">
            <div class="filters">
                <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text"
                        id="searchInput" placeholder="Attribut suchen..."></div>
                <select class="filter-select" id="typeFilter">
                    <option value="">Alle Typen</option>
                </select>
            </div>
            <div id="attributesLoading" class="loading-state"><span
                    class="material-symbols-rounded spinning">sync</span>
                <p>Lade Attribute...</p>
            </div>
            <table class="table" id="attributesTable" style="display:none;">
                <thead>
                    <tr>
                        <th>Attribut</th>
                        <th>Code</th>
                        <th>Typ</th>
                        <th>Optionen</th>
                        <th>Produkte</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody id="attributesBody"></tbody>
            </table>
            <div id="emptyState" class="empty-state" style="display:none;">
                <span class="material-symbols-rounded">category</span>
                <p>Keine Attribute vorhanden</p>
                <a href="?page=catalog/attribute_create" class="btn btn-primary">Erstes Attribut erstellen</a>
            </div>
        </div>
    </div>
</div>



<!-- Delete Modal -->
<div class="modal" id="deleteModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Löschen bestätigen</h3>
            <button class="modal-close" onclick="Attributes.closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="deleteMessage">Möchten Sie dieses Element wirklich löschen?</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Attributes.closeDeleteModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="Attributes.confirmDelete()">Löschen</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 20px;
        text-align: center;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--accent);
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 13px;
        margin-top: 4px;
    }

    .loading-state {
        text-align: center;
        padding: 60px;
        color: var(--text-muted);
    }

    .spinning {
        animation: spin 1s linear infinite;
        font-size: 36px;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .empty-state {
        text-align: center;
        padding: 60px;
        color: var(--text-muted);
    }

    .empty-state .material-symbols-rounded {
        font-size: 64px;
        opacity: 0.3;
    }

    .filters {
        display: flex;
        gap: 16px;
        margin-bottom: 20px;
    }

    .filter-search {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
        padding: 8px 16px;
        flex: 1;
        max-width: 300px;
    }

    .filter-search input {
        background: none;
        border: none;
        outline: none;
        color: var(--text-primary);
        width: 100%;
    }

    .filter-select {
        background: var(--bg-tertiary);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 8px 16px;
        color: var(--text-primary);
    }

    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-color {
        background: linear-gradient(45deg, #ff6b6b, #feca57, #48dbfb, #1dd1a1);
        color: white;
    }

    .badge-select {
        background: var(--accent);
        color: white;
    }

    .badge-text {
        background: var(--bg-tertiary);
        color: var(--text-secondary);
    }

    .badge-multiselect {
        background: #a55eea;
        color: white;
    }

    .badge-boolean {
        background: #26de81;
        color: white;
    }

    .badge-number {
        background: #fd9644;
        color: white;
    }

    .badge-date {
        background: #45aaf2;
        color: white;
    }

    .badge-price {
        background: #f7b731;
        color: #333;
    }

    .attr-tags {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .attr-tag {
        font-size: 10px;
        padding: 2px 6px;
        background: var(--bg-tertiary);
        border-radius: 4px;
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
        max-width: 400px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
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

    .required {
        color: var(--error);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<script>
    const Attributes = {
        apiBase: 'api/attributes.php',
        shopId: 1,
        attributes: [],
        types: {},
        deleteTarget: null,

        async init() {
            await this.loadStats();
            await this.loadAttributes();
            this.setupEventListeners();
        },

        async loadStats() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('statTotal').textContent = data.stats.total_attributes;
                    document.getElementById('statGroups').textContent = data.stats.total_groups;
                    document.getElementById('statVariants').textContent = data.stats.variant_attributes;
                    document.getElementById('statFilterable').textContent = data.stats.filterable_attributes;
                }
            } catch (e) { console.error(e); }
        },

        async loadAttributes() {
            const search = document.getElementById('searchInput')?.value || '';
            const type = document.getElementById('typeFilter')?.value || '';

            try {
                const res = await fetch(`${this.apiBase}?action=get_attributes&shop_id=${this.shopId}&search=${encodeURIComponent(search)}&type=${type}`);
                const data = await res.json();

                document.getElementById('attributesLoading').style.display = 'none';

                if (data.success) {
                    this.attributes = data.attributes;
                    this.types = data.types;
                    this.populateTypeFilter(data.types);
                    this.renderAttributes();
                }
            } catch (e) {
                document.getElementById('attributesLoading').style.display = 'none';
                this.showToast('Fehler beim Laden', 'error');
            }
        },

        populateTypeFilter(types) {
            const select = document.getElementById('typeFilter');
            const currentValue = select.value;
            select.innerHTML = '<option value="">Alle Typen</option>';
            for (const [key, label] of Object.entries(types)) {
                select.innerHTML += `<option value="${key}">${label}</option>`;
            }
            select.value = currentValue;
        },

        renderAttributes() {
            const tbody = document.getElementById('attributesBody');
            const table = document.getElementById('attributesTable');
            const empty = document.getElementById('emptyState');

            if (this.attributes.length === 0) {
                table.style.display = 'none';
                empty.style.display = 'block';
                return;
            }

            table.style.display = 'table';
            empty.style.display = 'none';

            const badgeClasses = {
                color: 'badge-color', select: 'badge-select', multiselect: 'badge-multiselect',
                boolean: 'badge-boolean', text: 'badge-text', textarea: 'badge-text',
                number: 'badge-number', date: 'badge-date', price: 'badge-price'
            };

            tbody.innerHTML = this.attributes.map(attr => {
                const badgeClass = badgeClasses[attr.type] || 'badge-text';
                const tags = [];
                if (attr.used_for_variants == 1) tags.push('Varianten');
                if (attr.is_filterable == 1) tags.push('Filter');
                if (attr.is_searchable == 1) tags.push('Suche');

                return `<tr>
                <td>
                    <a href="?page=catalog/attribute_edit&id=${attr.id}"><strong>${attr.name}</strong></a>
                    ${tags.length ? `<div class="attr-tags">${tags.map(t => `<span class="attr-tag">${t}</span>`).join('')}</div>` : ''}
                </td>
                <td><code>${attr.code}</code></td>
                <td><span class="badge ${badgeClass}">${attr.type_label}</span></td>
                <td>${attr.options_count || '-'}</td>
                <td>${attr.products_count || 0}</td>
                <td class="table-actions">
                    <a href="?page=catalog/attribute_edit&id=${attr.id}" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a>
                    <button class="btn btn-sm" onclick="Attributes.deleteAttribute(${attr.id}, '${attr.name}', ${attr.products_count || 0})"><span class="material-symbols-rounded">delete</span></button>
                </td>
            </tr>`;
            }).join('');
        },



        setupEventListeners() {
            // Search
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => this.loadAttributes(), 300);
            });

            // Type filter
            document.getElementById('typeFilter').addEventListener('change', () => this.loadAttributes());
        },



        deleteAttribute(id, name, productCount) {
            if (productCount > 0) {
                document.getElementById('deleteMessage').innerHTML = `<strong>${name}</strong> wird von ${productCount} Produkten verwendet und kann nicht gelöscht werden.`;
                document.querySelector('#deleteModal .btn-danger').style.display = 'none';
            } else {
                document.getElementById('deleteMessage').innerHTML = `Möchten Sie das Attribut <strong>${name}</strong> wirklich löschen?`;
                document.querySelector('#deleteModal .btn-danger').style.display = 'inline-flex';
            }
            this.deleteTarget = { type: 'attribute', id };
            document.getElementById('deleteModal').style.display = 'flex';
        },



        closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            this.deleteTarget = null;
        },

        async confirmDelete() {
            if (!this.deleteTarget) return;

            const formData = new FormData();
            formData.append('shop_id', this.shopId);
            formData.append('id', this.deleteTarget.id);
            formData.append('action', 'delete_attribute');

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast(data.message, 'success');
                    await this.loadAttributes();
                    await this.loadStats();
                } else {
                    this.showToast(data.error, 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }

            this.closeDeleteModal();
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', () => Attributes.init());
</script>