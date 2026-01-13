/**
 * Localization Manager
 * Handles all frontend logic for the localization admin page
 */

class LocalizationManager {
    constructor() {
        this.shopId = 1;
        this.currentTab = 'sprachen';
        this.languages = [];
        this.currencies = [];
        this.translations = [];
        this.translationFilters = {
            locale: '',
            group: '',
            search: ''
        };

        this.init();
    }

    init() {
        this.initTabs();
        this.loadLanguages();
        this.loadCurrencies();
        this.initEventListeners();
    }

    // =====================================================================
    // TAB NAVIGATION
    // =====================================================================

    initTabs() {
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const tabName = tab.dataset.tab;
                this.switchTab(tabName);
            });
        });
    }

    switchTab(tabName) {
        this.currentTab = tabName;

        // Update tab buttons
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelector(`.tab[data-tab="${tabName}"]`)?.classList.add('active');

        // Update tab content
        document.querySelectorAll('[data-tab-content]').forEach(c => c.style.display = 'none');
        document.querySelector(`[data-tab-content="${tabName}"]`).style.display = 'block';

        // Load data for tab if needed
        if (tabName === 'uebersetzungen' && this.translations.length === 0) {
            this.loadTranslations();
        }
    }

    // =====================================================================
    // LANGUAGES
    // =====================================================================

    async loadLanguages() {
        try {
            const response = await fetch(`/admin/api/localization.php?action=get_languages&shop_id=${this.shopId}`);
            const data = await response.json();

            if (data.success) {
                this.languages = data.languages;
                this.renderLanguagesTable();
                this.updateLanguageSelectors();
            }
        } catch (error) {
            console.error('Error loading languages:', error);
            this.showToast('Fehler beim Laden der Sprachen', 'error');
        }
    }

    renderLanguagesTable() {
        const tbody = document.getElementById('languages-tbody');
        if (!tbody) return;

        if (this.languages.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-state">Keine Sprachen gefunden</td></tr>';
            return;
        }

        tbody.innerHTML = this.languages.map(lang => `
            <tr data-id="${lang.id}">
                <td>
                    <strong>${lang.name}</strong>
                    <span class="native-name">(${lang.native_name})</span>
                </td>
                <td><code>${lang.code}</code></td>
                <td>
                    ${lang.is_default == 1
                ? '<span class="badge badge-success">Standard</span>'
                : `<button class="btn btn-sm btn-ghost" onclick="locManager.setDefaultLanguage(${lang.id})">Als Standard</button>`
            }
                </td>
                <td>
                    <label class="toggle">
                        <input type="checkbox" ${lang.is_active == 1 ? 'checked' : ''} 
                               onchange="locManager.toggleLanguage(${lang.id}, this.checked)"
                               ${lang.is_default == 1 ? 'disabled' : ''}>
                        <span class="toggle-slider"></span>
                    </label>
                </td>
                <td class="table-actions">
                    <button class="btn btn-sm btn-icon" onclick="locManager.editLanguage(${lang.id})" title="Bearbeiten">
                        <span class="material-symbols-rounded">edit</span>
                    </button>
                    ${lang.is_default != 1 ? `
                        <button class="btn btn-sm btn-icon btn-danger" onclick="locManager.deleteLanguage(${lang.id})" title="Löschen">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    ` : ''}
                </td>
            </tr>
        `).join('');
    }

    async setDefaultLanguage(id) {
        try {
            const response = await fetch('/admin/api/localization.php?action=set_default_language', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&shop_id=${this.shopId}`
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Standardsprache gesetzt', 'success');
                this.loadLanguages();
            } else {
                this.showToast(data.error, 'error');
            }
        } catch (error) {
            this.showToast('Fehler beim Setzen der Standardsprache', 'error');
        }
    }

    async toggleLanguage(id, isActive) {
        try {
            const response = await fetch('/admin/api/localization.php?action=toggle_language', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&is_active=${isActive ? 1 : 0}&shop_id=${this.shopId}`
            });

            const data = await response.json();

            if (!data.success) {
                this.showToast(data.error, 'error');
                this.loadLanguages(); // Revert UI
            }
        } catch (error) {
            this.showToast('Fehler', 'error');
            this.loadLanguages();
        }
    }

    async deleteLanguage(id) {
        if (!confirm('Diese Sprache wirklich löschen?')) return;

        try {
            const response = await fetch('/admin/api/localization.php?action=delete_language', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&shop_id=${this.shopId}`
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Sprache gelöscht', 'success');
                this.loadLanguages();
            } else {
                this.showToast(data.error, 'error');
            }
        } catch (error) {
            this.showToast('Fehler beim Löschen', 'error');
        }
    }

    editLanguage(id) {
        const lang = this.languages.find(l => l.id == id);
        if (!lang) return;

        this.openLanguageModal(lang);
    }

    openLanguageModal(lang = null) {
        const modal = document.getElementById('language-modal');
        const title = document.getElementById('language-modal-title');
        const form = document.getElementById('language-form');

        if (lang) {
            title.textContent = 'Sprache bearbeiten';
            form.elements['id'].value = lang.id;
            form.elements['code'].value = lang.code;
            form.elements['name'].value = lang.name;
            form.elements['native_name'].value = lang.native_name;
            form.elements['is_active'].checked = lang.is_active == 1;
        } else {
            title.textContent = 'Sprache hinzufügen';
            form.reset();
            form.elements['id'].value = '';
            form.elements['is_active'].checked = true;
        }

        modal.classList.add('open');
    }

    closeLanguageModal() {
        document.getElementById('language-modal')?.classList.remove('open');
    }

    async saveLanguage(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        formData.append('shop_id', this.shopId);
        formData.append('is_active', form.elements['is_active'].checked ? 1 : 0);

        try {
            const response = await fetch('/admin/api/localization.php?action=save_language', {
                method: 'POST',
                body: new URLSearchParams(formData)
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Sprache gespeichert', 'success');
                this.closeLanguageModal();
                this.loadLanguages();
            } else {
                this.showToast(data.error, 'error');
            }
        } catch (error) {
            this.showToast('Fehler beim Speichern', 'error');
        }
    }

    // =====================================================================
    // CURRENCIES
    // =====================================================================

    async loadCurrencies() {
        try {
            const response = await fetch(`/admin/api/localization.php?action=get_currencies&shop_id=${this.shopId}`);
            const data = await response.json();

            if (data.success) {
                this.currencies = data.currencies;
                this.renderCurrenciesTable();
            }
        } catch (error) {
            console.error('Error loading currencies:', error);
        }
    }

    renderCurrenciesTable() {
        const tbody = document.getElementById('currencies-tbody');
        if (!tbody) return;

        if (this.currencies.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="empty-state">Keine Währungen gefunden</td></tr>';
            return;
        }

        tbody.innerHTML = this.currencies.map(curr => `
            <tr data-id="${curr.id}">
                <td><strong>${curr.name}</strong></td>
                <td><code>${curr.code}</code></td>
                <td class="currency-symbol">${curr.symbol}</td>
                <td>${parseFloat(curr.exchange_rate).toFixed(4)}</td>
                <td>
                    ${curr.is_default == 1
                ? '<span class="badge badge-success">Standard</span>'
                : `<button class="btn btn-sm btn-ghost" onclick="locManager.setDefaultCurrency(${curr.id})">Als Standard</button>`
            }
                </td>
                <td>
                    <label class="toggle">
                        <input type="checkbox" ${curr.is_active == 1 ? 'checked' : ''} 
                               onchange="locManager.toggleCurrency(${curr.id}, this.checked)"
                               ${curr.is_default == 1 ? 'disabled' : ''}>
                        <span class="toggle-slider"></span>
                    </label>
                </td>
                <td class="table-actions">
                    <button class="btn btn-sm btn-icon" onclick="locManager.editCurrency(${curr.id})" title="Bearbeiten">
                        <span class="material-symbols-rounded">edit</span>
                    </button>
                    ${curr.is_default != 1 ? `
                        <button class="btn btn-sm btn-icon btn-danger" onclick="locManager.deleteCurrency(${curr.id})" title="Löschen">
                            <span class="material-symbols-rounded">delete</span>
                        </button>
                    ` : ''}
                </td>
            </tr>
        `).join('');
    }

    async setDefaultCurrency(id) {
        try {
            const response = await fetch('/admin/api/localization.php?action=set_default_currency', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&shop_id=${this.shopId}`
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Standardwährung gesetzt', 'success');
                this.loadCurrencies();
            } else {
                this.showToast(data.error, 'error');
            }
        } catch (error) {
            this.showToast('Fehler', 'error');
        }
    }

    async toggleCurrency(id, isActive) {
        try {
            const response = await fetch('/admin/api/localization.php?action=toggle_currency', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&is_active=${isActive ? 1 : 0}&shop_id=${this.shopId}`
            });

            const data = await response.json();

            if (!data.success) {
                this.showToast(data.error, 'error');
                this.loadCurrencies();
            }
        } catch (error) {
            this.loadCurrencies();
        }
    }

    async deleteCurrency(id) {
        if (!confirm('Diese Währung wirklich löschen?')) return;

        try {
            const response = await fetch('/admin/api/localization.php?action=delete_currency', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&shop_id=${this.shopId}`
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Währung gelöscht', 'success');
                this.loadCurrencies();
            } else {
                this.showToast(data.error, 'error');
            }
        } catch (error) {
            this.showToast('Fehler', 'error');
        }
    }

    editCurrency(id) {
        const curr = this.currencies.find(c => c.id == id);
        if (!curr) return;

        this.openCurrencyModal(curr);
    }

    openCurrencyModal(curr = null) {
        const modal = document.getElementById('currency-modal');
        const title = document.getElementById('currency-modal-title');
        const form = document.getElementById('currency-form');

        if (curr) {
            title.textContent = 'Währung bearbeiten';
            form.elements['id'].value = curr.id;
            form.elements['code'].value = curr.code;
            form.elements['name'].value = curr.name;
            form.elements['symbol'].value = curr.symbol;
            form.elements['exchange_rate'].value = curr.exchange_rate;
            form.elements['decimal_places'].value = curr.decimal_places;
            form.elements['decimal_separator'].value = curr.decimal_separator;
            form.elements['thousands_separator'].value = curr.thousands_separator;
            form.elements['symbol_position'].value = curr.symbol_position;
            form.elements['is_active'].checked = curr.is_active == 1;
        } else {
            title.textContent = 'Währung hinzufügen';
            form.reset();
            form.elements['id'].value = '';
            form.elements['exchange_rate'].value = '1.00';
            form.elements['decimal_places'].value = '2';
            form.elements['is_active'].checked = true;
        }

        modal.classList.add('open');
    }

    closeCurrencyModal() {
        document.getElementById('currency-modal')?.classList.remove('open');
    }

    async saveCurrency(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        formData.append('shop_id', this.shopId);
        formData.append('is_active', form.elements['is_active'].checked ? 1 : 0);

        try {
            const response = await fetch('/admin/api/localization.php?action=save_currency', {
                method: 'POST',
                body: new URLSearchParams(formData)
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Währung gespeichert', 'success');
                this.closeCurrencyModal();
                this.loadCurrencies();
            } else {
                this.showToast(data.error, 'error');
            }
        } catch (error) {
            this.showToast('Fehler beim Speichern', 'error');
        }
    }

    // =====================================================================
    // TRANSLATIONS
    // =====================================================================

    async loadTranslations() {
        try {
            const params = new URLSearchParams({
                action: 'get_translations',
                shop_id: this.shopId,
                locale: this.translationFilters.locale,
                group: this.translationFilters.group,
                search: this.translationFilters.search,
                limit: 100
            });

            const response = await fetch(`/admin/api/localization.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.translations = data.translations;
                this.renderTranslationsTable();
            }
        } catch (error) {
            console.error('Error loading translations:', error);
        }
    }

    renderTranslationsTable() {
        const tbody = document.getElementById('translations-tbody');
        if (!tbody) return;

        if (this.translations.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Keine Übersetzungen gefunden</td></tr>';
            return;
        }

        tbody.innerHTML = this.translations.map(t => `
            <tr data-id="${t.id}">
                <td><code>${t.translation_group}.${t.translation_key}</code></td>
                <td class="translation-locale">${t.locale}</td>
                <td>
                    <input type="text" class="form-input translation-input" 
                           value="${this.escapeHtml(t.translation_value)}"
                           data-id="${t.id}"
                           onchange="locManager.saveTranslationInline(${t.id}, this.value)">
                </td>
                <td>
                    <span class="badge ${t.translation_value ? 'badge-success' : 'badge-warning'}">
                        ${t.translation_value ? 'Übersetzt' : 'Fehlt'}
                    </span>
                </td>
            </tr>
        `).join('');
    }

    async saveTranslationInline(id, value) {
        try {
            const response = await fetch('/admin/api/localization.php?action=save_translation', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&value=${encodeURIComponent(value)}&shop_id=${this.shopId}`
            });

            const data = await response.json();

            if (data.success) {
                // Update UI badge
                const row = document.querySelector(`tr[data-id="${id}"]`);
                const badge = row?.querySelector('.badge');
                if (badge) {
                    badge.className = value ? 'badge badge-success' : 'badge badge-warning';
                    badge.textContent = value ? 'Übersetzt' : 'Fehlt';
                }
            }
        } catch (error) {
            console.error('Error saving translation:', error);
        }
    }

    filterTranslations() {
        this.translationFilters.locale = document.getElementById('filter-locale')?.value || '';
        this.translationFilters.group = document.getElementById('filter-group')?.value || '';
        this.translationFilters.search = document.getElementById('filter-search')?.value || '';

        this.loadTranslations();
    }

    exportTranslations() {
        const locale = this.translationFilters.locale || 'all';
        const url = `/admin/api/localization.php?action=export_translations&shop_id=${this.shopId}&locale=${locale}`;
        window.open(url, '_blank');
    }

    openImportModal() {
        document.getElementById('import-modal')?.classList.add('open');
    }

    closeImportModal() {
        document.getElementById('import-modal')?.classList.remove('open');
    }

    async importTranslations(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        formData.append('shop_id', this.shopId);

        try {
            const response = await fetch('/admin/api/localization.php?action=import_translations', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showToast(data.message, 'success');
                this.closeImportModal();
                this.loadTranslations();
            } else {
                this.showToast(data.error, 'error');
            }
        } catch (error) {
            this.showToast('Fehler beim Import', 'error');
        }
    }

    // =====================================================================
    // UTILITY FUNCTIONS
    // =====================================================================

    updateLanguageSelectors() {
        // Update the filter dropdown for translations
        const filterLocale = document.getElementById('filter-locale');
        if (filterLocale) {
            const currentValue = filterLocale.value;
            filterLocale.innerHTML = '<option value="">Alle Sprachen</option>' +
                this.languages.filter(l => l.is_active == 1).map(l =>
                    `<option value="${l.code}">${l.name} (${l.code})</option>`
                ).join('');
            filterLocale.value = currentValue;
        }

        // Update language count badge
        const activeLangs = this.languages.filter(l => l.is_active == 1).length;
        const langCount = document.getElementById('language-count');
        if (langCount) langCount.textContent = activeLangs;
    }

    initEventListeners() {
        // Add Language button
        document.getElementById('btn-add-language')?.addEventListener('click', () => this.openLanguageModal());

        // Add Currency button
        document.getElementById('btn-add-currency')?.addEventListener('click', () => this.openCurrencyModal());

        // Language form submit
        document.getElementById('language-form')?.addEventListener('submit', (e) => this.saveLanguage(e));

        // Currency form submit
        document.getElementById('currency-form')?.addEventListener('submit', (e) => this.saveCurrency(e));

        // Modal close buttons
        document.querySelectorAll('[data-close-modal]').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.modal')?.classList.remove('open');
            });
        });

        // Click outside modal to close
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.remove('open');
            });
        });

        // Translation filters
        document.getElementById('filter-locale')?.addEventListener('change', () => this.filterTranslations());
        document.getElementById('filter-group')?.addEventListener('change', () => this.filterTranslations());
        document.getElementById('filter-search')?.addEventListener('input',
            this.debounce(() => this.filterTranslations(), 300)
        );

        // Export button
        document.getElementById('btn-export')?.addEventListener('click', () => this.exportTranslations());

        // Import button
        document.getElementById('btn-import')?.addEventListener('click', () => this.openImportModal());

        // Import form
        document.getElementById('import-form')?.addEventListener('submit', (e) => this.importTranslations(e));
    }

    showToast(message, type = 'info') {
        // Use adminModal if available
        if (typeof adminModal !== 'undefined') {
            if (type === 'error') {
                adminModal.error(message);
            } else {
                adminModal.success(message);
            }
            return;
        }

        // Fallback toast
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 12px 24px;
            background: ${type === 'error' ? '#ef4444' : '#22c55e'};
            color: white;
            border-radius: 8px;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    async saveRegionalSettings() {
        const data = {
            shop_id: this.shopId,
            timezone: document.getElementById('timezone')?.value,
            date_format: document.getElementById('date-format')?.value,
            time_format: document.getElementById('time-format')?.value,
            weight_unit: document.getElementById('weight-unit')?.value,
            dimension_unit: document.getElementById('dimension-unit')?.value
        };

        try {
            const response = await fetch('/admin/api/localization.php?action=save_regional_settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showToast('Regionale Einstellungen gespeichert', 'success');
            } else {
                this.showToast(result.error, 'error');
            }
        } catch (error) {
            this.showToast('Fehler beim Speichern', 'error');
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Initialize when DOM is ready
let locManager;
document.addEventListener('DOMContentLoaded', () => {
    locManager = new LocalizationManager();
});
