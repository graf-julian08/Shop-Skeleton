<?php
/** Katalog - Produktdetail */
$productId = (int) ($_GET['id'] ?? 0);
?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/products">Produkte</a> <span>›</span> <span
                id="breadcrumbName">Produkt</span></nav>
        <h1 id="pageTitle">Produkt laden...</h1>
        <p class="page-subtitle" id="pageSubtitle">SKU: ---</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/product_edit&id=<?= $productId ?>" class="btn" id="editBtn"><span
                class="material-symbols-rounded">edit</span> Bearbeiten</a>
        <button class="btn btn-danger-ghost" onclick="ProductDetail.deleteProduct()"><span
                class="material-symbols-rounded">delete</span> Löschen</button>
    </div>
</div>

<!-- Status Bar -->
<div class="status-bar" id="statusBar" style="display:none;">
    <div class="status-info">
        <span class="status-label">Status:</span>
        <span class="badge" id="statusBadge">-</span>
    </div>
    <div class="status-actions">
        <button class="btn btn-sm" id="btnActivate" onclick="ProductDetail.setStatus('active')"><span
                class="material-symbols-rounded">check_circle</span> Aktivieren</button>
        <button class="btn btn-sm" id="btnDeactivate" onclick="ProductDetail.setStatus('draft')"><span
                class="material-symbols-rounded">pause_circle</span> Als Entwurf</button>
        <button class="btn btn-sm" id="btnArchive" onclick="ProductDetail.setStatus('archived')"><span
                class="material-symbols-rounded">archive</span> Archivieren</button>
    </div>
</div>

<div class="product-content" id="productContent" style="display:none;">
    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h3>Produktdaten</h3>
            </div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value"
                        id="detailName">-</span></div>
                <div class="detail-row"><span class="detail-label">SKU</span><span class="detail-value"
                        id="detailSku">-</span></div>
                <div class="detail-row"><span class="detail-label">Typ</span><span class="detail-value"
                        id="detailType">-</span></div>
                <div class="detail-row"><span class="detail-label">Kategorie(n)</span><span class="detail-value"
                        id="detailCategories">-</span></div>
                <div class="detail-row"><span class="detail-label">URL-Slug</span><span class="detail-value"
                        id="detailSlug">-</span></div>
                <div class="detail-row"><span class="detail-label">Erstellt am</span><span class="detail-value"
                        id="detailCreated">-</span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3>Preisgestaltung</h3>
            </div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">Verkaufspreis</span><span
                        class="detail-value price-big" id="detailPrice">-</span></div>
                <div class="detail-row" id="specialPriceRow" style="display:none;"><span
                        class="detail-label">Sonderpreis</span><span class="detail-value"
                        id="detailSpecialPrice">-</span></div>
                <div class="detail-row" id="costPriceRow" style="display:none;"><span
                        class="detail-label">Einkaufspreis</span><span class="detail-value"
                        id="detailCostPrice">-</span></div>
                <div class="detail-row" id="marginRow" style="display:none;"><span
                        class="detail-label">Marge</span><span class="detail-value" id="detailMargin">-</span></div>
                <div class="detail-row"><span class="detail-label">Steuerklasse</span><span class="detail-value"
                        id="detailTax">-</span></div>
            </div>
        </div>
    </div>

    <!-- Image Gallery -->
    <div class="card" id="imageCard">
        <div class="card-header">
            <h3>Produktbilder</h3>
        </div>
        <div class="card-body">
            <div class="detail-image-gallery" id="imageGallery">
                <p style="color:var(--text-muted);">Keine Bilder vorhanden</p>
            </div>
        </div>
    </div>

    <div class="card" id="descriptionCard">
        <div class="card-header">
            <h3>Beschreibung</h3>
        </div>
        <div class="card-body">
            <p id="detailShortDesc" style="color:var(--text-muted);font-style:italic;"></p>
            <div id="detailDescription" style="margin-top:12px;"></div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h3>Inventar</h3>
            </div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">Bestand verfolgt</span><span class="detail-value"
                        id="detailManageStock">-</span></div>
                <div class="detail-row" id="quantityRow"><span class="detail-label">Lagermenge</span><span
                        class="detail-value" id="detailQuantity">-</span></div>
                <div class="detail-row" id="lowStockRow"><span class="detail-label">Mindestbestand</span><span
                        class="detail-value" id="detailLowStock">-</span></div>
                <div class="detail-row"><span class="detail-label">Rückbestellungen</span><span class="detail-value"
                        id="detailBackorders">-</span></div>
            </div>
        </div>
        <div class="card" id="shippingCard">
            <div class="card-header">
                <h3>Versand</h3>
            </div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">Gewicht</span><span class="detail-value"
                        id="detailWeight">-</span></div>
                <div class="detail-row"><span class="detail-label">Maße (L×B×H)</span><span class="detail-value"
                        id="detailDimensions">-</span></div>
            </div>
        </div>
    </div>

    <div class="card" id="seoCard">
        <div class="card-header">
            <h3>SEO</h3>
        </div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Meta-Titel</span><span class="detail-value"
                    id="detailMetaTitle">-</span></div>
            <div class="detail-row"><span class="detail-label">Meta-Beschreibung</span><span class="detail-value"
                    id="detailMetaDesc">-</span></div>
            <div class="detail-row"><span class="detail-label">Keywords</span><span class="detail-value"
                    id="detailKeywords">-</span></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Statistiken</h3>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-item"><span class="stat-value" id="statViews">0</span><span
                        class="stat-label">Aufrufe</span></div>
                <div class="stat-item"><span class="stat-value" id="statSold">0</span><span
                        class="stat-label">Verkauft</span></div>
                <div class="stat-item"><span class="stat-value" id="statRating">-</span><span
                        class="stat-label">Bewertung</span></div>
                <div class="stat-item"><span class="stat-value" id="statReviews">0</span><span
                        class="stat-label">Rezensionen</span></div>
            </div>
        </div>
    </div>
</div>

<!-- Loading State -->
<div class="loading-state" id="loadingState">
    <span class="material-symbols-rounded spinning">sync</span>
    <p>Produkt wird geladen...</p>
</div>

<!-- Delete Modal -->
<div class="modal" id="confirmModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Produkt löschen</h3>
            <button class="modal-close" onclick="ProductDetail.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Möchten Sie dieses Produkt wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="ProductDetail.closeModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="ProductDetail.confirmDelete()">Löschen</button>
        </div>
    </div>
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

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-subtle);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: var(--text-muted);
    }

    .detail-value {
        font-weight: 500;
        text-align: right;
        max-width: 60%;
    }

    .price-big {
        font-size: 24px;
        font-weight: 600;
        color: var(--success);
    }

    .detail-image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .detail-image-item {
        width: 120px;
        height: 120px;
        border-radius: var(--radius-md);
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .detail-image-item:hover {
        transform: scale(1.05);
    }

    .detail-image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .detail-image-item.main-image {
        border: 2px solid var(--accent);
    }

    .status-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        background: var(--bg-secondary);
        border-radius: var(--radius-md);
        margin-bottom: 24px;
    }

    .status-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .status-label {
        color: var(--text-muted);
    }

    .status-actions {
        display: flex;
        gap: 8px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        display: block;
        font-size: 28px;
        font-weight: 600;
        color: var(--accent);
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 13px;
    }

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

    .modal-header h3 {
        margin: 0;
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
    const ProductDetail = {
        apiBase: 'api/products.php',
        shopId: 1,
        productId: <?= $productId ?>,
        product: null,

        async init() {
            if (!this.productId) {
                window.location.href = '?page=catalog/products';
                return;
            }
            await this.loadProduct();
        },

        async loadProduct() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_product&shop_id=${this.shopId}&id=${this.productId}`);
                const data = await res.json();

                if (!data.success) {
                    this.showToast('Produkt nicht gefunden', 'error');
                    setTimeout(() => window.location.href = '?page=catalog/products', 2000);
                    return;
                }

                this.product = data.product;
                this.renderProduct();

                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('productContent').style.display = 'block';
                document.getElementById('statusBar').style.display = 'flex';

            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        renderProduct() {
            const p = this.product;

            // Header
            document.getElementById('pageTitle').textContent = p.name;
            document.getElementById('breadcrumbName').textContent = p.name;
            document.getElementById('pageSubtitle').textContent = `SKU: ${p.sku} · Erstellt: ${this.formatDate(p.created_at)}`;

            // Status
            this.updateStatusUI(p.status);

            // Type labels
            const typeLabels = {
                simple: 'Physisches Produkt',
                digital: 'Digitales Produkt',
                bundle: 'Bundle',
                configurable: 'Konfigurierbar'
            };

            // Tax labels
            const taxLabels = { 1: 'Standard (19%)', 2: 'Ermäßigt (7%)', 3: 'Steuerfrei' };

            // Basic data
            document.getElementById('detailName').textContent = p.name;
            document.getElementById('detailSku').textContent = p.sku;
            document.getElementById('detailType').textContent = typeLabels[p.type] || p.type;
            document.getElementById('detailCategories').textContent = p.categories?.map(c => c.name).join(', ') || '-';
            document.getElementById('detailSlug').textContent = p.slug || '-';
            document.getElementById('detailCreated').textContent = this.formatDate(p.created_at);

            // Pricing
            const price = parseFloat(p.price || 0);
            document.getElementById('detailPrice').textContent = `€${price.toFixed(2).replace('.', ',')}`;

            if (p.special_price) {
                document.getElementById('specialPriceRow').style.display = 'flex';
                document.getElementById('detailSpecialPrice').textContent = `€${parseFloat(p.special_price).toFixed(2).replace('.', ',')}`;
            }

            if (p.cost_price) {
                const cost = parseFloat(p.cost_price);
                document.getElementById('costPriceRow').style.display = 'flex';
                document.getElementById('detailCostPrice').textContent = `€${cost.toFixed(2).replace('.', ',')}`;

                if (price > 0) {
                    const margin = ((price - cost) / price * 100).toFixed(1);
                    document.getElementById('marginRow').style.display = 'flex';
                    document.getElementById('detailMargin').innerHTML = `<span class="badge badge-${margin >= 0 ? 'success' : 'error'}">${margin}%</span>`;
                }
            }

            document.getElementById('detailTax').textContent = taxLabels[p.tax_class_id] || '-';

            // Images
            const imageGallery = document.getElementById('imageGallery');
            if (p.images && p.images.length > 0) {
                imageGallery.innerHTML = p.images.map((img, idx) => `
                    <div class="detail-image-item ${idx === 0 ? 'main-image' : ''}">
                        <img src="${img.image_url}" alt="${img.alt_text || p.name}" 
                             onclick="ProductDetail.openImage('${img.image_url}')">
                    </div>
                `).join('');
            } else {
                imageGallery.innerHTML = '<p style="color:var(--text-muted);">Keine Bilder vorhanden</p>';
            }

            // Description
            document.getElementById('detailShortDesc').textContent = p.short_description || '';
            document.getElementById('detailDescription').innerHTML = p.description || '<span style="color:var(--text-muted);">Keine Beschreibung</span>';

            // Inventory
            const manageStock = p.manage_stock == 1;
            document.getElementById('detailManageStock').textContent = manageStock ? 'Ja' : 'Nein (Unbegrenzt)';
            document.getElementById('quantityRow').style.display = manageStock ? 'flex' : 'none';
            document.getElementById('lowStockRow').style.display = manageStock ? 'flex' : 'none';

            if (manageStock) {
                const qty = parseInt(p.quantity || 0);
                const lowStock = parseInt(p.low_stock_threshold || 5);
                const qtyClass = qty === 0 ? 'badge-error' : (qty <= lowStock ? 'badge-warning' : 'badge-success');
                document.getElementById('detailQuantity').innerHTML = `<span class="badge ${qtyClass}">${qty}</span>`;
                document.getElementById('detailLowStock').textContent = lowStock;
            }

            document.getElementById('detailBackorders').textContent = p.allow_backorders == 1 ? 'Erlaubt' : 'Nicht erlaubt';

            // Shipping (only for physical products)
            if (p.type === 'simple' || p.type === 'bundle' || p.type === 'configurable') {
                document.getElementById('shippingCard').style.display = 'block';
                document.getElementById('detailWeight').textContent = p.weight ? `${p.weight} kg` : '-';
                const dims = [p.length, p.width, p.height].filter(d => d).map(d => `${d}cm`);
                document.getElementById('detailDimensions').textContent = dims.length ? dims.join(' × ') : '-';
            } else {
                document.getElementById('shippingCard').style.display = 'none';
            }

            // SEO
            document.getElementById('detailMetaTitle').textContent = p.meta_title || '-';
            document.getElementById('detailMetaDesc').textContent = p.meta_description || '-';
            document.getElementById('detailKeywords').textContent = p.meta_keywords || '-';

            // Stats
            document.getElementById('statViews').textContent = p.view_count || 0;
            document.getElementById('statSold').textContent = p.sold_count || 0;
            document.getElementById('statRating').textContent = p.avg_rating > 0 ? `${parseFloat(p.avg_rating).toFixed(1)}★` : '-';
            document.getElementById('statReviews').textContent = p.review_count || 0;
        },

        updateStatusUI(status) {
            const badge = document.getElementById('statusBadge');
            const labels = { active: 'Aktiv', draft: 'Entwurf', archived: 'Archiviert' };
            const classes = { active: 'badge-success', draft: 'badge-warning', archived: 'badge-default' };

            badge.textContent = labels[status] || status;
            badge.className = 'badge ' + (classes[status] || '');

            document.getElementById('btnActivate').style.display = status !== 'active' ? '' : 'none';
            document.getElementById('btnDeactivate').style.display = status !== 'draft' ? '' : 'none';
            document.getElementById('btnArchive').style.display = status !== 'archived' ? '' : 'none';
        },

        async setStatus(status) {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.productId);
            formData.append('status', status);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.product.status = status;
                    this.updateStatusUI(status);
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast('Fehler: ' + data.error, 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        deleteProduct() {
            document.getElementById('confirmModal').style.display = 'flex';
        },

        closeModal() {
            document.getElementById('confirmModal').style.display = 'none';
        },

        async confirmDelete() {
            const formData = new FormData();
            formData.append('action', 'delete_product');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.productId);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Produkt gelöscht', 'success');
                    setTimeout(() => window.location.href = '?page=catalog/products', 1000);
                } else {
                    this.showToast('Fehler: ' + data.error, 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }

            this.closeModal();
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },

        openImage(url) {
            window.open(url, '_blank');
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', () => ProductDetail.init());
</script>