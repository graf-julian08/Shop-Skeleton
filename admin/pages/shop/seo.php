<?php /** Shop - SEO - 100% funktionsfähig */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>SEO</h1>
        <p class="page-subtitle">Suchmaschinenoptimierung für Ihren Shop</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary" id="btnSaveAll" onclick="SEO.saveAll()">
            <span class="material-symbols-rounded">save</span> Speichern
        </button>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="metatags">Meta-Tags</button>
    <button class="tab" data-tab="sitemap">Sitemap</button>
    <button class="tab" data-tab="redirects">Redirects</button>
    <button class="tab" data-tab="robots">Robots.txt</button>
</div>

<!-- Tab: Meta-Tags -->
<div data-tab-content="metatags">
    <div class="card">
        <div class="card-header">
            <h3>Standard Meta-Tags</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Meta-Titel (Standard)</label>
                <input type="text" class="form-input" id="metaTitle" maxlength="60" oninput="SEO.updateCharCount('metaTitle', 60)">
                <p class="form-hint">Zeichen: <span id="metaTitleCount">0</span>/60</p>
            </div>
            <div class="form-group">
                <label class="form-label">Meta-Beschreibung (Standard)</label>
                <textarea class="form-textarea" id="metaDescription" maxlength="160" style="min-height:80px;" oninput="SEO.updateCharCount('metaDescription', 160)"></textarea>
                <p class="form-hint">Zeichen: <span id="metaDescriptionCount">0</span>/160</p>
            </div>
            <div class="form-group">
                <label class="form-label">Meta-Keywords</label>
                <input type="text" class="form-input" id="metaKeywords" placeholder="Kommagetrennt: keyword1, keyword2, keyword3">
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3>Strukturierte Daten (Schema.org)</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="schemaOrganization">
                    <span>Organization Schema aktivieren</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="schemaProduct">
                    <span>Product Schema aktivieren</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="schemaBreadcrumb">
                    <span>Breadcrumb Schema aktivieren</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="schemaFaq">
                    <span>FAQ Schema aktivieren</span>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Sitemap -->
<div data-tab-content="sitemap" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>Sitemap-Status</h3>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-label">Letzte Generierung</div>
                    <div class="stat-card-value" id="sitemapLastGenerated">-</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">URLs enthalten</div>
                    <div class="stat-card-value" id="sitemapUrlCount">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-label">Status</div>
                    <div class="stat-card-value" id="sitemapStatus" style="color:var(--text-muted);">Nicht vorhanden</div>
                </div>
            </div>
            <div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap;">
                <button class="btn btn-primary" onclick="SEO.generateSitemap()">
                    <span class="material-symbols-rounded">refresh</span> Neu generieren
                </button>
                <button class="btn" onclick="SEO.downloadSitemap()" id="btnDownloadSitemap">
                    <span class="material-symbols-rounded">download</span> Herunterladen
                </button>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Sitemap-Einstellungen</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="sitemapAutoGenerate">
                    <span>Automatische Generierung aktiviert</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="sitemapIncludeProducts">
                    <span>Produkte einschließen</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="sitemapIncludeCategories">
                    <span>Kategorien einschließen</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="sitemapIncludeCms">
                    <span>CMS-Seiten einschließen</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="sitemapIncludeBlog">
                    <span>Blog-Beiträge einschließen</span>
                </label>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" onclick="SEO.saveSitemapSettings()">
                <span class="material-symbols-rounded">save</span> Einstellungen speichern
            </button>
        </div>
    </div>
</div>

<!-- Tab: Redirects -->
<div data-tab-content="redirects" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>URL-Weiterleitungen</h3>
            <button class="btn btn-sm btn-primary" onclick="SEO.showRedirectModal()">
                <span class="material-symbols-rounded">add</span> Redirect hinzufügen
            </button>
        </div>
        <div class="card-body">
            <table class="table" id="redirectsTable">
                <thead>
                    <tr>
                        <th>Quelle</th>
                        <th>Ziel</th>
                        <th>Typ</th>
                        <th>Status</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody id="redirectsTableBody">
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--text-muted);">
                            <span class="material-symbols-rounded">hourglass_empty</span> Lade Redirects...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Robots.txt -->
<div data-tab-content="robots" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>Robots.txt Editor</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Robots.txt Inhalt</label>
                <textarea class="form-textarea" id="robotsTxt" style="min-height:250px;font-family:monospace;font-size:13px;"></textarea>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn" onclick="SEO.confirmResetRobots()">
                <span class="material-symbols-rounded">restart_alt</span> Standard wiederherstellen
            </button>
            <button class="btn btn-primary" onclick="SEO.saveRobots()">
                <span class="material-symbols-rounded">save</span> Speichern
            </button>
        </div>
    </div>
</div>

<!-- Redirect Modal -->
<div class="modal-backdrop" id="redirectModal" style="display:none;">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <h3 id="redirectModalTitle">Redirect hinzufügen</h3>
            <button class="modal-close" onclick="SEO.closeRedirectModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="redirectId">
            <div class="form-group">
                <label class="form-label">Quell-URL *</label>
                <input type="text" class="form-input" id="redirectSource" placeholder="/alte-seite">
            </div>
            <div class="form-group">
                <label class="form-label">Ziel-URL *</label>
                <input type="text" class="form-input" id="redirectTarget" placeholder="/neue-seite">
            </div>
            <div class="form-group">
                <label class="form-label">Redirect-Typ</label>
                <select class="form-select" id="redirectType">
                    <option value="301">301 - Permanent (empfohlen)</option>
                    <option value="302">302 - Temporär</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" id="redirectActive" checked>
                    <span>Redirect aktiv</span>
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="SEO.closeRedirectModal()">Abbrechen</button>
            <button class="btn btn-primary" onclick="SEO.saveRedirect()">
                <span class="material-symbols-rounded">save</span> Speichern
            </button>
        </div>
    </div>
</div>

<!-- Confirm Modal (für Löschen etc.) -->
<div class="modal-backdrop" id="confirmModal" style="display:none;">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <h3 id="confirmModalTitle">Bestätigung</h3>
            <button class="modal-close" onclick="SEO.closeConfirmModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body">
            <div style="text-align:center;padding:20px 0;">
                <span class="material-symbols-rounded" id="confirmModalIcon" style="font-size:48px;color:var(--warning);margin-bottom:16px;display:block;">warning</span>
                <p id="confirmModalMessage" style="font-size:16px;margin:0;">Sind Sie sicher?</p>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:center;gap:12px;">
            <button class="btn" onclick="SEO.closeConfirmModal()">Abbrechen</button>
            <button class="btn btn-danger" id="confirmModalBtn" onclick="SEO.executeConfirm()">
                <span class="material-symbols-rounded">delete</span> Löschen
            </button>
        </div>
    </div>
</div>

<script>
const SEO = {
    shopId: 1,
    apiBase: 'api/seo.php',
    confirmCallback: null,
    redirectsData: [],
    
    async init() {
        await this.loadSettings();
        await this.loadSitemapStatus();
        await this.loadRedirects();
        await this.loadRobots();
        this.initTabs();
    },
    
    initTabs() {
        // Restore saved tab from localStorage
        const savedTab = localStorage.getItem('seo_active_tab');
        if (savedTab) {
            const tabBtn = document.querySelector(`.tab[data-tab="${savedTab}"]`);
            if (tabBtn) {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('[data-tab-content]').forEach(c => c.style.display = 'none');
                tabBtn.classList.add('active');
                document.querySelector(`[data-tab-content="${savedTab}"]`).style.display = 'block';
            }
        }
        
        // Add click handlers with localStorage save
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('[data-tab-content]').forEach(c => c.style.display = 'none');
                tab.classList.add('active');
                document.querySelector(`[data-tab-content="${tab.dataset.tab}"]`).style.display = 'block';
                // Save active tab to localStorage
                localStorage.setItem('seo_active_tab', tab.dataset.tab);
            });
        });
    },
    
    // ===== SETTINGS =====
    async loadSettings() {
        try {
            const res = await fetch(`${this.apiBase}?action=get_settings&shop_id=${this.shopId}`);
            const data = await res.json();
            
            if (data.success && data.settings) {
                const s = data.settings;
                document.getElementById('metaTitle').value = s.default_meta_title || '';
                document.getElementById('metaDescription').value = s.default_meta_description || '';
                document.getElementById('metaKeywords').value = s.meta_keywords || '';
                document.getElementById('schemaOrganization').checked = parseInt(s.organization_schema) === 1;
                document.getElementById('schemaProduct').checked = parseInt(s.product_schema) === 1;
                document.getElementById('schemaBreadcrumb').checked = parseInt(s.breadcrumb_schema) === 1;
                document.getElementById('schemaFaq').checked = parseInt(s.faq_schema) === 1;
                
                this.updateCharCount('metaTitle', 60);
                this.updateCharCount('metaDescription', 160);
            }
        } catch (e) {
            console.error('Error loading settings:', e);
        }
    },
    
    async saveSettings() {
        const formData = new FormData();
        formData.append('action', 'save_settings');
        formData.append('shop_id', this.shopId);
        formData.append('default_meta_title', document.getElementById('metaTitle').value);
        formData.append('default_meta_description', document.getElementById('metaDescription').value);
        formData.append('meta_keywords', document.getElementById('metaKeywords').value);
        formData.append('organization_schema', document.getElementById('schemaOrganization').checked ? 1 : 0);
        formData.append('product_schema', document.getElementById('schemaProduct').checked ? 1 : 0);
        formData.append('breadcrumb_schema', document.getElementById('schemaBreadcrumb').checked ? 1 : 0);
        formData.append('faq_schema', document.getElementById('schemaFaq').checked ? 1 : 0);
        
        const res = await fetch(this.apiBase, { method: 'POST', body: formData });
        return await res.json();
    },
    
    async saveAll() {
        const btn = document.getElementById('btnSaveAll');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-rounded spinning">sync</span> Speichere...';
        
        try {
            const result = await this.saveSettings();
            if (result.success) {
                this.showToast('SEO-Einstellungen gespeichert!', 'success');
            } else {
                this.showToast('Fehler: ' + (result.error || 'Unbekannt'), 'error');
            }
        } catch (e) {
            this.showToast('Fehler: ' + e.message, 'error');
        }
        
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-rounded">save</span> Speichern';
    },
    
    updateCharCount(fieldId, max) {
        const field = document.getElementById(fieldId);
        const count = field.value.length;
        const countEl = document.getElementById(fieldId + 'Count');
        countEl.textContent = count;
        countEl.style.color = count > max ? 'var(--danger)' : (count > max * 0.9 ? 'var(--warning)' : 'inherit');
    },
    
    // ===== SITEMAP =====
    async loadSitemapStatus() {
        try {
            const res = await fetch(`${this.apiBase}?action=get_sitemap_status&shop_id=${this.shopId}`);
            const data = await res.json();
            
            if (data.success) {
                document.getElementById('sitemapLastGenerated').textContent = data.last_generated || 'Nie';
                document.getElementById('sitemapUrlCount').textContent = data.url_count;
                
                const statusEl = document.getElementById('sitemapStatus');
                if (data.exists) {
                    statusEl.textContent = 'Aktuell';
                    statusEl.style.color = 'var(--success)';
                } else {
                    statusEl.textContent = 'Nicht vorhanden';
                    statusEl.style.color = 'var(--text-muted)';
                }
                
                if (data.settings) {
                    document.getElementById('sitemapAutoGenerate').checked = parseInt(data.settings.sitemap_auto_generate) === 1;
                    document.getElementById('sitemapIncludeProducts').checked = parseInt(data.settings.include_products) === 1;
                    document.getElementById('sitemapIncludeCategories').checked = parseInt(data.settings.include_categories) === 1;
                    document.getElementById('sitemapIncludeCms').checked = parseInt(data.settings.include_cms_pages) === 1;
                    document.getElementById('sitemapIncludeBlog').checked = parseInt(data.settings.include_blog) === 1;
                }
            }
        } catch (e) {
            console.error('Error loading sitemap status:', e);
        }
    },
    
    async generateSitemap() {
        this.showToast('Generiere Sitemap...', 'info');
        
        try {
            const formData = new FormData();
            formData.append('action', 'generate_sitemap');
            formData.append('shop_id', this.shopId);
            
            const res = await fetch(this.apiBase, { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.success) {
                this.showToast(`Sitemap generiert mit ${data.url_count} URLs!`, 'success');
                await this.loadSitemapStatus();
            } else {
                this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
            }
        } catch (e) {
            this.showToast('Fehler: ' + e.message, 'error');
        }
    },
    
    downloadSitemap() {
        window.open(`${this.apiBase}?action=download_sitemap&shop_id=${this.shopId}`, '_blank');
    },
    
    async saveSitemapSettings() {
        const formData = new FormData();
        formData.append('action', 'save_sitemap_settings');
        formData.append('shop_id', this.shopId);
        formData.append('sitemap_auto_generate', document.getElementById('sitemapAutoGenerate').checked ? 1 : 0);
        formData.append('include_products', document.getElementById('sitemapIncludeProducts').checked ? 1 : 0);
        formData.append('include_categories', document.getElementById('sitemapIncludeCategories').checked ? 1 : 0);
        formData.append('include_cms_pages', document.getElementById('sitemapIncludeCms').checked ? 1 : 0);
        formData.append('include_blog', document.getElementById('sitemapIncludeBlog').checked ? 1 : 0);
        
        try {
            const res = await fetch(this.apiBase, { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.success) {
                this.showToast('Sitemap-Einstellungen gespeichert!', 'success');
            } else {
                this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
            }
        } catch (e) {
            this.showToast('Fehler: ' + e.message, 'error');
        }
    },
    
    // ===== REDIRECTS =====
    async loadRedirects() {
        try {
            const res = await fetch(`${this.apiBase}?action=get_redirects&shop_id=${this.shopId}`);
            const data = await res.json();
            
            this.redirectsData = data.redirects || [];
            const tbody = document.getElementById('redirectsTableBody');
            
            if (data.success && this.redirectsData.length > 0) {
                tbody.innerHTML = this.redirectsData.map(r => `
                    <tr data-id="${r.id}">
                        <td><code>${this.escapeHtml(r.source_url)}</code></td>
                        <td><code>${this.escapeHtml(r.target_url)}</code></td>
                        <td><span class="badge ${r.redirect_type === '301' ? 'badge-primary' : 'badge-warning'}">${r.redirect_type}</span></td>
                        <td>
                            <span class="status-badge ${parseInt(r.is_active) === 1 ? 'status-active' : 'status-inactive'}" onclick="SEO.toggleRedirect(${r.id}, ${parseInt(r.is_active) === 1 ? 0 : 1})">
                                ${parseInt(r.is_active) === 1 ? 'Aktiv' : 'Inaktiv'}
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <button class="btn btn-sm btn-icon" onclick="SEO.editRedirect(${r.id})" title="Bearbeiten">
                                    <span class="material-symbols-rounded">edit</span>
                                </button>
                                <button class="btn btn-sm btn-icon btn-danger-ghost" onclick="SEO.confirmDeleteRedirect(${r.id})" title="Löschen">
                                    <span class="material-symbols-rounded">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--text-muted);padding:40px;">
                            <span class="material-symbols-rounded" style="font-size:48px;display:block;margin-bottom:12px;">link_off</span>
                            Keine Redirects vorhanden.
                        </td>
                    </tr>
                `;
            }
        } catch (e) {
            console.error('Error loading redirects:', e);
        }
    },
    
    showRedirectModal(id = null) {
        document.getElementById('redirectId').value = id || '';
        document.getElementById('redirectModalTitle').textContent = id ? 'Redirect bearbeiten' : 'Redirect hinzufügen';
        document.getElementById('redirectSource').value = '';
        document.getElementById('redirectTarget').value = '';
        document.getElementById('redirectType').value = '301';
        document.getElementById('redirectActive').checked = true;
        document.getElementById('redirectModal').style.display = 'flex';
    },
    
    closeRedirectModal() {
        document.getElementById('redirectModal').style.display = 'none';
    },
    
    editRedirect(id) {
        const r = this.redirectsData.find(x => x.id == id);
        if (r) {
            document.getElementById('redirectId').value = r.id;
            document.getElementById('redirectModalTitle').textContent = 'Redirect bearbeiten';
            document.getElementById('redirectSource').value = r.source_url;
            document.getElementById('redirectTarget').value = r.target_url;
            document.getElementById('redirectType').value = r.redirect_type;
            document.getElementById('redirectActive').checked = parseInt(r.is_active) === 1;
            document.getElementById('redirectModal').style.display = 'flex';
        }
    },
    
    async saveRedirect() {
        const id = document.getElementById('redirectId').value;
        const source = document.getElementById('redirectSource').value.trim();
        const target = document.getElementById('redirectTarget').value.trim();
        
        if (!source || !target) {
            this.showToast('Bitte Quelle und Ziel angeben!', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'save_redirect');
        formData.append('shop_id', this.shopId);
        if (id) formData.append('id', id);
        formData.append('source_url', source);
        formData.append('target_url', target);
        formData.append('redirect_type', document.getElementById('redirectType').value);
        formData.append('is_active', document.getElementById('redirectActive').checked ? 1 : 0);
        
        try {
            const res = await fetch(this.apiBase, { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.success) {
                this.showToast('Redirect gespeichert!', 'success');
                this.closeRedirectModal();
                await this.loadRedirects();
            } else {
                this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
            }
        } catch (e) {
            this.showToast('Fehler: ' + e.message, 'error');
        }
    },
    
    async toggleRedirect(id, active) {
        const formData = new FormData();
        formData.append('action', 'toggle_redirect');
        formData.append('shop_id', this.shopId);
        formData.append('id', id);
        formData.append('is_active', active);
        
        try {
            await fetch(this.apiBase, { method: 'POST', body: formData });
            await this.loadRedirects();
            this.showToast(active ? 'Redirect aktiviert' : 'Redirect deaktiviert', 'success');
        } catch (e) {
            this.showToast('Fehler: ' + e.message, 'error');
        }
    },
    
    confirmDeleteRedirect(id) {
        this.showConfirmModal(
            'Redirect löschen',
            'Möchten Sie diesen Redirect wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
            'delete',
            'Löschen',
            () => this.deleteRedirect(id)
        );
    },
    
    async deleteRedirect(id) {
        const formData = new FormData();
        formData.append('action', 'delete_redirect');
        formData.append('shop_id', this.shopId);
        formData.append('id', id);
        
        try {
            const res = await fetch(this.apiBase, { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.success) {
                this.showToast('Redirect gelöscht!', 'success');
                await this.loadRedirects();
            } else {
                this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
            }
        } catch (e) {
            this.showToast('Fehler: ' + e.message, 'error');
        }
    },
    
    // ===== ROBOTS.TXT =====
    async loadRobots() {
        try {
            const res = await fetch(`${this.apiBase}?action=get_robots&shop_id=${this.shopId}`);
            const data = await res.json();
            
            if (data.success) {
                document.getElementById('robotsTxt').value = data.robots_txt || '';
            }
        } catch (e) {
            console.error('Error loading robots.txt:', e);
        }
    },
    
    async saveRobots() {
        const content = document.getElementById('robotsTxt').value;
        
        const formData = new FormData();
        formData.append('action', 'save_robots');
        formData.append('shop_id', this.shopId);
        formData.append('robots_txt', content);
        
        try {
            const res = await fetch(this.apiBase, { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.success) {
                this.showToast('Robots.txt gespeichert!', 'success');
            } else {
                this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
            }
        } catch (e) {
            this.showToast('Fehler: ' + e.message, 'error');
        }
    },
    
    confirmResetRobots() {
        this.showConfirmModal(
            'Standard wiederherstellen',
            'Möchten Sie die robots.txt auf den Standardinhalt zurücksetzen? Alle Änderungen gehen verloren.',
            'restart_alt',
            'Zurücksetzen',
            () => this.resetRobots()
        );
    },
    
    async resetRobots() {
        const formData = new FormData();
        formData.append('action', 'reset_robots');
        formData.append('shop_id', this.shopId);
        
        try {
            const res = await fetch(this.apiBase, { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.success) {
                document.getElementById('robotsTxt').value = data.robots_txt;
                this.showToast('Robots.txt zurückgesetzt!', 'success');
            } else {
                this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
            }
        } catch (e) {
            this.showToast('Fehler: ' + e.message, 'error');
        }
    },
    
    // ===== CONFIRM MODAL =====
    showConfirmModal(title, message, icon, btnText, callback) {
        document.getElementById('confirmModalTitle').textContent = title;
        document.getElementById('confirmModalMessage').textContent = message;
        document.getElementById('confirmModalIcon').textContent = icon;
        document.getElementById('confirmModalBtn').innerHTML = `<span class="material-symbols-rounded">${icon}</span> ${btnText}`;
        this.confirmCallback = callback;
        document.getElementById('confirmModal').style.display = 'flex';
    },
    
    closeConfirmModal() {
        document.getElementById('confirmModal').style.display = 'none';
        this.confirmCallback = null;
    },
    
    executeConfirm() {
        if (this.confirmCallback) {
            this.confirmCallback();
        }
        this.closeConfirmModal();
    },
    
    // ===== HELPERS =====
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
    
    showToast(message, type = 'info') {
        document.querySelectorAll('.seo-toast').forEach(t => t.remove());
        
        const toast = document.createElement('div');
        toast.className = `seo-toast seo-toast-${type}`;
        toast.innerHTML = `
            <span class="material-symbols-rounded">${type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info'}</span>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('seo-toast-hide');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
};

// Initialize
document.addEventListener('DOMContentLoaded', () => SEO.init());
</script>

<style>
/* ===== MODAL STYLES - Zentriert mit hohem Z-Index ===== */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: modalFadeIn 0.2s ease;
}

.modal {
    background: var(--bg-secondary, #1a1a1a);
    border: 1px solid var(--border-color, rgba(255,255,255,0.1));
    border-radius: 16px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255,255,255,0.05);
    animation: modalSlideIn 0.25s ease;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.1));
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

.modal-close {
    background: transparent;
    border: none;
    color: var(--text-muted, #888);
    cursor: pointer;
    padding: 6px;
    border-radius: 8px;
    display: flex;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: rgba(255,255,255,0.1);
    color: var(--text-primary, #fff);
}

.modal-body {
    padding: 24px;
    overflow-y: auto;
    max-height: 60vh;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid var(--border-color, rgba(255,255,255,0.1));
    background: rgba(0,0,0,0.2);
}

@keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes modalSlideIn {
    from { 
        opacity: 0;
        transform: scale(0.95) translateY(-20px);
    }
    to { 
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Status Badge für Redirects */
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.status-active {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}
.status-active:hover {
    background: rgba(16, 185, 129, 0.25);
}
.status-inactive {
    background: rgba(107, 114, 128, 0.15);
    color: #6b7280;
}
.status-inactive:hover {
    background: rgba(107, 114, 128, 0.25);
}

/* Badge Styles */
.badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}
.badge-primary {
    background: var(--primary);
    color: white;
}
.badge-warning {
    background: var(--warning);
    color: #1a1a1a;
}

/* Table Actions */
.table-actions {
    display: flex;
    gap: 6px;
}
.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-icon .material-symbols-rounded {
    font-size: 18px;
}
.btn-danger-ghost {
    color: var(--danger);
    background: transparent;
}
.btn-danger-ghost:hover {
    background: rgba(239, 68, 68, 0.1);
}

/* Toast Styles */
.seo-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    padding: 14px 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    z-index: 10000;
    animation: seoSlideIn 0.3s ease;
    font-weight: 500;
}
.seo-toast-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}
.seo-toast-error {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}
.seo-toast-info {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}
.seo-toast-hide {
    animation: seoSlideOut 0.3s ease forwards;
}

@keyframes seoSlideIn {
    from { transform: translateX(120%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes seoSlideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(120%); opacity: 0; }
}
@keyframes spinning {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.spinning { animation: spinning 1s linear infinite; }

/* Modal Danger Button */
.btn-danger {
    background: var(--danger);
    color: white;
    border: none;
}
.btn-danger:hover {
    background: #dc2626;
}
</style>