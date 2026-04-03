<?php
/**
 * Migration: Create Collaborations Tables
 * Run: php database/migrate_collaborations.php
 */

require_once __DIR__ . '/../admin/config.php';
require_once __DIR__ . '/../admin/includes/Database.php';

Database::configure($database);

echo "=== Collaborations Migration ===\n\n";

// 1. Create collaborations table
$sql1 = "
CREATE TABLE IF NOT EXISTS collaborations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL DEFAULT 1,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    short_description TEXT,
    description LONGTEXT,
    video_url VARCHAR(500) DEFAULT NULL COMMENT 'YouTube/Vimeo embed URL',
    status ENUM('draft','active','archived') NOT NULL DEFAULT 'draft',
    is_featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_shop_status (shop_id, status),
    INDEX idx_slug (slug),
    UNIQUE KEY uq_shop_slug (shop_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// 2. Create collaboration_images table
$sql2 = "
CREATE TABLE IF NOT EXISTS collaboration_images (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    collaboration_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_collab (collaboration_id),
    FOREIGN KEY (collaboration_id) REFERENCES collaborations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    Database::query($sql1);
    echo "✅ Table 'collaborations' created/verified.\n";
} catch (Exception $e) {
    echo "❌ Error creating 'collaborations': " . $e->getMessage() . "\n";
}

try {
    Database::query($sql2);
    echo "✅ Table 'collaboration_images' created/verified.\n";
} catch (Exception $e) {
    echo "❌ Error creating 'collaboration_images': " . $e->getMessage() . "\n";
}

echo "\n=== Migration Complete ===\n";
