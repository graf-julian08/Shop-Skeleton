<?php
/**
 * Run auth security migration
 * Usage: php database/migrations/run_migration.php
 */

require_once __DIR__ . '/../../admin/includes/Database.php';
require_once __DIR__ . '/../../admin/config.php';

Database::configure($database);

echo "=== Running Auth Security Migration ===\n\n";

try {
    // Create auth_2fa_codes table
    echo "1. Creating auth_2fa_codes table...\n";
    Database::query("
        CREATE TABLE IF NOT EXISTS auth_2fa_codes (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            admin_user_id BIGINT UNSIGNED NOT NULL,
            code VARCHAR(6) NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            is_used TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
            INDEX idx_2fa_lookup (admin_user_id, code, expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ auth_2fa_codes created\n";

    // Create auth_rate_limits table
    echo "2. Creating auth_rate_limits table...\n";
    Database::query("
        CREATE TABLE IF NOT EXISTS auth_rate_limits (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            ip_address VARCHAR(45) NOT NULL,
            action_type VARCHAR(50) NOT NULL,
            attempts INT UNSIGNED DEFAULT 1,
            first_attempt_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_attempt_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            blocked_until TIMESTAMP NULL,
            UNIQUE KEY uk_rate_limit (ip_address, action_type),
            INDEX idx_rate_cleanup (last_attempt_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   ✓ auth_rate_limits created\n";

    echo "\n=== Migration Complete ===\n";
}
catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
