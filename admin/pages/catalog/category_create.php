<?php /** Katalog - Neue Kategorie erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=catalog/categories">Kategorien</a> <span>›</span> <span>Neue Kategorie</span></nav>
        <h1>Neue Kategorie erstellen</h1>
        <p class="page-subtitle">Erstellen Sie eine neue Produktkategorie</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=catalog/categories" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Kategorie erstellen</button>
    </div>
</div>

<div class="category-form-container">
    <div class="tabs">
        <button class="tab active" data-tab="general">Allgemein</button>
        <button class="tab" data-tab="display">Darstellung</button>
        <button class="tab" data-tab="seo">SEO</button>
    </div>

    <!-- Tab: Allgemein -->
    <div class="tab-content active" data-tab-content="general" style="display:block;">
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header"><h3>Grunddaten</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Kategoriename <span style="color:var(--error)">*</span></label>
                        <input type="text" class="form-input" placeholder="z.B. Sommerkollekion">
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL-Slug</label>
                        <input type="text" class="form-input" placeholder="z.B. sommerkollektion">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Übergeordnete Kategorie</label>
                        <select class="form-select">
                            <option selected>Keine (Hauptkategorie)</option>
                            <option>Kleidung</option>
                            <option>— Herren</option>
                            <option>— Damen</option>
                            <option>Accessoires</option>
                            <option>Schuhe</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Beschreibung</label>
                        <textarea class="form-textarea" rows="4" placeholder="Beschreibung der Kategorie..."></textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Status & Sichtbarkeit</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option>Entwurf</option>
                            <option selected>Aktiv</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">In Navigation anzeigen</label>
                        <div class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sortierung</label>
                        <input type="number" class="form-input" value="10">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Darstellung -->
    <div class="tab-content" data-tab-content="display" style="display:none;">
        <div class="card">
            <div class="card-header"><h3>Kategoriebild</h3></div>
            <div class="card-body">
                <div class="image-upload-zone">
                    <div class="upload-placeholder">
                        <span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);">add_photo_alternate</span>
                        <p>Bild hier ablegen oder klicken zum Hochladen</p>
                        <button class="btn btn-primary" style="margin-top:16px;"><span class="material-symbols-rounded">upload</span> Bild auswählen</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:24px;">
            <div class="card-header"><h3>Anzeigeoptionen</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Produktsortierung</label>
                    <select class="form-select">
                        <option>Standard</option>
                        <option>Neueste zuerst</option>
                        <option>Preis aufsteigend</option>
                        <option>Preis absteigend</option>
                        <option>Meistverkauft</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Produkte pro Seite</label>
                    <select class="form-select">
                        <option>12</option>
                        <option selected>24</option>
                        <option>48</option>
                        <option>96</option>
                    </select>
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
                    <input type="text" class="form-input" placeholder="Titel für Suchergebnisse">
                </div>
                <div class="form-group">
                    <label class="form-label">Meta-Beschreibung</label>
                    <textarea class="form-textarea" rows="3" placeholder="Beschreibung für Suchergebnisse"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Meta-Keywords</label>
                    <input type="text" class="form-input" placeholder="Kommagetrennte Keywords">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:24px;">
    <div class="card-footer" style="display:flex;justify-content:flex-end;gap:12px;">
        <a href="?page=catalog/categories" class="btn">Abbrechen</a>
        <button class="btn btn-primary"><span class="material-symbols-rounded">save</span> Kategorie erstellen</button>
    </div>
</div>

<style>
.breadcrumb { font-size:13px; color:var(--text-muted); margin-bottom:8px; }
.breadcrumb a { color:var(--accent); }
.category-form-container { margin-top:24px; }
.image-upload-zone { border:2px dashed var(--border); border-radius:var(--radius-md); padding:48px; text-align:center; }
</style>
