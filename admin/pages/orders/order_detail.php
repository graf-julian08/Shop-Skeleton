<?php /** Bestellungen - Bestelldetail */ ?>
<div class="page-header" id="pageHeader">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=orders/orders">Bestellungen</a> <span>›</span> <span
                id="orderTitle">Lädt...</span></nav>
        <h1 id="headerTitle">Bestellung</h1>
        <p class="page-subtitle" id="headerSubtitle">Lädt...</p>
    </div>
    <div class="page-header-actions" id="headerActions">
        <!-- Dynamic actions -->
    </div>
</div>

<div class="loading-container" id="loadingContainer">
    <span class="material-symbols-rounded spinning">sync</span>
    <p>Lade Bestelldetails...</p>
</div>

<div id="orderContent" style="display: none;">
    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h3>Bestelldetails</h3>
            </div>
            <div class="card-body" id="orderDetails"></div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3>Kunde</h3>
                <a href="#" id="customerLink" class="btn btn-sm">Profil öffnen</a>
            </div>
            <div class="card-body" id="customerDetails"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Bestellpositionen</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>SKU</th>
                        <th>Preis</th>
                        <th>Menge</th>
                        <th>Gesamt</th>
                    </tr>
                </thead>
                <tbody id="orderItems"></tbody>
                <tfoot id="orderTotals"></tfoot>
            </table>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <h3>Rechnungsadresse</h3>
            </div>
            <div class="card-body" id="billingAddress"></div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3>Lieferadresse</h3>
            </div>
            <div class="card-body" id="shippingAddress"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Bestellverlauf</h3>
        </div>
        <div class="card-body">
            <div class="timeline" id="orderHistory"></div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal-overlay" id="statusModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Status ändern</h3>
            <button class="modal-close" onclick="OrderDetail.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Neuer Status</label>
                <select id="newStatus" class="filter-select" style="width: 100%;">
                    <option value="pending">Ausstehend</option>
                    <option value="processing">In Bearbeitung</option>
                    <option value="shipped">Versendet</option>
                    <option value="delivered">Zugestellt</option>
                    <option value="cancelled">Storniert</option>
                </select>
            </div>
            <div class="form-group">
                <label>Kommentar (optional)</label>
                <textarea id="statusComment" rows="3" class="form-control"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="OrderDetail.closeModal()">Abbrechen</button>
            <button class="btn btn-primary" onclick="OrderDetail.saveStatus()">Speichern</button>
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
    }

    .loading-container {
        text-align: center;
        padding: 80px 20px;
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

    .timeline {
        padding-left: 20px;
        border-left: 2px solid var(--border-color);
    }

    .timeline-item {
        position: relative;
        padding: 0 0 20px 20px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: -27px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--accent);
        border: 2px solid var(--bg-primary);
    }

    .timeline-dot.success {
        background: var(--success);
    }

    .timeline-dot.warning {
        background: #f59e0b;
    }

    .timeline-dot.error {
        background: var(--error);
    }

    .timeline-content strong {
        display: block;
        margin-bottom: 4px;
    }

    .timeline-content small {
        color: var(--text-muted);
    }

    .badge {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 10px;
        font-weight: 500;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .badge-info {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }

    .badge-error {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .badge-purple {
        background: rgba(139, 92, 246, 0.15);
        color: #8b5cf6;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 480px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-header h3 {
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--text-muted);
        cursor: pointer;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px;
        border-top: 1px solid var(--border-color);
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
        resize: vertical;
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

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<script>
    const OrderDetail = {
        apiBase: 'api/orders.php',
        shopId: 1,
        orderId: null,
        order: null,

        async init() {
            const params = new URLSearchParams(window.location.search);
            this.orderId = parseInt(params.get('id')) || 0;

            if (this.orderId <= 0) {
                document.getElementById('loadingContainer').innerHTML = '<p>Ungültige Bestell-ID</p>';
                return;
            }

            await this.loadOrder();
        },

        async loadOrder() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_order&shop_id=${this.shopId}&id=${this.orderId}`);
                const data = await res.json();

                if (data.success) {
                    this.order = data.order;
                    this.renderOrder();
                    document.getElementById('loadingContainer').style.display = 'none';
                    document.getElementById('orderContent').style.display = 'block';
                } else {
                    document.getElementById('loadingContainer').innerHTML = `<p>${data.error || 'Bestellung nicht gefunden'}</p>`;
                }
            } catch (e) {
                document.getElementById('loadingContainer').innerHTML = '<p>Fehler beim Laden</p>';
            }
        },

        renderOrder() {
            const o = this.order;
            const date = new Date(o.created_at).toLocaleString('de-DE');

            // Header
            document.getElementById('orderTitle').textContent = o.order_number;
            document.getElementById('headerTitle').textContent = `Bestellung ${o.order_number}`;
            document.getElementById('headerSubtitle').innerHTML = `${date} · ${this.getStatusBadge(o.status)} ${this.getPaymentBadge(o.payment_status)}`;

            // Actions
            document.getElementById('headerActions').innerHTML = `
            <a href="?page=orders/order_edit&id=${o.id}" class="btn"><span class="material-symbols-rounded">edit</span> Bearbeiten</a>
            <button class="btn" onclick="OrderDetail.openStatusModal()"><span class="material-symbols-rounded">sync</span> Status ändern</button>
            ${o.status === 'processing' || o.status === 'pending' ? `<button class="btn btn-primary" onclick="OrderDetail.markAsShipped()"><span class="material-symbols-rounded">local_shipping</span> Versenden</button>` : ''}
        `;

            // Order details
            document.getElementById('orderDetails').innerHTML = `
            <div class="detail-row"><span class="detail-label">Bestellnummer</span><span class="detail-value">${o.order_number}</span></div>
            <div class="detail-row"><span class="detail-label">Datum</span><span class="detail-value">${date}</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value">${this.getStatusBadge(o.status)}</span></div>
            <div class="detail-row"><span class="detail-label">Zahlungsstatus</span><span class="detail-value">${this.getPaymentBadge(o.payment_status)}</span></div>
            <div class="detail-row"><span class="detail-label">Zahlungsart</span><span class="detail-value">${o.payment_method || '-'}</span></div>
            <div class="detail-row"><span class="detail-label">Versandart</span><span class="detail-value">${o.shipping_method || '-'}</span></div>
            ${o.tracking_number ? `<div class="detail-row"><span class="detail-label">Tracking</span><span class="detail-value">${o.tracking_number}</span></div>` : ''}
        `;

            // Customer details
            if (o.customer_id) {
                document.getElementById('customerLink').href = `?page=customers/customer_edit&id=${o.customer_id}`;
                document.getElementById('customerLink').style.display = '';
            } else {
                document.getElementById('customerLink').style.display = 'none';
            }
            document.getElementById('customerDetails').innerHTML = `
            <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">${o.customer_id ? `<a href="?page=customers/customer_edit&id=${o.customer_id}">${this.escapeHtml(o.customer_name)}</a>` : this.escapeHtml(o.customer_name)}</span></div>
            <div class="detail-row"><span class="detail-label">E-Mail</span><span class="detail-value">${o.email || '-'}</span></div>
            <div class="detail-row"><span class="detail-label">Telefon</span><span class="detail-value">${o.phone || '-'}</span></div>
        `;

            // Order items
            const symbol = o.currency_code === 'EUR' ? '€' : (o.currency_code === 'USD' ? '$' : o.currency_code);
            document.getElementById('orderItems').innerHTML = o.items.map(item => {
                const options = item.options ? Object.values(item.options).join(' / ') : '';
                return `
                <tr>
                    <td>
                        ${item.product_id ? `<a href="?page=catalog/product_edit&id=${item.product_id}">${this.escapeHtml(item.name)}</a>` : this.escapeHtml(item.name)}
                        ${options ? `<br><small style="color: var(--text-muted);">${options}</small>` : ''}
                    </td>
                    <td>${item.sku || '-'}</td>
                    <td>${this.formatPrice(item.unit_price, symbol)}</td>
                    <td>${item.quantity}</td>
                    <td>${this.formatPrice(item.total_price, symbol)}</td>
                </tr>
            `;
            }).join('');

            document.getElementById('orderTotals').innerHTML = `
            <tr><td colspan="4" style="text-align:right;">Zwischensumme</td><td>${this.formatPrice(o.subtotal, symbol)}</td></tr>
            <tr><td colspan="4" style="text-align:right;">Versand (${o.shipping_method || 'Standard'})</td><td>${this.formatPrice(o.shipping_amount, symbol)}</td></tr>
            <tr><td colspan="4" style="text-align:right;">MwSt. (19%)</td><td>${this.formatPrice(o.tax_amount, symbol)}</td></tr>
            ${parseFloat(o.discount_amount) > 0 ? `<tr><td colspan="4" style="text-align:right;">Rabatt</td><td>-${this.formatPrice(o.discount_amount, symbol)}</td></tr>` : ''}
            <tr><td colspan="4" style="text-align:right;"><strong>Gesamtsumme</strong></td><td style="font-size:18px;font-weight:600;">${this.formatPrice(o.grand_total, symbol)}</td></tr>
        `;

            // Addresses
            this.renderAddress('billingAddress', o.billing_address);
            this.renderAddress('shippingAddress', o.shipping_address);

            // History
            document.getElementById('orderHistory').innerHTML = o.history.map((h, i) => {
                const time = new Date(h.created_at).toLocaleString('de-DE');
                const dotClass = h.status.includes('Bezahlt') || h.status.includes('Zugestellt') ? 'success' :
                    h.status.includes('Storniert') || h.status.includes('Fehlgeschlagen') ? 'error' : '';
                return `
                <div class="timeline-item">
                    <div class="timeline-dot ${dotClass}"></div>
                    <div class="timeline-content">
                        <strong>${this.escapeHtml(h.status)}</strong>
                        ${h.comment ? `<p>${this.escapeHtml(h.comment)}</p>` : ''}
                        <small>${time}</small>
                    </div>
                </div>
            `;
            }).join('');
        },

        renderAddress(elementId, address) {
            const el = document.getElementById(elementId);
            if (!address) {
                el.innerHTML = '<p style="color: var(--text-muted);">Keine Adresse hinterlegt</p>';
                return;
            }
            el.innerHTML = `
            <p>
                <strong>${this.escapeHtml(address.name || '')}</strong><br>
                ${this.escapeHtml(address.street || '')}<br>
                ${this.escapeHtml(address.zip || '')} ${this.escapeHtml(address.city || '')}<br>
                ${this.escapeHtml(address.country || '')}
            </p>
        `;
        },

        getStatusBadge(status) {
            const labels = {
                'pending': ['Ausstehend', 'badge-warning'],
                'processing': ['In Bearbeitung', 'badge-info'],
                'shipped': ['Versendet', 'badge-purple'],
                'delivered': ['Zugestellt', 'badge-success'],
                'cancelled': ['Storniert', 'badge-error'],
                'refunded': ['Erstattet', 'badge-error']
            };
            const [label, cls] = labels[status] || [status, ''];
            return `<span class="badge ${cls}">${label}</span>`;
        },

        getPaymentBadge(status) {
            const labels = {
                'pending': ['Zahlung ausstehend', 'badge-warning'],
                'paid': ['Bezahlt', 'badge-success'],
                'failed': ['Fehlgeschlagen', 'badge-error'],
                'refunded': ['Erstattet', 'badge-error']
            };
            const [label, cls] = labels[status] || [status, ''];
            return `<span class="badge ${cls}">${label}</span>`;
        },

        openStatusModal() {
            document.getElementById('newStatus').value = this.order.status;
            document.getElementById('statusComment').value = '';
            document.getElementById('statusModal').classList.add('show');
        },

        closeModal() {
            document.getElementById('statusModal').classList.remove('show');
        },

        async saveStatus() {
            const newStatus = document.getElementById('newStatus').value;
            const comment = document.getElementById('statusComment').value;

            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('shop_id', this.shopId);
            formData.append('order_id', this.orderId);
            formData.append('status', newStatus);
            formData.append('comment', comment);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Status aktualisiert', 'success');
                    this.closeModal();
                    this.loadOrder();
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) {
                this.showToast('Fehler beim Speichern', 'error');
            }
        },

        async markAsShipped() {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('shop_id', this.shopId);
            formData.append('order_id', this.orderId);
            formData.append('status', 'shipped');
            formData.append('comment', 'Bestellung wurde versendet');

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Als versendet markiert', 'success');
                    this.loadOrder();
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) {
                this.showToast('Fehler', 'error');
            }
        },

        formatPrice(amount, symbol = '€') {
            return symbol + parseFloat(amount || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        }
    };

    document.addEventListener('DOMContentLoaded', () => OrderDetail.init());
</script>