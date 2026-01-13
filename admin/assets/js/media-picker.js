/**
 * MediaPicker - Zentrale Media-Auswahl Komponente
 * 
 * Verwendung:
 * <div class="media-picker" data-field="mega_image" data-folder="menu"></div>
 * 
 * Oder per JavaScript:
 * new MediaPicker(element, { folder: 'menu', onSelect: (media) => {} });
 */

class MediaPicker {
    static instances = new Map();
    static modalOpen = false;
    static currentPicker = null;

    constructor(element, options = {}) {
        this.element = element;
        this.options = {
            folder: element.dataset.folder || options.folder || 'general',
            field: element.dataset.field || options.field || 'media_id',
            shopId: parseInt(element.dataset.shopId || options.shopId || 1),
            onSelect: options.onSelect || null,
            onRemove: options.onRemove || null,
            ...options
        };

        this.mediaId = null;
        this.mediaData = null;

        this.init();
        MediaPicker.instances.set(element, this);
    }

    init() {
        this.render();
        this.bindEvents();
        this.loadExistingMedia();
    }

    render() {
        this.element.innerHTML = `
            <input type="hidden" name="${this.options.field}" class="media-picker-input" value="">
            <div class="media-picker-preview">
                <img src="" alt="" class="media-picker-image" style="display:none">
                <div class="media-picker-placeholder">
                    <span class="material-symbols-rounded">add_photo_alternate</span>
                    <span class="media-picker-text">Bild hochladen</span>
                </div>
            </div>
            <div class="media-picker-actions">
                <button type="button" class="btn btn-sm media-picker-upload">
                    <span class="material-symbols-rounded">upload</span>
                    Hochladen
                </button>
                <button type="button" class="btn btn-sm media-picker-library">
                    <span class="material-symbols-rounded">photo_library</span>
                    Bibliothek
                </button>
                <button type="button" class="btn btn-sm btn-danger media-picker-remove" style="display:none">
                    <span class="material-symbols-rounded">delete</span>
                </button>
            </div>
            <input type="file" class="media-picker-file" accept="image/*" style="display:none">
            <div class="media-picker-progress" style="display:none">
                <div class="media-picker-progress-bar"></div>
            </div>
        `;

        this.input = this.element.querySelector('.media-picker-input');
        this.preview = this.element.querySelector('.media-picker-preview');
        this.image = this.element.querySelector('.media-picker-image');
        this.placeholder = this.element.querySelector('.media-picker-placeholder');
        this.uploadBtn = this.element.querySelector('.media-picker-upload');
        this.libraryBtn = this.element.querySelector('.media-picker-library');
        this.removeBtn = this.element.querySelector('.media-picker-remove');
        this.fileInput = this.element.querySelector('.media-picker-file');
        this.progress = this.element.querySelector('.media-picker-progress');
        this.progressBar = this.element.querySelector('.media-picker-progress-bar');
    }

    bindEvents() {
        // Click on preview = upload
        this.preview.addEventListener('click', () => this.fileInput.click());

        // Upload button
        this.uploadBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.fileInput.click();
        });

        // Library button
        this.libraryBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.openLibrary();
        });

        // Remove button
        this.removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.remove();
        });

        // File selected
        this.fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.upload(e.target.files[0]);
            }
        });

        // Drag & Drop
        this.preview.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.preview.classList.add('dragover');
        });

        this.preview.addEventListener('dragleave', () => {
            this.preview.classList.remove('dragover');
        });

        this.preview.addEventListener('drop', (e) => {
            e.preventDefault();
            this.preview.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                this.upload(e.dataTransfer.files[0]);
            }
        });

        // Paste from clipboard
        document.addEventListener('paste', (e) => {
            // Only handle paste if this picker is focused or was last interacted with
            if (!this.element.closest(':focus-within') && MediaPicker.currentPicker !== this) {
                return;
            }

            const items = e.clipboardData?.items;
            if (!items) return;

            for (const item of items) {
                if (item.type.startsWith('image/')) {
                    const file = item.getAsFile();
                    if (file) {
                        e.preventDefault();
                        this.upload(file);
                        break;
                    }
                }
            }
        });

        // Track current picker for paste
        this.element.addEventListener('mouseenter', () => {
            MediaPicker.currentPicker = this;
        });
    }

    loadExistingMedia() {
        // Check if there's a pre-set value
        const existingId = this.element.dataset.mediaId || this.input.value;
        if (existingId) {
            this.loadMedia(parseInt(existingId));
        }
    }

    async loadMedia(id) {
        try {
            const response = await fetch(`/admin/api/media.php?action=get&id=${id}`);
            const data = await response.json();

            if (data.success && data.media) {
                this.setMedia(data.media);
            }
        } catch (error) {
            console.error('Error loading media:', error);
        }
    }

    async upload(file) {
        // Validate client-side
        if (!file.type.startsWith('image/')) {
            this.showError('Nur Bilddateien erlaubt');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            this.showError('Datei zu groß (max. 10MB)');
            return;
        }

        // Show instant preview
        this.showPreview(file);

        // Show progress
        this.progress.style.display = 'block';
        this.progressBar.style.width = '0%';

        // Upload
        const formData = new FormData();
        formData.append('file', file);
        formData.append('folder', this.options.folder);
        formData.append('shop_id', this.options.shopId);

        try {
            const xhr = new XMLHttpRequest();

            // Progress tracking
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percent = (e.loaded / e.total) * 100;
                    this.progressBar.style.width = `${percent}%`;
                }
            });

            // Complete
            xhr.addEventListener('load', () => {
                this.progress.style.display = 'none';

                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success && data.media) {
                        this.setMedia(data.media);
                    } else {
                        this.showError(data.error || 'Upload fehlgeschlagen');
                        this.clearPreview();
                    }
                } else {
                    this.showError('Upload fehlgeschlagen');
                    this.clearPreview();
                }
            });

            // Error
            xhr.addEventListener('error', () => {
                this.progress.style.display = 'none';
                this.showError('Netzwerkfehler');
                this.clearPreview();
            });

            xhr.open('POST', '/admin/api/media.php?action=upload');
            xhr.send(formData);

        } catch (error) {
            this.progress.style.display = 'none';
            this.showError('Upload fehlgeschlagen: ' + error.message);
            this.clearPreview();
        }
    }

    showPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.image.src = e.target.result;
            this.image.style.display = 'block';
            this.placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    setMedia(media) {
        this.mediaId = media.id;
        this.mediaData = media;
        this.input.value = media.id;

        this.image.src = media.thumbnail_url || media.url;
        this.image.style.display = 'block';
        this.placeholder.style.display = 'none';
        this.removeBtn.style.display = 'inline-flex';

        // Dispatch custom event
        this.element.dispatchEvent(new CustomEvent('media-selected', {
            detail: { media }
        }));

        if (this.options.onSelect) {
            this.options.onSelect(media);
        }
    }

    remove() {
        this.mediaId = null;
        this.mediaData = null;
        this.input.value = '';

        this.clearPreview();

        // Dispatch custom event
        this.element.dispatchEvent(new CustomEvent('media-removed'));

        if (this.options.onRemove) {
            this.options.onRemove();
        }
    }

    clearPreview() {
        this.image.src = '';
        this.image.style.display = 'none';
        this.placeholder.style.display = 'flex';
        this.removeBtn.style.display = 'none';
    }

    showError(message) {
        // Show toast or modal
        if (typeof showToast === 'function') {
            showToast(message, 'error');
        } else if (typeof adminModal !== 'undefined') {
            adminModal.error(message);
        } else {
            console.error(message);
        }
    }

    openLibrary() {
        MediaPicker.openModal(this);
    }

    // ========== STATIC MODAL METHODS ==========

    static openModal(picker) {
        if (MediaPicker.modalOpen) {
            return;
        }

        MediaPicker.modalOpen = true;
        MediaPicker.currentPicker = picker;

        // Create modal if not exists
        let modal = document.getElementById('media-library-modal');
        if (!modal) {
            modal = MediaPicker.createModal();
            document.body.appendChild(modal);
        }

        // Show modal
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';

        // Load media
        MediaPicker.loadLibraryMedia(picker.options.shopId, picker.options.folder);
    }

    static createModal() {
        const modal = document.createElement('div');
        modal.id = 'media-library-modal';
        modal.className = 'media-modal';
        modal.innerHTML = `
            <div class="media-modal-backdrop" onclick="MediaPicker.closeModal()"></div>
            <div class="media-modal-content">
                <div class="media-modal-header">
                    <h3>Media Bibliothek</h3>
                    <button type="button" class="media-modal-close" onclick="MediaPicker.closeModal()">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>
                <div class="media-modal-toolbar">
                    <div class="media-modal-folders">
                        <button type="button" class="media-folder-btn active" data-folder="all">Alle</button>
                    </div>
                    <div class="media-modal-actions">
                        <label class="btn btn-primary media-modal-upload-btn">
                            <span class="material-symbols-rounded">upload</span>
                            Hochladen
                            <input type="file" accept="image/*" multiple style="display:none" 
                                onchange="MediaPicker.handleModalUpload(this.files)">
                        </label>
                    </div>
                </div>
                <div class="media-modal-body">
                    <div class="media-modal-grid" id="media-grid">
                        <div class="media-loading">Laden...</div>
                    </div>
                </div>
                <div class="media-modal-footer">
                    <button type="button" class="btn" onclick="MediaPicker.closeModal()">Abbrechen</button>
                    <button type="button" class="btn btn-primary" id="media-select-btn" disabled
                        onclick="MediaPicker.confirmSelection()">Auswählen</button>
                </div>
            </div>
        `;
        return modal;
    }

    static closeModal() {
        const modal = document.getElementById('media-library-modal');
        if (modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }
        MediaPicker.modalOpen = false;
        MediaPicker.selectedMedia = null;
    }

    static selectedMedia = null;

    static async loadLibraryMedia(shopId = 1, folder = 'all') {
        const grid = document.getElementById('media-grid');
        grid.innerHTML = '<div class="media-loading">Laden...</div>';

        try {
            const url = `/admin/api/media.php?action=list&shop_id=${shopId}&folder=${folder === 'all' ? '' : folder}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                MediaPicker.renderGrid(data.media);
                MediaPicker.renderFolders(data.folders);
            } else {
                grid.innerHTML = '<div class="media-error">Fehler beim Laden</div>';
            }
        } catch (error) {
            grid.innerHTML = '<div class="media-error">Netzwerkfehler</div>';
        }
    }

    static renderGrid(media) {
        const grid = document.getElementById('media-grid');

        if (media.length === 0) {
            grid.innerHTML = `
                <div class="media-empty">
                    <span class="material-symbols-rounded">photo_library</span>
                    <p>Keine Medien vorhanden</p>
                    <p>Laden Sie Bilder hoch, um zu beginnen.</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = media.map(item => `
            <div class="media-grid-item" data-id="${item.id}" onclick="MediaPicker.selectMedia(${item.id}, this)">
                <img src="${item.thumbnail_url}" alt="${item.alt_text || item.filename}" loading="lazy">
                <div class="media-grid-item-info">
                    <span class="media-grid-item-name">${item.filename}</span>
                </div>
                <div class="media-grid-item-check">
                    <span class="material-symbols-rounded">check_circle</span>
                </div>
            </div>
        `).join('');
    }

    static renderFolders(folders) {
        const container = document.querySelector('.media-modal-folders');
        if (!container) return;

        const total = folders.reduce((sum, f) => sum + parseInt(f.count), 0);

        container.innerHTML = `
            <button type="button" class="media-folder-btn active" data-folder="all" 
                onclick="MediaPicker.switchFolder('all', this)">
                Alle (${total})
            </button>
            ${folders.map(f => `
                <button type="button" class="media-folder-btn" data-folder="${f.folder}"
                    onclick="MediaPicker.switchFolder('${f.folder}', this)">
                    ${f.folder.charAt(0).toUpperCase() + f.folder.slice(1)} (${f.count})
                </button>
            `).join('')}
        `;
    }

    static switchFolder(folder, btn) {
        document.querySelectorAll('.media-folder-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const shopId = MediaPicker.currentPicker?.options.shopId || 1;
        MediaPicker.loadLibraryMedia(shopId, folder);
    }

    static selectMedia(id, element) {
        // Remove previous selection
        document.querySelectorAll('.media-grid-item.selected').forEach(el => {
            el.classList.remove('selected');
        });

        // Add selection
        element.classList.add('selected');
        MediaPicker.selectedMedia = id;

        // Enable select button
        document.getElementById('media-select-btn').disabled = false;
    }

    static async confirmSelection() {
        if (!MediaPicker.selectedMedia || !MediaPicker.currentPicker) {
            return;
        }

        try {
            const response = await fetch(`/admin/api/media.php?action=get&id=${MediaPicker.selectedMedia}`);
            const data = await response.json();

            if (data.success && data.media) {
                MediaPicker.currentPicker.setMedia(data.media);
            }
        } catch (error) {
            console.error('Error selecting media:', error);
        }

        MediaPicker.closeModal();
    }

    static async handleModalUpload(files) {
        const shopId = MediaPicker.currentPicker?.options.shopId || 1;
        const folder = MediaPicker.currentPicker?.options.folder || 'general';

        for (const file of files) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('folder', folder);
            formData.append('shop_id', shopId);

            try {
                await fetch('/admin/api/media.php?action=upload', {
                    method: 'POST',
                    body: formData
                });
            } catch (error) {
                console.error('Upload error:', error);
            }
        }

        // Reload grid
        MediaPicker.loadLibraryMedia(shopId, 'all');
    }
}

// Auto-initialize all media pickers on page load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.media-picker').forEach(el => {
        if (!MediaPicker.instances.has(el)) {
            new MediaPicker(el);
        }
    });
});

// Export for global access
window.MediaPicker = MediaPicker;
