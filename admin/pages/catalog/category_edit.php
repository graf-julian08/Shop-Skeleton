<?php
/** Katalog - Kategorie bearbeiten */
$categoryId = (int) ($_GET['id'] ?? 0);
?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/categories">Kategorien</a> <span>›</span> <span
                id="breadcrumbName">Kategorie</span></nav>
        <h1 id="pageTitle">Kategorie bearbeiten</h1>
        <p class="page-subtitle">Bearbeiten Sie die Kategorie-Einstellungen</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/categories" class="btn">Abbrechen</a>
        <button class="btn btn-danger-ghost" onclick="CategoryEdit.deleteCategory()"><span
                class="material-symbols-rounded">delete</span></button>
        <button class="btn btn-primary" onclick="CategoryEdit.save()"><span class="material-symbols-rounded">save</span>
            Speichern</button>
    </div>
</div>

<!-- Loading State -->
<div class="loading-state" id="loadingState">
    <span class="material-symbols-rounded spinning">sync</span>
    <p>Kategorie wird geladen...</p>
</div>

<form id="categoryForm" class="category-form" style="display:none;">
    <input type="hidden" id="categoryId" name="id" value="<?= $categoryId ?>">

    <div class="dashboard-grid">
        <!-- Main Content -->
        <div class="card">
            <div class="card-header">
                <h3>Grunddaten</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Kategoriename <span class="required">*</span></label>
                    <input type="text" class="form-input" id="categoryName" name="name" required>
                    <span class="form-error" id="errorName"></span>
                </div>

                <div class="form-group">
                    <label class="form-label">URL-Slug</label>
                    <input type="text" class="form-input" id="categorySlug" name="slug">
                </div>

                <div class="form-group">
                    <label class="form-label">Übergeordnete Kategorie</label>
                    <select class="form-select" id="parentCategory" name="parent_id">
                        <option value="">Keine (Hauptkategorie)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Beschreibung</label>
                    <textarea class="form-textarea" id="categoryDescription" name="description" rows="4"></textarea>
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
                            <input type="checkbox" id="isActive" name="is_active">
                            <span>Kategorie aktivieren</span>
                        </label>
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
                        </div>
                        <input type="file" id="imageInput" name="image" accept="image/*" style="display:none;">
                        <div class="image-preview" id="imagePreview" style="display:none;">
                            <img id="imagePreviewImg" src="" alt="Preview">
                            <button type="button" class="btn btn-sm btn-danger-ghost"
                                onclick="CategoryEdit.removeImage('image')">&times;</button>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:20px;">
                        <label class="form-label">Banner</label>
                        <div class="image-upload-zone" id="bannerUploadZone"
                            onclick="document.getElementById('bannerInput').click()">
                            <span class="material-symbols-rounded">panorama</span>
                            <p>Banner hochladen</p>
                        </div>
                        <input type="file" id="bannerInput" name="banner" accept="image/*" style="display:none;">
                        <div class="image-preview banner-preview" id="bannerPreview" style="display:none;">
                            <img id="bannerPreviewImg" src="" alt="Banner Preview">
                            <button type="button" class="btn btn-sm btn-danger-ghost"
                                onclick="CategoryEdit.removeImage('banner')">&times;</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="card" style="margin-top:24px;">
                <div class="card-header">
                    <h3>Statistiken</h3>
                </div>
                <div class="card-body">
                    <div class="stat-row">
                        <span>Produkte in dieser Kategorie:</span>
                        <strong id="statProducts">0</strong>
                    </div>
                    <div class="stat-row">
                        <span>Unterkategorien:</span>
                        <strong id="statChildren">0</strong>
                    </div>
                    <div class="stat-row">
                        <span>Erstellt:</span>
                        <span id="statCreated">-</span>
                    </div>
                    <div class="stat-row">
                        <span>Aktualisiert:</span>
                        <span id="statUpdated">-</span>
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
                        <input type="text" class="form-input" id="metaTitle" name="meta_title">
                        <div class="char-counter"><span id="metaTitleCount">0</span>/60</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta-Beschreibung</label>
                        <textarea class="form-textarea" id="metaDescription" name="meta_description"
                            rows="3"></textarea>
                        <div class="char-counter"><span id="metaDescCount">0</span>/160</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Meta-Keywords</label>
                        <input type="text" class="form-input" id="metaKeywords" name="meta_keywords">
                    </div>
                </div>

                <div class="seo-preview">
                    <h4>Vorschau in Suchergebnissen</h4>
                    <div class="google-preview">
                        <div class="google-title" id="previewTitle">Kategoriename - Mein Shop</div>
                        <div class="google-url">example.com/kategorie/<span id="previewSlug">kategorie-name</span></div>
                        <div class="google-desc" id="previewDesc">Meta-Beschreibung...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Delete Modal -->
<div class="modal" id="deleteModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Kategorie löschen</h3>
            <button class="modal-close" onclick="CategoryEdit.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Möchten Sie diese Kategorie wirklich löschen?</p>
            <p style="color:var(--error);margin-top:12px;" id="deleteWarning"></p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="CategoryEdit.closeModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="CategoryEdit.confirmDelete()">Löschen</button>
        </div>
    </div>
</div>

<!-- Toast -->
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

    .image-upload-zone {
        border: 2px dashed var(--border);
        border-radius: var(--radius-md);
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .image-upload-zone:hover {
        border-color: var(--accent);
    }

    .image-upload-zone .material-symbols-rounded {
        font-size: 36px;
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
        height: 80px;
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
</style>

<script>
    const CategoryEdit = {
        apiBase: 'api/categories.php',
        shopId: 1,
        categoryId: <?= $categoryId ?>,
        category: null,
        imageFile: null,
        bannerFile: null,
        deleteImage: false,
        deleteBanner: false,

        async init() {
            if (!this.categoryId) {
                this.showToast('Keine Kategorie-ID', 'error');
                return;
            }

            await this.loadParentCategories();
            await this.loadCategory();
            this.setupEventListeners();
        },

        async loadParentCategories() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_categories&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    const select = document.getElementById('parentCategory');
                    data.flat.forEach(cat => {
                        if (cat.id !== this.categoryId) {
                            const indent = cat.parent_id ? '└─ ' : '';
                            select.innerHTML += `<option value="${cat.id}">${indent}${cat.name}</option>`;
                        }
                    });
                }
            } catch (e) {
                console.error('Error loading categories:', e);
            }
        },

        async loadCategory() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_category&shop_id=${this.shopId}&id=${this.categoryId}`);
                const data = await res.json();

                if (!data.success) {
                    this.showToast('Kategorie nicht gefunden', 'error');
                    setTimeout(() => window.location.href = '?page=catalog/categories', 2000);
                    return;
                }

                this.category = data.category;
                this.populateForm();

                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('categoryForm').style.display = 'block';

            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        populateForm() {
            const c = this.category;

            document.getElementById('pageTitle').textContent = c.name;
            document.getElementById('breadcrumbName').textContent = c.name;

            document.getElementById('categoryName').value = c.name;
            document.getElementById('categorySlug').value = c.slug;
            document.getElementById('parentCategory').value = c.parent_id || '';
            document.getElementById('categoryDescription').value = c.description || '';
            document.getElementById('isActive').checked = c.is_active == 1;

            // SEO
            document.getElementById('metaTitle').value = c.meta_title || '';
            document.getElementById('metaDescription').value = c.meta_description || '';
            document.getElementById('metaKeywords').value = c.meta_keywords || '';
            document.getElementById('metaTitleCount').textContent = (c.meta_title || '').length;
            document.getElementById('metaDescCount').textContent = (c.meta_description || '').length;

            // Preview
            document.getElementById('previewTitle').textContent = c.meta_title || c.name + ' - Mein Shop';
            document.getElementById('previewSlug').textContent = c.slug;
            document.getElementById('previewDesc').textContent = c.meta_description || 'Meta-Beschreibung...';

            // Images
            if (c.image_path) {
                document.getElementById('imageUploadZone').style.display = 'none';
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('imagePreviewImg').src = c.image_path;
            }

            if (c.banner_path) {
                document.getElementById('bannerUploadZone').style.display = 'none';
                document.getElementById('bannerPreview').style.display = 'block';
                document.getElementById('bannerPreviewImg').src = c.banner_path;
            }

            // Stats
            document.getElementById('statProducts').textContent = c.product_count || 0;
            document.getElementById('statChildren').textContent = c.children?.length || 0;
            document.getElementById('statCreated').textContent = this.formatDate(c.created_at);
            document.getElementById('statUpdated').textContent = this.formatDate(c.updated_at);
        },

        setupEventListeners() {
            document.getElementById('categoryName').addEventListener('input', (e) => {
                document.getElementById('previewTitle').textContent = e.target.value || 'Kategoriename - Mein Shop';
            });

            document.getElementById('metaTitle').addEventListener('input', (e) => {
                document.getElementById('metaTitleCount').textContent = e.target.value.length;
            });

            document.getElementById('metaDescription').addEventListener('input', (e) => {
                document.getElementById('metaDescCount').textContent = e.target.value.length;
            });

            document.getElementById('imageInput').addEventListener('change', (e) => {
                this.handleImageUpload(e.target.files[0], 'image');
            });

            document.getElementById('bannerInput').addEventListener('change', (e) => {
                this.handleImageUpload(e.target.files[0], 'banner');
            });
        },

        handleImageUpload(file, type) {
            if (!file || !file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                if (type === 'image') {
                    this.imageFile = file;
                    this.deleteImage = false;
                    document.getElementById('imageUploadZone').style.display = 'none';
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('imagePreviewImg').src = e.target.result;
                } else {
                    this.bannerFile = file;
                    this.deleteBanner = false;
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
                this.deleteImage = true;
                document.getElementById('imageInput').value = '';
                document.getElementById('imageUploadZone').style.display = 'block';
                document.getElementById('imagePreview').style.display = 'none';
            } else {
                this.bannerFile = null;
                this.deleteBanner = true;
                document.getElementById('bannerInput').value = '';
                document.getElementById('bannerUploadZone').style.display = 'block';
                document.getElementById('bannerPreview').style.display = 'none';
            }
        },

        async save() {
            const formData = new FormData(document.getElementById('categoryForm'));
            formData.append('action', 'save_category');
            formData.append('shop_id', this.shopId);
            formData.set('is_active', document.getElementById('isActive').checked ? 1 : 0);

            if (this.imageFile) formData.set('image', this.imageFile);
            if (this.bannerFile) formData.set('banner', this.bannerFile);
            if (this.deleteImage) formData.append('delete_image', '1');
            if (this.deleteBanner) formData.append('delete_banner', '1');

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Kategorie gespeichert', 'success');
                    document.getElementById('pageTitle').textContent = formData.get('name');
                    document.getElementById('breadcrumbName').textContent = formData.get('name');
                } else {
                    this.showToast(data.errors?.join(', ') || data.error, 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        deleteCategory() {
            let warning = '';
            if (this.category.children?.length > 0) {
                warning = 'Hat Unterkategorien - bitte zuerst diese löschen.';
            } else if (this.category.product_count > 0) {
                warning = `Achtung: ${this.category.product_count} verknüpfte Produkte werden ebenfalls gelöscht!`;
            }
            document.getElementById('deleteWarning').textContent = warning;
            document.getElementById('deleteModal').style.display = 'flex';
        },

        async confirmDelete() {
            try {
                const formData = new FormData();
                formData.append('action', 'delete_category');
                formData.append('shop_id', this.shopId);
                formData.append('id', this.categoryId);

                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Kategorie gelöscht', 'success');
                    setTimeout(() => window.location.href = '?page=catalog/categories', 1000);
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

    document.addEventListener('DOMContentLoaded', () => CategoryEdit.init());
</script>