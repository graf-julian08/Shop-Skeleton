<?php /** System - Settings */ ?>
<div class="page-header">
    <div class="page-header-content">
        <h1 data-translate="settings.title">System Settings</h1>
        <p class="page-subtitle" data-translate="settings.subtitle">General system configuration</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary" onclick="Settings.save()" id="saveBtn">
            <span class="material-symbols-rounded">save</span>
            <span data-translate="common.save">Save</span>
        </button>
    </div>
</div>

<!-- Admin Panel Settings -->
<div class="card">
    <div class="card-header">
        <h3 data-translate="settings.admin_panel">Admin Panel</h3>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" data-translate="settings.language">Admin Language</label>
                <select class="form-select" id="settingLocale">
                    <option value="">Loading...</option>
                </select>
                <p class="form-hint" data-translate="settings.language_hint">Select the language for the admin panel
                </p>
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="settings.timezone">Timezone</label>
                <select class="form-select" id="settingTimezone">
                    <option value="">Loading...</option>
                </select>
                <p class="form-hint" data-translate="settings.timezone_hint">Timezone for all date and time displays
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="form-checkbox">
                <input type="checkbox" id="settingDarkMode" checked>
                <span data-translate="settings.dark_mode">Enable Dark Mode</span>
            </label>
        </div>
        <div class="form-group">
            <label class="form-checkbox">
                <input type="checkbox" id="settingSidebarCollapsed">
                <span data-translate="settings.sidebar_remember">Remember Sidebar State</span>
            </label>
        </div>
    </div>
</div>
</div>
</div>

<!-- Performance Settings -->
<div class="card">
    <div class="card-header">
        <h3 data-translate="settings.performance">Performance</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-checkbox">
                <input type="checkbox" id="settingCaching" checked>
                <span data-translate="settings.caching">Enable Caching</span>
            </label>
        </div>
        <div class="form-group">
            <label class="form-checkbox">
                <input type="checkbox" id="settingMinification" checked>
                <span data-translate="settings.minification">Asset Minification</span>
            </label>
        </div>
        <div class="form-group">
            <label class="form-checkbox">
                <input type="checkbox" id="settingDebugMode">
                <span data-translate="settings.debug_mode">Debug Mode</span>
            </label>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<style>
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: var(--text);
    }

    .form-select,
    .form-textarea {
        width: 100%;
        padding: 10px 14px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text);
        font-size: 14px;
    }

    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--accent);
    }

    .form-hint {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .form-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
    }

    .form-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--accent);
        cursor: pointer;
    }

    .form-checkbox span {
        color: var(--text);
    }

    .form-textarea {
        resize: vertical;
        min-height: 80px;
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

    /* Optgroup styling */
    .form-select optgroup {
        font-weight: 600;
        color: var(--text);
        background: var(--bg-secondary);
    }

    .form-select option {
        padding: 8px;
    }
</style>

<script>
    const Settings = {
        shopId: 1,
        apiBase: 'api/settings.php',
        languages: [],
        timezones: [],
        originalSettings: {},

        async init() {
            await Promise.all([
                this.loadLanguages(),
                this.loadTimezones(),
                this.loadSettings()
            ]);
            this.setupEventListeners();
        },

        setupEventListeners() {
            // Dark mode change should also update the UI immediately
            document.getElementById('settingDarkMode').addEventListener('change', (e) => {
                this.applyDarkMode(e.target.checked);
            });
        },

        async loadLanguages() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_languages&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    this.languages = data.languages;
                    const select = document.getElementById('settingLocale');
                    select.innerHTML = this.languages.map(lang =>
                        `<option value="${lang.code}">${lang.language_native} (${lang.language_name})</option>`
                    ).join('');
                }
            } catch (e) {
                console.error('Error loading languages:', e);
            }
        },

        async loadTimezones() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_timezones`);
                const data = await res.json();

                if (data.success && data.grouped) {
                    const select = document.getElementById('settingTimezone');
                    let html = '';

                    // Build optgroups by region
                    const regions = Object.keys(data.grouped).sort();
                    for (const region of regions) {
                        html += `<optgroup label="${region}">`;
                        for (const tz of data.grouped[region]) {
                            html += `<option value="${tz.value}">${tz.label}</option>`;
                        }
                        html += '</optgroup>';
                    }

                    select.innerHTML = html;
                    this.timezones = data.timezones;
                }
            } catch (e) {
                console.error('Error loading timezones:', e);
            }
        },

        async loadSettings() {
            try {
                const res = await fetch(`${this.apiBase}?action=get_settings&shop_id=${this.shopId}`);
                const data = await res.json();

                if (data.success) {
                    const s = data.settings;
                    this.originalSettings = s;

                    // Apply values to form
                    document.getElementById('settingLocale').value = s.locale || 'de_DE';
                    document.getElementById('settingTimezone').value = s.timezone || 'Europe/Berlin';
                    document.getElementById('settingDarkMode').checked = s.dark_mode !== false;
                    document.getElementById('settingSidebarCollapsed').checked = s.sidebar_remember === true;
                    document.getElementById('settingCaching').checked = s.caching_enabled !== false;
                    document.getElementById('settingMinification').checked = s.asset_minification !== false;
                    document.getElementById('settingDebugMode').checked = s.debug_mode === true;

                    // Apply dark mode immediately
                    this.applyDarkMode(s.dark_mode !== false);
                }
            } catch (e) {
                console.error('Error loading settings:', e);
            }
        },

        async save() {
            const btn = document.getElementById('saveBtn');
            const newLocale = document.getElementById('settingLocale').value;
            const languageChanged = newLocale !== this.originalSettings.locale;

            // Extract language code (de_DE -> de, or just de -> de)
            const newLangCode = newLocale.includes('_') ? newLocale.split('_')[0] : newLocale;

            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-rounded spinning">sync</span> ' + __('common.loading');

            const formData = new FormData();
            formData.append('locale', newLocale);
            formData.append('timezone', document.getElementById('settingTimezone').value);
            formData.append('dark_mode', document.getElementById('settingDarkMode').checked);

            // Sidebar: sidebar_remember = checkbox value, sidebar_collapsed = current sidebar state
            const rememberSidebar = document.getElementById('settingSidebarCollapsed').checked;
            const sidebarElement = document.querySelector('.sidebar');
            const isCurrentlyCollapsed = sidebarElement ? sidebarElement.classList.contains('collapsed') : false;
            formData.append('sidebar_remember', rememberSidebar);
            formData.append('sidebar_collapsed', isCurrentlyCollapsed);

            formData.append('caching_enabled', document.getElementById('settingCaching').checked);
            formData.append('asset_minification', document.getElementById('settingMinification').checked);
            formData.append('debug_mode', document.getElementById('settingDebugMode').checked);

            try {
                const res = await fetch(`${this.apiBase}?action=save_settings&shop_id=${this.shopId}`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    // If language changed, we need to reload for complete translation
                    // (sidebar is PHP-rendered and needs server-side re-render)
                    if (languageChanged) {
                        // First fetch new translations to show toast in new language
                        try {
                            const transRes = await fetch(`${this.apiBase}?action=get_translations&lang=${newLangCode}`);
                            const transData = await transRes.json();
                            if (transData.success && transData.translations) {
                                window.AdminTranslations = transData.translations;
                            }
                        } catch (e) { }

                        // Show toast in new language, then reload
                        this.showToast(__('settings.saved_success'), 'success');
                        setTimeout(() => window.location.reload(), 800);
                        return; // Exit early, page will reload
                    }

                    // No language change - just show success message
                    this.showToast(__('settings.saved_success'), 'success');

                    // Sync dark mode with header toggle
                    this.syncDarkModeWithHeader();
                } else {
                    this.showToast(data.error || __('common.error'), 'error');
                }
            } catch (e) {
                this.showToast(__('common.error') + ': ' + e.message, 'error');
            }

            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-rounded">save</span> ' + __('common.save');
        },

        async applyNewLanguage(langCode) {
            try {
                // Fetch new translations from API
                const res = await fetch(`${this.apiBase}?action=get_translations&lang=${langCode}`);
                const data = await res.json();

                if (data.success && data.translations) {
                    // Update global translations object
                    window.AdminTranslations = data.translations;
                    window.AdminLangCode = langCode;

                    // Apply translations to all elements with data-translate attribute
                    this.updateAllTranslations();

                    // Update document language
                    document.documentElement.lang = langCode;
                }
            } catch (e) {
                console.error('Error loading translations:', e);
                // On error, just reload the page as fallback
                window.location.reload();
            }
        },

        updateAllTranslations() {
            // Update all elements with data-translate attribute
            const elements = document.querySelectorAll('[data-translate]');
            elements.forEach(el => {
                const key = el.getAttribute('data-translate');
                const translation = __(key);
                if (translation && translation !== key) {
                    el.textContent = translation;
                }
            });

            // Update sidebar menu texts directly (they don't have data-translate)
            this.updateSidebarTranslations();

            // Update header search placeholder
            const searchInput = document.querySelector('.search-box input');
            if (searchInput) {
                searchInput.placeholder = __('header.search');
            }

            // Update save button text
            const saveBtn = document.getElementById('saveBtn');
            if (saveBtn) {
                const btnSpan = saveBtn.querySelector('span:not(.material-symbols-rounded)');
                if (btnSpan) {
                    btnSpan.textContent = __('common.save');
                }
            }
        },

        updateSidebarTranslations() {
            // Map of menu translations
            const menuMap = {
                'nav.dashboard': 'Dashboard',
                'nav.shop': 'Shop',
                'nav.general': 'Allgemein',
                'nav.design': 'Design',
                'nav.cms': 'CMS',
                'nav.navigation': 'Navigation',
                'nav.localization': 'Lokalisierung',
                'nav.seo': 'SEO',
                'nav.personalization': 'Personalisierung',
                'nav.catalog': 'Katalog',
                'nav.products': 'Produkte',
                'nav.categories': 'Kategorien',
                'nav.attributes': 'Attribute',
                'nav.bundles': 'Bundles',
                'nav.configurator': 'Konfigurator',
                'nav.inventory': 'Inventar',
                'nav.customers': 'Kunden',
                'nav.customer_list': 'Kundenliste',
                'nav.customer_groups': 'Kundengruppen',
                'nav.customer_history': 'Kundenhistorie',
                'nav.orders': 'Bestellungen',
                'nav.fulfillment': 'Fulfillment',
                'nav.returns': 'Retouren',
                'nav.cancellations': 'Stornierungen',
                'nav.commerce': 'Commerce',
                'nav.checkout': 'Checkout',
                'nav.carts': 'Warenkörbe',
                'nav.pricing_rules': 'Preisregeln',
                'nav.discounts': 'Rabatte',
                'nav.taxes': 'Steuern',
                'nav.shipping': 'Versand',
                'nav.payments': 'Zahlungen',
                'nav.subscriptions': 'Abonnements',
                'nav.digital_delivery': 'Digitale Lieferung',
                'nav.finance': 'Finanzen',
                'nav.invoices': 'Rechnungen',
                'nav.credit_notes': 'Gutschriften',
                'nav.payouts': 'Auszahlungen',
                'nav.accounting': 'Buchhaltung',
                'nav.reconciliation': 'Abstimmung',
                'nav.marketing': 'Marketing',
                'nav.campaigns': 'Kampagnen',
                'nav.coupons': 'Gutscheine',
                'nav.newsletter': 'Newsletter',
                'nav.reviews': 'Bewertungen',
                'nav.reports': 'Reports',
                'nav.revenue': 'Umsatz',
                'nav.administration': 'Administration',
                'nav.users': 'Benutzer',
                'nav.roles': 'Rollen',
                'nav.permissions': 'Berechtigungen',
                'nav.system': 'System',
                'nav.settings': 'Einstellungen',
                'nav.security': 'Sicherheit',
                'nav.logs': 'Logs',
                'nav.backups': 'Backups',
                'nav.email': 'E-Mail',
                'nav.integrations': 'Integrationen',
                'nav.developer': 'Entwickler',
                'nav.api': 'API',
                'nav.webhooks': 'Webhooks',
                'nav.themes': 'Themes',
                'nav.plugins': 'Plugins',
                'nav.debug': 'Debug'
            };

            // Update menu-text elements (group headers)
            document.querySelectorAll('.menu-text').forEach(el => {
                const text = el.textContent.trim();
                // Find matching key
                for (const [key, germanText] of Object.entries(menuMap)) {
                    if (text === germanText || text === __(key)) {
                        const translation = __(key);
                        if (translation && translation !== key) {
                            el.textContent = translation;
                        }
                        break;
                    }
                }
            });

            // Update menu-item elements (menu items)
            document.querySelectorAll('.menu-item').forEach(el => {
                const text = el.textContent.trim();
                for (const [key, germanText] of Object.entries(menuMap)) {
                    if (text === germanText || text === __(key)) {
                        const translation = __(key);
                        if (translation && translation !== key) {
                            el.textContent = translation;
                        }
                        break;
                    }
                }
            });
        },

        applyDarkMode(enabled) {
            if (enabled) {
                document.documentElement.classList.add('dark-mode');
                document.documentElement.classList.remove('light-mode');
            } else {
                document.documentElement.classList.remove('dark-mode');
                document.documentElement.classList.add('light-mode');
            }
        },

        syncDarkModeWithHeader() {
            // Dispatch custom event for header to pick up
            const isDark = document.getElementById('settingDarkMode').checked;
            window.dispatchEvent(new CustomEvent('darkModeChanged', { detail: { darkMode: isDark } }));
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type + ' show';
            setTimeout(() => toast.classList.remove('show'), 4000);
        }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => Settings.init());
</script>