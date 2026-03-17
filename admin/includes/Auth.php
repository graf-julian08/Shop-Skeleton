<?php
/**
 * Authentication Handler
 * Manages login, logout, session, access control, and 2FA
 */

class Auth {
    private static ?array $currentUser = null;
    private static ?array $userPermissions = null;
    
    /**
     * Initialize session
     */
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Load user from session if exists (only if 2FA is not pending)
        if (isset($_SESSION['admin_user_id']) && empty($_SESSION['2fa_pending'])) {
            self::$currentUser = self::loadUser($_SESSION['admin_user_id']);
            if (self::$currentUser) {
                self::loadPermissions();
            }
        }
    }
    
    /**
     * Attempt login with email and password
     * Returns: 'success', '2fa_required', or false
     * 
     * @return string|bool 'success' if logged in, '2fa_required' if 2FA pending, false on failure
     */
    public static function attempt(string $email, string $password) {
        $user = Database::fetch(
            "SELECT * FROM admin_users WHERE email = ? AND is_active = 1",
            [$email]
        );
        
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        
        // Update last login
        Database::update('admin_users', [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $_SERVER['REMOTE_ADDR'] ?? null
        ], 'id = ?', [$user['id']]);
        
        // Check if 2FA is enabled
        if (!empty($user['two_factor_enabled']) && !empty($user['two_factor_secret'])) {
            // Set 2FA pending state — user is NOT fully logged in yet
            $_SESSION['2fa_pending'] = true;
            $_SESSION['2fa_user_id'] = $user['id'];
            unset($_SESSION['admin_user_id']);
            return '2fa_required';
        }
        
        // No 2FA — full login
        $_SESSION['admin_user_id'] = $user['id'];
        unset($_SESSION['2fa_pending'], $_SESSION['2fa_user_id']);
        self::$currentUser = $user;
        self::loadPermissions();
        
        return 'success';
    }
    
    /**
     * Verify 2FA code and complete login
     * 
     * @param string $code TOTP code or recovery code
     * @return bool True if verified and logged in
     */
    public static function verify2fa(string $code): bool {
        if (empty($_SESSION['2fa_pending']) || empty($_SESSION['2fa_user_id'])) {
            return false;
        }
        
        $userId = $_SESSION['2fa_user_id'];
        $user = Database::fetch(
            "SELECT * FROM admin_users WHERE id = ? AND is_active = 1",
            [$userId]
        );
        
        if (!$user || empty($user['two_factor_secret'])) {
            return false;
        }
        
        $verified = false;
        
        // Try TOTP code first
        if (TwoFactorAuth::verifyCode($user['two_factor_secret'], $code)) {
            $verified = true;
        }
        
        // Try recovery code if TOTP didn't match
        if (!$verified && !empty($user['two_factor_recovery_codes'])) {
            $recoveryCodes = json_decode($user['two_factor_recovery_codes'], true);
            if (is_array($recoveryCodes)) {
                $matchIndex = TwoFactorAuth::verifyRecoveryCode($code, $recoveryCodes);
                if ($matchIndex !== false) {
                    // Remove used recovery code
                    unset($recoveryCodes[$matchIndex]);
                    $recoveryCodes = array_values($recoveryCodes);
                    Database::update('admin_users', [
                        'two_factor_recovery_codes' => json_encode($recoveryCodes)
                    ], 'id = ?', [$userId]);
                    $verified = true;
                }
            }
        }
        
        if ($verified) {
            // Complete login
            $_SESSION['admin_user_id'] = $userId;
            unset($_SESSION['2fa_pending'], $_SESSION['2fa_user_id']);
            self::$currentUser = $user;
            self::loadPermissions();
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if 2FA verification is pending
     */
    public static function is2faPending(): bool {
        return !empty($_SESSION['2fa_pending']) && !empty($_SESSION['2fa_user_id']);
    }
    
    /**
     * Setup 2FA: Generate secret and recovery codes (before activation)
     * 
     * @return array ['secret' => ..., 'qr_url' => ..., 'recovery_codes' => [...]]
     */
    public static function setup2fa(): array {
        if (!self::check()) {
            return [];
        }
        
        $secret = TwoFactorAuth::generateSecret();
        $email = self::$currentUser['email'];
        $otpAuthUrl = TwoFactorAuth::getOtpAuthUrl($email, $secret);
        $qrCodeUrl = TwoFactorAuth::getQrCodeImageUrl($otpAuthUrl, 200);
        $recoveryCodes = TwoFactorAuth::generateRecoveryCodes(8);
        
        // Store secret temporarily in session until user verifies
        $_SESSION['2fa_setup_secret'] = $secret;
        $_SESSION['2fa_setup_recovery_codes'] = $recoveryCodes;
        
        return [
            'secret' => $secret,
            'otp_auth_url' => $otpAuthUrl,
            'qr_code_url' => $qrCodeUrl,
            'recovery_codes' => $recoveryCodes,
        ];
    }
    
    /**
     * Enable 2FA after verifying the first code
     * 
     * @param string $code TOTP code to verify setup
     * @return bool True if enabled successfully
     */
    public static function enable2fa(string $code): bool {
        if (!self::check()) {
            return false;
        }
        
        $secret = $_SESSION['2fa_setup_secret'] ?? null;
        $recoveryCodes = $_SESSION['2fa_setup_recovery_codes'] ?? null;
        
        if (!$secret || !$recoveryCodes) {
            return false;
        }
        
        // Verify the code with the new secret
        if (!TwoFactorAuth::verifyCode($secret, $code)) {
            return false;
        }
        
        // Save to database
        Database::update('admin_users', [
            'two_factor_enabled' => 1,
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
        ], 'id = ?', [self::$currentUser['id']]);
        
        // Clean up session
        unset($_SESSION['2fa_setup_secret'], $_SESSION['2fa_setup_recovery_codes']);
        
        // Update current user data
        self::$currentUser['two_factor_enabled'] = 1;
        self::$currentUser['two_factor_secret'] = $secret;
        
        return true;
    }
    
    /**
     * Disable 2FA (requires password confirmation)
     * 
     * @param string $password User's current password for confirmation
     * @return bool True if disabled successfully
     */
    public static function disable2fa(string $password): bool {
        if (!self::check()) {
            return false;
        }
        
        // Verify password
        if (!password_verify($password, self::$currentUser['password'])) {
            return false;
        }
        
        // Remove 2FA from database
        Database::update('admin_users', [
            'two_factor_enabled' => 0,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ], 'id = ?', [self::$currentUser['id']]);
        
        // Update current user data
        self::$currentUser['two_factor_enabled'] = 0;
        self::$currentUser['two_factor_secret'] = null;
        
        return true;
    }
    
    /**
     * Check if current user has 2FA enabled
     */
    public static function has2faEnabled(): bool {
        if (!self::check()) {
            return false;
        }
        return !empty(self::$currentUser['two_factor_enabled']);
    }
    
    /**
     * Logout current user
     */
    public static function logout(): void {
        self::$currentUser = null;
        self::$userPermissions = null;
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
    
    /**
     * Check if user is logged in
     */
    public static function check(): bool {
        return self::$currentUser !== null;
    }
    
    /**
     * Get current user
     */
    public static function user(): ?array {
        return self::$currentUser;
    }
    
    /**
     * Get current user ID
     */
    public static function id(): ?int {
        return self::$currentUser['id'] ?? null;
    }
    
    /**
     * Check if user has a specific permission
     */
    public static function can(string $permission): bool {
        if (!self::check()) {
            return false;
        }
        
        // Super admin has all permissions
        if (self::isSuperAdmin()) {
            return true;
        }
        
        return in_array($permission, self::$userPermissions ?? []);
    }
    
    /**
     * Check if user is super admin (role ID 1 or specific flag)
     */
    public static function isSuperAdmin(): bool {
        if (!self::check()) {
            return false;
        }
        
        // Check if user has the super admin role
        $hasSuperRole = Database::fetch(
            "SELECT 1 FROM admin_user_roles aur 
             JOIN roles r ON aur.role_id = r.id 
             WHERE aur.admin_user_id = ? AND r.is_system = 1 AND r.name = 'Super Admin'",
            [self::$currentUser['id']]
        );
        
        return $hasSuperRole !== null;
    }
    
    /**
     * Load user by ID
     */
    private static function loadUser(int $id): ?array {
        return Database::fetch(
            "SELECT * FROM admin_users WHERE id = ? AND is_active = 1",
            [$id]
        );
    }
    
    /**
     * Load user permissions from roles
     */
    private static function loadPermissions(): void {
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
    public static function roles(): array {
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
    public static function permissions(): array {
        return self::$userPermissions ?? [];
    }
    
    /**
     * Hash a password
     */
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Require authentication - redirect to login if not authenticated
     */
    public static function requireAuth(): void {
        // If 2FA is pending, redirect to verify page
        if (self::is2faPending()) {
            header('Location: ?page=two_factor_verify');
            exit;
        }
        
        if (!self::check()) {
            header('Location: ?page=login');
            exit;
        }
    }
    
    /**
     * Require specific permission - abort if not allowed
     */
    public static function requirePermission(string $permission): void {
        if (!self::can($permission)) {
            http_response_code(403);
            die('Access denied: You do not have permission to access this resource.');
        }
    }
}
