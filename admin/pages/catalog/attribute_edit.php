<?php
/** Katalog - Attribut bearbeiten */
$attributeId = (int) ($_GET['id'] ?? 0);
?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/attributes">Attribute</a> <span>›</span> <span
                id="breadcrumbName">Attribut</span></nav>
        <h1 id="pageTitle">Attribut bearbeiten</h1>
        <p class="page-subtitle">Bearbeiten Sie die Attribut-Einstellungen</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/attributes" class="btn">Abbrechen</a>
        <button class="btn btn-danger-ghost" onclick="AttributeEdit.delete()"><span
                class="material-symbols-rounded">delete</span></button>
        <button class="btn btn-primary" onclick="AttributeEdit.save()"><span
                class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<div class="loading-state" id="loadingState">
    <span class="material-symbols-rounded spinning">sync</span>
    <p>Attribut wird geladen...</p>
</div>

<form id="attributeForm" class="attribute-form" style="display:none;">
    <input type="hidden" id="attributeId" name="id" value="<?= $attributeId ?>">

    <div class="dashboard-grid">
        <!-- Main Content -->
        <div class="card">
            <div class="card-header">
                <h3>Grunddaten</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Attributname <span class="required">*</span></label>
                    <input type="text" class="form-input" id="attrName" name="name">
                    <p class="form-error" id="errorName"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Code</label>
                    <input type="text" class="form-input" id="attrCode" name="code" readonly>
                    <p class="form-hint">Der Code kann nach dem Erstellen nicht geändert werden</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Typ</label>
                    <select class="form-select" id="attrType" name="type" disabled>
                        <option value="text">Text (Einzeilig)</option>
                        <option value="textarea">Textbereich (Mehrzeilig)</option>
                        <option value="number">Zahl</option>
                        <option value="select">Dropdown (Einzelauswahl)</option>
                        <option value="multiselect">Mehrfachauswahl</option>
                        <option value="boolean">Ja/Nein</option>
                        <option value="color">Farbe</option>
                        <option value="date">Datum</option>
                        <option value="price">Preis</option>
                    </select>
                    <p class="form-hint" id="typeWarning" style="display:none; color:var(--warning);">Der Typ kann nicht
                        geändert werden, da Produkte dieses Attribut verwenden.</p>
                </div>
            </div>
        </div>

        <!-- Settings & Stats -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3>Einstellungen</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-checkbox"><input type="checkbox" id="isRequired"
                                name="is_required"><span>Pflichtfeld</span></label>
                    </div>
                    <div class="form-group">
                        <label class="form-checkbox"><input type="checkbox" id="isFilterable"
                                name="is_filterable"><span>In Filtern anzeigen</span></label>
                    </div>
                    <div class="form-group">
                        <label class="form-checkbox"><input type="checkbox" id="isSearchable"
                                name="is_searchable"><span>Durchsuchbar</span></label>
                    </div>
                    <div class="form-group">
                        <label class="form-checkbox"><input type="checkbox" id="isVisibleOnFrontend"
                                name="is_visible_on_frontend"><span>Im Shop anzeigen</span></label>
                    </div>
                    <div class="form-group" id="variantCheckboxGroup">
                        <label class="form-checkbox"><input type="checkbox" id="usedForVariants"
                                name="used_for_variants"><span>Für Varianten verwenden</span></label>
                        <p class="form-hint" id="variantHint">Nur für Typen mit Optionen (Dropdown, Mehrfachauswahl,
                            Farbe)</p>
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top:24px;">
                <div class="card-header">
                    <h3>Statistiken</h3>
                </div>
                <div class="card-body">
                    <div class="stat-row"><span>Verwendet in:</span><strong id="statProducts">0 Produkte</strong></div>
                    <div class="stat-row"><span>Optionen:</span><strong id="statOptions">0</strong></div>
                    <div class="stat-row"><span>Erstellt:</span><span id="statCreated">-</span></div>
                    <div class="stat-row"><span>Aktualisiert:</span><span id="statUpdated">-</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Options Section -->
    <div class="card" id="optionsCard" style="margin-top:24px; display:none;">
        <div class="card-header">
            <h3>Optionen</h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="AttributeEdit.addOption()">
                <span class="material-symbols-rounded">add</span> Option hinzufügen
            </button>
        </div>
        <div class="card-body">
            <p class="form-hint" style="margin-bottom:16px;">Definieren Sie die verfügbaren Werte für dieses Attribut.
            </p>
            <div id="optionsList"></div>
            <div id="emptyOptions" class="empty-options">
                <span class="material-symbols-rounded">list</span>
                <p>Noch keine Optionen.</p>
            </div>
        </div>
    </div>
</form>

<!-- Delete Modal -->
<div class="modal" id="deleteModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Attribut löschen</h3>
            <button class="modal-close" onclick="AttributeEdit.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="deleteMessage">Möchten Sie dieses Attribut wirklich löschen?</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="AttributeEdit.closeModal()">Abbrechen</button>
            <button class="btn btn-danger" id="deleteBtn" onclick="AttributeEdit.confirmDelete()">Löschen</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<style>
    .loading-state {
        text-align: center;
        padding: 80px;
        color: var(--text-muted);
    }

    .spinning {
        animation: spin 1s linear infinite;
        font-size: 48px;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .form-error {
        color: var(--error);
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }

    .form-error.show {
        display: block;
    }

    .form-hint {
        color: var(--text-muted);
        font-size: 12px;
        margin-top: 4px;
    }

    .required {
        color: var(--error);
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
    }

    .stat-row:last-child {
        border: none;
    }

    .empty-options {
        text-align: center;
        padding: 40px;
        color: var(--text-muted);
    }

    .empty-options .material-symbols-rounded {
        font-size: 48px;
        opacity: 0.3;
    }

    .option-row {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-bottom: 12px;
        padding: 12px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
    }

    .option-row input {
        flex: 1;
    }

    .option-color {
        width: 60px !important;
        flex: none !important;
        padding: 4px !important;
    }

    .option-remove {
        flex: none;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 8px;
    }

    .option-remove:hover {
        color: var(--error);
    }

    .option-drag {
        flex: none;
        color: var(--text-muted);
        cursor: grab;
        padding: 8px;
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
</style>

<script>
    const AttributeEdit = {
        apiBase: 'api/attributes.php',
        shopId: 1,
        attributeId: <?= $attributeId ?>,
        attribute: null,
        options: [],

        async init() {
            if (!this.attributeId) {
                this.showToast('Keine Attribut-ID', 'error');
                return;
            }
            await this.loadAttribute();
        },

        async loadAttribute() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_attribute&shop_id=${this.shopId}&id=${this.attributeId}`);
                const data = await res.json();

                if (!data.success) {
                    this.showToast('Attribut nicht gefunden', 'error');
                    setTimeout(() => window.location.href = '?page=catalog/attributes', 2000);
                    return;
                }

                this.attribute = data.attribute;
                this.options = data.attribute.options || [];
                this.populateForm();

                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('attributeForm').style.display = 'block';
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        populateForm() {
            const a = this.attribute;

            document.getElementById('pageTitle').textContent = a.name;
            document.getElementById('breadcrumbName').textContent = a.name;
            document.getElementById('attrName').value = a.name;
            document.getElementById('attrCode').value = a.code;
            document.getElementById('attrType').value = a.type;

            document.getElementById('isRequired').checked = a.is_required == 1;
            document.getElementById('isFilterable').checked = a.is_filterable == 1;
            document.getElementById('isSearchable').checked = a.is_searchable == 1;
            document.getElementById('isVisibleOnFrontend').checked = a.is_visible_on_frontend == 1;
            document.getElementById('usedForVariants').checked = a.used_for_variants == 1;

            // Stats
            document.getElementById('statProducts').textContent = `${a.products_count || 0} Produkte`;
            document.getElementById('statOptions').textContent = this.options.length;
            document.getElementById('statCreated').textContent = this.formatDate(a.created_at);
            document.getElementById('statUpdated').textContent = this.formatDate(a.updated_at);

            // Show type warning if products use this attribute
            if (a.products_count > 0) {
                document.getElementById('typeWarning').style.display = 'block';
            }

            // Show options section for applicable types
            const supportsVariants = ['select', 'multiselect', 'color'].includes(a.type);
            if (supportsVariants) {
                document.getElementById('optionsCard').style.display = 'block';
                this.renderOptions();
            }

            // Disable variant checkbox for incompatible types
            const variantCheckbox = document.getElementById('usedForVariants');
            const variantHint = document.getElementById('variantHint');
            if (!supportsVariants) {
                variantCheckbox.disabled = true;
                variantCheckbox.checked = false;
                variantHint.style.color = 'var(--warning)';
                variantHint.textContent = '⚠️ Dieser Typ unterstützt keine Varianten (keine Optionen möglich)';
            }
        },

        addOption() {
            const id = Date.now();
            this.options.push({ id, value: '', label: '', color_hex: '' });
            this.renderOptions();
        },

        removeOption(id) {
            this.options = this.options.filter(o => o.id !== id);
            this.renderOptions();
        },

        renderOptions() {
            const list = document.getElementById('optionsList');
            const empty = document.getElementById('emptyOptions');
            const isColor = this.attribute.type === 'color';

            document.getElementById('statOptions').textContent = this.options.length;

            if (this.options.length === 0) {
                list.innerHTML = '';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            list.innerHTML = this.options.map((opt) => `
            <div class="option-row" data-id="${opt.id}">
                <span class="material-symbols-rounded option-drag">drag_indicator</span>
                <input type="text" class="form-input" placeholder="Wert" value="${opt.value || ''}" onchange="AttributeEdit.updateOption(${opt.id}, 'value', this.value)">
                <input type="text" class="form-input" placeholder="Anzeigename" value="${opt.label || ''}" onchange="AttributeEdit.updateOption(${opt.id}, 'label', this.value)">
                ${isColor ? `<input type="color" class="form-input option-color" value="${opt.color_hex || '#000000'}" onchange="AttributeEdit.updateOption(${opt.id}, 'color_hex', this.value)">` : ''}
                <button type="button" class="option-remove" onclick="AttributeEdit.removeOption(${opt.id})">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
        `).join('');
        },

        updateOption(id, field, value) {
            const opt = this.options.find(o => o.id === id);
            if (opt) opt[field] = value;
        },

        async save() {
            const name = document.getElementById('attrName').value.trim();
            if (!name) {
                document.getElementById('errorName').textContent = 'Attributname ist erforderlich';
                document.getElementById('errorName').classList.add('show');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'save_attribute');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.attributeId);
            formData.append('name', name);
            formData.append('code', document.getElementById('attrCode').value);
            formData.append('type', document.getElementById('attrType').value);
            formData.append('is_required', document.getElementById('isRequired').checked ? 1 : 0);
            formData.append('is_filterable', document.getElementById('isFilterable').checked ? 1 : 0);
            formData.append('is_searchable', document.getElementById('isSearchable').checked ? 1 : 0);
            formData.append('is_visible_on_frontend', document.getElementById('isVisibleOnFrontend').checked ? 1 : 0);
            formData.append('used_for_variants', document.getElementById('usedForVariants').checked ? 1 : 0);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    // Save options
                    if (['select', 'multiselect', 'color'].includes(this.attribute.type)) {
                        await this.saveOptions();
                    }

                    this.showToast('Attribut gespeichert', 'success');
                    document.getElementById('pageTitle').textContent = name;
                    document.getElementById('breadcrumbName').textContent = name;
                } else {
                    this.showToast(data.errors?.join(', ') || data.error, 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        async saveOptions() {
            const optionsToSave = this.options.map(o => ({
                id: typeof o.id === 'number' && o.id > 1000000000000 ? 0 : o.id, // New options have timestamp IDs
                value: o.value,
                label: o.label,
                color_hex: o.color_hex
            }));

            const formData = new FormData();
            formData.append('action', 'save_attribute_options');
            formData.append('shop_id', this.shopId);
            formData.append('attribute_id', this.attributeId);
            formData.append('options', JSON.stringify(optionsToSave));

            await fetch(this.apiBase, { method: 'POST', body: formData });
        },

        delete() {
            const count = this.attribute.products_count || 0;
            if (count > 0) {
                document.getElementById('deleteMessage').innerHTML = `<strong>${this.attribute.name}</strong> wird von ${count} Produkten verwendet und kann nicht gelöscht werden.`;
                document.getElementById('deleteBtn').style.display = 'none';
            } else {
                document.getElementById('deleteMessage').innerHTML = `Möchten Sie <strong>${this.attribute.name}</strong> wirklich löschen?`;
                document.getElementById('deleteBtn').style.display = 'inline-flex';
            }
            document.getElementById('deleteModal').style.display = 'flex';
        },

        closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        },

        async confirmDelete() {
            const formData = new FormData();
            formData.append('action', 'delete_attribute');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.attributeId);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Attribut gelöscht', 'success');
                    setTimeout(() => window.location.href = '?page=catalog/attributes', 1000);
                } else {
                    this.showToast(data.error, 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
            this.closeModal();
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', () => AttributeEdit.init());
</script>