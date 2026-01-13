<?php /** Katalog - Medien */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Medienbibliothek</h1>
        <p class="page-subtitle">Bilder und Videos verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary"><span class="material-symbols-rounded">cloud_upload</span> Hochladen</button>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-label">Gesamt Dateien</div>
        <div class="stat-card-value">1.284</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Bilder</div>
        <div class="stat-card-value">1.156</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Videos</div>
        <div class="stat-card-value">45</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Speicher verwendet</div>
        <div class="stat-card-value">2,4 GB</div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="upload-zone">
            <span class="material-symbols-rounded">cloud_upload</span>
            <p>Dateien hier ablegen oder klicken zum Hochladen</p>
            <p style="font-size:12px;">PNG, JPG, GIF, MP4, WebM (max. 50MB)</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Alle Medien</h3>
        <div style="display:flex;gap:8px;">
            <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Suchen..."></div>
            <select class="filter-select"><option>Alle Typen</option><option>Bilder</option><option>Videos</option></select>
            <select class="filter-select"><option>Neueste zuerst</option><option>Älteste zuerst</option><option>Name A-Z</option></select>
        </div>
    </div>
    <div class="card-body">
        <div class="media-grid">
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--accent);">play_circle</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--accent);">play_circle</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
            <div class="media-item"><span class="material-symbols-rounded" style="font-size:32px;color:var(--text-muted);">image</span></div>
        </div>
        <div style="display:flex;justify-content:center;padding-top:20px;">
            <button class="btn">Mehr laden</button>
        </div>
    </div>
</div>
