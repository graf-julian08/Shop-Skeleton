<?php /** Katalog - Neues Attribut erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/attributes">Attribute</a> <span>›</span> <span>Neues
                Attribut</span></nav>
        <h1>Neues Attribut erstellen</h1>
        <p class="page-subtitle">Definieren Sie ein neues Produktattribut</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/attributes" class="btn">Abbrechen</a>
        <button class="btn btn-primary" onclick="AttributeForm.save()"><span
                class="material-symbols-rounded">save</span> Speichern</button>
    </div>
</div>

<form id="attributeForm" class="attribute-form">
    <div class="dashboard-grid">
        <!-- Main Content -->
        <div class="card">
            <div class="card-header">
                <h3>Grunddaten</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Attributname <span class="required">*</span></label>
                    <input type="text" class="form-input" id="attrName" name="name"
                        placeholder="z.B. Farbe, Größe, Material">
                    <p class="form-error" id="errorName"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Code</label>
                    <input type="text" class="form-input" id="attrCode" name="code" placeholder="automatisch generiert">
                    <p class="form-hint">Technischer Name (nur Buchstaben, Zahlen, Unterstriche)</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Typ <span class="required">*</span></label>
                    <select class="form-select" id="attrType" name="type">
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
                    <p class="form-hint">Bestimmt, wie Kunden/Mitarbeiter Werte eingeben</p>
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3>Einstellungen</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-checkbox"><input type="checkbox"
                                name="is_required"><span>Pflichtfeld</span></label>
                        <p class="form-hint">Muss beim Erstellen eines Produkts ausgefüllt werden</p>
                    </div>
                    <div class="form-group">
                        <label class="form-checkbox"><input type="checkbox" name="is_filterable" checked><span>In
                                Filtern anzeigen</span></label>
                        <p class="form-hint">Kunden können nach diesem Attribut filtern</p>
                    </div>
                    <div class="form-group">
                        <label class="form-checkbox"><input type="checkbox"
                                name="is_searchable"><span>Durchsuchbar</span></label>
                        <p class="form-hint">In der Produktsuche berücksichtigen</p>
                    </div>
                    <div class="form-group">
                        <label class="form-checkbox"><input type="checkbox" name="is_visible_on_frontend"
                                checked><span>Im Shop anzeigen</span></label>
                        <p class="form-hint">Attributwert auf der Produktseite anzeigen</p>
                    </div>
                    <div class="form-group" id="variantCheckboxGroup">
                        <label class="form-checkbox"><input type="checkbox" name="used_for_variants"
                                id="usedForVariants"><span>Für Varianten
                                verwenden</span></label>
                        <p class="form-hint" id="variantHint">Ermöglicht Produktvarianten basierend auf diesem Attribut
                            (z.B. Farbe,
                            Größe)</p>
                        <p class="form-hint" id="variantWarning" style="display:none; color:var(--warning);">
                            ⚠️ Nur Typen mit Optionen (Dropdown, Mehrfachauswahl, Farbe) können für Varianten verwendet
                            werden.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Options Section (shown for select/multiselect/color types) -->
    <div class="card" id="optionsCard" style="margin-top:24px; display:none;">
        <div class="card-header">
            <h3>Optionen</h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="AttributeForm.addOption()">
                <span class="material-symbols-rounded">add</span> Option hinzufügen
            </button>
        </div>
        <div class="card-body">
            <p class="form-hint" style="margin-bottom:16px;">Definieren Sie die verfügbaren Werte für dieses Attribut.
            </p>
            <div id="optionsList"></div>
            <div id="emptyOptions" class="empty-options">
                <span class="material-symbols-rounded">list</span>
                <p>Noch keine Optionen. Klicken Sie oben auf "Option hinzufügen".</p>
            </div>
        </div>
    </div>
</form>

<div class="toast" id="toast"></div>

<style>
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
</style>

<script>
    const AttributeForm = {
        apiBase: 'api/attributes.php',
        shopId: 1,
        options: [],

        init() {
            this.setupEventListeners();
        },

        setupEventListeners() {
            // Type change - show/hide options
            document.getElementById('attrType').addEventListener('change', () => this.updateOptionsVisibility());

            // Name -> Code auto-generate
            document.getElementById('attrName').addEventListener('input', (e) => {
                const code = e.target.value.toLowerCase()
                    .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue').replace(/ß/g, 'ss')
                    .replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
                document.getElementById('attrCode').placeholder = code;
                document.getElementById('errorName').classList.remove('show');
            });
        },

        updateOptionsVisibility() {
            const type = document.getElementById('attrType').value;
            const optionsCard = document.getElementById('optionsCard');
            const variantCheckbox = document.getElementById('usedForVariants');
            const variantHint = document.getElementById('variantHint');
            const variantWarning = document.getElementById('variantWarning');
            
            // Types that support options (and thus variants)
            const supportsOptions = ['select', 'multiselect', 'color'].includes(type);
            
            // Show/hide options card
            optionsCard.style.display = supportsOptions ? 'block' : 'none';
            
            // Enable/disable variant checkbox
            variantCheckbox.disabled = !supportsOptions;
            if (!supportsOptions) {
                variantCheckbox.checked = false;
                variantHint.style.display = 'none';
                variantWarning.style.display = 'block';
            } else {
                variantHint.style.display = 'block';
                variantWarning.style.display = 'none';
            }
        },

        addOption() {
            const type = document.getElementById('attrType').value;
            const id = Date.now();
            const isColor = type === 'color';

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
            const type = document.getElementById('attrType').value;
            const isColor = type === 'color';

            if (this.options.length === 0) {
                list.innerHTML = '';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            list.innerHTML = this.options.map((opt, idx) => `
            <div class="option-row" data-id="${opt.id}">
                <span class="material-symbols-rounded option-drag">drag_indicator</span>
                <input type="text" class="form-input" placeholder="Wert (z.B. red, xs)" value="${opt.value}" onchange="AttributeForm.updateOption(${opt.id}, 'value', this.value)">
                <input type="text" class="form-input" placeholder="Anzeigename (z.B. Rot, XS)" value="${opt.label}" onchange="AttributeForm.updateOption(${opt.id}, 'label', this.value)">
                ${isColor ? `<input type="color" class="form-input option-color" value="${opt.color_hex || '#000000'}" onchange="AttributeForm.updateOption(${opt.id}, 'color_hex', this.value)">` : ''}
                <button type="button" class="option-remove" onclick="AttributeForm.removeOption(${opt.id})">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
        `).join('');
        },

        updateOption(id, field, value) {
            const opt = this.options.find(o => o.id === id);
            if (opt) opt[field] = value;
        },

        validate() {
            let valid = true;
            const name = document.getElementById('attrName').value.trim();

            if (!name) {
                document.getElementById('errorName').textContent = 'Attributname ist erforderlich';
                document.getElementById('errorName').classList.add('show');
                valid = false;
            }

            const type = document.getElementById('attrType').value;
            if (['select', 'multiselect', 'color'].includes(type) && this.options.length === 0) {
                this.showToast('Mindestens eine Option ist erforderlich', 'error');
                valid = false;
            }

            return valid;
        },

        async save() {
            if (!this.validate()) return;

            const formData = new FormData(document.getElementById('attributeForm'));
            formData.append('action', 'save_attribute');
            formData.append('shop_id', this.shopId);
            formData.set('is_required', formData.get('is_required') ? 1 : 0);
            formData.set('is_filterable', formData.get('is_filterable') ? 1 : 0);
            formData.set('is_searchable', formData.get('is_searchable') ? 1 : 0);
            formData.set('is_visible_on_frontend', formData.get('is_visible_on_frontend') ? 1 : 0);
            formData.set('used_for_variants', formData.get('used_for_variants') ? 1 : 0);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    // Save options if applicable
                    const type = document.getElementById('attrType').value;
                    if (['select', 'multiselect', 'color'].includes(type) && this.options.length > 0) {
                        await this.saveOptions(data.id);
                    }

                    this.showToast('Attribut erstellt', 'success');
                    setTimeout(() => window.location.href = `?page=catalog/attribute_edit&id=${data.id}`, 1000);
                } else {
                    this.showToast(data.errors?.join(', ') || data.error, 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        async saveOptions(attributeId) {
            const optionsToSave = this.options.map(o => ({
                value: o.value,
                label: o.label,
                color_hex: o.color_hex
            }));

            const formData = new FormData();
            formData.append('action', 'save_attribute_options');
            formData.append('shop_id', this.shopId);
            formData.append('attribute_id', attributeId);
            formData.append('options', JSON.stringify(optionsToSave));

            await fetch(this.apiBase, { method: 'POST', body: formData });
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', () => AttributeForm.init());
</script>