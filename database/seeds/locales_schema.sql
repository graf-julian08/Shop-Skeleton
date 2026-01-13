-- Localization System v2: Shop Locales Table
-- Combines language, currency, and regional formats into one cohesive system

-- Create shop_locales table (replaces separate languages/currencies concept)
CREATE TABLE IF NOT EXISTS shop_locales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shop_id INT UNSIGNED NOT NULL DEFAULT 1,
    code VARCHAR(10) NOT NULL COMMENT 'e.g. de_DE, en_US',
    language_code VARCHAR(5) NOT NULL COMMENT 'e.g. de, en, fr',
    language_name VARCHAR(50) NOT NULL COMMENT 'e.g. German, English',
    language_native VARCHAR(50) NOT NULL COMMENT 'e.g. Deutsch, English',
    country_code VARCHAR(2) NOT NULL COMMENT 'e.g. DE, US',
    country_name VARCHAR(50) NOT NULL COMMENT 'e.g. Germany, United States',
    currency_code VARCHAR(3) NOT NULL COMMENT 'e.g. EUR, USD',
    currency_symbol VARCHAR(10) NOT NULL COMMENT 'e.g. €, $',
    currency_position ENUM('before', 'after') DEFAULT 'before',
    decimal_separator CHAR(1) DEFAULT '.',
    thousands_separator CHAR(1) DEFAULT ',',
    date_format VARCHAR(20) DEFAULT 'd.m.Y',
    time_format VARCHAR(20) DEFAULT 'H:i',
    timezone VARCHAR(50) DEFAULT 'Europe/Berlin',
    is_rtl TINYINT(1) DEFAULT 0,
    is_default TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_shop_locale (shop_id, code),
    INDEX idx_shop_active (shop_id, is_active),
    INDEX idx_language (language_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add new columns to translations table for tracking auto vs custom translations
-- Safe approach - check if columns exist first
SET @dbname = DATABASE();
SET @tablename = 'translations';

-- Add is_auto_translated column if not exists
SET @columnname = 'is_auto_translated';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TINYINT(1) DEFAULT 0')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add is_custom column if not exists
SET @columnname = 'is_custom';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TINYINT(1) DEFAULT 0')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Insert default German (Germany) locale
INSERT INTO shop_locales (shop_id, code, language_code, language_name, language_native, country_code, country_name, currency_code, currency_symbol, currency_position, decimal_separator, thousands_separator, date_format, time_format, timezone, is_default, is_active)
VALUES (1, 'de_DE', 'de', 'German', 'Deutsch', 'DE', 'Germany', 'EUR', '€', 'after', ',', '.', 'd.m.Y', 'H:i', 'Europe/Berlin', 1, 1)
ON DUPLICATE KEY UPDATE is_default = 1;
