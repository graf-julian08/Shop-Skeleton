-- =====================================================================
-- COMPLETE E-COMMERCE DATABASE SCHEMA
-- Version: 1.0.0
-- Generated for: Universal Admin Panel (B2B, B2C, Digital, Physical, Subscriptions)
-- =====================================================================
-- This schema supports:
-- - Multi-shop / Multi-tenant architecture
-- - Physical products, Digital products, Subscriptions, Bundles
-- - Complete order lifecycle (fulfillment, returns, cancellations)
-- - Multi-currency, Multi-language
-- - Full RBAC (Role-Based Access Control)
-- - Marketing, Analytics, Reporting
-- - Inventory management with warehouse support
-- - Tax calculations per region
-- - Flexible settings system (EAV-pattern)
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
-- SECTION 1: CORE SHOP / MULTI-TENANT
-- =====================================================================

CREATE TABLE shops (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Unique shop identifier',
    name VARCHAR(255) NOT NULL,
    description TEXT,
    domain VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    
    -- Regional Settings
    default_locale VARCHAR(10) DEFAULT 'de_DE',
    default_currency VARCHAR(10) DEFAULT 'EUR',
    timezone VARCHAR(50) DEFAULT 'Europe/Berlin',
    date_format VARCHAR(20) DEFAULT 'DD.MM.YYYY',
    time_format VARCHAR(20) DEFAULT '24h',
    weight_unit ENUM('kg', 'g', 'lb', 'oz') DEFAULT 'kg',
    dimension_unit ENUM('cm', 'mm', 'in') DEFAULT 'cm',
    
    -- Status
    is_active TINYINT(1) DEFAULT 1,
    maintenance_mode TINYINT(1) DEFAULT 0,
    maintenance_message TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_shop_code (code),
    INDEX idx_shop_domain (domain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Shop Design Settings (Colors, Logo, Typography)
CREATE TABLE shop_design (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    
    -- Colors
    color_primary VARCHAR(7) DEFAULT '#7c3aed',
    color_secondary VARCHAR(7) DEFAULT '#1a1a1a',
    color_accent VARCHAR(7) DEFAULT '#22c55e',
    color_text VARCHAR(7) DEFAULT '#ffffff',
    color_background VARCHAR(7) DEFAULT '#ffffff',
    color_surface VARCHAR(7) DEFAULT '#f5f5f5',
    
    -- Branding
    logo_path VARCHAR(500),
    logo_dark_path VARCHAR(500),
    favicon_path VARCHAR(500),
    
    -- Typography
    font_heading VARCHAR(100) DEFAULT 'Inter',
    font_body VARCHAR(100) DEFAULT 'Inter',
    
    -- Layout
    header_style ENUM('default', 'transparent', 'sticky') DEFAULT 'default',
    footer_style ENUM('simple', 'columns', 'minimal') DEFAULT 'columns',
    
    -- Custom CSS
    custom_css TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_shop_design (shop_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 2: LOCALIZATION (Languages, Currencies, Translations)
-- =====================================================================

CREATE TABLE languages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(10) NOT NULL COMMENT 'ISO code e.g. de_DE, en_US',
    name VARCHAR(100) NOT NULL,
    native_name VARCHAR(100),
    is_default TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_shop_language (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE currencies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(10) NOT NULL COMMENT 'ISO code e.g. EUR, USD',
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(10) NOT NULL,
    exchange_rate DECIMAL(12,6) DEFAULT 1.000000,
    decimal_places TINYINT DEFAULT 2,
    decimal_separator VARCHAR(1) DEFAULT ',',
    thousands_separator VARCHAR(1) DEFAULT '.',
    symbol_position ENUM('before', 'after') DEFAULT 'after',
    is_default TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_shop_currency (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE translations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    locale VARCHAR(10) NOT NULL,
    translation_group VARCHAR(100) NOT NULL COMMENT 'e.g. cart, checkout, account',
    translation_key VARCHAR(200) NOT NULL,
    translation_value TEXT NOT NULL,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_translation (shop_id, locale, translation_group, translation_key),
    INDEX idx_translation_lookup (shop_id, locale, translation_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 3: USERS / AUTHENTICATION / RBAC
-- =====================================================================

-- Admin Users (Backend Users)
CREATE TABLE admin_users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED,
    
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    avatar_path VARCHAR(500),
    
    -- Security
    is_active TINYINT(1) DEFAULT 1,
    email_verified_at TIMESTAMP NULL,
    two_factor_enabled TINYINT(1) DEFAULT 0,
    two_factor_secret VARCHAR(255),
    two_factor_recovery_codes TEXT,
    
    -- Session tracking
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45),
    remember_token VARCHAR(100),
    
    -- Preferences
    locale VARCHAR(10) DEFAULT 'de_DE',
    timezone VARCHAR(50) DEFAULT 'Europe/Berlin',
    dark_mode TINYINT(1) DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE SET NULL,
    UNIQUE KEY uk_admin_email (email),
    INDEX idx_admin_shop (shop_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles
CREATE TABLE roles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_system TINYINT(1) DEFAULT 0 COMMENT 'System roles cannot be deleted',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permissions
CREATE TABLE permissions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(100) NOT NULL UNIQUE COMMENT 'e.g. products.view, orders.edit',
    display_name VARCHAR(200) NOT NULL,
    description TEXT,
    permission_group VARCHAR(100) COMMENT 'e.g. catalog, orders, customers',
    
    INDEX idx_permission_group (permission_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role-Permission Mapping
CREATE TABLE role_permissions (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User-Role Mapping
CREATE TABLE admin_user_roles (
    admin_user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (admin_user_id, role_id),
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 4: CUSTOMERS (Shop Customers)
-- =====================================================================

CREATE TABLE customer_groups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL,
    description TEXT,
    discount_percent DECIMAL(5,2) DEFAULT 0.00,
    is_default TINYINT(1) DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_group_code (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    customer_group_id BIGINT UNSIGNED,
    
    -- Personal Info
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255),
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(50),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    
    -- B2B Fields
    company_name VARCHAR(255),
    vat_id VARCHAR(50),
    tax_exempt TINYINT(1) DEFAULT 0,
    
    -- Status & Security
    is_active TINYINT(1) DEFAULT 1,
    is_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(255),
    password_reset_token VARCHAR(255),
    password_reset_expires TIMESTAMP NULL,
    
    -- Tracking
    last_login_at TIMESTAMP NULL,
    last_order_at TIMESTAMP NULL,
    
    -- Preferences
    subscribed_to_newsletter TINYINT(1) DEFAULT 0,
    preferred_locale VARCHAR(10),
    preferred_currency VARCHAR(10),
    
    -- Stats (denormalized for performance)
    orders_count INT UNSIGNED DEFAULT 0,
    total_spent DECIMAL(15,4) DEFAULT 0.0000,
    loyalty_points INT UNSIGNED DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_group_id) REFERENCES customer_groups(id) ON DELETE SET NULL,
    UNIQUE KEY uk_customer_email (shop_id, email),
    INDEX idx_customer_group (customer_group_id),
    INDEX idx_customer_lastname (last_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_addresses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    customer_id BIGINT UNSIGNED NOT NULL,
    
    address_type ENUM('billing', 'shipping', 'both') DEFAULT 'both',
    is_default_billing TINYINT(1) DEFAULT 0,
    is_default_shipping TINYINT(1) DEFAULT 0,
    
    -- Address Fields
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    company VARCHAR(255),
    address_line_1 VARCHAR(255) NOT NULL,
    address_line_2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    postal_code VARCHAR(20) NOT NULL,
    country_code VARCHAR(2) NOT NULL,
    phone VARCHAR(50),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_address_customer (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 5: CATALOG - CATEGORIES
-- =====================================================================

CREATE TABLE categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    
    -- Display
    image_path VARCHAR(500),
    banner_path VARCHAR(500),
    
    -- SEO
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords VARCHAR(255),
    
    -- Status & Sorting
    is_active TINYINT(1) DEFAULT 1,
    is_visible_in_menu TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    
    -- Hierarchy (for nested set or materialized path)
    level INT UNSIGNED DEFAULT 0,
    path VARCHAR(500) COMMENT 'Materialized path e.g. 1/5/12',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    UNIQUE KEY uk_category_slug (shop_id, slug),
    INDEX idx_category_parent (parent_id),
    INDEX idx_category_path (path)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 6: CATALOG - ATTRIBUTES
-- =====================================================================

CREATE TABLE attributes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    
    code VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('text', 'textarea', 'number', 'select', 'multiselect', 'boolean', 'color', 'date', 'price') NOT NULL,
    
    -- Configuration
    is_required TINYINT(1) DEFAULT 0,
    is_unique TINYINT(1) DEFAULT 0,
    is_filterable TINYINT(1) DEFAULT 0,
    is_searchable TINYINT(1) DEFAULT 0,
    is_visible_on_frontend TINYINT(1) DEFAULT 1,
    is_user_defined TINYINT(1) DEFAULT 1,
    used_for_variants TINYINT(1) DEFAULT 0 COMMENT 'Can be used to create product variants',
    
    validation_rules JSON COMMENT 'e.g. {"min": 0, "max": 100}',
    sort_order INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_attribute_code (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE attribute_options (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    attribute_id BIGINT UNSIGNED NOT NULL,
    
    value VARCHAR(255) NOT NULL,
    label VARCHAR(255) NOT NULL,
    color_hex VARCHAR(7) COMMENT 'For color attributes',
    sort_order INT DEFAULT 0,
    
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE,
    INDEX idx_attribute_options (attribute_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE attribute_groups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_attr_group_code (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE attribute_group_mappings (
    attribute_group_id BIGINT UNSIGNED NOT NULL,
    attribute_id BIGINT UNSIGNED NOT NULL,
    sort_order INT DEFAULT 0,
    
    PRIMARY KEY (attribute_group_id, attribute_id),
    FOREIGN KEY (attribute_group_id) REFERENCES attribute_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 7: CATALOG - PRODUCTS
-- =====================================================================

CREATE TABLE products (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    
    -- Basic Info
    type ENUM('simple', 'configurable', 'bundle', 'grouped', 'digital', 'subscription') NOT NULL DEFAULT 'simple',
    sku VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    short_description TEXT,
    description LONGTEXT,
    
    -- Pricing
    price DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
    special_price DECIMAL(15,4),
    special_price_from DATE,
    special_price_to DATE,
    cost_price DECIMAL(15,4) COMMENT 'For profit calculation',
    
    -- Tax
    tax_class_id BIGINT UNSIGNED,
    
    -- Status
    status ENUM('draft', 'active', 'archived') DEFAULT 'draft',
    is_visible TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    is_new TINYINT(1) DEFAULT 0,
    
    -- Inventory
    manage_stock TINYINT(1) DEFAULT 1,
    quantity INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 5,
    allow_backorders TINYINT(1) DEFAULT 0,
    
    -- Shipping
    weight DECIMAL(10,4),
    length DECIMAL(10,2),
    width DECIMAL(10,2),
    height DECIMAL(10,2),
    
    -- Digital Products
    is_downloadable TINYINT(1) DEFAULT 0,
    download_limit INT,
    download_expiry_days INT,
    
    -- SEO
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords VARCHAR(255),
    
    -- Stats
    view_count INT UNSIGNED DEFAULT 0,
    sold_count INT UNSIGNED DEFAULT 0,
    avg_rating DECIMAL(3,2) DEFAULT 0.00,
    review_count INT UNSIGNED DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_product_sku (shop_id, sku),
    UNIQUE KEY uk_product_slug (shop_id, slug),
    INDEX idx_product_status (status),
    INDEX idx_product_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Variants
CREATE TABLE product_variants (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    parent_product_id BIGINT UNSIGNED NOT NULL,
    sku VARCHAR(100) NOT NULL,
    name VARCHAR(255),
    attributes JSON COMMENT 'JSON object storing variant attribute values',
    price DECIMAL(15,4),
    special_price DECIMAL(15,4),
    quantity INT DEFAULT 0,
    weight DECIMAL(10,4),
    is_active TINYINT(1) DEFAULT 1,
    is_default TINYINT(1) DEFAULT 0 COMMENT 'Marks the default variant for display',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY uk_variant_sku (sku)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Variant Attribute Values
CREATE TABLE product_variant_attributes (
    variant_id BIGINT UNSIGNED NOT NULL,
    attribute_id BIGINT UNSIGNED NOT NULL,
    attribute_option_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (variant_id, attribute_id),
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_option_id) REFERENCES attribute_options(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product-Category Mapping
CREATE TABLE product_categories (
    product_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Attribute Values
CREATE TABLE product_attribute_values (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    attribute_id BIGINT UNSIGNED NOT NULL,
    text_value TEXT,
    integer_value INT,
    decimal_value DECIMAL(15,4),
    boolean_value TINYINT(1),
    date_value DATE,
    json_value JSON,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE,
    UNIQUE KEY uk_product_attribute (product_id, attribute_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Media
CREATE TABLE product_media (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED,
    type ENUM('image', 'video') DEFAULT 'image',
    path VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Digital Product Files
CREATE TABLE product_downloadables (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50),
    file_size INT UNSIGNED,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bundle Products
CREATE TABLE product_bundles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    bundle_product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('select', 'radio', 'checkbox', 'multiselect') DEFAULT 'select',
    is_required TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (bundle_product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_bundle_options (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    bundle_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT DEFAULT 1,
    price_adjustment DECIMAL(15,4) DEFAULT 0.0000,
    is_default TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (bundle_id) REFERENCES product_bundles(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Related Products
CREATE TABLE product_relations (
    product_id BIGINT UNSIGNED NOT NULL,
    related_product_id BIGINT UNSIGNED NOT NULL,
    relation_type ENUM('related', 'upsell', 'crosssell') DEFAULT 'related',
    PRIMARY KEY (product_id, related_product_id, relation_type),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (related_product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 8: INVENTORY / WAREHOUSES
-- =====================================================================

CREATE TABLE warehouses (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    address TEXT,
    is_active TINYINT(1) DEFAULT 1,
    is_default TINYINT(1) DEFAULT 0,
    priority INT DEFAULT 0,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_warehouse_code (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inventory (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    quantity INT DEFAULT 0,
    reserved_quantity INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    UNIQUE KEY uk_inventory (product_id, variant_id, warehouse_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inventory_movements (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    inventory_id BIGINT UNSIGNED NOT NULL,
    type ENUM('purchase', 'sale', 'return', 'adjustment', 'transfer') NOT NULL,
    quantity INT NOT NULL,
    reference_type VARCHAR(50),
    reference_id BIGINT UNSIGNED,
    notes TEXT,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 9: ORDERS
-- =====================================================================

CREATE TABLE orders (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED,
    order_number VARCHAR(50) NOT NULL,
    
    -- Status
    status ENUM('pending', 'processing', 'paid', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'partially_paid', 'refunded', 'failed') DEFAULT 'pending',
    fulfillment_status ENUM('unfulfilled', 'partially_fulfilled', 'fulfilled') DEFAULT 'unfulfilled',
    
    -- Totals
    subtotal DECIMAL(15,4) NOT NULL,
    discount_amount DECIMAL(15,4) DEFAULT 0.0000,
    shipping_amount DECIMAL(15,4) DEFAULT 0.0000,
    tax_amount DECIMAL(15,4) DEFAULT 0.0000,
    grand_total DECIMAL(15,4) NOT NULL,
    
    -- Currency
    currency_code VARCHAR(10) NOT NULL,
    currency_rate DECIMAL(12,6) DEFAULT 1.000000,
    
    -- Customer Info (snapshot)
    customer_email VARCHAR(255),
    customer_first_name VARCHAR(100),
    customer_last_name VARCHAR(100),
    customer_phone VARCHAR(50),
    is_guest TINYINT(1) DEFAULT 0,
    
    -- Addresses (JSON snapshot)
    billing_address JSON NOT NULL,
    shipping_address JSON,
    
    -- Methods
    shipping_method VARCHAR(100),
    shipping_description VARCHAR(255),
    payment_method VARCHAR(100),
    
    -- Notes
    customer_note TEXT,
    admin_note TEXT,
    
    -- Gift
    is_gift TINYINT(1) DEFAULT 0,
    gift_message TEXT,
    
    -- Tracking
    ip_address VARCHAR(45),
    user_agent TEXT,
    
    -- Timestamps
    paid_at TIMESTAMP NULL,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    UNIQUE KEY uk_order_number (shop_id, order_number),
    INDEX idx_order_status (status),
    INDEX idx_order_customer (customer_id),
    INDEX idx_order_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED,
    variant_id BIGINT UNSIGNED,
    
    -- Product Snapshot
    sku VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    options JSON,
    
    -- Pricing
    price DECIMAL(15,4) NOT NULL,
    original_price DECIMAL(15,4),
    discount_amount DECIMAL(15,4) DEFAULT 0.0000,
    tax_amount DECIMAL(15,4) DEFAULT 0.0000,
    row_total DECIMAL(15,4) NOT NULL,
    
    -- Quantity
    quantity INT NOT NULL,
    quantity_shipped INT DEFAULT 0,
    quantity_refunded INT DEFAULT 0,
    
    -- Digital products
    is_downloadable TINYINT(1) DEFAULT 0,
    download_count INT DEFAULT 0,
    download_expires_at TIMESTAMP NULL,
    
    weight DECIMAL(10,4),
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_history (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(50) NOT NULL,
    comment TEXT,
    is_customer_notified TINYINT(1) DEFAULT 0,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 10: SHIPPING
-- =====================================================================

CREATE TABLE shipping_methods (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('flat_rate', 'table_rate', 'weight_based', 'price_based', 'free', 'carrier') DEFAULT 'flat_rate',
    is_active TINYINT(1) DEFAULT 1,
    price DECIMAL(15,4) DEFAULT 0.0000,
    min_order_total DECIMAL(15,4),
    max_order_weight DECIMAL(10,4),
    free_shipping_threshold DECIMAL(15,4),
    estimated_days_min INT,
    estimated_days_max INT,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_shipping_method (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipping_zones (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    countries JSON COMMENT 'Array of country codes',
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipping_zone_rates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shipping_method_id BIGINT UNSIGNED NOT NULL,
    shipping_zone_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(15,4) NOT NULL,
    FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_zone_id) REFERENCES shipping_zones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending', 'picked', 'packed', 'shipped', 'in_transit', 'delivered', 'failed') DEFAULT 'pending',
    carrier VARCHAR(100),
    tracking_number VARCHAR(255),
    tracking_url TEXT,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipment_items (
    shipment_id BIGINT UNSIGNED NOT NULL,
    order_item_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    PRIMARY KEY (shipment_id, order_item_id),
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 11: PAYMENTS
-- =====================================================================

CREATE TABLE payment_methods (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    is_test_mode TINYINT(1) DEFAULT 0,
    min_order_total DECIMAL(15,4),
    max_order_total DECIMAL(15,4),
    config JSON,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_payment_method (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    payment_method_id BIGINT UNSIGNED,
    status ENUM('pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',
    amount DECIMAL(15,4) NOT NULL,
    currency_code VARCHAR(10) NOT NULL,
    transaction_id VARCHAR(255),
    gateway_response JSON,
    parent_payment_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_payment_id) REFERENCES payments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 12: TAXES
-- =====================================================================

CREATE TABLE tax_classes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL,
    is_default TINYINT(1) DEFAULT 0,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_tax_class (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tax_zones (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    countries JSON,
    states JSON,
    postal_codes JSON,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tax_rates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tax_class_id BIGINT UNSIGNED NOT NULL,
    tax_zone_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    rate DECIMAL(8,4) NOT NULL COMMENT 'Percentage e.g. 19.0000',
    priority INT DEFAULT 0,
    is_compound TINYINT(1) DEFAULT 0,
    FOREIGN KEY (tax_class_id) REFERENCES tax_classes(id) ON DELETE CASCADE,
    FOREIGN KEY (tax_zone_id) REFERENCES tax_zones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 13: DISCOUNTS & COUPONS
-- =====================================================================

CREATE TABLE discounts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50),
    description TEXT,
    type ENUM('percentage', 'fixed_amount', 'free_shipping', 'buy_x_get_y') NOT NULL,
    value DECIMAL(15,4) NOT NULL,
    apply_to ENUM('order', 'item', 'shipping') DEFAULT 'order',
    min_order_total DECIMAL(15,4),
    max_discount_amount DECIMAL(15,4),
    usage_limit INT,
    usage_per_customer INT,
    times_used INT DEFAULT 0,
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    is_automatic TINYINT(1) DEFAULT 0,
    conditions JSON COMMENT 'Advanced conditions like product IDs, categories',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    INDEX idx_discount_code (code),
    INDEX idx_discount_dates (starts_at, ends_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE discount_usage (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    discount_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED,
    amount DECIMAL(15,4) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (discount_id) REFERENCES discounts(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pricing Rules (B2B Discounts, Volume Discounts)
CREATE TABLE pricing_rules (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('customer_group', 'quantity', 'tiered') NOT NULL,
    customer_group_id BIGINT UNSIGNED,
    discount_type ENUM('percentage', 'fixed') DEFAULT 'percentage',
    discount_value DECIMAL(15,4) NOT NULL,
    min_quantity INT,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_group_id) REFERENCES customer_groups(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 14: SUBSCRIPTIONS
-- =====================================================================

CREATE TABLE subscription_plans (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    billing_interval ENUM('daily', 'weekly', 'monthly', 'quarterly', 'yearly') NOT NULL,
    billing_interval_count INT DEFAULT 1,
    price DECIMAL(15,4) NOT NULL,
    trial_days INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subscriptions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    customer_id BIGINT UNSIGNED NOT NULL,
    subscription_plan_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED,
    status ENUM('active', 'paused', 'cancelled', 'expired', 'past_due') DEFAULT 'active',
    current_period_start TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    current_period_end TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    trial_ends_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    next_billing_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subscription_payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    subscription_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED,
    amount DECIMAL(15,4) NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    billing_period_start TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    billing_period_end TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    attempted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 15: RETURNS & REFUNDS
-- =====================================================================

CREATE TABLE returns (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED,
    return_number VARCHAR(50) NOT NULL,
    status ENUM('requested', 'approved', 'rejected', 'received', 'refunded', 'closed') DEFAULT 'requested',
    reason ENUM('defective', 'not_as_described', 'wrong_item', 'no_longer_needed', 'other') NOT NULL,
    reason_details TEXT,
    refund_amount DECIMAL(15,4),
    admin_note TEXT,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    received_at TIMESTAMP NULL,
    refunded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE return_items (
    return_id BIGINT UNSIGNED NOT NULL,
    order_item_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    condition_status ENUM('unopened', 'opened', 'used', 'damaged') DEFAULT 'unopened',
    PRIMARY KEY (return_id, order_item_id),
    FOREIGN KEY (return_id) REFERENCES returns(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 16: CARTS & WISHLISTS
-- =====================================================================

CREATE TABLE carts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED,
    session_id VARCHAR(255),
    currency_code VARCHAR(10),
    coupon_code VARCHAR(50),
    subtotal DECIMAL(15,4) DEFAULT 0.0000,
    discount_amount DECIMAL(15,4) DEFAULT 0.0000,
    shipping_amount DECIMAL(15,4) DEFAULT 0.0000,
    tax_amount DECIMAL(15,4) DEFAULT 0.0000,
    grand_total DECIMAL(15,4) DEFAULT 0.0000,
    items_count INT DEFAULT 0,
    is_abandoned TINYINT(1) DEFAULT 0,
    abandoned_at TIMESTAMP NULL,
    recovery_email_sent_at TIMESTAMP NULL,
    converted_to_order_id BIGINT UNSIGNED,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (converted_to_order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_cart_session (session_id),
    INDEX idx_cart_abandoned (is_abandoned, abandoned_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cart_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    cart_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED,
    quantity INT NOT NULL,
    price DECIMAL(15,4) NOT NULL,
    row_total DECIMAL(15,4) NOT NULL,
    options JSON,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE wishlists (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    customer_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    UNIQUE KEY uk_wishlist (customer_id, product_id, variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 17: MARKETING & CAMPAIGNS
-- =====================================================================

CREATE TABLE campaigns (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('email', 'banner', 'popup', 'notification') NOT NULL,
    status ENUM('draft', 'scheduled', 'active', 'paused', 'completed') DEFAULT 'draft',
    target_audience JSON COMMENT 'Customer groups, segments',
    content JSON COMMENT 'Campaign content/template',
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    impressions INT DEFAULT 0,
    clicks INT DEFAULT 0,
    conversions INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE newsletter_subscribers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED,
    email VARCHAR(255) NOT NULL,
    status ENUM('subscribed', 'unsubscribed', 'pending') DEFAULT 'pending',
    confirmation_token VARCHAR(255),
    source ENUM('checkout', 'footer', 'popup', 'account', 'import') DEFAULT 'footer',
    subscribed_at TIMESTAMP NULL,
    unsubscribed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    UNIQUE KEY uk_newsletter (shop_id, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE newsletter_campaigns (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    status ENUM('draft', 'scheduled', 'sending', 'sent') DEFAULT 'draft',
    scheduled_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    recipients_count INT DEFAULT 0,
    opens_count INT DEFAULT 0,
    clicks_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 18: REVIEWS & RATINGS
-- =====================================================================

CREATE TABLE reviews (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED,
    order_id BIGINT UNSIGNED,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255),
    content TEXT,
    pros TEXT,
    cons TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_verified_purchase TINYINT(1) DEFAULT 0,
    helpful_count INT DEFAULT 0,
    admin_reply TEXT,
    admin_reply_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_review_status (status),
    INDEX idx_review_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE review_media (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    review_id BIGINT UNSIGNED NOT NULL,
    type ENUM('image', 'video') DEFAULT 'image',
    path VARCHAR(500) NOT NULL,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 19: FINANCE - INVOICES & CREDIT NOTES
-- =====================================================================

CREATE TABLE invoices (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    status ENUM('draft', 'open', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    subtotal DECIMAL(15,4) NOT NULL,
    discount_amount DECIMAL(15,4) DEFAULT 0.0000,
    shipping_amount DECIMAL(15,4) DEFAULT 0.0000,
    tax_amount DECIMAL(15,4) DEFAULT 0.0000,
    grand_total DECIMAL(15,4) NOT NULL,
    currency_code VARCHAR(10) NOT NULL,
    billing_address JSON NOT NULL,
    notes TEXT,
    due_date DATE,
    paid_at TIMESTAMP NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoice_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    invoice_id BIGINT UNSIGNED NOT NULL,
    order_item_id BIGINT UNSIGNED,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100),
    quantity INT NOT NULL,
    price DECIMAL(15,4) NOT NULL,
    tax_amount DECIMAL(15,4) DEFAULT 0.0000,
    row_total DECIMAL(15,4) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE credit_notes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    invoice_id BIGINT UNSIGNED,
    credit_note_number VARCHAR(50) NOT NULL,
    reason TEXT,
    subtotal DECIMAL(15,4) NOT NULL,
    tax_amount DECIMAL(15,4) DEFAULT 0.0000,
    grand_total DECIMAL(15,4) NOT NULL,
    currency_code VARCHAR(10) NOT NULL,
    status ENUM('pending', 'processed', 'refunded') DEFAULT 'pending',
    refunded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payouts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,4) NOT NULL,
    currency_code VARCHAR(10) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(100),
    reference VARCHAR(255),
    notes TEXT,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 20: CMS & NAVIGATION
-- =====================================================================

CREATE TABLE cms_pages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content LONGTEXT,
    layout ENUM('default', 'full_width', 'sidebar_left', 'sidebar_right') DEFAULT 'default',
    meta_title VARCHAR(255),
    meta_description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_cms_slug (shop_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE navigation_menus (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL COMMENT 'e.g. main, footer, mobile',
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_menu_code (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE navigation_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    menu_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED,
    label VARCHAR(255) NOT NULL,
    type ENUM('category', 'product', 'page', 'url', 'custom') DEFAULT 'url',
    reference_id BIGINT UNSIGNED COMMENT 'ID of category/product/page',
    url VARCHAR(500),
    target ENUM('_self', '_blank') DEFAULT '_self',
    -- Icon fields
    icon VARCHAR(50) COMMENT 'Material icon name',
    icon_position ENUM('left', 'right', 'only') DEFAULT 'left' COMMENT 'Icon position relative to text',
    custom_icon_url TEXT COMMENT 'Custom uploaded icon (data URL or path)',
    -- Styling fields
    custom_color VARCHAR(7) COMMENT 'Text color hex',
    bg_color VARCHAR(7) COMMENT 'Background color hex',
    font_weight VARCHAR(10) COMMENT 'normal or bold',
    text_decoration VARCHAR(20) COMMENT 'none, underline, line-through',
    -- Mega Menu fields
    mega_enabled TINYINT(1) DEFAULT 0 COMMENT 'Enable mega dropdown',
    mega_columns TINYINT DEFAULT 1 COMMENT 'Number of columns (1-4)',
    mega_width ENUM('auto', 'full', 'fixed') DEFAULT 'auto' COMMENT 'Dropdown width',
    mega_image VARCHAR(500) COMMENT 'Banner image URL',
    mega_promo_title VARCHAR(100) COMMENT 'Promo block title',
    mega_promo_text TEXT COMMENT 'Promo block text/HTML',
    mega_promo_link VARCHAR(500) COMMENT 'Promo block link',
    -- Badge fields
    badge_text VARCHAR(20) COMMENT 'e.g. NEU, SALE',
    badge_color VARCHAR(7) COMMENT 'Hex color for badge',
    css_class VARCHAR(100) COMMENT 'Custom CSS class',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (menu_id) REFERENCES navigation_menus(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES navigation_items(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 20b: MEDIA LIBRARY
-- =====================================================================

CREATE TABLE media_library (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED DEFAULT 1,
    
    -- File Info
    filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(50) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    
    -- Image Dimensions
    width INT UNSIGNED,
    height INT UNSIGNED,
    
    -- Metadata
    alt_text VARCHAR(255),
    title VARCHAR(255),
    folder VARCHAR(100) DEFAULT 'general',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_folder (shop_id, folder),
    INDEX idx_mime (mime_type),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 21: SEO
-- =====================================================================

CREATE TABLE seo_settings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    default_meta_title VARCHAR(255),
    default_meta_description TEXT,
    title_separator VARCHAR(10) DEFAULT '|',
    enable_canonical TINYINT(1) DEFAULT 1,
    robots_txt TEXT,
    enable_sitemap TINYINT(1) DEFAULT 1,
    sitemap_frequency ENUM('always', 'hourly', 'daily', 'weekly', 'monthly') DEFAULT 'weekly',
    google_analytics_id VARCHAR(50),
    facebook_pixel_id VARCHAR(50),
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_seo_shop (shop_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE url_redirects (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    source_url VARCHAR(500) NOT NULL,
    target_url VARCHAR(500) NOT NULL,
    redirect_type ENUM('301', '302') DEFAULT '301',
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    INDEX idx_redirect_source (source_url(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 22: SYSTEM SETTINGS (EAV Pattern)
-- =====================================================================

CREATE TABLE settings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED,
    scope ENUM('global', 'shop', 'checkout', 'catalog', 'customer', 'marketing', 'shipping', 'payment', 'email') NOT NULL,
    setting_key VARCHAR(200) NOT NULL,
    setting_value JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_setting (shop_id, scope, setting_key),
    INDEX idx_setting_scope (scope)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 23: EMAIL TEMPLATES
-- =====================================================================

CREATE TABLE email_templates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(100) NOT NULL COMMENT 'e.g. order_confirmation, password_reset',
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_email_template (shop_id, code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 24: LOGS & AUDIT
-- =====================================================================

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED,
    user_type ENUM('admin', 'customer', 'system') NOT NULL,
    user_id BIGINT UNSIGNED,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100),
    entity_id BIGINT UNSIGNED,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    INDEX idx_activity_entity (entity_type, entity_id),
    INDEX idx_activity_user (user_type, user_id),
    INDEX idx_activity_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE system_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    level ENUM('debug', 'info', 'notice', 'warning', 'error', 'critical') NOT NULL,
    channel VARCHAR(100) DEFAULT 'general',
    message TEXT NOT NULL,
    context JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_log_level (level),
    INDEX idx_log_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 25: BACKUPS
-- =====================================================================

CREATE TABLE backups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED,
    type ENUM('full', 'database', 'files') NOT NULL,
    file_path VARCHAR(500),
    file_size BIGINT UNSIGNED,
    status ENUM('pending', 'in_progress', 'completed', 'failed') DEFAULT 'pending',
    error_message TEXT,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 26: DEVELOPER - API & WEBHOOKS
-- =====================================================================

CREATE TABLE api_keys (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    api_key VARCHAR(64) NOT NULL,
    api_secret VARCHAR(64) NOT NULL,
    permissions JSON COMMENT 'Array of allowed endpoints',
    is_active TINYINT(1) DEFAULT 1,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_api_key (api_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE webhooks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(500) NOT NULL,
    events JSON NOT NULL COMMENT 'Array of event types to trigger',
    secret VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    last_triggered_at TIMESTAMP NULL,
    last_status_code INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE webhook_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    webhook_id BIGINT UNSIGNED NOT NULL,
    event VARCHAR(100) NOT NULL,
    payload JSON,
    response_status INT,
    response_body TEXT,
    duration_ms INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (webhook_id) REFERENCES webhooks(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 27: INTEGRATIONS & PLUGINS
-- =====================================================================

CREATE TABLE integrations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(100) NOT NULL COMMENT 'e.g. erp, accounting, crm',
    provider VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    config JSON,
    is_active TINYINT(1) DEFAULT 1,
    last_synced_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE plugins (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    version VARCHAR(20) NOT NULL,
    author VARCHAR(255),
    is_active TINYINT(1) DEFAULT 0,
    settings JSON,
    installed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_plugin_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 28: SEARCH INDEX
-- =====================================================================

CREATE TABLE search_index (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id BIGINT UNSIGNED NOT NULL,
    searchable_text TEXT NOT NULL,
    locale VARCHAR(10) DEFAULT 'de_DE',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    UNIQUE KEY uk_search (shop_id, entity_type, entity_id, locale),
    FULLTEXT INDEX ft_search (searchable_text)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 29: ANALYTICS EVENTS
-- =====================================================================

CREATE TABLE analytics_events (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    shop_id BIGINT UNSIGNED NOT NULL,
    event_type VARCHAR(50) NOT NULL COMMENT 'e.g. page_view, add_to_cart, purchase',
    session_id VARCHAR(100),
    customer_id BIGINT UNSIGNED,
    entity_type VARCHAR(50),
    entity_id BIGINT UNSIGNED,
    value DECIMAL(15,4),
    metadata JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    INDEX idx_analytics_type (event_type),
    INDEX idx_analytics_date (created_at),
    INDEX idx_analytics_session (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SECTION 30: LICENSE KEYS (Digital Products)
-- =====================================================================

CREATE TABLE license_keys (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    license_key VARCHAR(255) NOT NULL,
    status ENUM('available', 'sold', 'expired', 'revoked') DEFAULT 'available',
    order_item_id BIGINT UNSIGNED,
    customer_id BIGINT UNSIGNED,
    activated_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    UNIQUE KEY uk_license (license_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- RE-ENABLE FOREIGN KEY CHECKS
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- END OF SCHEMA
-- =====================================================================
