<?php
/**
 * Authentication Handler
 * Manages login, logout, session, and access control
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
        
        // Load user from session if exists
        if (isset($_SESSION['admin_user_id'])) {
            self::$currentUser = self::loadUser($_SESSION['admin_user_id']);
            if (self::$currentUser) {
                self::loadPermissions();
            }
        }
    }
    
    /**
     * Attempt login with email and password
     */
    public static function attempt(string $email, string $password): bool {
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
        
        // Set session
        $_SESSION['admin_user_id'] = $user['id'];
        self::$currentUser = $user;
        self::loadPermissions();
        
        return true;
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
