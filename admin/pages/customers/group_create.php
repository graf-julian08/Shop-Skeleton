<?php /** Kunden - Neue Kundengruppe erstellen */ ?>
<div class="page-header">
    <div class="page-header-content">
        <nav class="breadcrumb"><a href="?page=customers/groups">Kundengruppen</a> <span>›</span> <span>Neue
                Gruppe</span></nav>
        <h1>Neue Kundengruppe erstellen</h1>
        <p class="page-subtitle">Erstellen Sie eine neue Kundengruppe mit speziellen Vorteilen</p>
    </div>
    <div class="page-header-actions">
        <a href="?page=customers/groups" class="btn">Abbrechen</a>
        <button class="btn btn-primary" onclick="GroupCreate.save()"><span class="material-symbols-rounded">save</span>
            Gruppe erstellen</button>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header">
            <h3>Grunddaten</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Gruppenname <span style="color:var(--error)">*</span></label>
                <input type="text" class="form-input" id="groupName" placeholder="z.B. Premium"
                    oninput="GroupCreate.generateCode()">
            </div>
            <div class="form-group">
                <label class="form-label">Gruppen-Code</label>
                <input type="text" class="form-input" id="groupCode" placeholder="Wird automatisch generiert">
                <p class="form-hint">Eindeutiger Code für die Gruppe (wird automatisch aus dem Namen generiert)</p>
            </div>
            <div class="form-group">
                <label class="form-label">Beschreibung</label>
                <textarea class="form-textarea" id="groupDescription" rows="2"
                    placeholder="Beschreibung der Gruppe..."></textarea>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3>Vorteile</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Standardrabatt (%)</label>
                <input type="number" class="form-input" id="discountPercent" placeholder="0" min="0" max="100"
                    value="0">
            </div>
            <div class="form-group">
                <label class="form-label">Kostenloser Versand</label>
                <label class="toggle">
                    <input type="checkbox" id="freeShipping">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-label">Prioritärer Support</label>
                <label class="toggle">
                    <input type="checkbox" id="prioritySupport">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-label">Frühzeitiger Sale-Zugang</label>
                <label class="toggle">
                    <input type="checkbox" id="earlyAccess">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Automatische Zuordnung</h3>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Automatisch zuweisen wenn</label>
                <select class="form-select" id="autoAssignType" onchange="GroupCreate.toggleThreshold()">
                    <option value="disabled">Deaktiviert</option>
                    <option value="min_spent">Mindestumsatz erreicht</option>
                    <option value="min_orders">Mindestbestellungen erreicht</option>
                </select>
            </div>
            <div class="form-group" id="thresholdGroup" style="display:none;">
                <label class="form-label" id="thresholdLabel">Schwellenwert</label>
                <input type="number" class="form-input" id="autoAssignThreshold" placeholder="z.B. 500" min="0"
                    value="0">
            </div>
        </div>
        <p class="form-hint">Kunden werden automatisch dieser Gruppe zugewiesen, wenn die Bedingung erfüllt ist.</p>
    </div>
    <div class="card-footer">
        <a href="?page=customers/groups" class="btn">Abbrechen</a>
        <button class="btn btn-primary" onclick="GroupCreate.save()"><span class="material-symbols-rounded">save</span>
            Gruppe erstellen</button>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<style>
    .breadcrumb {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .breadcrumb a {
        color: var(--accent);
    }

    .form-hint {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .toggle {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        cursor: pointer;
    }

    .toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--bg-tertiary);
        transition: .3s;
        border-radius: 26px;
        border: 1px solid var(--border-color);
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    .toggle input:checked+.toggle-slider {
        background-color: var(--success);
        border-color: var(--success);
    }

    .toggle input:checked+.toggle-slider:before {
        transform: translateX(22px);
    }

    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 16px 24px;
        border-radius: var(--radius-md);
        color: white;
        font-weight: 500;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s;
        z-index: 1001;
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
</style>

<script>
    const GroupCreate = {
        apiBase: 'api/customers.php',
        shopId: 1,

        generateCode() {
            const name = document.getElementById('groupName').value;
            let code = name.toLowerCase()
                .replace(/[^a-z0-9äöü]/g, '_')
                .replace(/ä/g, 'ae')
                .replace(/ö/g, 'oe')
                .replace(/ü/g, 'ue')
                .replace(/_+/g, '_')
                .replace(/^_|_$/g, '');
            document.getElementById('groupCode').value = code;
        },

        toggleThreshold() {
            const type = document.getElementById('autoAssignType').value;
            const group = document.getElementById('thresholdGroup');
            const label = document.getElementById('thresholdLabel');

            if (type === 'disabled') {
                group.style.display = 'none';
            } else {
                group.style.display = 'block';
                label.textContent = type === 'min_spent' ? 'Mindestumsatz (€)' : 'Mindestbestellungen';
            }
        },

        async save() {
            const name = document.getElementById('groupName').value.trim();
            const code = document.getElementById('groupCode').value.trim();
            const description = document.getElementById('groupDescription').value.trim();
            const discountPercent = document.getElementById('discountPercent').value || 0;
            const freeShipping = document.getElementById('freeShipping').checked ? 1 : 0;
            const prioritySupport = document.getElementById('prioritySupport').checked ? 1 : 0;
            const earlyAccess = document.getElementById('earlyAccess').checked ? 1 : 0;
            const autoAssignType = document.getElementById('autoAssignType').value;
            const autoAssignThreshold = document.getElementById('autoAssignThreshold').value || 0;

            if (!name) {
                this.showToast('Bitte geben Sie einen Gruppennamen ein', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'create_group');
            formData.append('shop_id', this.shopId);
            formData.append('name', name);
            formData.append('code', code);
            formData.append('description', description);
            formData.append('discount_percent', discountPercent);
            formData.append('free_shipping', freeShipping);
            formData.append('priority_support', prioritySupport);
            formData.append('early_access', earlyAccess);
            formData.append('auto_assign_type', autoAssignType);
            formData.append('auto_assign_threshold', autoAssignThreshold);

            try {
                const res = await fetch(this.apiBase, { method: 'POST', body: formData });
                const data = await res.json();

                if (data.success) {
                    this.showToast('Gruppe erstellt!', 'success');
                    setTimeout(() => {
                        window.location.href = '?page=customers/groups';
                    }, 1000);
                } else {
                    this.showToast('Fehler: ' + (data.error || 'Unbekannt'), 'error');
                }
            } catch (e) {
                this.showToast('Fehler: ' + e.message, 'error');
            }
        },

        showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            setTimeout(() => toast.className = 'toast', 3000);
        }
    };
</script>