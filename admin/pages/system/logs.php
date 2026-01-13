<?php /** System - Logs */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>System-Logs</h1>
        <p class="page-subtitle">Ereignis- und Fehlerprotokolle</p>
    </div>
    <div class="page-header-actions">
        <button class="btn"><span class="material-symbols-rounded">download</span> Export</button>
        <button class="btn"><span class="material-symbols-rounded">delete</span> Logs löschen</button>
    </div>
</div>

<div class="tabs">
    <button class="tab active" data-tab="alle">Alle</button>
    <button class="tab" data-tab="fehler">Fehler <span class="badge badge-error" style="margin-left:4px;">2</span></button>
    <button class="tab" data-tab="warnungen">Warnungen <span class="badge badge-warning" style="margin-left:4px;">1</span></button>
    <button class="tab" data-tab="info">Info</button>
</div>

<!-- Tab: Alle -->
<div data-tab-content="alle">
    <div class="card">
        <div class="card-body">
            <div class="filters">
                <div class="filter-search"><span class="material-symbols-rounded">search</span><input type="text" placeholder="Log durchsuchen..."></div>
                <select class="filter-select"><option>Alle Level</option><option>Error</option><option>Warning</option><option>Info</option><option>Debug</option></select>
                <select class="filter-select"><option>Heute</option><option>Letzte 7 Tage</option><option>Alle</option></select>
            </div>
            <div class="log-console">
                <div class="log-line"><span class="log-error">[ERROR]</span> <span class="log-time">2026-01-07 09:15:23</span> Payment gateway timeout for order #10046</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 09:14:58</span> User julian@example.com logged in successfully</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 09:12:45</span> Order #10045 status changed to "processing"</div>
                <div class="log-line"><span class="log-warn">[WARN]</span> <span class="log-time">2026-01-07 09:10:22</span> Low disk space warning: 85% used</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 09:08:15</span> Cache cleared successfully</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 09:05:30</span> Email sent to max@example.com (order confirmation)</div>
                <div class="log-line"><span class="log-error">[ERROR]</span> <span class="log-time">2026-01-07 08:45:12</span> Database connection timeout - reconnected</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 08:30:00</span> Scheduled task "sitemap_generate" completed</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 03:00:00</span> Backup completed: backup_2026-01-07.zip (245MB)</div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Fehler -->
<div data-tab-content="fehler" style="display:none;">
    <div class="card">
        <div class="card-body">
            <div class="log-console">
                <div class="log-line"><span class="log-error">[ERROR]</span> <span class="log-time">2026-01-07 09:15:23</span> Payment gateway timeout for order #10046</div>
                <div class="log-line"><span class="log-error">[ERROR]</span> <span class="log-time">2026-01-07 08:45:12</span> Database connection timeout - reconnected</div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Warnungen -->
<div data-tab-content="warnungen" style="display:none;">
    <div class="card">
        <div class="card-body">
            <div class="log-console">
                <div class="log-line"><span class="log-warn">[WARN]</span> <span class="log-time">2026-01-07 09:10:22</span> Low disk space warning: 85% used</div>
            </div>
        </div>
    </div>
</div>

<!-- Tab: Info -->
<div data-tab-content="info" style="display:none;">
    <div class="card">
        <div class="card-body">
            <div class="log-console">
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 09:14:58</span> User julian@example.com logged in successfully</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 09:12:45</span> Order #10045 status changed to "processing"</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 09:08:15</span> Cache cleared successfully</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 09:05:30</span> Email sent to max@example.com (order confirmation)</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 08:30:00</span> Scheduled task "sitemap_generate" completed</div>
                <div class="log-line"><span class="log-info">[INFO]</span> <span class="log-time">2026-01-07 03:00:00</span> Backup completed: backup_2026-01-07.zip (245MB)</div>
            </div>
        </div>
    </div>
</div>

<style>
.log-console { font-family:monospace; font-size:13px; background:var(--bg-tertiary); border-radius:var(--radius-md); padding:16px; max-height:500px; overflow:auto; }
.log-line { margin-bottom:8px; }
.log-error { color:var(--error); }
.log-warn { color:var(--warning); }
.log-info { color:var(--success); }
.log-time { color:var(--text-muted); }
</style>
