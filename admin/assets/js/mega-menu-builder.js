/**
 * Mega Menu Builder
 * Grid-based editor for mega menu content
 */

class MegaMenuBuilder {
    constructor(navItemId, container) {
        this.navItemId = navItemId;
        this.container = typeof container === 'string' ? document.querySelector(container) : container;
        this.data = { columns: [] };
        this.selectedBlock = null;

        this.init();
    }

    async init() {
        await this.load();
        this.render();
        this.bindEvents();
    }

    // ========== DATA OPERATIONS ==========

    async load() {
        try {
            const response = await fetch(`/admin/api/mega_menu.php?action=get&nav_item_id=${this.navItemId}`);
            const result = await response.json();
            if (result.success) {
                this.data = result.data;
            }
        } catch (error) {
            console.error('Error loading mega menu:', error);
        }
    }

    async applyLayout(layout) {
        try {
            const formData = new FormData();
            formData.append('nav_item_id', this.navItemId);
            formData.append('layout', layout);

            const response = await fetch('/admin/api/mega_menu.php?action=apply_layout', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                this.data = result.data;
                this.render();
            }
        } catch (error) {
            console.error('Error applying layout:', error);
        }
    }

    async addColumn(width = 25) {
        try {
            const formData = new FormData();
            formData.append('nav_item_id', this.navItemId);
            formData.append('width', width);

            const response = await fetch('/admin/api/mega_menu.php?action=add_column', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                this.data = result.data;
                this.render();
            }
        } catch (error) {
            console.error('Error adding column:', error);
        }
    }

    async deleteColumn(columnId) {
        const confirmed = await adminModal.confirm('Möchten Sie diese Spalte wirklich löschen? Alle Blöcke darin werden ebenfalls gelöscht.', {
            title: 'Spalte löschen',
            icon: 'view_column',
            type: 'warning',
            confirmText: 'Ja, löschen',
            danger: true
        });
        if (!confirmed) return;

        try {
            const formData = new FormData();
            formData.append('column_id', columnId);

            await fetch('/admin/api/mega_menu.php?action=delete_column', {
                method: 'POST',
                body: formData
            });
            await this.load();
            this.render();
        } catch (error) {
            console.error('Error deleting column:', error);
        }
    }

    async updateColumnWidth(columnId, width) {
        try {
            const formData = new FormData();
            formData.append('column_id', columnId);
            formData.append('width', width);

            await fetch('/admin/api/mega_menu.php?action=update_column_width', {
                method: 'POST',
                body: formData
            });

            // Update local data
            const col = this.data.columns.find(c => c.id == columnId);
            if (col) col.width = width;
            this.renderPreview();
        } catch (error) {
            console.error('Error updating column width:', error);
        }
    }

    async addBlock(columnId, blockType, config = {}) {
        try {
            const formData = new FormData();
            formData.append('column_id', columnId);
            formData.append('block_type', blockType);
            formData.append('config', JSON.stringify(config));

            const response = await fetch('/admin/api/mega_menu.php?action=add_block', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                await this.load();
                this.render();
                return result.block_id;
            }
        } catch (error) {
            console.error('Error adding block:', error);
        }
        return null;
    }

    async updateBlock(blockId, config) {
        try {
            const formData = new FormData();
            formData.append('block_id', blockId);
            formData.append('config', JSON.stringify(config));

            await fetch('/admin/api/mega_menu.php?action=update_block', {
                method: 'POST',
                body: formData
            });
            await this.load();
            this.render();
        } catch (error) {
            console.error('Error updating block:', error);
        }
    }

    async deleteBlock(blockId) {
        const confirmed = await adminModal.confirm('Möchten Sie diesen Block wirklich löschen?', {
            title: 'Block löschen',
            icon: 'widgets',
            type: 'warning',
            confirmText: 'Ja, löschen',
            danger: true
        });
        if (!confirmed) return;

        try {
            const formData = new FormData();
            formData.append('block_id', blockId);

            await fetch('/admin/api/mega_menu.php?action=delete_block', {
                method: 'POST',
                body: formData
            });
            await this.load();
            this.render();
        } catch (error) {
            console.error('Error deleting block:', error);
        }
    }

    async reorderBlocks(columnId, blockIds) {
        try {
            const formData = new FormData();
            formData.append('column_id', columnId);
            formData.append('block_ids', JSON.stringify(blockIds));

            await fetch('/admin/api/mega_menu.php?action=reorder_blocks', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error reordering blocks:', error);
        }
    }

    // ========== RENDERING ==========

    render() {
        this.container.innerHTML = `
            <div class="mega-builder">
                <div class="mega-builder-header">
                    <h3>Mega-Menu Editor</h3>
                    <div class="mega-builder-layouts">
                        <span>Layout:</span>
                        <button type="button" class="layout-btn" data-layout="2-col">2 Spalten</button>
                        <button type="button" class="layout-btn" data-layout="3-col">3 Spalten</button>
                        <button type="button" class="layout-btn" data-layout="4-col">4 Spalten</button>
                        <button type="button" class="layout-btn" data-layout="1-2">1:2</button>
                        <button type="button" class="layout-btn" data-layout="2-1">2:1</button>
                    </div>
                </div>
                <div class="mega-builder-columns" id="mega-columns">
                    ${this.renderColumns()}
                </div>
                <div class="mega-builder-add-column">
                    <button type="button" class="btn" id="add-column-btn">
                        <span class="material-symbols-rounded">add</span>
                        Spalte hinzufügen
                    </button>
                </div>
                <div class="mega-builder-preview">
                    <h4>Vorschau</h4>
                    <div class="mega-preview" id="mega-preview">
                        ${this.renderPreviewContent()}
                    </div>
                </div>
            </div>
        `;
    }

    renderColumns() {
        if (this.data.columns.length === 0) {
            return `
                <div class="mega-empty">
                    <span class="material-symbols-rounded">view_column</span>
                    <p>Noch keine Spalten vorhanden</p>
                    <p>Wählen Sie ein Layout oder fügen Sie eine Spalte hinzu.</p>
                </div>
            `;
        }

        return this.data.columns.map(column => `
            <div class="mega-column" data-column-id="${column.id}" style="flex: 0 0 ${column.width}%">
                <div class="mega-column-header">
                    <span class="drag-handle material-symbols-rounded">drag_indicator</span>
                    <span class="column-title">Spalte (${column.width}%)</span>
                    <input type="range" class="column-width-slider" value="${column.width}" min="10" max="100" step="5"
                        data-column-id="${column.id}">
                    <button type="button" class="icon-btn delete-column-btn" data-column-id="${column.id}">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
                <div class="mega-column-blocks" data-column-id="${column.id}">
                    ${this.renderBlocks(column.blocks)}
                </div>
                <div class="mega-add-block">
                    <button type="button" class="btn-add-block" data-column-id="${column.id}">
                        <span class="material-symbols-rounded">add</span>
                        Block hinzufügen
                    </button>
                </div>
            </div>
        `).join('');
    }

    renderBlocks(blocks) {
        if (!blocks || blocks.length === 0) {
            return '<div class="blocks-empty">Blöcke hierher ziehen</div>';
        }

        return blocks.map(block => this.renderBlock(block)).join('');
    }

    renderBlock(block) {
        const icons = {
            links: 'list',
            image: 'image',
            promo: 'campaign',
            divider: 'horizontal_rule',
            html: 'code'
        };

        const labels = {
            links: 'Links',
            image: 'Bild',
            promo: 'Promo',
            divider: 'Trenner',
            html: 'HTML'
        };

        let content = '';
        const config = block.config || {};

        switch (block.type) {
            case 'links':
                content = `
                    <div class="block-title">${config.title || 'Ohne Titel'}</div>
                    <div class="block-links">
                        ${(config.links || []).slice(0, 3).map(l => `<span>• ${l.label}</span>`).join('')}
                        ${(config.links || []).length > 3 ? `<span>+${config.links.length - 3} weitere</span>` : ''}
                    </div>
                `;
                break;
            case 'image':
                content = config.media_id
                    ? `<div class="block-image-preview"><img src="/uploads/media/1/thumbnails/${config.stored_filename || ''}" alt=""></div>`
                    : '<div class="block-image-placeholder">Kein Bild</div>';
                break;
            case 'promo':
                content = `
                    <div class="block-promo-title">${config.title || 'Promo'}</div>
                    <div class="block-promo-text">${config.text || ''}</div>
                `;
                break;
            case 'divider':
                content = '<hr class="block-divider">';
                break;
            case 'html':
                content = '<div class="block-html-preview">HTML Block</div>';
                break;
        }

        return `
            <div class="mega-block" data-block-id="${block.id}" data-block-type="${block.type}">
                <div class="mega-block-header">
                    <span class="drag-handle material-symbols-rounded">drag_indicator</span>
                    <span class="material-symbols-rounded block-icon">${icons[block.type]}</span>
                    <span class="block-type-label">${labels[block.type]}</span>
                    <div class="block-actions">
                        <button type="button" class="icon-btn edit-block-btn" data-block-id="${block.id}">
                            <span class="material-symbols-rounded">edit</span>
                        </button>
                        <button type="button" class="icon-btn delete-block-btn" data-block-id="${block.id}">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    </div>
                </div>
                <div class="mega-block-content">
                    ${content}
                </div>
            </div>
        `;
    }

    renderPreview() {
        const preview = document.getElementById('mega-preview');
        if (preview) {
            preview.innerHTML = this.renderPreviewContent();
        }
    }

    renderPreviewContent() {
        if (this.data.columns.length === 0) {
            return '<div class="preview-empty">Keine Vorschau</div>';
        }

        return `
            <div class="preview-grid" style="display: flex; gap: 20px;">
                ${this.data.columns.map(col => `
                    <div class="preview-column" style="flex: 0 0 ${col.width}%; min-width: 0;">
                        ${(col.blocks || []).map(block => this.renderPreviewBlock(block)).join('')}
                    </div>
                `).join('')}
            </div>
        `;
    }

    renderPreviewBlock(block) {
        const config = block.config || {};

        switch (block.type) {
            case 'links':
                return `
                    <div class="preview-links">
                        <strong>${config.title || ''}</strong>
                        <ul>
                            ${(config.links || []).map(l => `<li><a href="#">${l.label}</a></li>`).join('')}
                        </ul>
                    </div>
                `;
            case 'image':
                return config.media_id
                    ? `<div class="preview-image"><img src="/uploads/media/1/medium/${config.stored_filename || ''}" alt="${config.alt_text || ''}"></div>`
                    : '';
            case 'promo':
                return `
                    <div class="preview-promo">
                        <strong>${config.title || ''}</strong>
                        <p>${config.text || ''}</p>
                        ${config.cta_text ? `<button>${config.cta_text}</button>` : ''}
                    </div>
                `;
            case 'divider':
                return '<hr>';
            case 'html':
                return `<div class="preview-html">${config.html || ''}</div>`;
            default:
                return '';
        }
    }

    // ========== EVENT BINDING ==========

    bindEvents() {
        // Layout buttons
        this.container.querySelectorAll('.layout-btn').forEach(btn => {
            btn.addEventListener('click', () => this.applyLayout(btn.dataset.layout));
        });

        // Add column button
        const addColBtn = this.container.querySelector('#add-column-btn');
        if (addColBtn) {
            addColBtn.addEventListener('click', () => this.addColumn(25));
        }

        // Delete column buttons
        this.container.querySelectorAll('.delete-column-btn').forEach(btn => {
            btn.addEventListener('click', () => this.deleteColumn(btn.dataset.columnId));
        });

        // Column width sliders
        this.container.querySelectorAll('.column-width-slider').forEach(slider => {
            slider.addEventListener('input', (e) => {
                const column = e.target.closest('.mega-column');
                column.style.flex = `0 0 ${e.target.value}%`;
                column.querySelector('.column-title').textContent = `Spalte (${e.target.value}%)`;
            });
            slider.addEventListener('change', (e) => {
                this.updateColumnWidth(e.target.dataset.columnId, parseInt(e.target.value));
            });
        });

        // Add block buttons
        this.container.querySelectorAll('.btn-add-block').forEach(btn => {
            btn.addEventListener('click', () => this.showAddBlockModal(btn.dataset.columnId));
        });

        // Edit block buttons
        this.container.querySelectorAll('.edit-block-btn').forEach(btn => {
            btn.addEventListener('click', () => this.showEditBlockModal(btn.dataset.blockId));
        });

        // Delete block buttons
        this.container.querySelectorAll('.delete-block-btn').forEach(btn => {
            btn.addEventListener('click', () => this.deleteBlock(btn.dataset.blockId));
        });

        // Setup drag and drop for blocks
        this.setupDragDrop();
    }

    setupDragDrop() {
        const blocks = this.container.querySelectorAll('.mega-block');
        const columns = this.container.querySelectorAll('.mega-column-blocks');

        blocks.forEach(block => {
            block.draggable = true;
            block.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', block.dataset.blockId);
                block.classList.add('dragging');
            });
            block.addEventListener('dragend', () => {
                block.classList.remove('dragging');
            });
        });

        columns.forEach(column => {
            column.addEventListener('dragover', (e) => {
                e.preventDefault();
                column.classList.add('drag-over');
            });
            column.addEventListener('dragleave', () => {
                column.classList.remove('drag-over');
            });
            column.addEventListener('drop', async (e) => {
                e.preventDefault();
                column.classList.remove('drag-over');

                const blockId = e.dataTransfer.getData('text/plain');
                const columnId = column.dataset.columnId;

                // Get new order
                const blocksInColumn = Array.from(column.querySelectorAll('.mega-block'));
                const blockIds = blocksInColumn.map(b => parseInt(b.dataset.blockId));
                if (!blockIds.includes(parseInt(blockId))) {
                    blockIds.push(parseInt(blockId));
                }

                await this.reorderBlocks(columnId, blockIds);
                await this.load();
                this.render();
            });
        });
    }

    // ========== MODALS ==========

    showAddBlockModal(columnId) {
        const modal = document.createElement('div');
        modal.className = 'mega-modal';
        modal.innerHTML = `
            <div class="mega-modal-backdrop"></div>
            <div class="mega-modal-content">
                <div class="mega-modal-header">
                    <h3>Block hinzufügen</h3>
                    <button type="button" class="mega-modal-close">&times;</button>
                </div>
                <div class="mega-modal-body">
                    <div class="block-type-grid">
                        <button type="button" class="block-type-option" data-type="links">
                            <span class="material-symbols-rounded">list</span>
                            <span>Links</span>
                        </button>
                        <button type="button" class="block-type-option" data-type="image">
                            <span class="material-symbols-rounded">image</span>
                            <span>Bild</span>
                        </button>
                        <button type="button" class="block-type-option" data-type="promo">
                            <span class="material-symbols-rounded">campaign</span>
                            <span>Promo Karte</span>
                        </button>
                        <button type="button" class="block-type-option" data-type="divider">
                            <span class="material-symbols-rounded">horizontal_rule</span>
                            <span>Trenner</span>
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        setTimeout(() => modal.classList.add('open'), 10);

        // Close handlers
        modal.querySelector('.mega-modal-backdrop').addEventListener('click', () => this.closeModal(modal));
        modal.querySelector('.mega-modal-close').addEventListener('click', () => this.closeModal(modal));

        // Block type selection
        modal.querySelectorAll('.block-type-option').forEach(btn => {
            btn.addEventListener('click', async () => {
                this.closeModal(modal);
                const blockId = await this.addBlock(columnId, btn.dataset.type, this.getDefaultConfig(btn.dataset.type));
                if (blockId) {
                    this.showEditBlockModal(blockId);
                }
            });
        });
    }

    getDefaultConfig(type) {
        switch (type) {
            case 'links':
                return { title: 'Neue Kategorie', links: [] };
            case 'image':
                return { media_id: null, link: '', alt_text: '' };
            case 'promo':
                return { title: 'Promo Titel', text: 'Beschreibung', cta_text: 'Mehr erfahren', cta_link: '' };
            case 'divider':
                return {};
            default:
                return {};
        }
    }

    async showEditBlockModal(blockId) {
        // Find block in data
        let block = null;
        for (const col of this.data.columns) {
            block = col.blocks.find(b => b.id == blockId);
            if (block) break;
        }
        if (!block) return;

        const config = block.config || {};

        let formContent = '';
        switch (block.type) {
            case 'links':
                formContent = this.getLinksBlockForm(config);
                break;
            case 'image':
                formContent = this.getImageBlockForm(config);
                break;
            case 'promo':
                formContent = this.getPromoBlockForm(config);
                break;
            case 'divider':
                formContent = '<p>Trennlinie - keine Konfiguration nötig</p>';
                break;
        }

        const modal = document.createElement('div');
        modal.className = 'mega-modal';
        modal.innerHTML = `
            <div class="mega-modal-backdrop"></div>
            <div class="mega-modal-content mega-modal-large">
                <div class="mega-modal-header">
                    <h3>Block bearbeiten</h3>
                    <button type="button" class="mega-modal-close">&times;</button>
                </div>
                <div class="mega-modal-body">
                    <form id="edit-block-form">
                        ${formContent}
                    </form>
                </div>
                <div class="mega-modal-footer">
                    <button type="button" class="btn mega-modal-cancel">Abbrechen</button>
                    <button type="button" class="btn btn-primary mega-modal-save">Speichern</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        setTimeout(() => modal.classList.add('open'), 10);

        // Init MediaPickers in modal
        modal.querySelectorAll('.media-picker').forEach(el => {
            if (!MediaPicker.instances.has(el)) {
                new MediaPicker(el);
            }
        });

        // Close handlers
        modal.querySelector('.mega-modal-backdrop').addEventListener('click', () => this.closeModal(modal));
        modal.querySelector('.mega-modal-close').addEventListener('click', () => this.closeModal(modal));
        modal.querySelector('.mega-modal-cancel').addEventListener('click', () => this.closeModal(modal));

        // Save handler
        modal.querySelector('.mega-modal-save').addEventListener('click', async () => {
            const newConfig = this.getFormConfig(block.type, modal.querySelector('#edit-block-form'));
            await this.updateBlock(blockId, newConfig);
            this.closeModal(modal);
        });
    }

    getLinksBlockForm(config) {
        const links = config.links || [];
        return `
            <div class="form-group">
                <label class="form-label">Überschrift</label>
                <input type="text" name="title" class="form-input" value="${config.title || ''}">
            </div>
            <div class="form-group">
                <label class="form-label">Links</label>
                <div id="links-list" class="links-editor">
                    ${links.map((link, i) => `
                        <div class="link-item" data-index="${i}">
                            <input type="text" name="link_label_${i}" class="form-input" placeholder="Label" value="${link.label || ''}">
                            <input type="text" name="link_url_${i}" class="form-input" placeholder="URL" value="${link.url || ''}">
                            <button type="button" class="icon-btn remove-link-btn">
                                <span class="material-symbols-rounded">delete</span>
                            </button>
                        </div>
                    `).join('')}
                </div>
                <button type="button" class="btn btn-sm" id="add-link-btn">
                    <span class="material-symbols-rounded">add</span> Link hinzufügen
                </button>
            </div>
        `;
    }

    getImageBlockForm(config) {
        return `
            <div class="form-group">
                <label class="form-label">Bild</label>
                <div class="media-picker" data-field="media_id" data-folder="menu"
                    data-media-id="${config.media_id || ''}"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Link (optional)</label>
                <input type="text" name="link" class="form-input" value="${config.link || ''}" placeholder="/sale">
            </div>
            <div class="form-group">
                <label class="form-label">Alt-Text</label>
                <input type="text" name="alt_text" class="form-input" value="${config.alt_text || ''}">
            </div>
        `;
    }

    getPromoBlockForm(config) {
        return `
            <div class="form-group">
                <label class="form-label">Bild (optional)</label>
                <div class="media-picker" data-field="media_id" data-folder="menu"
                    data-media-id="${config.media_id || ''}"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Titel</label>
                <input type="text" name="title" class="form-input" value="${config.title || ''}">
            </div>
            <div class="form-group">
                <label class="form-label">Text</label>
                <textarea name="text" class="form-textarea" rows="3">${config.text || ''}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Button Text</label>
                    <input type="text" name="cta_text" class="form-input" value="${config.cta_text || ''}">
                </div>
                <div class="form-group">
                    <label class="form-label">Button Link</label>
                    <input type="text" name="cta_link" class="form-input" value="${config.cta_link || ''}">
                </div>
            </div>
        `;
    }

    getFormConfig(type, form) {
        const formData = new FormData(form);
        const config = {};

        switch (type) {
            case 'links':
                config.title = formData.get('title');
                config.links = [];
                let i = 0;
                while (formData.has(`link_label_${i}`)) {
                    const label = formData.get(`link_label_${i}`);
                    const url = formData.get(`link_url_${i}`);
                    if (label) {
                        config.links.push({ label, url });
                    }
                    i++;
                }
                break;
            case 'image':
                config.media_id = formData.get('media_id') || null;
                config.link = formData.get('link');
                config.alt_text = formData.get('alt_text');
                break;
            case 'promo':
                config.media_id = formData.get('media_id') || null;
                config.title = formData.get('title');
                config.text = formData.get('text');
                config.cta_text = formData.get('cta_text');
                config.cta_link = formData.get('cta_link');
                break;
        }

        return config;
    }

    closeModal(modal) {
        modal.classList.remove('open');
        setTimeout(() => modal.remove(), 300);
    }
}

// Export for global access
window.MegaMenuBuilder = MegaMenuBuilder;
