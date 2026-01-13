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

                // Alle offenen Menügruppen schließen beim Kollabieren
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
        init: function () {
            themeToggle = document.getElementById('theme-toggle');
            this.updateIcon();
            this.bindToggle();
        },

        getCurrentTheme: function () {
            return document.documentElement.getAttribute('data-theme') || 'dark';
        },

        setTheme: function (theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            this.updateIcon();
        },

        toggleTheme: function () {
            var current = this.getCurrentTheme();
            var newTheme = current === 'dark' ? 'light' : 'dark';
            this.setTheme(newTheme);
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

    // ========== ÖFFENTLICHE API ==========
    return {
        init: function () {
            Sidebar.init();
            Theme.init();
            Dropdowns.init();
            Alerts.init();
            Tabs.init();
            Forms.init();
        },

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
