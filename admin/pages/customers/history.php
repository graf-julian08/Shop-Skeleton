<?php /** Kunden - Historie */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1>Kundenhistorie</h1>
        <p class="page-subtitle">Aktivitäten und Bestellhistorie</p>
    </div>
    <div class="page-header-actions">
        <div class="export-dropdown">
            <button class="btn" onclick="ActivityLog.toggleExportMenu()"><span class="material-symbols-rounded">download</span> Export</button>
            <div class="export-menu" id="exportMenu">
                <button onclick="ActivityLog.exportAs('json')"><span class="material-symbols-rounded">data_object</span> Als JSON</button>
                <button onclick="ActivityLog.exportAs('sql')"><span class="material-symbols-rounded">database</span> Als SQL</button>
            </div>
        </div>
    </div>
</div>

<!-- Filters + Timeline -->
<div class="card">
    <div class="card-body">
        <div class="filters">
            <div class="filter-search">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="searchInput" placeholder="Kunde suchen..." oninput="ActivityLog.debounceSearch()">
            </div>
            <select class="filter-select" id="typeFilter" onchange="ActivityLog.loadActivities()">
                <option value="all">Alle Aktivitäten</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="order">Bestellung</option>
                <option value="cart_add">Warenkorb</option>
                <option value="profile_update">Profil</option>
                <option value="password_reset">Passwort</option>
                <option value="support_ticket">Support</option>
                <option value="registration">Registrierung</option>
                <option value="newsletter_subscribe">Newsletter</option>
                <option value="review">Bewertung</option>
            </select>
            <select class="filter-select" id="periodFilter" onchange="ActivityLog.loadActivities()">
                <option value="today">Heute</option>
                <option value="7d" selected>Letzte 7 Tage</option>
                <option value="30d">Letzte 30 Tage</option>
                <option value="year">Dieses Jahr</option>
                <option value="all">Alle</option>
            </select>
        </div>
    </div>
</div>

<!-- Timeline -->
<div class="card">
    <div class="card-header">
        <h3>Aktivitäten</h3>
        <span class="activity-count" id="activityCount">0 Einträge</span>
    </div>
    <div class="card-body">
        <div class="timeline" id="timeline">
            <div class="loading-row"><span class="material-symbols-rounded spinning">sync</span> Lade Aktivitäten...</div>
        </div>
        <div class="load-more-container" id="loadMoreContainer" style="display:none;">
            <button class="btn" id="loadMoreBtn" onclick="ActivityLog.loadMore()">Mehr laden</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
.card-header { display: flex; justify-content: space-between; align-items: center; }
.activity-count { font-size: 13px; color: var(--text-muted); }

.timeline { margin-top: 10px; }
.timeline-item {
    border-left: 2px solid var(--border-color);
    padding: 0 0 24px 24px;
    margin-left: 10px;
    position: relative;
}
.timeline-item:last-child { padding-bottom: 0; }
.timeline-item::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 3px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--accent);
    border: 2px solid var(--bg-secondary);
}

.timeline-item.login::before { background: var(--success); }
.timeline-item.logout::before { background: #6b7280; }
.timeline-item.order::before { background: #3b82f6; }
.timeline-item.profile_update::before, .timeline-item.password_reset::before { background: #f59e0b; }
.timeline-item.support_ticket::before { background: #ef4444; }
.timeline-item.cart_add::before { background: #8b5cf6; }
.timeline-item.registration::before { background: #10b981; }
.timeline-item.newsletter_subscribe::before { background: #ec4899; }
.timeline-item.review::before { background: #06b6d4; }

.timeline-time { font-size: 12px; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
.timeline-content strong { color: var(--text-primary); }
.timeline-content a { color: var(--accent); }
.timeline-meta { font-size: 12px; color: var(--text-muted); margin-top: 4px; display: flex; gap: 12px; flex-wrap: wrap; }
.timeline-meta span { display: flex; align-items: center; gap: 4px; }
.timeline-meta .material-symbols-rounded { font-size: 14px; }

.activity-badge {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 500;
    text-transform: uppercase;
}
.activity-badge.login { background: rgba(16, 185, 129, 0.2); color: #10b981; }
.activity-badge.logout { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
.activity-badge.order { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
.activity-badge.profile_update { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.activity-badge.password_reset { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.activity-badge.support_ticket { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
.activity-badge.cart_add { background: rgba(139, 92, 246, 0.2); color: #8b5cf6; }
.activity-badge.registration { background: rgba(16, 185, 129, 0.2); color: #10b981; }
.activity-badge.newsletter_subscribe { background: rgba(236, 72, 153, 0.2); color: #ec4899; }
.activity-badge.review { background: rgba(6, 182, 212, 0.2); color: #06b6d4; }
.activity-badge.other { background: var(--bg-tertiary); color: var(--text-muted); }

.loading-row { text-align: center; padding: 40px; color: var(--text-muted); }
.spinning { animation: spin 1s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

.load-more-container { text-align: center; padding-top: 20px; }

.export-dropdown { position: relative; }
.export-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    padding: 8px 0;
    display: none;
    z-index: 100;
    min-width: 150px;
}
.export-menu.show { display: block; }
.export-menu button {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 10px 16px;
    background: none;
    border: none;
    color: var(--text-primary);
    cursor: pointer;
    font-size: 14px;
}
.export-menu button:hover { background: var(--bg-tertiary); }

.toast { position: fixed; bottom: 24px; right: 24px; padding: 16px 24px; border-radius: var(--radius-md); color: white; font-weight: 500; transform: translateY(100px); opacity: 0; transition: all 0.3s; z-index: 1001; }
.toast.show { transform: translateY(0); opacity: 1; }
.toast.success { background: var(--success); }
.toast.error { background: var(--error); }
</style>

<script>
const ActivityLog = {
    apiBase: 'api/customers.php',
    shopId: 1,
    activities: [],
    offset: 0,
    limit: 50,
    hasMore: false,
    searchTimeout: null,

    async init() {
        await this.loadActivities();
        // Generate test data if needed
        setTimeout(() => this.generateTestDataIfEmpty(), 500);
    },

    async loadActivities(reset = true) {
        if (reset) {
            this.offset = 0;
            this.activities = [];
        }

        const timeline = document.getElementById('timeline');
        if (reset) {
            timeline.innerHTML = '<div class="loading-row"><span class="material-symbols-rounded spinning">sync</span> Lade Aktivitäten...</div>';
        }

        const type = document.getElementById('typeFilter').value;
        const period = document.getElementById('periodFilter').value;
        const search = document.getElementById('searchInput').value.trim();

        try {
            const url = `${this.apiBase}?action=get_activity_log&shop_id=${this.shopId}&type=${type}&period=${period}&search=${encodeURIComponent(search)}&limit=${this.limit}&offset=${this.offset}`;
            const res = await fetch(url);
            const data = await res.json();

            if (data.success) {
                this.activities = reset ? data.activities : [...this.activities, ...data.activities];
                this.hasMore = data.has_more;
                this.renderTimeline();
                document.getElementById('activityCount').textContent = `${data.total} Einträge`;
                document.getElementById('loadMoreContainer').style.display = this.hasMore ? 'block' : 'none';
            }
        } catch (e) {
            timeline.innerHTML = '<div class="loading-row">Fehler beim Laden</div>';
        }
    },

    loadMore() {
        this.offset += this.limit;
        this.loadActivities(false);
    },

    renderTimeline() {
        const timeline = document.getElementById('timeline');
        
        if (this.activities.length === 0) {
            timeline.innerHTML = '<div class="loading-row">Keine Aktivitäten gefunden</div>';
            return;
        }

        const typeLabels = {
            'login': 'hat sich eingeloggt',
            'logout': 'hat sich ausgeloggt',
            'order': 'hat eine Bestellung aufgegeben',
            'cart_add': 'hat zum Warenkorb hinzugefügt',
            'cart_remove': 'hat aus dem Warenkorb entfernt',
            'profile_update': 'hat sein Profil aktualisiert',
            'password_reset': 'hat sein Passwort zurückgesetzt',
            'support_ticket': 'hat eine Support-Anfrage erstellt',
            'registration': 'hat sich registriert',
            'newsletter_subscribe': 'hat den Newsletter abonniert',
            'newsletter_unsubscribe': 'hat den Newsletter abbestellt',
            'review': 'hat eine Bewertung geschrieben',
            'wishlist': 'hat zur Wunschliste hinzugefügt',
            'other': 'Sonstige Aktivität'
        };

        const typeBadges = {
            'login': 'Login', 'logout': 'Logout', 'order': 'Bestellung', 
            'cart_add': 'Warenkorb', 'profile_update': 'Profil',
            'password_reset': 'Passwort', 'support_ticket': 'Support',
            'registration': 'Registrierung', 'newsletter_subscribe': 'Newsletter',
            'review': 'Bewertung', 'other': 'Sonstig'
        };

        timeline.innerHTML = this.activities.map(a => {
            const name = [a.first_name, a.last_name].filter(Boolean).join(' ') || a.email || 'Gast';
            const action = a.description || typeLabels[a.activity_type] || 'Aktivität';
            const time = this.formatTime(a.created_at);
            const badge = typeBadges[a.activity_type] || a.activity_type;
            
            let metaHtml = '';
            if (a.browser || a.device_type) {
                metaHtml = '<div class="timeline-meta">';
                if (a.browser) metaHtml += `<span><span class="material-symbols-rounded">web</span>${a.browser}</span>`;
                if (a.os) metaHtml += `<span><span class="material-symbols-rounded">computer</span>${a.os}</span>`;
                if (a.device_type && a.device_type !== 'unknown') {
                    const deviceIcon = a.device_type === 'mobile' ? 'smartphone' : (a.device_type === 'tablet' ? 'tablet' : 'computer');
                    metaHtml += `<span><span class="material-symbols-rounded">${deviceIcon}</span>${this.capitalizeFirst(a.device_type)}</span>`;
                }
                metaHtml += '</div>';
            }

            const customerLink = a.customer_id ? `<a href="?page=customers/customer_edit&id=${a.customer_id}">${this.escapeHtml(name)}</a>` : this.escapeHtml(name);

            return `
                <div class="timeline-item ${a.activity_type}">
                    <div class="timeline-time">
                        ${time}
                        <span class="activity-badge ${a.activity_type}">${badge}</span>
                    </div>
                    <div class="timeline-content">
                        <strong>${customerLink}</strong> ${action}
                        ${metaHtml}
                    </div>
                </div>
            `;
        }).join('');
    },

    formatTime(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        const diff = now - date;
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        if (days === 0) {
            return 'Heute, ' + date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
        } else if (days === 1) {
            return 'Gestern, ' + date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
        } else if (days < 7) {
            return date.toLocaleDateString('de-DE', { weekday: 'long' }) + ', ' + date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
        } else {
            return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ', ' + date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
        }
    },

    maskIP(ip) {
        if (!ip) return '';
        const parts = ip.split('.');
        if (parts.length === 4) {
            return `${parts[0]}.${parts[1]}.xxx.xxx`;
        }
        return ip.substring(0, ip.length / 2) + '...';
    },

    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    },

    debounceSearch() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => this.loadActivities(), 300);
    },

    toggleExportMenu() {
        document.getElementById('exportMenu').classList.toggle('show');
    },

    exportAs(format) {
        const type = document.getElementById('typeFilter').value;
        const period = document.getElementById('periodFilter').value;
        window.location.href = `${this.apiBase}?action=export_activity&shop_id=${this.shopId}&format=${format}&type=${type}&period=${period}`;
        document.getElementById('exportMenu').classList.remove('show');
    },

    async generateTestDataIfEmpty() {
        // Check if there are enough activities
        if (this.activities.length >= 20) return;

        // Generate 50 test activities
        const products = ['Premium Kopfhörer', 'Bluetooth Lautsprecher', 'USB-C Kabel', 'Wireless Mouse', 'Mechanische Tastatur', 'Webcam HD', 'LED Monitorlampe', 'Laptop Ständer'];
        const descriptions = {
            'login': ['hat sich eingeloggt', 'hat sich über das Web eingeloggt', 'hat sich über die App eingeloggt'],
            'logout': ['hat sich ausgeloggt', 'Sitzung beendet'],
            'order': ['hat Bestellung #%ORDER% aufgegeben', 'hat eine Expressbestellung #%ORDER% getätigt', 'hat B2B-Bestellung #%ORDER% aufgegeben'],
            'cart_add': ['hat "%PRODUCT%" zum Warenkorb hinzugefügt', 'hat 2x "%PRODUCT%" hinzugefügt', 'hat "%PRODUCT%" (Größe L) hinzugefügt'],
            'profile_update': ['hat die Lieferadresse aktualisiert', 'hat die Telefonnummer geändert', 'hat das Profilbild aktualisiert', 'hat die Rechnungsadresse geändert'],
            'password_reset': ['hat sein Passwort zurückgesetzt', 'hat ein neues Passwort angefordert'],
            'support_ticket': ['hat eine Support-Anfrage erstellt: Frage zur Retoure', 'hat Ticket #SUP-%TICKET% eröffnet', 'hat eine Reklamation eingereicht'],
            'registration': ['hat sich registriert', 'hat ein neues Konto erstellt'],
            'newsletter_subscribe': ['hat den Newsletter abonniert', 'hat sich für den VIP-Newsletter angemeldet'],
            'review': ['hat "%PRODUCT%" mit 5 Sternen bewertet', 'hat eine Bewertung für "%PRODUCT%" geschrieben']
        };
        
        const types = Object.keys(descriptions);
        const customerIds = [1, 2, 3, 4]; // Existing customer IDs

        for (let i = 0; i < 50; i++) {
            const type = types[Math.floor(Math.random() * types.length)];
            const customerId = customerIds[Math.floor(Math.random() * customerIds.length)];
            const descArray = descriptions[type];
            let desc = descArray[Math.floor(Math.random() * descArray.length)];
            
            // Replace placeholders
            desc = desc.replace('%PRODUCT%', products[Math.floor(Math.random() * products.length)]);
            desc = desc.replace('%ORDER%', 10000 + Math.floor(Math.random() * 1000));
            desc = desc.replace('%TICKET%', 100 + Math.floor(Math.random() * 900));

            const formData = new FormData();
            formData.append('action', 'log_activity');
            formData.append('shop_id', this.shopId);
            formData.append('customer_id', customerId);
            formData.append('activity_type', type);
            formData.append('description', desc);

            try {
                await fetch(this.apiBase, { method: 'POST', body: formData });
            } catch (e) {
                console.error('Error generating test data:', e);
            }

            // Small delay between inserts
            await new Promise(resolve => setTimeout(resolve, 20));
        }

        // Reload after generating
        await this.loadActivities();
        this.showToast('50 Testeinträge generiert!', 'success');
    },

    showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `toast ${type} show`;
        setTimeout(() => toast.className = 'toast', 3000);
    },

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Close export menu when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.export-dropdown')) {
        document.getElementById('exportMenu').classList.remove('show');
    }
});

document.addEventListener('DOMContentLoaded', () => ActivityLog.init());
</script>