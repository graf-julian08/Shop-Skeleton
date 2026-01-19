<?php
/** Kunden - Kunde bearbeiten */
$customerId = (int) ($_GET['id'] ?? 0);

global $database;
require_once __DIR__ . '/../../includes/Database.php';

if (is_array($database)) {
    Database::configure($database);
}

// Get shop currency
$shopCurrency = Database::fetch("SELECT default_currency FROM shops WHERE id = 1");
$currencyCode = $shopCurrency['default_currency'] ?? 'EUR';
$currencySymbols = ['EUR' => '€', 'USD' => '$', 'GBP' => '£', 'CHF' => 'CHF', 'JPY' => '¥'];
$currencySymbol = $currencySymbols[$currencyCode] ?? $currencyCode;

// Get customer groups for dropdown
$groups = Database::fetchAll("SELECT id, name FROM customer_groups WHERE shop_id = 1 ORDER BY is_default DESC, name") ?: [];
?>

<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb">
            <a href="?page=customers/customers">Kundenliste</a>
            <span>›</span>
            <span id="breadcrumbName">Kunde bearbeiten</span>
        </nav>
        <h1 id="pageTitle">Kunde bearbeiten</h1>
        <p class="page-subtitle">Kundendetails bearbeiten und speichern</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-danger-ghost" onclick="CustomerEdit.deleteCustomer()">
            <span class="material-symbols-rounded">delete</span> Löschen
        </button>
        <a href="?page=customers/customers" class="btn">Abbrechen</a>
        <button class="btn btn-primary" onclick="CustomerEdit.save()">
            <span class="material-symbols-rounded">save</span> Speichern
        </button>
    </div>
</div>

<div class="customer-edit-container" id="customerContainer" style="display:none;">
    <!-- Status Bar -->
    <div class="status-bar card" id="statusBar">
        <div class="status-info">
            <span class="status-label">Status:</span>
            <span class="badge" id="statusBadge">-</span>
        </div>
        <div class="status-actions">
            <button class="btn btn-sm" id="btnActivate" onclick="CustomerEdit.setStatus(1)">
                <span class="material-symbols-rounded">check_circle</span> Aktivieren
            </button>
            <button class="btn btn-sm" id="btnBlock" onclick="CustomerEdit.setStatus(0)">
                <span class="material-symbols-rounded">block</span> Sperren
            </button>
        </div>
    </div>

    <div class="tabs" id="editTabs">
        <button class="tab active" data-tab="details">Grunddaten</button>
        <button class="tab" data-tab="orders">Bestellungen</button>
        <button class="tab" data-tab="addresses">Adressen</button>
        <button class="tab" data-tab="notes">Notizen</button>
    </div>

    <form id="customerForm">
        <input type="hidden" name="id" id="customerId" value="<?= $customerId ?>">

        <!-- Tab: Grunddaten -->
        <div class="tab-content active" data-tab-content="details">
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Persönliche Daten</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Vorname <span class="required">*</span></label>
                                <input type="text" class="form-input" name="first_name" id="firstName" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nachname <span class="required">*</span></label>
                                <input type="text" class="form-input" name="last_name" id="lastName" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-Mail <span class="required">*</span></label>
                            <input type="email" class="form-input" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefon</label>
                            <input type="text" class="form-input" name="phone" id="phone">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Firma</label>
                            <input type="text" class="form-input" name="company_name" id="companyName">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Einstellungen</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Kundengruppe</label>
                            <select class="form-select" name="customer_group_id" id="customerGroup">
                                <option value="">Keine Gruppe</option>
                                <?php foreach ($groups as $g): ?>
                                    <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="toggle-label">
                                <input type="checkbox" name="subscribed_to_newsletter" id="newsletter" value="1">
                                Newsletter abonniert
                            </label>
                        </div>
                        <hr style="margin:20px 0; border-color:var(--border-color);">
                        <div class="form-group">
                            <label class="form-label">Bevorzugte Sprache</label>
                            <input type="text" class="form-input" name="preferred_locale" id="preferredLocale"
                                placeholder="z.B. de_DE" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bevorzugte Währung</label>
                            <input type="text" class="form-input" name="preferred_currency" id="preferredCurrency"
                                placeholder="z.B. EUR" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-row" style="margin-top:24px;">
                <div class="card stat-card">
                    <div class="stat-value" id="statOrders">0</div>
                    <div class="stat-label">Bestellungen</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-value" id="statSpent"><?= $currencySymbol ?>0</div>
                    <div class="stat-label">Gesamtumsatz</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-value" id="statSince">-</div>
                    <div class="stat-label">Kunde seit</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-value" id="statLastLogin">-</div>
                    <div class="stat-label">Letzter Login</div>
                </div>
            </div>
        </div>

        <!-- Tab: Bestellungen -->
        <div class="tab-content" data-tab-content="orders" style="display:none;">
            <div class="card">
                <div class="card-header">
                    <h3>Bestellhistorie</h3>
                </div>
                <div class="card-body">
                    <div id="ordersLoading" class="loading-state">
                        <span class="material-symbols-rounded spinning">sync</span>
                        <p>Bestellungen werden geladen...</p>
                    </div>
                    <div id="ordersEmpty" class="empty-state" style="display:none;">
                        <span class="material-symbols-rounded">shopping_cart</span>
                        <p>Keine Bestellungen vorhanden</p>
                    </div>
                    <table class="table" id="ordersTable" style="display:none;">
                        <thead>
                            <tr>
                                <th>Bestellnummer</th>
                                <th>Datum</th>
                                <th>Status</th>
                                <th>Summe</th>
                            </tr>
                        </thead>
                        <tbody id="ordersBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab: Adressen -->
        <div class="tab-content" data-tab-content="addresses" style="display:none;">
            <div class="card">
                <div class="card-header">
                    <h3>Gespeicherte Adressen</h3>
                </div>
                <div class="card-body">
                    <div id="addressesLoading" class="loading-state">
                        <span class="material-symbols-rounded spinning">sync</span>
                        <p>Adressen werden geladen...</p>
                    </div>
                    <div id="addressesEmpty" class="empty-state" style="display:none;">
                        <span class="material-symbols-rounded">location_off</span>
                        <p>Keine Adressen hinterlegt</p>
                    </div>
                    <div id="addressesGrid" class="addresses-grid" style="display:none;"></div>
                </div>
            </div>
        </div>

        <!-- Tab: Notizen -->
        <div class="tab-content" data-tab-content="notes" style="display:none;">
            <div class="card">
                <div class="card-header">
                    <h3>Interne Notizen</h3>
                </div>
                <div class="card-body">
                    <p class="form-hint" style="margin-bottom:16px;">
                        Diese Notizen sind nur für Administratoren sichtbar und werden dem Kunden nicht angezeigt.
                    </p>
                    <div class="form-group">
                        <textarea class="form-textarea" name="admin_notes" id="adminNotes" rows="8"
                            placeholder="z.B. Treuer Kunde, bevorzugt Express-Versand..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Loading State -->
<div class="loading-state" id="loadingState">
    <span class="material-symbols-rounded spinning">sync</span>
    <p>Kunde wird geladen...</p>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="confirmModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Kunde löschen</h3>
            <button class="modal-close" onclick="CustomerEdit.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Möchten Sie diesen Kunden wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="CustomerEdit.closeModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="CustomerEdit.confirmDelete()">Löschen</button>
        </div>
    </div>
</div>

<!-- Block Confirmation Modal -->
<div class="modal" id="blockModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Kunde sperren</h3>
            <button class="modal-close" onclick="CustomerEdit.closeBlockModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Kunde wirklich sperren?</p>
            <p style="color:var(--text-muted); font-size:13px; margin-top:8px;">Der Kunde kann sich nicht mehr einloggen
                oder bestellen.</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="CustomerEdit.closeBlockModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="CustomerEdit.confirmBlock()">Sperren</button>
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

    .required {
        color: var(--error);
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

    .status-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px !important;
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

    .toggle-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .stat-card {
        padding: 20px !important;
        text-align: center;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 13px;
        color: var(--text-muted);
    }

    .loading-state,
    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--text-muted);
    }

    .loading-state .material-symbols-rounded,
    .empty-state .material-symbols-rounded {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
    }

    .spinning {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .addresses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }

    .address-card {
        background: var(--hover-bg);
        border-radius: 12px;
        padding: 16px;
        border: 1px solid var(--border-color);
    }

    .address-card-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .address-type {
        font-weight: 600;
        font-size: 13px;
        color: var(--accent);
    }

    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: var(--card-bg);
        border-radius: 12px;
        max-width: 400px;
        width: 90%;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
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
        padding: 16px 20px;
        border-top: 1px solid var(--border-color);
    }

    .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        display: none;
    }

    .toast.show {
        display: block;
        animation: slideIn 0.3s ease;
    }

    .toast.success {
        background: var(--success);
    }

    .toast.error {
        background: var(--danger);
    }

    @keyframes slideIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<script>
    const CustomerEdit = {
        apiBase: 'api/customers.php',
        shopId: 1,
        customerId: <?= $customerId ?>,
        currencySymbol: '<?= $currencySymbol ?>',
        customer: null,

        async init() {
            if (!this.customerId) {
                this.showToast('Ungültige Kunden-ID', 'error');
                return;
            }

            await this.loadCustomer();
            this.setupTabs();
        },

        setupTabs() {
            document.querySelectorAll('.tab[data-tab]').forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabName = tab.dataset.tab;

                    // Update active tab
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    // Show/hide content
                    document.querySelectorAll('.tab-content').forEach(c => {
                        c.style.display = c.dataset.tabContent === tabName ? 'block' : 'none';
                    });
                });
            });
        },

        async loadCustomer() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_customer&shop_id=${this.shopId}&id=${this.customerId}`);
                const data = await res.json();

                if (!data.success) {
                    this.showToast(data.error || 'Kunde nicht gefunden', 'error');
                    setTimeout(() => window.location.href = '?page=customers/customers', 2000);
                    return;
                }

                this.customer = data.customer;
                this.populateForm();

                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('customerContainer').style.display = 'block';

            } catch (e) {
                console.error('Load error:', e);
                this.showToast('Fehler beim Laden', 'error');
            }
        },

        populateForm() {
            const c = this.customer;

            // Page title
            const fullName = `${c.first_name || ''} ${c.last_name || ''}`.trim() || c.email;
            document.getElementById('pageTitle').textContent = fullName;
            document.getElementById('breadcrumbName').textContent = fullName;

            // Form fields
            document.getElementById('firstName').value = c.first_name || '';
            document.getElementById('lastName').value = c.last_name || '';
            document.getElementById('email').value = c.email || '';
            document.getElementById('phone').value = c.phone || '';
            document.getElementById('companyName').value = c.company_name || '';
            document.getElementById('customerGroup').value = c.customer_group_id || '';
            document.getElementById('newsletter').checked = c.subscribed_to_newsletter == 1;
            document.getElementById('preferredLocale').value = c.preferred_locale || '';
            document.getElementById('preferredCurrency').value = c.preferred_currency || '';
            document.getElementById('adminNotes').value = c.admin_notes || '';

            // Status
            this.updateStatusBadge(c.is_active);

            // Stats
            document.getElementById('statOrders').textContent = c.orders_count || 0;
            document.getElementById('statSpent').textContent =
                `${this.currencySymbol}${parseFloat(c.total_spent || 0).toLocaleString('de-DE', { minimumFractionDigits: 2 })}`;
            document.getElementById('statSince').textContent =
                c.created_at ? new Date(c.created_at).toLocaleDateString('de-DE') : '-';
            document.getElementById('statLastLogin').textContent =
                c.last_login_at ? new Date(c.last_login_at).toLocaleDateString('de-DE') : 'Nie';

            // Load orders
            this.renderOrders(c.orders || []);

            // Load addresses
            this.renderAddresses(c.addresses || []);
        },

        updateStatusBadge(isActive) {
            const badge = document.getElementById('statusBadge');
            if (isActive) {
                badge.className = 'badge badge-success';
                badge.textContent = 'Aktiv';
                document.getElementById('btnActivate').style.display = 'none';
                document.getElementById('btnBlock').style.display = 'inline-flex';
            } else {
                badge.className = 'badge badge-danger';
                badge.textContent = 'Gesperrt';
                document.getElementById('btnActivate').style.display = 'inline-flex';
                document.getElementById('btnBlock').style.display = 'none';
            }
        },

        renderOrders(orders) {
            document.getElementById('ordersLoading').style.display = 'none';

            if (orders.length === 0) {
                document.getElementById('ordersEmpty').style.display = 'block';
                return;
            }

            document.getElementById('ordersTable').style.display = 'table';

            const tbody = document.getElementById('ordersBody');
            tbody.innerHTML = orders.map(o => {
                const statusClass = {
                    'pending': 'badge-warning',
                    'processing': 'badge-info',
                    'paid': 'badge-success',
                    'shipped': 'badge-primary',
                    'delivered': 'badge-success',
                    'completed': 'badge-success',
                    'cancelled': 'badge-danger',
                    'refunded': 'badge-danger'
                }[o.status] || 'badge-default';

                return `
                <tr>
                    <td><strong>#${o.order_number}</strong></td>
                    <td>${new Date(o.created_at).toLocaleDateString('de-DE')}</td>
                    <td><span class="badge ${statusClass}">${o.status}</span></td>
                    <td>${this.currencySymbol}${parseFloat(o.grand_total).toLocaleString('de-DE', { minimumFractionDigits: 2 })}</td>
                </tr>
            `;
            }).join('');
        },

        renderAddresses(addresses) {
            document.getElementById('addressesLoading').style.display = 'none';

            if (addresses.length === 0) {
                document.getElementById('addressesEmpty').style.display = 'block';
                return;
            }

            document.getElementById('addressesGrid').style.display = 'grid';

            const grid = document.getElementById('addressesGrid');
            grid.innerHTML = addresses.map(a => {
                const typeLabels = [];
                if (a.is_default_billing) typeLabels.push('Rechnung');
                if (a.is_default_shipping) typeLabels.push('Versand');
                const typeText = typeLabels.length > 0 ? typeLabels.join(' & ') : a.address_type || 'Adresse';

                return `
                <div class="address-card">
                    <div class="address-card-header">
                        <span class="address-type">${typeText}</span>
                    </div>
                    <div>
                        ${a.first_name || ''} ${a.last_name || ''}<br>
                        ${a.company ? a.company + '<br>' : ''}
                        ${a.address_line_1}<br>
                        ${a.address_line_2 ? a.address_line_2 + '<br>' : ''}
                        ${a.postal_code} ${a.city}<br>
                        ${a.country_code}
                    </div>
                </div>
            `;
            }).join('');
        },

        async save() {
            const formData = new FormData();
            formData.append('action', 'update_customer');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.customerId);
            formData.append('first_name', document.getElementById('firstName').value);
            formData.append('last_name', document.getElementById('lastName').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('phone', document.getElementById('phone').value);
            formData.append('customer_group_id', document.getElementById('customerGroup').value);
            formData.append('subscribed_to_newsletter', document.getElementById('newsletter').checked ? 1 : 0);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    // Also save notes
                    await this.saveNotes();
                    this.showToast('Kunde gespeichert', 'success');
                } else {
                    this.showToast(data.error || 'Fehler beim Speichern', 'error');
                }
            } catch (e) {
                console.error('Save error:', e);
                this.showToast('Fehler beim Speichern', 'error');
            }
        },

        async saveNotes() {
            const formData = new FormData();
            formData.append('action', 'update_notes');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.customerId);
            formData.append('notes', document.getElementById('adminNotes').value);

            try {
                await fetch(this.apiBase, { method: 'POST', body: formData });
            } catch (e) {
                console.error('Notes save error:', e);
            }
        },

        async setStatus(isActive) {
            // If blocking (isActive = 0), show confirmation first
            if (!isActive) {
                document.getElementById('blockModal').style.display = 'flex';
                return;
            }

            // Activating - no confirmation needed
            await this.executeStatusChange();
        },

        closeBlockModal() {
            document.getElementById('blockModal').style.display = 'none';
        },

        async confirmBlock() {
            this.closeBlockModal();
            await this.executeStatusChange();
        },

        async executeStatusChange() {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.customerId);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.customer.is_active = data.is_active;
                    this.updateStatusBadge(data.is_active);
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                }
            } catch (e) {
                console.error('Status error:', e);
                this.showToast('Fehler', 'error');
            }
        },

        deleteCustomer() {
            document.getElementById('confirmModal').style.display = 'flex';
        },

        closeModal() {
            document.getElementById('confirmModal').style.display = 'none';
        },

        async confirmDelete() {
            const formData = new FormData();
            formData.append('action', 'delete_customer');
            formData.append('shop_id', this.shopId);
            formData.append('id', this.customerId);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Kunde gelöscht', 'success');
                    setTimeout(() => window.location.href = '?page=customers/customers', 1500);
                } else {
                    this.showToast(data.error || 'Fehler', 'error');
                    this.closeModal();
                }
            } catch (e) {
                console.error('Delete error:', e);
                this.showToast('Fehler beim Löschen', 'error');
                this.closeModal();
            }
        },

        showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast show ${type}`;

            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    };

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => CustomerEdit.init());
</script>