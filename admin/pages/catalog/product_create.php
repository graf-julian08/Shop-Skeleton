<?php /** Katalog - Neues Produkt erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/products">Produkte</a> <span>›</span> <span>Neues Produkt</span>
        </nav>
        <h1>Neues Produkt erstellen</h1>
        <p class="page-subtitle">Fügen Sie ein neues Produkt zu Ihrem Katalog hinzu</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/products" class="btn">Abbrechen</a>
        <button class="btn" onclick="ProductForm.saveAsDraft()"><span class="material-symbols-rounded">draft</span> Als
            Entwurf</button>
        <button class="btn btn-primary" onclick="ProductForm.save()"><span
                class="material-symbols-rounded">publish</span> Veröffentlichen</button>
    </div>
</div>

<!-- Step Navigation -->
<div class="step-nav" id="stepNav">
    <div class="step active" data-step="1"><span class="step-number">1</span><span class="step-label">Produkttyp</span>
    </div>
    <div class="step" data-step="2"><span class="step-number">2</span><span class="step-label">Grunddaten</span></div>
    <div class="step" data-step="3"><span class="step-number">3</span><span class="step-label">Variationen</span></div>
    <div class="step" data-step="4"><span class="step-number">4</span><span class="step-label">Preise</span></div>
    <div class="step" data-step="5"><span class="step-number">5</span><span class="step-label">Inventar</span></div>
    <div class="step" data-step="6"><span class="step-number">6</span><span class="step-label">Bilder</span></div>
    <div class="step" data-step="7"><span class="step-number">7</span><span class="step-label">SEO</span></div>
</div>

<form id="productForm" class="product-form">
    <input type="hidden" name="id" value="">

    <!-- Step 1: Produkttyp -->
    <div class="step-content active" data-step-content="1">
        <div class="card">
            <div class="card-header">
                <h3>Produkttyp wählen</h3>
            </div>
            <div class="card-body">
                <p style="margin-bottom:24px;color:var(--text-muted);">Wählen Sie die Art des Produkts. Dies beeinflusst
                    die verfügbaren Optionen.</p>
                <div class="product-type-grid">
                    <label class="product-type-card selected" data-type="simple">
                        <input type="radio" name="type" value="simple" checked>
                        <span class="material-symbols-rounded">inventory_2</span>
                        <strong>Physisches Produkt</strong>
                        <small>Reguläres Produkt mit Versand</small>
                    </label>
                    <label class="product-type-card" data-type="digital">
                        <input type="radio" name="type" value="digital">
                        <span class="material-symbols-rounded">cloud_download</span>
                        <strong>Digitales Produkt</strong>
                        <small>Download, E-Book, Software</small>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Grunddaten -->
    <div class="step-content" data-step-content="2">
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Grunddaten</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Produktname <span class="required">*</span></label>
                        <input type="text" class="form-input" name="name" id="productName"
                            placeholder="z.B. Premium Lederjacke" required>
                        <p class="form-error" id="errorName"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SKU (Artikelnummer) <span class="required">*</span></label>
                        <input type="text" class="form-input" name="sku" id="productSku" placeholder="z.B. LJ-001"
                            required>
                        <p class="form-error" id="errorSku"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL-Slug</label>
                        <input type="text" class="form-input" name="slug" id="productSlug"
                            placeholder="wird automatisch generiert">
                        <p class="form-hint">Leer lassen für automatische Generierung</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kurzbeschreibung</label>
                        <textarea class="form-textarea" name="short_description" rows="2"
                            placeholder="Kurze Beschreibung für Listen..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Beschreibung</label>
                        <textarea class="form-textarea" name="description" rows="5"
                            placeholder="Ausführliche Produktbeschreibung..."></textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Kategorien & Status</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Kategorien <span class="required">*</span></label>
                        <div class="category-checkboxes" id="categoryCheckboxes">
                            <!-- Filled by JS -->
                        </div>
                        <p class="form-error" id="errorCategories"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sichtbarkeit</label>
                        <div class="toggle-group">
                            <label class="toggle-label"><input type="checkbox" name="is_visible" value="1" checked> Im
                                Shop anzeigen</label>
                            <label class="toggle-label"><input type="checkbox" name="is_featured" value="1"> Als
                                Featured markieren</label>
                            <label class="toggle-label"><input type="checkbox" name="is_new" value="1"> Als Neu
                                markieren</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 4: Preise -->
    <div class="step-content" data-step-content="4">
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <h3>💰 Preisinformationen</h3>
                    <div class="currency-selector">
                        <label>Basiswährung:</label>
                        <select id="baseCurrencySelect" onchange="ProductForm.updateCurrencyLabels()">
                            <option value="USD">$ USD</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Regulärer Preis (<span id="priceSymbol">$</span>) <span
                                    class="required">*</span></label>
                            <input type="number" class="form-input" name="price" id="productPrice" placeholder="0.00"
                                step="0.01" min="0" required
                                oninput="ProductForm.calculateCurrencyPreview(); ProductForm.calculateMargin()">
                            <p class="form-error" id="errorPrice"></p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sonderpreis (<span id="specialSymbol">$</span>)</label>
                            <input type="number" class="form-input" name="special_price" id="specialPrice"
                                placeholder="0.00" step="0.01" min="0"
                                oninput="ProductForm.calculateCurrencyPreview(); ProductForm.calculateMargin()">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Sonderpreis von</label>
                            <input type="date" class="form-input" name="special_price_from" id="specialFrom"
                                onchange="ProductForm.validateSpecialPriceDates()">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sonderpreis bis</label>
                            <input type="date" class="form-input" name="special_price_to" id="specialTo"
                                onchange="ProductForm.validateSpecialPriceDates()">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Kosten & Steuern</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Einkaufspreis (<span id="costSymbol">$</span>)</label>
                        <input type="number" class="form-input" name="cost_price" id="costPrice" placeholder="0.00"
                            step="0.01" min="0" oninput="ProductForm.calculateMargin()">
                        <p class="form-hint">Für Margenberechnung (wird Kunden nicht angezeigt)</p>
                    </div>
                    <!-- Margin Display -->
                    <div id="marginDisplay" class="margin-display"
                        style="display:none; padding:12px; background:var(--glass-bg); border-radius:8px; margin-bottom:16px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-weight:500;">Marge:</span>
                            <div style="text-align:right;">
                                <span id="marginAmount" style="font-size:18px; font-weight:600;">€0.00</span>
                                <span id="marginPercent"
                                    style="margin-left:8px; padding:4px 8px; border-radius:4px; font-weight:500; font-size:13px;">0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Steuerklasse</label>
                        <select class="form-select" name="tax_class_id" id="taxClassSelect">
                            <!-- Wird dynamisch geladen -->
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Währungspreise Section - NUR für Produkte OHNE Varianten -->
        <div class="card" id="currencyPricesCard" style="margin-top:24px;">
            <div class="card-header">
                <h3>🌍 Währungspreise</h3>
                <div class="rounding-options">
                    <label class="form-checkbox">
                        <input type="checkbox" id="enableRounding" onchange="ProductForm.toggleRounding()">
                        <span>Runden auf:</span>
                    </label>
                    <select id="roundingStep" onchange="ProductForm.calculateCurrencyPreview()" disabled>
                        <option value="0.01">0.01</option>
                        <option value="0.05" selected>0.05 (CHF)</option>
                        <option value="0.10">0.10</option>
                        <option value="0.50">0.50</option>
                        <option value="1">1.00</option>
                        <option value="5">5.00</option>
                        <option value="10">10.00</option>
                        <option value="100">100.00</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <p class="form-hint" style="margin-bottom:16px;">
                    Preise werden automatisch umgerechnet. Geben Sie einen Überschreibungspreis ein, um einen festen
                    Wert zu setzen.
                </p>

                <div id="currencyPricesTable">
                    <div class="currency-search">
                        <span class="material-symbols-rounded">search</span>
                        <input type="text" id="currencySearch" placeholder="Währung suchen..."
                            oninput="ProductForm.filterCurrencies()">
                    </div>
                    <span class="currency-count"><span id="currencyCount">135</span> Währungen</span>
                </div>
                <div class="currency-table-scroll">
                    <table class="data-table currency-table">
                        <thead>
                            <tr>
                                <th>Währung</th>
                                <th>Berechnet</th>
                                <th>Überschreibung</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="currencyPricesBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Varianten-Preise Section - NUR für Produkte MIT Varianten -->
        <div class="card" id="variantPricesCard" style="margin-top:24px; display:none;">
            <div class="card-header">
                <h3>💎 Varianten-Preise mit Währungen</h3>
                <div class="rounding-options">
                    <label class="form-checkbox">
                        <input type="checkbox" id="variantEnableRounding"
                            onchange="ProductForm.toggleVariantRounding()">
                        <span>Runden auf:</span>
                    </label>
                    <select id="variantRoundingStep" onchange="ProductForm.refreshVariantPrices()" disabled>
                        <option value="0.01">0.01</option>
                        <option value="0.05" selected>0.05 (CHF)</option>
                        <option value="0.10">0.10</option>
                        <option value="0.50">0.50</option>
                        <option value="1">1.00</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <p class="form-hint" style="margin-bottom:16px;">
                    Klicken Sie auf eine Variante, um die Währungspreise anzupassen. Preise werden automatisch aus dem
                    Hauptpreis berechnet.
                </p>

                <!-- Varianten-Liste mit expandierbaren Währungs-Überschreibungen -->
                <div id="variantPricesAccordions" style="display:flex; flex-direction:column; gap:8px;">
                    <!-- Dynamisch generiert -->
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Step 5: Inventar -->
    <div class="step-content" data-step-content="5">
        <!-- Info-Banner wenn Varianten existieren -->
        <div id="inventoryVariantsBanner" class="info-banner"
            style="display:none; margin-bottom:24px; padding:16px; background:var(--glass-bg); border-radius:12px; border-left:4px solid var(--primary); align-items:center; gap:12px;">
            <span class="material-symbols-rounded" style="color:var(--primary);">info</span>
            <span>Dieses Produkt hat <strong id="inventoryVariantCount">0</strong> Varianten. Lagerbestand und Gewicht
                werden pro Variante verwaltet.</span>
        </div>

        <!-- Einfache Ansicht (ohne Varianten) -->
        <div id="inventorySimple" class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Lagerbestand</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="toggle-label">
                            <input type="checkbox" name="manage_stock" value="1" id="manageStock" checked
                                onchange="ProductForm.toggleStockFields()">
                            Bestand verfolgen
                        </label>
                        <p class="form-hint">Wenn deaktiviert, wird der Bestand nicht verfolgt (unbegrenzt)</p>
                    </div>
                    <div id="stockFields">
                        <div class="form-group">
                            <label class="form-label">Lagermenge</label>
                            <input type="number" class="form-input" name="quantity" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mindestbestand (Warnung)</label>
                            <input type="number" class="form-input" name="low_stock_threshold" value="5" min="0">
                            <p class="form-hint">Sie werden benachrichtigt wenn der Bestand unter diesen Wert fällt</p>
                        </div>
                        <div class="form-group">
                            <label class="toggle-label">
                                <input type="checkbox" name="allow_backorders" value="1">
                                Rückbestellungen erlauben
                            </label>
                            <p class="form-hint">Kunden können bestellen auch wenn nicht auf Lager</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" id="shippingCard">
                <div class="card-header">
                    <h3>Versand</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Gewicht (kg)</label>
                        <input type="number" class="form-input" name="weight" placeholder="0.0" step="0.1" min="0">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Länge (cm)</label>
                            <input type="number" class="form-input" name="length" placeholder="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Breite (cm)</label>
                            <input type="number" class="form-input" name="width" placeholder="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Höhe (cm)</label>
                            <input type="number" class="form-input" name="height" placeholder="0" min="0">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Digital Product Options -->
            <div class="card" id="digitalCard" style="display:none;">
                <div class="card-header">
                    <h3>📥 Download-Dateien</h3>
                </div>
                <div class="card-body">
                    <p class="form-hint" style="margin-bottom:16px;">
                        Fügen Sie Download-Links oder Dateien hinzu, die Kunden nach dem Kauf erhalten.
                    </p>

                    <!-- Download Files List -->
                    <div id="downloadFilesList"
                        style="display:flex; flex-direction:column; gap:12px; margin-bottom:16px;">
                        <!-- Dynamically generated -->
                    </div>

                    <button type="button" class="btn btn-sm" onclick="ProductForm.addDownloadFile()">
                        <span class="material-symbols-rounded">add</span> Download hinzufügen
                    </button>

                    <hr style="margin:24px 0; border-color:var(--border-color);">

                    <h4 style="font-size:15px; margin-bottom:16px;">Einschränkungen</h4>

                    <div class="form-row" style="gap:24px;">
                        <div class="form-group">
                            <label class="form-label">Download-Limit pro Kunde</label>
                            <input type="number" class="form-input" name="download_limit" id="downloadLimit" value="0"
                                min="0" style="width:120px;">
                            <p class="form-hint">0 = Unbegrenzte Downloads</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Link läuft ab nach (Tage)</label>
                            <input type="number" class="form-input" name="download_expiry_days" id="downloadExpiry"
                                value="0" min="0" style="width:120px;">
                            <p class="form-hint">0 = Kein Ablauf</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Varianten-Ansicht (mit Varianten) -->
        <div id="inventoryVariants" style="display:none;">
            <div class="card">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Varianten-Inventar</h3>
                    <div style="display:flex; gap:8px;">
                        <button type="button" class="btn btn-sm" onclick="ProductForm.setAllVariantStock()"
                            title="Für alle setzen">
                            <span class="material-symbols-rounded">inventory_2</span> Alle setzen
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                        <table class="table" id="inventoryVariantsTable">
                            <thead style="position:sticky; top:0; background:var(--card-bg); z-index:1;">
                                <tr>
                                    <th>Variante</th>
                                    <th style="width:100px;">Lagerbestand</th>
                                    <th style="width:100px;">Gewicht (kg)</th>
                                    <th style="width:80px;">Länge</th>
                                    <th style="width:80px;">Breite</th>
                                    <th style="width:80px;">Höhe</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryVariantsBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Inventar Bulk Modal -->
        <div id="inventoryBulkModal" class="modal"
            style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center;">
            <div class="modal-content"
                style="background:var(--card-bg); border-radius:16px; padding:24px; max-width:500px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.3);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="margin:0;">Inventar für alle <span id="inventoryModalCount">0</span> Varianten setzen
                    </h3>
                    <button type="button" class="btn btn-icon" onclick="ProductForm.closeInventoryModal()">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>
                <div class="form-row"
                    style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div class="form-group">
                        <label class="form-label">Lagerbestand</label>
                        <input type="number" id="bulkStock" class="form-input" min="0"
                            placeholder="Leer = nicht ändern">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gewicht (kg)</label>
                        <input type="number" id="bulkWeight" class="form-input" step="0.1" min="0"
                            placeholder="Leer = nicht ändern">
                    </div>
                </div>
                <div class="form-row"
                    style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div class="form-group">
                        <label class="form-label">Länge (cm)</label>
                        <input type="number" id="bulkLength" class="form-input" min="0" placeholder="Leer">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Breite (cm)</label>
                        <input type="number" id="bulkWidth" class="form-input" min="0" placeholder="Leer">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Höhe (cm)</label>
                        <input type="number" id="bulkHeight" class="form-input" min="0" placeholder="Leer">
                    </div>
                </div>
                <p class="form-hint" style="margin-bottom:20px;">Leere Felder werden nicht geändert.</p>
                <div style="display:flex; justify-content:flex-end; gap:12px;">
                    <button type="button" class="btn" onclick="ProductForm.closeInventoryModal()">Abbrechen</button>
                    <button type="button" class="btn btn-primary" onclick="ProductForm.applyBulkInventory()">
                        <span class="material-symbols-rounded">check</span> Anwenden
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 6: Bilder -->
    <div class="step-content" data-step-content="6">
        <!-- Info-Banner wenn Varianten existieren -->
        <div id="imagesVariantsBanner" class="info-banner"
            style="display:none; margin-bottom:24px; padding:16px; background:var(--glass-bg); border-radius:12px; border-left:4px solid var(--primary); align-items:center; gap:12px;">
            <span class="material-symbols-rounded" style="color:var(--primary);">info</span>
            <span>Laden Sie Bilder pro Variante hoch. Max. 5 Bilder pro Gruppe.</span>
        </div>

        <!-- Einfache Ansicht (ohne Varianten) -->
        <div id="imagesSimple">
            <div class="card">
                <div class="card-header">
                    <h3>Produktbilder</h3>
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
                    <p class="form-error" id="errorImages"></p>
                    <p class="form-hint" style="margin-top:8px;"><span class="required">*</span> Mindestens ein Bild
                        erforderlich. Bilder können per Drag & Drop umsortiert werden.</p>
                    <div class="image-gallery-preview" id="imageGallery" style="display:none;margin-top:24px;">
                        <!-- Preview images will be added here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Varianten-Bilder-Ansicht -->
        <div id="imagesVariants" style="display:none;">
            <div class="card">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Bilder pro Variante</h3>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <label style="font-size:13px; color:var(--text-muted);">Gruppieren nach:</label>
                        <select id="imageGroupBy" class="form-select" style="width:auto;"
                            onchange="ProductForm.updateImageGrouping()">
                            <option value="all">Alle Varianten einzeln</option>
                        </select>
                        <div id="imageValidationStatus" style="display:flex; align-items:center; gap:8px;"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="variantImageAccordions">
                        <!-- Dynamically generated per-variant/group image accordions -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Variationen -->
    <div class="step-content" data-step-content="3">
        <div class="card">
            <div class="card-header">
                <h3>Produktvariationen</h3>
            </div>
            <div class="card-body">
                <p class="form-hint" style="margin-bottom:20px;">
                    Wählen Sie Attribute aus, um Produktvariationen zu erstellen (z.B. verschiedene Farben und Größen).
                    Optional.
                </p>
                <div class="variant-attributes" id="variantAttributes">
                    <div class="loading-state" style="padding:20px;">
                        <span class="material-symbols-rounded spinning" style="font-size:24px;">sync</span>
                        <p>Lade Attribute...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" id="variantOptionsCard" style="margin-top:24px; display:none;">
            <div class="card-header">
                <h3>Optionen auswählen</h3>
            </div>
            <div class="card-body" id="variantOptionsBody"></div>
        </div>
        <div class="card" id="generatedVariantsCard" style="margin-top:24px; display:none;">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <h3>Varianten</h3>
                    <span id="variantCount" class="badge">0</span>
                </div>
            </div>
            <div class="card-body">
                <div class="variants-table-wrapper">
                    <table class="table" id="variantsTable">
                        <thead>
                            <tr>
                                <th>Variante</th>
                                <th>SKU</th>
                                <th style="width:70px;text-align:center;" title="Standardvariante für Produktanzeige">
                                    Standard</th>
                                <th style="width:60px;text-align:center;">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody id="variantsBody"></tbody>
                    </table>
                </div>
                <div id="noVariants" class="empty-state" style="display:none;">
                    <span class="material-symbols-rounded">layers</span>
                    <p>Wählen Sie Attribute und Optionen oben aus. Varianten werden automatisch erstellt.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 7: SEO -->
    <div class="step-content" data-step-content="7">
        <div class="card">
            <div class="card-header">
                <h3>Suchmaschinenoptimierung</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Meta-Titel <span class="required">*</span></label>
                    <input type="text" class="form-input" name="meta_title" id="metaTitle"
                        placeholder="Titel für Suchergebnisse (max. 60 Zeichen)" maxlength="60">
                    <small style="color:var(--text-muted);"><span id="metaTitleCount">0</span>/60 Zeichen</small>
                    <p class="form-error" id="errorMetaTitle"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Meta-Beschreibung <span class="required">*</span></label>
                    <textarea class="form-textarea" name="meta_description" id="metaDescription" rows="3"
                        placeholder="Beschreibung für Suchergebnisse (max. 160 Zeichen)" maxlength="160"></textarea>
                    <small style="color:var(--text-muted);"><span id="metaDescCount">0</span>/160 Zeichen</small>
                    <p class="form-error" id="errorMetaDescription"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Keywords <span class="required">*</span></label>
                    <input type="text" class="form-input" name="meta_keywords" id="metaKeywords"
                        placeholder="Kommagetrennte Keywords">
                    <p class="form-error" id="errorMetaKeywords"></p>
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:24px;">
            <div class="card-header">
                <h3>Suchvorschau</h3>
            </div>
            <div class="card-body">
                <div class="seo-preview">
                    <div class="seo-preview-url">meinshop.de › produkte › <span id="previewSlug">produkt-name</span>
                    </div>
                    <div class="seo-preview-title" id="previewTitle">Produktname - Mein Online Shop</div>
                    <div class="seo-preview-desc" id="previewDesc">Meta-Beschreibung wird hier angezeigt...</div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Step Navigation Buttons -->
<div class="step-buttons">
    <button class="btn" id="prevBtn" onclick="ProductForm.prevStep()" style="display:none;">
        <span class="material-symbols-rounded">arrow_back</span> Zurück
    </button>
    <button class="btn btn-primary" id="nextBtn" onclick="ProductForm.nextStep()">
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

    .form-input.is-invalid,
    .form-textarea.is-invalid {
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

    .product-type-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .product-type-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 24px;
        background: var(--bg-tertiary);
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }

    .product-type-card:hover {
        border-color: var(--accent);
    }

    .product-type-card.selected {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.1);
    }

    .product-type-card input {
        display: none;
    }

    .product-type-card .material-symbols-rounded {
        font-size: 32px;
        color: var(--accent);
    }

    .product-type-card strong {
        font-size: 14px;
    }

    .product-type-card small {
        color: var(--text-muted);
        font-size: 12px;
    }

    .category-checkboxes {
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 200px;
        overflow-y: auto;
        padding: 12px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
    }

    .category-checkboxes label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .toggle-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .toggle-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .image-upload-zone {
        border: 2px dashed var(--border);
        border-radius: var(--radius-md);
        padding: 48px;
        text-align: center;
        transition: all 0.2s;
    }

    .image-upload-zone.dragover {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.05);
    }

    .image-gallery-preview {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .image-preview-item {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: var(--radius-md);
        overflow: hidden;
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-preview-item button {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--error);
        border: none;
        color: white;
        cursor: pointer;
    }

    .image-preview-item .drag-handle {
        position: absolute;
        bottom: 4px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 16px;
        color: white;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
        cursor: grab;
    }

    .image-preview-item:active .drag-handle {
        cursor: grabbing;
    }

    .image-gallery-preview {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        min-height: 100px;
    }

    .image-preview-item {
        transition: transform 0.15s ease, opacity 0.15s ease, box-shadow 0.15s ease;
    }

    .image-preview-item.dragging {
        opacity: 0.5;
        transform: scale(1.05);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        z-index: 100;
    }

    .image-preview-item:hover {
        transform: scale(1.02);
    }

    .seo-preview {
        padding: 16px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
    }

    .seo-preview-url {
        color: var(--success);
        font-size: 12px;
    }

    .seo-preview-title {
        color: var(--accent);
        font-size: 18px;
        margin: 4px 0;
    }

    .seo-preview-desc {
        color: var(--text-muted);
        font-size: 13px;
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

    /* Variant Styles */
    .variant-attributes {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
    }

    .attr-card {
        background: var(--bg-tertiary);
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .attr-card:hover {
        border-color: var(--accent);
    }

    .attr-card.selected {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.1);
    }

    .attr-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .attr-card-header input {
        margin: 0;
    }

    .attr-card-header strong {
        flex: 1;
    }

    .attr-card-badge {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        background: var(--accent);
        color: white;
    }

    .attr-options-group {
        margin-bottom: 20px;
        padding: 16px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
    }

    .attr-options-group h4 {
        margin: 0 0 12px 0;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .attr-options-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .option-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: var(--bg-secondary);
        border: 2px solid var(--border);
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .option-chip:hover {
        border-color: var(--accent);
    }

    .option-chip.selected {
        border-color: var(--accent);
        background: var(--accent);
        color: white;
    }

    .option-chip input {
        display: none;
    }

    .color-swatch {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .variants-table-wrapper {
        overflow-x: auto;
    }

    .variant-name-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .variant-colors {
        display: flex;
        gap: 4px;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--text-muted);
    }

    .empty-state .material-symbols-rounded {
        font-size: 48px;
        opacity: 0.3;
    }

    /* Currency System Styles */
    .currency-selector {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .currency-selector label {
        color: var(--text-muted);
        font-size: 13px;
    }

    .currency-selector select {
        padding: 6px 12px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-tertiary);
        color: var(--text-primary);
    }

    .rounding-options {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rounding-options select {
        padding: 6px 12px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-tertiary);
        color: var(--text-primary);
    }

    .currency-prices-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .currency-count {
        color: var(--text-muted);
        font-size: 13px;
    }

    .currency-search {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        flex: 1;
        max-width: 300px;
    }

    .currency-search input {
        border: none;
        background: transparent;
        color: var(--text-primary);
        font-size: 14px;
        width: 100%;
        outline: none;
    }

    .currency-search input::placeholder {
        color: var(--text-muted);
    }

    .currency-search .material-symbols-rounded {
        color: var(--text-muted);
        font-size: 18px;
    }

    .currency-table-scroll {
        max-height: 400px;
        overflow-y: auto;
        padding: 4px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-tertiary);
    }

    .currency-table {
        width: 100%;
    }

    .currency-table th {
        text-align: left;
        font-weight: 500;
        padding: 12px 8px;
        border-bottom: 2px solid var(--border);
    }

    .currency-table td {
        padding: 12px 8px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .currency-table .currency-name {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .currency-table .currency-code {
        font-weight: 600;
    }

    .currency-table .currency-symbol {
        color: var(--text-muted);
    }

    .currency-table .calculated-price {
        color: var(--text-muted);
        font-style: italic;
    }

    .currency-table .override-input {
        width: 120px;
    }

    .currency-table .has-override {
        background: rgba(var(--accent-rgb), 0.05);
    }

    .currency-table .remove-btn {
        padding: 4px 8px;
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
    }

    .currency-table .remove-btn:hover {
        color: var(--error);
    }
</style>

<script>
    const ProductForm = {
        apiBase: '/admin/api/products.php',
        attrApi: '/admin/api/attributes.php',
        shopId: 1,
        currentStep: 1,
        totalSteps: 7,
        uploadedImages: [],
        // Variant system
        variantAttributes: [],
        selectedAttributes: [],
        selectedOptions: {},
        generatedVariants: [],
        // Currency system
        shopCurrencies: [],
        defaultCurrency: { code: 'USD', symbol: '$' },
        currencyPrices: {},
        currencyOverrides: {},

        // Helper to parse numbers with German locale (comma as decimal separator)
        parseLocaleNumber(value) {
            if (!value) return 0;
            // Convert string, replace comma with dot for German locale support
            const strValue = String(value).replace(/\s/g, '').replace(',', '.');
            const parsed = parseFloat(strValue);
            return isNaN(parsed) ? 0 : parsed;
        },

        async init() {
            await this.loadCategories();
            await this.loadTaxClasses();
            await this.loadShopCurrency();
            await this.loadVariantAttributes();
            this.setupEventListeners();
            this.updateStepUI();
            // Initial currency preview
            this.calculateCurrencyPreview();
        },

        async loadTaxClasses() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_tax_classes&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    const select = document.getElementById('taxClassSelect');
                    select.innerHTML = data.tax_classes.map(tc => `
                        <option value="${tc.id}" ${tc.is_default == 1 ? 'selected' : ''}>${tc.name}</option>
                    `).join('');
                }
            } catch (e) {
                console.error('Error loading tax classes:', e);
            }
        },

        async loadCategories() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_categories&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    const container = document.getElementById('categoryCheckboxes');
                    container.innerHTML = data.categories.map(cat => `
                    <label><input type="checkbox" name="category_ids[]" value="${cat.id}"> ${cat.name}</label>
                `).join('');
                }
            } catch (e) {
                console.error('Error loading categories:', e);
            }
        },

        setupEventListeners() {
            // Product type selection
            document.querySelectorAll('.product-type-card').forEach(card => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('.product-type-card').forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    card.querySelector('input').checked = true;
                    this.updateTypeFields();
                });
            });

            // Auto-generate slug from name
            document.getElementById('productName').addEventListener('input', (e) => {
                const slug = this.generateSlug(e.target.value);
                document.getElementById('productSlug').placeholder = slug;
                document.getElementById('previewSlug').textContent = slug || 'produkt-name';
                document.getElementById('previewTitle').textContent = e.target.value || 'Produktname' + ' - Mein Online Shop';
                // Clear error on input
                this.clearFieldError('errorName');
            });

            // SKU input - clear error on type
            document.getElementById('productSku').addEventListener('input', () => {
                this.clearFieldError('errorSku');
            });

            // Price input - clear error on type
            document.getElementById('productPrice').addEventListener('input', () => {
                this.clearFieldError('errorPrice');
            });

            // SEO character counters
            document.getElementById('metaTitle').addEventListener('input', (e) => {
                document.getElementById('metaTitleCount').textContent = e.target.value.length;
                document.getElementById('previewTitle').textContent = e.target.value || 'Produktname - Mein Online Shop';
            });

            document.getElementById('metaDescription').addEventListener('input', (e) => {
                document.getElementById('metaDescCount').textContent = e.target.value.length;
                document.getElementById('previewDesc').textContent = e.target.value || 'Meta-Beschreibung wird hier angezeigt...';
            });

            // Step indicator click navigation
            document.querySelectorAll('.step-nav .step').forEach(step => {
                step.style.cursor = 'pointer';
                step.addEventListener('click', () => {
                    const targetStep = parseInt(step.dataset.step);
                    // Allow going back, or forward only if current step is valid
                    if (targetStep < this.currentStep || this.validateCurrentStep()) {
                        this.currentStep = targetStep;
                        this.updateStepUI();
                    }
                });
            });

            // Image upload
            const imageInput = document.getElementById('imageInput');
            const uploadZone = document.getElementById('imageUploadZone');

            imageInput.addEventListener('change', (e) => this.handleImageUpload(e.target.files));

            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.classList.add('dragover');
            });

            uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));

            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.classList.remove('dragover');
                this.handleImageUpload(e.dataTransfer.files);
            });
        },

        clearFieldError(errorId) {
            const el = document.getElementById(errorId);
            if (el) {
                el.textContent = '';
                el.classList.remove('show');
            }
        },

        updateTypeFields() {
            const type = document.querySelector('input[name="type"]:checked').value;
            const shippingCard = document.getElementById('shippingCard');
            const digitalCard = document.getElementById('digitalCard');

            if (type === 'digital') {
                shippingCard.style.display = 'none';
                digitalCard.style.display = 'block';
            } else {
                shippingCard.style.display = 'block';
                digitalCard.style.display = 'none';
            }
        },

        toggleStockFields() {
            const manageStock = document.getElementById('manageStock').checked;
            const stockFields = document.getElementById('stockFields');
            stockFields.style.opacity = manageStock ? '1' : '0.5';
            stockFields.querySelectorAll('input').forEach(i => i.disabled = !manageStock);
        },

        // ==================== DIGITAL PRODUCT DOWNLOADS ====================

        downloadFiles: [],

        addDownloadFile() {
            const id = Date.now();
            this.downloadFiles.push({ id, name: '', url: '', type: 'url' });
            this.renderDownloadFiles();
        },

        removeDownloadFile(id) {
            this.downloadFiles = this.downloadFiles.filter(f => f.id !== id);
            this.renderDownloadFiles();
        },

        updateDownloadFile(id, field, value) {
            const file = this.downloadFiles.find(f => f.id === id);
            if (file) file[field] = value;
        },

        renderDownloadFiles() {
            const container = document.getElementById('downloadFilesList');
            if (!container) return;

            if (this.downloadFiles.length === 0) {
                container.innerHTML = `
                    <div style="padding:24px; text-align:center; border:2px dashed var(--border-color); border-radius:12px; color:var(--text-muted);">
                        <span class="material-symbols-rounded" style="font-size:32px; display:block; margin-bottom:8px;">cloud_upload</span>
                        <p>Noch keine Downloads hinzugefügt</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = this.downloadFiles.map((file, idx) => `
                <div class="download-file-row" data-file-id="${file.id}" 
                    style="display:flex; gap:12px; align-items:flex-start; padding:16px; background:var(--glass-bg); border-radius:12px; border:1px solid var(--border-color);">
                    <span class="material-symbols-rounded" style="color:var(--primary); font-size:24px; margin-top:4px;">
                        ${file.type === 'url' ? 'link' : 'upload_file'}
                    </span>
                    <div style="flex:1; display:flex; flex-direction:column; gap:12px;">
                        <div class="form-row" style="gap:12px;">
                            <div class="form-group" style="flex:1; margin-bottom:0;">
                                <label class="form-label" style="font-size:12px;">Dateiname / Bezeichnung</label>
                                <input type="text" class="form-input" value="${file.name}" placeholder="z.B. Hauptdatei.zip"
                                    onchange="ProductForm.updateDownloadFile(${file.id}, 'name', this.value)">
                            </div>
                            <div class="form-group" style="flex:1; margin-bottom:0;">
                                <label class="form-label" style="font-size:12px;">Typ</label>
                                <select class="form-input" onchange="ProductForm.updateDownloadFile(${file.id}, 'type', this.value)">
                                    <option value="url" ${file.type === 'url' ? 'selected' : ''}>Externe URL</option>
                                    <option value="upload" ${file.type === 'upload' ? 'selected' : ''}>Datei hochladen</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" style="font-size:12px;">${file.type === 'url' ? 'Download-URL' : 'Datei'}</label>
                            ${file.type === 'url'
                    ? `<input type="url" class="form-input" value="${file.url}" placeholder="https://..."
                                    onchange="ProductForm.updateDownloadFile(${file.id}, 'url', this.value)">`
                    : `<input type="file" class="form-input" style="padding:8px;"
                                    onchange="ProductForm.handleDownloadFileUpload(${file.id}, this.files[0])">`
                }
                        </div>
                    </div>
                    <button type="button" onclick="ProductForm.removeDownloadFile(${file.id})" 
                        style="background:none; border:none; cursor:pointer; color:var(--danger); padding:4px;">
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
            `).join('');
        },

        handleDownloadFileUpload(fileId, file) {
            if (!file) return;
            const downloadFile = this.downloadFiles.find(f => f.id === fileId);
            if (downloadFile) {
                downloadFile.file = file;
                downloadFile.name = downloadFile.name || file.name;
            }
        },


        handleImageUpload(files) {
            const gallery = document.getElementById('imageGallery');
            const imageInput = document.getElementById('imageInput');

            // Clear any previous error message
            const errorEl = document.getElementById('errorImages');
            if (errorEl) {
                errorEl.textContent = '';
                errorEl.classList.remove('show');
            }

            // Show gallery
            gallery.style.display = 'flex';

            Array.from(files).forEach(file => {
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
                    const id = Date.now() + Math.random();
                    this.uploadedImages.push({ id, file, dataUrl: e.target.result });

                    const div = document.createElement('div');
                    div.className = 'image-preview-item';
                    div.dataset.id = id;
                    div.draggable = true;
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" onclick="ProductForm.removeImage(${id})">&times;</button>
                        <span class="drag-handle material-symbols-rounded">drag_indicator</span>
                    `;
                    gallery.appendChild(div);

                    // Initialize drag & drop if first image
                    if (this.uploadedImages.length === 1) {
                        this.setupImageDragDrop();
                    }

                    // Show success feedback
                    this.showToast(`${this.uploadedImages.length} Bild(er) hinzugefügt`, 'success');
                };
                reader.readAsDataURL(file);
            });

            // CRITICAL: Reset file input to allow selecting same/new files again
            imageInput.value = '';
        },

        removeImage(id) {
            this.uploadedImages = this.uploadedImages.filter(img => img.id !== id);
            document.querySelector(`.image-preview-item[data-id="${id}"]`)?.remove();

            if (this.uploadedImages.length === 0) {
                document.getElementById('imageGallery').style.display = 'none';
            }
            this.updateImageOrder();
        },

        updateImageOrder() {
            const gallery = document.getElementById('imageGallery');
            const items = gallery.querySelectorAll('.image-preview-item');
            const newOrder = [];
            items.forEach(item => {
                const id = parseFloat(item.dataset.id);
                const img = this.uploadedImages.find(i => i.id === id);
                if (img) newOrder.push(img);
            });
            this.uploadedImages = newOrder;
        },

        setupImageDragDrop() {
            const gallery = document.getElementById('imageGallery');

            // Flag to prevent registering multiple times
            if (gallery.dataset.dragInitialized) return;
            gallery.dataset.dragInitialized = 'true';

            let draggedItem = null;

            // Use event delegation for dynamically added elements
            gallery.addEventListener('dragstart', (e) => {
                const item = e.target.closest('.image-preview-item');
                if (item) {
                    draggedItem = item;
                    item.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    // Required for Firefox
                    e.dataTransfer.setData('text/html', item.innerHTML);
                }
            });

            gallery.addEventListener('dragend', (e) => {
                const item = e.target.closest('.image-preview-item');
                if (item) {
                    item.classList.remove('dragging');
                    draggedItem = null;
                    this.updateImageOrder();
                }
            });

            gallery.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';

                if (!draggedItem) return;

                const afterElement = this.getDragAfterElement(gallery, e.clientX, e.clientY);
                if (afterElement) {
                    gallery.insertBefore(draggedItem, afterElement);
                } else {
                    gallery.appendChild(draggedItem);
                }
            });

            gallery.addEventListener('dragenter', (e) => {
                e.preventDefault();
            });
        },

        getDragAfterElement(container, x, y) {
            const elements = [...container.querySelectorAll('.image-preview-item:not(.dragging)')];

            return elements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offsetX = x - box.left - box.width / 2;
                const offsetY = y - box.top - box.height / 2;
                const offset = Math.sqrt(offsetX * offsetX + offsetY * offsetY);

                if (offsetX < 0 && offset < closest.offset) {
                    return { offset, element: child };
                }
                return closest;
            }, { offset: Number.POSITIVE_INFINITY }).element;
        },

        generateSlug(text) {
            return text.toLowerCase()
                .replace(/[äÄ]/g, 'ae')
                .replace(/[öÖ]/g, 'oe')
                .replace(/[üÜ]/g, 'ue')
                .replace(/ß/g, 'ss')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-|-$/g, '');
        },

        nextStep() {
            if (!this.validateCurrentStep()) return;

            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.updateStepUI();
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.updateStepUI();
            }
        },

        goToStep(step) {
            // Only allow going back or to completed steps
            if (step < this.currentStep) {
                this.currentStep = step;
                this.updateStepUI();
            }
        },

        updateStepUI() {
            // Update step navigation
            document.querySelectorAll('.step').forEach((s, i) => {
                s.classList.remove('active', 'completed');
                if (i + 1 === this.currentStep) s.classList.add('active');
                else if (i + 1 < this.currentStep) s.classList.add('completed');
            });

            // Update step content
            document.querySelectorAll('.step-content').forEach(c => c.classList.remove('active'));
            document.querySelector(`[data-step-content="${this.currentStep}"]`).classList.add('active');

            // Update buttons
            document.getElementById('prevBtn').style.display = this.currentStep > 1 ? 'flex' : 'none';
            document.getElementById('nextBtn').style.display = this.currentStep < this.totalSteps ? 'flex' : 'none';
        },

        validateCurrentStep() {
            this.clearErrors();
            let isValid = true;

            if (this.currentStep === 2) {
                // Validate name
                const name = document.getElementById('productName').value.trim();
                if (!name) {
                    this.showError('errorName', 'Produktname ist erforderlich');
                    isValid = false;
                }

                // Validate SKU
                const sku = document.getElementById('productSku').value.trim();
                if (!sku) {
                    this.showError('errorSku', 'SKU ist erforderlich');
                    isValid = false;
                }

                // Validate categories
                const categories = document.querySelectorAll('input[name="category_ids[]"]:checked');
                if (categories.length === 0) {
                    this.showError('errorCategories', 'Mindestens eine Kategorie ist erforderlich');
                    isValid = false;
                }
            }

            // Step 3: Variationen - no validation required, just optional variant selection
            // (Price validation moved to step 4)

            if (this.currentStep === 4) {
                // Validate price in Prices step
                const price = parseFloat(document.getElementById('productPrice').value);
                if (!price || price <= 0) {
                    this.showError('errorPrice', 'Preis muss größer als 0 sein');
                    isValid = false;
                }

                // Validate special price dates
                const fromDate = document.querySelector('input[name="special_price_from"]').value;
                const toDate = document.querySelector('input[name="special_price_to"]').value;
                if (fromDate && toDate && new Date(toDate) < new Date(fromDate)) {
                    this.showToast('Enddatum des Sonderpreises muss nach dem Startdatum liegen', 'e        rror');
                    isValid = false;
                }
            }

            // Step 6: Bilder - require at least 1 image per variant/group
            if (this.currentStep === 6) {
                const hasVariants = this.generatedVariants && this.generatedVariants.length >= 2;
                if (hasVariants) {
                    const groupBy = document.getElementById('imageGroupBy')?.value || 'all';

                    let totalCount, withImagesCount, label;

                    if (groupBy === 'all') {
                        // Each variant needs images
                        totalCount = this.generatedVariants.length;
                        withImagesCount = this.generatedVariants.filter(v => (v.images || []).length >= 1).length;
                        label = 'Variante(n)';
                    } else {
                        // Grouped by attribute - count unique groups
                        const groupMap = new Map();
                        this.generatedVariants.forEach(v => {
                            const attrEntry = Object.values(v).find(x => x && x.attribute && x.attribute.id == groupBy);
                            if (attrEntry) {
                                const key = attrEntry.option.id;
                                if (!groupMap.has(key)) {
                                    groupMap.set(key, { hasImages: false });
                                }
                                // A group has images if any of its variants has images
                                if ((v.images || []).length >= 1) {
                                    groupMap.get(key).hasImages = true;
                                }
                            }
                        });
                        totalCount = groupMap.size;
                        withImagesCount = Array.from(groupMap.values()).filter(g => g.hasImages).length;
                        label = 'Gruppe(n)';
                    }

                    const missingCount = totalCount - withImagesCount;
                    if (missingCount > 0) {
                        this.showError('errorImages', `${missingCount} ${label} ohne Bilder`);
                        this.showToast(`${missingCount}/${totalCount} ${label} fehlen Bilder.`, 'error');
                        isValid = false;
                    }
                } else {
                    // Simple product - check uploadedImages
                    if (!this.uploadedImages || this.uploadedImages.length === 0) {
                        this.showError('errorImages', 'Mindestens ein Bild ist erforderlich');
                        isValid = false;
                    }
                }
            }


            // Step 7: SEO - require all fields filled
            if (this.currentStep === 7) {
                const metaTitleEl = document.getElementById('metaTitle');
                const metaDescEl = document.getElementById('metaDescription');
                const metaKeywordsEl = document.getElementById('metaKeywords');

                const metaTitle = metaTitleEl?.value.trim();
                const metaDescription = metaDescEl?.value.trim();
                const metaKeywords = metaKeywordsEl?.value.trim();

                if (!metaTitle) {
                    this.showError('errorMetaTitle', 'Meta-Titel ist erforderlich');
                    metaTitleEl?.classList.add('is-invalid');
                    isValid = false;
                } else {
                    metaTitleEl?.classList.remove('is-invalid');
                }
                if (!metaDescription) {
                    this.showError('errorMetaDescription', 'Meta-Beschreibung ist erforderlich');
                    metaDescEl?.classList.add('is-invalid');
                    isValid = false;
                } else {
                    metaDescEl?.classList.remove('is-invalid');
                }
                if (!metaKeywords) {
                    this.showError('errorMetaKeywords', 'Keywords sind erforderlich');
                    metaKeywordsEl?.classList.add('is-invalid');
                    isValid = false;
                } else {
                    metaKeywordsEl?.classList.remove('is-invalid');
                }
            }

            if (!isValid) {
                this.showToast('Bitte füllen Sie alle Pflichtfelder aus', 'error');
            }

            return isValid;
        },

        showError(elementId, message) {
            const el = document.getElementById(elementId);
            if (el) {
                el.textContent = message;
                el.classList.add('show');
            }
        },

        clearErrors() {
            document.querySelectorAll('.form-error').forEach(e => {
                e.textContent = '';
                e.classList.remove('show');
            });
            // Also clear is-invalid class from all inputs
            document.querySelectorAll('.is-invalid').forEach(e => {
                e.classList.remove('is-invalid');
            });
        },

        async save(asDraft = false) {
            // Validate all steps
            for (let i = 1; i <= this.totalSteps; i++) {
                this.currentStep = i;
                if (!this.validateCurrentStep()) {
                    this.updateStepUI();
                    return;
                }
            }

            const formData = new FormData(document.getElementById('productForm'));
            formData.append('action', 'save_product');
            formData.append('shop_id', this.shopId);
            formData.append('status', asDraft ? 'draft' : 'active');

            // Get category IDs
            const categoryIds = Array.from(document.querySelectorAll('input[name="category_ids[]"]:checked'))
                .map(cb => cb.value);
            formData.set('category_ids', JSON.stringify(categoryIds));

            // Handle checkboxes
            formData.set('is_visible', document.querySelector('input[name="is_visible"]').checked ? '1' : '0');
            formData.set('is_featured', document.querySelector('input[name="is_featured"]').checked ? '1' : '0');
            formData.set('is_new', document.querySelector('input[name="is_new"]').checked ? '1' : '0');
            formData.set('manage_stock', document.getElementById('manageStock').checked ? '1' : '0');
            formData.set('allow_backorders', document.querySelector('input[name="allow_backorders"]')?.checked ? '1' : '0');

            // ========================================
            // APPEND IMAGES TO FORMDATA
            // ========================================
            this.uploadedImages.forEach((img, index) => {
                if (img.file) {
                    // New image - append file
                    formData.append('images[]', img.file);
                }
            });

            // Send image order (for existing images in edit mode)
            const imageOrder = this.uploadedImages
                .filter(img => img.dbId)
                .map(img => img.dbId);
            if (imageOrder.length > 0) {
                formData.append('image_order', JSON.stringify(imageOrder));
            }

            // ========================================
            // APPEND VARIANTS TO FORMDATA
            // ========================================
            if (this.generatedVariants && this.generatedVariants.length > 0) {
                // Convert to API format including all pricing and inventory data
                const variantsForApi = this.generatedVariants.map((v, idx) => {
                    // Extract attributes as {attributeName: optionValue}
                    const attributes = {};
                    Object.entries(v).forEach(([key, data]) => {
                        // Only serialize attribute/option pairs, skip scalar properties
                        if (data && typeof data === 'object' && data.attribute && data.option) {
                            attributes[data.attribute.name] = data.option.label || data.option.value;
                        }
                    });

                    // Build complete variant object with all data
                    return {
                        attributes: attributes,
                        sku: v.sku || null,
                        is_active: v.is_active !== false ? 1 : 0,
                        is_default: v.is_default === true ? 1 : 0,
                        price_adjustment: parseFloat(v.price_adjustment) || 0,
                        special_price: v.special_price ? parseFloat(v.special_price) : null,
                        cost_price: v.cost_price ? parseFloat(v.cost_price) : null,
                        stock: parseInt(v.stock) || 0,
                        weight: v.weight ? parseFloat(v.weight) : null,
                        length: v.length ? parseFloat(v.length) : null,
                        width: v.width ? parseFloat(v.width) : null,
                        height: v.height ? parseFloat(v.height) : null,
                        currency_overrides: v.currency_overrides || {},
                        // Note: Images are handled separately via variant image upload
                        has_images: (v.images || []).length
                    };
                });
                formData.append('variants', JSON.stringify(variantsForApi));
            }

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    // Save currency prices if product was created successfully
                    if (data.id && Object.keys(this.currencyOverrides).length > 0) {
                        await this.saveCurrencyPrices(data.id);
                    }

                    this.showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '?page=catalog/products';
                    }, 1000);
                } else {
                    const errors = data.errors || [data.error || 'Unbekannter Fehler'];
                    this.showToast(errors.join(', '), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        saveAsDraft() {
            this.save(true);
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        },

        // =============== VARIANT SYSTEM ===============
        async loadVariantAttributes() {
            try {
                const res = await fetch(`${this.attrApi}?action=get_attributes&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    this.variantAttributes = data.attributes.filter(a => a.used_for_variants == 1 && a.options_count > 0);
                    this.renderVariantAttributes();
                }
            } catch (e) { console.error(e); }
        },

        renderVariantAttributes() {
            const container = document.getElementById('variantAttributes');
            if (this.variantAttributes.length === 0) {
                container.innerHTML = `<div class="empty-state" style="padding:20px;"><span class="material-symbols-rounded">info</span><p>Keine Attribute für Varianten. <a href="?page=catalog/attributes">Attribute verwalten</a></p></div>`;
                return;
            }
            container.innerHTML = this.variantAttributes.map(attr => `
                <div class="attr-card ${this.selectedAttributes.includes(attr.id) ? 'selected' : ''}" onclick="ProductForm.toggleAttribute(${attr.id})">
                    <div class="attr-card-header">
                        <input type="checkbox" ${this.selectedAttributes.includes(attr.id) ? 'checked' : ''}>
                        <strong>${attr.name}</strong>
                        <span class="attr-card-badge">${attr.options_count || 0}</span>
                    </div>
                    <small style="color:var(--text-muted);">${attr.type_label || attr.type}</small>
                </div>
            `).join('');
        },

        async toggleAttribute(attrId) {
            const idx = this.selectedAttributes.indexOf(attrId);
            if (idx > -1) {
                // Attribute is being DESELECTED
                this.selectedAttributes.splice(idx, 1);
                delete this.selectedOptions[attrId];
                // CRITICAL: Regenerate variants when attribute is removed
                // This will clear variants if no attributes remain selected
                this.autoGenerateVariants();
            } else {
                // Attribute is being SELECTED
                this.selectedAttributes.push(attrId);
                await this.loadAttributeOptions(attrId);
            }
            this.renderVariantAttributes();
            this.renderVariantOptions();
            this.updateGenerateButton();
        },

        async loadAttributeOptions(attrId) {
            try {
                const res = await fetch(`${this.attrApi}?action=get_attribute&shop_id=${this.shopId}&id=${attrId}`);
                const data = await res.json();
                if (data.success && data.attribute.options) {
                    const attr = this.variantAttributes.find(a => a.id === attrId);
                    if (attr) attr.options = data.attribute.options;
                    this.selectedOptions[attrId] = [];
                }
            } catch (e) { console.error(e); }
        },

        renderVariantOptions() {
            const card = document.getElementById('variantOptionsCard');
            const body = document.getElementById('variantOptionsBody');
            if (this.selectedAttributes.length === 0) { card.style.display = 'none'; return; }
            card.style.display = 'block';
            body.innerHTML = '';
            this.selectedAttributes.forEach(attrId => {
                const attr = this.variantAttributes.find(a => a.id === attrId);
                if (!attr || !attr.options) return;
                const selectedOpts = this.selectedOptions[attrId] || [];
                const group = document.createElement('div');
                group.className = 'attr-options-group';
                group.innerHTML = `
                    <h4>${attr.name} <button type="button" class="btn btn-sm" onclick="ProductForm.selectAllOptions(${attrId})">Alle auswählen</button></h4>
                    <div class="attr-options-list">
                        ${attr.options.map(opt => `
                            <label class="option-chip ${selectedOpts.includes(opt.id) ? 'selected' : ''}" onclick="ProductForm.toggleOption(${attrId}, ${opt.id})">
                                ${opt.color_hex ? `<span class="color-swatch" style="background:${opt.color_hex}"></span>` : ''}
                                <span>${opt.label || opt.value}</span>
                            </label>
                        `).join('')}
                    </div>
                `;
                body.appendChild(group);
            });
        },

        toggleOption(attrId, optId) {
            if (!this.selectedOptions[attrId]) this.selectedOptions[attrId] = [];
            const idx = this.selectedOptions[attrId].indexOf(optId);
            if (idx > -1) this.selectedOptions[attrId].splice(idx, 1);
            else this.selectedOptions[attrId].push(optId);
            this.renderVariantOptions();
            this.autoGenerateVariants();
        },

        selectAllOptions(attrId) {
            const attr = this.variantAttributes.find(a => a.id === attrId);
            if (attr && attr.options) this.selectedOptions[attrId] = attr.options.map(o => o.id);
            this.renderVariantOptions();
            this.autoGenerateVariants();
        },

        updateGenerateButton() {
            // Legacy function - kept for compatibility
            const hasOpts = Object.values(this.selectedOptions).some(o => o && o.length > 0);
            return hasOpts;
        },

        autoGenerateVariants() {
            // Auto-generate variants in real-time
            const attrOpts = [];
            this.selectedAttributes.forEach(attrId => {
                const attr = this.variantAttributes.find(a => a.id === attrId);
                if (!attr || !attr.options) return;
                const opts = attr.options.filter(o => (this.selectedOptions[attrId] || []).includes(o.id));
                if (opts.length > 0) attrOpts.push({ attribute: attr, options: opts });
            });

            if (attrOpts.length === 0) {
                this.generatedVariants = [];
            } else {
                this.generatedVariants = this.generateCombinations(attrOpts);
            }
            this.renderGeneratedVariants();
            this.onVariantsChanged();
        },

        generateVariants() {
            const attrOpts = [];
            this.selectedAttributes.forEach(attrId => {
                const attr = this.variantAttributes.find(a => a.id === attrId);
                if (!attr || !attr.options) return;
                const opts = attr.options.filter(o => (this.selectedOptions[attrId] || []).includes(o.id));
                if (opts.length > 0) attrOpts.push({ attribute: attr, options: opts });
            });
            if (attrOpts.length === 0) { this.showToast('Bitte wählen Sie mindestens eine Option', 'error'); return; }
            this.generatedVariants = this.generateCombinations(attrOpts);
            this.renderGeneratedVariants();
            this.showToast(`${this.generatedVariants.length} Varianten generiert`, 'success');
        },

        generateCombinations(attrOptions, index = 0, current = {}) {
            if (index === attrOptions.length) return [{ ...current }];
            const results = [];
            const { attribute, options } = attrOptions[index];
            options.forEach(opt => {
                results.push(...this.generateCombinations(attrOptions, index + 1, { ...current, [attribute.id]: { attribute, option: opt } }));
            });
            return results;
        },

        renderGeneratedVariants() {
            const card = document.getElementById('generatedVariantsCard');
            const tbody = document.getElementById('variantsBody');
            const countBadge = document.getElementById('variantCount');
            const noVar = document.getElementById('noVariants');

            // Hide card if no attributes selected at all
            if (this.selectedAttributes.length === 0) {
                card.style.display = 'none';
                return;
            }

            // Show card but with "no variants" message if no options selected
            if (this.generatedVariants.length === 0) {
                card.style.display = 'block';
                noVar.style.display = 'block';
                tbody.innerHTML = '';
                countBadge.textContent = '0';
                return;
            }

            card.style.display = 'block';
            noVar.style.display = 'none';
            countBadge.textContent = this.generatedVariants.length;
            const baseSku = document.getElementById('productSku')?.value || 'SKU';
            const basePrice = parseFloat(document.getElementById('productPrice')?.value) || 0;
            // Ensure exactly one variant is marked as default (first one if none set)
            const hasDefault = this.generatedVariants.some(v => v.is_default === true);
            if (!hasDefault && this.generatedVariants.length > 0) {
                this.generatedVariants[0].is_default = true;
            }

            tbody.innerHTML = this.generatedVariants.map((v, idx) => {
                const vals = Object.values(v).filter(x => x && typeof x === 'object' && x.attribute && x.option);
                const name = vals.map(x => x.option.label || x.option.value).join(' / ');
                const sku = `${baseSku}-${vals.map(x => x.option.value).join('-')}`;
                const colors = vals.filter(x => x.option.color_hex).map(x => `<span class="color-swatch" style="background:${x.option.color_hex}"></span>`).join('');
                // Store SKU and active state in variant object
                if (!v.sku) v.sku = sku;
                if (v.is_active === undefined) v.is_active = true;
                if (v.is_default === undefined) v.is_default = false;
                return `<tr data-variant-idx="${idx}">
                    <td><div class="variant-name-cell">${colors ? `<div class="variant-colors">${colors}</div>` : ''}<span>${name}</span></div></td>
                    <td><input type="text" class="form-input variant-sku" value="${v.sku}" style="width:180px;" onchange="ProductForm.updateVariantSku(${idx}, this.value)"></td>
                    <td style="text-align:center;"><input type="radio" name="default_variant" class="variant-default" ${v.is_default ? 'checked' : ''} onchange="ProductForm.setDefaultVariant(${idx})" title="Als Standard setzen"></td>
                    <td style="text-align:center;"><button type="button" class="btn btn-sm btn-icon" onclick="ProductForm.removeVariant(${idx})" title="Variante löschen"><span class="material-symbols-rounded">delete</span></button></td>
                </tr>`;
            }).join('');
        },

        removeVariant(idx) {
            this.generatedVariants.splice(idx, 1);
            this.renderGeneratedVariants();
            this.onVariantsChanged();
        },

        updateVariantSku(idx, sku) {
            if (this.generatedVariants[idx]) {
                this.generatedVariants[idx].sku = sku;
            }
        },

        updateVariantActive(idx, isActive) {
            if (this.generatedVariants[idx]) {
                this.generatedVariants[idx].is_active = isActive;
            }
        },
        setDefaultVariant(idx) {
            // Ensure exactly one variant is default
            this.generatedVariants.forEach((v, i) => {
                v.is_default = (i === idx);
            });
            // No need to re-render, radio buttons handle visual state
        },
        // ==================== VARIANT-AWARE TABS ====================

        onVariantsChanged() {
            // Only show variant-specific views when there are 2+ variants
            const hasVariants = this.generatedVariants.length >= 2;
            const variantCount = this.generatedVariants.length;

            // Toggle Currency Prices card (hide when variants exist - use unified variant pricing instead)
            const currencyPricesCard = document.getElementById('currencyPricesCard');
            if (currencyPricesCard) {
                currencyPricesCard.style.display = hasVariants ? 'none' : 'block';
            }

            // Toggle Variant Prices card (unified variant + currency pricing)
            const variantPricesCard = document.getElementById('variantPricesCard');
            if (variantPricesCard) {
                variantPricesCard.style.display = hasVariants ? 'block' : 'none';
                if (hasVariants) {
                    this.renderVariantPricesAccordions();
                }
            }

            // Toggle Inventar views
            const inventorySimple = document.getElementById('inventorySimple');
            const inventoryVariants = document.getElementById('inventoryVariants');
            const inventoryBanner = document.getElementById('inventoryVariantsBanner');
            const inventoryCount = document.getElementById('inventoryVariantCount');

            if (inventorySimple && inventoryVariants) {
                inventorySimple.style.display = hasVariants ? 'none' : 'grid';
                inventoryVariants.style.display = hasVariants ? 'block' : 'none';
                if (inventoryBanner) inventoryBanner.style.display = hasVariants ? 'flex' : 'none';
                if (inventoryCount) inventoryCount.textContent = variantCount;
                if (hasVariants) this.renderInventoryVariants();
            }

            // Toggle Bilder views
            const imagesSimple = document.getElementById('imagesSimple');
            const imagesVariants = document.getElementById('imagesVariants');
            const imagesBanner = document.getElementById('imagesVariantsBanner');

            if (imagesSimple && imagesVariants) {
                imagesSimple.style.display = hasVariants ? 'none' : 'block';
                imagesVariants.style.display = hasVariants ? 'block' : 'none';
                if (imagesBanner) imagesBanner.style.display = hasVariants ? 'flex' : 'none';
                if (hasVariants) {
                    this.updateImageGroupingOptions();
                    this.renderImagesVariants();
                }
            }
        },

        // ==================== UNIFIED VARIANT PRICING ====================

        toggleVariantRounding() {
            const enabled = document.getElementById('variantEnableRounding').checked;
            document.getElementById('variantRoundingStep').disabled = !enabled;
            this.renderVariantPricesAccordions();
        },

        refreshVariantPrices() {
            this.renderVariantPricesAccordions();
        },

        // Check if special price is currently active based on date range
        isSpecialPriceActive() {
            const fromDate = document.getElementById('specialFrom')?.value;
            const toDate = document.getElementById('specialTo')?.value;

            // If no dates set, special price is always active (if it exists)
            if (!fromDate && !toDate) return true;

            const today = new Date();
            today.setHours(0, 0, 0, 0); // Normalize to start of day

            if (fromDate) {
                const from = new Date(fromDate);
                from.setHours(0, 0, 0, 0);
                if (today < from) return false; // Before start date
            }

            if (toDate) {
                const to = new Date(toDate);
                to.setHours(23, 59, 59, 999); // End of that day
                if (today > to) return false; // After end date
            }

            return true;
        },

        renderVariantPricesAccordions() {
            const container = document.getElementById('variantPricesAccordions');
            if (!container) return;

            const basePrice = parseFloat(document.getElementById('productPrice')?.value) || 0;
            const baseSpecialPrice = parseFloat(document.getElementById('specialPrice')?.value) || 0;
            const baseCostPrice = parseFloat(document.getElementById('costPrice')?.value) || 0;
            const baseCurrency = this.defaultCurrency || { symbol: '$', code: 'USD' };

            container.innerHTML = this.generatedVariants.map((v, idx) => {
                const vals = Object.values(v).filter(x => x && x.option);
                const name = vals.map(x => x.option.label || x.option.value).join(' / ');
                const colors = vals.filter(x => x.option.color_hex).map(x =>
                    `<span class="color-swatch" style="background:${x.option.color_hex}"></span>`
                ).join('');

                const adjustment = v.price_adjustment || 0;
                const finalPrice = basePrice + adjustment;

                // Use variant-specific prices if set, otherwise fall back to base prices
                const variantSpecialPrice = v.special_price !== null && v.special_price !== undefined ? v.special_price : baseSpecialPrice;
                const variantCostPrice = v.cost_price !== null && v.cost_price !== undefined ? v.cost_price : baseCostPrice;

                const currencyOverrideCount = Object.keys(v.currency_overrides || {}).length;

                // Check if special price is active based on date range
                const isSpecialActive = this.isSpecialPriceActive();

                // Calculate effective sale price (special if set AND dates are valid, otherwise regular)
                const effectivePrice = (variantSpecialPrice > 0 && isSpecialActive) ? variantSpecialPrice : finalPrice;

                // Calculate margin based on effective price and cost
                let marginDisplay = '';
                if (variantCostPrice > 0 && effectivePrice > 0) {
                    const margin = ((effectivePrice - variantCostPrice) / variantCostPrice * 100);
                    const marginClass = margin >= 0 ? 'color:var(--success)' : 'color:var(--danger)';
                    marginDisplay = `<span style="font-size:11px; font-weight:500; ${marginClass};">${margin >= 0 ? '+' : ''}${margin.toFixed(0)}%</span>`;
                }

                // Price display: strikethrough if special priceAND dates are valid, otherwise just final price
                let priceDisplay = '';
                if (variantSpecialPrice > 0 && variantSpecialPrice < finalPrice && isSpecialActive) {
                    priceDisplay = `
                        <span style="text-decoration:line-through; color:var(--text-muted); font-size:13px;">${baseCurrency.symbol} ${finalPrice.toFixed(2)}</span>
                        <strong style="color:var(--danger); margin-left:6px;">${baseCurrency.symbol} ${variantSpecialPrice.toFixed(2)}</strong>
                    `;
                } else {
                    priceDisplay = `<strong style="color:var(--primary);">${baseCurrency.symbol} ${finalPrice.toFixed(2)}</strong>`;
                }

                return `
                    <div class="variant-price-accordion" data-variant-idx="${idx}" 
                        style="border:1px solid var(--border-color); border-radius:12px; overflow:hidden; margin-bottom:8px;">
                        <div class="accordion-header" onclick="ProductForm.toggleVariantPriceAccordion(${idx})" 
                            style="padding:16px; background:var(--glass-bg); cursor:pointer; display:flex; justify-content:space-between; align-items:center;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                ${colors ? `<div style="display:flex; gap:4px;">${colors}</div>` : ''}
                                <span style="font-weight:500;">${name}</span>
                                ${marginDisplay}
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="min-width:140px; text-align:right;">
                                    ${priceDisplay}
                                </div>
                                ${currencyOverrideCount > 0 ?
                        `<span class="badge badge-info" style="font-size:10px;">${currencyOverrideCount} Währ.</span>` :
                        ''}
                                <span class="material-symbols-rounded accordion-icon" id="variantPriceIcon${idx}" 
                                    style="color:var(--text-muted);">expand_more</span>
                            </div>
                        </div>
                        <div class="accordion-body" id="variantPriceBody${idx}" style="display:none; padding:16px; background:var(--bg-secondary);">
                            <!-- Per-Variant Prices -->
                            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-bottom:20px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">Regulärer Preis</label>
                                    <div style="display:flex; align-items:center; gap:4px;">
                                        <span style="color:var(--text-muted);">${baseCurrency.symbol}</span>
                                        <input type="number" class="form-input" value="${finalPrice.toFixed(2)}" step="0.01"
                                            onchange="ProductForm.updateVariantFinalPrice(${idx}, this.value)">
                                    </div>
                                    <p class="form-hint" style="margin-top:4px;">Basis ${baseCurrency.symbol}${basePrice.toFixed(2)} ${adjustment !== 0 ? (adjustment > 0 ? '+' : '') + adjustment.toFixed(2) : ''}</p>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">Sonderpreis ${variantSpecialPrice > 0 ? '<span style="color:var(--danger);">✓</span>' : ''}</label>
                                    <div style="display:flex; align-items:center; gap:4px;">
                                        <span style="color:var(--text-muted);">${baseCurrency.symbol}</span>
                                        <input type="number" class="form-input" value="${variantSpecialPrice > 0 ? variantSpecialPrice.toFixed(2) : ''}" placeholder="${baseSpecialPrice > 0 ? baseSpecialPrice.toFixed(2) : '—'}" step="0.01"
                                            onchange="ProductForm.updateVariantSpecialPrice(${idx}, this.value)">
                                    </div>
                                    <p class="form-hint" style="margin-top:4px;">Leer = ${baseSpecialPrice > 0 ? 'Basis ' + baseCurrency.symbol + baseSpecialPrice.toFixed(2) : 'kein Angebot'}</p>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">Einkaufspreis (EK) ${variantCostPrice > 0 ? '<span style="color:var(--success);">✓</span>' : ''}</label>
                                    <div style="display:flex; align-items:center; gap:4px;">
                                        <span style="color:var(--text-muted);">${baseCurrency.symbol}</span>
                                        <input type="number" class="form-input" value="${variantCostPrice > 0 ? variantCostPrice.toFixed(2) : (baseCostPrice > 0 ? baseCostPrice.toFixed(2) : '')}" placeholder="—" step="0.01"
                                            onchange="ProductForm.updateVariantCostPrice(${idx}, this.value)">
                                    </div>
                                    <p class="form-hint" style="margin-top:4px;">${baseCostPrice > 0 ? 'Marge: ' + marginDisplay : 'Für Margenberechnung'}</p>
                                </div>
                            </div>

                            <!-- Currency Overrides -->
                            <div style="border-top:1px solid var(--border-color); padding-top:16px;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
                                    <h5 style="font-size:14px; margin:0;">Währungs-Überschreibungen</h5>
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <input type="text" class="form-input" placeholder="Währung suchen..." 
                                            style="width:160px; height:28px; padding:4px 8px; font-size:12px;"
                                            oninput="ProductForm.filterVariantCurrencies(${idx}, this.value)">
                                        <span style="font-size:12px; color:var(--text-muted);" id="variantCurrencyCount${idx}">
                                            ${this.shopCurrencies?.filter(c => c.code !== this.defaultCurrency?.code).length || 0} Währungen
                                        </span>
                                    </div>
                                </div>
                                <div class="variant-currency-grid" id="variantCurrencyGrid${idx}" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:8px;">
                                    ${this.renderVariantCurrencyInputs(idx, effectivePrice)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        },

        renderVariantCurrencyInputs(variantIdx, baseVariantPrice) {
            // Show ALL currencies (not just 8) - removed slice limit
            const allCurrencies = this.shopCurrencies
                ?.filter(c => c.code !== this.defaultCurrency?.code) || [];

            if (allCurrencies.length === 0) {
                return '<p style="color:var(--text-muted); grid-column:1/-1;">Keine zusätzlichen Währungen konfiguriert.</p>';
            }

            const variant = this.generatedVariants[variantIdx];
            const overrides = variant.currency_overrides || {};
            const roundingStep = parseFloat(document.getElementById('variantRoundingStep')?.value) || 0;
            const enableRounding = document.getElementById('variantEnableRounding')?.checked || false;

            return allCurrencies.map(currency => {
                const baseRate = this.defaultCurrency?.exchange_rate || 1;
                const targetRate = currency.exchange_rate || 1;
                let calculated = baseVariantPrice * (targetRate / baseRate);

                if (enableRounding && roundingStep > 0) {
                    calculated = Math.round(calculated / roundingStep) * roundingStep;
                }

                const override = overrides[currency.code] || '';
                const hasOverride = override !== '';

                return `
                    <div class="currency-item" data-currency-code="${currency.code}" data-currency-name="${currency.name || ''}" 
                        style="display:flex; align-items:center; gap:6px; padding:8px; background:var(--card-bg); border-radius:8px; ${hasOverride ? 'border:1px solid var(--primary);' : ''}">
                        <span style="font-weight:600; min-width:40px; color:${hasOverride ? 'var(--primary)' : 'inherit'};">${currency.code}</span>
                        <span style="color:var(--text-muted); font-size:11px; min-width:55px;">${currency.symbol} ${calculated.toFixed(2)}</span>
                        <input type="number" class="form-input" value="${override}" placeholder="—" step="0.01" 
                            style="width:70px; height:26px; padding:2px 6px; font-size:12px;"
                            onchange="ProductForm.updateVariantCurrencyOverride(${variantIdx}, '${currency.code}', this.value)">
                    </div>
                `;
            }).join('');
        },

        filterVariantCurrencies(variantIdx, searchTerm) {
            const grid = document.getElementById(`variantCurrencyGrid${variantIdx}`);
            const countEl = document.getElementById(`variantCurrencyCount${variantIdx}`);
            if (!grid) return;

            const items = grid.querySelectorAll('.currency-item');
            const term = searchTerm.toLowerCase().trim();
            let visibleCount = 0;

            items.forEach(item => {
                const code = (item.dataset.currencyCode || '').toLowerCase();
                const name = (item.dataset.currencyName || '').toLowerCase();
                const matches = term === '' || code.includes(term) || name.includes(term);

                item.style.display = matches ? 'flex' : 'none';
                if (matches) visibleCount++;
            });

            if (countEl) {
                countEl.textContent = term ? `${visibleCount} von ${items.length} Währungen` : `${items.length} Währungen`;
            }
        },

        toggleVariantPriceAccordion(idx) {
            const body = document.getElementById(`variantPriceBody${idx}`);
            const icon = document.getElementById(`variantPriceIcon${idx}`);
            if (body && icon) {
                const isOpen = body.style.display !== 'none';
                body.style.display = isOpen ? 'none' : 'block';
                icon.textContent = isOpen ? 'expand_more' : 'expand_less';
            }
        },

        updateVariantPriceAdjustment(idx, value) {
            this.generatedVariants[idx].price_adjustment = parseFloat(value) || 0;
            // Refresh accordion header price display
            this.renderVariantPricesAccordions();
        },

        updateVariantFinalPrice(idx, value) {
            // Calculate adjustment from absolute price
            const basePrice = parseFloat(document.getElementById('productPrice')?.value) || 0;
            const newPrice = parseFloat(value) || 0;
            this.generatedVariants[idx].price_adjustment = newPrice - basePrice;
            // Refresh to update display
            this.renderVariantPricesAccordions();
        },

        updateVariantSpecialPrice(idx, value) {
            this.generatedVariants[idx].special_price = value ? parseFloat(value) : null;
            // Refresh to update strikethrough display
            this.renderVariantPricesAccordions();
        },

        updateVariantCostPrice(idx, value) {
            this.generatedVariants[idx].cost_price = value ? parseFloat(value) : null;
            // Refresh to update margin display
            this.renderVariantPricesAccordions();
        },

        updateVariantCurrencyOverride(variantIdx, currencyCode, value) {
            if (!this.generatedVariants[variantIdx].currency_overrides) {
                this.generatedVariants[variantIdx].currency_overrides = {};
            }

            if (value === '' || value === null) {
                delete this.generatedVariants[variantIdx].currency_overrides[currencyCode];
            } else {
                this.generatedVariants[variantIdx].currency_overrides[currencyCode] = parseFloat(value);
            }

            // Update badge count
            this.updateVariantCurrencyBadge(variantIdx);
        },

        updateVariantCurrencyBadge(variantIdx) {
            const accordion = document.querySelector(`[data-variant-idx="${variantIdx}"]`);
            if (!accordion) return;

            const overrideCount = Object.keys(this.generatedVariants[variantIdx].currency_overrides || {}).length;
            const header = accordion.querySelector('.accordion-header');
            const existingBadge = header.querySelector('.badge-info');

            if (overrideCount > 0) {
                if (existingBadge) {
                    existingBadge.textContent = `${overrideCount} Überschr.`;
                } else {
                    const icon = header.querySelector('.accordion-icon');
                    if (icon) {
                        icon.insertAdjacentHTML('beforebegin',
                            `<span class="badge badge-info" style="font-size:10px;">${overrideCount} Überschr.</span>`
                        );
                    }
                }
            } else if (existingBadge) {
                existingBadge.remove();
            }
        },


        renderInventoryVariants() {
            const tbody = document.getElementById('inventoryVariantsBody');
            if (!tbody) return;

            tbody.innerHTML = this.generatedVariants.map((v, idx) => {
                const vals = Object.values(v).filter(x => x && x.option);
                const name = vals.map(x => x.option.label || x.option.value).join(' / ');
                const colors = vals.filter(x => x.option.color_hex).map(x => `<span class="color-swatch" style="background:${x.option.color_hex}"></span>`).join('');

                return `
                    <tr data-inventory-idx="${idx}">
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                ${colors ? `<div style="display:flex; gap:4px;">${colors}</div>` : ''}
                                <span>${name}</span>
                            </div>
                        </td>
                        <td><input type="number" class="form-input" value="${v.stock || 0}" min="0" style="width:80px;" onchange="ProductForm.updateVariantInventory(${idx}, 'stock', this.value)"></td>
                        <td><input type="number" class="form-input" value="${v.weight || ''}" step="0.1" min="0" style="width:80px;" onchange="ProductForm.updateVariantInventory(${idx}, 'weight', this.value)"></td>
                        <td><input type="number" class="form-input" value="${v.length || ''}" min="0" style="width:60px;" onchange="ProductForm.updateVariantInventory(${idx}, 'length', this.value)"></td>
                        <td><input type="number" class="form-input" value="${v.width || ''}" min="0" style="width:60px;" onchange="ProductForm.updateVariantInventory(${idx}, 'width', this.value)"></td>
                        <td><input type="number" class="form-input" value="${v.height || ''}" min="0" style="width:60px;" onchange="ProductForm.updateVariantInventory(${idx}, 'height', this.value)"></td>
                    </tr>
                `;
            }).join('');
        },

        updateVariantInventory(idx, field, value) {
            if (field === 'stock') {
                this.generatedVariants[idx][field] = parseInt(value) || 0;
            } else {
                this.generatedVariants[idx][field] = parseFloat(value) || null;
            }
        },

        setAllVariantStock() {
            // Open the bulk inventory modal
            const modal = document.getElementById('inventoryBulkModal');
            if (!modal) return;

            // Set variant count
            document.getElementById('inventoryModalCount').textContent = this.generatedVariants.length;

            // Clear all fields
            ['bulkStock', 'bulkWeight', 'bulkLength', 'bulkWidth', 'bulkHeight'].forEach(id => {
                document.getElementById(id).value = '';
            });

            // Show modal
            modal.style.display = 'flex';

            // Focus first field
            document.getElementById('bulkStock').focus();

            // Add escape key listener
            this._escHandler = (e) => {
                if (e.key === 'Escape') this.closeInventoryModal();
            };
            document.addEventListener('keydown', this._escHandler);
        },

        closeInventoryModal() {
            const modal = document.getElementById('inventoryBulkModal');
            if (modal) modal.style.display = 'none';
            if (this._escHandler) {
                document.removeEventListener('keydown', this._escHandler);
                this._escHandler = null;
            }
        },

        applyBulkInventory() {
            const stock = document.getElementById('bulkStock').value;
            const weight = document.getElementById('bulkWeight').value;
            const length = document.getElementById('bulkLength').value;
            const width = document.getElementById('bulkWidth').value;
            const height = document.getElementById('bulkHeight').value;

            let changedFields = 0;

            this.generatedVariants.forEach(v => {
                if (stock !== '') { v.stock = parseInt(stock) || 0; changedFields++; }
                if (weight !== '') { v.weight = parseFloat(weight) || 0; changedFields++; }
                if (length !== '') { v.length = parseFloat(length) || 0; changedFields++; }
                if (width !== '') { v.width = parseFloat(width) || 0; changedFields++; }
                if (height !== '') { v.height = parseFloat(height) || 0; changedFields++; }
            });

            this.closeInventoryModal();
            this.renderInventoryVariants();

            if (changedFields > 0) {
                this.showToast(`Inventar für ${this.generatedVariants.length} Varianten aktualisiert`, 'success');
            }
        },

        updateImageGroupingOptions() {
            const select = document.getElementById('imageGroupBy');
            if (!select) return;

            // Get unique attributes from variants
            const attributes = [];
            if (this.generatedVariants.length > 0) {
                const firstVariant = this.generatedVariants[0];
                Object.values(firstVariant).forEach(v => {
                    if (v && v.attribute) {
                        attributes.push({ id: v.attribute.id, name: v.attribute.name });
                    }
                });
            }

            select.innerHTML = '<option value="all">Alle Varianten einzeln</option>' +
                attributes.map(attr => `<option value="${attr.id}">${attr.name}</option>`).join('');
        },

        updateImageGrouping() {
            this.renderImagesVariants();
        },

        renderImagesVariants() {
            const container = document.getElementById('variantImageAccordions');
            if (!container) return;

            const groupBy = document.getElementById('imageGroupBy')?.value || 'all';

            let groups = [];
            if (groupBy === 'all') {
                // Each variant is its own group
                groups = this.generatedVariants.map((v, idx) => ({
                    key: `variant_${idx}`,
                    variants: [v],
                    variantIndices: [idx],
                    name: Object.values(v).filter(x => x && x.option).map(x => x.option.label || x.option.value).join(' / ')
                }));
            } else {
                // Group by selected attribute
                const groupMap = new Map();
                this.generatedVariants.forEach((v, idx) => {
                    const attrEntry = Object.values(v).find(x => x && x.attribute && x.attribute.id == groupBy);
                    if (attrEntry) {
                        const key = attrEntry.option.id;
                        if (!groupMap.has(key)) {
                            groupMap.set(key, {
                                key: `attr_${groupBy}_${key}`,
                                variants: [],
                                variantIndices: [],
                                name: attrEntry.option.label || attrEntry.option.value
                            });
                        }
                        groupMap.get(key).variants.push(v);
                        groupMap.get(key).variantIndices.push(idx);
                    }
                });
                groups = Array.from(groupMap.values());
            }

            container.innerHTML = groups.map((group, gIdx) => {
                const images = group.variants[0]?.images || [];
                const imageCount = images.length;
                const isValid = imageCount >= 1;

                // Only show swatches when grouping by 'all', or for the grouped attribute only
                let colorSwatches = '';
                if (groupBy === 'all') {
                    // Show all color swatches for this variant
                    colorSwatches = Object.values(group.variants[0] || {})
                        .filter(x => x && x.option && x.option.color_hex)
                        .map(x => `<span class="color-swatch" style="background:${x.option.color_hex}"></span>`)
                        .join('');
                } else {
                    // When grouping by an attribute, only show that attribute's swatch
                    const groupedAttrEntry = Object.values(group.variants[0] || {})
                        .find(x => x && x.attribute && x.attribute.id == groupBy);
                    if (groupedAttrEntry && groupedAttrEntry.option && groupedAttrEntry.option.color_hex) {
                        colorSwatches = `<span class="color-swatch" style="background:${groupedAttrEntry.option.color_hex}"></span>`;
                    }
                }

                return `
                    <div class="variant-image-accordion" data-group-idx="${gIdx}" style="border:1px solid var(--border-color); border-radius:12px; margin-bottom:12px; overflow:hidden;">
                        <div class="accordion-header" onclick="ProductForm.toggleImageAccordion(${gIdx})" 
                            style="padding:16px; background:var(--glass-bg); cursor:pointer; display:flex; justify-content:space-between; align-items:center;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                ${colorSwatches ? `<div style="display:flex; gap:4px;">${colorSwatches}</div>` : ''}
                                <span style="font-weight:500;">${group.name}</span>
                                <span class="badge ${isValid ? 'badge-success' : 'badge-warning'}" id="accordionBadge${gIdx}" style="font-size:11px;">
                                    ${imageCount} Bild${imageCount !== 1 ? 'er' : ''}
                                </span>
                            </div>
                            <span class="material-symbols-rounded accordion-icon" id="accordionIcon${gIdx}">expand_more</span>
                        </div>
                        <div class="accordion-body" id="accordionBody${gIdx}" style="display:none; padding:16px;">
                            <div class="variant-image-upload" style="border:2px dashed var(--border-color); border-radius:8px; padding:24px; text-align:center; margin-bottom:16px;">
                                <span class="material-symbols-rounded" style="font-size:32px; color:var(--text-muted);">add_photo_alternate</span>
                                <p style="margin:8px 0 0; color:var(--text-muted);">Bilder hier ablegen oder klicken</p>
                                <input type="file" accept="image/*" multiple style="display:none;" id="groupImageInput${gIdx}"
                                    onchange="ProductForm.handleGroupImageUpload(${gIdx}, '${group.key}', this.files)">
                                <button type="button" class="btn btn-sm" style="margin-top:12px;"
                                    onclick="document.getElementById('groupImageInput${gIdx}').click()">
                                    <span class="material-symbols-rounded">upload</span> Bilder wählen
                                </button>
                            </div>
                            <div class="variant-image-gallery" id="groupGallery${gIdx}" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(100px, 1fr)); gap:12px;">
                                ${this.renderGroupImageGallery(group, gIdx)}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            this.updateImageValidationStatus();
        },

        toggleImageAccordion(idx) {
            const body = document.getElementById(`accordionBody${idx}`);
            const icon = document.getElementById(`accordionIcon${idx}`);
            if (body && icon) {
                const isOpen = body.style.display !== 'none';
                body.style.display = isOpen ? 'none' : 'block';
                icon.textContent = isOpen ? 'expand_more' : 'expand_less';
            }
        },

        updateAccordionBadge(gIdx, variantIndices) {
            const badge = document.getElementById(`accordionBadge${gIdx}`);
            if (!badge) return;

            // Get the image count from the first variant in the group
            const imageCount = this.generatedVariants[variantIndices[0]]?.images?.length || 0;
            const isValid = imageCount >= 1;

            badge.textContent = `${imageCount} Bild${imageCount !== 1 ? 'er' : ''}`;
            badge.className = `badge ${isValid ? 'badge-success' : 'badge-warning'}`;
        },

        renderGroupImageGallery(group, gIdx) {
            const images = group.variants[0]?.images || [];
            if (images.length === 0) {
                return '<p style="color:var(--text-muted); grid-column:1/-1; text-align:center; padding:20px;">Noch keine Bilder</p>';
            }

            return images.map((img, imgIdx) => `
                <div class="variant-image-item" draggable="true" 
                    data-group-idx="${gIdx}" data-img-idx="${imgIdx}"
                    ondragstart="ProductForm.handleVariantImageDragStart(event, ${gIdx}, ${imgIdx})"
                    ondragend="ProductForm.handleVariantImageDragEnd(event)"
                    ondragover="ProductForm.handleVariantImageDragOver(event)"
                    ondrop="ProductForm.handleVariantImageDrop(event, ${gIdx}, ${imgIdx})"
                    style="position:relative; aspect-ratio:1; border-radius:8px; overflow:hidden; border:1px solid var(--border-color); cursor:grab;">
                    <img src="${img.url || img}" alt="" style="width:100%; height:100%; object-fit:cover; pointer-events:none;">
                    <button type="button" onclick="event.stopPropagation(); ProductForm.removeGroupImage(${gIdx}, ${imgIdx})" 
                        style="position:absolute; top:4px; right:4px; width:24px; height:24px; border-radius:50%; background:var(--danger); color:white; border:none; cursor:pointer; z-index:10;">
                        <span class="material-symbols-rounded" style="font-size:16px;">close</span>
                    </button>
                    <span class="drag-handle material-symbols-rounded" style="position:absolute; bottom:4px; right:4px; color:white; text-shadow:0 1px 3px rgba(0,0,0,0.5); font-size:16px;">drag_indicator</span>
                    ${imgIdx === 0 ? '<span style="position:absolute; bottom:4px; left:4px; background:var(--primary); color:white; font-size:10px; padding:2px 6px; border-radius:4px;">Haupt</span>' : ''}
                </div>
            `).join('');
        },

        handleGroupImageUpload(gIdx, groupKey, files) {
            // Find variants in this group
            const groupBy = document.getElementById('imageGroupBy')?.value || 'all';
            let variantIndices = [];

            if (groupBy === 'all') {
                variantIndices = [gIdx];
            } else {
                // Recalculate group to find indices
                const groupMap = new Map();
                this.generatedVariants.forEach((v, idx) => {
                    const attrEntry = Object.values(v).find(x => x && x.attribute && x.attribute.id == groupBy);
                    if (attrEntry) {
                        const key = `attr_${groupBy}_${attrEntry.option.id}`;
                        if (!groupMap.has(key)) {
                            groupMap.set(key, []);
                        }
                        groupMap.get(key).push(idx);
                    }
                });
                variantIndices = groupMap.get(groupKey) || [gIdx];
            }

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    variantIndices.forEach(idx => {
                        if (!this.generatedVariants[idx].images) {
                            this.generatedVariants[idx].images = [];
                        }
                        this.generatedVariants[idx].images.push({
                            url: e.target.result,
                            file: file,
                            isNew: true
                        });
                    });
                    const gallery = document.getElementById(`groupGallery${gIdx}`);
                    if (gallery) {
                        const group = { variants: variantIndices.map(idx => this.generatedVariants[idx]) };
                        gallery.innerHTML = this.renderGroupImageGallery(group, gIdx);
                    }
                    this.updateAccordionBadge(gIdx, variantIndices);
                    this.updateImageValidationStatus();
                };
                reader.readAsDataURL(file);
            });
        },

        removeGroupImage(gIdx, imgIdx) {
            const groupBy = document.getElementById('imageGroupBy')?.value || 'all';
            let variantIndices = [];

            if (groupBy === 'all') {
                variantIndices = [gIdx];
            } else {
                const groupMap = new Map();
                let gCounter = 0;
                this.generatedVariants.forEach((v, idx) => {
                    const attrEntry = Object.values(v).find(x => x && x.attribute && x.attribute.id == groupBy);
                    if (attrEntry) {
                        const key = attrEntry.option.id;
                        if (!groupMap.has(key)) {
                            groupMap.set(key, { gIdx: gCounter++, indices: [] });
                        }
                        groupMap.get(key).indices.push(idx);
                    }
                });
                for (const [key, value] of groupMap.entries()) {
                    if (value.gIdx === gIdx) {
                        variantIndices = value.indices;
                        break;
                    }
                }
            }

            variantIndices.forEach(idx => {
                if (this.generatedVariants[idx]?.images) {
                    this.generatedVariants[idx].images.splice(imgIdx, 1);
                }
            });

            const gallery = document.getElementById(`groupGallery${gIdx}`);
            if (gallery) {
                const group = { variants: variantIndices.map(idx => this.generatedVariants[idx]) };
                gallery.innerHTML = this.renderGroupImageGallery(group, gIdx);
            }
            this.updateAccordionBadge(gIdx, variantIndices);
            this.updateImageValidationStatus();
        },

        updateImageValidationStatus() {
            const statusEl = document.getElementById('imageValidationStatus');
            if (!statusEl) return;

            const groupBy = document.getElementById('imageGroupBy')?.value || 'all';

            let totalCount, withImagesCount, label;

            if (groupBy === 'all') {
                // Show total variants count
                totalCount = this.generatedVariants.length;
                withImagesCount = this.generatedVariants.filter(v => (v.images || []).length >= 1).length;
                label = 'Varianten';
            } else {
                // Group by attribute - count unique groups
                // Get attribute name from variantAttributes or from the variants themselves
                let attrName = 'Gruppen';
                const attrData = this.variantAttributes?.find(a => a.id == groupBy);
                if (attrData) {
                    attrName = attrData.name;
                } else if (this.generatedVariants.length > 0) {
                    // Fallback: extract attribute name from variant data
                    const firstVariant = this.generatedVariants[0];
                    const attrEntry = Object.values(firstVariant).find(x => x && x.attribute && x.attribute.id == groupBy);
                    if (attrEntry) attrName = attrEntry.attribute.name;
                }

                const groupMap = new Map();
                this.generatedVariants.forEach(v => {
                    const attrEntry = Object.values(v).find(x => x && x.attribute && x.attribute.id == groupBy);
                    if (attrEntry) {
                        const key = attrEntry.option.id;
                        if (!groupMap.has(key)) {
                            groupMap.set(key, { hasImages: false });
                        }
                        // A group has images if any of its variants has images
                        if ((v.images || []).length >= 1) {
                            groupMap.get(key).hasImages = true;
                        }
                    }
                });

                totalCount = groupMap.size;
                withImagesCount = Array.from(groupMap.values()).filter(g => g.hasImages).length;
                label = attrName;
            }

            const allValid = withImagesCount === totalCount && totalCount > 0;

            statusEl.innerHTML = `
                <span class="material-symbols-rounded" style="color:${allValid ? 'var(--success)' : 'var(--warning)'};">
                    ${allValid ? 'check_circle' : 'warning'}
                </span>
                <span style="font-size:13px; color:var(--text-muted);">
                    ${withImagesCount}/${totalCount} ${label} mit Bildern
                </span>
            `;
        },

        // ==================== VARIANT IMAGE DRAG & DROP ====================

        _variantDragData: null,

        handleVariantImageDragStart(event, gIdx, imgIdx) {
            this._variantDragData = { gIdx, imgIdx };
            event.target.classList.add('dragging');
            event.target.style.opacity = '0.5';
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', `${gIdx}:${imgIdx}`);
        },

        handleVariantImageDragEnd(event) {
            event.target.classList.remove('dragging');
            event.target.style.opacity = '1';
            this._variantDragData = null;
            // Remove any drag-over styling from all items
            document.querySelectorAll('.variant-image-item').forEach(el => {
                el.style.transform = '';
                el.style.border = '';
            });
        },

        handleVariantImageDragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            // Add visual feedback
            const target = event.target.closest('.variant-image-item');
            if (target && !target.classList.contains('dragging')) {
                target.style.border = '2px solid var(--primary)';
            }
        },

        handleVariantImageDrop(event, targetGIdx, targetImgIdx) {
            event.preventDefault();
            const target = event.target.closest('.variant-image-item');
            if (target) {
                target.style.border = '';
            }

            if (!this._variantDragData) return;

            const { gIdx: sourceGIdx, imgIdx: sourceImgIdx } = this._variantDragData;

            // Only allow reordering within the same group
            if (sourceGIdx !== targetGIdx) {
                this.showToast('Bilder können nur innerhalb derselben Gruppe verschoben werden', 'error');
                return;
            }

            // Don't do anything if dropping on itself
            if (sourceImgIdx === targetImgIdx) return;

            // Get the variant indices for this group
            const groupBy = document.getElementById('imageGroupBy')?.value || 'all';
            let variantIndices = this.getVariantIndicesForGroup(sourceGIdx, groupBy);

            // Reorder images for all variants in the group
            variantIndices.forEach(idx => {
                const variant = this.generatedVariants[idx];
                if (variant && variant.images && variant.images.length > 1) {
                    const images = variant.images;
                    const [movedImage] = images.splice(sourceImgIdx, 1);
                    images.splice(targetImgIdx, 0, movedImage);
                }
            });

            // Re-render the gallery
            const gallery = document.getElementById(`groupGallery${sourceGIdx}`);
            if (gallery) {
                const group = { variants: variantIndices.map(idx => this.generatedVariants[idx]) };
                gallery.innerHTML = this.renderGroupImageGallery(group, sourceGIdx);
            }

            this.showToast('Bildreihenfolge aktualisiert', 'success');
        },

        getVariantIndicesForGroup(gIdx, groupBy) {
            if (groupBy === 'all') {
                return [gIdx];
            }

            const groupMap = new Map();
            let counter = 0;

            this.generatedVariants.forEach((v, idx) => {
                const attrEntry = Object.values(v).find(x => x && x.attribute && x.attribute.id == groupBy);
                if (attrEntry) {
                    const key = attrEntry.option.id;
                    if (!groupMap.has(key)) {
                        groupMap.set(key, { gIdx: counter++, indices: [] });
                    }
                    groupMap.get(key).indices.push(idx);
                }
            });

            for (const [key, value] of groupMap.entries()) {
                if (value.gIdx === gIdx) {
                    return value.indices;
                }
            }

            return [gIdx];
        },

        // ==================== CURRENCY SYSTEM ====================

        async loadShopCurrency() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_shop_currency&shop_id=${this.shopId}`);
                const data = await res.json();
                if (data.success) {
                    this.defaultCurrency = data.default_currency || { code: 'USD', symbol: '$' };
                    this.shopCurrencies = data.currencies || [];
                    this.populateCurrencyDropdown();
                    this.updateCurrencyLabels();
                }
            } catch (e) {
                console.error('Error loading shop currencies:', e);
            }
        },

        populateCurrencyDropdown() {
            const select = document.getElementById('baseCurrencySelect');
            if (!select || !this.shopCurrencies.length) return;

            select.innerHTML = this.shopCurrencies.map(c => `
                <option value="${c.code}" ${c.code === this.defaultCurrency.code ? 'selected' : ''}>
                    ${c.symbol} ${c.code}
                </option>
            `).join('');
        },

        updateCurrencyLabels() {
            const select = document.getElementById('baseCurrencySelect');
            const code = select?.value || 'USD';
            const currency = this.shopCurrencies.find(c => c.code === code) || { symbol: '$' };

            ['priceSymbol', 'specialSymbol', 'costSymbol'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = currency.symbol;
            });

            this.calculateCurrencyPreview();
        },

        toggleRounding() {
            const enabled = document.getElementById('enableRounding').checked;
            const select = document.getElementById('roundingStep');
            select.disabled = !enabled;
            // Don't reset value - keep last selection so it's remembered when re-enabled
            this.calculateCurrencyPreview();
        },

        // Toggle variant-specific rounding
        toggleVariantRounding() {
            const enabled = document.getElementById('variantEnableRounding')?.checked || false;
            const select = document.getElementById('variantRoundingStep');
            if (select) {
                select.disabled = !enabled;
            }
            // Refresh variant prices to apply rounding
            this.renderVariantPricesAccordions();
        },

        // Refresh variant prices (called when rounding step changes)
        refreshVariantPrices() {
            this.renderVariantPricesAccordions();
        },

        // Validate special price date range and prevent end < start
        validateSpecialPriceDates() {
            const fromInput = document.getElementById('specialFrom');
            const toInput = document.getElementById('specialTo');
            const fromDate = fromInput?.value;
            const toDate = toInput?.value;

            if (fromDate && toDate) {
                const from = new Date(fromDate);
                const to = new Date(toDate);

                if (to < from) {
                    // End date is before start date - fix it
                    toInput.value = fromDate;
                    this.showToast('Enddatum kann nicht vor dem Startdatum liegen', 'error');
                }
            }

            // Update variant displays with new dates
            this.renderVariantPricesAccordions();
            this.calculateCurrencyPreview();
        },

        async calculateCurrencyPreview() {
            const basePrice = this.parseLocaleNumber(document.getElementById('productPrice')?.value);
            const specialPrice = this.parseLocaleNumber(document.getElementById('specialPrice')?.value);
            const baseCurrency = document.getElementById('baseCurrencySelect')?.value || 'USD';
            const roundingStep = document.getElementById('enableRounding')?.checked
                ? this.parseLocaleNumber(document.getElementById('roundingStep')?.value)
                : 0;

            if (basePrice <= 0) {
                document.getElementById('currencyPricesBody').innerHTML = `
                    <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">
                        Geben Sie einen Preis ein, um die Währungsvorschau zu sehen
                    </td></tr>
                `;
                return;
            }

            try {
                const params = new URLSearchParams({
                    action: 'calculate_prices',
                    shop_id: this.shopId,
                    base_price: basePrice,
                    special_price: specialPrice || '',
                    base_currency: baseCurrency,
                    rounding_step: roundingStep,
                    product_id: 0
                });

                const res = await fetch(`${this.apiBase}?${params}`);
                const data = await res.json();

                if (data.success && data.prices) {
                    this.currencyPrices = data.prices;
                    this.renderCurrencyPrices();
                }
                // Also refresh variant prices if variants exist (real-time update)
                if (this.generatedVariants.length >= 2) {
                    this.renderVariantPricesAccordions();
                }
            } catch (e) {
                console.error('Error calculating prices:', e);
            }
        },

        renderCurrencyPrices() {
            const tbody = document.getElementById('currencyPricesBody');
            const baseCurrency = document.getElementById('baseCurrencySelect')?.value || 'USD';

            // Always show all currencies (except base currency)
            let currenciesToShow = Object.entries(this.currencyPrices)
                .filter(([code]) => code !== baseCurrency);

            // Update count
            const countEl = document.getElementById('currencyCount');
            if (countEl) countEl.textContent = currenciesToShow.length;

            if (currenciesToShow.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--text-muted);">Keine Währungen verfügbar</td></tr>';
                return;
            }

            tbody.innerHTML = currenciesToShow.map(([code, data]) => this.renderCurrencyRow(code, data)).join('');
        },

        renderCurrencyRow(code, data) {
            const hasOverride = !!this.currencyOverrides[code]?.price;
            const overrideValue = this.currencyOverrides[code]?.price || '';
            const calculatedPrice = parseFloat(data.calculated_price || 0).toFixed(4);
            const specialPrice = data.calculated_special ? parseFloat(data.calculated_special).toFixed(2) : null;

            return `
                <tr class="${hasOverride ? 'has-override' : ''}">
                    <td>
                        <div class="currency-name">
                            <span class="currency-code">${code}</span>
                            <span class="currency-symbol">${data.name || ''}</span>
                        </div>
                    </td>
                    <td class="calculated-price">
                        ${data.symbol} ${calculatedPrice}
                        ${specialPrice ? `<br><small>Sonder: ${data.symbol} ${specialPrice}</small>` : ''}
                    </td>
                    <td>
                        <input type="number" class="form-input override-input" 
                            value="${overrideValue}" 
                            placeholder="${data.symbol}"
                            step="0.01" min="0"
                            onchange="ProductForm.updateOverride('${code}', this.value)">
                    </td>
                    <td>
                        ${hasOverride ? `
                            <button type="button" class="remove-btn" onclick="ProductForm.removeOverride('${code}')" title="Override entfernen">
                                <span class="material-symbols-rounded">close</span>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        },

        updateOverride(code, value) {
            if (value && parseFloat(value) > 0) {
                this.currencyOverrides[code] = { price: parseFloat(value) };
            } else {
                delete this.currencyOverrides[code];
            }
            this.renderCurrencyPrices();
        },

        removeOverride(code) {
            delete this.currencyOverrides[code];
            this.renderCurrencyPrices();
        },

        filterCurrencies() {
            const searchTerm = document.getElementById('currencySearch').value.toLowerCase();
            const rows = document.querySelectorAll('#currencyPricesBody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const currencyCode = row.querySelector('.currency-code')?.textContent.toLowerCase() || '';
                const currencyName = row.querySelector('.currency-symbol')?.textContent.toLowerCase() || '';

                if (currencyCode.includes(searchTerm) || currencyName.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const countEl = document.getElementById('currencyCount');
            if (countEl) countEl.textContent = visibleCount;
        },

        toggleShowAll() {
            this.calculateCurrencyPreview();
        },

        async saveCurrencyPrices(productId) {
            const baseCurrency = document.getElementById('baseCurrencySelect')?.value || 'USD';
            const roundingStep = document.getElementById('enableRounding')?.checked
                ? document.getElementById('roundingStep')?.value
                : null;

            const formData = new FormData();
            formData.append('action', 'save_currency_prices');
            formData.append('shop_id', this.shopId);
            formData.append('product_id', productId);
            formData.append('base_currency', baseCurrency);
            formData.append('rounding_step', roundingStep || '');
            formData.append('prices', JSON.stringify(this.currencyOverrides));

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();
                return data.success;
            } catch (e) {
                console.error('Error saving currency prices:', e);
                return false;
            }
        },

        // ==================== MARGIN CALCULATION ====================

        calculateMargin() {
            const marginDisplay = document.getElementById('marginDisplay');
            const marginAmountEl = document.getElementById('marginAmount');
            const marginPercentEl = document.getElementById('marginPercent');

            if (!marginDisplay || !marginAmountEl || !marginPercentEl) return;

            const regularPrice = this.parseLocaleNumber(document.getElementById('productPrice')?.value);
            const specialPrice = this.parseLocaleNumber(document.getElementById('specialPrice')?.value);
            const costPrice = this.parseLocaleNumber(document.getElementById('costPrice')?.value);

            // Use special price if set and lower than regular price
            const effectivePrice = (specialPrice > 0 && specialPrice < regularPrice) ? specialPrice : regularPrice;

            // Only show if we have both a cost price and a selling price
            if (costPrice <= 0 || effectivePrice <= 0) {
                marginDisplay.style.display = 'none';
                return;
            }

            // Calculate margin
            const marginAmount = effectivePrice - costPrice;
            const marginPercent = (marginAmount / costPrice) * 100;

            // Get currency symbol
            const symbol = document.getElementById('priceSymbol')?.textContent || '€';

            // Update display
            marginDisplay.style.display = 'block';
            marginAmountEl.textContent = `${symbol}${marginAmount.toFixed(2)}`;

            // Color coding and display
            const isPositive = marginPercent >= 0;
            marginPercentEl.textContent = `${isPositive ? '+' : ''}${marginPercent.toFixed(0)}%`;
            marginPercentEl.style.background = isPositive ? 'var(--success)' : 'var(--danger)';
            marginPercentEl.style.color = 'white';
            marginAmountEl.style.color = isPositive ? 'var(--success)' : 'var(--danger)';
        }
    };

    document.addEventListener('DOMContentLoaded', () => ProductForm.init());
</script>