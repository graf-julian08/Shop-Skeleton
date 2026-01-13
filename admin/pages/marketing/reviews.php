<?php /** Marketing - Reviews */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Bewertungen</h1>
        <p class="page-subtitle">Produktbewertungen verwalten</p>
    </div>
</div>

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Gesamt Bewertungen</span></div>
        <div class="kpi-value">1.234</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Durchschnitt</span></div>
        <div class="kpi-value">4,6 ★</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-header"><span class="kpi-title">Ausstehend</span></div>
        <div class="kpi-value" style="color:var(--warning);">8</div>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="alle">Alle</button>
    <button class="tab" data-tab="ausstehend">Ausstehend <span class="badge badge-warning" style="margin-left:4px;">8</span></button>
    <button class="tab" data-tab="veroeffentlicht">Veröffentlicht</button>
    <button class="tab" data-tab="abgelehnt">Abgelehnt</button>
</div>

<!-- Tab: Alle -->
<div data-tab-content="alle">
    <div class="card">
        <div class="card-body">
            <div class="review-item"><div class="review-content"><strong>Premium Lederjacke</strong> <span class="badge badge-warning" style="margin-left:8px;">Ausstehend</span><div style="color:#fbbf24;margin:8px 0;">★★★★★</div><p style="color:var(--text-secondary);">"Ausgezeichnete Qualität! Das Leder ist weich und die Verarbeitung erstklassig. Passform perfekt."</p><small style="color:var(--text-muted);">Max M. • 07.01.2026</small></div><div class="review-actions"><button class="btn btn-sm btn-success"><span class="material-symbols-rounded">check</span></button><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">close</span></button></div></div>
            <div class="review-item"><div class="review-content"><strong>Designer Sneaker Pro</strong> <span class="badge badge-success" style="margin-left:8px;">Veröffentlicht</span><div style="color:#fbbf24;margin:8px 0;">★★★★☆</div><p style="color:var(--text-secondary);">"Sehr bequem und stylisch. Ein halber Stern Abzug für die lange Lieferzeit."</p><small style="color:var(--text-muted);">Anna S. • 06.01.2026</small></div><div class="review-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></div></div>
            <div class="review-item"><div class="review-content"><strong>Cashmere Pullover</strong> <span class="badge badge-success" style="margin-left:8px;">Veröffentlicht</span><div style="color:#fbbf24;margin:8px 0;">★★★★★</div><p style="color:var(--text-secondary);">"Unglaublich weich und warm. Perfekt für den Winter!"</p><small style="color:var(--text-muted);">Peter W. • 05.01.2026</small></div><div class="review-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">edit</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></div></div>
        </div>
    </div>
</div>

<!-- Tab: Ausstehend -->
<div data-tab-content="ausstehend" style="display:none;">
    <div class="card">
        <div class="card-header"><h3>Ausstehende Bewertungen zur Moderation</h3><button class="btn btn-sm btn-success">Alle genehmigen</button></div>
        <div class="card-body">
            <div class="review-item"><div class="review-content"><strong>Premium Lederjacke</strong><div style="color:#fbbf24;margin:8px 0;">★★★★★</div><p style="color:var(--text-secondary);">"Ausgezeichnete Qualität! Das Leder ist weich und die Verarbeitung erstklassig."</p><small style="color:var(--text-muted);">Max M. • 07.01.2026</small></div><div class="review-actions"><button class="btn btn-sm btn-success"><span class="material-symbols-rounded">check</span> Genehmigen</button><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">close</span> Ablehnen</button></div></div>
            <div class="review-item"><div class="review-content"><strong>Sommer Kleid</strong><div style="color:#fbbf24;margin:8px 0;">★★★☆☆</div><p style="color:var(--text-secondary);">"Schönes Design, aber Stoff etwas dünn."</p><small style="color:var(--text-muted);">Lisa K. • 07.01.2026</small></div><div class="review-actions"><button class="btn btn-sm btn-success"><span class="material-symbols-rounded">check</span> Genehmigen</button><button class="btn btn-sm btn-danger"><span class="material-symbols-rounded">close</span> Ablehnen</button></div></div>
        </div>
    </div>
</div>

<!-- Tab: Veröffentlicht -->
<div data-tab-content="veroeffentlicht" style="display:none;">
    <div class="card">
        <div class="card-body">
            <div class="review-item"><div class="review-content"><strong>Designer Sneaker Pro</strong><div style="color:#fbbf24;margin:8px 0;">★★★★☆</div><p>"Sehr bequem und stylisch."</p><small style="color:var(--text-muted);">Anna S. • 06.01.2026</small></div><div class="review-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility_off</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></div></div>
            <div class="review-item"><div class="review-content"><strong>Cashmere Pullover</strong><div style="color:#fbbf24;margin:8px 0;">★★★★★</div><p>"Unglaublich weich und warm."</p><small style="color:var(--text-muted);">Peter W. • 05.01.2026</small></div><div class="review-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">visibility_off</span></button><button class="btn btn-sm"><span class="material-symbols-rounded">delete</span></button></div></div>
        </div>
    </div>
</div>

<!-- Tab: Abgelehnt -->
<div data-tab-content="abgelehnt" style="display:none;">
    <div class="card">
        <div class="card-body">
            <div class="review-item" style="opacity:0.6;"><div class="review-content"><strong>Winterjacke Deluxe</strong><div style="color:#fbbf24;margin:8px 0;">★☆☆☆☆</div><p style="color:var(--text-secondary);">"[Spam-Inhalt entfernt]"</p><small style="color:var(--text-muted);">Unbekannt • 03.01.2026 • Abgelehnt: Spam</small></div><div class="review-actions"><button class="btn btn-sm"><span class="material-symbols-rounded">restore</span> Wiederherstellen</button><button class="btn btn-sm"><span class="material-symbols-rounded">delete_forever</span></button></div></div>
        </div>
    </div>
</div>

<style>
.review-item { border:1px solid var(--border-color); border-radius:var(--radius-md); padding:16px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:flex-start; }
.review-actions { display:flex; gap:8px; flex-shrink:0; }
</style>
