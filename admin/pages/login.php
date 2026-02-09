<?php /** Login Page */

// Handle login form submission
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Bitte E-Mail und Passwort eingeben.';
    } elseif (Auth::attempt($email, $password)) {
        // Redirect to dashboard on successful login
        header('Location: ?page=dashboard');
        exit;
    } else {
        $error = 'Ungültige Anmeldedaten.';
    }
}

// If already logged in, redirect to dashboard
if (Auth::check()) {
    header('Location: ?page=dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Panel</title>
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

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            padding: 40px;
            box-shadow: var(--shadow-lg);
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .login-form .form-group {
            margin-bottom: 20px;
        }

        .login-form .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .login-form .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 14px;
            background: var(--bg-input);
            color: var(--text-primary);
        }

        .login-form .form-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .login-form .btn-login {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
        }

        .login-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error);
            color: var(--error);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 14px;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: var(--text-muted);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <span class="material-symbols-rounded"
                    style="font-size:48px;color:var(--accent);margin-bottom:16px;display:block;">storefront</span>
                <h1>Admin Login</h1>
                <p>Melden Sie sich an, um fortzufahren</p>
            </div>

            <?php if ($error): ?>
                <div class="login-error">
                    <span class="material-symbols-rounded"
                        style="vertical-align:middle;margin-right:8px;font-size:18px;">error</span>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label class="form-label" for="email">E-Mail-Adresse</label>
                    <input type="email" id="email" name="email" class="form-input"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="admin@example.com" required
                        autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Passwort</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••"
                        required>
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    <span class="material-symbols-rounded" style="vertical-align:middle;margin-right:8px;">login</span>
                    Anmelden
                </button>
            </form>

            <div class="login-footer">
                &copy; <?= date('Y') ?> Admin Panel
            </div>
        </div>
    </div>
    <script>
        // Enter-Taste zum Login
        document.getElementById('loginForm').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.submit();
            }
        });
    </script>
</body>

</html>