<?php
/**
 * Two-Factor Authentication - Verification Page
 * Standalone page (no admin layout, like login)
 * Shown after email/password login when 2FA is enabled
 */

// If not in 2FA pending state, redirect
if (!Auth::is2faPending()) {
    header('Location: ?page=login');
    exit;
}

// If already fully logged in, go to dashboard
if (Auth::check()) {
    header('Location: ?page=dashboard');
    exit;
}

$error = '';
$showRecovery = isset($_GET['recovery']);

// Handle form submission (server-side fallback)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    
    if (empty($code)) {
        $error = 'Bitte geben Sie den Code ein.';
    } elseif (Auth::verify2fa($code)) {
        header('Location: ?page=dashboard');
        exit;
    } else {
        $error = 'Ungültiger Code. Bitte versuchen Sie es erneut.';
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zwei-Faktor-Verifizierung - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
        }

        .tfa-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .tfa-card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            padding: 40px;
            box-shadow: var(--shadow-lg);
        }

        .tfa-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .tfa-header .icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.15), rgba(124, 58, 237, 0.05));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .tfa-header .icon-wrapper .material-symbols-rounded {
            font-size: 32px;
            color: var(--accent);
        }

        .tfa-header h1 {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .tfa-header p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .tfa-form .form-group {
            margin-bottom: 20px;
        }

        .tfa-form .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .code-input-wrapper {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .code-digit {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--bg-input);
            color: var(--text-primary);
            transition: all 0.2s;
        }

        .code-digit:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        }

        .recovery-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 16px;
            font-family: monospace;
            letter-spacing: 2px;
            text-align: center;
            background: var(--bg-input);
            color: var(--text-primary);
            text-transform: uppercase;
        }

        .recovery-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        }

        /* Hidden actual input for form submission */
        .code-hidden {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .tfa-form .btn-verify {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
        }

        .tfa-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error);
            color: var(--error);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tfa-links {
            text-align: center;
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .tfa-links a {
            color: var(--accent);
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .tfa-links a:hover {
            text-decoration: underline;
        }

        .tfa-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: var(--text-muted);
        }
    </style>
</head>

<body>
    <div class="tfa-container">
        <div class="tfa-card">
            <div class="tfa-header">
                <div class="icon-wrapper">
                    <span class="material-symbols-rounded"><?= $showRecovery ? 'key' : 'security' ?></span>
                </div>
                <?php if ($showRecovery): ?>
                    <h1>Recovery-Code</h1>
                    <p>Geben Sie einen Ihrer Wiederherstellungs-Codes ein, um sich anzumelden.</p>
                <?php else: ?>
                    <h1>Zwei-Faktor-Verifizierung</h1>
                    <p>Geben Sie den 6-stelligen Code aus Ihrer Authenticator-App ein.</p>
                <?php endif; ?>
            </div>

            <?php if ($error): ?>
                <div class="tfa-error">
                    <span class="material-symbols-rounded" style="font-size:18px;">error</span>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form class="tfa-form" method="POST" action="?page=two_factor_verify<?= $showRecovery ? '&recovery' : '' ?>" id="tfaForm">
                <?php if ($showRecovery): ?>
                    <!-- Recovery Code Input -->
                    <div class="form-group">
                        <label class="form-label" for="recoveryCode">Wiederherstellungs-Code</label>
                        <input type="text" id="recoveryCode" name="code" class="recovery-input"
                            placeholder="XXXX-XXXX" maxlength="9" required autofocus
                            autocomplete="off">
                    </div>
                <?php else: ?>
                    <!-- TOTP Code Input (6 digit boxes) -->
                    <div class="form-group">
                        <label class="form-label">Authentifizierungs-Code</label>
                        <div class="code-input-wrapper">
                            <input type="text" class="code-digit" maxlength="1" data-index="0" inputmode="numeric" autocomplete="off" autofocus>
                            <input type="text" class="code-digit" maxlength="1" data-index="1" inputmode="numeric" autocomplete="off">
                            <input type="text" class="code-digit" maxlength="1" data-index="2" inputmode="numeric" autocomplete="off">
                            <input type="text" class="code-digit" maxlength="1" data-index="3" inputmode="numeric" autocomplete="off">
                            <input type="text" class="code-digit" maxlength="1" data-index="4" inputmode="numeric" autocomplete="off">
                            <input type="text" class="code-digit" maxlength="1" data-index="5" inputmode="numeric" autocomplete="off">
                        </div>
                        <!-- Hidden field that gets submitted -->
                        <input type="hidden" name="code" id="codeHidden">
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary btn-verify" id="verifyBtn">
                    <span class="material-symbols-rounded" style="vertical-align:middle;margin-right:8px;">verified</span>
                    Verifizieren
                </button>
            </form>

            <div class="tfa-links">
                <?php if ($showRecovery): ?>
                    <a href="?page=two_factor_verify">
                        <span class="material-symbols-rounded" style="font-size:16px;">arrow_back</span>
                        Zurück zur Code-Eingabe
                    </a>
                <?php else: ?>
                    <a href="?page=two_factor_verify&recovery">
                        <span class="material-symbols-rounded" style="font-size:16px;">key</span>
                        Recovery-Code verwenden
                    </a>
                <?php endif; ?>
                <a href="?page=login" onclick="fetch('?page=logout')">
                    <span class="material-symbols-rounded" style="font-size:16px;">logout</span>
                    Abmelden
                </a>
            </div>

            <div class="tfa-footer">
                &copy; <?= date('Y') ?> Admin Panel
            </div>
        </div>
    </div>

    <script>
        (function() {
            const digits = document.querySelectorAll('.code-digit');
            const hiddenInput = document.getElementById('codeHidden');
            const form = document.getElementById('tfaForm');

            if (digits.length === 0) return; // Recovery mode

            // Collect all digit values into hidden field
            function updateHidden() {
                let code = '';
                digits.forEach(d => code += d.value);
                hiddenInput.value = code;
                return code;
            }

            // Auto-focus next input on digit entry
            digits.forEach((digit, idx) => {
                digit.addEventListener('input', function(e) {
                    // Only allow digits
                    this.value = this.value.replace(/\D/g, '').slice(0, 1);
                    
                    if (this.value && idx < digits.length - 1) {
                        digits[idx + 1].focus();
                    }
                    
                    const code = updateHidden();
                    
                    // Auto-submit when all boxes are filled
                    if (code.length === 6) {
                        setTimeout(() => form.submit(), 150);
                    }
                });

                digit.addEventListener('keydown', function(e) {
                    // Backspace → go to previous
                    if (e.key === 'Backspace' && !this.value && idx > 0) {
                        digits[idx - 1].focus();
                        digits[idx - 1].value = '';
                    }
                    // Arrow keys
                    if (e.key === 'ArrowLeft' && idx > 0) {
                        digits[idx - 1].focus();
                    }
                    if (e.key === 'ArrowRight' && idx < digits.length - 1) {
                        digits[idx + 1].focus();
                    }
                });

                // Handle paste
                digit.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pasted = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
                    
                    pasted.split('').forEach((char, i) => {
                        if (digits[i]) {
                            digits[i].value = char;
                        }
                    });
                    
                    const code = updateHidden();
                    if (code.length === 6) {
                        setTimeout(() => form.submit(), 150);
                    } else if (digits[pasted.length]) {
                        digits[pasted.length].focus();
                    }
                });

                // Select all on focus
                digit.addEventListener('focus', function() {
                    this.select();
                });
            });

            // Prevent form submit if code incomplete
            form.addEventListener('submit', function(e) {
                const code = updateHidden();
                if (code.length !== 6) {
                    e.preventDefault();
                    digits[0].focus();
                }
            });
        })();
    </script>
</body>

</html>
