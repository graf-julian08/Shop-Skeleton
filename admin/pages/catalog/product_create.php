<?php /** Katalog - Neues Produkt erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/products">Produkte</a> <span>›</span> <span>Neues Produkt</span></nav>
        <h1>Neues Produkt erstellen</h1>
        <p class="page-subtitle">Fügen Sie ein neues Produkt zu Ihrem Katalog hinzu</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/products" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Produkt speichern</button>
    </div>
</div>

<div class="product-form-container">
    <div class="tabs">
        <button class="tab active" data-tab="general">Allgemein</button>
        <button class="tab" data-tab="pricing">Preise</button>
        <button class="tab" data-tab="inventory">Inventar</button>
        <button class="tab" data-tab="images">Bilder</button>
        <button class="tab" data-tab="seo">SEO</button>
    </div>

    <!-- Tab: Allgemein -->
    <div class="tab-content active" data-tab-content="general" style="display:block;">
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header"><h3>Grunddaten</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Produktname <span style="color:var(--error)">*</span></label>
                        <input type="text" class="form-input" placeholder="z.B. Premium Lederjacke">
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL-Slug</label>
                        <input type="text" class="form-input" placeholder="z.B. premium-lederjacke">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kurzbeschreibung</label>
                        <textarea class="form-textarea" rows="2" placeholder="Kurze Beschreibung für Listen..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Beschreibung</label>
                        <textarea class="form-textarea" rows="5" placeholder="Ausführliche Produktbeschreibung..."></textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Produkttyp & Kategorie</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Produkttyp</label>
                        <select class="form-select">
                            <option selected>Physisches Produkt</option>
                            <option>Digitales Produkt</option>
                            <option>Abonnement</option>
                            <option>Bundle</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategorie <span style="color:var(--error)">*</span></label>
                        <select class="form-select">
                            <option>Kategorie wählen...</option>
                            <option>Kleidung</option>
                            <option>Accessoires</option>
                            <option>Schuhe</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option>Entwurf</option>
                            <option>Aktiv</option>
                            <option>Archiviert</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sichtbarkeit</label>
                        <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
                        <small style="color:var(--text-muted);">Im Shop anzeigen</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Preise -->
    <div class="tab-content" data-tab-content="pricing" style="display:none;">
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header"><h3>Preisinformationen</h3></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Regulärer Preis (€) <span style="color:var(--error)">*</span></label>
                            <input type="number" class="form-input" placeholder="0.00" step="0.01">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sonderpreis (€)</label>
                            <input type="number" class="form-input" placeholder="0.00" step="0.01">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Sonderpreis von</label>
                            <input type="date" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sonderpreis bis</label>
                            <input type="date" class="form-input">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Kosten & Steuern</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Einkaufspreis (€)</label>
                        <input type="number" class="form-input" placeholder="0.00" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Steuerklasse</label>
                        <select class="form-select">
                            <option selected>Standard (19%)</option>
                            <option>Ermäßigt (7%)</option>
                            <option>Steuerfrei</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Inventar -->
    <div class="tab-content" data-tab-content="inventory" style="display:none;">
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header"><h3>Lagerbestand</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">SKU (Artikelnummer)</label>
                        <input type="text" class="form-input" placeholder="z.B. LJ-001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bestand verfolgen</label>
                        <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lagermenge</label>
                        <input type="number" class="form-input" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mindestbestand (Warnung)</label>
                        <input type="number" class="form-input" placeholder="5">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Versand</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Gewicht (kg)</label>
                        <input type="number" class="form-input" placeholder="0.0" step="0.1">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Länge (cm)</label>
                            <input type="number" class="form-input" placeholder="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Breite (cm)</label>
                            <input type="number" class="form-input" placeholder="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Höhe (cm)</label>
                            <input type="number" class="form-input" placeholder="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Bilder -->
    <div class="tab-content" data-tab-content="images" style="display:none;">
        <div class="card">
            <div class="card-header"><h3>Produktbilder</h3></div>
            <div class="card-body">
                <div class="image-upload-zone">
                    <div class="upload-placeholder">
                        <span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);">add_photo_alternate</span>
                        <p>Bilder hier ablegen oder klicken zum Hochladen</p>
                        <small style="color:var(--text-muted);">PNG, JPG oder WEBP · Max. 5MB pro Bild</small>
                        <button class="btn btn-primary" style="margin-top:16px;"><span class="material-symbols-rounded">upload</span> Bilder auswählen</button>
                    </div>
                </div>
                <div class="form-group" style="margin-top:24px;">
                    <label class="form-label">Galerie-Reihenfolge</label>
                    <div class="image-gallery-preview" style="display:flex;gap:12px;flex-wrap:wrap;">
                        <div class="image-preview-slot" style="width:100px;height:100px;border:2px dashed var(--border);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;">
                            <span class="material-symbols-rounded" style="color:var(--text-muted);">image</span>
                        </div>
                        <div class="image-preview-slot" style="width:100px;height:100px;border:2px dashed var(--border);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;">
                            <span class="material-symbols-rounded" style="color:var(--text-muted);">image</span>
                        </div>
                        <div class="image-preview-slot" style="width:100px;height:100px;border:2px dashed var(--border);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;">
                            <span class="material-symbols-rounded" style="color:var(--text-muted);">image</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: SEO -->
    <div class="tab-content" data-tab-content="seo" style="display:none;">
        <div class="card">
            <div class="card-header"><h3>Suchmaschinenoptimierung</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Meta-Titel</label>
                    <input type="text" class="form-input" placeholder="Titel für Suchergebnisse (max. 60 Zeichen)">
                    <small style="color:var(--text-muted);">0/60 Zeichen</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Meta-Beschreibung</label>
                    <textarea class="form-textarea" rows="3" placeholder="Beschreibung für Suchergebnisse (max. 160 Zeichen)"></textarea>
                    <small style="color:var(--text-muted);">0/160 Zeichen</small>
                </div>
                <div class="form-group">
                    <label class="form-label">URL-Handle</label>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="color:var(--text-muted);">meinshop.de/produkte/</span>
                        <input type="text" class="form-input" placeholder="produkt-name" style="flex:1;">
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:24px;">
            <div class="card-header"><h3>Suchvorschau</h3></div>
            <div class="card-body">
                <div class="seo-preview" style="padding:16px;background:var(--bg-tertiary);border-radius:var(--radius-md);">
                    <div style="color:var(--accent);font-size:12px;">meinshop.de › produkte › produkt-name</div>
                    <div style="color:var(--accent);font-size:18px;margin:4px 0;">Produktname - Mein Online Shop</div>
                    <div style="color:var(--text-muted);font-size:13px;">Meta-Beschreibung wird hier angezeigt...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:24px;">
    <div class="card-footer" style="display:flex;justify-content:flex-end;gap:12px;">
        <a href="?page=catalog/products" class="btn">Abbrechen</a>
        <button class="btn"><span class="material-symbols-rounded">visibility</span> Vorschau</button>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Produkt speichern</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.product-form-container { margin-top:24px; }
.image-upload-zone { border:2px dashed var(--border); border-radius:var(--radius-md); padding:48px; text-align:center; }
.form-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; }
</style>
