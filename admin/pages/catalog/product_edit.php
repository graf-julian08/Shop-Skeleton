<?php
/** Katalog - Produkt bearbeiten */
$productId = (int) ($_GET['id'] ?? 0);
?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/products">Produkte</a> <span>›</span> <span
                id="breadcrumbName">Produkt bearbeiten</span></nav>
        <h1 id="pageTitle">Produkt bearbeiten</h1>
        <p class="page-subtitle">Produktdetails bearbeiten und speichern</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-danger-ghost" onclick="ProductEdit.deleteProduct()"><span
                class="material-symbols-rounded">delete</span> Löschen</button>
        <a href="?page=catalog/products" class="btn">Abbrechen</a>
        <button class="btn btn-primary" onclick="ProductEdit.save()"><span class="material-symbols-rounded">save</span>
            Speichern</button>
    </div>
</div>

<div class="product-edit-container" id="productContainer" style="display:none;">
    <!-- Status Bar -->
    <div class="status-bar card" id="statusBar">
        <div class="status-info">
            <span class="status-label">Status:</span>
            <span class="badge" id="statusBadge">-</span>
        </div>
        <div class="status-actions">
            <button class="btn btn-sm" id="btnActivate" onclick="ProductEdit.setStatus('active')"><span
                    class="material-symbols-rounded">check_circle</span> Aktivieren</button>
            <button class="btn btn-sm" id="btnDeactivate" onclick="ProductEdit.setStatus('draft')"><span
                    class="material-symbols-rounded">pause_circle</span> Als Entwurf</button>
            <button class="btn btn-sm" id="btnArchive" onclick="ProductEdit.setStatus('archived')"><span
                    class="material-symbols-rounded">archive</span> Archivieren</button>
        </div>
    </div>

    <div class="tabs" id="editTabs">
        <button class="tab active" data-tab="general">Allgemein</button>
        <button class="tab" data-tab="variants">Variationen</button>
        <button class="tab" data-tab="pricing">Preise</button>
        <button class="tab" data-tab="inventory">Inventar</button>
        <button class="tab" data-tab="images">Bilder</button>
        <button class="tab" data-tab="seo">SEO</button>
    </div>

    <form id="productForm">
        <input type="hidden" name="id" id="productId" value="<?= $productId ?>">

        <!-- Tab: Allgemein -->
        <div class="tab-content active" data-tab-content="general">
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Grunddaten</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Produkttyp</label>
                            <select class="form-select" name="type" id="productType"
                                onchange="ProductEdit.updateTypeFields()">
                                <option value="simple">Physisches Produkt</option>
                                <option value="digital">Digitales Produkt</option>
                                <option value="bundle">Bundle</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Produktname <span class="required">*</span></label>
                            <input type="text" class="form-input" name="name" id="productName" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">SKU (Artikelnummer) <span class="required">*</span></label>
                            <input type="text" class="form-input" name="sku" id="productSku" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">URL-Slug</label>
                            <input type="text" class="form-input" name="slug" id="productSlug">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kurzbeschreibung</label>
                            <textarea class="form-textarea" name="short_description" id="shortDescription"
                                rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Beschreibung</label>
                            <textarea class="form-textarea" name="description" id="description" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Kategorien & Sichtbarkeit</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Kategorien <span class="required">*</span></label>
                            <div class="category-checkboxes" id="categoryCheckboxes"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sichtbarkeit</label>
                            <div class="toggle-group">
                                <label class="toggle-label"><input type="checkbox" name="is_visible" id="isVisible"
                                        value="1"> Im Shop anzeigen</label>
                                <label class="toggle-label"><input type="checkbox" name="is_featured" id="isFeatured"
                                        value="1"> Als Featured markieren</label>
                                <label class="toggle-label"><input type="checkbox" name="is_new" id="isNew" value="1">
                                    Als Neu markieren</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Preise -->
        <div class="tab-content" data-tab-content="pricing" style="display:none;">
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>💰 Preisinformationen</h3>
                        <div class="currency-selector">
                            <label>Basiswährung:</label>
                            <select id="baseCurrencySelect" onchange="ProductEdit.updateCurrencyLabels()">
                                <option value="USD">$ USD</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Regulärer Preis (<span id="priceSymbol">$</span>) <span
                                        class="required">*</span></label>
                                <input type="number" class="form-input" name="price" id="productPrice" step="0.01"
                                    min="0" required
                                    oninput="ProductEdit.calculateCurrencyPreview(); ProductEdit.calculateMargin()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sonderpreis (<span id="specialSymbol">$</span>)</label>
                                <input type="number" class="form-input" name="special_price" id="specialPrice"
                                    step="0.01" min="0"
                                    oninput="ProductEdit.calculateCurrencyPreview(); ProductEdit.calculateMargin()">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Sonderpreis von</label>
                                <input type="date" class="form-input" name="special_price_from" id="specialFrom"
                                    onchange="ProductEdit.validateSpecialPriceDates()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sonderpreis bis</label>
                                <input type="date" class="form-input" name="special_price_to" id="specialTo"
                                    onchange="ProductEdit.validateSpecialPriceDates()">
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
                            <input type="number" class="form-input" name="cost_price" id="costPrice" step="0.01" min="0"
                                oninput="ProductEdit.calculateMargin()">
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
                            <select class="form-select" name="tax_class_id" id="taxClassId">
                                <option value="1">Standard (19%)</option>
                                <option value="2">Ermäßigt (7%)</option>
                                <option value="3">Steuerfrei</option>
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
                            <input type="checkbox" id="enableRounding" onchange="ProductEdit.toggleRounding()">
                            <span>Runden auf:</span>
                        </label>
                        <select id="roundingStep" onchange="ProductEdit.calculateCurrencyPreview()" disabled>
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

                    <div id="currencyPricesLoading" class="empty-state" style="display:none;">
                        <span class="material-symbols-rounded spinning">sync</span>
                        <p>Währungen werden geladen...</p>
                    </div>

                    <div class="currency-prices-header">
                        <div class="currency-search">
                            <span class="material-symbols-rounded">search</span>
                            <input type="text" id="currencySearch" placeholder="Währung suchen..."
                                oninput="ProductEdit.filterCurrencies()">
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
                                onchange="ProductEdit.toggleVariantRounding()">
                            <span>Runden auf:</span>
                        </label>
                        <select id="variantRoundingStep" onchange="ProductEdit.refreshVariantPrices()" disabled>
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
                        Klicken Sie auf eine Variante, um die Währungspreise anzupassen. Preise werden automatisch aus
                        dem Hauptpreis berechnet.
                    </p>

                    <!-- Varianten-Liste mit expandierbaren Währungs-Überschreibungen -->
                    <div id="variantPricesAccordions" style="display:flex; flex-direction:column; gap:8px;">
                        <!-- Dynamisch generiert -->
                    </div>
                </div>
            </div>
        </div>
</div>

<!-- Tab: Inventar -->
<div class="tab-content" data-tab-content="inventory" style="display:none;">

    <!-- Info-Banner wenn Varianten existieren -->
    <div id="inventoryVariantsBanner" class="info-banner"
        style="display:none; margin-bottom:24px; padding:16px; background:var(--glass-bg); border-radius:12px; border-left:4px solid var(--primary);">
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
                        <input type="checkbox" name="manage_stock" id="manageStock" value="1"
                            onchange="ProductEdit.toggleStockFields()">
                        Bestand verfolgen
                    </label>
                </div>
                <div id="stockFields">
                    <div class="form-group">
                        <label class="form-label">Lagermenge</label>
                        <input type="number" class="form-input" name="quantity" id="quantity" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mindestbestand (Warnung)</label>
                        <input type="number" class="form-input" name="low_stock_threshold" id="lowStock" min="0">
                    </div>
                    <div class="form-group">
                        <label class="toggle-label">
                            <input type="checkbox" name="allow_backorders" id="allowBackorders" value="1">
                            Rückbestellungen erlauben
                        </label>
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
                    <input type="number" class="form-input" name="weight" id="weight" step="0.1" min="0">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Länge (cm)</label>
                        <input type="number" class="form-input" name="length" id="length" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Breite (cm)</label>
                        <input type="number" class="form-input" name="width" id="width" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Höhe (cm)</label>
                        <input type="number" class="form-input" name="height" id="height" min="0">
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
                <div id="downloadFilesList" style="display:flex; flex-direction:column; gap:12px; margin-bottom:16px;">
                    <!-- Dynamically generated -->
                </div>

                <button type="button" class="btn btn-sm" onclick="ProductEdit.addDownloadFile()">
                    <span class="material-symbols-rounded">add</span> Download hinzufügen
                </button>

                <hr style="margin:24px 0; border-color:var(--border-color);">

                <h4 style="font-size:15px; margin-bottom:16px;">Einschränkungen</h4>

                <div class="form-row" style="gap:24px;">
                    <div class="form-group">
                        <label class="form-label">Download-Limit pro Kunde</label>
                        <input type="number" class="form-input" name="download_limit" id="downloadLimit" min="0"
                            style="width:120px;">
                        <p class="form-hint">0 = Unbegrenzte Downloads</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Link läuft ab nach (Tage)</label>
                        <input type="number" class="form-input" name="download_expiry_days" id="downloadExpiry" min="0"
                            style="width:120px;">
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
                    <button type="button" class="btn btn-sm" onclick="ProductEdit.setAllVariantStock()"
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
</div>

<!-- Tab: Bilder -->
<div class="tab-content" data-tab-content="images" style="display:none;">

    <!-- Info-Banner wenn Varianten existieren -->
    <div id="imagesVariantsBanner" class="info-banner"
        style="display:none; margin-bottom:24px; padding:16px; background:var(--glass-bg); border-radius:12px; border-left:4px solid var(--primary);">
        <span class="material-symbols-rounded" style="color:var(--primary);">info</span>
        <span>Laden Sie für jede Variante mindestens 1 Bild hoch. Unbegrenzt viele Bilder pro Variante möglich.</span>
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
                        <input type="file" id="imageInput" accept="image/*" multiple style="display:none;">
                        <button type="button" class="btn btn-primary" style="margin-top:16px;"
                            onclick="document.getElementById('imageInput').click()">
                            <span class="material-symbols-rounded">upload</span> Bilder auswählen
                        </button>
                    </div>
                </div>
                <div class="image-gallery" id="imageGallery" style="margin-top:24px;"></div>
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
                        onchange="ProductEdit.updateImageGrouping()">
                        <option value="all">Alle Varianten einzeln</option>
                    </select>
                    <div id="imageValidationStatus" style="display:flex; align-items:center; gap:8px;"></div>
                </div>
            </div>
            <div class="card-body">
                <div id="variantImageAccordions">
                    <!-- Dynamically generated per-variant/group accordions -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Variationen -->
<div class="tab-content" data-tab-content="variants" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>Produktvariationen</h3>
        </div>
        <div class="card-body">
            <p class="form-hint" style="margin-bottom:20px;">
                Wählen Sie Attribute aus, um Produktvariationen zu erstellen (z.B. verschiedene Farben und
                Größen).
            </p>

            <!-- Attribute Selection -->
            <div class="variant-attributes" id="variantAttributes">
                <div class="loading-state" style="padding:20px;">
                    <span class="material-symbols-rounded spinning" style="font-size:24px;">sync</span>
                    <p>Lade Attribute...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Options -->
    <div class="card" id="variantOptionsCard" style="margin-top:24px; display:none;">
        <div class="card-header">
            <h3>Optionen auswählen</h3>
        </div>
        <div class="card-body" id="variantOptionsBody">
            <!-- Dynamic attribute options will be rendered here -->
        </div>
    </div>

    <!-- Generated Variants -->
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
                            <th style="width:60px;text-align:center;">Aktiv</th>
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

<!-- Tab: SEO -->
<div class="tab-content" data-tab-content="seo" style="display:none;">
    <div class="card">
        <div class="card-header">
            <h3>Suchmaschinenoptimierung</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Meta-Titel <span class="required">*</span></label>
                <input type="text" class="form-input" name="meta_title" id="metaTitle" maxlength="60">
                <small style="color:var(--text-muted);"><span id="metaTitleCount">0</span>/60 Zeichen</small>
                <p class="form-error" id="errorMetaTitle"></p>
            </div>
            <div class="form-group">
                <label class="form-label">Meta-Beschreibung <span class="required">*</span></label>
                <textarea class="form-textarea" name="meta_description" id="metaDescription" rows="3"
                    maxlength="160"></textarea>
                <small style="color:var(--text-muted);"><span id="metaDescCount">0</span>/160 Zeichen</small>
                <p class="form-error" id="errorMetaDescription"></p>
            </div>
            <div class="form-group">
                <label class="form-label">Keywords <span class="required">*</span></label>
                <input type="text" class="form-input" name="meta_keywords" id="metaKeywords">
                <p class="form-error" id="errorMetaKeywords"></p>
            </div>
        </div>
    </div>
</div>
</form>
</div>

<!-- Loading State -->
<div class="loading-state" id="loadingState">
    <span class="material-symbols-rounded spinning">sync</span>
    <p>Produkt wird geladen...</p>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="confirmModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Produkt löschen</h3>
            <button class="modal-close" onclick="ProductEdit.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Möchten Sie dieses Produkt wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.</p>
        </div>
        <div class="modal-footer">
            <button class="btn" onclick="ProductEdit.closeModal()">Abbrechen</button>
            <button class="btn btn-danger" onclick="ProductEdit.confirmDelete()">Löschen</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<!-- Inventory Bulk Set Modal -->
<div class="modal-overlay" id="inventoryBulkModal"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div class="modal-content" style="max-width:400px;">
        <div class="modal-header">
            <h3>Inventar für alle Varianten</h3>
            <button type="button" class="modal-close" onclick="ProductEdit.closeInventoryModal()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        <div class="modal-body">
            <p class="form-hint" style="margin-bottom:16px;">
                Diese Werte werden für alle <strong id="inventoryModalCount">0</strong> Varianten übernommen.
            </p>
            <div class="form-group">
                <label class="form-label">Lagerbestand</label>
                <input type="number" class="form-input" id="bulkStock" min="0" placeholder="0">
            </div>
            <div class="form-group">
                <label class="form-label">Gewicht (kg)</label>
                <input type="number" class="form-input" id="bulkWeight" step="0.1" min="0" placeholder="0.0">
            </div>
            <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                <div class="form-group">
                    <label class="form-label">Länge (cm)</label>
                    <input type="number" class="form-input" id="bulkLength" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Breite (cm)</label>
                    <input type="number" class="form-input" id="bulkWidth" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Höhe (cm)</label>
                    <input type="number" class="form-input" id="bulkHeight" min="0" placeholder="0">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" onclick="ProductEdit.closeInventoryModal()">Abbrechen</button>
            <button type="button" class="btn btn-primary" onclick="ProductEdit.applyBulkInventory()">
                <span class="material-symbols-rounded">check</span> Übernehmen
            </button>
        </div>
    </div>
</div>

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
    }

    .image-gallery {
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
        z-index: 2;
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

    .image-preview-item {
        transition: transform 0.15s ease, opacity 0.15s ease;
    }

    .image-preview-item.dragging {
        opacity: 0.5;
        transform: scale(1.05);
        z-index: 100;
    }

    .image-preview-item:hover {
        transform: scale(1.02);
    }

    .image-upload-zone.dragover {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.05);
    }

    .margin-display {
        font-size: 24px;
        font-weight: 600;
        color: var(--success);
    }

    .margin-display.negative {
        color: var(--error);
    }

    /* Currency Pricing Styles */
    .currency-selector {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .currency-selector label {
        font-size: 13px;
        color: var(--text-muted);
    }

    .currency-selector select {
        padding: 6px 12px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-tertiary);
        font-weight: 500;
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
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px;
    }

    .currency-table .remove-btn:hover {
        color: var(--error);
    }

    .currency-table-scroll {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
    }

    .currency-table .override-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .currency-table .override-group label {
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
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
        font-size: 18px;
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
</style>

<script>
    const ProductEdit = {
        apiBase: '/admin/api/products.php',
        attrApi: '/admin/api/attributes.php',
        shopId: 1,
        productId: <?= $productId ?>,
        product: null,
        uploadedImages: [],      // New images to upload
        existingImages: [],      // Images from DB
        deletedImageIds: [],     // IDs of images to delete
        // Variant system
        variantAttributes: [],   // Attributes marked for variants
        selectedAttributes: [],  // User-selected attribute IDs
        selectedOptions: {},     // {attributeId: [optionIds]}
        generatedVariants: [],   // Generated variant combinations
        savedVariants: [],        // Variants loaded from database
        deletedVariantIds: [],    // IDs of variants to delete
        // Currency pricing
        shopCurrencies: [],      // All active currencies
        defaultCurrency: null,   // Shop's default currency
        currencyOverrides: {},   // User-defined price overrides
        variantCurrency: null,   // Current currency for variant display

        async init() {
            if (!this.productId) {
                window.location.href = '?page=catalog/products';
                return;
            }

            await this.loadCategories();
            await this.loadShopCurrency();  // Load currencies first
            await this.loadProduct();
            await this.loadVariantAttributes();
            this.setupTabs();
            this.setupEventListeners();
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
                this.populateForm();

                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('productContainer').style.display = 'block';

            } catch (e) {
                this.showToast('Fehler beim Laden: ' + e.message, 'error');
            }
        },

        populateForm() {
            const p = this.product;

            // Title
            document.getElementById('pageTitle').textContent = p.name;
            document.getElementById('breadcrumbName').textContent = p.name;

            // Status
            this.updateStatusUI(p.status);

            // Basic fields
            document.getElementById('productType').value = p.type;
            document.getElementById('productName').value = p.name;
            document.getElementById('productSku').value = p.sku;
            document.getElementById('productSlug').value = p.slug || '';
            document.getElementById('shortDescription').value = p.short_description || '';
            document.getElementById('description').value = p.description || '';

            // Categories
            if (p.categories) {
                const catIds = p.categories.map(c => String(c.id));
                document.querySelectorAll('#categoryCheckboxes input[type="checkbox"]').forEach(cb => {
                    cb.checked = catIds.includes(cb.value);
                });
            }

            // Visibility
            document.getElementById('isVisible').checked = p.is_visible == 1;
            document.getElementById('isFeatured').checked = p.is_featured == 1;
            document.getElementById('isNew').checked = p.is_new == 1;

            // Pricing
            document.getElementById('productPrice').value = parseFloat(p.price) || 0;
            document.getElementById('specialPrice').value = p.special_price ? parseFloat(p.special_price) : '';
            document.getElementById('specialFrom').value = p.special_price_from || '';
            document.getElementById('specialTo').value = p.special_price_to || '';
            document.getElementById('costPrice').value = p.cost_price ? parseFloat(p.cost_price) : '';
            document.getElementById('taxClassId').value = p.tax_class_id || 1;

            // Currency settings
            if (p.base_currency) {
                const baseCurrencySelect = document.getElementById('baseCurrencySelect');
                if (baseCurrencySelect) {
                    baseCurrencySelect.value = p.base_currency;
                    this.updateCurrencyLabels();
                }
            }
            if (p.price_rounding_step && parseFloat(p.price_rounding_step) > 0) {
                document.getElementById('enableRounding').checked = true;
                document.getElementById('roundingStep').disabled = false;
                // Convert 5.0000 to "5" format for dropdown matching
                const roundingValue = parseFloat(p.price_rounding_step);
                document.getElementById('roundingStep').value = roundingValue.toString();
            }

            // Load currency overrides from database
            this.loadCurrencyOverrides();

            // Inventory
            document.getElementById('manageStock').checked = p.manage_stock == 1;
            document.getElementById('quantity').value = p.quantity || 0;
            document.getElementById('lowStock').value = p.low_stock_threshold || 5;
            document.getElementById('allowBackorders').checked = p.allow_backorders == 1;

            // Shipping
            document.getElementById('weight').value = p.weight || '';
            document.getElementById('length').value = p.length || '';
            document.getElementById('width').value = p.width || '';
            document.getElementById('height').value = p.height || '';

            // Digital
            document.getElementById('downloadLimit').value = p.download_limit || 0;
            document.getElementById('downloadExpiry').value = p.download_expiry_days || 0;

            // SEO
            document.getElementById('metaTitle').value = p.meta_title || '';
            document.getElementById('metaDescription').value = p.meta_description || '';
            document.getElementById('metaKeywords').value = p.meta_keywords || '';
            document.getElementById('metaTitleCount').textContent = (p.meta_title || '').length;
            document.getElementById('metaDescCount').textContent = (p.meta_description || '').length;

            // Load existing images
            this.existingImages = p.images || [];
            this.renderImages();

            // Load saved variants from database
            this.loadSavedVariants(p.variants || []);

            this.updateTypeFields();
            this.toggleStockFields();
            this.updateMargin();
        },

        // Load and display saved variants from database
        loadSavedVariants(savedVariants) {
            if (!savedVariants || savedVariants.length === 0) {
                this.savedVariants = [];
                this.generatedVariants = [];
                return;
            }

            this.savedVariants = savedVariants;

            // Wait for variant attributes to load, then restore selections
            this.waitForAttributesAndRestore(savedVariants);
        },

        waitForAttributesAndRestore(savedVariants) {
            // If attributes not loaded yet, wait
            if (!this.variantAttributes || this.variantAttributes.length === 0) {
                setTimeout(() => this.waitForAttributesAndRestore(savedVariants), 100);
                return;
            }

            this.restoreVariantSelections(savedVariants);
            this.renderSavedVariants();

            // Also populate generatedVariants for inventory/images/prices
            this.convertSavedToGeneratedVariants(savedVariants);

            // Update variant-aware tabs
            this.onVariantsChanged();
        },

        restoreVariantSelections(savedVariants) {
            // Clear existing selections
            this.selectedAttributes = [];
            this.selectedOptions = {};

            // For each saved variant, extract and restore attribute/option selections
            savedVariants.forEach(variant => {
                let attributes = variant.attributes;
                if (typeof attributes === 'string') {
                    try {
                        attributes = JSON.parse(attributes);
                    } catch (e) {
                        attributes = {};
                    }
                }

                // attributes format: {"Farbe": "Rot", "Größe": "S"}
                Object.entries(attributes).forEach(([attrName, optionValue]) => {
                    // Find the attribute by name
                    const attr = this.variantAttributes.find(a =>
                        a.name === attrName || a.code === attrName
                    );

                    if (attr) {
                        // Add attribute to selectedAttributes if not already there
                        if (!this.selectedAttributes.includes(attr.id)) {
                            this.selectedAttributes.push(attr.id);
                        }

                        // Find the option by value or label
                        const option = attr.options?.find(o =>
                            o.value === optionValue || o.label === optionValue
                        );

                        if (option) {
                            // Add to selectedOptions
                            if (!this.selectedOptions[attr.id]) {
                                this.selectedOptions[attr.id] = [];
                            }
                            if (!this.selectedOptions[attr.id].includes(option.id)) {
                                this.selectedOptions[attr.id].push(option.id);
                            }
                        }
                    }
                });
            });

            // Re-render attributes and options with restored selections
            this.renderVariantAttributes();
            if (this.selectedAttributes.length > 0) {
                this.renderVariantOptions();
            }
        },

        convertSavedToGeneratedVariants(savedVariants) {
            // Convert saved variants to generatedVariants format for inventory/images/prices
            this.generatedVariants = savedVariants.map(variant => {
                let attributes = variant.attributes;
                if (typeof attributes === 'string') {
                    try {
                        attributes = JSON.parse(attributes);
                    } catch (e) {
                        attributes = {};
                    }
                }

                // Build the generatedVariant format: { attrId: { attribute, option }, ... }
                const generated = {};

                // Store raw attributes for fallback name generation
                generated._rawAttributes = attributes;

                // Build display name from raw attributes as fallback
                generated._displayName = Object.entries(attributes || {})
                    .map(([name, value]) => value)
                    .join(' / ') || variant.sku || `Variante ${variant.id}`;

                Object.entries(attributes).forEach(([attrName, optionValue]) => {
                    const attr = this.variantAttributes?.find(a =>
                        a.name === attrName || a.code === attrName
                    );

                    if (attr) {
                        const option = attr.options?.find(o =>
                            o.value === optionValue || o.label === optionValue
                        );

                        if (option) {
                            generated[attr.id] = {
                                attribute: attr,
                                option: option
                            };
                        } else {
                            // Create fallback option object if not found
                            generated[attr.id] = {
                                attribute: { id: attr.id, name: attrName },
                                option: { id: 0, label: optionValue, value: optionValue }
                            };
                        }
                    } else {
                        // Create fallback if attribute not found
                        generated[`attr_${attrName}`] = {
                            attribute: { id: 0, name: attrName },
                            option: { id: 0, label: optionValue, value: optionValue }
                        };
                    }
                });

                // Add variant-specific data
                generated.id = variant.id;
                generated.sku = variant.sku;
                generated.is_active = variant.is_active;
                generated.price = variant.price;
                generated.special_price = variant.special_price;
                generated.stock = variant.quantity || 0;
                generated.weight = variant.weight;
                generated.length = variant.length;
                generated.width = variant.width;
                generated.height = variant.height;
                generated.images = variant.images || [];
                generated.price_adjustment = variant.price_adjustment || 0;

                return generated;
            });
        },

        // Render saved variants from database (different structure than auto-generated)
        renderSavedVariants() {
            const card = document.getElementById('generatedVariantsCard');
            const tbody = document.getElementById('variantsBody');
            const countBadge = document.getElementById('variantCount');
            const noVariants = document.getElementById('noVariants');

            if (!this.savedVariants || this.savedVariants.length === 0) {
                return;
            }

            card.style.display = 'block';
            noVariants.style.display = 'none';
            document.querySelector('.variants-table-wrapper').style.display = 'block';
            countBadge.textContent = this.savedVariants.length;

            tbody.innerHTML = this.savedVariants.map((variant, idx) => {
                // Parse attributes from JSON if needed
                let attributes = variant.attributes;
                if (typeof attributes === 'string') {
                    try {
                        attributes = JSON.parse(attributes);
                    } catch (e) {
                        attributes = {};
                    }
                }

                const variantName = Object.values(attributes).join(' / ') || variant.name;

                return `
                    <tr data-variant-id="${variant.id}" data-variant-idx="${idx}">
                        <td>
                            <div class="variant-name-cell">
                                <span>${variantName}</span>
                            </div>
                        </td>
                        <td><input type="text" class="form-input variant-sku" value="${variant.sku || ''}" style="width:180px;" onchange="ProductEdit.updateSavedVariantSku(${idx}, this.value)"></td>
                        <td style="text-align:center;"><input type="checkbox" class="variant-active" ${variant.is_active == 1 ? 'checked' : ''} onchange="ProductEdit.updateSavedVariantActive(${idx}, this.checked)"></td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-sm btn-icon" onclick="ProductEdit.removeSavedVariant(${variant.id}, ${idx})" title="Variante löschen">
                                <span class="material-symbols-rounded">delete</span>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        },

        removeSavedVariant(id, idx) {
            this.savedVariants.splice(idx, 1);
            // Mark for deletion on save
            if (!this.deletedVariantIds) this.deletedVariantIds = [];
            if (id) this.deletedVariantIds.push(id);
            this.renderSavedVariants();
            this.showToast('Variante zum Löschen markiert', 'success');
        },

        updateSavedVariantSku(idx, sku) {
            if (this.savedVariants[idx]) {
                this.savedVariants[idx].sku = sku;
            }
        },

        updateSavedVariantActive(idx, isActive) {
            if (this.savedVariants[idx]) {
                this.savedVariants[idx].is_active = isActive ? 1 : 0;
            }
        },

        renderImages() {
            const gallery = document.getElementById('imageGallery');
            gallery.innerHTML = '';

            // Render existing images from DB
            this.existingImages.forEach((img, idx) => {
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                div.dataset.dbId = img.id;
                div.draggable = true;
                div.innerHTML = `
                    <img src="${img.image_url}" alt="${img.alt_text || ''}">
                    <button type="button" onclick="ProductEdit.removeExistingImage(${img.id})">&times;</button>
                    <span class="drag-handle material-symbols-rounded">drag_indicator</span>
                `;
                gallery.appendChild(div);
            });

            // Render newly uploaded images
            this.uploadedImages.forEach((img, idx) => {
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                div.dataset.tempId = img.id;
                div.draggable = true;
                div.innerHTML = `
                    <img src="${img.dataUrl}" alt="Neu">
                    <button type="button" onclick="ProductEdit.removeNewImage(${img.id})">&times;</button>
                    <span class="drag-handle material-symbols-rounded">drag_indicator</span>
                `;
                gallery.appendChild(div);
            });

            // Setup drag and drop if we have images
            if (this.existingImages.length > 0 || this.uploadedImages.length > 0) {
                this.setupImageDragDrop();
            }
        },

        removeExistingImage(id) {
            this.existingImages = this.existingImages.filter(img => img.id !== id);
            this.deletedImageIds.push(id);
            this.renderImages();
            this.showToast('Bild zum Löschen markiert', 'success');
        },

        removeNewImage(id) {
            this.uploadedImages = this.uploadedImages.filter(img => img.id !== id);
            this.renderImages();
        },

        handleImageUpload(files) {
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
                    this.renderImages();
                    this.showToast('Bild hinzugefügt', 'success');
                };
                reader.readAsDataURL(file);
            });

            // Reset input
            document.getElementById('imageInput').value = '';
        },

        setupImageDragDrop() {
            const gallery = document.getElementById('imageGallery');
            if (gallery.dataset.dragInitialized) return;
            gallery.dataset.dragInitialized = 'true';

            let draggedItem = null;

            gallery.addEventListener('dragstart', (e) => {
                const item = e.target.closest('.image-preview-item');
                if (item) {
                    draggedItem = item;
                    item.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
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
                if (!draggedItem) return;

                const afterElement = this.getDragAfterElement(gallery, e.clientX);
                if (afterElement) {
                    gallery.insertBefore(draggedItem, afterElement);
                } else {
                    gallery.appendChild(draggedItem);
                }
            });
        },

        getDragAfterElement(container, x) {
            const elements = [...container.querySelectorAll('.image-preview-item:not(.dragging)')];
            return elements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = x - box.left - box.width / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset, element: child };
                }
                return closest;
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        },

        updateImageOrder() {
            const gallery = document.getElementById('imageGallery');
            const items = gallery.querySelectorAll('.image-preview-item');

            // Rebuild existingImages array based on DOM order
            const newExisting = [];
            const newUploaded = [];

            items.forEach(item => {
                if (item.dataset.dbId) {
                    const img = this.existingImages.find(i => i.id == item.dataset.dbId);
                    if (img) newExisting.push(img);
                } else if (item.dataset.tempId) {
                    const img = this.uploadedImages.find(i => i.id == item.dataset.tempId);
                    if (img) newUploaded.push(img);
                }
            });

            this.existingImages = newExisting;
            this.uploadedImages = newUploaded;
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

        updateTypeFields() {
            const type = document.getElementById('productType').value;
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
            stockFields.querySelectorAll('input:not(#manageStock)').forEach(i => i.disabled = !manageStock);
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
                                    onchange="ProductEdit.updateDownloadFile(${file.id}, 'name', this.value)">
                            </div>
                            <div class="form-group" style="flex:1; margin-bottom:0;">
                                <label class="form-label" style="font-size:12px;">Typ</label>
                                <select class="form-input" onchange="ProductEdit.updateDownloadFile(${file.id}, 'type', this.value)">
                                    <option value="url" ${file.type === 'url' ? 'selected' : ''}>Externe URL</option>
                                    <option value="upload" ${file.type === 'upload' ? 'selected' : ''}>Datei hochladen</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" style="font-size:12px;">${file.type === 'url' ? 'Download-URL' : 'Datei'}</label>
                            ${file.type === 'url'
                    ? `<input type="url" class="form-input" value="${file.url}" placeholder="https://..."
                                    onchange="ProductEdit.updateDownloadFile(${file.id}, 'url', this.value)">`
                    : `<input type="file" class="form-input" style="padding:8px;"
                                    onchange="ProductEdit.handleDownloadFileUpload(${file.id}, this.files[0])">`
                }
                        </div>
                    </div>
                    <button type="button" onclick="ProductEdit.removeDownloadFile(${file.id})" 
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


        updateMargin() {
            // Redirect to new calculateMargin for consistent implementation
            this.calculateMargin();
        },

        setupTabs() {
            document.querySelectorAll('#editTabs .tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabName = tab.dataset.tab;

                    document.querySelectorAll('#editTabs .tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
                    document.querySelector(`[data-tab-content="${tabName}"]`).style.display = 'block';
                });
            });
        },

        setupEventListeners() {
            document.getElementById('metaTitle').addEventListener('input', (e) => {
                document.getElementById('metaTitleCount').textContent = e.target.value.length;
            });

            document.getElementById('metaDescription').addEventListener('input', (e) => {
                document.getElementById('metaDescCount').textContent = e.target.value.length;
            });

            document.getElementById('productPrice').addEventListener('input', () => this.updateMargin());
            document.getElementById('costPrice').addEventListener('input', () => this.updateMargin());

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

        async save() {
            const formData = new FormData(document.getElementById('productForm'));
            formData.append('action', 'save_product');
            formData.append('shop_id', this.shopId);
            formData.append('status', this.product.status);

            const categoryIds = Array.from(document.querySelectorAll('#categoryCheckboxes input:checked'))
                .map(cb => cb.value);
            formData.set('category_ids', JSON.stringify(categoryIds));

            formData.set('is_visible', document.getElementById('isVisible').checked ? '1' : '0');
            formData.set('is_featured', document.getElementById('isFeatured').checked ? '1' : '0');
            formData.set('is_new', document.getElementById('isNew').checked ? '1' : '0');
            formData.set('manage_stock', document.getElementById('manageStock').checked ? '1' : '0');
            formData.set('allow_backorders', document.getElementById('allowBackorders').checked ? '1' : '0');

            // Add new images
            this.uploadedImages.forEach(img => {
                if (img.file) {
                    formData.append('images[]', img.file);
                }
            });

            // Add deleted image IDs
            if (this.deletedImageIds.length > 0) {
                formData.append('delete_image_ids', JSON.stringify(this.deletedImageIds));
            }

            // Add image order for existing images
            const imageOrder = this.existingImages.map(img => img.id);
            if (imageOrder.length > 0) {
                formData.append('image_order', JSON.stringify(imageOrder));
            }

            // Add variants if any generated
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
                        id: v.id || null, // Include existing variant ID for updates
                        attributes: attributes,
                        sku: v.sku || null,
                        is_active: v.is_active !== false ? 1 : 0,
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

            // Add deleted variant IDs for removal
            if (this.deletedVariantIds && this.deletedVariantIds.length > 0) {
                formData.append('delete_variant_ids', JSON.stringify(this.deletedVariantIds));
            }

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    // Save currency prices
                    await this.saveCurrencyPrices();

                    this.showToast('Produkt gespeichert', 'success');
                    this.product.name = formData.get('name');
                    document.getElementById('pageTitle').textContent = this.product.name;
                    document.getElementById('breadcrumbName').textContent = this.product.name;

                    // Clear deleted IDs after successful save
                    this.deletedImageIds = [];
                    this.uploadedImages = [];
                    this.deletedVariantIds = [];
                    this.generatedVariants = [];

                    // Reload to get updated images and variants from server
                    await this.loadProduct();
                } else {
                    const errors = data.errors || [data.error || 'Unbekannter Fehler'];
                    this.showToast(errors.join(', '), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
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
                    // Filter only attributes that can be used for variants
                    // Filter: only variant attributes WITH options (select, multiselect, color types)
                    this.variantAttributes = data.attributes.filter(a => a.used_for_variants == 1 && a.options_count > 0);
                    this.renderVariantAttributes();
                }
            } catch (e) {
                console.error('Error loading variant attributes:', e);
            }
        },

        renderVariantAttributes() {
            const container = document.getElementById('variantAttributes');

            if (this.variantAttributes.length === 0) {
                container.innerHTML = `
                    <div class="empty-state" style="padding:20px;">
                        <span class="material-symbols-rounded">info</span>
                        <p>Keine Attribute für Varianten verfügbar. <a href="?page=catalog/attributes">Attribute verwalten</a></p>
                    </div>
                `;
                return;
            }

            container.innerHTML = this.variantAttributes.map(attr => `
                <div class="attr-card ${this.selectedAttributes.includes(attr.id) ? 'selected' : ''}" 
                     data-id="${attr.id}" onclick="ProductEdit.toggleAttribute(${attr.id})">
                    <div class="attr-card-header">
                        <input type="checkbox" ${this.selectedAttributes.includes(attr.id) ? 'checked' : ''} onclick="event.stopPropagation();">
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
                    // Store attribute with options
                    const attr = this.variantAttributes.find(a => a.id === attrId);
                    if (attr) {
                        attr.options = data.attribute.options;
                    }
                    this.selectedOptions[attrId] = [];
                }
            } catch (e) {
                console.error('Error loading attribute options:', e);
            }
        },

        renderVariantOptions() {
            const card = document.getElementById('variantOptionsCard');
            const body = document.getElementById('variantOptionsBody');

            if (this.selectedAttributes.length === 0) {
                card.style.display = 'none';
                return;
            }

            card.style.display = 'block';
            body.innerHTML = '';

            this.selectedAttributes.forEach(attrId => {
                const attr = this.variantAttributes.find(a => a.id === attrId);
                if (!attr || !attr.options) return;

                const selectedOpts = this.selectedOptions[attrId] || [];

                const group = document.createElement('div');
                group.className = 'attr-options-group';
                group.innerHTML = `
                    <h4>
                        ${attr.name}
                        <button type="button" class="btn btn-sm" onclick="ProductEdit.selectAllOptions(${attrId})">Alle auswählen</button>
                    </h4>
                    <div class="attr-options-list">
                        ${attr.options.map(opt => `
                            <label class="option-chip ${selectedOpts.includes(opt.id) ? 'selected' : ''}" 
                                   onclick="ProductEdit.toggleOption(${attrId}, ${opt.id})">
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
            if (!this.selectedOptions[attrId]) {
                this.selectedOptions[attrId] = [];
            }

            const idx = this.selectedOptions[attrId].indexOf(optId);
            if (idx > -1) {
                this.selectedOptions[attrId].splice(idx, 1);
            } else {
                this.selectedOptions[attrId].push(optId);
            }

            this.renderVariantOptions();
            this.autoGenerateVariants();
        },

        selectAllOptions(attrId) {
            const attr = this.variantAttributes.find(a => a.id === attrId);
            if (!attr || !attr.options) return;

            this.selectedOptions[attrId] = attr.options.map(o => o.id);
            this.renderVariantOptions();
            this.autoGenerateVariants();
        },

        // Auto-generate variants in real-time
        autoGenerateVariants() {
            const attrOpts = [];
            this.selectedAttributes.forEach(attrId => {
                const attr = this.variantAttributes.find(a => a.id === attrId);
                if (!attr || !attr.options) return;
                const selectedOptIds = this.selectedOptions[attrId] || [];
                const opts = attr.options.filter(o => selectedOptIds.includes(o.id));
                if (opts.length > 0) {
                    attrOpts.push({ attribute: attr, options: opts });
                }
            });

            if (attrOpts.length === 0) {
                this.generatedVariants = [];
            } else {
                this.generatedVariants = this.generateCombinations(attrOpts);
            }
            this.renderGeneratedVariants();
            this.onVariantsChanged(); // Update inventory & images views

            // Show success toast only if we have variants
            if (this.generatedVariants.length > 0) {
                this.showToast(`${this.generatedVariants.length} Varianten generiert`, 'success');
            }
        },

        updateGenerateButton() {
            // Legacy function - no longer needed but kept for compatibility
        },

        generateVariants() {
            // Get selected options for each attribute
            const attributeOptions = [];

            this.selectedAttributes.forEach(attrId => {
                const attr = this.variantAttributes.find(a => a.id === attrId);
                if (!attr || !attr.options) return;

                const selectedOptIds = this.selectedOptions[attrId] || [];
                const opts = attr.options.filter(o => selectedOptIds.includes(o.id));

                if (opts.length > 0) {
                    attributeOptions.push({ attribute: attr, options: opts });
                }
            });

            if (attributeOptions.length === 0) {
                this.showToast('Bitte wählen Sie mindestens eine Option aus', 'error');
                return;
            }

            // Generate combinations
            this.generatedVariants = this.generateCombinations(attributeOptions);
            this.renderGeneratedVariants();
            this.showToast(`${this.generatedVariants.length} Varianten generiert`, 'success');
        },

        generateCombinations(attrOptions, index = 0, current = {}) {
            if (index === attrOptions.length) {
                return [{ ...current }];
            }

            const results = [];
            const { attribute, options } = attrOptions[index];

            options.forEach(opt => {
                const newCurrent = {
                    ...current,
                    [attribute.id]: {
                        attribute,
                        option: opt
                    }
                };
                results.push(...this.generateCombinations(attrOptions, index + 1, newCurrent));
            });

            return results;
        },

        renderGeneratedVariants() {
            const card = document.getElementById('generatedVariantsCard');
            const tbody = document.getElementById('variantsBody');
            const countBadge = document.getElementById('variantCount');
            const noVariants = document.getElementById('noVariants');

            if (this.generatedVariants.length === 0) {
                card.style.display = 'block';
                noVariants.style.display = 'block';
                document.querySelector('.variants-table-wrapper').style.display = 'none';
                return;
            }

            card.style.display = 'block';
            noVariants.style.display = 'none';
            document.querySelector('.variants-table-wrapper').style.display = 'block';
            countBadge.textContent = this.generatedVariants.length;

            const baseSku = this.product?.sku || document.getElementById('productSku')?.value || 'SKU';

            tbody.innerHTML = this.generatedVariants.map((variant, idx) => {
                const optionValues = Object.values(variant).filter(v => v && v.option);
                // Use fallback display name if no option values found
                let variantName = optionValues.map(v => v.option.label || v.option.value).join(' / ');
                if (!variantName && variant._displayName) {
                    variantName = variant._displayName;
                }
                if (!variantName && variant.sku) {
                    variantName = variant.sku;
                }
                if (!variantName) {
                    variantName = `Variante ${idx + 1}`;
                }
                
                // Generate SKU with fallback
                const optionSkuPart = optionValues.length > 0 
                    ? optionValues.map(v => v.option.value).join('-')
                    : (idx + 1).toString();
                const variantSku = variant.sku || `${baseSku}-${optionSkuPart}`;
                
                const colorSwatches = optionValues
                    .filter(v => v.option?.color_hex)
                    .map(v => `<span class="color-swatch" style="background:${v.option.color_hex}"></span>`)
                    .join('');

                return `
                    <tr data-variant-idx="${idx}">
                        <td>
                            <div class="variant-name-cell">
                                ${colorSwatches ? `<div class="variant-colors">${colorSwatches}</div>` : ''}
                                <span>${variantName}</span>
                            </div>
                        </td>
                        <td><input type="text" class="form-input variant-sku" value="${variantSku}" style="width:180px;" onchange="ProductEdit.updateVariantSku(${idx}, this.value)"></td>
                        <td><input type="checkbox" class="variant-active" ${variant.is_active !== false ? 'checked' : ''} onchange="ProductEdit.updateVariantActive(${idx}, this.checked)"></td>
                        <td>
                            <button type="button" class="btn btn-sm" onclick="ProductEdit.removeVariant(${idx})">
                                <span class="material-symbols-rounded">delete</span>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        },

        updateVariantSku(idx, sku) {
            this.generatedVariants[idx].sku = sku;
        },

        removeVariant(idx) {
            this.generatedVariants.splice(idx, 1);
            this.renderGeneratedVariants();
            this.onVariantsChanged(); // Update inventory & images views
        },

        updateVariantAdjustment(idx, adjustmentValue) {
            // Store adjustment in base currency in the variant object
            const adjustment = parseFloat(adjustmentValue) || 0;
            this.generatedVariants[idx].price_adjustment = adjustment;

            // Re-render to update the displayed price
            this.renderGeneratedVariants();
        },

        updateVariantStock(idx, stockValue) {
            this.generatedVariants[idx].stock = parseInt(stockValue) || 0;
            this.onVariantsChanged();
        },

        updateVariantActive(idx, isActive) {
            this.generatedVariants[idx].is_active = isActive;
        },

        // =====================================================================
        // VARIANT-AWARE INVENTORY & IMAGES
        // =====================================================================

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
            }

            // Toggle Bilder views
            const imagesSimple = document.getElementById('imagesSimple');
            const imagesVariants = document.getElementById('imagesVariants');
            const imagesBanner = document.getElementById('imagesVariantsBanner');

            if (imagesSimple && imagesVariants) {
                imagesSimple.style.display = hasVariants ? 'none' : 'block';
                imagesVariants.style.display = hasVariants ? 'block' : 'none';
                if (imagesBanner) imagesBanner.style.display = hasVariants ? 'flex' : 'none';
            }

            if (hasVariants) {
                this.renderInventoryVariants();
                this.updateImageGroupingOptions();
                this.renderImagesVariants();
                this.renderVariantPricesAccordions();
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
            const baseCurrency = this.defaultCurrency || { symbol: '€', code: 'EUR' };

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

                // Price display: strikethrough if special price AND dates are valid, otherwise just final price
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
                        <div class="accordion-header" onclick="ProductEdit.toggleVariantPriceAccordion(${idx})" 
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
                                            onchange="ProductEdit.updateVariantFinalPrice(${idx}, this.value)">
                                    </div>
                                    <p class="form-hint" style="margin-top:4px;">Basis ${baseCurrency.symbol}${basePrice.toFixed(2)} ${adjustment !== 0 ? (adjustment > 0 ? '+' : '') + adjustment.toFixed(2) : ''}</p>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">Sonderpreis ${variantSpecialPrice > 0 ? '<span style="color:var(--danger);">✓</span>' : ''}</label>
                                    <div style="display:flex; align-items:center; gap:4px;">
                                        <span style="color:var(--text-muted);">${baseCurrency.symbol}</span>
                                        <input type="number" class="form-input" value="${variantSpecialPrice > 0 ? variantSpecialPrice.toFixed(2) : ''}" placeholder="${baseSpecialPrice > 0 ? baseSpecialPrice.toFixed(2) : '—'}" step="0.01"
                                            onchange="ProductEdit.updateVariantSpecialPrice(${idx}, this.value)">
                                    </div>
                                    <p class="form-hint" style="margin-top:4px;">Leer = ${baseSpecialPrice > 0 ? 'Basis ' + baseCurrency.symbol + baseSpecialPrice.toFixed(2) : 'kein Angebot'}</p>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label" style="font-size:12px;">Einkaufspreis (EK) ${variantCostPrice > 0 ? '<span style="color:var(--success);">✓</span>' : ''}</label>
                                    <div style="display:flex; align-items:center; gap:4px;">
                                        <span style="color:var(--text-muted);">${baseCurrency.symbol}</span>
                                        <input type="number" class="form-input" value="${variantCostPrice > 0 ? variantCostPrice.toFixed(2) : (baseCostPrice > 0 ? baseCostPrice.toFixed(2) : '')}" placeholder="—" step="0.01"
                                            onchange="ProductEdit.updateVariantCostPrice(${idx}, this.value)">
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
                                            oninput="ProductEdit.filterVariantCurrencies(${idx}, this.value)">
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
                            onchange="ProductEdit.updateVariantCurrencyOverride(${variantIdx}, '${currency.code}', this.value)">
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

            const baseSku = this.product?.sku || 'SKU';

            tbody.innerHTML = this.generatedVariants.map((variant, idx) => {
                const optionValues = Object.values(variant).filter(v => v && v.option);
                // Use fallback display name if no option values found
                let variantName = optionValues.map(v => v.option.label || v.option.value).join(' / ');
                if (!variantName && variant._displayName) {
                    variantName = variant._displayName;
                }
                if (!variantName && variant.sku) {
                    variantName = variant.sku;
                }
                if (!variantName) {
                    variantName = `Variante ${idx + 1}`;
                }

                const colorSwatches = optionValues
                    .filter(v => v.option?.color_hex)
                    .map(v => `<span class="color-swatch" style="background:${v.option.color_hex}"></span>`)
                    .join('');

                return `
                    <tr data-inventory-idx="${idx}">
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                ${colorSwatches ? `<div style="display:flex; gap:4px;">${colorSwatches}</div>` : ''}
                                <span>${variantName}</span>
                            </div>
                        </td>
                        <td>
                            <input type="number" class="form-input" value="${variant.stock || 0}" min="0" style="width:80px;"
                                onchange="ProductEdit.updateVariantInventory(${idx}, 'stock', this.value)">
                        </td>
                        <td>
                            <input type="number" class="form-input" value="${variant.weight || ''}" step="0.1" min="0" style="width:80px;"
                                onchange="ProductEdit.updateVariantInventory(${idx}, 'weight', this.value)">
                        </td>
                        <td>
                            <input type="number" class="form-input" value="${variant.length || ''}" min="0" style="width:60px;"
                                onchange="ProductEdit.updateVariantInventory(${idx}, 'length', this.value)">
                        </td>
                        <td>
                            <input type="number" class="form-input" value="${variant.width || ''}" min="0" style="width:60px;"
                                onchange="ProductEdit.updateVariantInventory(${idx}, 'width', this.value)">
                        </td>
                        <td>
                            <input type="number" class="form-input" value="${variant.height || ''}" min="0" style="width:60px;"
                                onchange="ProductEdit.updateVariantInventory(${idx}, 'height', this.value)">
                        </td>
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
            // Open the inventory bulk modal instead of using prompt
            const modal = document.getElementById('inventoryBulkModal');
            const countEl = document.getElementById('inventoryModalCount');

            if (countEl) countEl.textContent = this.generatedVariants.length;

            // Clear previous values
            document.getElementById('bulkStock').value = '';
            document.getElementById('bulkWeight').value = '';
            document.getElementById('bulkLength').value = '';
            document.getElementById('bulkWidth').value = '';
            document.getElementById('bulkHeight').value = '';

            modal.style.display = 'flex';
            document.getElementById('bulkStock').focus();

            // Add escape key listener
            this._inventoryModalEscapeHandler = (e) => {
                if (e.key === 'Escape') this.closeInventoryModal();
            };
            document.addEventListener('keydown', this._inventoryModalEscapeHandler);
        },

        closeInventoryModal() {
            document.getElementById('inventoryBulkModal').style.display = 'none';
            if (this._inventoryModalEscapeHandler) {
                document.removeEventListener('keydown', this._inventoryModalEscapeHandler);
            }
        },

        applyBulkInventory() {
            const stock = document.getElementById('bulkStock').value;
            const weight = document.getElementById('bulkWeight').value;
            const length = document.getElementById('bulkLength').value;
            const width = document.getElementById('bulkWidth').value;
            const height = document.getElementById('bulkHeight').value;

            let changedFields = 0;

            this.generatedVariants.forEach((v, idx) => {
                if (stock !== '') {
                    this.generatedVariants[idx].stock = parseInt(stock) || 0;
                    changedFields++;
                }
                if (weight !== '') {
                    this.generatedVariants[idx].weight = parseFloat(weight) || 0;
                    changedFields++;
                }
                if (length !== '') {
                    this.generatedVariants[idx].length = parseFloat(length) || null;
                    changedFields++;
                }
                if (width !== '') {
                    this.generatedVariants[idx].width = parseFloat(width) || null;
                    changedFields++;
                }
                if (height !== '') {
                    this.generatedVariants[idx].height = parseFloat(height) || null;
                    changedFields++;
                }
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
                        <div class="accordion-header" onclick="ProductEdit.toggleImageAccordion(${gIdx})" 
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
                                    onchange="ProductEdit.handleGroupImageUpload(${gIdx}, '${group.key}', this.files)">
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

        renderGroupImageGallery(group, gIdx) {
            const images = group.variants[0]?.images || [];
            if (images.length === 0) {
                return '<p style="color:var(--text-muted); grid-column:1/-1; text-align:center; padding:20px;">Noch keine Bilder</p>';
            }

            return images.map((img, imgIdx) => `
                <div class="variant-image-item" draggable="true" 
                    data-group-idx="${gIdx}" data-img-idx="${imgIdx}"
                    ondragstart="ProductEdit.handleVariantImageDragStart(event, ${gIdx}, ${imgIdx})"
                    ondragend="ProductEdit.handleVariantImageDragEnd(event)"
                    ondragover="ProductEdit.handleVariantImageDragOver(event)"
                    ondrop="ProductEdit.handleVariantImageDrop(event, ${gIdx}, ${imgIdx})"
                    style="position:relative; aspect-ratio:1; border-radius:8px; overflow:hidden; border:1px solid var(--border-color); cursor:grab;">
                    <img src="${img.url || img}" alt="" style="width:100%; height:100%; object-fit:cover; pointer-events:none;">
                    <button type="button" onclick="event.stopPropagation(); ProductEdit.removeGroupImage(${gIdx}, ${imgIdx})" 
                        style="position:absolute; top:4px; right:4px; width:24px; height:24px; border-radius:50%; background:var(--danger); color:white; border:none; cursor:pointer; z-index:10;">
                        <span class="material-symbols-rounded" style="font-size:16px;">close</span>
                    </button>
                    <span class="drag-handle material-symbols-rounded" style="position:absolute; bottom:4px; right:4px; color:white; text-shadow:0 1px 3px rgba(0,0,0,0.5); font-size:16px;">drag_indicator</span>
                    ${imgIdx === 0 ? '<span style="position:absolute; bottom:4px; left:4px; background:var(--primary); color:white; font-size:10px; padding:2px 6px; border-radius:4px;">Haupt</span>' : ''}
                </div>
            `).join('');
        },

        handleGroupImageUpload(gIdx, groupKey, files) {
            const groupBy = document.getElementById('imageGroupBy')?.value || 'all';
            let variantIndices = [];

            if (groupBy === 'all') {
                variantIndices = [gIdx];
            } else {
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

            const imageCount = this.generatedVariants[variantIndices[0]]?.images?.length || 0;
            const isValid = imageCount >= 1;

            badge.textContent = `${imageCount} Bild${imageCount !== 1 ? 'er' : ''}`;
            badge.className = `badge ${isValid ? 'badge-success' : 'badge-warning'}`;
        },

        renderVariantImageGallery(idx) {
            const variant = this.generatedVariants[idx];
            const images = variant.images || [];

            if (images.length === 0) {
                return '<p style="color:var(--text-muted); grid-column:1/-1; text-align:center; padding:20px;">Noch keine Bilder</p>';
            }

            return images.map((img, imgIdx) => `
                <div class="variant-image-item" style="position:relative; aspect-ratio:1; border-radius:8px; overflow:hidden; border:1px solid var(--border-color);">
                    <img src="${img.url || img}" alt="" style="width:100%; height:100%; object-fit:cover;">
                    <button type="button" onclick="ProductEdit.removeVariantImage(${idx}, ${imgIdx})" 
                        style="position:absolute; top:4px; right:4px; width:24px; height:24px; border-radius:50%; background:var(--danger); color:white; border:none; cursor:pointer;">
                        <span class="material-symbols-rounded" style="font-size:16px;">close</span>
                    </button>
                    ${imgIdx === 0 ? '<span style="position:absolute; bottom:4px; left:4px; background:var(--primary); color:white; font-size:10px; padding:2px 6px; border-radius:4px;">Haupt</span>' : ''}
                </div>
            `).join('');
        },

        handleVariantImageUpload(idx, files) {
            if (!this.generatedVariants[idx].images) {
                this.generatedVariants[idx].images = [];
            }

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.generatedVariants[idx].images.push({
                        url: e.target.result,
                        file: file,
                        isNew: true
                    });
                    const gallery = document.getElementById(`variantGallery${idx}`);
                    if (gallery) {
                        gallery.innerHTML = this.renderVariantImageGallery(idx);
                    }
                    this.updateImageValidationStatus();
                };
                reader.readAsDataURL(file);
            });
        },

        removeVariantImage(variantIdx, imageIdx) {
            this.generatedVariants[variantIdx].images.splice(imageIdx, 1);
            const gallery = document.getElementById(`variantGallery${variantIdx}`);
            if (gallery) {
                gallery.innerHTML = this.renderVariantImageGallery(variantIdx);
            }
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
            document.querySelectorAll('.variant-image-item').forEach(el => {
                el.style.transform = '';
                el.style.border = '';
            });
        },

        handleVariantImageDragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            const target = event.target.closest('.variant-image-item');
            if (target && !target.classList.contains('dragging')) {
                target.style.border = '2px solid var(--primary)';
            }
        },

        handleVariantImageDrop(event, targetGIdx, targetImgIdx) {
            event.preventDefault();
            const target = event.target.closest('.variant-image-item');
            if (target) target.style.border = '';

            if (!this._variantDragData) return;

            const { gIdx: sourceGIdx, imgIdx: sourceImgIdx } = this._variantDragData;

            if (sourceGIdx !== targetGIdx) {
                this.showToast('Bilder können nur innerhalb derselben Gruppe verschoben werden', 'error');
                return;
            }

            if (sourceImgIdx === targetImgIdx) return;

            const groupBy = document.getElementById('imageGroupBy')?.value || 'all';
            let variantIndices = this.getVariantIndicesForGroup(sourceGIdx, groupBy);

            variantIndices.forEach(idx => {
                const variant = this.generatedVariants[idx];
                if (variant && variant.images && variant.images.length > 1) {
                    const images = variant.images;
                    const [movedImage] = images.splice(sourceImgIdx, 1);
                    images.splice(targetImgIdx, 0, movedImage);
                }
            });

            const gallery = document.getElementById(`groupGallery${sourceGIdx}`);
            if (gallery) {
                const group = { variants: variantIndices.map(idx => this.generatedVariants[idx]) };
                gallery.innerHTML = this.renderGroupImageGallery(group, sourceGIdx);
            }

            this.showToast('Bildreihenfolge aktualisiert', 'success');
        },

        getVariantIndicesForGroup(gIdx, groupBy) {
            if (groupBy === 'all') return [gIdx];

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
                if (value.gIdx === gIdx) return value.indices;
            }

            return [gIdx];
        },

        // =====================================================================
        // MULTI-CURRENCY PRICING
        // =====================================================================

        async loadShopCurrency() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_shop_currency&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    this.defaultCurrency = data.default_currency;
                    this.shopCurrencies = data.currencies || [];
                    this.variantCurrency = this.defaultCurrency; // Set variant currency to default
                    this.populateCurrencyDropdown();
                    this.populateVariantCurrencyDropdown();
                }
            } catch (e) {
                console.error('Currency load error:', e);
            }
        },

        populateVariantCurrencyDropdown() {
            const select = document.getElementById('variantCurrencySelect');
            if (!select || !this.shopCurrencies.length) return;

            select.innerHTML = this.shopCurrencies.map(c =>
                `<option value="${c.code}" ${c.code === this.defaultCurrency?.code ? 'selected' : ''}>
                    ${c.symbol} ${c.code}
                </option>`
            ).join('');

            // Update currency symbol in table header
            this.updateVariantCurrencySymbol();
        },

        updateVariantCurrency(code) {
            const currency = this.shopCurrencies.find(c => c.code === code);
            if (currency) {
                this.variantCurrency = currency;
                this.updateVariantCurrencySymbol();
                this.renderGeneratedVariants();
            }
        },

        updateVariantCurrencySymbol() {
            const symbolEl = document.getElementById('variantCurrencySymbol');
            if (symbolEl && this.variantCurrency) {
                symbolEl.textContent = this.variantCurrency.symbol;
            }
        },

        populateCurrencyDropdown() {
            const select = document.getElementById('baseCurrencySelect');
            if (!select || !this.shopCurrencies.length) return;

            select.innerHTML = this.shopCurrencies.map(c =>
                `<option value="${c.code}" ${c.code === this.defaultCurrency?.code ? 'selected' : ''}>
                    ${c.symbol} ${c.code}
                </option>`
            ).join('');

            this.updateCurrencyLabels();
        },

        updateCurrencyLabels() {
            const code = document.getElementById('baseCurrencySelect')?.value || 'USD';
            const currency = this.shopCurrencies.find(c => c.code === code) || { symbol: '$' };

            document.getElementById('priceSymbol').textContent = currency.symbol;
            document.getElementById('specialSymbol').textContent = currency.symbol;
            document.getElementById('costSymbol').textContent = currency.symbol;

            this.calculateCurrencyPreview();
        },

        toggleRounding() {
            const enabled = document.getElementById('enableRounding').checked;
            document.getElementById('roundingStep').disabled = !enabled;
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
            const basePrice = parseFloat(document.getElementById('productPrice').value) || 0;
            const specialPrice = parseFloat(document.getElementById('specialPrice').value) || 0;
            const baseCurrency = document.getElementById('baseCurrencySelect')?.value || 'USD';
            const roundingStep = parseFloat(document.getElementById('roundingStep')?.value) || 0;

            if (basePrice <= 0) {
                const loadingEl = document.getElementById('currencyPricesLoading');
                const tableEl = document.querySelector('.currency-table-scroll');
                if (loadingEl) loadingEl.style.display = 'none';
                if (tableEl) tableEl.style.display = 'none';
                return;
            }

            try {
                const url = `${this.apiBase}?action=calculate_prices&shop_id=${this.shopId}&base_price=${basePrice}&special_price=${specialPrice}&base_currency=${baseCurrency}&rounding_step=${roundingStep}&product_id=${this.productId}`;
                const res = await fetch(url);
                const data = await res.json();

                if (data.success) {
                    this.renderCurrencyPrices(data.prices);
                }
            } catch (e) {
                console.error('Price calculation error:', e);
            }
        },

        renderCurrencyPrices(prices) {
            const tbody = document.getElementById('currencyPricesBody');
            if (!tbody) return; // Exit if element doesn't exist

            const baseCurrency = document.getElementById('baseCurrencySelect')?.value || 'USD';
            const loadingEl = document.getElementById('currencyPricesLoading');
            const tableEl = document.querySelector('.currency-table-scroll');

            if (loadingEl) loadingEl.style.display = 'none';
            if (tableEl) tableEl.style.display = 'block'; // Always show the table container

            // Store all prices for later
            this.allPrices = prices;
            const priceArray = Object.values(prices);

            // Always show all currencies (except base currency)
            let visiblePrices = priceArray.filter(p => p.code !== baseCurrency);

            // Update count display
            const countEl = document.getElementById('currencyCount');
            if (countEl) {
                countEl.textContent = visiblePrices.length;
            }

            if (visiblePrices.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="empty-state" style="padding:20px;">
                    Keine Währungen konfiguriert.
                </td></tr>`;
            } else {
                tbody.innerHTML = visiblePrices.map(p => this.renderCurrencyRow(p)).join('');
            }
        },

        renderCurrencyRow(p) {
            const hasOverride = p.has_override || (this.currencyOverrides[p.code]?.price || this.currencyOverrides[p.code]?.special_price);
            const overridePrice = this.currencyOverrides[p.code]?.price ?? p.override_price ?? '';
            const overrideSpecial = this.currencyOverrides[p.code]?.special_price ?? p.override_special ?? '';

            return `
                <tr class="${hasOverride ? 'has-override' : ''}" data-currency="${p.code}">
                    <td>
                        <div class="currency-name">
                            <span class="currency-code">${p.code}</span>
                            <span class="currency-symbol">${p.symbol}</span>
                        </div>
                        <small style="color:var(--text-muted)">${p.name}</small>
                    </td>
                    <td class="calculated-price">
                        <strong>${p.symbol} ${p.calculated_price.toFixed(2)}</strong>
                        ${p.calculated_special > 0 ? `<br><small>Sonder: ${p.symbol} ${p.calculated_special.toFixed(2)}</small>` : ''}
                    </td>
                    <td>
                        <div class="override-group">
                            <input type="number" class="form-input override-input" 
                                value="${overridePrice}" 
                                placeholder="Auto"
                                step="0.01" min="0"
                                oninput="ProductEdit.updateOverride('${p.code}', 'price', this.value)">
                        </div>
                    </td>
                    <td>
                        ${hasOverride ? `
                            <button type="button" class="remove-btn" onclick="ProductEdit.clearOverride('${p.code}')" title="Zurücksetzen">
                                <span class="material-symbols-rounded">refresh</span>
                            </button>` : ''}
                    </td>
                </tr>
            `;
        },

        updateOverride(code, field, value) {
            if (!this.currencyOverrides[code]) {
                this.currencyOverrides[code] = {};
            }
            this.currencyOverrides[code][field] = value !== '' ? parseFloat(value) : null;

            // Update row styling
            const row = document.querySelector(`tr[data-currency="${code}"]`);
            if (row) {
                const hasAnyOverride = this.currencyOverrides[code].price || this.currencyOverrides[code].special_price;
                row.classList.toggle('has-override', !!hasAnyOverride);
            }
        },

        clearOverride(code) {
            delete this.currencyOverrides[code];
            // Re-render that row
            if (this.allPrices && this.allPrices[code]) {
                const row = document.querySelector(`tr[data-currency="${code}"]`);
                if (row) {
                    row.outerHTML = this.renderCurrencyRow(this.allPrices[code]);
                }
            }
        },

        removeCurrencyRow(code) {
            delete this.currencyOverrides[code];
            const row = document.querySelector(`tr[data-currency="${code}"]`);
            if (row) row.remove();
        },

        addCurrencyOverride() {
            // Show modal or dropdown to select currency
            const available = this.shopCurrencies.filter(c =>
                !document.querySelector(`tr[data-currency="${c.code}"]`)
            );

            if (available.length === 0) {
                this.showToast('Alle Währungen bereits hinzugefügt', 'info');
                return;
            }

            const code = prompt('Währungscode eingeben (z.B. EUR, CHF, GBP):\n\n' +
                'Verfügbar: ' + available.slice(0, 20).map(c => c.code).join(', '));

            if (!code) return;

            const currency = this.shopCurrencies.find(c => c.code === code.toUpperCase());
            if (!currency) {
                this.showToast('Währung nicht gefunden', 'error');
                return;
            }

            // Add empty row
            const tbody = document.getElementById('currencyPricesBody');
            const basePrice = parseFloat(document.getElementById('productPrice').value) || 0;
            const specialPrice = parseFloat(document.getElementById('specialPrice').value) || 0;
            const baseCurrency = document.getElementById('baseCurrencySelect')?.value || 'USD';

            // Quick calculation
            const baseRate = this.shopCurrencies.find(c => c.code === baseCurrency)?.exchange_rate || 1;
            const targetRate = currency.exchange_rate || 1;
            const calculated = basePrice * (targetRate / baseRate);
            const calculatedSpecial = specialPrice * (targetRate / baseRate);

            tbody.insertAdjacentHTML('beforeend', this.renderCurrencyRow({
                code: currency.code,
                name: currency.name,
                symbol: currency.symbol,
                calculated_price: calculated,
                calculated_special: calculatedSpecial,
                override_price: null,
                override_special: null,
                has_override: false
            }));
        },

        toggleShowAll() {
            this.calculateCurrencyPreview();
        },

        filterCurrencies() {
            const searchTerm = document.getElementById('currencySearch').value.toLowerCase().trim();
            const rows = document.querySelectorAll('#currencyPricesBody tr');
            const tableEl = document.querySelector('.currency-table-scroll');
            let visibleCount = 0;

            // If no search term, show all rows
            if (!searchTerm) {
                rows.forEach(row => {
                    row.style.display = '';
                    visibleCount++;
                });
            } else {
                rows.forEach(row => {
                    const currencyCode = row.querySelector('.currency-code')?.textContent.toLowerCase() || '';
                    const currencySymbol = row.querySelector('.currency-symbol')?.textContent.toLowerCase() || '';
                    // Also search in the full currency name (in the <small> tag)
                    const currencyName = row.querySelector('small')?.textContent.toLowerCase() || '';

                    if (currencyCode.includes(searchTerm) || currencySymbol.includes(searchTerm) || currencyName.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Always show the table container when we have any currencies
            if (tableEl && rows.length > 0) {
                tableEl.style.display = 'block';
            }

            // Update count display
            const countEl = document.getElementById('currencyCount');
            if (countEl) {
                countEl.textContent = visibleCount;
            }
        },

        async loadCurrencyOverrides() {
            try {
                const res = await fetch(`${this.apiBase}?action=calculate_prices&shop_id=${this.shopId}&base_price=0&base_currency=USD&product_id=${this.productId}`);
                const data = await res.json();

                if (data.success && data.prices) {
                    // Extract existing overrides from API response
                    Object.entries(data.prices).forEach(([code, priceData]) => {
                        if (priceData.has_override) {
                            this.currencyOverrides[code] = {
                                price: priceData.override_price ? parseFloat(priceData.override_price) : null,
                                special_price: priceData.override_special ? parseFloat(priceData.override_special) : null
                            };
                        }
                    });
                }

                // Now render the currency table with loaded overrides
                this.calculateCurrencyPreview();
            } catch (e) {
                console.error('Error loading currency overrides:', e);
                // Still show currency preview even if loading overrides failed
                this.calculateCurrencyPreview();
            }
        },

        async saveCurrencyPrices() {
            const baseCurrency = document.getElementById('baseCurrencySelect')?.value || 'USD';
            const roundingStep = document.getElementById('enableRounding')?.checked
                ? document.getElementById('roundingStep')?.value
                : null;

            const formData = new FormData();
            formData.append('action', 'save_currency_prices');
            formData.append('shop_id', this.shopId);
            formData.append('product_id', this.productId);
            formData.append('base_currency', baseCurrency);
            formData.append('rounding_step', roundingStep || '');
            formData.append('prices', JSON.stringify(this.currencyOverrides));

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();
                return data.success;
            } catch (e) {
                console.error('Currency save error:', e);
                return false;
            }
        },

        // ==================== MARGIN CALCULATION ====================

        calculateMargin() {
            const marginDisplay = document.getElementById('marginDisplay');
            const marginAmountEl = document.getElementById('marginAmount');
            const marginPercentEl = document.getElementById('marginPercent');

            if (!marginDisplay || !marginAmountEl || !marginPercentEl) return;

            const regularPrice = parseFloat(document.getElementById('productPrice')?.value) || 0;
            const specialPrice = parseFloat(document.getElementById('specialPrice')?.value) || 0;
            const costPrice = parseFloat(document.getElementById('costPrice')?.value) || 0;

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

    document.addEventListener('DOMContentLoaded', () => ProductEdit.init());
</script>