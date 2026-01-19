<?php /** Katalog - Neue Kategorie erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/categories">Kategorien</a> <span>›</span> <span>Neue
                Kategorie</span></nav>
        <h1>Neue Kategorie erstellen</h1>
        <p class="page-subtitle">Erstellen Sie eine neue Produktkategorie</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/categories" class="btn">Abbrechen</a>
        <button class="btn btn-primary" onclick="CategoryForm.save()"><span class="material-symbols-rounded">save</span>
            Speichern</button>
    </div>
</div>

<form id="categoryForm" class="category-form">
    <div class="dashboard-grid">
        <!-- Main Content -->
        <div class="card">
            <div class="card-header">
                <h3>Grunddaten</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Kategoriename <span class="required">*</span></label>
                    <input type="text" class="form-input" id="categoryName" name="name" placeholder="z.B. Kleidung"
                        required>
                    <span class="form-error" id="errorName"></span>
                </div>

                <div class="form-group">
                    <label class="form-label">URL-Slug</label>
                    <input type="text" class="form-input" id="categorySlug" name="slug"
                        placeholder="wird automatisch generiert">
                    <small class="form-hint">Leer lassen für automatische Generierung</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Übergeordnete Kategorie</label>
                    <select class="form-select" id="parentCategory" name="parent_id">
                        <option value="">Keine (Hauptkategorie)</option>
                    </select>
                    <small class="form-hint">Optional: Wählen Sie eine Elternkategorie für eine Unterkategorie</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Beschreibung</label>
                    <textarea class="form-textarea" id="categoryDescription" name="description" rows="4"
                        placeholder="Beschreiben Sie diese Kategorie..."></textarea>
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
                        <label class="form-checkbox">
                            <input type="checkbox" id="isActive" name="is_active" checked>
                            <span>Kategorie aktivieren</span>
                        </label>
                        <small class="form-hint">Inaktive Kategorien werden im Shop nicht angezeigt</small>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="card" style="margin-top:24px;">
                <div class="card-header">
                    <h3>Bilder</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Kategoriebild</label>
                        <div class="image-upload-zone" id="imageUploadZone"
                            onclick="document.getElementById('imageInput').click()">
                            <span class="material-symbols-rounded">add_photo_alternate</span>
                            <p>Bild hochladen</p>
                            <small>JPG, PNG, WebP · Max. 5MB</small>
                        </div>
                        <input type="file" id="imageInput" name="image" accept="image/*" style="display:none;">
                        <div class="image-preview" id="imagePreview" style="display:none;">
                            <img id="imagePreviewImg" src="" alt="Preview">
                            <button type="button" class="btn btn-sm btn-danger-ghost"
                                onclick="CategoryForm.removeImage('image')">&times;</button>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:20px;">
                        <label class="form-label">Banner (optional)</label>
                        <div class="image-upload-zone" id="bannerUploadZone"
                            onclick="document.getElementById('bannerInput').click()">
                            <span class="material-symbols-rounded">panorama</span>
                            <p>Banner hochladen</p>
                            <small>Empfohlen: 1200×300px</small>
                        </div>
                        <input type="file" id="bannerInput" name="banner" accept="image/*" style="display:none;">
                        <div class="image-preview banner-preview" id="bannerPreview" style="display:none;">
                            <img id="bannerPreviewImg" src="" alt="Banner Preview">
                            <button type="button" class="btn btn-sm btn-danger-ghost"
                                onclick="CategoryForm.removeImage('banner')">&times;</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO -->
    <div class="card" style="margin-top:24px;">
        <div class="card-header">
            <h3>SEO</h3>
        </div>
        <div class="card-body">
            <div class="dashboard-grid">
                <div>
                    <div class="form-group">
                        <label class="form-label">Meta-Titel</label>
                        <input type="text" class="form-input" id="metaTitle" name="meta_title"
                            placeholder="Seitentitel für Suchmaschinen">
                        <div class="char-counter"><span id="metaTitleCount">0</span>/60</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta-Beschreibung</label>
                        <textarea class="form-textarea" id="metaDescription" name="meta_description" rows="3"
                            placeholder="Beschreibung für Suchergebnisse"></textarea>
                        <div class="char-counter"><span id="metaDescCount">0</span>/160</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta-Keywords</label>
                        <input type="text" class="form-input" id="metaKeywords" name="meta_keywords"
                            placeholder="keyword1, keyword2, keyword3">
                    </div>
                </div>

                <div class="seo-preview">
                    <h4>Vorschau in Suchergebnissen</h4>
                    <div class="google-preview">
                        <div class="google-title" id="previewTitle">Kategoriename - Mein Shop</div>
                        <div class="google-url" id="previewUrl">example.com/kategorie/<span
                                id="previewSlug">kategorie-name</span></div>
                        <div class="google-desc" id="previewDesc">Meta-Beschreibung wird hier angezeigt...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Toast -->
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

    .image-upload-zone {
        border: 2px dashed var(--border);
        border-radius: var(--radius-md);
        padding: 32px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .image-upload-zone:hover {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.05);
    }

    .image-upload-zone .material-symbols-rounded {
        font-size: 48px;
        color: var(--text-muted);
    }

    .image-preview {
        position: relative;
        width: 120px;
        height: 120px;
        margin-top: 12px;
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-preview button {
        position: absolute;
        top: 4px;
        right: 4px;
    }

    .banner-preview {
        width: 100%;
        height: 100px;
    }

    .char-counter {
        font-size: 12px;
        color: var(--text-muted);
        text-align: right;
        margin-top: 4px;
    }

    .seo-preview {
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
        padding: 20px;
    }

    .seo-preview h4 {
        margin-bottom: 16px;
        color: var(--text-muted);
        font-size: 13px;
    }

    .google-preview {
        font-family: Arial, sans-serif;
    }

    .google-title {
        color: #1a0dab;
        font-size: 18px;
        margin-bottom: 2px;
    }

    .google-url {
        color: #006621;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .google-desc {
        color: #545454;
        font-size: 13px;
        line-height: 1.4;
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
</style>

<script>
    const CategoryForm = {
        apiBase: 'api/categories.php',
        shopId: 1,
        imageFile: null,
        bannerFile: null,

        async init() {
            await this.loadParentCategories();
            this.setupEventListeners();
        },

        async loadParentCategories() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_categories&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    const select = document.getElementById('parentCategory');
                    data.flat.forEach(cat => {
                        const indent = cat.parent_id ? '└─ ' : '';
                        select.innerHTML += `<option value="${cat.id}">${indent}${cat.name}</option>`;
                    });
                }
            } catch (e) {
                console.error('Error loading categories:', e);
            }
        },

        setupEventListeners() {
            // Name input - auto-generate slug preview
            document.getElementById('categoryName').addEventListener('input', (e) => {
                const slug = this.generateSlug(e.target.value);
                document.getElementById('categorySlug').placeholder = slug;
                document.getElementById('previewSlug').textContent = slug || 'kategorie-name';
                document.getElementById('previewTitle').textContent = e.target.value || 'Kategoriename' + ' - Mein Shop';
                this.clearError('errorName');
            });

            // SEO meta counters
            document.getElementById('metaTitle').addEventListener('input', (e) => {
                document.getElementById('metaTitleCount').textContent = e.target.value.length;
                document.getElementById('previewTitle').textContent = e.target.value || 'Kategoriename - Mein Shop';
            });

            document.getElementById('metaDescription').addEventListener('input', (e) => {
                document.getElementById('metaDescCount').textContent = e.target.value.length;
                document.getElementById('previewDesc').textContent = e.target.value || 'Meta-Beschreibung wird hier angezeigt...';
            });

            // Image uploads
            document.getElementById('imageInput').addEventListener('change', (e) => {
                this.handleImageUpload(e.target.files[0], 'image');
            });

            document.getElementById('bannerInput').addEventListener('change', (e) => {
                this.handleImageUpload(e.target.files[0], 'banner');
            });
        },

        handleImageUpload(file, type) {
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                this.showToast('Nur Bilddateien erlaubt', 'error');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                this.showToast('Bild zu groß (max. 5MB)', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                if (type === 'image') {
                    this.imageFile = file;
                    document.getElementById('imageUploadZone').style.display = 'none';
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('imagePreviewImg').src = e.target.result;
                } else {
                    this.bannerFile = file;
                    document.getElementById('bannerUploadZone').style.display = 'none';
                    document.getElementById('bannerPreview').style.display = 'block';
                    document.getElementById('bannerPreviewImg').src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        },

        removeImage(type) {
            if (type === 'image') {
                this.imageFile = null;
                document.getElementById('imageInput').value = '';
                document.getElementById('imageUploadZone').style.display = 'block';
                document.getElementById('imagePreview').style.display = 'none';
            } else {
                this.bannerFile = null;
                document.getElementById('bannerInput').value = '';
                document.getElementById('bannerUploadZone').style.display = 'block';
                document.getElementById('bannerPreview').style.display = 'none';
            }
        },

        generateSlug(text) {
            return text.toLowerCase()
                .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue').replace(/ß/g, 'ss')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '');
        },

        clearError(errorId) {
            const el = document.getElementById(errorId);
            if (el) {
                el.textContent = '';
                el.classList.remove('show');
            }
        },

        validate() {
            let valid = true;

            const name = document.getElementById('categoryName').value.trim();
            if (!name) {
                document.getElementById('errorName').textContent = 'Kategoriename ist erforderlich';
                document.getElementById('errorName').classList.add('show');
                valid = false;
            }

            return valid;
        },

        async save() {
            if (!this.validate()) {
                this.showToast('Bitte füllen Sie alle Pflichtfelder aus', 'error');
                return;
            }

            const formData = new FormData(document.getElementById('categoryForm'));
            formData.append('action', 'save_category');
            formData.append('shop_id', this.shopId);
            formData.set('is_active', document.getElementById('isActive').checked ? 1 : 0);

            // Add image files
            if (this.imageFile) {
                formData.set('image', this.imageFile);
            }
            if (this.bannerFile) {
                formData.set('banner', this.bannerFile);
            }

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Kategorie erstellt', 'success');
                    setTimeout(() => {
                        window.location.href = '?page=catalog/categories';
                    }, 1000);
                } else {
                    const errors = data.errors || [data.error || 'Unbekannter Fehler'];
                    this.showToast(errors.join(', '), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', () => CategoryForm.init());
</script>