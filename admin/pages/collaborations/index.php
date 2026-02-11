<?php /** Collaborations - Übersicht */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kollaborationen</h1>
        <p class="page-subtitle">Verwalten Sie Ihre Kollaborationen und Partnerschaften</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=collaborations/create" class="btn btn-primary">
            <span class="material-symbols-rounded">add</span> Neue Kollaboration
        </a>
    </div>
</div>

<div class="tabs" id="collabTabs">
    <button class="tab active" data-tab="alle" onclick="Collabs.switchTab('alle')">Alle <span
            class="badge badge-default" id="badgeAll">0</span></button>
    <button class="tab" data-tab="active" onclick="Collabs.switchTab('active')">Aktiv <span class="badge badge-success"
            id="badgeActive">0</span></button>
    <button class="tab" data-tab="draft" onclick="Collabs.switchTab('draft')">Entwurf <span class="badge badge-warning"
            id="badgeDraft">0</span></button>
    <button class="tab" data-tab="archived" onclick="Collabs.switchTab('archived')">Archiviert <span
            class="badge badge-default" id="badgeArchived">0</span></button>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filters -->
        <div class="filters">
            <div class="filter-search">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Kollaborationen durchsuchen..."
                    oninput="Collabs.debounceSearch()">
            </div>
            <select class="filter-select" id="sortFilter" onchange="Collabs.load()">
                <option value="created_at-DESC">Neueste zuerst</option>
                <option value="created_at-ASC">Älteste zuerst</option>
                <option value="name-ASC">Name A–Z</option>
                <option value="name-DESC">Name Z–A</option>
            </select>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions" style="display:none;">
            <span id="selectedCount">0 ausgewählt</span>
            <button class="btn btn-sm" onclick="Collabs.bulkAction('activate')"><span
                    class="material-symbols-rounded">check_circle</span> Aktivieren</button>
            <button class="btn btn-sm" onclick="Collabs.bulkAction('deactivate')"><span
                    class="material-symbols-rounded">pause_circle</span> Deaktivieren</button>
            <button class="btn btn-sm btn-danger-ghost" onclick="Collabs.bulkAction('delete')"><span
                    class="material-symbols-rounded">delete</span> Löschen</button>
        </div>

        <!-- Table -->
        <table class="table" id="collabsTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" onchange="Collabs.toggleSelectAll()"></th>
                    <th>Kollaboration</th>
                    <th>Status</th>
                    <th>Video</th>
                    <th>Erstellt</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody id="collabsBody">
                <tr>
                    <td colspan="6" class="loading-row">
                        <span class="material-symbols-rounded spinning">sync</span> Lade Kollaborationen...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="confirmModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="confirmModalTitle">Bestätigung</h3>
            <button class="modal-close" onclick="Collabs.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="confirmModalMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="Collabs.closeModal()">Abbrechen</button>
            <button class="btn btn-danger" id="confirmModalBtn" onclick="Collabs.confirmAction()">Löschen</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .collab-image {
        width: 48px;
        height: 48px;
        background: var(--bg-lighter);
        border-radius: var(--radius-sm);
        object-fit: cover;
        flex-shrink: 0;
    }

    .collab-image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
    }

    .collab-image-placeholder .material-symbols-rounded {
        font-size: 22px;
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

    .video-icon {
        color: var(--accent);
        font-size: 20px;
    }
</style>

<script>
    const Collabs = {
        apiBase: 'api/collaborations.php',
        shopId: 1,
        currentTab: 'alle',
        selectedIds: [],
        searchTimeout: null,
        confirmCallback: null,

        async init() {
            await this.loadStats();
            await this.load();
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
            } catch (e) { console.error('Stats error:', e); }
        },

        async load() {
            const tbody = document.getElementById('collabsBody');
            tbody.innerHTML = `<tr><td colspan="6" class="loading-row"><span class="material-symbols-rounded spinning">sync</span> Laden...</td></tr>`;

            const search = document.getElementById('searchInput').value;
            const sortParts = document.getElementById('sortFilter').value.split('-');
            let status = '';
            if (this.currentTab === 'active') status = 'active';
            else if (this.currentTab === 'draft') status = 'draft';
            else if (this.currentTab === 'archived') status = 'archived';

            const params = new URLSearchParams({
                action: 'get_collaborations', shop_id: this.shopId,
                search, status, sort_by: sortParts[0], sort_dir: sortParts[1] || 'DESC',
            });

            try {
                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();
                if (data.success) {
                    this.render(data.collaborations);
                } else {
                    tbody.innerHTML = `<tr><td colspan="6" class="loading-row">Fehler beim Laden</td></tr>`;
                }
            } catch (e) {
                tbody.innerHTML = `<tr><td colspan="6" class="loading-row">Fehler: ${e.message}</td></tr>`;
            }
        },

        render(items) {
            const tbody = document.getElementById('collabsBody');
            if (items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="loading-row">Keine Kollaborationen gefunden</td></tr>`;
                return;
            }
            const statusBadges = {
                active: '<span class="badge badge-success">Aktiv</span>',
                draft: '<span class="badge badge-warning">Entwurf</span>',
                archived: '<span class="badge badge-default">Archiviert</span>',
            };
            tbody.innerHTML = items.map(c => {
                const thumb = c.thumbnail
                    ? `<img src="${c.thumbnail}" alt="${this.esc(c.name)}" class="collab-image">`
                    : `<div class="collab-image collab-image-placeholder"><span class="material-symbols-rounded">image</span></div>`;
                const videoIcon = c.video_url
                    ? '<span class="material-symbols-rounded video-icon" title="Video vorhanden">play_circle</span>'
                    : '<span style="color:var(--text-muted)">—</span>';
                const created = new Date(c.created_at).toLocaleDateString('de-DE');
                return `
            <tr data-id="${c.id}">
                <td><input type="checkbox" class="collab-checkbox" value="${c.id}" onchange="Collabs.updateSelection()"></td>
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        ${thumb}
                        <div>
                            <a href="?page=collaborations/edit&id=${c.id}"><strong>${this.esc(c.name)}</strong></a><br>
                            <small style="color:var(--text-muted);">/${this.esc(c.slug)}</small>
                        </div>
                    </div>
                </td>
                <td>${statusBadges[c.status] || c.status}</td>
                <td>${videoIcon}</td>
                <td style="font-size:12px;color:var(--text-muted);">${created}</td>
                <td class="table-actions">
                    <a href="?page=collaborations/edit&id=${c.id}" class="btn btn-sm"><span class="material-symbols-rounded">edit</span></a>
                    <button class="btn btn-sm btn-danger-ghost" onclick="Collabs.deleteItem(${c.id}, '${this.esc(c.name)}')"><span class="material-symbols-rounded">delete</span></button>
                </td>
            </tr>`;
            }).join('');
        },

        switchTab(tab) {
            this.currentTab = tab;
            document.querySelectorAll('#collabTabs .tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`#collabTabs .tab[data-tab="${tab}"]`).classList.add('active');
            this.load();
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.load(), 300);
        },

        toggleSelectAll() {
            const checked = document.getElementById('selectAll').checked;
            document.querySelectorAll('.collab-checkbox').forEach(cb => cb.checked = checked);
            this.updateSelection();
        },

        updateSelection() {
            const cbs = document.querySelectorAll('.collab-checkbox:checked');
            this.selectedIds = Array.from(cbs).map(cb => cb.value);
            const bulk = document.getElementById('bulkActions');
            if (this.selectedIds.length > 0) {
                bulk.style.display = 'flex';
                document.getElementById('selectedCount').textContent = `${this.selectedIds.length} ausgewählt`;
            } else {
                bulk.style.display = 'none';
            }
        },

        async bulkAction(action) {
            if (this.selectedIds.length === 0) return;
            if (action === 'delete') {
                this.showModal('Löschen', `Möchten Sie ${this.selectedIds.length} Kollaboration(en) wirklich löschen?`, async () => {
                    await this.executeBulk('delete');
                });
                return;
            }
            await this.executeBulk(action);
        },

        async executeBulk(action) {
            const fd = new FormData();
            fd.append('action', 'bulk_action');
            fd.append('shop_id', this.shopId);
            fd.append('bulk_action', action);
            fd.append('ids', JSON.stringify(this.selectedIds));
            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    this.showToast(data.message, 'success');
                    this.selectedIds = [];
                    document.getElementById('selectAll').checked = false;
                    document.getElementById('bulkActions').style.display = 'none';
                    await this.loadStats();
                    await this.load();
                } else { this.showToast('Fehler: ' + (data.error || ''), 'error'); }
            } catch (e) { this.showToast('Fehler: ' + e.message, 'error'); }
        },

        deleteItem(id, name) {
            this.showModal('Löschen', `Möchten Sie "${name}" wirklich löschen?`, async () => {
                const fd = new FormData();
                fd.append('action', 'delete_collaboration');
                fd.append('shop_id', this.shopId);
                fd.append('id', id);
                try {
                    const res = await fetch(this.apiBase, { method: 'POST', body: fd });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message, 'success');
                        await this.loadStats();
                        await this.load();
                    } else { this.showToast('Fehler: ' + (data.error || ''), 'error'); }
                } catch (e) { this.showToast('Fehler: ' + e.message, 'error'); }
            });
        },

        showModal(title, msg, cb) {
            document.getElementById('confirmModalTitle').textContent = title;
            document.getElementById('confirmModalMessage').textContent = msg;
            this.confirmCallback = cb;
            document.getElementById('confirmModal').style.display = 'flex';
        },
        closeModal() {
            document.getElementById('confirmModal').style.display = 'none';
            this.confirmCallback = null;
        },
        async confirmAction() {
            if (this.confirmCallback) await this.confirmCallback();
            this.closeModal();
        },

        showToast(msg, type = 'success') {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = `toast ${type} show`;
            setTimeout(() => t.className = 'toast', 3000);
        },

        esc(text) {
            const d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        }
    };

    document.addEventListener('DOMContentLoaded', () => Collabs.init());
</script>