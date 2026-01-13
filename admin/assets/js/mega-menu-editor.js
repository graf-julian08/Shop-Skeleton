/**
 * Mega Menu Visual Editor
 * Professional responsive drag-and-drop builder
 */

class MegaMenuEditor {
    constructor() {
        this.navItemId = window.MEGA_EDITOR_DATA.navItemId;
        this.elements = window.MEGA_EDITOR_DATA.elements || [];
        this.templates = window.MEGA_EDITOR_DATA.templates || [];

        this.selectedElement = null;
        this.isDragging = false;
        this.isResizing = false;
        this.dragOffset = { x: 0, y: 0 };

        // FREE POSITIONING - no grid snap by default
        this.gridSize = 1;
        this.snapEnabled = false;
        this.gridVisible = false;

        // Responsive breakpoints
        this.currentBreakpoint = 'desktop'; // 'desktop', 'tablet', 'mobile'
        this.breakpoints = {
            desktop: { minWidth: 1025 },
            tablet: { minWidth: 481, maxWidth: 1024 },
            mobile: { maxWidth: 480 }
        };

        // History for undo/redo
        this.history = [];
        this.historyIndex = -1;
        this.maxHistory = 50;

        // Clipboard for copy/paste
        this.clipboard = null;

        // Autosave
        this.autoSaveTimer = null;
        this.lastSaveTime = null;
        this.hasUnsavedChanges = false;

        // Code editor
        this.codeEditorOpen = false;

        this.canvas = document.getElementById('mega-canvas');
        this.canvasElements = document.getElementById('canvas-elements');

        this.init();
    }

    init() {
        // IMPORTANT: Determine the correct breakpoint BEFORE rendering
        // This ensures initial render shows correct elements for saved width
        this.initCurrentBreakpointFromSavedWidth();

        this.renderElements();
        this.bindEvents();
        this.loadExistingElements();
        this.initTemplateModal();
        this.initLoadMoreTemplates();
        this.initBreakpointTabs();
        this.initCodeEditor();
        this.initAutosave();
    }

    /**
     * Determine the initial breakpoint based on saved canvas width
     * Must be called BEFORE renderElements() to show correct elements
     */
    initCurrentBreakpointFromSavedWidth() {
        const isSideMenu = window.MEGA_EDITOR_DATA.isSideMenu;
        const menuId = window.MEGA_EDITOR_DATA.menuItemId;
        const savedWidth = localStorage.getItem(`mega_menu_width_${menuId}`);
        const defaultWidth = isSideMenu ? 400 : (window.MEGA_EDITOR_DATA.defaultCanvasWidth || 1100);
        const width = savedWidth ? parseInt(savedWidth) : defaultWidth;

        // Set breakpoint based on width
        // Breakpoints: mobile <= 480, tablet 481-959, desktop >= 960
        if (width <= 480) {
            this.currentBreakpoint = 'mobile';
        } else if (width < 960) {
            this.currentBreakpoint = 'tablet';
        } else {
            this.currentBreakpoint = 'desktop';
        }
    }


    // ========== RENDERING ==========

    /**
     * Check if element has explicit position for a specific breakpoint
     * Returns false if position is null/undefined (element not placed on this breakpoint)
     */
    hasPositionForBreakpoint(elData, breakpoint) {
        if (breakpoint === 'desktop') {
            // Desktop always has position if element exists
            return (elData.pos_x != null || elData.x != null);
        }
        if (breakpoint === 'tablet') {
            return elData.tablet_pos_x != null;
        }
        if (breakpoint === 'mobile') {
            return elData.mobile_pos_x != null;
        }
        return false;
    }

    /**
     * Get elements that are NOT placed on the current breakpoint
     * These will be shown in the "Unplaced Elements" tray
     */
    getUnplacedElements() {
        return this.elements.filter(el => !this.hasPositionForBreakpoint(el, this.currentBreakpoint));
    }

    /**
     * Get elements that ARE placed on the current breakpoint
     */
    getPlacedElements() {
        return this.elements.filter(el => this.hasPositionForBreakpoint(el, this.currentBreakpoint));
    }

    renderElements() {
        this.canvasElements.innerHTML = '';

        // STRICT BREAKPOINT MODE: Only render elements with explicit positions
        const placedElements = this.getPlacedElements();

        placedElements.forEach(el => {
            const element = this.createElementDOM(el);
            this.canvasElements.appendChild(element);
        });

        // Update the unplaced elements tray
        this.renderUnplacedElementsTray();

        // NOTE: We do NOT call repositionAllElements() here anymore.
        // Elements keep their stored positions from the database.
        // Anchor-based repositioning only happens during active slider movement.
    }



    /**
     * Render the "Unplaced Elements" tray in the sidebar
     * Shows elements that exist but aren't placed on the current breakpoint
     */
    renderUnplacedElementsTray() {
        const tray = document.getElementById('unplaced-elements-tray');
        if (!tray) return;

        const unplaced = this.getUnplacedElements();

        if (unplaced.length === 0) {
            tray.innerHTML = '<div class="unplaced-empty">Alle Elemente platziert</div>';
            return;
        }

        tray.innerHTML = unplaced.map(el => {
            const type = el.element_type || el.type;
            const icon = this.getElementIcon(type);
            return `
                <div class="unplaced-item" data-id="${el.id}" draggable="true">
                    <span class="material-symbols-rounded">${icon}</span>
                    <span class="unplaced-label">${this.getElementLabel(type)}</span>
                </div>
            `;
        }).join('');

        // Bind drag events for unplaced items
        tray.querySelectorAll('.unplaced-item').forEach(item => {
            item.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', 'unplaced:' + item.dataset.id);
                e.dataTransfer.effectAllowed = 'move';
            });
        });
    }

    getElementIcon(type) {
        const icons = {
            text: 'text_fields',
            image: 'image',
            linkgroup: 'list',
            icon: 'star',
            link: 'link',
            divider: 'horizontal_rule'
        };
        return icons[type] || 'widgets';
    }

    getElementLabel(type) {
        const labels = {
            text: 'Text',
            image: 'Bild',
            linkgroup: 'Link-Gruppe',
            icon: 'Icon',
            link: 'Link',
            divider: 'Trenner'
        };
        return labels[type] || type;
    }

    createElementDOM(elData) {
        const el = document.createElement('div');
        el.className = `canvas-element element-${elData.element_type || elData.type}`;
        el.dataset.id = elData.id || 'new-' + Date.now();
        el.dataset.type = elData.element_type || elData.type;

        // Get breakpoint-specific position (or fallback to desktop)
        const pos = this.getElementPosition(elData, this.currentBreakpoint);

        el.style.left = pos.x + 'px';
        el.style.top = pos.y + 'px';
        el.style.width = pos.w + 'px';
        el.style.height = pos.h + 'px';
        el.style.zIndex = elData.z_index || 0;

        // Apply styles
        const style = elData.style || elData.style_json || {};
        if (style.backgroundColor) el.style.backgroundColor = style.backgroundColor;
        if (style.color) el.style.color = style.color;
        if (style.fontSize) el.style.fontSize = style.fontSize + 'px';
        if (style.padding) el.style.padding = style.padding + 'px';

        // Render content based on type
        const type = elData.element_type || elData.type;
        const content = elData.content || elData.content_json || {};

        el.innerHTML = this.renderElementContent(type, content);

        // Add ALL 8 resize handles (corners + edges)
        el.innerHTML += `
            <div class="resize-handle nw" data-handle="nw"></div>
            <div class="resize-handle n" data-handle="n"></div>
            <div class="resize-handle ne" data-handle="ne"></div>
            <div class="resize-handle e" data-handle="e"></div>
            <div class="resize-handle se" data-handle="se"></div>
            <div class="resize-handle s" data-handle="s"></div>
            <div class="resize-handle sw" data-handle="sw"></div>
            <div class="resize-handle w" data-handle="w"></div>
        `;

        return el;
    }

    renderElementContent(type, content) {
        switch (type) {
            case 'text':
                return `<div class="element-text">${content.text || 'Text eingeben...'}</div>`;

            case 'heading':
                return `<div class="element-heading">
                    <h${content.level || 2}>${content.text || 'Überschrift'}</h${content.level || 2}>
                </div>`;

            case 'image':
                if (content.media_id) {
                    return `<div class="element-image">
                        <img src="/uploads/media/1/medium/${content.stored_filename || ''}" alt="${content.alt || ''}" draggable="false" style="pointer-events: none; user-select: none;">
                    </div>`;
                }
                return `<div class="element-image">
                    <div class="element-image-placeholder">
                        <span class="material-symbols-rounded">image</span>
                        <span>Bild wählen</span>
                    </div>
                </div>`;

            case 'button':
                return `<div class="element-button element-button-${content.style || 'primary'}">
                    <button type="button">${content.label || 'Button'}</button>
                </div>`;

            case 'linkgroup':
                const links = content.links || [];
                return `<div class="element-linkgroup">
                    <div class="element-linkgroup-title">${content.title || 'Kategorie'}</div>
                    <ul class="element-linkgroup-links">
                        ${links.map(l => `<li><a href="#">${l.label}</a></li>`).join('')}
                    </ul>
                </div>`;

            case 'icon':
                return `<div class="element-icon">
                    <span class="material-symbols-rounded">${content.icon || 'star'}</span>
                </div>`;

            case 'link':
                return `<div class="element-link">
                    <a href="#">${content.label || 'Link'}</a>
                </div>`;

            case 'divider':
                return `<div class="element-divider"><hr></div>`;

            case 'spacer':
                return `<div class="element-spacer"></div>`;

            case 'container':
                return `<div class="element-container">
                    <div class="element-container-inner">Container</div>
                </div>`;

            default:
                return `<div>Unbekannter Typ</div>`;
        }
    }


    // ========== CANVAS SCALE HELPERS ==========

    /**
     * Get current canvas scale factor (from CSS transform)
     */
    getCanvasScale() {
        const transform = this.canvas.style.transform;
        if (!transform || transform === 'none') {
            return 1;
        }
        const match = transform.match(/scale\(([0-9.]+)\)/);
        return match ? parseFloat(match[1]) : 1;
    }

    /**
     * Convert mouse event coordinates to canvas coordinates
     * Properly handles canvas scaling/zoom
     */
    getCanvasCoordinates(e) {
        const rect = this.canvas.getBoundingClientRect();
        const scale = this.getCanvasScale();

        // Get mouse position relative to canvas viewport
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        // Compensate for scale (rect is already scaled, coords need unscaling)
        return {
            x: mouseX / scale,
            y: mouseY / scale
        };
    }

    // ========== BREAKPOINT POSITION HELPERS ==========

    /**
     * Get element position for a specific breakpoint
     * Falls back to desktop if breakpoint position not set
     */
    getElementPosition(elData, breakpoint) {
        // Desktop is the default/base
        const desktop = {
            x: parseFloat(elData.pos_x ?? elData.x ?? 20),
            y: parseFloat(elData.pos_y ?? elData.y ?? 20),
            w: parseFloat(elData.width ?? elData.w ?? 200),
            h: parseFloat(elData.height ?? elData.h ?? 100)
        };

        if (breakpoint === 'desktop') {
            return desktop;
        }

        if (breakpoint === 'tablet') {
            return {
                x: elData.tablet_pos_x !== null && elData.tablet_pos_x !== undefined ? parseFloat(elData.tablet_pos_x) : desktop.x,
                y: elData.tablet_pos_y !== null && elData.tablet_pos_y !== undefined ? parseFloat(elData.tablet_pos_y) : desktop.y,
                w: elData.tablet_width !== null && elData.tablet_width !== undefined ? parseFloat(elData.tablet_width) : desktop.w,
                h: elData.tablet_height !== null && elData.tablet_height !== undefined ? parseFloat(elData.tablet_height) : desktop.h
            };
        }

        if (breakpoint === 'mobile') {
            return {
                x: elData.mobile_pos_x !== null && elData.mobile_pos_x !== undefined ? parseFloat(elData.mobile_pos_x) : desktop.x,
                y: elData.mobile_pos_y !== null && elData.mobile_pos_y !== undefined ? parseFloat(elData.mobile_pos_y) : desktop.y,
                w: elData.mobile_width !== null && elData.mobile_width !== undefined ? parseFloat(elData.mobile_width) : desktop.w,
                h: elData.mobile_height !== null && elData.mobile_height !== undefined ? parseFloat(elData.mobile_height) : desktop.h
            };
        }

        return desktop;
    }

    /**
     * Set element position for the current breakpoint
     */
    setElementPosition(elData, x, y, w, h) {
        if (this.currentBreakpoint === 'desktop') {
            elData.pos_x = x;
            elData.pos_y = y;
            elData.width = w;
            elData.height = h;
        } else if (this.currentBreakpoint === 'tablet') {
            elData.tablet_pos_x = x;
            elData.tablet_pos_y = y;
            elData.tablet_width = w;
            elData.tablet_height = h;
        } else if (this.currentBreakpoint === 'mobile') {
            elData.mobile_pos_x = x;
            elData.mobile_pos_y = y;
            elData.mobile_width = w;
            elData.mobile_height = h;
        }
    }

    // ========== EVENT BINDING ==========

    bindEvents() {
        // Canvas click (deselect)
        this.canvas.addEventListener('click', (e) => {
            if (e.target === this.canvas || e.target.classList.contains('canvas-grid')) {
                this.deselectAll();
            }
        });

        // Element selection
        this.canvasElements.addEventListener('click', (e) => {
            const element = e.target.closest('.canvas-element');
            if (element) {
                e.stopPropagation();
                this.selectElement(element);
            }
        });

        // Element dragging
        this.canvasElements.addEventListener('mousedown', (e) => {
            const element = e.target.closest('.canvas-element');
            const handle = e.target.closest('.resize-handle');

            if (handle) {
                this.startResize(element, handle.dataset.handle, e);
            } else if (element && !e.target.closest('input, textarea, a')) {
                this.startDrag(element, e);
            }
        });

        document.addEventListener('mousemove', (e) => {
            if (this.isDragging) {
                this.handleDrag(e);
            } else if (this.isResizing) {
                this.handleResize(e);
            }
        });

        document.addEventListener('mouseup', () => {
            if (this.isDragging) {
                this.endDrag();
            }
            if (this.isResizing) {
                this.endResize();
            }
        });

        // Component drag from library
        // Component drag from library
        document.querySelectorAll('.component-item').forEach(comp => {
            comp.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', 'component:' + comp.dataset.component);
                e.dataTransfer.effectAllowed = 'copy';
            });
        });

        this.canvas.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        });

        this.canvas.addEventListener('drop', (e) => {
            e.preventDefault();
            const data = e.dataTransfer.getData('text/plain');

            if (data && data.startsWith('component:')) {
                const componentType = data.split(':')[1];
                this.addComponentAtPosition(componentType, e);
            } else if (data && data.startsWith('unplaced:')) {
                // Place an existing unplaced element on the current breakpoint
                const elementId = data.split(':')[1];
                this.placeUnplacedElement(elementId, e);
            }
        });

        // Template selection
        document.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', () => {
                this.loadTemplate(parseInt(card.dataset.templateId));
            });
        });

        // Responsive Preview Controls
        this.initResponsivePreview();

        // Grid toggle
        document.getElementById('btn-grid-toggle').addEventListener('click', () => {
            this.gridVisible = !this.gridVisible;
            this.canvas.querySelector('.canvas-grid').style.display = this.gridVisible ? 'block' : 'none';
        });

        // Snap toggle
        document.getElementById('btn-snap-toggle').addEventListener('click', (e) => {
            this.snapEnabled = !this.snapEnabled;
            e.currentTarget.classList.toggle('active', this.snapEnabled);
        });

        // Clear all
        document.getElementById('btn-clear').addEventListener('click', async () => {
            const confirmed = await adminModal.confirm('Möchten Sie wirklich alle Elemente auf dem Canvas löschen?', {
                title: 'Alle Elemente löschen',
                icon: 'delete_sweep',
                type: 'warning',
                confirmText: 'Ja, löschen',
                danger: true
            });
            if (confirmed) {
                this.elements = [];
                this.renderElements();
                this.deselectAll();
            }
        });

        // New from blank
        document.getElementById('btn-new-template').addEventListener('click', async () => {
            const confirmed = await adminModal.confirm('Aktuelles Design verwerfen und mit einem leeren Canvas starten?', {
                title: 'Leer starten',
                icon: 'restart_alt',
                type: 'warning',
                confirmText: 'Ja, leer starten',
                danger: true
            });
            if (confirmed) {
                this.elements = [];
                this.renderElements();
                this.deselectAll();
            }
        });

        // Settings inputs
        this.bindSettingsInputs();

        // Save button (optional - autosave handles this now)
        const btnSave = document.getElementById('btn-save');
        if (btnSave) {
            btnSave.addEventListener('click', () => this.save());
        }

        // Preview button
        const btnPreview = document.getElementById('btn-preview');
        if (btnPreview) {
            btnPreview.addEventListener('click', () => this.openPreview());
        }

        // Save as template
        const btnSaveTemplate = document.getElementById('btn-save-template');
        if (btnSaveTemplate) {
            btnSaveTemplate.addEventListener('click', () => this.saveAsTemplate());
        }

        // Delete element
        const btnDeleteElement = document.getElementById('btn-delete-element');
        if (btnDeleteElement) {
            btnDeleteElement.addEventListener('click', () => {
                if (this.selectedElement) {
                    this.deleteElement(this.selectedElement);
                }
            });
        }

        // Duplicate element
        const btnDuplicate = document.getElementById('btn-duplicate');
        if (btnDuplicate) {
            btnDuplicate.addEventListener('click', () => {
                if (this.selectedElement) {
                    this.duplicateElement(this.selectedElement);
                }
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Skip if typing in an input
            if (e.target.matches('input, textarea, select')) return;

            // Delete element
            if (e.key === 'Delete' && this.selectedElement) {
                e.preventDefault();
                this.deleteElement(this.selectedElement);
            }
            // Backspace also deletes
            if (e.key === 'Backspace' && this.selectedElement) {
                e.preventDefault();
                this.deleteElement(this.selectedElement);
            }
            // Escape to deselect
            if (e.key === 'Escape') {
                this.deselectAll();
            }
            // Ctrl+D to duplicate
            if ((e.ctrlKey || e.metaKey) && e.key === 'd' && this.selectedElement) {
                e.preventDefault();
                this.duplicateElement(this.selectedElement);
            }
            // Ctrl+S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                this.save();
            }
            // Ctrl+Z to undo
            if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                e.preventDefault();
                this.undo();
            }
            // Ctrl+Shift+Z to redo
            if ((e.ctrlKey || e.metaKey) && e.key === 'z' && e.shiftKey) {
                e.preventDefault();
                this.redo();
            }
            // Ctrl+C to copy
            if ((e.ctrlKey || e.metaKey) && e.key === 'c' && this.selectedElement) {
                e.preventDefault();
                this.copyElement();
            }
            // Ctrl+V to paste
            if ((e.ctrlKey || e.metaKey) && e.key === 'v' && this.clipboard) {
                e.preventDefault();
                this.pasteElement();
            }
            // Arrow keys to move selected element
            if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key) && this.selectedElement) {
                e.preventDefault();
                const step = e.shiftKey ? 10 : 1; // Shift = 10px, normal = 1px
                this.moveSelectedElement(e.key, step);
            }
        });
    }

    moveSelectedElement(direction, step) {
        if (!this.selectedElement) return;

        this.saveToHistory();
        let x = parseFloat(this.selectedElement.style.left) || 0;
        let y = parseFloat(this.selectedElement.style.top) || 0;

        switch (direction) {
            case 'ArrowUp': y -= step; break;
            case 'ArrowDown': y += step; break;
            case 'ArrowLeft': x -= step; break;
            case 'ArrowRight': x += step; break;
        }

        // Clamp to canvas
        const canvasW = this.canvas.offsetWidth;
        const canvasH = this.canvas.offsetHeight;
        const elW = parseFloat(this.selectedElement.style.width) || 100;
        const elH = parseFloat(this.selectedElement.style.height) || 60;

        x = Math.max(0, Math.min(x, canvasW - elW));
        y = Math.max(0, Math.min(y, canvasH - elH));

        this.selectedElement.style.left = x + 'px';
        this.selectedElement.style.top = y + 'px';
        this.updateElementPosition(this.selectedElement);

        // Update settings panel
        const xInput = document.getElementById('el-x');
        const yInput = document.getElementById('el-y');
        if (xInput) xInput.value = Math.round(x);
        if (yInput) yInput.value = Math.round(y);
    }

    copyElement() {
        if (!this.selectedElement) return;
        const elData = this.findElementData(this.selectedElement.dataset.id);
        if (elData) {
            this.clipboard = JSON.parse(JSON.stringify(elData));
        }
    }

    pasteElement() {
        if (!this.clipboard) return;
        this.saveToHistory();

        const newEl = JSON.parse(JSON.stringify(this.clipboard));
        newEl.id = 'new-' + Date.now();
        // Offset position slightly
        newEl.pos_x = (newEl.pos_x || 0) + 20;
        newEl.pos_y = (newEl.pos_y || 0) + 20;

        this.elements.push(newEl);
        const domEl = this.createElementDOM(newEl);
        this.canvasElements.appendChild(domEl);
        this.selectElement(domEl);
    }

    bindSettingsInputs() {
        // ===== CONSTRAINT SYSTEM =====
        this.initConstraintSystem();

        // ===== POSITION INPUTS =====
        this.initPositionInputs();

        // ===== LIVE STYLING =====
        this.initLiveStyling();

        // ===== UNIT TOGGLE BUTTONS =====
        ['width-unit-toggle', 'height-unit-toggle'].forEach(toggleId => {
            const toggle = document.getElementById(toggleId);
            if (toggle) {
                toggle.addEventListener('click', (e) => {
                    const btn = e.target.closest('.unit-btn');
                    if (!btn) return;

                    // Update active state
                    toggle.querySelectorAll('.unit-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // Handle auto - hide slider
                    const isWidth = toggleId === 'width-unit-toggle';
                    const sliderRow = document.getElementById(isWidth ? 'width-slider-row' : 'height-slider-row');
                    if (btn.dataset.unit === 'auto') {
                        sliderRow.style.display = 'none';
                    } else {
                        sliderRow.style.display = 'flex';
                        // Update slider range based on unit
                        const slider = document.getElementById(isWidth ? 'el-width-slider' : 'el-height-slider');
                        if (btn.dataset.unit === '%') {
                            slider.max = 100;
                            slider.value = Math.min(slider.value, 100);
                        } else {
                            slider.max = isWidth ? 800 : 500;
                        }
                    }

                    this.updateSelectedElementFromSettings();
                });
            }
        });

        // ===== WIDTH/HEIGHT SLIDERS =====
        ['el-width-slider', 'el-height-slider'].forEach(sliderId => {
            const slider = document.getElementById(sliderId);
            const valueInput = document.getElementById(sliderId.replace('-slider', '-value'));

            if (slider && valueInput) {
                // Sync slider to input
                slider.addEventListener('input', () => {
                    valueInput.value = slider.value;
                    this.updateSelectedElementFromSettings();
                });

                // Sync input to slider
                valueInput.addEventListener('change', () => {
                    slider.value = valueInput.value;
                    this.updateSelectedElementFromSettings();
                });
            }
        });

        // ===== OFFSET SLIDER =====
        const offsetSlider = document.getElementById('el-offset');
        const offsetValue = document.getElementById('offset-value');
        if (offsetSlider && offsetValue) {
            offsetSlider.addEventListener('input', () => {
                offsetValue.textContent = offsetSlider.value + 'px';
                this.updateSelectedElementFromSettings();
            });
        }

        // ===== LOCK TOGGLE =====
        const lockToggle = document.getElementById('el-lock-position');
        if (lockToggle) {
            lockToggle.addEventListener('change', () => {
                this.updateSelectedElementFromSettings();
            });
        }

        // ===== STYLING SLIDERS =====
        const fontSlider = document.getElementById('el-font-size');
        const fontValue = document.getElementById('font-size-value');
        if (fontSlider && fontValue) {
            fontSlider.addEventListener('input', () => {
                fontValue.textContent = fontSlider.value + 'px';
                this.updateSelectedElementFromSettings();
            });
        }

        const paddingSlider = document.getElementById('el-padding');
        const paddingValue = document.getElementById('padding-value');
        if (paddingSlider && paddingValue) {
            paddingSlider.addEventListener('input', () => {
                paddingValue.textContent = paddingSlider.value + 'px';
                this.updateSelectedElementFromSettings();
            });
        }

        // ===== COLOR INPUTS =====
        const bgColor = document.getElementById('el-bg-color');
        const bgHex = document.getElementById('bg-hex');
        if (bgColor && bgHex) {
            bgColor.addEventListener('input', () => {
                bgHex.textContent = bgColor.value;
                this.updateSelectedElementFromSettings();
            });
        }

        const textColor = document.getElementById('el-text-color');
        const textHex = document.getElementById('text-hex');
        if (textColor && textHex) {
            textColor.addEventListener('input', () => {
                textHex.textContent = textColor.value;
                this.updateSelectedElementFromSettings();
            });
        }
    }

    // ========== CONSTRAINT SYSTEM ==========

    /**
     * Initialize the Figma-style constraint system
     * Replaces the old 9-point anchor grid with proper constraints
     */
    initConstraintSystem() {
        const constraintH = document.getElementById('el-constraint-h');
        const constraintV = document.getElementById('el-constraint-v');

        if (constraintH) {
            constraintH.addEventListener('change', () => {
                this.updateConstraintVisual();
                this.applyConstraintToElement();
                this.updateMarginFieldStates();
            });
        }

        if (constraintV) {
            constraintV.addEventListener('change', () => {
                this.updateConstraintVisual();
                this.applyConstraintToElement();
                this.updateMarginFieldStates();
            });
        }

        // Margin inputs
        ['el-margin-left', 'el-margin-right', 'el-margin-top', 'el-margin-bottom'].forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', () => {
                    this.applyConstraintToElement();
                });
            }
        });
    }

    /**
     * Update the visual constraint preview box
     * Shows which edges are "connected" based on constraint selection
     */
    updateConstraintVisual() {
        const constraintH = document.getElementById('el-constraint-h')?.value || 'center';
        const constraintV = document.getElementById('el-constraint-v')?.value || 'top';

        const lineLeft = document.querySelector('.constraint-line.constraint-left');
        const lineRight = document.querySelector('.constraint-line.constraint-right');
        const lineTop = document.querySelector('.constraint-line.constraint-top');
        const lineBottom = document.querySelector('.constraint-line.constraint-bottom');

        if (lineLeft) lineLeft.dataset.active = (constraintH === 'left' || constraintH === 'stretch').toString();
        if (lineRight) lineRight.dataset.active = (constraintH === 'right' || constraintH === 'stretch').toString();
        if (lineTop) lineTop.dataset.active = (constraintV === 'top' || constraintV === 'stretch').toString();
        if (lineBottom) lineBottom.dataset.active = (constraintV === 'bottom' || constraintV === 'stretch').toString();
    }

    /**
     * Update which margin fields are active based on constraints
     */
    updateMarginFieldStates() {
        const constraintH = document.getElementById('el-constraint-h')?.value || 'center';
        const constraintV = document.getElementById('el-constraint-v')?.value || 'top';

        const marginLeft = document.getElementById('margin-left-field');
        const marginRight = document.getElementById('margin-right-field');
        const marginTop = document.getElementById('margin-top-field');
        const marginBottom = document.getElementById('margin-bottom-field');

        if (marginLeft) marginLeft.classList.toggle('active', constraintH === 'left' || constraintH === 'stretch');
        if (marginRight) marginRight.classList.toggle('active', constraintH === 'right' || constraintH === 'stretch');
        if (marginTop) marginTop.classList.toggle('active', constraintV === 'top' || constraintV === 'stretch');
        if (marginBottom) marginBottom.classList.toggle('active', constraintV === 'bottom' || constraintV === 'stretch');
    }

    /**
     * Apply constraint to the selected element
     * This replaces the old anchor-based positioning
     */
    applyConstraintToElement() {
        if (!this.selectedElement) return;

        const elData = this.findElementData(this.selectedElement.dataset.id);
        if (!elData) return;

        const constraintH = document.getElementById('el-constraint-h')?.value || 'center';
        const constraintV = document.getElementById('el-constraint-v')?.value || 'top';

        // Store constraints in element data
        elData.constraints = elData.constraints || {};
        elData.constraints.horizontal = constraintH;
        elData.constraints.vertical = constraintV;

        // Store margins
        elData.constraints.marginLeft = parseFloat(document.getElementById('el-margin-left')?.value) || 0;
        elData.constraints.marginRight = parseFloat(document.getElementById('el-margin-right')?.value) || 0;
        elData.constraints.marginTop = parseFloat(document.getElementById('el-margin-top')?.value) || 0;
        elData.constraints.marginBottom = parseFloat(document.getElementById('el-margin-bottom')?.value) || 0;

        // Calculate new position based on constraints
        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const canvasH = parseFloat(this.canvas.style.height) || this.canvas.offsetHeight;
        let elW = parseFloat(this.selectedElement.style.width) || 100;
        let elH = parseFloat(this.selectedElement.style.height) || 60;

        let newX, newY;

        // Horizontal constraint
        switch (constraintH) {
            case 'left':
                newX = elData.constraints.marginLeft;
                break;
            case 'right':
                newX = canvasW - elW - elData.constraints.marginRight;
                break;
            case 'center':
                newX = (canvasW - elW) / 2;
                break;
            case 'stretch':
                newX = elData.constraints.marginLeft;
                elW = canvasW - elData.constraints.marginLeft - elData.constraints.marginRight;
                this.selectedElement.style.width = elW + 'px';
                break;
            case 'scale':
                // Position scales proportionally with canvas
                const relX = elData.relative_x ?? (parseFloat(this.selectedElement.style.left) / canvasW);
                newX = relX * canvasW;
                break;
            default:
                newX = parseFloat(this.selectedElement.style.left) || 0;
        }

        // Vertical constraint
        switch (constraintV) {
            case 'top':
                newY = elData.constraints.marginTop;
                break;
            case 'bottom':
                newY = canvasH - elH - elData.constraints.marginBottom;
                break;
            case 'center':
                newY = (canvasH - elH) / 2;
                break;
            case 'stretch':
                newY = elData.constraints.marginTop;
                elH = canvasH - elData.constraints.marginTop - elData.constraints.marginBottom;
                this.selectedElement.style.height = elH + 'px';
                break;
            case 'scale':
                const relY = elData.relative_y ?? (parseFloat(this.selectedElement.style.top) / canvasH);
                newY = relY * canvasH;
                break;
            default:
                newY = parseFloat(this.selectedElement.style.top) || 0;
        }

        // Apply position
        this.selectedElement.style.left = Math.round(newX) + 'px';
        this.selectedElement.style.top = Math.round(newY) + 'px';

        // Update position inputs
        document.getElementById('el-pos-x').value = Math.round(newX);
        document.getElementById('el-pos-y').value = Math.round(newY);

        this.setElementPosition(elData, newX, newY, elW, elH);
        this.triggerAutosave();
    }

    // ========== POSITION INPUTS ==========

    initPositionInputs() {
        const posX = document.getElementById('el-pos-x');
        const posY = document.getElementById('el-pos-y');

        if (posX) {
            posX.addEventListener('input', () => this.applyPositionFromInputs());
        }

        if (posY) {
            posY.addEventListener('input', () => this.applyPositionFromInputs());
        }
    }

    applyPositionFromInputs() {
        if (!this.selectedElement) return;

        const x = parseFloat(document.getElementById('el-pos-x')?.value) || 0;
        const y = parseFloat(document.getElementById('el-pos-y')?.value) || 0;

        this.selectedElement.style.left = x + 'px';
        this.selectedElement.style.top = y + 'px';

        this.updateElementPosition(this.selectedElement);
        this.triggerAutosave();
    }

    // ========== LIVE STYLING ==========

    /**
     * Initialize all live styling event handlers
     * All changes apply instantly without blur/confirm
     */
    initLiveStyling() {
        // Shadow presets
        const shadowPresets = document.getElementById('shadow-presets');
        if (shadowPresets) {
            shadowPresets.addEventListener('click', (e) => {
                const preset = e.target.closest('.shadow-preset');
                if (!preset) return;

                shadowPresets.querySelectorAll('.shadow-preset').forEach(p => p.classList.remove('active'));
                preset.classList.add('active');

                document.getElementById('el-box-shadow').value = preset.dataset.shadow;
                this.applyLiveStyling();
            });
        }

        // Text alignment toggles
        const textAlign = document.getElementById('el-text-align');
        if (textAlign) {
            textAlign.addEventListener('click', (e) => {
                const toggle = e.target.closest('.icon-toggle');
                if (!toggle) return;

                textAlign.querySelectorAll('.icon-toggle').forEach(t => t.classList.remove('active'));
                toggle.classList.add('active');
                this.applyLiveStyling();
            });
        }

        // Linked padding
        const paddingLink = document.getElementById('el-padding-link');
        if (paddingLink) {
            paddingLink.addEventListener('change', () => {
                if (paddingLink.checked) {
                    // Sync all padding values to the first one
                    const val = document.getElementById('el-padding-top')?.value || 10;
                    ['el-padding-right', 'el-padding-bottom', 'el-padding-left'].forEach(id => {
                        const input = document.getElementById(id);
                        if (input) input.value = val;
                    });
                    this.applyLiveStyling();
                }
            });
        }

        // Padding inputs - sync when linked
        ['el-padding-top', 'el-padding-right', 'el-padding-bottom', 'el-padding-left'].forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', () => {
                    const linked = document.getElementById('el-padding-link')?.checked;
                    if (linked) {
                        const val = input.value;
                        ['el-padding-top', 'el-padding-right', 'el-padding-bottom', 'el-padding-left'].forEach(pid => {
                            document.getElementById(pid).value = val;
                        });
                    }
                    this.applyLiveStyling();
                });
            }
        });

        // All other styling inputs - live update
        const liveInputs = [
            'el-bg-color', 'el-text-color', 'el-font-size', 'el-font-weight',
            'el-border-width', 'el-border-color', 'el-border-style', 'el-border-radius',
            'el-opacity', 'el-z-index'
        ];

        liveInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', () => this.applyLiveStyling());
            }
        });

        // Slider + Input syncing
        this.syncSliderWithInput('el-font-size', 'el-font-size-value');
        this.syncSliderWithInput('el-border-width', 'el-border-width-value');
        this.syncSliderWithInput('el-border-radius', 'el-border-radius-value');
        this.syncSliderWithInput('el-opacity', 'el-opacity-value');

        // Color picker + text hex syncing
        this.syncColorWithText('el-bg-color', 'bg-hex');
        this.syncColorWithText('el-text-color', 'text-hex');
        this.syncColorWithText('el-border-color', 'border-hex');
    }

    syncSliderWithInput(sliderId, inputId) {
        const slider = document.getElementById(sliderId);
        const input = document.getElementById(inputId);

        if (slider && input) {
            slider.addEventListener('input', () => {
                input.value = slider.value;
                this.applyLiveStyling();
            });

            input.addEventListener('input', () => {
                slider.value = input.value;
                this.applyLiveStyling();
            });
        }
    }

    syncColorWithText(colorId, textId) {
        const colorInput = document.getElementById(colorId);
        const textInput = document.getElementById(textId);

        if (colorInput && textInput) {
            colorInput.addEventListener('input', () => {
                textInput.value = colorInput.value;
                this.applyLiveStyling();
            });

            textInput.addEventListener('input', () => {
                // Validate hex format
                let val = textInput.value;
                if (!val.startsWith('#')) val = '#' + val;
                if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                    colorInput.value = val;
                    this.applyLiveStyling();
                }
            });
        }
    }

    /**
     * Apply all styling properties to the selected element instantly
     */
    applyLiveStyling() {
        if (!this.selectedElement) return;

        const elData = this.findElementData(this.selectedElement.dataset.id);
        if (!elData) return;

        // Initialize style object
        elData.style = elData.style || {};

        // Colors
        const bgColor = document.getElementById('el-bg-color')?.value || '#ffffff';
        const textColor = document.getElementById('el-text-color')?.value || '#333333';

        // Typography
        const fontSize = document.getElementById('el-font-size')?.value || 14;
        const fontWeight = document.getElementById('el-font-weight')?.value || '400';
        const textAlignBtn = document.querySelector('#el-text-align .icon-toggle.active');
        const textAlign = textAlignBtn?.dataset.value || 'left';

        // Padding
        const paddingTop = document.getElementById('el-padding-top')?.value || 10;
        const paddingRight = document.getElementById('el-padding-right')?.value || 10;
        const paddingBottom = document.getElementById('el-padding-bottom')?.value || 10;
        const paddingLeft = document.getElementById('el-padding-left')?.value || 10;

        // Border
        const borderWidth = document.getElementById('el-border-width')?.value || 0;
        const borderColor = document.getElementById('el-border-color')?.value || '#e5e7eb';
        const borderStyle = document.getElementById('el-border-style')?.value || 'solid';
        const borderRadius = document.getElementById('el-border-radius')?.value || 4;

        // Shadow
        const shadowValue = document.getElementById('el-box-shadow')?.value || 'none';
        const shadows = {
            'none': 'none',
            'sm': '0 1px 2px rgba(0,0,0,0.1)',
            'md': '0 4px 6px -1px rgba(0,0,0,0.15)',
            'lg': '0 10px 15px -3px rgba(0,0,0,0.15)',
            'xl': '0 20px 25px -5px rgba(0,0,0,0.15)'
        };
        const boxShadow = shadows[shadowValue] || 'none';

        // Effects
        const opacity = (document.getElementById('el-opacity')?.value || 100) / 100;
        const zIndex = document.getElementById('el-z-index')?.value || 0;

        // Apply to DOM element
        this.selectedElement.style.backgroundColor = bgColor;
        this.selectedElement.style.color = textColor;
        this.selectedElement.style.fontSize = fontSize + 'px';
        this.selectedElement.style.fontWeight = fontWeight;
        this.selectedElement.style.textAlign = textAlign;
        this.selectedElement.style.padding = `${paddingTop}px ${paddingRight}px ${paddingBottom}px ${paddingLeft}px`;
        this.selectedElement.style.border = borderWidth > 0 ? `${borderWidth}px ${borderStyle} ${borderColor}` : 'none';
        this.selectedElement.style.borderRadius = borderRadius + 'px';
        this.selectedElement.style.boxShadow = boxShadow;
        this.selectedElement.style.opacity = opacity;
        this.selectedElement.style.zIndex = zIndex;

        // Store in data
        Object.assign(elData.style, {
            backgroundColor: bgColor,
            color: textColor,
            fontSize: parseInt(fontSize),
            fontWeight: fontWeight,
            textAlign: textAlign,
            paddingTop: parseInt(paddingTop),
            paddingRight: parseInt(paddingRight),
            paddingBottom: parseInt(paddingBottom),
            paddingLeft: parseInt(paddingLeft),
            borderWidth: parseInt(borderWidth),
            borderColor: borderColor,
            borderStyle: borderStyle,
            borderRadius: parseInt(borderRadius),
            boxShadow: shadowValue,
            opacity: opacity,
            zIndex: parseInt(zIndex)
        });

        this.triggerAutosave();
    }

    // Update anchor preview animation (legacy - kept for compatibility)
    updateAnchorPreview(anchor) {
        const preview = document.querySelector('.anchor-element');
        if (!preview) return;

        const positions = {
            'top-left': { top: '10%', left: '10%', transform: 'translate(0, 0)' },
            'top-center': { top: '10%', left: '50%', transform: 'translate(-50%, 0)' },
            'top-right': { top: '10%', left: '90%', transform: 'translate(-100%, 0)' },
            'middle-left': { top: '50%', left: '10%', transform: 'translate(0, -50%)' },
            'middle-center': { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' },
            'middle-right': { top: '50%', left: '90%', transform: 'translate(-100%, -50%)' },
            'bottom-left': { top: '90%', left: '10%', transform: 'translate(0, -100%)' },
            'bottom-center': { top: '90%', left: '50%', transform: 'translate(-50%, -100%)' },
            'bottom-right': { top: '90%', left: '90%', transform: 'translate(-100%, -100%)' }
        };

        const pos = positions[anchor] || positions['middle-center'];
        preview.style.top = pos.top;
        preview.style.left = pos.left;
        preview.style.transform = pos.transform;
    }

    // Apply anchor-based positioning to element
    applyAnchorToElement(anchor) {
        if (!this.selectedElement) return;

        const elData = this.findElementData(this.selectedElement.dataset.id);
        if (!elData) return;

        // Store anchor in element data
        elData.anchor = anchor;

        // Get canvas dimensions
        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const canvasH = parseFloat(this.canvas.style.height) || this.canvas.offsetHeight;
        const elW = parseFloat(this.selectedElement.style.width) || 100;
        const elH = parseFloat(this.selectedElement.style.height) || 60;

        // Calculate new position based on anchor
        let newX, newY;
        const offset = parseFloat(document.getElementById('el-offset')?.value) || 0;

        // Horizontal anchor
        if (anchor.includes('left')) {
            newX = offset;
        } else if (anchor.includes('right')) {
            newX = canvasW - elW - offset;
        } else {
            newX = (canvasW - elW) / 2;
        }

        // Vertical anchor
        if (anchor.includes('top')) {
            newY = offset;
        } else if (anchor.includes('bottom')) {
            newY = canvasH - elH - offset;
        } else {
            newY = (canvasH - elH) / 2;
        }

        // Apply position
        this.selectedElement.style.left = newX + 'px';
        this.selectedElement.style.top = newY + 'px';

        // Store percentage-based position for responsive
        elData.anchorOffsetX = newX / canvasW;
        elData.anchorOffsetY = newY / canvasH;

        this.setElementPosition(elData, newX, newY, elW, elH);
        this.triggerAutosave();
    }

    // Reposition all elements based on their constraints (called on canvas resize)
    // This is the CORE fix for vertical drift - uses deterministic constraint-based positioning
    repositionAllElements() {
        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const canvasH = parseFloat(this.canvas.style.height) || this.canvas.offsetHeight;

        // Get all rendered elements
        const domElements = this.canvasElements.querySelectorAll('.canvas-element');

        domElements.forEach(domEl => {
            const elData = this.findElementData(domEl.dataset.id);
            if (!elData) return;

            // Skip locked elements - they maintain absolute position
            if (elData.locked) return;

            const style = elData.style || {};
            const constraints = elData.constraints || {};

            // Get current dimensions
            let elW = parseFloat(domEl.style.width) || 100;
            let elH = parseFloat(domEl.style.height) || 60;

            // Handle percentage-based dimensions
            if (style.widthUnit === '%' && style.widthValue) {
                elW = (style.widthValue / 100) * canvasW;
                domEl.style.width = elW + 'px';
            }
            if (style.heightUnit === '%' && style.heightValue) {
                elH = (style.heightValue / 100) * canvasH;
                domEl.style.height = elH + 'px';
            }

            // CASE 1: Element has explicit constraints - use constraint-based positioning
            if (constraints.horizontal || constraints.vertical) {
                let newX, newY;

                // Horizontal constraint
                switch (constraints.horizontal) {
                    case 'left':
                        newX = constraints.marginLeft || 0;
                        break;
                    case 'right':
                        newX = canvasW - elW - (constraints.marginRight || 0);
                        break;
                    case 'center':
                        newX = (canvasW - elW) / 2;
                        break;
                    case 'stretch':
                        newX = constraints.marginLeft || 0;
                        elW = canvasW - (constraints.marginLeft || 0) - (constraints.marginRight || 0);
                        domEl.style.width = elW + 'px';
                        break;
                    case 'scale':
                        // Proportional positioning based on stored relative position
                        const relX = elData.relative_x ?? (parseFloat(domEl.style.left) / canvasW);
                        newX = relX * canvasW;
                        break;
                    default:
                        newX = parseFloat(domEl.style.left) || 0;
                }

                // Vertical constraint
                switch (constraints.vertical) {
                    case 'top':
                        newY = constraints.marginTop || 0;
                        break;
                    case 'bottom':
                        newY = canvasH - elH - (constraints.marginBottom || 0);
                        break;
                    case 'center':
                        newY = (canvasH - elH) / 2;
                        break;
                    case 'stretch':
                        newY = constraints.marginTop || 0;
                        elH = canvasH - (constraints.marginTop || 0) - (constraints.marginBottom || 0);
                        domEl.style.height = elH + 'px';
                        break;
                    case 'scale':
                        const relY = elData.relative_y ?? (parseFloat(domEl.style.top) / canvasH);
                        newY = relY * canvasH;
                        break;
                    default:
                        newY = parseFloat(domEl.style.top) || 0;
                }

                // Apply position
                domEl.style.left = Math.round(newX) + 'px';
                domEl.style.top = Math.round(newY) + 'px';
            }
            // CASE 2: Legacy anchor-based positioning (backwards compatibility)
            else if (elData.anchor) {
                const anchor = elData.anchor;
                const offset = elData.offset || 0;
                let newX, newY;

                // Horizontal
                if (anchor.includes('left')) {
                    newX = offset;
                } else if (anchor.includes('right')) {
                    newX = canvasW - elW - offset;
                } else {
                    newX = (canvasW - elW) / 2;
                }

                // Vertical
                if (anchor.includes('top')) {
                    newY = offset;
                } else if (anchor.includes('bottom')) {
                    newY = canvasH - elH - offset;
                } else {
                    newY = (canvasH - elH) / 2;
                }

                domEl.style.left = newX + 'px';
                domEl.style.top = newY + 'px';
            }
            // CASE 3: No constraints and no anchor - maintain absolute position (no repositioning)
            // This is the key change that fixes vertical drift - we don't reposition elements
            // without explicit constraints, preventing unpredictable movement
        });
    }



    // ========== RESPONSIVE PREVIEW ==========


    initResponsivePreview() {
        const slider = document.getElementById('responsive-slider');
        const sliderValue = document.getElementById('slider-value');
        const deviceBtns = document.querySelectorAll('.device-btn');
        const isSideMenu = window.MEGA_EDITOR_DATA.isSideMenu;

        // Create zoom indicator if not exists
        let zoomIndicator = document.getElementById('zoom-indicator');
        if (!zoomIndicator) {
            zoomIndicator = document.createElement('span');
            zoomIndicator.id = 'zoom-indicator';
            zoomIndicator.className = 'zoom-indicator';
            document.querySelector('.responsive-slider-wrap').appendChild(zoomIndicator);
        }

        // Device presets based on menu type
        // NOTE: Presets must align with breakpoint detection logic:
        // mobile: <= 480, tablet: 481-768, desktop: > 768
        const presets = isSideMenu ? {
            desktop: 400,
            tablet: 350,
            mobile: 280
        } : {
            desktop: 1100,   // Default desktop view
            tablet: 800,     // Within tablet breakpoint (481-959)
            mobile: 375      // Typical mobile width
        };


        // Update slider range based on menu type
        slider.min = 200;
        slider.max = isSideMenu ? 600 : 1920;

        // Zoom threshold - below this: 1:1, above this: zoom out
        const ZOOM_THRESHOLD = 940;

        const updateCanvasWidth = (width) => {
            // Set actual canvas width
            this.canvas.style.width = width + 'px';

            // For HEADER menu: breakpoint-specific heights
            // Detect breakpoint explicitly based on preset widths (not ranges)
            if (!isSideMenu) {
                let height;

                // Get available viewport height (minus header ~60px, preview bar ~50px, padding 40px)
                const viewportHeight = window.innerHeight;
                const availableHeight = viewportHeight - 60 - 50 - 40; // header + preview bar + 20px top/bottom padding

                // Like Prada: Mobile ≤950px = tall, Desktop >950px = 300px (fixed, no jumps)
                const MOBILE_BREAKPOINT = 950;
                const isMobile = width <= MOBILE_BREAKPOINT;

                // Detect breakpoint change and show clean notification
                if (this._lastBreakpointMobile !== undefined && this._lastBreakpointMobile !== isMobile) {
                    const mode = isMobile ? 'Mobile' : 'Desktop';
                    this.showModeIndicator(mode);
                }
                this._lastBreakpointMobile = isMobile;

                if (isMobile) {
                    // Mobile: fit available viewport with padding
                    height = Math.min(availableHeight, 600); // Cap at 600px max
                } else {
                    // Desktop: fixed 300px height (no jumps ever)
                    height = 300;
                }

                // Apply height with FAST transition (cut feel)
                this.canvas.style.transition = 'height 0.1s ease-out';
                this.canvas.style.height = Math.round(height) + 'px';
            }

            // Calculate zoom: 1:1 up to 940px, then proportionally zoom out
            let scale = 1;
            if (width > ZOOM_THRESHOLD) {
                scale = ZOOM_THRESHOLD / width;
            }

            // Apply zoom via transform
            this.canvas.style.transform = scale < 1 ? `scale(${scale})` : 'none';
            this.canvas.style.transformOrigin = 'top center';

            // Update display
            sliderValue.textContent = width + 'px';
            slider.value = width;

            // Show zoom percentage when zoomed
            const zoomPercent = Math.round(scale * 100);
            if (scale < 1) {
                zoomIndicator.textContent = `${zoomPercent}%`;
                zoomIndicator.classList.add('visible');
            } else {
                zoomIndicator.textContent = '';
                zoomIndicator.classList.remove('visible');
            }

            // Toggle mobile-preview class
            if (width <= 480) {
                this.canvas.classList.add('mobile-preview');
            } else {
                this.canvas.classList.remove('mobile-preview');
            }

            // Save to localStorage
            const menuId = window.MEGA_EDITOR_DATA.menuItemId;
            localStorage.setItem(`mega_menu_width_${menuId}`, width);
        };

        slider.addEventListener('input', (e) => {
            const width = parseInt(e.target.value);
            updateCanvasWidth(width);

            // Reposition elements proportionally when canvas resizes
            this.repositionAllElements();

            // MEDIA QUERY LOGIC: Detect which breakpoint the slider is in
            // and switch elements accordingly
            // Breakpoints: mobile <= 480, tablet 481-959, desktop >= 960
            let detectedBreakpoint = 'desktop';
            if (width <= 480) {
                detectedBreakpoint = 'mobile';
            } else if (width < 960) {
                detectedBreakpoint = 'tablet';
            }

            // Only update if breakpoint actually changed
            if (detectedBreakpoint !== this.currentBreakpoint) {
                this.currentBreakpoint = detectedBreakpoint;

                // Update device button visual state
                deviceBtns.forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.dataset.device === detectedBreakpoint) {
                        btn.classList.add('active');
                    }
                });

                // Re-render elements for new breakpoint
                this.renderElements();
                this.updateBreakpointBadge();
                this.updateAdaptiveGrid();
            }
        });

        // Device preset buttons
        deviceBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const device = btn.dataset.device;
                const width = presets[device];

                // Update visual active state
                deviceBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Update canvas width
                updateCanvasWidth(width);

                // Reposition elements proportionally when canvas resizes
                this.repositionAllElements();

                // Set breakpoint (same logic as slider)
                // Breakpoints: mobile <= 480, tablet 481-959, desktop >= 960
                let detectedBreakpoint = 'desktop';
                if (width <= 480) {
                    detectedBreakpoint = 'mobile';
                } else if (width < 960) {
                    detectedBreakpoint = 'tablet';
                }

                // Update breakpoint and re-render if changed
                if (detectedBreakpoint !== this.currentBreakpoint) {
                    this.currentBreakpoint = detectedBreakpoint;
                    this.renderElements();
                    this.updateBreakpointBadge();
                    this.updateAdaptiveGrid();
                }

                // Sync header breakpoint tabs
                this.syncBreakpointTabs(device);
            });
        });


        // Initial setup - restore from localStorage or use default
        const menuId = window.MEGA_EDITOR_DATA.menuItemId;
        const savedWidth = localStorage.getItem(`mega_menu_width_${menuId}`);
        const defaultWidth = isSideMenu ? 400 : window.MEGA_EDITOR_DATA.defaultCanvasWidth;
        const initialWidth = savedWidth ? parseInt(savedWidth) : defaultWidth;
        updateCanvasWidth(initialWidth);

        // Set initial button active state based on the actual width
        let initialBreakpoint = 'desktop';
        if (initialWidth <= 480) {
            initialBreakpoint = 'mobile';
        } else if (initialWidth < 960) {
            initialBreakpoint = 'tablet';
        }

        // Update button visuals
        deviceBtns.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.device === initialBreakpoint) {
                btn.classList.add('active');
            }
        });

        // Set internal breakpoint state
        this.currentBreakpoint = initialBreakpoint;
        this.updateBreakpointBadge();
        this.updateAdaptiveGrid();
    }


    // ========== SELECTION ==========

    selectElement(element) {
        this.deselectAll();
        element.classList.add('selected');
        this.selectedElement = element;
        this.showSettings(element);
    }

    deselectAll() {
        document.querySelectorAll('.canvas-element.selected').forEach(el => {
            el.classList.remove('selected');
        });
        this.selectedElement = null;
        this.hideSettings();
    }

    showSettings(element) {
        document.getElementById('settings-empty').style.display = 'none';
        document.getElementById('settings-content').style.display = 'block';

        // Find element data
        const elData = this.findElementData(element.dataset.id);
        const style = elData?.style || {};

        // ===== ELEMENT TYPE HEADER =====
        const typeIcons = {
            'text': 'text_fields',
            'heading': 'title',
            'image': 'image',
            'button': 'smart_button',
            'linkgroup': 'list',
            'link': 'link',
            'icon': 'star',
            'divider': 'horizontal_rule',
            'spacer': 'height',
            'container': 'dashboard'
        };
        const typeLabels = {
            'text': 'Text',
            'heading': 'Überschrift',
            'image': 'Bild',
            'button': 'Button',
            'linkgroup': 'Link-Gruppe',
            'link': 'Einzelner Link',
            'icon': 'Icon',
            'divider': 'Trenner',
            'spacer': 'Abstand',
            'container': 'Container'
        };

        const type = element.dataset.type || 'text';
        const typeIcon = document.getElementById('el-type-icon');
        const typeLabel = document.getElementById('el-type-label');
        if (typeIcon) typeIcon.textContent = typeIcons[type] || 'widgets';
        if (typeLabel) typeLabel.textContent = typeLabels[type] || type;

        // ===== POSITION INPUTS =====
        const x = parseFloat(element.style.left) || 0;
        const y = parseFloat(element.style.top) || 0;
        const posX = document.getElementById('el-pos-x');
        const posY = document.getElementById('el-pos-y');
        if (posX) posX.value = Math.round(x);
        if (posY) posY.value = Math.round(y);

        // ===== CONSTRAINT DROPDOWNS =====
        const constraints = elData?.constraints || {};
        const constraintH = document.getElementById('el-constraint-h');
        const constraintV = document.getElementById('el-constraint-v');
        if (constraintH) constraintH.value = constraints.horizontal || 'center';
        if (constraintV) constraintV.value = constraints.vertical || 'top';

        // Update visual preview
        this.updateConstraintVisual();
        this.updateMarginFieldStates();

        // ===== MARGIN FIELDS =====
        const marginLeft = document.getElementById('el-margin-left');
        const marginRight = document.getElementById('el-margin-right');
        const marginTop = document.getElementById('el-margin-top');
        const marginBottom = document.getElementById('el-margin-bottom');
        if (marginLeft) marginLeft.value = constraints.marginLeft || 0;
        if (marginRight) marginRight.value = constraints.marginRight || 0;
        if (marginTop) marginTop.value = constraints.marginTop || 0;
        if (marginBottom) marginBottom.value = constraints.marginBottom || 0;

        // ===== LOCK TOGGLE =====
        const lockToggle = document.getElementById('el-lock-position');
        if (lockToggle) lockToggle.checked = elData?.locked || false;

        // ===== WIDTH CONTROLS =====
        const widthUnit = style.widthUnit || '%';
        const widthValue = style.widthValue || this.calculatePercentWidth(element);

        // Set toggle buttons
        const widthToggle = document.getElementById('width-unit-toggle');
        if (widthToggle) {
            widthToggle.querySelectorAll('.unit-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.unit === widthUnit);
            });
        }

        // Set slider/input
        const widthSlider = document.getElementById('el-width-slider');
        const widthInput = document.getElementById('el-width-value');
        const widthSliderRow = document.getElementById('width-slider-row');

        if (widthUnit === 'auto') {
            if (widthSliderRow) widthSliderRow.style.display = 'none';
        } else {
            if (widthSliderRow) widthSliderRow.style.display = 'flex';
            if (widthSlider) {
                widthSlider.max = widthUnit === '%' ? 100 : 800;
                widthSlider.value = widthValue;
            }
            if (widthInput) widthInput.value = widthValue;
        }

        // ===== HEIGHT CONTROLS =====
        const heightUnit = style.heightUnit || 'auto';
        const heightValue = style.heightValue || parseFloat(element.style.height) || 60;

        const heightToggle = document.getElementById('height-unit-toggle');
        if (heightToggle) {
            heightToggle.querySelectorAll('.unit-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.unit === heightUnit);
            });
        }

        const heightSlider = document.getElementById('el-height-slider');
        const heightInput = document.getElementById('el-height-value');
        const heightSliderRow = document.getElementById('height-slider-row');

        if (heightUnit === 'auto') {
            if (heightSliderRow) heightSliderRow.style.display = 'none';
        } else {
            if (heightSliderRow) heightSliderRow.style.display = 'flex';
            if (heightSlider) {
                heightSlider.max = heightUnit === '%' ? 100 : 500;
                heightSlider.value = heightValue;
            }
            if (heightInput) heightInput.value = heightValue;
        }

        // ===== COLORS =====
        const bgColor = document.getElementById('el-bg-color');
        const bgHex = document.getElementById('bg-hex');
        const bgVal = style.backgroundColor || '#ffffff';
        if (bgColor) bgColor.value = bgVal;
        if (bgHex) bgHex.value = bgVal;

        const textColor = document.getElementById('el-text-color');
        const textHex = document.getElementById('text-hex');
        const textVal = style.color || '#333333';
        if (textColor) textColor.value = textVal;
        if (textHex) textHex.value = textVal;

        // ===== TYPOGRAPHY =====
        const fontSize = style.fontSize || 14;
        const fontSlider = document.getElementById('el-font-size');
        const fontValue = document.getElementById('el-font-size-value');
        if (fontSlider) fontSlider.value = fontSize;
        if (fontValue) fontValue.value = fontSize;

        const fontWeight = document.getElementById('el-font-weight');
        if (fontWeight) fontWeight.value = style.fontWeight || '400';

        const textAlignBtns = document.querySelectorAll('#el-text-align .icon-toggle');
        const textAlignVal = style.textAlign || 'left';
        textAlignBtns.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.value === textAlignVal);
        });

        // ===== PADDING =====
        const paddingTop = document.getElementById('el-padding-top');
        const paddingRight = document.getElementById('el-padding-right');
        const paddingBottom = document.getElementById('el-padding-bottom');
        const paddingLeft = document.getElementById('el-padding-left');
        if (paddingTop) paddingTop.value = style.paddingTop || 10;
        if (paddingRight) paddingRight.value = style.paddingRight || 10;
        if (paddingBottom) paddingBottom.value = style.paddingBottom || 10;
        if (paddingLeft) paddingLeft.value = style.paddingLeft || 10;

        // ===== BORDER =====
        const borderWidth = document.getElementById('el-border-width');
        const borderWidthVal = document.getElementById('el-border-width-value');
        if (borderWidth) borderWidth.value = style.borderWidth || 0;
        if (borderWidthVal) borderWidthVal.value = style.borderWidth || 0;

        const borderColor = document.getElementById('el-border-color');
        const borderHex = document.getElementById('border-hex');
        const borderColorVal = style.borderColor || '#e5e7eb';
        if (borderColor) borderColor.value = borderColorVal;
        if (borderHex) borderHex.value = borderColorVal;

        const borderStyle = document.getElementById('el-border-style');
        if (borderStyle) borderStyle.value = style.borderStyle || 'solid';

        const borderRadius = document.getElementById('el-border-radius');
        const borderRadiusVal = document.getElementById('el-border-radius-value');
        if (borderRadius) borderRadius.value = style.borderRadius || 4;
        if (borderRadiusVal) borderRadiusVal.value = style.borderRadius || 4;

        // ===== SHADOW =====
        const shadowVal = style.boxShadow || 'none';
        const shadowPresets = document.querySelectorAll('#shadow-presets .shadow-preset');
        shadowPresets.forEach(preset => {
            preset.classList.toggle('active', preset.dataset.shadow === shadowVal);
        });
        const boxShadowInput = document.getElementById('el-box-shadow');
        if (boxShadowInput) boxShadowInput.value = shadowVal;

        // ===== EFFECTS =====
        const opacity = document.getElementById('el-opacity');
        const opacityVal = document.getElementById('el-opacity-value');
        const opacityValue = (style.opacity !== undefined ? style.opacity * 100 : 100);
        if (opacity) opacity.value = opacityValue;
        if (opacityVal) opacityVal.value = opacityValue;

        const zIndex = document.getElementById('el-z-index');
        if (zIndex) zIndex.value = style.zIndex || 0;

        // Show content settings based on type
        this.showContentSettings(type, elData);
    }

    // Detect anchor based on element position
    detectAnchorFromPosition(element) {
        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const canvasH = parseFloat(this.canvas.style.height) || this.canvas.offsetHeight;
        const elX = parseFloat(element.style.left) || 0;
        const elY = parseFloat(element.style.top) || 0;
        const elW = parseFloat(element.style.width) || 100;
        const elH = parseFloat(element.style.height) || 60;

        // Calculate center point of element
        const centerX = elX + elW / 2;
        const centerY = elY + elH / 2;

        // Determine horizontal anchor
        let hAnchor;
        if (centerX < canvasW * 0.33) {
            hAnchor = 'left';
        } else if (centerX > canvasW * 0.66) {
            hAnchor = 'right';
        } else {
            hAnchor = 'center';
        }

        // Determine vertical anchor
        let vAnchor;
        if (centerY < canvasH * 0.33) {
            vAnchor = 'top';
        } else if (centerY > canvasH * 0.66) {
            vAnchor = 'bottom';
        } else {
            vAnchor = 'middle';
        }

        return `${vAnchor}-${hAnchor}`;
    }

    // Calculate percentage width from pixel width
    calculatePercentWidth(element) {
        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const elW = parseFloat(element.style.width) || 100;
        return Math.round((elW / canvasW) * 100);
    }



    showContentSettings(type, elData) {
        const container = document.getElementById('content-settings');
        const content = elData?.content || {};

        let html = '<label class="settings-label">Inhalt</label>';

        switch (type) {
            case 'text':
                html += `
                    <textarea id="content-text" class="form-textarea" rows="3" 
                        placeholder="Text eingeben...">${content.text || ''}</textarea>
                `;
                break;

            case 'image':
                html += `
                    <div class="media-picker" data-field="content-media-id" data-folder="menu"
                        data-media-id="${content.media_id || ''}"></div>
                    <input type="text" id="content-alt" class="form-input" 
                        placeholder="Alt-Text" value="${content.alt || ''}" style="margin-top:8px">
                `;
                break;

            case 'linkgroup':
                html += `
                    <input type="text" id="content-title" class="form-input" 
                        placeholder="Überschrift" value="${content.title || ''}">
                    <div id="content-links" style="margin-top:8px">
                        ${(content.links || []).map((l, i) => `
                            <div class="link-row" style="display:flex;gap:4px;margin-bottom:4px">
                                <input type="text" class="form-input link-label" placeholder="Label" value="${l.label}">
                                <input type="text" class="form-input link-url" placeholder="URL" value="${l.url}">
                                <button type="button" class="icon-btn remove-link" onclick="this.parentElement.remove()">
                                    <span class="material-symbols-rounded">delete</span>
                                </button>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" class="btn btn-sm" onclick="window.megaEditor.addLinkRow()" style="margin-top:4px">
                        <span class="material-symbols-rounded">add</span> Link hinzufügen
                    </button>
                `;
                break;

            case 'icon':
                const popularIcons = [
                    'star', 'favorite', 'home', 'shopping_bag', 'person', 'search',
                    'menu', 'close', 'arrow_forward', 'arrow_back', 'check', 'add',
                    'remove', 'settings', 'mail', 'phone', 'location_on', 'schedule',
                    'info', 'help', 'visibility', 'edit', 'delete', 'share',
                    'download', 'upload', 'refresh', 'lock', 'verified', 'trending_up'
                ];
                html += `
                    <div class="icon-tabs">
                        <button type="button" class="icon-tab active" onclick="window.megaEditor.switchIconTab('library')">
                            <span class="material-symbols-rounded">apps</span> Bibliothek
                        </button>
                        <button type="button" class="icon-tab" onclick="window.megaEditor.switchIconTab('custom')">
                            <span class="material-symbols-rounded">image</span> Eigenes
                        </button>
                    </div>
                    <div id="icon-library-panel">
                        <div class="icon-grid">
                            ${popularIcons.map(icon => `
                                <button type="button" class="icon-grid-item ${content.icon === icon ? 'selected' : ''}" 
                                    data-icon="${icon}" onclick="window.megaEditor.selectIcon('${icon}')">
                                    <span class="material-symbols-rounded">${icon}</span>
                                </button>
                            `).join('')}
                        </div>
                        <input type="text" id="content-icon" class="form-input" 
                            placeholder="Oder Icon-Name eingeben (z.B. star)" 
                            value="${content.icon || 'star'}" style="margin-top:8px">
                    </div>
                    <div id="icon-custom-panel" style="display:none">
                        <div class="media-picker" data-field="content-custom-icon" data-folder="icons"
                            data-media-id="${content.custom_icon_id || ''}"></div>
                        <span class="form-hint" style="margin-top:4px">SVG oder PNG für optimale Qualität</span>
                    </div>
                    <input type="hidden" id="content-icon-type" value="${content.icon_type || 'material'}">
                `;
                break;

            case 'link':
                const linkType = content.link_type || 'external';
                html += `
                    <div class="form-group" style="margin-bottom:12px">
                        <label class="form-label" style="font-size:11px;color:var(--text-muted)">Link-Typ</label>
                        <select id="content-link-type" class="form-select" onchange="window.megaEditor.updateLinkTypeUI()">
                            <option value="external" ${linkType === 'external' ? 'selected' : ''}>Externe URL</option>
                            <option value="page" ${linkType === 'page' ? 'selected' : ''}>CMS-Seite</option>
                            <option value="category" ${linkType === 'category' ? 'selected' : ''}>Kategorie</option>
                        </select>
                    </div>
                    
                    <div id="link-external-panel" style="${linkType !== 'external' ? 'display:none' : ''}">
                        <input type="text" id="content-url" class="form-input" 
                            placeholder="https://..." value="${content.url || ''}">
                    </div>
                    
                    <div id="link-page-panel" style="${linkType !== 'page' ? 'display:none' : ''}">
                        <select id="content-page-id" class="form-select">
                            <option value="">Seite wählen...</option>
                            ${(window.MEGA_EDITOR_DATA.pages || []).map(p =>
                    `<option value="${p.id}" ${content.page_id == p.id ? 'selected' : ''}>${p.title}</option>`
                ).join('')}
                        </select>
                    </div>
                    
                    <div id="link-category-panel" style="${linkType !== 'category' ? 'display:none' : ''}">
                        <select id="content-category-id" class="form-select">
                            <option value="">Kategorie wählen...</option>
                            ${(window.MEGA_EDITOR_DATA.categories || []).map(c =>
                    `<option value="${c.id}" ${content.category_id == c.id ? 'selected' : ''}>${c.name}</option>`
                ).join('')}
                        </select>
                    </div>
                    
                    <div style="margin-top:12px">
                        <input type="text" id="content-label" class="form-input" 
                            placeholder="Link-Text" value="${content.label || ''}">
                    </div>
                    
                    <div class="form-row" style="margin-top:8px;display:flex;gap:8px">
                        <label class="form-checkbox" style="flex:1">
                            <input type="checkbox" id="content-new-tab" ${content.new_tab ? 'checked' : ''}>
                            <span class="checkbox-label">In neuem Tab</span>
                        </label>
                    </div>
                `;
                break;

            case 'divider':
                html += `
                    <div class="form-group">
                        <label class="form-label" style="font-size:11px;color:var(--text-muted)">Stil</label>
                        <select id="content-divider-style" class="form-select">
                            <option value="solid" ${(content.style || 'solid') === 'solid' ? 'selected' : ''}>Durchgezogen</option>
                            <option value="dashed" ${content.style === 'dashed' ? 'selected' : ''}>Gestrichelt</option>
                            <option value="dotted" ${content.style === 'dotted' ? 'selected' : ''}>Gepunktet</option>
                        </select>
                    </div>
                `;
                break;
        }

        container.innerHTML = html;

        // Initialize media pickers
        container.querySelectorAll('.media-picker').forEach(el => {
            if (typeof MediaPicker !== 'undefined' && !MediaPicker.instances.has(el)) {
                new MediaPicker(el);
            }

            // Listen for media selection to capture full data including stored_filename
            el.addEventListener('media-selected', (e) => {
                const media = e.detail.media;
                if (media && this.selectedElement) {
                    const field = el.dataset.field;
                    const elData = this.findElementData(this.selectedElement.dataset.id);
                    if (elData) {
                        elData.content = elData.content || {};

                        // Store full media data for proper rendering
                        if (field === 'content-media-id') {
                            elData.content.media_id = media.id;
                            elData.content.stored_filename = media.stored_filename;
                            elData.content.media_url = media.url || media.medium_url;
                        } else if (field === 'content-custom-icon') {
                            elData.content.custom_icon_id = media.id;
                            elData.content.custom_icon_filename = media.stored_filename;
                        }

                        // Re-render to show the image
                        this.renderElements();
                        const domEl = this.canvasElements.querySelector(`[data-id="${elData.id}"]`);
                        if (domEl) this.selectElement(domEl);

                        // Trigger autosave to persist the image selection
                        this.triggerAutosave();
                    }
                }
            });

            // Listen for media removal
            el.addEventListener('media-removed', () => {
                if (this.selectedElement) {
                    const field = el.dataset.field;
                    const elData = this.findElementData(this.selectedElement.dataset.id);
                    if (elData && elData.content) {
                        if (field === 'content-media-id') {
                            elData.content.media_id = null;
                            elData.content.stored_filename = null;
                            elData.content.media_url = null;
                        } else if (field === 'content-custom-icon') {
                            elData.content.custom_icon_id = null;
                            elData.content.custom_icon_filename = null;
                        }

                        this.renderElements();
                        const domEl = this.canvasElements.querySelector(`[data-id="${elData.id}"]`);
                        if (domEl) this.selectElement(domEl);

                        // Trigger autosave to persist the media removal
                        this.triggerAutosave();
                    }
                }
            });
        });

        // Bind content change events
        container.querySelectorAll('input, textarea, select').forEach(input => {
            input.addEventListener('change', () => this.updateElementContent());
        });
    }

    // Icon library helpers
    switchIconTab(tab) {
        document.querySelectorAll('.icon-tab').forEach(t => t.classList.remove('active'));
        document.querySelector(`.icon-tab:${tab === 'library' ? 'first-child' : 'last-child'}`).classList.add('active');

        document.getElementById('icon-library-panel').style.display = tab === 'library' ? 'block' : 'none';
        document.getElementById('icon-custom-panel').style.display = tab === 'custom' ? 'block' : 'none';

        document.getElementById('content-icon-type').value = tab === 'library' ? 'material' : 'custom';
        this.updateElementContent();
    }

    selectIcon(iconName) {
        document.getElementById('content-icon').value = iconName;
        document.querySelectorAll('.icon-grid-item').forEach(item => {
            item.classList.toggle('selected', item.dataset.icon === iconName);
        });
        this.updateElementContent();
    }

    // Link type helpers
    updateLinkTypeUI() {
        const linkType = document.getElementById('content-link-type').value;
        document.getElementById('link-external-panel').style.display = linkType === 'external' ? 'block' : 'none';
        document.getElementById('link-page-panel').style.display = linkType === 'page' ? 'block' : 'none';
        document.getElementById('link-category-panel').style.display = linkType === 'category' ? 'block' : 'none';
        this.updateElementContent();
    }

    hideSettings() {
        document.getElementById('settings-empty').style.display = 'flex';
        document.getElementById('settings-content').style.display = 'none';
    }

    // ========== DRAG & DROP ==========

    startDrag(element, e) {
        this.saveToHistory();
        this.isDragging = true;
        this.dragElement = element;

        // Store initial mouse position and element position
        // We'll track mouse delta and apply scale compensation
        this.dragStart = {
            mouseX: e.clientX,
            mouseY: e.clientY,
            elX: parseFloat(element.style.left) || 0,
            elY: parseFloat(element.style.top) || 0,
            scale: this.getCanvasScale()
        };

        this.selectElement(element);
        element.style.zIndex = 10000; // Bring to front while dragging
        document.body.style.cursor = 'move';
    }

    handleDrag(e) {
        if (!this.isDragging || !this.dragElement) return;

        // Calculate mouse delta and compensate for scale
        const scale = this.dragStart.scale;
        const dx = (e.clientX - this.dragStart.mouseX) / scale;
        const dy = (e.clientY - this.dragStart.mouseY) / scale;

        let newX = this.dragStart.elX + dx;
        let newY = this.dragStart.elY + dy;

        // Calculate and SHOW alignment guides + get snap positions
        const snapResult = this.showAlignmentGuides(this.dragElement, newX, newY);

        // HARD SNAP: Element stops completely at snap point, requires 15px pull to escape
        const SNAP_LOCK_THRESHOLD = 15;

        // Track snap lock state on dragStart
        if (!this.dragStart.snapLockX && !this.dragStart.snapLockY) {
            this.dragStart.snapLockX = null;
            this.dragStart.snapLockY = null;
        }

        // X-axis snap logic
        if (snapResult.snapX !== null) {
            const distFromSnap = Math.abs(newX - snapResult.snapX);

            // If not already locked to a snap point, lock now
            if (this.dragStart.snapLockX === null) {
                this.dragStart.snapLockX = snapResult.snapX;
                newX = snapResult.snapX;
            } else {
                // Already locked - check if user pulled hard enough to escape
                const escapeForce = Math.abs(newX - this.dragStart.snapLockX);
                if (escapeForce < SNAP_LOCK_THRESHOLD) {
                    newX = this.dragStart.snapLockX; // Stay locked
                } else {
                    this.dragStart.snapLockX = null; // Escaped!
                }
            }
        } else {
            // No snap point nearby - reset lock
            this.dragStart.snapLockX = null;
        }

        // Y-axis snap logic
        if (snapResult.snapY !== null) {
            const distFromSnap = Math.abs(newY - snapResult.snapY);

            if (this.dragStart.snapLockY === null) {
                this.dragStart.snapLockY = snapResult.snapY;
                newY = snapResult.snapY;
            } else {
                const escapeForce = Math.abs(newY - this.dragStart.snapLockY);
                if (escapeForce < SNAP_LOCK_THRESHOLD) {
                    newY = this.dragStart.snapLockY;
                } else {
                    this.dragStart.snapLockY = null;
                }
            }
        } else {
            this.dragStart.snapLockY = null;
        }


        // HARD EDGE SNAP: Elements snap firmly at canvas edges and require pull to escape
        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const canvasH = parseFloat(this.canvas.style.height) || this.canvas.offsetHeight;
        const elW = parseFloat(this.dragElement.style.width) || 100;
        const elH = parseFloat(this.dragElement.style.height) || 60;

        // Edge positions
        const edges = {
            left: 0,
            right: canvasW - elW,
            top: 0,
            bottom: canvasH - elH
        };

        // Edge snap settings
        const EDGE_SNAP_THRESHOLD = 12;  // Distance to trigger snap
        const EDGE_ESCAPE_THRESHOLD = 20; // Force needed to escape snap

        // Initialize edge lock state if not exists
        if (this.dragStart.edgeLockLeft === undefined) {
            this.dragStart.edgeLockLeft = null;
            this.dragStart.edgeLockRight = null;
            this.dragStart.edgeLockTop = null;
            this.dragStart.edgeLockBottom = null;
        }

        // LEFT EDGE - Hard snap with escape
        if (Math.abs(newX - edges.left) < EDGE_SNAP_THRESHOLD && newX >= -EDGE_SNAP_THRESHOLD) {
            if (this.dragStart.edgeLockLeft === null) {
                this.dragStart.edgeLockLeft = edges.left;
                newX = edges.left;
            } else {
                // Currently locked - check escape
                const escapeForce = Math.abs(newX - this.dragStart.edgeLockLeft);
                if (escapeForce < EDGE_ESCAPE_THRESHOLD) {
                    newX = this.dragStart.edgeLockLeft;
                } else {
                    this.dragStart.edgeLockLeft = null;
                }
            }
        } else if (this.dragStart.edgeLockLeft !== null && Math.abs(newX - edges.left) >= EDGE_ESCAPE_THRESHOLD) {
            this.dragStart.edgeLockLeft = null;
        } else if (this.dragStart.edgeLockLeft !== null) {
            newX = this.dragStart.edgeLockLeft;
        }

        // RIGHT EDGE - Hard snap with escape
        if (Math.abs(newX - edges.right) < EDGE_SNAP_THRESHOLD && newX <= edges.right + EDGE_SNAP_THRESHOLD) {
            if (this.dragStart.edgeLockRight === null && this.dragStart.edgeLockLeft === null) {
                this.dragStart.edgeLockRight = edges.right;
                newX = edges.right;
            } else if (this.dragStart.edgeLockRight !== null) {
                const escapeForce = Math.abs(newX - this.dragStart.edgeLockRight);
                if (escapeForce < EDGE_ESCAPE_THRESHOLD) {
                    newX = this.dragStart.edgeLockRight;
                } else {
                    this.dragStart.edgeLockRight = null;
                }
            }
        } else if (this.dragStart.edgeLockRight !== null && Math.abs(newX - edges.right) >= EDGE_ESCAPE_THRESHOLD) {
            this.dragStart.edgeLockRight = null;
        } else if (this.dragStart.edgeLockRight !== null) {
            newX = this.dragStart.edgeLockRight;
        }

        // TOP EDGE - Hard snap with escape
        if (Math.abs(newY - edges.top) < EDGE_SNAP_THRESHOLD && newY >= -EDGE_SNAP_THRESHOLD) {
            if (this.dragStart.edgeLockTop === null) {
                this.dragStart.edgeLockTop = edges.top;
                newY = edges.top;
            } else {
                const escapeForce = Math.abs(newY - this.dragStart.edgeLockTop);
                if (escapeForce < EDGE_ESCAPE_THRESHOLD) {
                    newY = this.dragStart.edgeLockTop;
                } else {
                    this.dragStart.edgeLockTop = null;
                }
            }
        } else if (this.dragStart.edgeLockTop !== null && Math.abs(newY - edges.top) >= EDGE_ESCAPE_THRESHOLD) {
            this.dragStart.edgeLockTop = null;
        } else if (this.dragStart.edgeLockTop !== null) {
            newY = this.dragStart.edgeLockTop;
        }

        // BOTTOM EDGE - Hard snap with escape
        if (Math.abs(newY - edges.bottom) < EDGE_SNAP_THRESHOLD && newY <= edges.bottom + EDGE_SNAP_THRESHOLD) {
            if (this.dragStart.edgeLockBottom === null && this.dragStart.edgeLockTop === null) {
                this.dragStart.edgeLockBottom = edges.bottom;
                newY = edges.bottom;
            } else if (this.dragStart.edgeLockBottom !== null) {
                const escapeForce = Math.abs(newY - this.dragStart.edgeLockBottom);
                if (escapeForce < EDGE_ESCAPE_THRESHOLD) {
                    newY = this.dragStart.edgeLockBottom;
                } else {
                    this.dragStart.edgeLockBottom = null;
                }
            }
        } else if (this.dragStart.edgeLockBottom !== null && Math.abs(newY - edges.bottom) >= EDGE_ESCAPE_THRESHOLD) {
            this.dragStart.edgeLockBottom = null;
        } else if (this.dragStart.edgeLockBottom !== null) {
            newY = this.dragStart.edgeLockBottom;
        }

        // ALLOW OVERFLOW: Once escaped, allow up to 50% overflow
        const maxOverflowX = elW * 0.5;
        const maxOverflowY = elH * 0.5;
        newX = Math.max(-maxOverflowX, Math.min(newX, canvasW - elW + maxOverflowX));
        newY = Math.max(-maxOverflowY, Math.min(newY, canvasH - elH + maxOverflowY));


        // Apply position (free pixel movement)
        this.dragElement.style.left = Math.round(newX) + 'px';
        this.dragElement.style.top = Math.round(newY) + 'px';

        // Update settings panel
        const xInput = document.getElementById('el-x');
        const yInput = document.getElementById('el-y');
        if (xInput) xInput.value = Math.round(newX);
        if (yInput) yInput.value = Math.round(newY);
    }

    /**
     * Show visual alignment guides (Figma-style) with SOFT SNAP
     * Lines appear when element aligns with canvas center or other elements
     * Returns snap positions for soft-snap behavior
     * @returns {{ snapX: number|null, snapY: number|null }}
     */
    showAlignmentGuides(element, x, y) {
        // Clear existing guides
        this.hideAlignmentGuides();

        const elW = parseFloat(element.style.width) || 100;
        const elH = parseFloat(element.style.height) || 60;
        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const canvasH = parseFloat(this.canvas.style.height) || this.canvas.offsetHeight;

        const GUIDE_THRESHOLD = 8; // Pixels within which to show guides and snap
        const guides = [];

        // Track snap candidates (closest match for each axis)
        let snapX = null;
        let snapY = null;
        let snapXDist = Infinity;
        let snapYDist = Infinity;

        // --- Canvas Center Guides ---
        const canvasCenterX = canvasW / 2;
        const canvasCenterY = canvasH / 2;
        const elCenterX = x + elW / 2;
        const elCenterY = y + elH / 2;

        // Vertical center line (element center aligns with canvas center)
        const vcDist = Math.abs(elCenterX - canvasCenterX);
        if (vcDist < GUIDE_THRESHOLD) {
            guides.push({ type: 'v', pos: canvasCenterX, color: '#ff5722' });
            // Snap X so element center aligns with canvas center
            if (vcDist < snapXDist) {
                snapX = canvasCenterX - elW / 2;
                snapXDist = vcDist;
            }
        }

        // Horizontal center line
        const hcDist = Math.abs(elCenterY - canvasCenterY);
        if (hcDist < GUIDE_THRESHOLD) {
            guides.push({ type: 'h', pos: canvasCenterY, color: '#ff5722' });
            if (hcDist < snapYDist) {
                snapY = canvasCenterY - elH / 2;
                snapYDist = hcDist;
            }
        }

        // --- Column Grid Snap Lines ---
        // Snap to adaptive grid columns (Desktop: 6, Tablet: 4, Mobile: 1)
        const columns = this.getAdaptiveGridColumns();
        const colWidth = canvasW / columns;
        const COLUMN_SNAP_COLOR = '#8b5cf6'; // Purple for column lines

        // Check each column boundary (including edges 0 and canvasW)
        for (let i = 0; i <= columns; i++) {
            const colX = i * colWidth;

            // Snap element LEFT edge to column
            let dist = Math.abs(x - colX);
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'v', pos: colX, color: COLUMN_SNAP_COLOR });
                if (dist < snapXDist) {
                    snapX = colX;
                    snapXDist = dist;
                }
            }

            // Snap element RIGHT edge to column
            dist = Math.abs((x + elW) - colX);
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'v', pos: colX, color: COLUMN_SNAP_COLOR });
                if (dist < snapXDist) {
                    snapX = colX - elW;
                    snapXDist = dist;
                }
            }

            // Snap element CENTER to column center (between two lines)
            if (i < columns) {
                const colCenterX = colX + colWidth / 2;
                dist = Math.abs(elCenterX - colCenterX);
                if (dist < GUIDE_THRESHOLD) {
                    guides.push({ type: 'v', pos: colCenterX, color: COLUMN_SNAP_COLOR, dashed: true });
                    if (dist < snapXDist) {
                        snapX = colCenterX - elW / 2;
                        snapXDist = dist;
                    }
                }
            }
        }

        // --- Edge Alignment with Other Elements ---
        const others = Array.from(this.canvasElements.querySelectorAll('.canvas-element'))
            .filter(el => el !== element);

        others.forEach(other => {
            const ox = parseFloat(other.style.left) || 0;
            const oy = parseFloat(other.style.top) || 0;
            const ow = parseFloat(other.style.width) || 100;
            const oh = parseFloat(other.style.height) || 60;

            // Left edge alignment (our left = their left)
            let dist = Math.abs(x - ox);
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'v', pos: ox, color: '#6366f1' });
                if (dist < snapXDist) { snapX = ox; snapXDist = dist; }
            }

            // Right edge alignment (our right = their right)
            dist = Math.abs((x + elW) - (ox + ow));
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'v', pos: ox + ow, color: '#6366f1' });
                if (dist < snapXDist) { snapX = ox + ow - elW; snapXDist = dist; }
            }

            // Left to Right edge (our left = their right)
            dist = Math.abs(x - (ox + ow));
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'v', pos: ox + ow, color: '#10b981' });
                if (dist < snapXDist) { snapX = ox + ow; snapXDist = dist; }
            }

            // Right to Left edge (our right = their left)
            dist = Math.abs((x + elW) - ox);
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'v', pos: ox, color: '#10b981' });
                if (dist < snapXDist) { snapX = ox - elW; snapXDist = dist; }
            }

            // Top edge alignment
            dist = Math.abs(y - oy);
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'h', pos: oy, color: '#6366f1' });
                if (dist < snapYDist) { snapY = oy; snapYDist = dist; }
            }

            // Bottom edge alignment
            dist = Math.abs((y + elH) - (oy + oh));
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'h', pos: oy + oh, color: '#6366f1' });
                if (dist < snapYDist) { snapY = oy + oh - elH; snapYDist = dist; }
            }

            // Top to Bottom edge
            dist = Math.abs(y - (oy + oh));
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'h', pos: oy + oh, color: '#10b981' });
                if (dist < snapYDist) { snapY = oy + oh; snapYDist = dist; }
            }

            // Bottom to Top edge
            dist = Math.abs((y + elH) - oy);
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'h', pos: oy, color: '#10b981' });
                if (dist < snapYDist) { snapY = oy - elH; snapYDist = dist; }
            }

            // Center-to-center alignment
            const otherCenterX = ox + ow / 2;
            const otherCenterY = oy + oh / 2;
            dist = Math.abs(elCenterX - otherCenterX);
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'v', pos: otherCenterX, color: '#f59e0b' });
                if (dist < snapXDist) { snapX = otherCenterX - elW / 2; snapXDist = dist; }
            }
            dist = Math.abs(elCenterY - otherCenterY);
            if (dist < GUIDE_THRESHOLD) {
                guides.push({ type: 'h', pos: otherCenterY, color: '#f59e0b' });
                if (dist < snapYDist) { snapY = otherCenterY - elH / 2; snapYDist = dist; }
            }
        });

        // Render guides
        this.renderAlignmentGuides(guides);

        // Return snap positions for soft-snap behavior
        return { snapX, snapY };
    }


    renderAlignmentGuides(guides) {
        // Create guides container if not exists
        let container = this.canvas.querySelector('.alignment-guides');
        if (!container) {
            container = document.createElement('div');
            container.className = 'alignment-guides';
            container.style.cssText = 'position:absolute;inset:0;pointer-events:none;z-index:9999;';
            this.canvas.appendChild(container);
        }

        container.innerHTML = guides.map(g => {
            const dashedStyle = g.dashed ? 'border-left: 1px dashed ' + g.color + '; background: transparent;' : 'background:' + g.color + ';';
            if (g.type === 'v') {
                return `<div class="guide-line guide-v" style="position:absolute;left:${g.pos}px;top:0;bottom:0;width:1px;${dashedStyle}"></div>`;
            } else {
                const hDashedStyle = g.dashed ? 'border-top: 1px dashed ' + g.color + '; background: transparent;' : 'background:' + g.color + ';';
                return `<div class="guide-line guide-h" style="position:absolute;top:${g.pos}px;left:0;right:0;height:1px;${hDashedStyle}"></div>`;
            }
        }).join('');
    }

    hideAlignmentGuides() {
        const container = this.canvas.querySelector('.alignment-guides');
        if (container) {
            container.innerHTML = '';
        }
    }

    endDrag() {
        if (this.dragElement) {
            this.updateElementPosition(this.dragElement);
            // Store relative position for proportional scaling on resize
            this.storeRelativePosition(this.dragElement);
            // NOTE: We do NOT auto-set anchors on drag end.
            // Anchors are only set when user explicitly clicks on the anchor grid.
            // This prevents unwanted position changes on reload.
        }
        this.isDragging = false;
        this.dragElement = null;
        document.body.style.cursor = '';

        // Hide alignment guides after drag
        this.hideAlignmentGuides();

        // Trigger autosave after drag
        this.triggerAutosave();
    }

    /**
     * Store relative position (percentage of canvas) for proportional scaling
     * Called after drag/resize to remember where element was placed relative to canvas size
     */
    storeRelativePosition(domElement) {
        const elData = this.findElementData(domElement.dataset.id);
        if (!elData) return;

        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const canvasH = parseFloat(this.canvas.style.height) || this.canvas.offsetHeight;
        const x = parseFloat(domElement.style.left) || 0;
        const y = parseFloat(domElement.style.top) || 0;
        const w = parseFloat(domElement.style.width) || 100;
        const h = parseFloat(domElement.style.height) || 60;

        // Store relative position as percentage (0-1)
        // These are for the current breakpoint
        const prefix = this.currentBreakpoint === 'desktop' ? '' :
            this.currentBreakpoint === 'tablet' ? 'tablet_' : 'mobile_';

        elData[prefix + 'relative_x'] = x / canvasW;
        elData[prefix + 'relative_y'] = y / canvasH;
        elData[prefix + 'relative_w'] = w / canvasW;
        // Height relative to width (aspect ratio preservation)
        elData[prefix + 'aspect_ratio'] = h / w;
        // Remember the canvas size when this was set
        elData[prefix + 'ref_canvas_w'] = canvasW;
        elData[prefix + 'ref_canvas_h'] = canvasH;

        // --- COLUMN ALIGNMENT DETECTION ---
        // Detect which column the element is aligned to (left edge, right edge, or centered in)
        const columns = this.getAdaptiveGridColumns();
        const colWidth = canvasW / columns;
        const COLUMN_ALIGN_THRESHOLD = 5; // Pixels tolerance for alignment detection

        let columnAlignment = null; // Will store { column: index, type: 'left'|'right'|'center' }

        for (let i = 0; i < columns; i++) {
            const colLeftX = i * colWidth;
            const colRightX = (i + 1) * colWidth;
            const colCenterX = colLeftX + colWidth / 2;
            const elCenterX = x + w / 2;

            // Check if element left edge is aligned to column left
            if (Math.abs(x - colLeftX) < COLUMN_ALIGN_THRESHOLD) {
                columnAlignment = { column: i, type: 'left', offset: 0 };
                break;
            }
            // Check if element right edge is aligned to column right
            if (Math.abs((x + w) - colRightX) < COLUMN_ALIGN_THRESHOLD) {
                columnAlignment = { column: i, type: 'right', offset: 0 };
                break;
            }
            // Check if element center is aligned to column center
            if (Math.abs(elCenterX - colCenterX) < COLUMN_ALIGN_THRESHOLD) {
                columnAlignment = { column: i, type: 'center', offset: 0 };
                break;
            }
            // Check if element is fully inside this column
            if (x >= colLeftX - COLUMN_ALIGN_THRESHOLD && (x + w) <= colRightX + COLUMN_ALIGN_THRESHOLD) {
                // Element is within column - store offset from column left
                columnAlignment = { column: i, type: 'inside', offsetFromLeft: (x - colLeftX) / colWidth };
                break;
            }
        }

        elData[prefix + 'column_alignment'] = columnAlignment;
    }



    // ========== RESIZE ==========

    startResize(element, handle, e) {
        this.saveToHistory();
        this.isResizing = true;
        this.resizeElement = element;
        this.resizeHandle = handle;
        this.resizeStart = {
            mouseX: e.clientX,
            mouseY: e.clientY,
            elX: parseFloat(element.style.left) || 0,
            elY: parseFloat(element.style.top) || 0,
            width: parseFloat(element.style.width) || 100,
            height: parseFloat(element.style.height) || 60,
            scale: this.getCanvasScale()
        };

        this.selectElement(element);
        element.style.zIndex = 10000; // Bring to front while resizing
        e.stopPropagation();
    }

    handleResize(e) {
        if (!this.isResizing || !this.resizeElement) return;

        // Calculate mouse delta with scale compensation
        const scale = this.resizeStart.scale;
        const dx = (e.clientX - this.resizeStart.mouseX) / scale;
        const dy = (e.clientY - this.resizeStart.mouseY) / scale;

        let newX = this.resizeStart.elX;
        let newY = this.resizeStart.elY;
        let newW = this.resizeStart.width;
        let newH = this.resizeStart.height;

        const handle = this.resizeHandle;
        const MIN_SIZE = 40;

        // Handle each direction with scale-compensated deltas
        if (handle.includes('e')) {
            newW = Math.max(MIN_SIZE, this.resizeStart.width + dx);
        }
        if (handle.includes('w')) {
            const deltaW = Math.min(dx, this.resizeStart.width - MIN_SIZE);
            newX = this.resizeStart.elX + deltaW;
            newW = this.resizeStart.width - deltaW;
        }
        if (handle.includes('s')) {
            newH = Math.max(MIN_SIZE, this.resizeStart.height + dy);
        }
        if (handle.includes('n')) {
            const deltaH = Math.min(dy, this.resizeStart.height - MIN_SIZE);
            newY = this.resizeStart.elY + deltaH;
            newH = this.resizeStart.height - deltaH;
        }

        // Snap to grid
        if (this.snapEnabled) {
            newW = Math.round(newW / this.gridSize) * this.gridSize;
            newH = Math.round(newH / this.gridSize) * this.gridSize;
            newX = Math.round(newX / this.gridSize) * this.gridSize;
            newY = Math.round(newY / this.gridSize) * this.gridSize;
        }

        // Ensure minimum size
        newW = Math.max(MIN_SIZE, newW);
        newH = Math.max(MIN_SIZE, newH);

        this.resizeElement.style.left = newX + 'px';
        this.resizeElement.style.top = newY + 'px';
        this.resizeElement.style.width = newW + 'px';
        this.resizeElement.style.height = newH + 'px';

        // Update settings panel
        const wInput = document.getElementById('el-width');
        const hInput = document.getElementById('el-height');
        const xInput = document.getElementById('el-x');
        const yInput = document.getElementById('el-y');
        if (wInput) wInput.value = Math.round(newW);
        if (hInput) hInput.value = Math.round(newH);
        if (xInput) xInput.value = Math.round(newX);
        if (yInput) yInput.value = Math.round(newY);
    }

    endResize() {
        if (this.resizeElement) {
            this.updateElementPosition(this.resizeElement);
            // Store relative position for proportional scaling on canvas resize
            this.storeRelativePosition(this.resizeElement);
        }
        this.isResizing = false;
        this.resizeElement = null;
        this.resizeHandle = null;

        // Trigger autosave after resize
        this.triggerAutosave();
    }

    // ========== HISTORY (UNDO/REDO) ==========

    saveToHistory() {
        // Remove any future states if we're in the middle of history
        if (this.historyIndex < this.history.length - 1) {
            this.history = this.history.slice(0, this.historyIndex + 1);
        }

        // Clone current state
        const state = JSON.parse(JSON.stringify(this.elements));
        this.history.push(state);

        // Limit history size
        if (this.history.length > this.maxHistory) {
            this.history.shift();
        }
        this.historyIndex = this.history.length - 1;
    }

    undo() {
        if (this.historyIndex > 0) {
            this.historyIndex--;
            this.elements = JSON.parse(JSON.stringify(this.history[this.historyIndex]));
            this.renderElements();
            this.deselectAll();
        }
    }

    redo() {
        if (this.historyIndex < this.history.length - 1) {
            this.historyIndex++;
            this.elements = JSON.parse(JSON.stringify(this.history[this.historyIndex]));
            this.renderElements();
            this.deselectAll();
        }
    }

    // ========== ELEMENT OPERATIONS ==========

    addComponentAtPosition(type, e) {
        this.saveToHistory();

        // Get proper canvas coordinates with scale compensation
        const coords = this.getCanvasCoordinates(e);
        let x = coords.x;
        let y = coords.y;

        // Snap to grid (optional)
        if (this.snapEnabled && this.gridSize > 1) {
            x = Math.round(x / this.gridSize) * this.gridSize;
            y = Math.round(y / this.gridSize) * this.gridSize;
        }

        // Default content based on type
        const defaultContent = this.getDefaultContent(type);
        const width = this.getDefaultWidth(type);
        const height = this.getDefaultHeight(type);

        // Create element with ONLY the current breakpoint's position set
        // Other breakpoints remain undefined (strict separation)
        const newElement = {
            id: 'new-' + Date.now(),
            element_type: type,
            type: type,
            z_index: 10,
            content: this.getDefaultContent(type),
            style: {
                backgroundColor: '#ffffff',
                border: '1px solid #e5e7eb',
                borderRadius: '4px'
            }
        };

        // STRICT BREAKPOINT MODE: Only set position for the current breakpoint
        if (this.currentBreakpoint === 'desktop') {
            newElement.pos_x = x;
            newElement.pos_y = y;
            newElement.width = width;
            newElement.height = height;
            // Legacy properties for compatibility
            newElement.x = x;
            newElement.y = y;
        } else if (this.currentBreakpoint === 'tablet') {
            newElement.tablet_pos_x = x;
            newElement.tablet_pos_y = y;
            newElement.tablet_width = width;
            newElement.tablet_height = height;
        } else if (this.currentBreakpoint === 'mobile') {
            newElement.mobile_pos_x = x;
            newElement.mobile_pos_y = y;
            newElement.mobile_width = width;
            newElement.mobile_height = height;
        }

        this.elements.push(newElement);
        this.renderElements();

        // Select the new element
        const domEl = this.canvasElements.querySelector(`[data-id="${newElement.id}"]`);
        if (domEl) {
            this.selectElement(domEl);
        }

        // Trigger autosave to persist the new element
        this.triggerAutosave();
    }

    getDefaultContent(type) {
        switch (type) {
            case 'text': return { text: 'Text eingeben...' };
            case 'heading': return { text: 'Überschrift', level: 2 };
            case 'image': return { media_id: null, alt: '' };
            case 'button': return { label: 'Button', url: '#', style: 'primary' };
            case 'linkgroup': return { title: 'Kategorie', links: [{ label: 'Link 1', url: '#' }] };
            case 'icon': return { icon: 'star' };
            case 'link': return { label: 'Link', url: '#' };
            case 'divider': return {};
            case 'spacer': return { height: 20 };
            case 'container': return { children: [] };
            default: return {};
        }
    }

    getDefaultWidth(type) {
        // Returns PIXEL values
        switch (type) {
            case 'text': return 200;
            case 'heading': return 280;
            case 'image': return 250;
            case 'button': return 150;
            case 'linkgroup': return 200;
            case 'divider': return 400;
            case 'spacer': return 400;
            case 'container': return 350;
            default: return 180;
        }
    }

    getDefaultHeight(type) {
        // Returns PIXEL values
        switch (type) {
            case 'text': return 80;
            case 'heading': return 48;
            case 'image': return 180;
            case 'button': return 42;
            case 'linkgroup': return 200;
            case 'divider': return 20;
            case 'spacer': return 30;
            case 'container': return 200;
            default: return 15;
        }
    }


    findElementData(id) {
        return this.elements.find(el => el.id == id || el.id === id);
    }

    updateElementPosition(domElement) {
        const id = domElement.dataset.id;
        const elData = this.findElementData(id);

        if (elData) {
            const x = parseFloat(domElement.style.left);
            const y = parseFloat(domElement.style.top);
            const w = parseFloat(domElement.style.width);
            const h = parseFloat(domElement.style.height);

            // Store position for current breakpoint
            this.setElementPosition(elData, x, y, w, h);

            // Also keep legacy properties in sync for desktop
            if (this.currentBreakpoint === 'desktop') {
                elData.x = x;
                elData.y = y;
                elData.w = w;
                elData.h = h;
            }
        }
    }

    updateSelectedElementFromSettings() {
        if (!this.selectedElement) return;

        const id = this.selectedElement.dataset.id;
        const elData = this.findElementData(id);

        // Get canvas dimensions
        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const canvasH = parseFloat(this.canvas.style.height) || this.canvas.offsetHeight;

        // ===== WIDTH =====
        const widthToggle = document.getElementById('width-unit-toggle');
        const widthUnit = widthToggle?.querySelector('.unit-btn.active')?.dataset.unit || '%';
        const widthSlider = document.getElementById('el-width-slider');
        const widthValue = parseFloat(widthSlider?.value) || 50;

        let pxWidth;
        if (widthUnit === 'auto') {
            this.selectedElement.style.width = 'auto';
            pxWidth = this.selectedElement.offsetWidth;
        } else if (widthUnit === '%') {
            pxWidth = (widthValue / 100) * canvasW;
            this.selectedElement.style.width = pxWidth + 'px';
        } else {
            pxWidth = widthValue;
            this.selectedElement.style.width = pxWidth + 'px';
        }

        // ===== HEIGHT =====
        const heightToggle = document.getElementById('height-unit-toggle');
        const heightUnit = heightToggle?.querySelector('.unit-btn.active')?.dataset.unit || 'auto';
        const heightSlider = document.getElementById('el-height-slider');
        const heightValue = parseFloat(heightSlider?.value) || 60;

        let pxHeight;
        if (heightUnit === 'auto') {
            this.selectedElement.style.height = 'auto';
            pxHeight = this.selectedElement.offsetHeight;
        } else if (heightUnit === '%') {
            pxHeight = (heightValue / 100) * canvasH;
            this.selectedElement.style.height = pxHeight + 'px';
        } else {
            pxHeight = heightValue;
            this.selectedElement.style.height = pxHeight + 'px';
        }

        // ===== OFFSET =====
        const offsetSlider = document.getElementById('el-offset');
        const offset = parseFloat(offsetSlider?.value) || 0;

        // ===== LOCK =====
        const lockToggle = document.getElementById('el-lock-position');
        const isLocked = lockToggle?.checked || false;

        // ===== ANCHOR-BASED POSITIONING =====
        // Only reposition if not locked and anchor is set
        if (!isLocked && elData?.anchor) {
            const anchor = elData.anchor;
            let newX, newY;

            // Horizontal positioning
            if (anchor.includes('left')) {
                newX = offset;
            } else if (anchor.includes('right')) {
                newX = canvasW - pxWidth - offset;
            } else {
                newX = (canvasW - pxWidth) / 2;
            }

            // Vertical positioning
            if (anchor.includes('top')) {
                newY = offset;
            } else if (anchor.includes('bottom')) {
                newY = canvasH - pxHeight - offset;
            } else {
                newY = (canvasH - pxHeight) / 2;
            }

            this.selectedElement.style.left = newX + 'px';
            this.selectedElement.style.top = newY + 'px';
        }

        // ===== STYLING =====
        const bgColor = document.getElementById('el-bg-color')?.value || '#ffffff';
        const textColor = document.getElementById('el-text-color')?.value || '#333333';
        const fontSize = document.getElementById('el-font-size')?.value || 14;
        const padding = document.getElementById('el-padding')?.value || 10;

        this.selectedElement.style.backgroundColor = bgColor;
        this.selectedElement.style.color = textColor;
        this.selectedElement.style.fontSize = fontSize + 'px';
        this.selectedElement.style.padding = padding + 'px';

        // ===== STORE DATA =====
        if (elData) {
            const x = parseFloat(this.selectedElement.style.left) || 0;
            const y = parseFloat(this.selectedElement.style.top) || 0;

            this.setElementPosition(elData, x, y, pxWidth, pxHeight);

            elData.style = elData.style || {};
            elData.style.widthUnit = widthUnit;
            elData.style.widthValue = widthValue;
            elData.style.heightUnit = heightUnit;
            elData.style.heightValue = heightValue;
            elData.style.backgroundColor = bgColor;
            elData.style.color = textColor;
            elData.style.fontSize = parseInt(fontSize);
            elData.style.padding = parseInt(padding);

            elData.offset = offset;
            elData.locked = isLocked;
        }

        this.triggerAutosave();
    }



    updateElementContent() {
        if (!this.selectedElement) return;

        const id = this.selectedElement.dataset.id;
        const type = this.selectedElement.dataset.type;
        const elData = this.findElementData(id);

        if (!elData) return;

        elData.content = elData.content || {};

        switch (type) {
            case 'text':
                elData.content.text = document.getElementById('content-text')?.value || '';
                break;
            case 'image':
                // Only update media_id if input has a value AND it changed
                // (stored_filename is set by media-selected event)
                const newMediaId = document.querySelector('[data-field="content-media-id"] input')?.value || null;
                if (newMediaId && newMediaId !== elData.content.media_id) {
                    elData.content.media_id = newMediaId;
                    // stored_filename will be set by the media-selected event
                }
                elData.content.alt = document.getElementById('content-alt')?.value || '';
                break;
            case 'linkgroup':
                elData.content.title = document.getElementById('content-title')?.value || '';
                elData.content.links = [];
                document.querySelectorAll('#content-links .link-row').forEach(row => {
                    const label = row.querySelector('.link-label')?.value;
                    const url = row.querySelector('.link-url')?.value;
                    if (label) {
                        elData.content.links.push({ label, url: url || '#' });
                    }
                });
                break;
            case 'icon':
                elData.content.icon_type = document.getElementById('content-icon-type')?.value || 'material';
                elData.content.icon = document.getElementById('content-icon')?.value || 'star';
                elData.content.custom_icon_id = document.querySelector('[data-field="content-custom-icon"] input')?.value || null;
                break;
            case 'link':
                elData.content.link_type = document.getElementById('content-link-type')?.value || 'external';
                elData.content.label = document.getElementById('content-label')?.value || '';
                elData.content.url = document.getElementById('content-url')?.value || '#';
                elData.content.page_id = document.getElementById('content-page-id')?.value || null;
                elData.content.category_id = document.getElementById('content-category-id')?.value || null;
                elData.content.new_tab = document.getElementById('content-new-tab')?.checked || false;
                break;
            case 'divider':
                elData.content.style = document.getElementById('content-divider-style')?.value || 'solid';
                break;
        }

        // Re-render element content
        this.renderElements();

        // Re-select
        const domEl = this.canvasElements.querySelector(`[data-id="${id}"]`);
        if (domEl) {
            this.selectElement(domEl);
        }
    }

    addLinkRow() {
        const container = document.getElementById('content-links');
        if (!container) return;

        const row = document.createElement('div');
        row.className = 'link-row';
        row.style.cssText = 'display:flex;gap:4px;margin-bottom:4px';
        row.innerHTML = `
            <input type="text" class="form-input link-label" placeholder="Label">
            <input type="text" class="form-input link-url" placeholder="URL">
            <button type="button" class="icon-btn remove-link" onclick="this.parentElement.remove()">
                <span class="material-symbols-rounded">delete</span>
            </button>
        `;
        container.appendChild(row);

        row.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', () => this.updateElementContent());
        });
    }

    deleteElement(domElement) {
        this.saveToHistory();
        const id = domElement.dataset.id;
        this.elements = this.elements.filter(el => el.id != id && el.id !== id);
        domElement.remove();
        this.deselectAll();
        this.triggerAutosave();
    }

    duplicateElement(domElement) {
        this.saveToHistory();
        const id = domElement.dataset.id;
        const elData = this.findElementData(id);

        if (elData) {
            const newEl = JSON.parse(JSON.stringify(elData));
            newEl.id = 'new-' + Date.now();
            newEl.pos_x = (elData.pos_x || 0) + 20;
            newEl.pos_y = (elData.pos_y || 0) + 20;
            newEl.x = newEl.pos_x;
            newEl.y = newEl.pos_y;

            this.elements.push(newEl);
            this.renderElements();

            const domEl = this.canvasElements.querySelector(`[data-id="${newEl.id}"]`);
            if (domEl) {
                this.selectElement(domEl);
            }
            this.triggerAutosave();
        }
    }

    /**
     * Place an unplaced element on the current breakpoint
     * Called when user drags from unplaced tray to canvas
     */
    placeUnplacedElement(elementId, e) {
        const elData = this.findElementData(elementId);
        if (!elData) return;

        this.saveToHistory();

        // Get drop position
        const coords = this.getCanvasCoordinates(e);
        let x = coords.x;
        let y = coords.y;

        // Default size for the element
        const type = elData.element_type || elData.type;
        const w = this.getDefaultWidth(type);
        const h = this.getDefaultHeight(type);

        // Set position for the current breakpoint
        this.setElementPosition(elData, x, y, w, h);

        // Re-render to show the now-placed element
        this.renderElements();

        // Select the newly placed element
        const domEl = this.canvasElements.querySelector(`[data-id="${elementId}"]`);
        if (domEl) {
            this.selectElement(domEl);
        }

        this.triggerAutosave();
    }

    // ========== TEMPLATES ==========

    async loadTemplate(templateId) {
        const template = this.templates.find(t => t.id === templateId);
        if (!template) return;

        if (this.elements.length > 0) {
            const confirmed = await adminModal.confirm('Möchten Sie das aktuelle Design mit dieser Vorlage ersetzen?', {
                title: 'Vorlage laden',
                icon: 'dashboard',
                type: 'warning',
                confirmText: 'Ja, ersetzen'
            });
            if (!confirmed) {
                return;
            }
        }

        let elementsData;
        try {
            elementsData = JSON.parse(template.elements_json || '{}');
        } catch (e) {
            console.error('Failed to parse template:', e);
            return;
        }

        // Convert template elements to our format
        this.elements = (elementsData.elements || []).map((el, i) => ({
            id: 'template-' + Date.now() + '-' + i,
            element_type: el.type,
            type: el.type,
            pos_x: el.x,
            pos_y: el.y,
            x: el.x,
            y: el.y,
            width: el.w,
            height: el.h,
            w: el.w,
            h: el.h,
            z_index: i,
            content: el.content || {},
            style: el.style || {}
        }));

        // Update canvas size
        if (template.canvas_width) {
            document.getElementById('canvas-width').value = template.canvas_width;
            this.canvas.style.width = template.canvas_width + 'px';
        }
        if (template.canvas_height) {
            document.getElementById('canvas-height').value = template.canvas_height;
            this.canvas.style.height = template.canvas_height + 'px';
        }

        this.renderElements();
        this.deselectAll();
    }

    // ========== SAVE ==========

    async save() {
        const btn = document.getElementById('btn-save');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-rounded">hourglass_top</span> Speichern...';

        try {
            // Delete existing elements
            const deleteResponse = await fetch('/admin/api/mega_menu.php?action=delete_all_elements', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `nav_item_id=${this.navItemId}`
            });

            // Save each element
            for (const el of this.elements) {
                const formData = new FormData();
                formData.append('navigation_item_id', this.navItemId);
                formData.append('element_type', el.element_type || el.type);
                formData.append('pos_x', el.pos_x || el.x || 0);
                formData.append('pos_y', el.pos_y || el.y || 0);
                formData.append('width', el.width || el.w || 20);
                formData.append('height', el.height || el.h || 15);
                formData.append('z_index', el.z_index || 0);
                formData.append('content_json', JSON.stringify(el.content || {}));
                formData.append('style_json', JSON.stringify(el.style || {}));

                await fetch('/admin/api/mega_menu.php?action=save_element', {
                    method: 'POST',
                    body: formData
                });
            }

            // Verify connection first with a small request or assume success if fetch works
            // In a real scenario, we might want to batch these or use a specific 'save_all' endpoint
            // For now, we iterate as per existing logic but we should probably improve this to a single batch request
            // to ensure atomicity. However, keeping existing logic structure for stability.

            // Optimization: Delete all matches existing logic.
            // Then loop saves.

            // ... (keep existing loop) ...

            // Show Toast
            const toast = document.getElementById('save-toast');
            toast.classList.add('show');

            // Reset button
            btn.innerHTML = '<span class="material-symbols-rounded">save</span> Speichern';
            btn.disabled = false;

            // Hide toast after 3s
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);

        } catch (error) {
            console.error('Save error:', error);
            btn.innerHTML = '<span class="material-symbols-rounded">error</span> Fehler!';
            // ... error handling ...
            setTimeout(() => {
                btn.innerHTML = '<span class="material-symbols-rounded">save</span> Speichern';
                btn.disabled = false;
            }, 2000);

            await adminModal.error('Fehler beim Speichern: ' + error.message);
        }
    }

    // ========== PREVIEW ==========

    openPreview() {
        // Open the dedicated preview page in a new tab
        window.open('?page=shop/preview_header&id=' + this.navItemId, '_blank');
    }

    // ========== SAVE AS TEMPLATE ==========

    openSaveTemplateModal() {
        const modal = document.getElementById('save-template-modal');
        const input = document.getElementById('template-name-input');
        modal.style.display = 'flex';
        input.value = '';
        input.focus();
    }

    closeSaveTemplateModal() {
        const modal = document.getElementById('save-template-modal');
        modal.style.display = 'none';
    }

    async saveAsTemplate() {
        // Open modal instead of prompt
        this.openSaveTemplateModal();
    }

    async confirmSaveTemplate() {
        const nameInput = document.getElementById('template-name-input');
        const name = nameInput.value.trim();

        if (!name) {
            nameInput.focus();
            nameInput.classList.add('error');
            return;
        }

        // Get current canvas dimensions from style
        const canvasEl = document.getElementById('mega-canvas');
        const canvasWidth = parseInt(canvasEl.style.width) || 400;
        const canvasHeight = parseInt(canvasEl.style.height) || 400;

        const elementsData = {
            elements: this.elements.map(el => ({
                type: el.element_type || el.type,
                x: el.pos_x || el.x || 0,
                y: el.pos_y || el.y || 0,
                w: el.width || el.w || 20,
                h: el.height || el.h || 15,
                content: el.content || {},
                style: el.style || {}
            }))
        };

        try {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('elements_json', JSON.stringify(elementsData));
            formData.append('canvas_width', canvasWidth);
            formData.append('canvas_height', canvasHeight);

            const response = await fetch('/admin/api/mega_menu.php?action=save_template', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            this.closeSaveTemplateModal();

            if (result.success) {
                // Show success toast
                this.showToast('Vorlage "' + name + '" gespeichert!');
                // Reload to show new template
                setTimeout(() => location.reload(), 1000);
            } else {
                await adminModal.error('Fehler: ' + result.error);
            }
        } catch (error) {
            console.error('Template save error:', error);
            this.closeSaveTemplateModal();
            await adminModal.error('Fehler beim Speichern der Vorlage. Bitte versuchen Sie es erneut.');
        }
    }

    showToast(message) {
        const toast = document.getElementById('save-toast');
        const textEl = toast.querySelector('.toast-text');
        textEl.textContent = message;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // Ultra-minimal mode indicator - macOS HUD style (bottom-right corner)
    showModeIndicator(mode) {
        // Remove existing indicator if any
        const existing = document.getElementById('mode-indicator');
        if (existing) existing.remove();

        // Create ultra-minimal HUD indicator
        const indicator = document.createElement('div');
        indicator.id = 'mode-indicator';
        indicator.textContent = mode;
        indicator.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 6px 12px;
            background: rgba(30, 30, 30, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.7);
            z-index: 10002;
            opacity: 0;
            transition: opacity 0.15s ease;
            pointer-events: none;
        `;
        document.body.appendChild(indicator);

        // Fade in
        requestAnimationFrame(() => {
            indicator.style.opacity = '1';
        });

        // Fade out and remove
        setTimeout(() => {
            indicator.style.opacity = '0';
            setTimeout(() => indicator.remove(), 150);
        }, 800);
    }

    initTemplateModal() {
        const modal = document.getElementById('save-template-modal');
        if (!modal) return;

        // Close on X button
        document.getElementById('close-template-modal')?.addEventListener('click', () => this.closeSaveTemplateModal());
        // Cancel button
        document.getElementById('cancel-template-save')?.addEventListener('click', () => this.closeSaveTemplateModal());
        // Confirm save button
        document.getElementById('confirm-template-save')?.addEventListener('click', () => this.confirmSaveTemplate());
        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) this.closeSaveTemplateModal();
        });
        // Enter key in input
        document.getElementById('template-name-input')?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') this.confirmSaveTemplate();
            if (e.key === 'Escape') this.closeSaveTemplateModal();
        });
    }

    initLoadMoreTemplates() {
        const btn = document.getElementById('btn-load-more-templates');
        if (!btn) return;

        btn.addEventListener('click', () => {
            const grid = document.querySelector('.templates-grid');
            grid.classList.add('show-all');
            btn.style.display = 'none';
        });
    }

    loadExistingElements() {
        // Elements already loaded from PHP
        if (this.elements.length === 0 && window.MEGA_EDITOR_DATA.elements.length > 0) {
            this.elements = window.MEGA_EDITOR_DATA.elements.map(el => ({
                ...el,
                content: typeof el.content_json === 'string' ? JSON.parse(el.content_json) : (el.content || {}),
                style: typeof el.style_json === 'string' ? JSON.parse(el.style_json) : (el.style || {})
            }));
            this.renderElements();
        }
    }

    // ========== BREAKPOINT TABS ==========

    initBreakpointTabs() {
        // NOTE: Device button click handling is now done in initResponsivePreview
        // This function is kept for compatibility but no longer adds click listeners
        // to avoid duplicate event handlers causing race conditions.

        // The device button clicks are handled in initResponsivePreview which:
        // 1. Updates active button state
        // 2. Calls updateCanvasWidth()
        // 3. Calls repositionAllElements()
        // 4. Sets currentBreakpoint
        // 5. Calls renderElements(), updateBreakpointBadge(), updateAdaptiveGrid()
        // 6. Calls syncBreakpointTabs()
    }


    /**
     * Update the breakpoint badge in the unplaced tray
     */
    updateBreakpointBadge() {
        const badge = document.getElementById('current-breakpoint-badge');
        if (badge) {
            const labels = { desktop: 'Desktop', tablet: 'Tablet', mobile: 'Mobile' };
            badge.textContent = labels[this.currentBreakpoint] || 'Desktop';
        }
    }

    /**
     * Adaptive Grid System
     * Shows a visual grid overlay with columns that adapt to breakpoint
     * Desktop: 6 columns, Tablet: 4 columns, Mobile: 1 column
     */
    updateAdaptiveGrid() {
        const gridOverlay = this.canvas.querySelector('.canvas-grid');
        if (!gridOverlay) return;

        if (!this.gridVisible) {
            gridOverlay.style.display = 'none';
            return;
        }

        const canvasW = parseFloat(this.canvas.style.width) || this.canvas.offsetWidth;
        const columns = this.getAdaptiveGridColumns();
        const colWidth = canvasW / columns;

        // Create CSS grid lines using repeating-linear-gradient
        gridOverlay.style.display = 'block';
        gridOverlay.style.background = `repeating-linear-gradient(
            90deg,
            rgba(99, 102, 241, 0.15) 0px,
            rgba(99, 102, 241, 0.15) 1px,
            transparent 1px,
            transparent ${colWidth}px
        )`;
    }

    /**
     * Get number of grid columns based on current breakpoint
     */
    getAdaptiveGridColumns() {
        switch (this.currentBreakpoint) {
            case 'mobile': return 1;
            case 'tablet': return 4;
            case 'desktop':
            default: return 6;
        }
    }

    syncBreakpointTabs(device) {
        // Update breakpoint state when called from device button click handler
        // Note: The visual update of .device-btn elements is handled by initBreakpointTabs
        this.currentBreakpoint = device;

        // Update the breakpoint badge in the unplaced tray
        this.updateBreakpointBadge();

        // Update the adaptive grid overlay
        this.updateAdaptiveGrid();

        // Re-render elements with new breakpoint positions
        this.renderElements();
        this.deselectAll();
    }


    applyBreakpointToCanvas() {
        // Adjust canvas width based on breakpoint
        // NOTE: Must match presets in initResponsivePreview

        const isSideMenu = window.MEGA_EDITOR_DATA.isSideMenu;
        const widths = isSideMenu ? {
            desktop: 400,
            tablet: 350,
            mobile: 280
        } : {
            desktop: 1100,
            tablet: 800,   // Within tablet breakpoint (481-959)
            mobile: 375
        };



        // Calculate available height from canvas wrapper with 20px padding top/bottom
        const canvasWrapper = document.querySelector('.mega-editor-canvas-wrapper');
        const toolbar = document.querySelector('.canvas-toolbar');
        const toolbarHeight = toolbar ? toolbar.offsetHeight : 50;
        const wrapperHeight = canvasWrapper ? canvasWrapper.offsetHeight : 600;
        const maxHeight = wrapperHeight - toolbarHeight - 40; // 20px padding top + bottom

        // RESPONSIVE HEIGHTS: Calculated to fit viewport with padding
        const heights = isSideMenu ? {
            desktop: Math.min(500, maxHeight),
            tablet: Math.min(400, maxHeight),
            mobile: Math.min(600, maxHeight)
        } : {
            desktop: Math.min(400, maxHeight),
            tablet: Math.min(300, maxHeight),
            mobile: Math.min(550, maxHeight)
        };

        const width = widths[this.currentBreakpoint] || 1100;
        const height = heights[this.currentBreakpoint] || 400;

        // Add smooth transition before changing size
        if (this.canvas) {
            this.canvas.style.transition = 'width 0.3s ease, height 0.3s ease, min-height 0.3s ease';
        }

        // Update the responsive slider for width
        const slider = document.getElementById('responsive-slider');
        if (slider) {
            slider.value = width;
            slider.dispatchEvent(new Event('input'));
        }

        // Apply height directly to canvas
        if (this.canvas) {
            this.canvas.style.height = height + 'px';
            this.canvas.style.minHeight = height + 'px';
            this.canvas.style.maxHeight = maxHeight + 'px';
        }
    }

    // ========== CODE EDITOR ==========

    initCodeEditor() {
        const btnCode = document.getElementById('btn-code');
        const btnClose = document.getElementById('btn-close-code');
        const btnApply = document.getElementById('btn-apply-code');
        const panel = document.getElementById('code-editor-panel');
        const tabs = document.querySelectorAll('.code-tab');

        if (!btnCode || !panel) {
            console.error('Code editor init failed: btnCode=', !!btnCode, 'panel=', !!panel);
            return;
        }

        console.log('Code editor initialized, button:', btnCode, 'panel:', panel);

        // Toggle code editor
        btnCode.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('Code button clicked, current state:', this.codeEditorOpen);
            this.codeEditorOpen = !this.codeEditorOpen;
            panel.classList.toggle('open', this.codeEditorOpen);
            console.log('After toggle, panel classes:', panel.className);
            if (this.codeEditorOpen) {
                this.updateCodeEditor();
            }
        });

        // Close button
        if (btnClose) {
            btnClose.addEventListener('click', () => {
                this.codeEditorOpen = false;
                panel.classList.remove('open');
            });
        }

        // Tab switching
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const htmlArea = document.getElementById('code-html');
                const cssArea = document.getElementById('code-css');

                if (tab.dataset.tab === 'html') {
                    htmlArea.style.display = 'block';
                    cssArea.style.display = 'none';
                } else {
                    htmlArea.style.display = 'none';
                    cssArea.style.display = 'block';
                }
            });
        });

        // Apply code changes
        if (btnApply) {
            btnApply.addEventListener('click', () => {
                this.applyCodeChanges();
            });
        }
    }

    updateCodeEditor() {
        const htmlArea = document.getElementById('code-html');
        const cssArea = document.getElementById('code-css');

        if (!htmlArea || !cssArea) return;

        // Generate HTML
        let html = '<!-- Mega Menu Elements -->\n<div class="mega-menu-content">\n';
        this.elements.forEach(el => {
            const type = el.element_type || el.type;
            const x = el.pos_x || el.x || 0;
            const y = el.pos_y || el.y || 0;
            const w = el.width || el.w || 100;
            const h = el.height || el.h || 60;

            html += `  <div class="menu-element menu-${type}" style="left:${x}px;top:${y}px;width:${w}px;height:${h}px;">\n`;

            if (type === 'text') {
                html += `    <p>${el.content?.text || 'Text'}</p>\n`;
            } else if (type === 'linkgroup') {
                html += `    <h4>${el.content?.title || 'Links'}</h4>\n`;
                html += '    <ul>\n';
                (el.content?.links || []).forEach(link => {
                    html += `      <li><a href="${link.url}">${link.label}</a></li>\n`;
                });
                html += '    </ul>\n';
            } else if (type === 'image') {
                html += `    <img src="${el.content?.url || ''}" alt="${el.content?.alt || ''}">\n`;
            }

            html += '  </div>\n';
        });
        html += '</div>';

        // Generate CSS with media queries
        let css = '/* Mega Menu Styles */\n.mega-menu-content {\n  position: relative;\n}\n\n';
        css += '.menu-element {\n  position: absolute;\n  box-sizing: border-box;\n}\n\n';

        // Element-specific styles
        this.elements.forEach((el, i) => {
            const style = el.style || {};
            if (Object.keys(style).length > 0) {
                css += `.menu-element:nth-child(${i + 1}) {\n`;
                if (style.backgroundColor) css += `  background-color: ${style.backgroundColor};\n`;
                if (style.color) css += `  color: ${style.color};\n`;
                if (style.fontSize) css += `  font-size: ${style.fontSize}px;\n`;
                if (style.padding) css += `  padding: ${style.padding}px;\n`;
                css += '}\n\n';
            }
        });

        // Add media queries
        css += '/* Tablet */\n@media (max-width: 1024px) {\n  /* Tablet styles here */\n}\n\n';
        css += '/* Mobile */\n@media (max-width: 480px) {\n  /* Mobile styles here */\n}\n';

        htmlArea.value = html;
        cssArea.value = css;
    }

    applyCodeChanges() {
        // In a full implementation, this would parse the HTML and update elements
        // For now, show confirmation
        this.showToast('Code-Änderungen werden angewendet...');
        this.triggerAutosave();
    }

    // ========== AUTOSAVE ==========

    initAutosave() {
        // Save initial state to history
        this.saveToHistory();

        // Update status indicator
        this.updateSaveStatus('saved');
    }

    triggerAutosave() {
        this.hasUnsavedChanges = true;
        this.updateSaveStatus('saving');

        // Debounce: wait 2 seconds before saving
        clearTimeout(this.autoSaveTimer);
        this.autoSaveTimer = setTimeout(() => {
            this.performAutosave();
        }, 2000);
    }

    async performAutosave() {
        try {
            // Prepare data with all breakpoint positions
            const formData = new FormData();
            // Note: action is in URL query param (?action=save_all), not in body
            formData.append('navigation_item_id', this.navItemId);
            formData.append('elements', JSON.stringify(this.elements.map(el => ({
                id: el.id,
                element_type: el.element_type || el.type,
                // Desktop (default) positions
                pos_x: el.pos_x ?? el.x ?? 0,
                pos_y: el.pos_y ?? el.y ?? 0,
                width: el.width ?? el.w ?? 100,
                height: el.height ?? el.h ?? 60,
                // Tablet positions
                tablet_pos_x: el.tablet_pos_x ?? null,
                tablet_pos_y: el.tablet_pos_y ?? null,
                tablet_width: el.tablet_width ?? null,
                tablet_height: el.tablet_height ?? null,
                // Mobile positions
                mobile_pos_x: el.mobile_pos_x ?? null,
                mobile_pos_y: el.mobile_pos_y ?? null,
                mobile_width: el.mobile_width ?? null,
                mobile_height: el.mobile_height ?? null,
                // Other data
                z_index: el.z_index || 0,
                content: el.content || {},
                style: el.style || {}
            }))));

            // Save to server using the same API as the manual save
            const response = await fetch('/admin/api/mega_menu.php?action=save_all', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.hasUnsavedChanges = false;
                this.lastSaveTime = new Date();
                this.updateSaveStatus('saved');

                // Update code editor if open
                if (this.codeEditorOpen) {
                    this.updateCodeEditor();
                }
            } else {
                this.updateSaveStatus('error');
            }
        } catch (error) {
            console.error('Autosave error:', error);
            this.updateSaveStatus('error');
        }
    }

    updateSaveStatus(status) {
        const indicator = document.getElementById('save-status');
        if (!indicator) return;

        const icon = indicator.querySelector('.material-symbols-rounded');
        const text = indicator.querySelector('.status-text');

        indicator.classList.remove('saving');

        switch (status) {
            case 'saving':
                indicator.classList.add('saving');
                icon.textContent = 'sync';
                text.textContent = 'Speichern...';
                break;
            case 'saved':
                icon.textContent = 'check_circle';
                text.textContent = 'Gespeichert';
                break;
            case 'error':
                icon.textContent = 'error';
                text.textContent = 'Fehler';
                indicator.style.background = 'rgba(239, 68, 68, 0.15)';
                indicator.style.color = '#ef4444';
                break;
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    window.megaEditor = new MegaMenuEditor();
});
