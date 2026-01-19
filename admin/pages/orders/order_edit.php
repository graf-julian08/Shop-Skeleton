<?php /** Bestellungen - Bearbeiten */ ?>
<div class="page-header" id="pageHeader">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=orders/orders">Bestellungen</a> <span>›</span> <span
                id="orderTitle">Lädt...</span> <span>›</span> <span>Bearbeiten</span></nav>
        <h1 id="headerTitle">Bestellung bearbeiten</h1>
    </div>
    <div class="page-header-actions">
        <a href="#" id="backLink" class="btn"><span class="material-symbols-rounded">arrow_back</span> Zurück</a>
        <button class="btn btn-primary" onclick="OrderEdit.save()"><span class="material-symbols-rounded">save</span>
            Speichern</button>
    </div>
</div>

<div class="loading-container" id="loadingContainer">
    <span class="material-symbols-rounded spinning">sync</span>
    <p>Lade Bestellung...</p>
</div>

<div id="editContent" style="display: none;">
    <div class="dashboard-grid">
        <!-- Status -->
        <div class="card">
            <div class="card-header">
                <h3>Status</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Bestellstatus</label>
                    <select id="orderStatus" class="filter-select" style="width: 100%;">
                        <option value="pending">Ausstehend</option>
                        <option value="processing">In Bearbeitung</option>
                        <option value="shipped">Versendet</option>
                        <option value="delivered">Zugestellt</option>
                        <option value="cancelled">Storniert</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Zahlungsstatus</label>
                    <select id="paymentStatus" class="filter-select" style="width: 100%;">
                        <option value="pending">Ausstehend</option>
                        <option value="paid">Bezahlt</option>
                        <option value="failed">Fehlgeschlagen</option>
                        <option value="refunded">Erstattet</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tracking-Nummer</label>
                    <input type="text" id="trackingNumber" class="form-control" placeholder="z.B. DHL123456789">
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="card">
            <div class="card-header">
                <h3>Notizen</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Admin-Notizen (intern)</label>
                    <textarea id="adminNotes" rows="4" class="form-control" placeholder="Interne Notizen..."></textarea>
                </div>
                <div class="form-group">
                    <label>Kundennotizen</label>
                    <textarea id="customerNotes" rows="3" class="form-control" readonly></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Billing Address -->
        <div class="card">
            <div class="card-header">
                <h3>Rechnungsadresse</h3>
            </div>
            <div class="card-body" id="billingAddressDisplay"></div>
        </div>

        <!-- Shipping Address -->
        <div class="card">
            <div class="card-header">
                <h3>Lieferadresse</h3>
            </div>
            <div class="card-body" id="shippingAddressDisplay"></div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="card">
        <div class="card-header">
            <h3>Bestellübersicht</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Menge</th>
                        <th>Preis</th>
                        <th>Gesamt</th>
                    </tr>
                </thead>
                <tbody id="orderItems"></tbody>
                <tfoot id="orderTotals"></tfoot>
            </table>
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

    .form-group {
        margin-bottom: 16px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-secondary);
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent);
    }

    .form-control[readonly] {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .address-display {
        line-height: 1.6;
    }

    .address-display strong {
        display: block;
        margin-bottom: 4px;
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
    const OrderEdit = {
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

            document.getElementById('backLink').href = `?page=orders/order_detail&id=${this.orderId}`;
            await this.loadOrder();
        },

        async loadOrder() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_order&shop_id=${this.shopId}&id=${this.orderId}`);
                const data = await res.json();

                if (data.success) {
                    this.order = data.order;
                    this.renderForm();
                    document.getElementById('loadingContainer').style.display = 'none';
                    document.getElementById('editContent').style.display = 'block';
                } else {
                    document.getElementById('loadingContainer').innerHTML = `<p>${data.error || 'Bestellung nicht gefunden'}</p>`;
                }
            } catch (e) {
                document.getElementById('loadingContainer').innerHTML = '<p>Fehler beim Laden</p>';
            }
        },

        renderForm() {
            const o = this.order;

            document.getElementById('orderTitle').textContent = o.order_number;
            document.getElementById('headerTitle').textContent = `Bestellung ${o.order_number} bearbeiten`;

            // Status
            document.getElementById('orderStatus').value = o.status;
            document.getElementById('paymentStatus').value = o.payment_status;
            document.getElementById('trackingNumber').value = o.tracking_number || '';

            // Notes
            document.getElementById('adminNotes').value = o.admin_notes || '';
            document.getElementById('customerNotes').value = o.customer_notes || 'Keine Kundennotizen';

            // Addresses
            this.renderAddress('billingAddressDisplay', o.billing_address);
            this.renderAddress('shippingAddressDisplay', o.shipping_address);

            // Order items
            const symbol = o.currency_code === 'EUR' ? '€' : (o.currency_code === 'USD' ? '$' : o.currency_code);
            document.getElementById('orderItems').innerHTML = o.items.map(item => `
            <tr>
                <td>${this.escapeHtml(item.name)}</td>
                <td>${item.quantity}</td>
                <td>${this.formatPrice(item.unit_price, symbol)}</td>
                <td>${this.formatPrice(item.total_price, symbol)}</td>
            </tr>
        `).join('');

            document.getElementById('orderTotals').innerHTML = `
            <tr><td colspan="3" style="text-align:right;">Zwischensumme</td><td>${this.formatPrice(o.subtotal, symbol)}</td></tr>
            <tr><td colspan="3" style="text-align:right;">Versand</td><td>${this.formatPrice(o.shipping_amount, symbol)}</td></tr>
            <tr><td colspan="3" style="text-align:right;">MwSt.</td><td>${this.formatPrice(o.tax_amount, symbol)}</td></tr>
            <tr><td colspan="3" style="text-align:right;"><strong>Gesamt</strong></td><td><strong>${this.formatPrice(o.grand_total, symbol)}</strong></td></tr>
        `;
        },

        renderAddress(elementId, address) {
            const el = document.getElementById(elementId);
            if (!address) {
                el.innerHTML = '<p style="color: var(--text-muted);">Keine Adresse</p>';
                return;
            }
            el.innerHTML = `
            <div class="address-display">
                <strong>${this.escapeHtml(address.name || '')}</strong>
                ${this.escapeHtml(address.street || '')}<br>
                ${this.escapeHtml(address.zip || '')} ${this.escapeHtml(address.city || '')}<br>
                ${this.escapeHtml(address.country || '')}
            </div>
        `;
        },

        async save() {
            const newStatus = document.getElementById('orderStatus').value;
            const newPaymentStatus = document.getElementById('paymentStatus').value;

            // Update order status if changed
            if (newStatus !== this.order.status) {
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('shop_id', this.shopId);
                formData.append('order_id', this.orderId);
                formData.append('status', newStatus);

                try {
                    await fetch(this.apiBase, { method: 'POST', body: formData });
                } catch (e) { console.error(e); }
            }

            // Update payment status if changed
            if (newPaymentStatus !== this.order.payment_status) {
                const formData = new FormData();
                formData.append('action', 'update_payment_status');
                formData.append('shop_id', this.shopId);
                formData.append('order_id', this.orderId);
                formData.append('payment_status', newPaymentStatus);

                try {
                    await fetch(this.apiBase, { method: 'POST', body: formData });
                } catch (e) { console.error(e); }
            }

            this.showToast('Änderungen gespeichert', 'success');

            // Reload after short delay
            setTimeout(() => {
                window.location.href = `?page=orders/order_detail&id=${this.orderId}`;
            }, 1000);
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

    document.addEventListener('DOMContentLoaded', () => OrderEdit.init());
</script>