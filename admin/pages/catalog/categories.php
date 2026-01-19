<?php /** Katalog - Kategorien */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kategorien</h1>
        <p class="page-subtitle">Produktkategorien verwalten</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/category_create" class="btn btn-primary"><span
                class="material-symbols-rounded">add</span> Kategorie erstellen</a>
    </div>
</div>

<!-- Tabs -->
<div class="tabs" id="categoryTabs">
    <button class="tab active" data-tab="all" onclick="Categories.switchTab('all')">Alle <span
            class="badge badge-default" id="badgeAll">0</span></button>
    <button class="tab" data-tab="active" onclick="Categories.switchTab('active')">Aktiv <span
            class="badge badge-success" id="badgeActive">0</span></button>
    <button class="tab" data-tab="inactive" onclick="Categories.switchTab('inactive')">Inaktiv <span
            class="badge badge-warning" id="badgeInactive">0</span></button>
</div>

<div class="dashboard-grid">
    <!-- Category Tree -->
    <div class="card">
        <div class="card-header">
            <h3>Kategoriestruktur</h3>
            <div class="filter-search" style="width:250px;">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Suchen..." oninput="Categories.debounceSearch()">
            </div>
        </div>
        <div class="card-body" id="categoryTree">
            <div class="loading-state">
                <span class="material-symbols-rounded spinning">sync</span>
                <p>Kategorien werden geladen...</p>
            </div>
        </div>
    </div>

    <!-- Quick Edit Panel -->
    <div class="card" id="quickEditPanel" style="display:none;">
        <div class="card-header">
            <h3>Schnellbearbeitung</h3>
            <button class="btn btn-sm" onclick="Categories.closeQuickEdit()"><span
                    class="material-symbols-rounded">close</span></button>
        </div>
        <div class="card-body">
            <input type="hidden" id="quickEditId">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" class="form-input" id="quickEditName">
            </div>
            <div class="form-group">
                <label class="form-label">URL-Slug</label>
                <input type="text" class="form-input" id="quickEditSlug">
            </div>
            <div class="form-group">
                <label class="form-label">Übergeordnete Kategorie</label>
                <select class="form-select" id="quickEditParent">
                    <option value="">Keine (Hauptkategorie)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-checkbox"><input type="checkbox" id="quickEditActive"><span>Aktiv</span></label>
            </div>
        </div>
        <div class="card-footer">
            <a href="#" id="quickEditFullLink" class="btn"><span class="material-symbols-rounded">edit</span>
                Vollständig bearbeiten</a>
            <button class="btn btn-primary" onclick="Categories.saveQuickEdit()">Speichern</button>
        </div>
    </div>

    <!-- Empty State Panel -->
    <div class="card" id="emptyPanel">
        <div class="card-body" style="text-align:center;padding:48px;">
            <span class="material-symbols-rounded" style="font-size:64px;color:var(--text-muted);">category</span>
            <h3 style="margin:16px 0 8px;">Kategorie auswählen</h3>
            <p style="color:var(--text-muted);">Wählen Sie eine Kategorie links aus, um sie zu bearbeiten.</p>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal" id="deleteModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Kategorie löschen</h3>
            <button class="modal-close" onclick="Categories.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Möchten Sie die Kategorie "<strong id="deleteModalName"></strong>" wirklich löschen?</p>
            <p style="color:var(--error);margin-top:12px;" id="deleteModalWarning"></p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Categories.closeModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="Categories.confirmDelete()">Löschen</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .category-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--bg-secondary);
    }

    .category-item:hover {
        border-color: var(--accent);
    }

    .category-item.selected {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.1);
    }

    .category-item.inactive {
        opacity: 0.6;
    }

    .category-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-tertiary);
        color: var(--accent);
    }

    .category-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: var(--radius-sm);
    }

    .category-info {
        flex: 1;
    }

    .category-name {
        font-weight: 500;
        margin-bottom: 2px;
    }

    .category-meta {
        font-size: 12px;
        color: var(--text-muted);
    }

    .category-actions {
        display: flex;
        gap: 4px;
    }

    .category-children {
        margin-left: 24px;
        border-left: 2px solid var(--border);
        padding-left: 12px;
    }

    .loading-state {
        text-align: center;
        padding: 48px;
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

    .empty-state {
        text-align: center;
        padding: 48px;
        color: var(--text-muted);
    }
</style>

<script>
    const Categories = {
        apiBase: 'api/categories.php',
        shopId: 1,
        categories: [],
        flatCategories: [],
        selectedId: null,
        currentTab: 'all',
        searchTimeout: null,
        deleteId: null,

        async init() {
            await this.loadStats();
            await this.loadCategories();
        },

        async loadStats() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('badgeAll').textContent = data.stats.total || 0;
                    document.getElementById('badgeActive').textContent = data.stats.active || 0;
                    document.getElementById('badgeInactive').textContent = data.stats.inactive || 0;
                }
            } catch (e) {
                console.error('Error loading stats:', e);
            }
        },

        async loadCategories() {
            try {
                const search = document.getElementById('searchInput').value;
                const status = this.currentTab === 'all' ? '' : this.currentTab;

                const res = await fetch(`${this.apiBase}?action=get_categories&shop_id=${this.shopId}&search=${encodeURIComponent(search)}&status=${status}`);
                const data = await res.json();

                if (data.success) {
                    this.categories = data.categories;
                    this.flatCategories = data.flat;
                    this.renderCategories();
                    this.updateParentSelect();
                }
            } catch (e) {
                console.error('Error loading categories:', e);
                document.getElementById('categoryTree').innerHTML = '<p class="empty-state">Fehler beim Laden</p>';
            }
        },

        renderCategories() {
            const container = document.getElementById('categoryTree');

            if (this.categories.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <span class="material-symbols-rounded" style="font-size:48px;">folder_off</span>
                        <p style="margin-top:12px;">Keine Kategorien gefunden</p>
                        <a href="?page=catalog/category_create" class="btn btn-primary" style="margin-top:16px;">
                            <span class="material-symbols-rounded">add</span> Erste Kategorie erstellen
                        </a>
                    </div>
                `;
                return;
            }

            container.innerHTML = this.renderCategoryTree(this.categories);
        },

        renderCategoryTree(categories, isChild = false) {
            let html = isChild ? '<div class="category-children">' : '';

            categories.forEach(cat => {
                const isSelected = this.selectedId === cat.id;
                const hasImage = cat.image_path;
                const iconHtml = hasImage
                    ? `<img src="${cat.image_path}" alt="${cat.name}">`
                    : `<span class="material-symbols-rounded">folder</span>`;

                html += `
                    <div class="category-item ${isSelected ? 'selected' : ''} ${!cat.is_active ? 'inactive' : ''}"
                         onclick="Categories.selectCategory(${cat.id})" data-id="${cat.id}">
                        <div class="category-icon">${iconHtml}</div>
                        <div class="category-info">
                            <div class="category-name">${this.escapeHtml(cat.name)}</div>
                            <div class="category-meta">
                                ${cat.product_count} Produkte · ${cat.children_count || 0} Unterkategorien
                            </div>
                        </div>
                        <span class="badge badge-${cat.is_active ? 'success' : 'warning'}">${cat.is_active ? 'Aktiv' : 'Inaktiv'}</span>
                        <div class="category-actions">
                            <a href="?page=catalog/category_edit&id=${cat.id}" class="btn btn-sm" onclick="event.stopPropagation()">
                                <span class="material-symbols-rounded">edit</span>
                            </a>
                            <button class="btn btn-sm btn-danger-ghost" onclick="event.stopPropagation(); Categories.deleteCategory(${cat.id}, '${this.escapeHtml(cat.name)}', ${cat.product_count}, ${cat.children_count || 0})">
                                <span class="material-symbols-rounded">delete</span>
                            </button>
                        </div>
                    </div>
                `;

                if (cat.children && cat.children.length > 0) {
                    html += this.renderCategoryTree(cat.children, true);
                }
            });

            if (isChild) html += '</div>';
            return html;
        },

        updateParentSelect() {
            const select = document.getElementById('quickEditParent');
            select.innerHTML = '<option value="">Keine (Hauptkategorie)</option>';

            this.flatCategories.forEach(cat => {
                if (cat.id !== this.selectedId) {
                    const indent = cat.parent_id ? '└─ ' : '';
                    select.innerHTML += `<option value="${cat.id}">${indent}${cat.name}</option>`;
                }
            });
        },

        async selectCategory(id) {
            this.selectedId = id;

            // Update UI
            document.querySelectorAll('.category-item').forEach(el => {
                el.classList.toggle('selected', el.dataset.id == id);
            });

            // Load category data
            try {
                const res = await fetch(`${this.apiBase}?action=get_category&shop_id=${this.shopId}&id=${id}`);
                const data = await res.json();

                if (data.success) {
                    this.showQuickEdit(data.category);
                }
            } catch (e) {
                console.error('Error loading category:', e);
            }
        },

        showQuickEdit(cat) {
            document.getElementById('emptyPanel').style.display = 'none';
            document.getElementById('quickEditPanel').style.display = 'block';

            document.getElementById('quickEditId').value = cat.id;
            document.getElementById('quickEditName').value = cat.name;
            document.getElementById('quickEditSlug').value = cat.slug;
            document.getElementById('quickEditParent').value = cat.parent_id || '';
            document.getElementById('quickEditActive').checked = cat.is_active == 1;
            document.getElementById('quickEditFullLink').href = `?page=catalog/category_edit&id=${cat.id}`;
        },

        closeQuickEdit() {
            document.getElementById('quickEditPanel').style.display = 'none';
            document.getElementById('emptyPanel').style.display = 'block';
            this.selectedId = null;

            document.querySelectorAll('.category-item').forEach(el => {
                el.classList.remove('selected');
            });
        },

        async saveQuickEdit() {
            const formData = new FormData();
            formData.append('action', 'save_category');
            formData.append('shop_id', this.shopId);
            formData.append('id', document.getElementById('quickEditId').value);
            formData.append('name', document.getElementById('quickEditName').value);
            formData.append('slug', document.getElementById('quickEditSlug').value);
            formData.append('parent_id', document.getElementById('quickEditParent').value);
            formData.append('is_active', document.getElementById('quickEditActive').checked ? 1 : 0);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Kategorie gespeichert', 'success');
                    await this.loadStats();
                    await this.loadCategories();
                } else {
                    this.showToast(data.errors?.join(', ') || data.error, 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        switchTab(tab) {
            this.currentTab = tab;
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
            this.loadCategories();
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.loadCategories(), 300);
        },

        deleteCategory(id, name, productCount, childrenCount) {
            this.deleteId = id;
            document.getElementById('deleteModalName').textContent = name;

            let warning = '';
            if (childrenCount > 0) {
                warning = `Hat ${childrenCount} Unterkategorien - bitte zuerst diese löschen.`;
            } else if (productCount > 0) {
                warning = `Enthält ${productCount} Produkte - bitte erst Produkte entfernen.`;
            }
            document.getElementById('deleteModalWarning').textContent = warning;

            document.getElementById('deleteModal').style.display = 'flex';
        },

        async confirmDelete() {
            try {
                const formData = new FormData();
                formData.append('action', 'delete_category');
                formData.append('shop_id', this.shopId);
                formData.append('id', this.deleteId);

                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Kategorie gelöscht', 'success');
                    this.closeQuickEdit();
                    await this.loadStats();
                    await this.loadCategories();
                } else {
                    this.showToast(data.error, 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }

            this.closeModal();
        },

        closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
            this.deleteId = null;
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

    document.addEventListener('DOMContentLoaded', () => Categories.init());
</script>