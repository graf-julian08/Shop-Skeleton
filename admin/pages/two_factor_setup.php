<?php
/**
 * Two-Factor Authentication - Setup / Management Page
 * Within admin layout (user must be logged in)
 */

$is2faEnabled = Auth::has2faEnabled();
?>

<style>
    .tfa-setup-container {
        max-width: 680px;
    }

    .tfa-status-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 24px;
        border-radius: var(--radius-lg);
        margin-bottom: 24px;
    }

    .tfa-status-card.enabled {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .tfa-status-card.disabled {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .tfa-status-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .tfa-status-card.enabled .tfa-status-icon {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .tfa-status-card.disabled .tfa-status-icon {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .tfa-status-info h3 {
        margin: 0 0 4px;
        font-size: 16px;
    }

    .tfa-status-info p {
        margin: 0;
        font-size: 14px;
        color: var(--text-muted);
    }

    /* Setup Flow */
    .tfa-setup-steps {
        display: none;
    }

    .tfa-setup-steps.active {
        display: block;
    }

    .tfa-step {
        margin-bottom: 32px;
    }

    .tfa-step-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--accent);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    .step-title {
        font-size: 16px;
        font-weight: 600;
    }

    .qr-code-wrapper {
        text-align: center;
        padding: 24px;
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        margin-bottom: 16px;
        display: inline-block;
    }

    .qr-code-wrapper img {
        display: block;
        width: 200px;
        height: 200px;
    }

    .secret-key-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 16px;
        font-family: 'Courier New', monospace;
        font-size: 15px;
        letter-spacing: 2px;
        word-break: break-all;
    }

    .secret-key-box .copy-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--accent);
        padding: 4px;
        flex-shrink: 0;
    }

    .verify-code-input {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 16px;
    }

    .verify-code-input input {
        width: 200px;
        padding: 12px 16px;
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 20px;
        font-family: monospace;
        letter-spacing: 4px;
        text-align: center;
        background: var(--bg-input);
        color: var(--text-primary);
    }

    .verify-code-input input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
    }

    /* Recovery Codes */
    .recovery-codes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 8px;
        margin-bottom: 16px;
    }

    .recovery-code-item {
        padding: 10px;
        background: var(--bg-tertiary);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-family: monospace;
        font-size: 14px;
        text-align: center;
        letter-spacing: 1px;
    }

    .recovery-warning {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: var(--radius-md);
        margin-bottom: 16px;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .recovery-warning .material-symbols-rounded {
        color: #ef4444;
        flex-shrink: 0;
        font-size: 20px;
    }

    /* Disable Section */
    .disable-section {
        border-top: 1px solid var(--border);
        padding-top: 24px;
        margin-top: 24px;
    }

    .disable-section h3 {
        color: #ef4444;
        margin-bottom: 8px;
    }

    .disable-section p {
        color: var(--text-muted);
        font-size: 14px;
        margin-bottom: 16px;
    }

    .password-confirm {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .password-confirm input {
        padding: 10px 16px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: var(--bg-input);
        color: var(--text-primary);
        font-size: 14px;
        width: 250px;
    }

    .tfa-message {
        padding: 12px 16px;
        border-radius: var(--radius-md);
        margin-bottom: 20px;
        font-size: 14px;
        display: none;
    }

    .tfa-message.success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
        display: block;
    }

    .tfa-message.error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--error);
        color: var(--error);
        display: block;
    }

    .center-block {
        text-align: center;
    }
</style>

<div class="tfa-setup-container">
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <span class="material-symbols-rounded" style="vertical-align: middle; margin-right: 8px;">security</span>
                Zwei-Faktor-Authentifizierung
            </h1>
            <p class="page-subtitle">Schützen Sie Ihr Konto mit einer zusätzlichen Sicherheitsebene</p>
        </div>
    </div>

    <!-- Status Message -->
    <div id="tfaMessage" class="tfa-message"></div>

    <!-- Current Status -->
    <div class="tfa-status-card <?= $is2faEnabled ? 'enabled' : 'disabled' ?>" id="statusCard">
        <div class="tfa-status-icon">
            <span class="material-symbols-rounded" style="font-size:28px;">
                <?= $is2faEnabled ? 'verified_user' : 'shield' ?>
            </span>
        </div>
        <div class="tfa-status-info">
            <h3 id="statusTitle"><?= $is2faEnabled ? '2FA ist aktiviert' : '2FA ist nicht aktiviert' ?></h3>
            <p id="statusDesc"><?= $is2faEnabled
                ? 'Ihr Konto ist mit Zwei-Faktor-Authentifizierung geschützt.'
                : 'Aktivieren Sie 2FA für zusätzliche Sicherheit beim Login.' ?></p>
        </div>
    </div>

    <?php if (!$is2faEnabled): ?>
        <!-- Enable Button -->
        <div id="enableSection">
            <button class="btn btn-primary" id="startSetupBtn" onclick="TfaSetup.startSetup()">
                <span class="material-symbols-rounded" style="vertical-align:middle;margin-right:8px;">add_moderator</span>
                2FA aktivieren
            </button>
        </div>

        <!-- Setup Steps (hidden initially) -->
        <div class="tfa-setup-steps" id="setupSteps">
            <div class="card">
                <div class="card-body">
                    <!-- Step 1: QR Code -->
                    <div class="tfa-step">
                        <div class="tfa-step-header">
                            <div class="step-number">1</div>
                            <div class="step-title">Authenticator-App scannen</div>
                        </div>
                        <p style="color:var(--text-muted);font-size:14px;margin-bottom:16px;">
                            Scannen Sie den QR-Code mit Ihrer Authenticator-App
                            (Google Authenticator, Authy, Microsoft Authenticator, etc.)
                        </p>
                        <div class="center-block">
                            <div class="qr-code-wrapper">
                                <img id="qrCodeImage" src="" alt="QR Code" loading="lazy">
                            </div>
                        </div>
                        <p style="color:var(--text-muted);font-size:13px;margin-top:12px;">
                            Oder geben Sie diesen Schlüssel manuell ein:
                        </p>
                        <div class="secret-key-box">
                            <span id="secretKeyDisplay">—</span>
                            <button class="copy-btn" onclick="TfaSetup.copySecret()" title="Kopieren">
                                <span class="material-symbols-rounded">content_copy</span>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Verify -->
                    <div class="tfa-step">
                        <div class="tfa-step-header">
                            <div class="step-number">2</div>
                            <div class="step-title">Code verifizieren</div>
                        </div>
                        <p style="color:var(--text-muted);font-size:14px;margin-bottom:16px;">
                            Geben Sie den 6-stelligen Code aus Ihrer Authenticator-App ein, um die Einrichtung abzuschliessen.
                        </p>
                        <div class="verify-code-input">
                            <input type="text" id="verifyCodeInput" maxlength="6" placeholder="000000"
                                inputmode="numeric" autocomplete="off">
                            <button class="btn btn-primary" onclick="TfaSetup.enableTfa()" id="enableBtn">
                                Aktivieren
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Recovery Codes (shown after activation) -->
                    <div class="tfa-step" id="recoveryStep" style="display:none;">
                        <div class="tfa-step-header">
                            <div class="step-number">3</div>
                            <div class="step-title">Wiederherstellungs-Codes sichern</div>
                        </div>
                        <div class="recovery-warning">
                            <span class="material-symbols-rounded">warning</span>
                            <div>
                                <strong>Wichtig:</strong> Bewahren Sie diese Codes sicher auf! Jeder Code kann
                                nur einmal verwendet werden. Sie benötigen diese Codes, falls Sie keinen Zugriff
                                auf Ihre Authenticator-App haben.
                            </div>
                        </div>
                        <div class="recovery-codes-grid" id="recoveryCodes"></div>
                        <div style="display:flex;gap:12px;">
                            <button class="btn btn-sm" onclick="TfaSetup.copyRecoveryCodes()">
                                <span class="material-symbols-rounded" style="vertical-align:middle;margin-right:4px;font-size:16px;">content_copy</span>
                                Codes kopieren
                            </button>
                            <button class="btn btn-sm" onclick="TfaSetup.downloadRecoveryCodes()">
                                <span class="material-symbols-rounded" style="vertical-align:middle;margin-right:4px;font-size:16px;">download</span>
                                Als Datei speichern
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Disable Section (when 2FA is active) -->
        <div class="disable-section" id="disableSection">
            <h3>
                <span class="material-symbols-rounded" style="vertical-align:middle;margin-right:4px;font-size:20px;">remove_moderator</span>
                2FA deaktivieren
            </h3>
            <p>
                Wenn Sie die Zwei-Faktor-Authentifizierung deaktivieren, wird Ihr Konto nur noch
                durch Ihr Passwort geschützt. Bestätigen Sie mit Ihrem aktuellen Passwort.
            </p>
            <div class="password-confirm">
                <input type="password" id="disablePassword" placeholder="Aktuelles Passwort">
                <button class="btn" style="background:var(--error);color:white;" onclick="TfaSetup.disableTfa()" id="disableBtn">
                    Deaktivieren
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
const TfaSetup = {
    secret: '',
    recoveryCodes: [],

    async startSetup() {
        const btn = document.getElementById('startSetupBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-rounded spinning" style="vertical-align:middle;margin-right:8px;">sync</span> Wird geladen...';

        try {
            const res = await fetch('api/two_factor.php?action=setup');
            const data = await res.json();

            if (data.success) {
                this.secret = data.data.secret;
                this.recoveryCodes = data.data.recovery_codes;

                document.getElementById('qrCodeImage').src = data.data.qr_code_url;
                document.getElementById('secretKeyDisplay').textContent = data.data.secret;
                document.getElementById('enableSection').style.display = 'none';
                document.getElementById('setupSteps').classList.add('active');
                document.getElementById('verifyCodeInput').focus();
            } else {
                this.showMessage(data.error || 'Fehler beim Setup.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-rounded" style="vertical-align:middle;margin-right:8px;">add_moderator</span> 2FA aktivieren';
            }
        } catch (e) {
            this.showMessage('Verbindungsfehler. Bitte erneut versuchen.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-rounded" style="vertical-align:middle;margin-right:8px;">add_moderator</span> 2FA aktivieren';
        }
    },

    async enableTfa() {
        const code = document.getElementById('verifyCodeInput').value.trim();
        if (code.length !== 6) {
            this.showMessage('Bitte vollständigen 6-stelligen Code eingeben.', 'error');
            return;
        }

        const btn = document.getElementById('enableBtn');
        btn.disabled = true;
        btn.textContent = 'Wird verifiziert...';

        try {
            const formData = new FormData();
            formData.append('code', code);

            const res = await fetch('api/two_factor.php?action=enable', {
                method: 'POST',
                body: formData,
            });
            const data = await res.json();

            if (data.success) {
                this.showMessage('2FA wurde erfolgreich aktiviert!', 'success');

                // Show recovery codes
                document.getElementById('recoveryStep').style.display = 'block';
                const grid = document.getElementById('recoveryCodes');
                grid.innerHTML = this.recoveryCodes.map(c =>
                    `<div class="recovery-code-item">${c}</div>`
                ).join('');

                // Update status card
                const card = document.getElementById('statusCard');
                card.className = 'tfa-status-card enabled';
                card.querySelector('.tfa-status-icon .material-symbols-rounded').textContent = 'verified_user';
                document.getElementById('statusTitle').textContent = '2FA ist aktiviert';
                document.getElementById('statusDesc').textContent = 'Ihr Konto ist mit Zwei-Faktor-Authentifizierung geschützt.';

                // Hide verify input
                btn.style.display = 'none';
                document.getElementById('verifyCodeInput').style.display = 'none';
            } else {
                this.showMessage(data.error || 'Ungültiger Code.', 'error');
                btn.disabled = false;
                btn.textContent = 'Aktivieren';
            }
        } catch (e) {
            this.showMessage('Verbindungsfehler.', 'error');
            btn.disabled = false;
            btn.textContent = 'Aktivieren';
        }
    },

    async disableTfa() {
        const password = document.getElementById('disablePassword').value;
        if (!password) {
            this.showMessage('Bitte Passwort eingeben.', 'error');
            return;
        }

        const btn = document.getElementById('disableBtn');
        btn.disabled = true;
        btn.textContent = 'Wird deaktiviert...';

        try {
            const formData = new FormData();
            formData.append('password', password);

            const res = await fetch('api/two_factor.php?action=disable', {
                method: 'POST',
                body: formData,
            });
            const data = await res.json();

            if (data.success) {
                this.showMessage('2FA wurde deaktiviert.', 'success');
                // Reload to show updated state
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showMessage(data.error || 'Deaktivierung fehlgeschlagen.', 'error');
                btn.disabled = false;
                btn.textContent = 'Deaktivieren';
            }
        } catch (e) {
            this.showMessage('Verbindungsfehler.', 'error');
            btn.disabled = false;
            btn.textContent = 'Deaktivieren';
        }
    },

    copySecret() {
        navigator.clipboard.writeText(this.secret).then(() => {
            this.showMessage('Schlüssel in die Zwischenablage kopiert.', 'success');
        });
    },

    copyRecoveryCodes() {
        const text = this.recoveryCodes.join('\n');
        navigator.clipboard.writeText(text).then(() => {
            this.showMessage('Recovery-Codes in die Zwischenablage kopiert.', 'success');
        });
    },

    downloadRecoveryCodes() {
        const text = 'Admin Panel - Recovery Codes\n' +
            '================================\n' +
            'Datum: ' + new Date().toLocaleString('de-DE') + '\n\n' +
            this.recoveryCodes.join('\n') + '\n\n' +
            'Jeder Code kann nur einmal verwendet werden.\nBewahren Sie diese Codes sicher auf!';

        const blob = new Blob([text], { type: 'text/plain' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'recovery-codes.txt';
        a.click();
        URL.revokeObjectURL(a.href);
    },

    showMessage(text, type) {
        const el = document.getElementById('tfaMessage');
        el.textContent = text;
        el.className = 'tfa-message ' + type;
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        if (type === 'success') {
            setTimeout(() => { el.style.display = 'none'; }, 5000);
        }
    }
};

// Allow Enter key to trigger verify
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('verifyCodeInput');
    if (input) {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                TfaSetup.enableTfa();
            }
        });
    }

    const pwInput = document.getElementById('disablePassword');
    if (pwInput) {
        pwInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                TfaSetup.disableTfa();
            }
        });
    }
});
</script>
