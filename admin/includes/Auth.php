<?php
/**
 * ============================================
 * AUTHENTICATION HANDLER
 * ============================================
 * Sichere Admin-Authentifizierung mit:
 * - Fester Admin-E-Mail (keine Registrierung)
 * - bcrypt Passwort-Hashing (Kosten 12)
 * - E-Mail-basierte 2FA (6-stelliger Code, 5 Min Ablauf)
 * - Session-Validierung (pending_2fa → fully_verified)
 * - Rate-Limiting Integration
 * ============================================
 */

class Auth
{
    /**
     * Einzige erlaubte Admin-E-Mail – KEINE Registrierung möglich
     */
    private const ALLOWED_EMAIL = 'nevio.weishaupt@ksb-sg.ch';

    /**
     * bcrypt Kosten-Faktor (höher = sicherer, aber langsamer)
     */
    private const BCRYPT_COST = 12;

    /**
     * 2FA Code Gültigkeit in Minuten
     */
    private const CODE_EXPIRY_MINUTES = 5;

    private static ?array $currentUser = null;
    private static ?array $userPermissions = null;

    // =========================================
    // SESSION & INITIALIZATION
    // =========================================

    /**
     * Initialize session and load user if fully verified
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Sichere Session-Konfiguration
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_strict_mode', '1');
            session_start();
        }

        // Nur vollständig verifizierte Sessions laden
        if (isset($_SESSION['admin_user_id']) && self::getAuthStatus() === 'fully_verified') {
            self::$currentUser = self::loadUser($_SESSION['admin_user_id']);
            if (self::$currentUser) {
                self::loadPermissions();
            }
            else {
                // User existiert nicht mehr oder ist deaktiviert
                self::destroySession();
            }
        }
    }

    // =========================================
    // LOGIN FLOW: STEP 1 – PASSWORD
    // =========================================

    /**
     * Schritt 1: Passwort-Prüfung
     * 
     * Prüft E-Mail (muss ALLOWED_EMAIL sein) und Passwort.
     * Bei Erfolg wird Session-Status auf 'pending_2fa' gesetzt.
     * 
     * @return array ['success' => bool, 'error' => string|null, 'step' => string]
     */
    public static function attempt(string $email, string $password): array
    {
        // 1. E-Mail-Einschränkung prüfen
        if (strtolower(trim($email)) !== strtolower(self::ALLOWED_EMAIL)) {
            return [
                'success' => false,
                'error' => 'Zugriff verweigert. Nur die vordefinierte Admin-E-Mail ist zugelassen.',
                'step' => 'password',
            ];
        }

        // 2. User aus DB laden
        $user = Database::fetch(
            "SELECT * FROM admin_users WHERE email = ? AND is_active = 1",
        [strtolower(trim($email))]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'error' => 'Ungültige Anmeldedaten.',
                'step' => 'password',
            ];
        }

        // 3. Passwort-Hash upgraden falls nötig (z.B. von PASSWORD_DEFAULT auf bcrypt mit Kosten 12)
        if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST])) {
            Database::update('admin_users', [
                'password' => self::hashPassword($password),
            ], 'id = ?', [$user['id']]);
        }

        // 4. Session auf pending_2fa setzen (NICHT fully_verified!)
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['auth_status'] = 'pending_2fa';
        $_SESSION['auth_email'] = $user['email'];

        // 5. Login-Timestamp aktualisieren
        Database::update('admin_users', [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ], 'id = ?', [$user['id']]);

        return [
            'success' => true,
            'error' => null,
            'step' => '2fa',
        ];
    }

    // =========================================
    // LOGIN FLOW: STEP 2 – 2FA CODE
    // =========================================

    /**
     * Generiert einen kryptografisch sicheren 6-stelligen 2FA-Code,
     * speichert ihn in der DB und sendet ihn per E-Mail.
     * 
     * @return array ['success' => bool, 'error' => string|null]
     */
    public static function generate2FACode(): array
    {
        $userId = $_SESSION['admin_user_id'] ?? null;
        $email = $_SESSION['auth_email'] ?? null;

        if (!$userId || !$email) {
            return ['success' => false, 'error' => 'Ungültige Session. Bitte erneut anmelden.'];
        }

        // Alle vorherigen unbenutzten Codes für diesen User invalidieren
        Database::update('auth_2fa_codes', [
            'is_used' => 1,
        ], 'admin_user_id = ? AND is_used = 0', [$userId]);

        // Kryptografisch sicheren 6-stelligen Code generieren
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Code in DB speichern mit Ablaufdatum
        $expiresAt = date('Y-m-d H:i:s', time() + (self::CODE_EXPIRY_MINUTES * 60));

        Database::insert('auth_2fa_codes', [
            'admin_user_id' => $userId,
            'code' => $code,
            'expires_at' => $expiresAt,
            'is_used' => 0,
        ]);

        // E-Mail mit Code senden
        $result = self::send2FAEmail($email, $code);

        if (!$result['success']) {
            return ['success' => false, 'error' => 'Fehler beim E-Mail-Versand. Bitte erneut versuchen.'];
        }

        return ['success' => true, 'error' => null];
    }

    /**
     * Verifiziert den eingegebenen 2FA-Code.
     * Bei Erfolg wird Session-Status auf 'fully_verified' gesetzt.
     * 
     * @return array ['success' => bool, 'error' => string|null]
     */
    public static function verify2FA(string $code): array
    {
        $userId = $_SESSION['admin_user_id'] ?? null;

        if (!$userId || self::getAuthStatus() !== 'pending_2fa') {
            return ['success' => false, 'error' => 'Ungültige Session. Bitte erneut anmelden.'];
        }

        // Code aus DB verifizieren (unbenutzt + nicht abgelaufen)
        $record = Database::fetch(
            "SELECT * FROM auth_2fa_codes 
             WHERE admin_user_id = ? 
               AND code = ? 
               AND is_used = 0 
               AND expires_at > NOW()
             ORDER BY created_at DESC 
             LIMIT 1",
        [$userId, $code]
        );

        if (!$record) {
            return ['success' => false, 'error' => 'Ungültiger oder abgelaufener Code.'];
        }

        // Code als benutzt markieren
        Database::update('auth_2fa_codes', [
            'is_used' => 1,
        ], 'id = ?', [$record['id']]);

        // Session als vollständig verifiziert markieren
        $_SESSION['auth_status'] = 'fully_verified';

        // Session-ID regenerieren (Security: Session Fixation Prevention)
        session_regenerate_id(true);

        // User laden
        self::$currentUser = self::loadUser($userId);
        if (self::$currentUser) {
            self::loadPermissions();
        }

        return ['success' => true, 'error' => null];
    }

    // =========================================
    // 2FA EMAIL
    // =========================================

    /**
     * Sendet den 2FA-Code per E-Mail
     */
    private static function send2FAEmail(string $email, string $code): array
    {
        $subject = 'Ihr Verifizierungscode – Admin Login';

        $htmlBody = '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#0f172a;font-family:\'Segoe UI\',system-ui,-apple-system,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#0f172a;padding:40px 20px;">
                <tr><td align="center">
                    <table width="480" cellpadding="0" cellspacing="0" style="background:#1e293b;border-radius:16px;border:1px solid #334155;overflow:hidden;">
                        <!-- Header -->
                        <tr><td style="padding:32px 40px 20px;text-align:center;">
                            <div style="width:56px;height:56px;background:linear-gradient(135deg,#7c3aed,#6366f1);border-radius:14px;margin:0 auto 16px;line-height:56px;font-size:28px;">🔐</div>
                            <h1 style="color:#f8fafc;font-size:22px;margin:0 0 6px;">Verifizierungscode</h1>
                            <p style="color:#94a3b8;font-size:14px;margin:0;">Ihr Sicherheitscode für den Admin-Login</p>
                        </td></tr>
                        <!-- Code -->
                        <tr><td style="padding:10px 40px 24px;text-align:center;">
                            <div style="background:#0f172a;border:2px solid #7c3aed;border-radius:12px;padding:20px;letter-spacing:12px;font-size:36px;font-weight:700;color:#e2e8f0;font-family:\'Courier New\',monospace;">' . htmlspecialchars($code) . '</div>
                        </td></tr>
                        <!-- Info -->
                        <tr><td style="padding:0 40px 32px;">
                            <div style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);border-radius:8px;padding:12px 16px;">
                                <p style="color:#fbbf24;font-size:13px;margin:0;">⏱ Dieser Code ist <strong>' . self::CODE_EXPIRY_MINUTES . ' Minuten</strong> gültig.</p>
                            </div>
                            <p style="color:#64748b;font-size:12px;margin:16px 0 0;text-align:center;">Falls Sie diesen Login nicht angefordert haben, ignorieren Sie diese E-Mail.</p>
                        </td></tr>
                    </table>
                </td></tr>
            </table>
        </body>
        </html>';

        $textBody = "Ihr Verifizierungscode: {$code}\n\nDieser Code ist " . self::CODE_EXPIRY_MINUTES . " Minuten gültig.\n\nFalls Sie diesen Login nicht angefordert haben, ignorieren Sie diese E-Mail.";

        try {
            require_once __DIR__ . '/Mailer.php';
            return Mailer::send($email, $subject, $htmlBody, $textBody);
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => 'Mailer-Fehler: ' . $e->getMessage()];
        }
    }

    // =========================================
    // SESSION MANAGEMENT
    // =========================================

    /**
     * Get current auth status from session
     */
    public static function getAuthStatus(): ?string
    {
        return $_SESSION['auth_status'] ?? null;
    }

    /**
     * Check if session is fully verified (password + 2FA)
     */
    public static function isFullyVerified(): bool
    {
        return self::getAuthStatus() === 'fully_verified' && self::$currentUser !== null;
    }

    /**
     * Check if user is logged in (fully verified)
     */
    public static function check(): bool
    {
        return self::isFullyVerified();
    }

    /**
     * Logout current user and destroy session
     */
    public static function logout(): void
    {
        self::$currentUser = null;
        self::$userPermissions = null;
        self::destroySession();
    }

    /**
     * Destroy session completely
     */
    private static function destroySession(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    // =========================================
    // USER DATA
    // =========================================

    /**
     * Get current user
     */
    public static function user(): ?array
    {
        return self::$currentUser;
    }

    /**
     * Get current user ID
     */
    public static function id(): ?int
    {
        return self::$currentUser['id'] ?? null;
    }

    /**
     * Load user by ID
     */
    private static function loadUser(int $id): ?array
    {
        return Database::fetch(
            "SELECT * FROM admin_users WHERE id = ? AND is_active = 1",
        [$id]
        );
    }

    // =========================================
    // PERMISSIONS / RBAC
    // =========================================

    /**
     * Check if user has a specific permission
     */
    public static function can(string $permission): bool
    {
        if (!self::check()) {
            return false;
        }

        if (self::isSuperAdmin()) {
            return true;
        }

        return in_array($permission, self::$userPermissions ?? []);
    }

    /**
     * Check if user is super admin
     */
    public static function isSuperAdmin(): bool
    {
        if (!self::check()) {
            return false;
        }

        $hasSuperRole = Database::fetch(
            "SELECT 1 FROM admin_user_roles aur 
             JOIN roles r ON aur.role_id = r.id 
             WHERE aur.admin_user_id = ? AND r.is_system = 1 AND r.name = 'Super Admin'",
        [self::$currentUser['id']]
        );

        return $hasSuperRole !== null;
    }

    /**
     * Load user permissions from roles
     */
    private static function loadPermissions(): void
    {
        if (!self::$currentUser) {
            self::$userPermissions = [];
            return;
        }

        $permissions = Database::fetchAll(
            "SELECT DISTINCT p.key_name 
             FROM permissions p
             JOIN role_permissions rp ON p.id = rp.permission_id
             JOIN admin_user_roles aur ON rp.role_id = aur.role_id
             WHERE aur.admin_user_id = ?",
        [self::$currentUser['id']]
        );

        self::$userPermissions = array_column($permissions, 'key_name');
    }

    /**
     * Get all user roles
     */
    public static function roles(): array
    {
        if (!self::$currentUser) {
            return [];
        }

        return Database::fetchAll(
            "SELECT r.* FROM roles r
             JOIN admin_user_roles aur ON r.id = aur.role_id
             WHERE aur.admin_user_id = ?",
        [self::$currentUser['id']]
        );
    }

    /**
     * Get all user permissions
     */
    public static function permissions(): array
    {
        return self::$userPermissions ?? [];
    }

    // =========================================
    // PASSWORD HASHING
    // =========================================

    /**
     * Hash a password with bcrypt (Kosten 12)
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST]);
    }

    // =========================================
    // AUTH GUARDS (MIDDLEWARE)
    // =========================================

    /**
     * Require full authentication (password + 2FA verified)
     * Redirects to login if not fully verified
     */
    public static function requireFullAuth(): void
    {
        if (!self::isFullyVerified()) {
            header('Location: ?page=login');
            exit;
        }
    }

    /**
     * Require authentication (alias for requireFullAuth)
     */
    public static function requireAuth(): void
    {
        self::requireFullAuth();
    }

    /**
     * Require specific permission
     */
    public static function requirePermission(string $permission): void
    {
        if (!self::can($permission)) {
            http_response_code(403);
            die('Zugriff verweigert: Sie haben keine Berechtigung für diese Ressource.');
        }
    }

    /**
     * Get the allowed admin email (for display purposes)
     */
    public static function getAllowedEmail(): string
    {
        return self::ALLOWED_EMAIL;
    }
}
