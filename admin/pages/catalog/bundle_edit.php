<?php
/** Katalog - Bundle bearbeiten */
$bundleId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/bundles">Bundles</a> <span>›</span> <span>Bundle
                bearbeiten</span>
        </nav>
        <h1>Bundle bearbeiten</h1>
        <p class="page-subtitle">Bearbeiten Sie die Bundle-Details</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/bundles" class="btn">Abbrechen</a>
        <button class="btn" onclick="BundleForm.saveAsDraft()"><span class="material-symbols-rounded">draft</span> Als
            Entwurf</button>
        <button class="btn btn-primary" onclick="BundleForm.save()"><span class="material-symbols-rounded">save</span>
            Speichern</button>
    </div>
</div>

<div id="loadingOverlay" class="loading-overlay">
    <span class="material-symbols-rounded spinning">sync</span>
    <p>Lade Bundle...</p>
</div>

<form id="bundleForm" class="bundle-form" style="display:none;">
    <input type="hidden" name="id" id="bundleId" value="<?php echo $bundleId; ?>">

    <div class="bundle-grid">
        <!-- Left Column: Main Info -->
        <div class="bundle-main">
            <!-- Bundle Information -->
            <div class="card">
                <div class="card-header">
                    <h3>Bundle-Informationen</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Bundle-Name <span class="required">*</span></label>
                        <input type="text" class="form-input" name="name" id="bundleName" placeholder="z.B. Starter Kit"
                            oninput="BundleForm.autoGenerateSlug()">
                        <p class="form-error" id="errorName"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL-Slug</label>
                        <input type="text" class="form-input" name="slug" id="bundleSlug"
                            placeholder="wird automatisch generiert">
                        <p class="form-hint">Leer lassen für automatische Generierung aus dem Namen</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Beschreibung</label>
                        <textarea class="form-textarea" name="description" id="bundleDescription" rows="4"
                            placeholder="Beschreibung des Bundles..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Bundle Type & Time Period -->
            <div class="card">
                <div class="card-header">
                    <h3>Bundle-Typ</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Typ <span class="required">*</span></label>
                        <div class="bundle-type-cards">
                            <label class="type-card selected" data-type="standard">
                                <input type="radio" name="bundle_type" value="standard" checked>
                                <span class="material-symbols-rounded">inventory_2</span>
                                <strong>Standard</strong>
                                <small>Dauerhaft verfügbares Produktpaket</small>
                            </label>
                            <label class="type-card" data-type="limited">
                                <input type="radio" name="bundle_type" value="limited">
                                <span class="material-symbols-rounded">schedule</span>
                                <strong>Zeitlich begrenzt</strong>
                                <small>Aktionsangebot mit Start- und Enddatum</small>
                            </label>
                        </div>
                    </div>

                    <!-- Date Range (shown only for limited bundles) -->
                    <div id="dateRangeSection" class="date-range-section" style="display:none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Startdatum <span class="required">*</span></label>
                                <input type="date" class="form-input" name="valid_from" id="validFrom"
                                    onchange="BundleForm.validateDates()">
                                <p class="form-error" id="errorValidFrom"></p>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Enddatum <span class="required">*</span></label>
                                <input type="date" class="form-input" name="valid_to" id="validTo"
                                    onchange="BundleForm.validateDates()">
                                <p class="form-error" id="errorValidTo"></p>
                            </div>
                        </div>
                        <p class="form-hint">Das Bundle ist nur während dieses Zeitraums im Shop sichtbar und kaufbar.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Products Selection -->
            <div class="card">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Produkte <span class="required">*</span></h3>
                    <div class="product-summary">
                        <span id="productCount">0</span> Produkte ausgewählt
                    </div>
                </div>
                <div class="card-body">
                    <p class="form-error" id="errorProducts"></p>

                    <!-- Search -->
                    <div class="product-search-bar">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" id="productSearch" placeholder="Produkte suchen..."
                            oninput="BundleForm.debounceProductSearch()">
                    </div>

                    <!-- Products Table -->
                    <div class="products-table-wrapper">
                        <table class="table products-table" id="productsTable">
                            <thead>
                                <tr>
                                    <th style="width:50px;">
                                        <input type="checkbox" id="selectAllProducts"
                                            onchange="BundleForm.toggleSelectAll()">
                                    </th>
                                    <th style="width:60px;">Bild</th>
                                    <th>Produkt</th>
                                    <th style="width:120px;">SKU</th>
                                    <th style="width:100px;">Preis</th>
                                    <th style="width:100px;">Menge</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <tr class="loading-row">
                                    <td colspan="6" style="text-align:center; padding:40px;">
                                        <span class="material-symbols-rounded spinning">sync</span>
                                        <p>Lade Produkte...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="products-pagination" id="productsPagination"></div>

                    <p class="form-hint" style="margin-top:12px;">Mindestens 2 Produkte müssen ausgewählt werden.</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Pricing & Status -->
        <div class="bundle-sidebar">
            <!-- Pricing -->
            <div class="card">
                <div class="card-header">
                    <h3>💰 Preisgestaltung</h3>
                </div>
                <div class="card-body">
                    <!-- Base Currency -->
                    <div class="form-group">
                        <label class="form-label">Basiswährung</label>
                        <select class="form-select" id="baseCurrency" onchange="BundleForm.updatePricing()">
                            <!-- Filled by JS -->
                        </select>
                    </div>

                    <!-- Calculated Base Price -->
                    <div class="price-summary" id="priceSummary">
                        <div class="price-row">
                            <span>Summe Einzelpreise:</span>
                            <strong id="totalProductPrice">0.00 €</strong>
                        </div>
                    </div>

                    <!-- Price Type -->
                    <div class="form-group">
                        <label class="form-label">Preisberechnung <span class="required">*</span></label>
                        <select class="form-select" id="priceType" onchange="BundleForm.updatePriceTypeUI()">
                            <option value="percentage">Prozentrabatt</option>
                            <option value="fixed_discount">Fester Rabatt (Betrag abziehen)</option>
                            <option value="fixed_price">Fester Gesamtpreis</option>
                        </select>
                        <p class="form-hint" id="priceTypeHint">Prozentualer Rabatt auf die Gesamtsumme</p>
                    </div>

                    <!-- Discount/Price Value -->
                    <div class="form-group">
                        <label class="form-label" id="discountLabel">Rabatt (%) <span class="required">*</span></label>
                        <div class="input-with-suffix">
                            <input type="number" class="form-input" id="discountValue" placeholder="15" min="0"
                                step="0.01" oninput="BundleForm.calculateBundlePrice()">
                            <span class="input-suffix" id="discountSuffix">%</span>
                        </div>
                        <p class="form-error" id="errorDiscount"></p>
                    </div>

                    <!-- Final Price Display -->
                    <div class="final-price-box" id="finalPriceBox">
                        <span class="label">Bundle-Preis:</span>
                        <span class="price" id="finalBundlePrice">0.00 €</span>
                        <span class="savings" id="savingsDisplay"></span>
                    </div>
                </div>
            </div>

            <!-- Currency Overrides -->
            <div class="card">
                <div class="card-header">
                    <h3>🌍 Währungspreise</h3>
                    <div class="rounding-options">
                        <label class="form-checkbox">
                            <input type="checkbox" id="enableRounding" onchange="BundleForm.toggleRounding()">
                            <span>Runden auf:</span>
                        </label>
                        <select id="roundingStep" onchange="BundleForm.calculateCurrencyPrices()" disabled>
                            <option value="0.01">0.01</option>
                            <option value="0.05" selected>0.05</option>
                            <option value="0.10">0.10</option>
                            <option value="0.50">0.50</option>
                            <option value="1">1.00</option>
                            <option value="5">5.00</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <p class="form-hint" style="margin-bottom:12px;">
                        Preise werden automatisch umgerechnet. Geben Sie einen Wert ein, um zu überschreiben.
                    </p>

                    <div class="currency-search-box">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" id="currencySearch" placeholder="Währung suchen..."
                            oninput="BundleForm.filterCurrencies()">
                    </div>

                    <div class="currency-table-scroll">
                        <table class="table currency-table">
                            <thead>
                                <tr>
                                    <th>Währung</th>
                                    <th>Berechnet</th>
                                    <th>Überschreibung</th>
                                </tr>
                            </thead>
                            <tbody id="currencyPricesBody">
                                <tr>
                                    <td colspan="3" style="text-align:center; padding:20px; color:var(--text-muted);">
                                        Lade Währungen...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="card">
                <div class="card-header">
                    <h3>Status</h3>
                </div>
                <div class="card-body">
                    <div class="status-options">
                        <label class="status-option">
                            <input type="radio" name="status" value="active">
                            <span class="material-symbols-rounded">visibility</span>
                            <div>
                                <strong>Aktiv</strong>
                                <small>Im Shop sichtbar</small>
                            </div>
                        </label>
                        <label class="status-option">
                            <input type="radio" name="status" value="draft" checked>
                            <span class="material-symbols-rounded">edit_note</span>
                            <div>
                                <strong>Entwurf</strong>
                                <small>Nicht im Shop sichtbar</small>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .loading-overlay {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 80px 40px;
        color: var(--text-muted);
        text-align: center;
    }

    .loading-overlay .spinning {
        font-size: 48px;
        margin-bottom: 16px;
    }

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

    .form-input.is-invalid,
    .form-textarea.is-invalid,
    .form-select.is-invalid {
        border-color: var(--error) !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.15);
    }

    .form-hint {
        color: var(--text-muted);
        font-size: 12px;
        margin-top: 4px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* Layout */
    .bundle-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 1200px) {
        .bundle-grid {
            grid-template-columns: 1fr;
        }
    }

    .bundle-main {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .bundle-sidebar {
        display: flex;
        flex-direction: column;
        gap: 24px;
        position: sticky;
        top: 24px;
    }

    /* Bundle Type Cards */
    .bundle-type-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .type-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 20px;
        background: var(--bg-tertiary);
        border: 2px solid var(--border-color);
        border-radius: var(--radius-md);
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }

    .type-card:hover {
        border-color: var(--accent);
    }

    .type-card.selected {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.1);
    }

    .type-card input {
        display: none;
    }

    .type-card .material-symbols-rounded {
        font-size: 32px;
        color: var(--accent);
    }

    .type-card small {
        color: var(--text-muted);
        font-size: 12px;
    }

    /* Date Range Section */
    .date-range-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    /* Products Search */
    .product-search-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-tertiary);
        padding: 12px 16px;
        border-radius: var(--radius-md);
        margin-bottom: 16px;
    }

    .product-search-bar input {
        flex: 1;
        background: none;
        border: none;
        color: var(--text);
        outline: none;
        font-size: 14px;
    }

    /* Products Table */
    .products-table-wrapper {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
    }

    .products-table {
        margin: 0;
    }

    .products-table thead {
        position: sticky;
        top: 0;
        background: var(--card-bg);
        z-index: 1;
    }

    .products-table .product-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
        background: var(--bg-secondary);
    }

    .products-table .product-img-placeholder {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-secondary);
        border-radius: 6px;
        color: var(--text-muted);
    }

    .products-table tr.selected {
        background: rgba(var(--accent-rgb), 0.08);
    }

    .products-table input[type="number"] {
        width: 70px;
        padding: 6px 8px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text);
        text-align: center;
    }

    .products-table input[type="number"]:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .loading-row td {
        color: var(--text-muted);
    }

    .spinning {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Pagination */
    .products-pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 16px;
    }

    .products-pagination button {
        padding: 8px 16px;
        background: var(--bg-tertiary);
        border: none;
        border-radius: 6px;
        color: var(--text);
        cursor: pointer;
    }

    .products-pagination button:hover {
        background: var(--bg-secondary);
    }

    .products-pagination button.active {
        background: var(--accent);
        color: white;
    }

    /* Price Summary */
    .price-summary {
        background: var(--bg-tertiary);
        padding: 12px 16px;
        border-radius: var(--radius-md);
        margin-bottom: 16px;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Input with suffix */
    .input-with-suffix {
        display: flex;
        align-items: center;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .input-with-suffix .form-input {
        border: none;
        background: none;
        flex: 1;
    }

    .input-suffix {
        padding: 12px 16px;
        background: var(--bg-secondary);
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Final Price Box */
    .final-price-box {
        background: linear-gradient(135deg, var(--accent), var(--primary));
        padding: 20px;
        border-radius: var(--radius-md);
        text-align: center;
        color: white;
    }

    .final-price-box .label {
        display: block;
        font-size: 12px;
        opacity: 0.8;
        margin-bottom: 4px;
    }

    .final-price-box .price {
        display: block;
        font-size: 28px;
        font-weight: 700;
    }

    .final-price-box .savings {
        display: block;
        font-size: 13px;
        margin-top: 8px;
        opacity: 0.9;
    }

    /* Currency Table */
    .currency-search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-tertiary);
        padding: 8px 12px;
        border-radius: var(--radius-md);
        margin-bottom: 12px;
    }

    .currency-search-box input {
        flex: 1;
        background: none;
        border: none;
        color: var(--text);
        outline: none;
        font-size: 13px;
    }

    .currency-table-scroll {
        max-height: 300px;
        overflow-y: auto;
    }

    .currency-table {
        font-size: 13px;
    }

    .currency-table input[type="number"] {
        width: 100%;
        padding: 6px 8px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text);
    }

    .rounding-options {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }

    .rounding-options select {
        padding: 4px 8px;
        font-size: 12px;
    }

    /* Status Options */
    .status-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .status-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--bg-tertiary);
        border: 2px solid var(--border-color);
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.2s;
    }

    .status-option:has(input:checked) {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.1);
    }

    .status-option input {
        display: none;
    }

    .status-option .material-symbols-rounded {
        font-size: 24px;
        color: var(--accent);
    }

    .status-option div {
        flex: 1;
    }

    .status-option strong {
        display: block;
        font-size: 14px;
    }

    .status-option small {
        color: var(--text-muted);
        font-size: 12px;
    }

    /* Toast */
    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 16px 24px;
        border-radius: var(--radius-md);
        color: white;
        font-weight: 500;
        z-index: 2000;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s;
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
    const BundleForm = {
        bundleId: <?php echo $bundleId; ?>,
        products: [],
        selectedProducts: {}, // prices always in shop's default currency
        currencies: [],
        currencyPrices: {},
        baseCurrency: 'EUR',
        baseCurrencySymbol: '€',
        shopDefaultCurrency: 'EUR', // The shop's default currency - product prices are stored in this
        shopDefaultCurrencySymbol: '€',
        shopId: 1,
        currentPage: 1,
        totalPages: 1,
        searchTimeout: null,
        isEditMode: true,

        async init() {
            this.setupEventListeners();
            await this.loadShopCurrency();
            await this.loadCurrencies();
            await this.loadBundleData();
            await this.loadProducts();
            this.updatePricing();

            // Show form after loading
            document.getElementById('loadingOverlay').style.display = 'none';
            document.getElementById('bundleForm').style.display = 'block';
        },

        setupEventListeners() {
            // Bundle type cards
            document.querySelectorAll('.type-card').forEach(card => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    card.querySelector('input').checked = true;
                    this.toggleDateSection();
                });
            });

            // Status options
            document.querySelectorAll('.status-option').forEach(opt => {
                opt.addEventListener('click', () => {
                    opt.querySelector('input').checked = true;
                });
            });
        },

        async loadBundleData() {
            try {
                const res = await fetch(`api/bundles.php?action=get_bundle&shop_id=${this.shopId}&id=${this.bundleId}`);
                const data = await res.json();

                if (data.success) {
                    this.populateForm(data.bundle);
                } else {
                    this.showToast(data.error || 'Bundle nicht gefunden', 'error');
                    setTimeout(() => window.location.href = '?page=catalog/bundles', 2000);
                }
            } catch (e) {
                this.showToast('Fehler beim Laden: ' + e.message, 'error');
            }
        },

        populateForm(bundle) {
            // Basic info
            document.getElementById('bundleName').value = bundle.name;
            document.getElementById('bundleSlug').value = bundle.slug;
            document.getElementById('bundleSlug').dataset.autoGenerated = 'false';
            document.getElementById('bundleDescription').value = bundle.description || '';

            // Bundle type
            const typeRadio = document.querySelector(`input[name="bundle_type"][value="${bundle.bundle_type}"]`);
            if (typeRadio) {
                typeRadio.checked = true;
                document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
                typeRadio.closest('.type-card').classList.add('selected');
            }
            this.toggleDateSection();

            // Dates
            if (bundle.valid_from) document.getElementById('validFrom').value = bundle.valid_from;
            if (bundle.valid_to) document.getElementById('validTo').value = bundle.valid_to;

            // Pricing
            document.getElementById('baseCurrency').value = bundle.base_currency || 'EUR';
            document.getElementById('priceType').value = bundle.price_type || 'percentage';
            document.getElementById('discountValue').value = bundle.discount_value || '';
            this.updatePriceTypeUI();

            // Status
            const statusRadio = document.querySelector(`input[name="status"][value="${bundle.status}"]`);
            if (statusRadio) statusRadio.checked = true;

            // Products
            bundle.products.forEach(p => {
                this.selectedProducts[p.product_id] = {
                    id: p.product_id,
                    name: p.name,
                    sku: p.sku,
                    price: parseFloat(p.price),
                    quantity: parseInt(p.quantity) || 1
                };
            });
            this.updateProductCount();

            // Currency overrides
            this.currencyPrices = bundle.currency_prices || {};

            // Apply currency overrides to inputs after currencies are loaded
            setTimeout(() => {
                Object.entries(this.currencyPrices).forEach(([code, price]) => {
                    const input = document.getElementById(`override_${code}`);
                    if (input) input.value = price;
                });
            }, 500);
        },

        toggleDateSection() {
            const bundleType = document.querySelector('input[name="bundle_type"]:checked').value;
            const dateSection = document.getElementById('dateRangeSection');
            dateSection.style.display = bundleType === 'limited' ? 'block' : 'none';
        },

        validateDates() {
            const validFrom = document.getElementById('validFrom').value;
            const validTo = document.getElementById('validTo').value;
            const errorTo = document.getElementById('errorValidTo');

            if (validFrom && validTo && validTo < validFrom) {
                errorTo.textContent = 'Enddatum darf nicht vor dem Startdatum liegen';
                errorTo.classList.add('show');
                document.getElementById('validTo').classList.add('is-invalid');
                return false;
            } else {
                errorTo.classList.remove('show');
                document.getElementById('validTo').classList.remove('is-invalid');
                return true;
            }
        },

        autoGenerateSlug() {
            const name = document.getElementById('bundleName').value;
            const slugField = document.getElementById('bundleSlug');

            if (slugField.dataset.autoGenerated === 'true') {
                const slug = this.generateSlug(name);
                slugField.value = slug;
            }
        },

        generateSlug(text) {
            return text.toLowerCase()
                .replace(/[äÄ]/g, 'ae')
                .replace(/[öÖ]/g, 'oe')
                .replace(/[üÜ]/g, 'ue')
                .replace(/ß/g, 'ss')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },

        // ========== Currency Loading ==========
        async loadShopCurrency() {
            try {
                const res = await fetch(`api/bundles.php?action=get_shop_currency&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    // Store shop's default currency - product prices are in this currency
                    this.shopDefaultCurrency = data.currency.code;
                    this.shopDefaultCurrencySymbol = data.currency.symbol;
                    // Initially, base currency = shop default
                    this.baseCurrency = data.currency.code;
                    this.baseCurrencySymbol = data.currency.symbol;
                }
            } catch (e) {
                console.error('Error loading shop currency:', e);
            }
        },

        async loadCurrencies() {
            try {
                const res = await fetch(`api/bundles.php?action=get_currencies&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    this.currencies = data.currencies;
                    this.renderBaseCurrencySelect();
                    this.renderCurrencyTable();
                }
            } catch (e) {
                console.error('Error loading currencies:', e);
            }
        },

        renderBaseCurrencySelect() {
            const select = document.getElementById('baseCurrency');
            select.innerHTML = this.currencies.map(c =>
                `<option value="${c.code}" ${c.is_default == 1 ? 'selected' : ''}>${c.symbol} ${c.code} - ${c.name}</option>`
            ).join('');

            this.baseCurrency = select.value;
            const selected = this.currencies.find(c => c.code === this.baseCurrency);
            if (selected) this.baseCurrencySymbol = selected.symbol;
        },

        renderCurrencyTable() {
            const tbody = document.getElementById('currencyPricesBody');

            tbody.innerHTML = this.currencies.map(c => `
            <tr data-currency="${c.code}" style="${c.is_default == 1 ? 'display:none;' : ''}">
                <td><strong>${c.symbol}</strong> ${c.code}</td>
                <td class="calculated-price" id="calc_${c.code}">-</td>
                <td>
                    <input type="number" step="0.01" min="0" 
                           placeholder="Auto" 
                           id="override_${c.code}"
                           value="${this.currencyPrices[c.code] || ''}"
                           onchange="BundleForm.setCurrencyOverride('${c.code}', this.value)">
                </td>
            </tr>
        `).join('');
        },

        filterCurrencies() {
            const search = document.getElementById('currencySearch').value.toLowerCase();
            const rows = document.querySelectorAll('#currencyPricesBody tr');

            rows.forEach(row => {
                const currency = row.dataset.currency;
                if (!currency) return;

                const isDefault = this.currencies.find(c => c.code === currency)?.is_default == 1;
                const matches = currency.toLowerCase().includes(search) ||
                    this.currencies.find(c => c.code === currency)?.name?.toLowerCase().includes(search);

                row.style.display = (isDefault || !matches) ? 'none' : '';
            });
        },

        setCurrencyOverride(code, value) {
            if (value === '' || value === null) {
                delete this.currencyPrices[code];
            } else {
                this.currencyPrices[code] = parseFloat(value);
            }
        },

        toggleRounding() {
            const enabled = document.getElementById('enableRounding').checked;
            document.getElementById('roundingStep').disabled = !enabled;
            this.calculateCurrencyPrices();
        },

        // ========== Products Loading ==========
        async loadProducts(page = 1) {
            this.currentPage = page;
            const search = document.getElementById('productSearch')?.value || '';
            const tbody = document.getElementById('productsTableBody');

            tbody.innerHTML = `
            <tr class="loading-row">
                <td colspan="6" style="text-align:center; padding:40px;">
                    <span class="material-symbols-rounded spinning">sync</span>
                    <p>Lade Produkte...</p>
                </td>
            </tr>
        `;

            try {
                const res = await fetch(`api/bundles.php?action=get_products&shop_id=${this.shopId}&page=${page}&search=${encodeURIComponent(search)}`);
                const data = await res.json();

                if (data.success) {
                    this.products = data.products;
                    this.totalPages = data.pagination.total_pages;
                    this.renderProductsTable();
                    this.renderPagination();
                }
            } catch (e) {
                console.error('Error loading products:', e);
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:40px; color:var(--error);">Fehler beim Laden</td></tr>';
            }
        },

        renderProductsTable() {
            const tbody = document.getElementById('productsTableBody');

            if (this.products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">Keine Produkte gefunden</td></tr>';
                return;
            }

            tbody.innerHTML = this.products.map(product => {
                const isSelected = !!this.selectedProducts[product.id];
                const quantity = isSelected ? this.selectedProducts[product.id].quantity : 1;

                return `
                <tr data-product-id="${product.id}" class="${isSelected ? 'selected' : ''}">
                    <td>
                        <input type="checkbox" 
                               ${isSelected ? 'checked' : ''} 
                               onchange="BundleForm.toggleProduct(${product.id})">
                    </td>
                    <td>
                        ${product.thumbnail
                        ? `<img src="${product.thumbnail}" alt="" class="product-img">`
                        : '<div class="product-img-placeholder"><span class="material-symbols-rounded">inventory_2</span></div>'}
                    </td>
                    <td>
                        <strong>${this.escapeHtml(product.name)}</strong>
                        <br><small style="color:var(--text-muted);">${product.type}</small>
                    </td>
                    <td>${this.escapeHtml(product.sku)}</td>
                    <td>${parseFloat(product.price).toFixed(2)} ${this.baseCurrencySymbol}</td>
                    <td>
                        <input type="number" 
                               value="${quantity}" 
                               min="1" 
                               ${isSelected ? '' : 'disabled'}
                               oninput="BundleForm.updateProductQuantity(${product.id}, this.value)">
                    </td>
                </tr>
            `;
            }).join('');
        },

        renderPagination() {
            const container = document.getElementById('productsPagination');
            if (this.totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';
            for (let i = 1; i <= this.totalPages; i++) {
                html += `<button class="${i === this.currentPage ? 'active' : ''}" onclick="BundleForm.loadProducts(${i})">${i}</button>`;
            }
            container.innerHTML = html;
        },

        debounceProductSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.loadProducts(1), 300);
        },

        toggleSelectAll() {
            const checked = document.getElementById('selectAllProducts').checked;
            this.products.forEach(p => {
                if (checked) {
                    this.selectedProducts[p.id] = {
                        id: p.id,
                        name: p.name,
                        sku: p.sku,
                        price: parseFloat(p.price),
                        quantity: this.selectedProducts[p.id]?.quantity || 1
                    };
                } else {
                    delete this.selectedProducts[p.id];
                }
            });
            this.renderProductsTable();
            this.updateProductCount();
            this.updatePricing();
        },

        toggleProduct(productId) {
            const product = this.products.find(p => p.id === productId);
            if (!product) return;

            if (this.selectedProducts[productId]) {
                delete this.selectedProducts[productId];
            } else {
                this.selectedProducts[productId] = {
                    id: product.id,
                    name: product.name,
                    sku: product.sku,
                    price: parseFloat(product.price),
                    quantity: 1
                };
            }

            this.renderProductsTable();
            this.updateProductCount();
            this.updatePricing();
        },

        updateProductQuantity(productId, quantity) {
            if (this.selectedProducts[productId]) {
                this.selectedProducts[productId].quantity = Math.max(1, parseInt(quantity) || 1);
                this.updatePricing();
            }
        },

        updateProductCount() {
            const count = Object.keys(this.selectedProducts).length;
            const totalItems = Object.values(this.selectedProducts).reduce((sum, p) => sum + (p.quantity || 1), 0);
            document.getElementById('productCount').textContent = `${count} Produkte (${totalItems} Artikel)`;
        },

        // ========== Pricing ==========

        // Get exchange rate for a currency
        getExchangeRate(currencyCode) {
            const currency = this.currencies.find(c => c.code === currencyCode);
            return currency ? parseFloat(currency.exchange_rate) || 1 : 1;
        },

        // Convert amount from one currency to another
        convertCurrency(amount, fromCurrency, toCurrency) {
            if (fromCurrency === toCurrency) return amount;
            const fromRate = this.getExchangeRate(fromCurrency);
            const toRate = this.getExchangeRate(toCurrency);
            // Convert: amount * (toRate / fromRate)
            return amount * (toRate / fromRate);
        },

        updatePricing() {
            const newBaseCurrency = document.getElementById('baseCurrency').value;
            const newBaseCurrencyData = this.currencies.find(c => c.code === newBaseCurrency);

            if (newBaseCurrencyData) {
                this.baseCurrency = newBaseCurrencyData.code;
                this.baseCurrencySymbol = newBaseCurrencyData.symbol;
            }

            // Calculate total product price in SHOP'S DEFAULT currency first
            let totalProductPriceInDefault = 0;
            Object.values(this.selectedProducts).forEach(p => {
                totalProductPriceInDefault += p.price * (p.quantity || 1);
            });

            // Convert to selected base currency
            const totalProductPrice = this.convertCurrency(
                totalProductPriceInDefault,
                this.shopDefaultCurrency,
                this.baseCurrency
            );

            document.getElementById('totalProductPrice').textContent =
                `${totalProductPrice.toFixed(2)} ${this.baseCurrencySymbol}`;

            // Update the currency table visibility - hide the base currency row
            this.updateCurrencyTableVisibility();

            this.calculateBundlePrice();
        },

        updateCurrencyTableVisibility() {
            // Show all currencies except the currently selected base currency
            const rows = document.querySelectorAll('#currencyPricesBody tr');
            rows.forEach(row => {
                const currency = row.dataset.currency;
                if (!currency) return;
                row.style.display = currency === this.baseCurrency ? 'none' : '';
            });
        },

        updatePriceTypeUI() {
            const priceType = document.getElementById('priceType').value;
            const label = document.getElementById('discountLabel');
            const suffix = document.getElementById('discountSuffix');
            const hint = document.getElementById('priceTypeHint');
            const input = document.getElementById('discountValue');

            switch (priceType) {
                case 'percentage':
                    label.innerHTML = 'Rabatt (%) <span class="required">*</span>';
                    suffix.textContent = '%';
                    hint.textContent = 'Prozentualer Rabatt auf die Gesamtsumme';
                    input.placeholder = '15';
                    break;
                case 'fixed_discount':
                    label.innerHTML = `Rabatt (${this.baseCurrencySymbol}) <span class="required">*</span>`;
                    suffix.textContent = this.baseCurrencySymbol;
                    hint.textContent = 'Fester Betrag, der von der Summe abgezogen wird';
                    input.placeholder = '10.00';
                    break;
                case 'fixed_price':
                    label.innerHTML = `Gesamtpreis (${this.baseCurrencySymbol}) <span class="required">*</span>`;
                    suffix.textContent = this.baseCurrencySymbol;
                    hint.textContent = 'Fester Preis für das gesamte Bundle';
                    input.placeholder = '49.99';
                    break;
            }

            this.calculateBundlePrice();
        },

        calculateBundlePrice() {
            const priceType = document.getElementById('priceType').value;
            const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;

            // Calculate total product price in SHOP'S DEFAULT currency first
            let totalProductPriceInDefault = 0;
            Object.values(this.selectedProducts).forEach(p => {
                totalProductPriceInDefault += p.price * (p.quantity || 1);
            });

            // Convert to selected base currency for display
            const totalProductPrice = this.convertCurrency(
                totalProductPriceInDefault,
                this.shopDefaultCurrency,
                this.baseCurrency
            );

            let bundlePrice = 0;
            let savings = 0;

            switch (priceType) {
                case 'percentage':
                    savings = totalProductPrice * (discountValue / 100);
                    bundlePrice = totalProductPrice - savings;
                    break;
                case 'fixed_discount':
                    savings = discountValue;
                    bundlePrice = Math.max(0, totalProductPrice - discountValue);
                    break;
                case 'fixed_price':
                    bundlePrice = discountValue;
                    savings = totalProductPrice - discountValue;
                    break;
            }

            document.getElementById('finalBundlePrice').textContent =
                `${bundlePrice.toFixed(2)} ${this.baseCurrencySymbol}`;

            if (savings > 0 && totalProductPrice > 0) {
                const savingsPercent = (savings / totalProductPrice * 100).toFixed(0);
                document.getElementById('savingsDisplay').textContent =
                    `Sie sparen ${savings.toFixed(2)} ${this.baseCurrencySymbol} (${savingsPercent}%)`;
            } else {
                document.getElementById('savingsDisplay').textContent = '';
            }

            this.calculateCurrencyPrices(bundlePrice);
        },

        calculateCurrencyPrices(bundlePriceInBaseCurrency = null) {
            const priceType = document.getElementById('priceType').value;
            const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;
            const enableRounding = document.getElementById('enableRounding').checked;
            const roundingStep = parseFloat(document.getElementById('roundingStep').value) || 0.01;

            // If bundle price not passed, calculate it
            if (bundlePriceInBaseCurrency === null) {
                let totalProductPriceInDefault = 0;
                Object.values(this.selectedProducts).forEach(p => {
                    totalProductPriceInDefault += p.price * (p.quantity || 1);
                });

                const totalProductPrice = this.convertCurrency(
                    totalProductPriceInDefault,
                    this.shopDefaultCurrency,
                    this.baseCurrency
                );

                switch (priceType) {
                    case 'percentage':
                        bundlePriceInBaseCurrency = totalProductPrice * (1 - discountValue / 100);
                        break;
                    case 'fixed_discount':
                        bundlePriceInBaseCurrency = Math.max(0, totalProductPrice - discountValue);
                        break;
                    case 'fixed_price':
                        bundlePriceInBaseCurrency = discountValue;
                        break;
                }
            }

            // Calculate for each currency by converting from base currency
            this.currencies.forEach(c => {
                // Skip the currently selected base currency
                if (c.code === this.baseCurrency) return;

                const calcEl = document.getElementById(`calc_${c.code}`);
                if (!calcEl) return;

                // Convert from base currency to this currency
                let calculatedPrice = this.convertCurrency(
                    bundlePriceInBaseCurrency,
                    this.baseCurrency,
                    c.code
                );

                if (enableRounding) {
                    calculatedPrice = Math.round(calculatedPrice / roundingStep) * roundingStep;
                }

                calcEl.textContent = `${calculatedPrice.toFixed(2)} ${c.symbol}`;
            });
        },

        // ========== Validation & Saving ==========
        clearErrors() {
            document.querySelectorAll('.form-error').forEach(e => {
                e.textContent = '';
                e.classList.remove('show');
            });
            document.querySelectorAll('.is-invalid').forEach(e => {
                e.classList.remove('is-invalid');
            });
        },

        showError(fieldId, message) {
            const errorEl = document.getElementById(fieldId);
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.add('show');
            }
        },

        validate() {
            this.clearErrors();
            let isValid = true;

            // Name
            const name = document.getElementById('bundleName').value.trim();
            if (!name) {
                this.showError('errorName', 'Bundle-Name ist erforderlich');
                document.getElementById('bundleName').classList.add('is-invalid');
                isValid = false;
            }

            // Products
            const productCount = Object.keys(this.selectedProducts).length;
            if (productCount < 2) {
                this.showError('errorProducts', 'Mindestens 2 Produkte müssen ausgewählt werden');
                isValid = false;
            }

            // Discount Value
            const priceType = document.getElementById('priceType').value;
            const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;

            if (priceType === 'percentage') {
                if (discountValue <= 0 || discountValue > 100) {
                    this.showError('errorDiscount', 'Prozentrabatt muss zwischen 1 und 100 liegen');
                    document.getElementById('discountValue').classList.add('is-invalid');
                    isValid = false;
                }
            } else if (discountValue <= 0) {
                this.showError('errorDiscount', 'Wert muss größer als 0 sein');
                document.getElementById('discountValue').classList.add('is-invalid');
                isValid = false;
            }

            // Dates for limited bundles
            const bundleType = document.querySelector('input[name="bundle_type"]:checked').value;
            if (bundleType === 'limited') {
                const validFrom = document.getElementById('validFrom').value;
                const validTo = document.getElementById('validTo').value;

                if (!validFrom) {
                    this.showError('errorValidFrom', 'Startdatum ist erforderlich');
                    document.getElementById('validFrom').classList.add('is-invalid');
                    isValid = false;
                }
                if (!validTo) {
                    this.showError('errorValidTo', 'Enddatum ist erforderlich');
                    document.getElementById('validTo').classList.add('is-invalid');
                    isValid = false;
                }
                if (validFrom && validTo && validTo < validFrom) {
                    this.showError('errorValidTo', 'Enddatum darf nicht vor dem Startdatum liegen');
                    document.getElementById('validTo').classList.add('is-invalid');
                    isValid = false;
                }
            }

            return isValid;
        },

        async save(status = null) {
            if (!this.validate()) {
                this.showToast('Bitte korrigieren Sie die Fehler', 'error');
                return;
            }

            // Use current status if not specified
            if (status === null) {
                status = document.querySelector('input[name="status"]:checked').value;
            }

            const bundleData = this.collectFormData(status);

            try {
                const res = await fetch('api/bundles.php?action=save_bundle&shop_id=' + this.shopId, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(bundleData)
                });

                const data = await res.json();

                if (data.success) {
                    this.showToast('Bundle erfolgreich gespeichert!', 'success');
                    setTimeout(() => {
                        window.location.href = '?page=catalog/bundles';
                    }, 1500);
                } else if (data.errors) {
                    Object.entries(data.errors).forEach(([field, msg]) => {
                        this.showError('error' + field.charAt(0).toUpperCase() + field.slice(1), msg);
                    });
                    this.showToast('Bitte korrigieren Sie die Fehler', 'error');
                } else {
                    this.showToast(data.error || 'Speichern fehlgeschlagen', 'error');
                }
            } catch (e) {
                this.showToast('Verbindungsfehler: ' + e.message, 'error');
            }
        },

        saveAsDraft() {
            this.save('draft');
        },

        collectFormData(status) {
            const products = Object.values(this.selectedProducts).map(p => ({
                id: p.id,
                quantity: p.quantity || 1
            }));

            return {
                id: this.bundleId,
                name: document.getElementById('bundleName').value.trim(),
                slug: document.getElementById('bundleSlug').value.trim(),
                description: document.getElementById('bundleDescription').value.trim(),
                bundle_type: document.querySelector('input[name="bundle_type"]:checked').value,
                price_type: document.getElementById('priceType').value,
                discount_value: parseFloat(document.getElementById('discountValue').value) || 0,
                base_currency: document.getElementById('baseCurrency').value,
                valid_from: document.getElementById('validFrom').value || null,
                valid_to: document.getElementById('validTo').value || null,
                status: status,
                products: products,
                currency_prices: this.currencyPrices
            };
        },

        // ========== Utilities ==========
        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type + ' show';
            setTimeout(() => toast.classList.remove('show'), 4000);
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => BundleForm.init());
</script>