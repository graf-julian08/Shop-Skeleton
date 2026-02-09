/**
 * ============================================
 * ADMIN PANEL - JAVASCRIPT
 * ============================================
 * Namespace: AdminApp
 * KEIN globaler Code
 * Alle Funktionen im Namespace
 * ============================================
 */

var AdminApp = (function () {
    'use strict';

    // ========== PRIVATE VARIABLEN ==========
    var sidebar = null;
    var sidebarToggle = null;
    var mobileMenuToggle = null;
    var sidebarOverlay = null;
    var themeToggle = null;

    // ========== SIDEBAR MODUL ==========
    var Sidebar = {
        init: function () {
            sidebar = document.querySelector('.sidebar');
            sidebarToggle = document.getElementById('sidebar-toggle');
            mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            sidebarOverlay = document.getElementById('sidebar-overlay');

            if (!sidebar) return;

            // Load sidebar state from sessionStorage (persists on reload, resets on new session)
            var savedState = sessionStorage.getItem('sidebar_collapsed');
            if (savedState === 'true') {
                sidebar.classList.add('collapsed');
            }
            // Default: sidebar is open (no class added)

            this.bindDesktopToggle();
            this.bindMobileToggle();
            this.bindMenuGroups();
            this.bindHoverExpand();
            this.bindResize();
        },

        bindDesktopToggle: function () {
            if (!sidebarToggle) return;

            sidebarToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.toggle('collapsed');

                // Save to sessionStorage (survives page reload, not new session)
                sessionStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));

                // Close all open menu groups when collapsing
                if (sidebar.classList.contains('collapsed')) {
                    var openGroups = document.querySelectorAll('.menu-group.open');
                    for (var i = 0; i < openGroups.length; i++) {
                        openGroups[i].classList.remove('open');
                    }
                }
            });
        },

        bindMobileToggle: function () {
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('open');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.toggle('active');
                    }
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.remove('active');
                });
            }
        },

        bindMenuGroups: function () {
            var menuGroups = document.querySelectorAll('.menu-group');

            for (var i = 0; i < menuGroups.length; i++) {
                (function (group) {
                    var header = group.querySelector('.menu-group-header');
                    if (header) {
                        header.addEventListener('click', function () {
                            // Andere Gruppen schließen (außer aktive)
                            for (var j = 0; j < menuGroups.length; j++) {
                                if (menuGroups[j] !== group && !menuGroups[j].classList.contains('has-active')) {
                                    menuGroups[j].classList.remove('open');
                                }
                            }
                            group.classList.toggle('open');
                        });
                    }
                })(menuGroups[i]);
            }

            // Auto-open Gruppe mit aktivem Item
            var activeItem = document.querySelector('.menu-item.active');
            if (activeItem) {
                var parentGroup = activeItem.closest('.menu-group');
                if (parentGroup) {
                    parentGroup.classList.add('open', 'has-active');
                }
            }
        },

        bindHoverExpand: function () {
            sidebar.addEventListener('mouseenter', function () {
                if (window.innerWidth > 768 && sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                }
            });
        },

        bindResize: function () {
            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.remove('active');
                    }
                }
            });
        }
    };

    // ========== THEME MODUL ==========
    var Theme = {
        shopId: 1,
        apiBase: 'api/settings.php',

        init: function () {
            themeToggle = document.getElementById('theme-toggle');
            this.loadFromDatabase();
            this.updateIcon();
            this.bindToggle();
            this.listenForSettingsPageChanges();
        },

        loadFromDatabase: function () {
            var self = this;
            // Fetch settings from database
            fetch(this.apiBase + '?action=get_settings&shop_id=' + this.shopId)
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success && data.settings) {
                        var darkMode = data.settings.dark_mode !== false;
                        var theme = darkMode ? 'dark' : 'light';
                        self.setTheme(theme, false); // Don't save back to DB
                    }
                })
                .catch(function (e) {
                    console.log('Could not load theme from database, using localStorage');
                });
        },

        getCurrentTheme: function () {
            return document.documentElement.getAttribute('data-theme') || 'dark';
        },

        setTheme: function (theme, saveToDb) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            this.updateIcon();

            // Sync Settings page checkbox if visible
            var settingsCheckbox = document.getElementById('settingDarkMode');
            if (settingsCheckbox) {
                settingsCheckbox.checked = (theme === 'dark');
            }

            // Save to database if requested
            if (saveToDb !== false) {
                this.saveToDatabase(theme === 'dark');
            }
        },

        toggleTheme: function () {
            var current = this.getCurrentTheme();
            var newTheme = current === 'dark' ? 'light' : 'dark';
            this.setTheme(newTheme, true); // Save to DB
        },

        saveToDatabase: function (darkMode) {
            var formData = new FormData();
            formData.append('dark_mode', darkMode);

            fetch(this.apiBase + '?action=toggle_dark_mode&shop_id=' + this.shopId, {
                method: 'POST',
                body: formData
            }).catch(function (e) {
                console.log('Could not save theme to database');
            });
        },

        updateIcon: function () {
            if (!themeToggle) return;
            var icon = themeToggle.querySelector('.material-symbols-rounded');
            if (icon) {
                icon.textContent = this.getCurrentTheme() === 'dark' ? 'light_mode' : 'dark_mode';
            }
        },

        bindToggle: function () {
            var self = this;
            if (themeToggle) {
                themeToggle.addEventListener('click', function () {
                    self.toggleTheme();
                });
            }
        },

        listenForSettingsPageChanges: function () {
            var self = this;
            // Listen for custom event from Settings page
            window.addEventListener('darkModeChanged', function (e) {
                var darkMode = e.detail.darkMode;
                self.setTheme(darkMode ? 'dark' : 'light', false);
            });
        }
    };

    // ========== DROPDOWNS MODUL ==========
    var Dropdowns = {
        toggles: [
            { btn: 'notifications-toggle', menu: 'notifications-dropdown' },
            { btn: 'help-toggle', menu: 'help-dropdown' },
            { btn: 'user-menu-toggle', menu: 'user-dropdown' }
        ],

        init: function () {
            this.bindToggles();
            this.bindOutsideClick();
            this.bindEscapeKey();
        },

        closeAll: function () {
            for (var i = 0; i < this.toggles.length; i++) {
                var menu = document.getElementById(this.toggles[i].menu);
                var btn = document.getElementById(this.toggles[i].btn);
                if (menu) menu.classList.remove('active');
                if (btn && btn.parentElement) btn.parentElement.classList.remove('active');
            }
        },

        bindToggles: function () {
            var self = this;

            for (var i = 0; i < this.toggles.length; i++) {
                (function (toggle) {
                    var btn = document.getElementById(toggle.btn);
                    var menu = document.getElementById(toggle.menu);

                    if (btn && menu) {
                        btn.addEventListener('click', function (e) {
                            e.stopPropagation();

                            // Andere Dropdowns schließen
                            for (var j = 0; j < self.toggles.length; j++) {
                                if (self.toggles[j].menu !== toggle.menu) {
                                    var otherMenu = document.getElementById(self.toggles[j].menu);
                                    var otherBtn = document.getElementById(self.toggles[j].btn);
                                    if (otherMenu) otherMenu.classList.remove('active');
                                    if (otherBtn && otherBtn.parentElement) {
                                        otherBtn.parentElement.classList.remove('active');
                                    }
                                }
                            }

                            // Aktuelles togglen
                            menu.classList.toggle('active');
                            if (btn.parentElement) {
                                btn.parentElement.classList.toggle('active');
                            }
                        });
                    }
                })(this.toggles[i]);
            }
        },

        bindOutsideClick: function () {
            var self = this;
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.header-dropout-wrapper')) {
                    self.closeAll();
                }
            });
        },

        bindEscapeKey: function () {
            var self = this;
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    self.closeAll();
                }
            });
        }
    };

    // ========== ALERTS MODUL ==========
    var Alerts = {
        init: function () {
            var closeButtons = document.querySelectorAll('.alert-close');
            for (var i = 0; i < closeButtons.length; i++) {
                closeButtons[i].addEventListener('click', function () {
                    var alert = this.closest('.alert');
                    if (alert) {
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        setTimeout(function () {
                            alert.remove();
                        }, 200);
                    }
                });
            }
        }
    };

    // ========== TABS MODUL ==========
    var Tabs = {
        init: function () {
            var tabContainers = document.querySelectorAll('.tabs');
            var self = this;

            for (var i = 0; i < tabContainers.length; i++) {
                var tabs = tabContainers[i].querySelectorAll('.tab');
                for (var j = 0; j < tabs.length; j++) {
                    (function (tab, container) {
                        tab.addEventListener('click', function () {
                            // Remove active from all tabs in this container
                            var siblings = container.querySelectorAll('.tab');
                            for (var k = 0; k < siblings.length; k++) {
                                siblings[k].classList.remove('active');
                            }
                            this.classList.add('active');

                            // Find and show the corresponding tab content
                            var tabId = this.getAttribute('data-tab');
                            if (tabId) {
                                self.switchContent(tabId, container);
                            }
                        });
                    })(tabs[j], tabContainers[i]);
                }
            }
        },

        switchContent: function (tabId, tabsContainer) {
            // Find the parent that contains both tabs and tab-content
            var parent = tabsContainer.parentElement;
            if (!parent) return;

            // Hide all tab content panels (look for [data-tab-content] attribute)
            var allPanels = parent.querySelectorAll('[data-tab-content]');
            for (var i = 0; i < allPanels.length; i++) {
                allPanels[i].style.display = 'none';
                allPanels[i].classList.remove('active');
            }

            // Show the selected panel
            var targetPanel = parent.querySelector('[data-tab-content="' + tabId + '"]');
            if (targetPanel) {
                targetPanel.style.display = 'block';
                targetPanel.classList.add('active');
            }
        }
    };

    // ========== FORMS MODUL ==========
    var Forms = {
        init: function () {
            this.bindToggles();
            this.bindValidation();
            this.bindSelectAll();
        },

        bindToggles: function () {
            var toggles = document.querySelectorAll('.toggle input');
            for (var i = 0; i < toggles.length; i++) {
                toggles[i].addEventListener('change', function () {
                    // Placeholder für Toggle-Logik
                });
            }
        },

        bindValidation: function () {
            var inputs = document.querySelectorAll('.form-input, .form-textarea');
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].addEventListener('blur', function () {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.style.borderColor = 'var(--error)';
                    } else {
                        this.style.borderColor = '';
                    }
                });
            }
        },

        bindSelectAll: function () {
            var selectAllCheckboxes = document.querySelectorAll('.select-all');
            for (var i = 0; i < selectAllCheckboxes.length; i++) {
                selectAllCheckboxes[i].addEventListener('change', function () {
                    var table = this.closest('table');
                    if (table) {
                        var checkboxes = table.querySelectorAll('tbody input[type="checkbox"]');
                        var checked = this.checked;
                        for (var j = 0; j < checkboxes.length; j++) {
                            checkboxes[j].checked = checked;
                        }
                    }
                });
            }
        }
    };

    // ========== TRANSLATIONS MODUL ==========
    var Translations = {
        // Menu translation map (German to translation key)
        menuTranslations: {
            'Dashboard': 'nav.dashboard',
            'Shop': 'nav.shop',
            'Allgemein': 'nav.settings',
            'Design': 'nav.design',
            'CMS': 'nav.cms',
            'Navigation': 'nav.navigation',
            'Lokalisierung': 'nav.localization',
            'SEO': 'nav.seo',
            'Personalisierung': 'nav.personalization',
            'Katalog': 'nav.catalog',
            'Produkte': 'nav.products',
            'Kategorien': 'nav.categories',
            'Attribute': 'nav.attributes',
            'Bundles': 'nav.bundles',
            'Konfigurator': 'nav.configurator',
            'Inventar': 'nav.inventory',
            'Kunden': 'nav.customers',
            'Kundenliste': 'nav.customer_list',
            'Kundengruppen': 'nav.customer_groups',
            'Kundenhistorie': 'nav.customer_list',
            'Bestellungen': 'nav.orders',
            'Fulfillment': 'nav.fulfillment',
            'Retouren': 'nav.returns',
            'Stornierungen': 'nav.cancellations',
            'Commerce': 'nav.commerce',
            'Checkout': 'nav.commerce',
            'Warenkörbe': 'nav.commerce',
            'Preisregeln': 'nav.commerce',
            'Rabatte': 'nav.coupons',
            'Steuern': 'nav.taxes',
            'Versand': 'nav.shipping',
            'Zahlungen': 'nav.payments',
            'Abonnements': 'nav.subscriptions',
            'Digitale Lieferung': 'nav.commerce',
            'Finanzen': 'nav.finance',
            'Rechnungen': 'nav.invoices',
            'Gutschriften': 'nav.credit_notes',
            'Auszahlungen': 'nav.finance',
            'Buchhaltung': 'nav.finance',
            'Abstimmung': 'nav.finance',
            'Marketing': 'nav.marketing',
            'Kampagnen': 'nav.marketing',
            'Gutscheine': 'nav.coupons',
            'Newsletter': 'nav.newsletter',
            'Bewertungen': 'nav.reviews',
            'Reports': 'nav.reports',
            'Umsatz': 'dashboard.revenue',
            'Administration': 'nav.administration',
            'Benutzer': 'nav.users',
            'Rollen': 'nav.roles',
            'Berechtigungen': 'nav.roles',
            'System': 'nav.system',
            'Einstellungen': 'nav.settings',
            'Sicherheit': 'nav.security',
            'Logs': 'nav.logs',
            'Backups': 'nav.system',
            'E-Mail': 'nav.email',
            'Integrationen': 'nav.system',
            'Entwickler': 'nav.developer',
            'API': 'nav.api',
            'Webhooks': 'nav.developer',
            'Themes': 'nav.themes',
            'Plugins': 'nav.developer',
            'Debug': 'nav.developer'
        },

        init: function () {
            // Only apply translations if not German
            if (window.AdminLangCode && window.AdminLangCode !== 'de') {
                this.applyTranslations();
            }
        },

        applyTranslations: function () {
            var self = this;

            // Translate elements with data-translate attribute
            var elements = document.querySelectorAll('[data-translate]');
            for (var i = 0; i < elements.length; i++) {
                var key = elements[i].getAttribute('data-translate');
                var translation = window.__(key);
                if (translation && translation !== key) {
                    elements[i].textContent = translation;
                }
            }

            // Translate sidebar menu items
            this.translateSidebar();

            // Translate page headers
            this.translatePageHeaders();
        },

        translateSidebar: function () {
            var self = this;

            // Translate menu group headers
            var menuTexts = document.querySelectorAll('.menu-text');
            for (var i = 0; i < menuTexts.length; i++) {
                var text = menuTexts[i].textContent.trim();
                var key = this.menuTranslations[text];
                if (key) {
                    var translation = window.__(key);
                    if (translation && translation !== key) {
                        menuTexts[i].textContent = translation;
                    }
                }
            }

            // Translate menu items
            var menuItems = document.querySelectorAll('.menu-item');
            for (var i = 0; i < menuItems.length; i++) {
                var text = menuItems[i].textContent.trim();
                var key = this.menuTranslations[text];
                if (key) {
                    var translation = window.__(key);
                    if (translation && translation !== key) {
                        menuItems[i].textContent = translation;
                    }
                }
            }

            // Translate direct menu links
            var directLinks = document.querySelectorAll('.menu-direct .menu-text');
            for (var i = 0; i < directLinks.length; i++) {
                var text = directLinks[i].textContent.trim();
                var key = this.menuTranslations[text];
                if (key) {
                    var translation = window.__(key);
                    if (translation && translation !== key) {
                        directLinks[i].textContent = translation;
                    }
                }
            }
        },

        translatePageHeaders: function () {
            // Translate common page header elements
            var pageTitle = document.querySelector('.page-header h1');
            if (pageTitle) {
                var translateKey = pageTitle.getAttribute('data-translate');
                if (translateKey) {
                    var translation = window.__(translateKey);
                    if (translation && translation !== translateKey) {
                        pageTitle.textContent = translation;
                    }
                }
            }

            // Translate page subtitles
            var pageSubtitle = document.querySelector('.page-subtitle');
            if (pageSubtitle) {
                var translateKey = pageSubtitle.getAttribute('data-translate');
                if (translateKey) {
                    var translation = window.__(translateKey);
                    if (translation && translation !== translateKey) {
                        pageSubtitle.textContent = translation;
                    }
                }
            }
        }
    };

    // ========== ÖFFENTLICHE API ==========
    return {
        init: function () {
            Sidebar.init();
            Theme.init();
            Dropdowns.init();
            Alerts.init();
            Tabs.init();
            Forms.init();
            Translations.init();
        },

        // Expose Translations module for external use (language switching)
        Translations: Translations,

        // Öffentliche Methoden für externe Nutzung
        toggleTheme: function () {
            Theme.toggleTheme();
        },

        closeDropdowns: function () {
            Dropdowns.closeAll();
        },

        showAlert: function (message, type) {
            // Placeholder für dynamische Alert-Erstellung
            console.log('[AdminApp] Alert:', type, message);
        }
    };
})();

// ========== INITIALISIERUNG ==========
document.addEventListener('DOMContentLoaded', function () {
    AdminApp.init();
});
