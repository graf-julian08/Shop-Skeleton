<?php /** Developer - Themes */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Themes</h1>
        <p class="page-subtitle">Frontend-Themes verwalten</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">upload</span> Theme hochladen</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Installierte Themes</h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
            <div style="border:2px solid var(--accent);border-radius:var(--radius-md);overflow:hidden;">
                <div style="height:150px;background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;"><span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);">palette</span></div>
                <div style="padding:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <strong>Modern Luxury</strong>
                        <span class="badge badge-success">Aktiv</span>
                    </div>
                    <p style="color:var(--text-muted);font-size:13px;margin:8px 0;">v2.1.0 • Premium Theme</p>
                    <div style="display:flex;gap:8px;margin-top:12px;">
                        <button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button>
                        <button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button>
                    </div>
                </div>
            </div>
            <div style="border:1px solid var(--border-color);border-radius:var(--radius-md);overflow:hidden;">
                <div style="height:150px;background:var(--bg-tertiary);display:flex;align-items:center;justify-content:center;"><span class="material-symbols-rounded" style="font-size:48px;color:var(--text-muted);">palette</span></div>
                <div style="padding:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <strong>Classic Minimal</strong>
                        <span class="badge badge-default">Inaktiv</span>
                    </div>
                    <p style="color:var(--text-muted);font-size:13px;margin:8px 0;">v1.8.3 • Standard Theme</p>
                    <div style="display:flex;gap:8px;margin-top:12px;">
                        <button class="btn btn-sm btn-primary">Aktivieren</button>
                        <button class="btn btn-sm"><span class="material-symbols-rounded">visibility</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Theme-Editor</h3></div>
    <div class="card-body">
        <p style="color:var(--text-secondary);margin-bottom:16px;">Passen Sie das aktive Theme an, ohne den Code zu verändern.</p>
        <div style="display:flex;gap:12px;">
            <button class="btn btn-primary"><span class="material-symbols-rounded">brush</span> Theme-Editor öffnen</button>
            <button class="btn"><span class="material-symbols-rounded">code</span> Code bearbeiten</button>
        </div>
    </div>
</div>
