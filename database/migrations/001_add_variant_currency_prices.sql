-- Migration: Add variant_currency_prices table
-- Date: 2026-01-15

CREATE TABLE IF NOT EXISTS variant_currency_prices (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    variant_id BIGINT UNSIGNED NOT NULL,
    currency_code VARCHAR(10) NOT NULL,
    price DECIMAL(15,4) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    UNIQUE KEY uk_variant_currency (variant_id, currency_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
