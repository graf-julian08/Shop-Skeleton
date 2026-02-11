<?php /** Kollaborationen - Neue Kollaboration erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=collaborations/index">Kollaborationen</a> <span>›</span> <span>Neue
                Kollaboration</span></nav>
        <h1>Neue Kollaboration erstellen</h1>
        <p class="page-subtitle">Fügen Sie eine neue Kollaboration hinzu</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=collaborations/index" class="btn">Abbrechen</a>
        <button class="btn" onclick="CollabForm.saveAsDraft()"><span class="material-symbols-rounded">draft</span> Als
            Entwurf</button>
        <button class="btn btn-primary" onclick="CollabForm.save()"><span
                class="material-symbols-rounded">publish</span> Veröffentlichen</button>
    </div>
</div>

<!-- Step Navigation -->
<div class="step-nav" id="stepNav">
    <div class="step active" data-step="1"><span class="step-number">1</span><span class="step-label">Grunddaten</span>
    </div>
    <div class="step" data-step="2"><span class="step-number">2</span><span class="step-label">Medien</span></div>
    <div class="step" data-step="3"><span class="step-number">3</span><span class="step-label">SEO</span></div>
</div>

<form id="collabForm" class="product-form">
    <input type="hidden" name="id" value="">

    <!-- Step 1: Grunddaten -->
    <div class="step-content active" data-step-content="1">
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Grunddaten</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Name <span class="required">*</span></label>
                        <input type="text" class="form-input" name="name" id="collabName"
                            placeholder="z.B. Nike x BY Production" required>
                        <p class="form-error" id="errorName"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL-Slug</label>
                        <input type="text" class="form-input" name="slug" id="collabSlug"
                            placeholder="wird automatisch generiert">
                        <p class="form-hint">Leer lassen für automatische Generierung</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kurzbeschreibung</label>
                        <textarea class="form-textarea" name="short_description" rows="2"
                            placeholder="Kurze Beschreibung für Übersichten..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Beschreibung</label>
                        <textarea class="form-textarea" name="description" rows="6"
                            placeholder="Ausführliche Beschreibung der Kollaboration..."></textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Einstellungen</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Video URL</label>
                        <input type="url" class="form-input" name="video_url" id="videoUrl"
                            placeholder="https://youtube.com/watch?v=...">
                        <p class="form-hint">YouTube oder Vimeo Link</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sortierung</label>
                        <input type="number" class="form-input" name="sort_order" value="0" min="0"
                            style="width:120px;">
                        <p class="form-hint">Niedrigere Zahlen werden zuerst angezeigt</p>
                    </div>
                    <div class="form-group">
                        <div class="toggle-group">
                            <label class="toggle-label"><input type="checkbox" name="is_featured" value="1"> Als
                                Featured markieren</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Medien -->
    <div class="step-content" data-step-content="2">
        <div class="card">
            <div class="card-header">
                <h3>Bilder</h3>
            </div>
            <div class="card-body">
                <div class="image-upload-zone" id="imageUploadZone">
                    <div class="upload-placeholder">
                        <span class="material-symbols-rounded"
                            style="font-size:48px;color:var(--text-muted);">add_photo_alternate</span>
                        <p>Bilder hier ablegen oder klicken zum Hochladen</p>
                        <small style="color:var(--text-muted);">PNG, JPG oder WEBP · Max. 5MB pro Bild</small>
                        <input type="file" id="imageInput" accept="image/*" multiple style="display:none;">
                        <button type="button" class="btn btn-primary" style="margin-top:16px;"
                            onclick="document.getElementById('imageInput').click()">
                            <span class="material-symbols-rounded">upload</span> Bilder auswählen
                        </button>
                    </div>
                </div>
                <p class="form-hint" style="margin-top:8px;">Bilder können per Drag & Drop umsortiert werden.</p>
                <div class="image-gallery-preview" id="imageGallery" style="display:none;margin-top:24px;"></div>
            </div>
        </div>

        <!-- Video Preview -->
        <div class="card" id="videoPreviewCard" style="margin-top:24px; display:none;">
            <div class="card-header">
                <h3>Video-Vorschau</h3>
            </div>
            <div class="card-body">
                <div id="videoPreview" style="text-align:center;color:var(--text-muted);">
                    <span class="material-symbols-rounded" style="font-size:48px;">videocam_off</span>
                    <p>Fügen Sie im vorherigen Schritt eine Video-URL hinzu</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: SEO -->
    <div class="step-content" data-step-content="3">
        <div class="card">
            <div class="card-header">
                <h3>Suchmaschinenoptimierung</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Meta-Titel</label>
                    <input type="text" class="form-input" name="meta_title" id="metaTitle"
                        placeholder="Titel für Suchergebnisse (max. 60 Zeichen)" maxlength="60">
                    <small style="color:var(--text-muted);"><span id="metaTitleCount">0</span>/60 Zeichen</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Meta-Beschreibung</label>
                    <textarea class="form-textarea" name="meta_description" id="metaDescription" rows="3"
                        placeholder="Beschreibung für Suchergebnisse (max. 160 Zeichen)" maxlength="160"></textarea>
                    <small style="color:var(--text-muted);"><span id="metaDescCount">0</span>/160 Zeichen</small>
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:24px;">
            <div class="card-header">
                <h3>Suchvorschau</h3>
            </div>
            <div class="card-body">
                <div class="seo-preview">
                    <div class="seo-preview-url">meinshop.de › kollaborationen › <span id="previewSlug">name</span>
                    </div>
                    <div class="seo-preview-title" id="previewTitle">Kollaborationsname - Mein Online Shop</div>
                    <div class="seo-preview-desc" id="previewDesc">Meta-Beschreibung wird hier angezeigt...</div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Step Navigation Buttons -->
<div class="step-buttons">
    <button class="btn" id="prevBtn" onclick="CollabForm.prevStep()" style="display:none;">
        <span class="material-symbols-rounded">arrow_back</span> Zurück
    </button>
    <button class="btn btn-primary" id="nextBtn" onclick="CollabForm.nextStep()">
        Weiter <span class="material-symbols-rounded">arrow_forward</span>
    </button>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .breadcrumb {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .breadcrumb a {
        color: var(--accent);
    }

    .required {
        color: var(--error);
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

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 24px;
    }

    .step-nav {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        overflow-x: auto;
        padding: 4px;
    }

    .step {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: var(--bg-secondary);
        border-radius: var(--radius-md);
        cursor: pointer;
        opacity: 0.5;
        transition: all 0.2s;
    }

    .step.active {
        opacity: 1;
        background: var(--accent);
        color: white;
    }

    .step.completed {
        opacity: 1;
    }

    .step.completed .step-number {
        background: var(--success);
    }

    .step-number {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--bg-lighter);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
    }

    .step.active .step-number {
        background: rgba(255, 255, 255, 0.2);
    }

    .step-label {
        font-size: 13px;
        white-space: nowrap;
    }

    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
    }

    .step-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 24px;
    }

    .image-upload-zone {
        border: 2px dashed var(--border);
        border-radius: var(--radius-md);
        padding: 40px 20px;
        text-align: center;
        transition: all 0.2s;
    }

    .image-upload-zone:hover,
    .image-upload-zone.dragover {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.05);
    }

    .image-gallery-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
    }

    .image-gallery-preview .gallery-item {
        position: relative;
        border-radius: var(--radius-sm);
        overflow: hidden;
        aspect-ratio: 1;
    }

    .image-gallery-preview .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-gallery-preview .gallery-item .remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .seo-preview {
        padding: 16px;
        background: var(--bg-lighter);
        border-radius: var(--radius-md);
    }

    .seo-preview-url {
        font-size: 12px;
        color: var(--success);
        margin-bottom: 4px;
    }

    .seo-preview-title {
        font-size: 18px;
        color: var(--accent);
        margin-bottom: 4px;
        font-weight: 500;
    }

    .seo-preview-desc {
        font-size: 13px;
        color: var(--text-muted);
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
    const CollabForm = {
        apiBase: 'api/collaborations.php',
        shopId: 1,
        currentStep: 1,
        totalSteps: 3,
        uploadedFiles: [],
        existingImages: [],
        deleteImageIds: [],

        init() {
            this.setupImageUpload();
            this.setupSeoListeners();
            this.setupStepNav();
            this.updateVideoPreview();
        },

        // ========== STEP NAVIGATION ==========
        setupStepNav() {
            document.querySelectorAll('.step').forEach(step => {
                step.addEventListener('click', () => {
                    const s = parseInt(step.dataset.step);
                    if (s) this.goToStep(s);
                });
            });
        },

        goToStep(step) {
            if (step < 1 || step > this.totalSteps) return;
            // Mark current as completed
            if (step > this.currentStep) {
                document.querySelector(`.step[data-step="${this.currentStep}"]`).classList.add('completed');
            }
            this.currentStep = step;
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.querySelector(`.step[data-step="${step}"]`).classList.add('active');
            document.querySelectorAll('.step-content').forEach(c => c.classList.remove('active'));
            document.querySelector(`.step-content[data-step-content="${step}"]`).classList.add('active');
            document.getElementById('prevBtn').style.display = step === 1 ? 'none' : '';
            const nextBtn = document.getElementById('nextBtn');
            nextBtn.innerHTML = step === this.totalSteps
                ? 'Veröffentlichen <span class="material-symbols-rounded">publish</span>'
                : 'Weiter <span class="material-symbols-rounded">arrow_forward</span>';
            if (step === 2) this.updateVideoPreview();
        },

        nextStep() {
            if (this.currentStep < this.totalSteps) {
                this.goToStep(this.currentStep + 1);
            } else {
                this.save();
            }
        },

        prevStep() {
            if (this.currentStep > 1) this.goToStep(this.currentStep - 1);
        },

        // ========== IMAGE UPLOAD ==========
        setupImageUpload() {
            const input = document.getElementById('imageInput');
            const zone = document.getElementById('imageUploadZone');
            if (!input || !zone) return;

            input.addEventListener('change', (e) => this.handleFiles(e.target.files));

            zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('dragover'); });
            zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
            zone.addEventListener('drop', (e) => {
                e.preventDefault(); zone.classList.remove('dragover');
                this.handleFiles(e.dataTransfer.files);
            });
        },

        handleFiles(files) {
            for (const file of files) {
                if (!file.type.startsWith('image/')) continue;
                if (file.size > 5 * 1024 * 1024) { this.showToast('Datei zu groß (max. 5MB)', 'error'); continue; }
                this.uploadedFiles.push(file);
            }
            this.renderGallery();
        },

        renderGallery() {
            const gallery = document.getElementById('imageGallery');
            if (this.uploadedFiles.length === 0 && this.existingImages.length === 0) {
                gallery.style.display = 'none';
                return;
            }
            gallery.style.display = 'grid';
            gallery.innerHTML = '';

            // Existing images
            this.existingImages.forEach((img, i) => {
                const div = document.createElement('div');
                div.className = 'gallery-item';
                div.innerHTML = `<img src="${img.image_url}" alt="${img.alt_text || ''}">
                <button type="button" class="remove-btn" onclick="CollabForm.removeExistingImage(${i})">
                    <span class="material-symbols-rounded" style="font-size:16px;">close</span>
                </button>`;
                gallery.appendChild(div);
            });

            // New uploads
            this.uploadedFiles.forEach((file, i) => {
                const div = document.createElement('div');
                div.className = 'gallery-item';
                const url = URL.createObjectURL(file);
                div.innerHTML = `<img src="${url}" alt="${file.name}">
                <button type="button" class="remove-btn" onclick="CollabForm.removeFile(${i})">
                    <span class="material-symbols-rounded" style="font-size:16px;">close</span>
                </button>`;
                gallery.appendChild(div);
            });
        },

        removeFile(index) {
            this.uploadedFiles.splice(index, 1);
            this.renderGallery();
        },

        removeExistingImage(index) {
            const img = this.existingImages.splice(index, 1)[0];
            if (img && img.id) this.deleteImageIds.push(img.id);
            this.renderGallery();
        },

        // ========== VIDEO PREVIEW ==========
        updateVideoPreview() {
            const url = document.getElementById('videoUrl')?.value || '';
            const preview = document.getElementById('videoPreview');
            if (!preview) return;

            if (!url) {
                preview.innerHTML = '<span class="material-symbols-rounded" style="font-size:48px;">videocam_off</span><p>Fügen Sie im vorherigen Schritt eine Video-URL hinzu</p>';
                return;
            }

            // Extract YouTube/Vimeo embed
            let embedUrl = '';
            const ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
            const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
            if (ytMatch) embedUrl = `https://www.youtube.com/embed/${ytMatch[1]}`;
            else if (vimeoMatch) embedUrl = `https://player.vimeo.com/video/${vimeoMatch[1]}`;

            if (embedUrl) {
                preview.innerHTML = `<iframe src="${embedUrl}" width="100%" height="360" frameborder="0" allowfullscreen style="border-radius:var(--radius-md);"></iframe>`;
            } else {
                preview.innerHTML = `<p style="color:var(--text-muted);">Video URL konnte nicht erkannt werden</p>`;
            }
        },

        // ========== SEO ==========
        setupSeoListeners() {
            const name = document.getElementById('collabName');
            const metaTitle = document.getElementById('metaTitle');
            const metaDesc = document.getElementById('metaDescription');

            if (name) name.addEventListener('input', () => {
                document.getElementById('previewTitle').textContent = name.value || 'Kollaborationsname - Mein Online Shop';
                // Auto-generate slug preview
                const slug = name.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                document.getElementById('previewSlug').textContent = slug || 'name';
            });

            if (metaTitle) metaTitle.addEventListener('input', () => {
                document.getElementById('metaTitleCount').textContent = metaTitle.value.length;
                if (metaTitle.value) document.getElementById('previewTitle').textContent = metaTitle.value;
            });

            if (metaDesc) metaDesc.addEventListener('input', () => {
                document.getElementById('metaDescCount').textContent = metaDesc.value.length;
                if (metaDesc.value) document.getElementById('previewDesc').textContent = metaDesc.value;
            });

            const videoUrl = document.getElementById('videoUrl');
            if (videoUrl) videoUrl.addEventListener('change', () => this.updateVideoPreview());
        },

        // ========== SAVE ==========
        async save(status = 'active') {
            const form = document.getElementById('collabForm');
            const name = form.querySelector('[name="name"]').value.trim();
            if (!name) {
                this.showError('errorName', 'Name ist erforderlich');
                this.goToStep(1);
                return;
            }

            const fd = new FormData(form);
            fd.set('action', 'save_collaboration');
            fd.set('shop_id', this.shopId);
            fd.set('status', status);

            // Add images
            this.uploadedFiles.forEach(f => fd.append('images[]', f));
            if (this.deleteImageIds.length > 0) {
                fd.set('delete_image_ids', JSON.stringify(this.deleteImageIds));
            }
            // Image order for existing
            const order = this.existingImages.map(img => img.id);
            if (order.length > 0) {
                fd.set('image_order', JSON.stringify(order));
            }

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    this.showToast(data.message, 'success');
                    setTimeout(() => window.location.href = '?page=collaborations/index', 1000);
                } else {
                    const errs = data.errors || [data.error || 'Unbekannter Fehler'];
                    this.showToast(errs.join(', '), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        saveAsDraft() {
            this.save('draft');
        },

        // ========== HELPERS ==========
        showError(id, msg) {
            const el = document.getElementById(id);
            if (el) { el.textContent = msg; el.classList.add('show'); }
        },

        showToast(msg, type = 'success') {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = `toast ${type} show`;
            setTimeout(() => t.className = 'toast', 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', () => CollabForm.init());
</script>