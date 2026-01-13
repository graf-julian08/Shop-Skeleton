/**
 * AdminModal - Reusable Modal System
 * Replaces all alert() and confirm() with proper styled modals
 */

class AdminModal {
    constructor() {
        this.currentModal = null;
        this.resolveCallback = null;
        this.init();
    }

    init() {
        // Only add modal container if it doesn't exist
        if (!document.getElementById('admin-modal-container')) {
            const container = document.createElement('div');
            container.id = 'admin-modal-container';
            container.innerHTML = `
                <div class="admin-modal-backdrop" onclick="adminModal.close(false)"></div>
                <div class="admin-modal">
                    <div class="admin-modal-header">
                        <span class="admin-modal-icon material-symbols-rounded"></span>
                        <span class="admin-modal-title"></span>
                        <button class="admin-modal-close" onclick="adminModal.close(false)">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>
                    <div class="admin-modal-body"></div>
                    <div class="admin-modal-footer"></div>
                </div>
            `;
            document.body.appendChild(container);
            this.addStyles();
        }
    }

    addStyles() {
        if (document.getElementById('admin-modal-styles')) return;

        const style = document.createElement('style');
        style.id = 'admin-modal-styles';
        style.textContent = `
            #admin-modal-container {
                display: none;
                position: fixed;
                inset: 0;
                z-index: 99999;
                align-items: center;
                justify-content: center;
            }
            
            #admin-modal-container.open {
                display: flex;
            }
            
            .admin-modal-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(4px);
                animation: modalFadeIn 0.2s ease;
            }
            
            @keyframes modalFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes modalSlideIn {
                from { 
                    opacity: 0;
                    transform: translateY(-20px) scale(0.95);
                }
                to { 
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            
            .admin-modal {
                position: relative;
                background: var(--bg-card, #1a1a1a);
                border: 1px solid var(--border-color, #333);
                border-radius: 16px;
                min-width: 400px;
                max-width: 500px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
                animation: modalSlideIn 0.25s ease;
            }
            
            .admin-modal-header {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 20px 24px 16px;
                border-bottom: 1px solid var(--border-color, #333);
            }
            
            .admin-modal-icon {
                font-size: 28px;
            }
            
            .admin-modal-icon.info { color: #6366f1; }
            .admin-modal-icon.warning { color: #f59e0b; }
            .admin-modal-icon.danger { color: #ef4444; }
            .admin-modal-icon.success { color: #10b981; }
            
            .admin-modal-title {
                flex: 1;
                font-size: 18px;
                font-weight: 600;
            }
            
            .admin-modal-close {
                background: none;
                border: none;
                color: var(--text-muted, #888);
                cursor: pointer;
                padding: 4px;
                border-radius: 6px;
                transition: all 0.15s;
            }
            
            .admin-modal-close:hover {
                background: rgba(255,255,255,0.1);
                color: var(--text, #fff);
            }
            
            .admin-modal-body {
                padding: 20px 24px;
                font-size: 14px;
                line-height: 1.6;
                color: var(--text-secondary, #aaa);
            }
            
            .admin-modal-footer {
                display: flex;
                gap: 12px;
                justify-content: flex-end;
                padding: 16px 24px 20px;
                border-top: 1px solid var(--border-color, #333);
            }
            
            .admin-modal-footer .btn {
                min-width: 100px;
            }
            
            .admin-modal-footer .btn-danger {
                background: #ef4444;
                border-color: #ef4444;
            }
            
            .admin-modal-footer .btn-danger:hover {
                background: #dc2626;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Show an alert modal (replaces alert())
     */
    alert(message, options = {}) {
        return new Promise(resolve => {
            const type = options.type || 'info';
            const title = options.title || this.getDefaultTitle(type);
            const icon = options.icon || this.getDefaultIcon(type);

            this.show({
                icon,
                iconClass: type,
                title,
                body: message,
                buttons: [
                    { text: 'OK', class: 'btn btn-primary', action: true }
                ],
                resolve
            });
        });
    }

    /**
     * Show a confirm modal (replaces confirm())
     */
    confirm(message, options = {}) {
        return new Promise(resolve => {
            const type = options.type || 'warning';
            const title = options.title || 'Bestätigen';
            const icon = options.icon || 'help';
            const confirmText = options.confirmText || 'Ja, fortfahren';
            const cancelText = options.cancelText || 'Abbrechen';
            const confirmClass = options.danger ? 'btn btn-danger' : 'btn btn-primary';

            this.show({
                icon,
                iconClass: type,
                title,
                body: message,
                buttons: [
                    { text: cancelText, class: 'btn', action: false },
                    { text: confirmText, class: confirmClass, action: true }
                ],
                resolve
            });
        });
    }

    /**
     * Show a delete confirmation modal
     */
    confirmDelete(itemName, options = {}) {
        return this.confirm(
            `Möchten Sie <strong>"${itemName}"</strong> wirklich löschen?<br><br>Diese Aktion kann nicht rückgängig gemacht werden.`,
            {
                title: 'Löschen bestätigen',
                icon: 'delete',
                type: 'danger',
                confirmText: 'Löschen',
                cancelText: 'Abbrechen',
                danger: true,
                ...options
            }
        );
    }

    /**
     * Show a success message
     */
    success(message, options = {}) {
        return this.alert(message, {
            type: 'success',
            title: options.title || 'Erfolgreich',
            icon: 'check_circle',
            ...options
        });
    }

    /**
     * Show an error message
     */
    error(message, options = {}) {
        return this.alert(message, {
            type: 'danger',
            title: options.title || 'Fehler',
            icon: 'error',
            ...options
        });
    }

    getDefaultTitle(type) {
        switch (type) {
            case 'success': return 'Erfolgreich';
            case 'warning': return 'Achtung';
            case 'danger': return 'Fehler';
            default: return 'Hinweis';
        }
    }

    getDefaultIcon(type) {
        switch (type) {
            case 'success': return 'check_circle';
            case 'warning': return 'warning';
            case 'danger': return 'error';
            default: return 'info';
        }
    }

    show(config) {
        const container = document.getElementById('admin-modal-container');
        const modal = container.querySelector('.admin-modal');

        // Set content
        modal.querySelector('.admin-modal-icon').textContent = config.icon;
        modal.querySelector('.admin-modal-icon').className = `admin-modal-icon material-symbols-rounded ${config.iconClass}`;
        modal.querySelector('.admin-modal-title').textContent = config.title;
        modal.querySelector('.admin-modal-body').innerHTML = config.body;

        // Build buttons
        const footer = modal.querySelector('.admin-modal-footer');
        footer.innerHTML = config.buttons.map(btn =>
            `<button class="${btn.class}" data-action="${btn.action}">${btn.text}</button>`
        ).join('');

        // Bind button events
        footer.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.dataset.action === 'true';
                this.close(action);
                if (config.resolve) config.resolve(action);
            });
        });

        // Show modal
        container.classList.add('open');

        // Focus first button
        setTimeout(() => {
            const primaryBtn = footer.querySelector('.btn-primary, .btn-danger');
            if (primaryBtn) primaryBtn.focus();
        }, 50);

        // ESC key handler
        this.escHandler = (e) => {
            if (e.key === 'Escape') {
                this.close(false);
                if (config.resolve) config.resolve(false);
            }
        };
        document.addEventListener('keydown', this.escHandler);
    }

    close(result = false) {
        const container = document.getElementById('admin-modal-container');
        if (container) {
            container.classList.remove('open');
        }
        if (this.escHandler) {
            document.removeEventListener('keydown', this.escHandler);
        }
    }
}

// Initialize global instance
const adminModal = new AdminModal();
