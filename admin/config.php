<?php
/**
 * ============================================
 * ADMIN PANEL - CONFIGURATION
 * ============================================
 * Zentrale Konfigurationsdatei
 * Alle globalen Einstellungen und Menü-Definition
 * 
 * Phase 1.1: Optimierte Struktur
 * ============================================
 */

// Verhindere direkten Zugriff
if (!defined('ADMIN_ACCESS')) {
    define('ADMIN_ACCESS', true);
}

/**
 * Environment
 * dev = Entwicklung, prod = Produktion
 */
$environment = 'dev';

/**
 * Shop-Konfiguration
 */
$config = [
    // Grundeinstellungen
    'shop_name' => 'Mein Online Shop',
    'shop_email' => 'info@meinshop.de',
    'shop_url' => 'https://meinshop.de',

    // Lokalisierung
    'default_locale' => 'de_DE',
    'default_currency' => 'EUR',
    'currency_symbol' => '€',
    'timezone' => 'Europe/Berlin',

    // Admin Panel
    'admin_title' => 'Admin Panel',
    'admin_version' => '1.0.0',

    // Pfade
    'base_path' => __DIR__,
    'assets_path' => 'assets',
    'pages_path' => 'pages',
    'includes_path' => 'includes',

    // Environment
    'environment' => $environment,
];

/**
 * Database Configuration
 */
$database = [
    'host' => 'localhost',
    'database' => 'bagisto_admin',
    'username' => 'root',
    'password' => 'Nailuj18@NESA08',
    'charset' => 'utf8mb4',
];

/**
 * Feature Toggles
 * Aktiviert/Deaktiviert einzelne Funktionen
 */
$features = [
    'multi_language' => true,
    'multi_currency' => true,
    'subscriptions' => true,
    'digital_products' => true,
    'abandoned_cart' => true,
    'reviews' => true,
    'wishlist' => true,
    'compare' => false,
    'blog' => false,
    'marketplace' => false,
    'b2b' => false,
    'pos' => false,
    'warehouse_management' => false,
];

/**
 * ============================================
 * SIDEBAR MENÜ-STRUKTUR (OPTIMIERT)
 * ============================================
 * 
 * Änderungen Phase 1.1:
 * - Medien entfernt (sind Attachments, keine Entität)
 * - Kunden vs. Admin strikt getrennt
 * - Analytics → Reports verschoben
 * - Administration neuer Bereich
 * - Reports neuer Bereich
 * - Developer als "Advanced" markiert
 * ============================================
 */
$menu = [
    // ===== DASHBOARD =====
    'dashboard' => [
        'label' => 'nav.dashboard',
        'icon' => 'space_dashboard',
        'url' => 'index.php',
    ],

    // ===== SHOP =====
    'shop' => [
        'label' => 'nav.shop',
        'icon' => 'storefront',
        'items' => [
            ['label' => 'nav.general', 'page' => 'shop/general'],
            ['label' => 'nav.design', 'page' => 'shop/design'],
            ['label' => 'nav.cms', 'page' => 'shop/cms'],
            ['label' => 'nav.navigation', 'page' => 'shop/navigation'],
            ['label' => 'nav.localization', 'page' => 'shop/localization'],
            ['label' => 'nav.seo', 'page' => 'shop/seo'],
            ['label' => 'nav.personalization', 'page' => 'shop/personalization'],
        ],
    ],

    // ===== CATALOG =====
    'catalog' => [
        'label' => 'nav.catalog',
        'icon' => 'inventory_2',
        'items' => [
            ['label' => 'nav.products', 'page' => 'catalog/products'],
            ['label' => 'nav.categories', 'page' => 'catalog/categories'],
            ['label' => 'nav.attributes', 'page' => 'catalog/attributes'],
            ['label' => 'nav.bundles', 'page' => 'catalog/bundles'],
            ['label' => 'nav.configurator', 'page' => 'catalog/configurator'],
            ['label' => 'nav.inventory', 'page' => 'catalog/inventory'],
        ],
    ],

    // ===== CUSTOMERS =====
    'customers' => [
        'label' => 'nav.customers',
        'icon' => 'group',
        'items' => [
            ['label' => 'nav.customer_list', 'page' => 'customers/customers'],
            ['label' => 'nav.customer_groups', 'page' => 'customers/groups'],
            ['label' => 'nav.customer_history', 'page' => 'customers/history'],
        ],
    ],

    // ===== ORDERS =====
    'orders' => [
        'label' => 'nav.orders',
        'icon' => 'receipt_long',
        'items' => [
            ['label' => 'nav.orders', 'page' => 'orders/orders'],
            ['label' => 'nav.fulfillment', 'page' => 'orders/fulfillment'],
            ['label' => 'nav.returns', 'page' => 'orders/returns'],
            ['label' => 'nav.cancellations', 'page' => 'orders/cancellations'],
        ],
    ],

    // ===== COMMERCE =====
    'commerce' => [
        'label' => 'nav.commerce',
        'icon' => 'shopping_bag',
        'items' => [
            ['label' => 'nav.checkout', 'page' => 'commerce/checkout'],
            ['label' => 'nav.carts', 'page' => 'commerce/carts'],
            ['label' => 'nav.pricing_rules', 'page' => 'commerce/pricing'],
            ['label' => 'nav.discounts', 'page' => 'commerce/discounts'],
            ['label' => 'nav.taxes', 'page' => 'commerce/taxes'],
            ['label' => 'nav.shipping', 'page' => 'commerce/shipping'],
            ['label' => 'nav.payments', 'page' => 'commerce/payments'],
            ['label' => 'nav.subscriptions', 'page' => 'commerce/subscriptions'],
            ['label' => 'nav.digital_delivery', 'page' => 'commerce/digital_delivery'],
        ],
    ],

    // ===== FINANCE =====
    'finance' => [
        'label' => 'nav.finance',
        'icon' => 'account_balance_wallet',
        'items' => [
            ['label' => 'nav.invoices', 'page' => 'finance/invoices'],
            ['label' => 'nav.credit_notes', 'page' => 'finance/credit_notes'],
            ['label' => 'nav.payouts', 'page' => 'finance/payouts'],
            ['label' => 'nav.accounting', 'page' => 'finance/accounting'],
            ['label' => 'nav.reconciliation', 'page' => 'finance/reconciliation'],
        ],
    ],

    // ===== MARKETING =====
    'marketing' => [
        'label' => 'nav.marketing',
        'icon' => 'campaign',
        'items' => [
            ['label' => 'nav.campaigns', 'page' => 'marketing/campaigns'],
            ['label' => 'nav.coupons', 'page' => 'marketing/coupons'],
            ['label' => 'nav.newsletter', 'page' => 'marketing/newsletter'],
            ['label' => 'nav.reviews', 'page' => 'marketing/reviews'],
        ],
    ],

    // ===== REPORTS =====
    'reports' => [
        'label' => 'nav.reports',
        'icon' => 'analytics',
        'items' => [
            ['label' => 'nav.revenue', 'page' => 'reports/revenue'],
            ['label' => 'nav.orders', 'page' => 'reports/orders'],
            ['label' => 'nav.customers', 'page' => 'reports/customers'],
            ['label' => 'nav.products', 'page' => 'reports/products'],
            ['label' => 'nav.marketing', 'page' => 'reports/marketing'],
        ],
    ],

    // ===== ADMINISTRATION =====
    'administration' => [
        'label' => 'nav.administration',
        'icon' => 'admin_panel_settings',
        'items' => [
            ['label' => 'nav.users', 'page' => 'administration/users'],
            ['label' => 'nav.roles', 'page' => 'administration/roles'],
            ['label' => 'nav.permissions', 'page' => 'administration/permissions'],
        ],
    ],

    // ===== SYSTEM =====
    'system' => [
        'label' => 'nav.system',
        'icon' => 'tune',
        'items' => [
            ['label' => 'nav.settings', 'page' => 'system/settings'],
            ['label' => 'nav.security', 'page' => 'system/security'],
            ['label' => 'nav.logs', 'page' => 'system/logs'],
            ['label' => 'nav.backups', 'page' => 'system/backups'],
            ['label' => 'nav.email', 'page' => 'system/email'],
            ['label' => 'nav.integrations', 'page' => 'system/integrations'],
        ],
    ],

    // ===== DEVELOPER =====
    'developer' => [
        'label' => 'nav.developer',
        'icon' => 'terminal',
        'badge' => 'Advanced',
        'items' => [
            ['label' => 'nav.api', 'page' => 'developer/api'],
            ['label' => 'nav.webhooks', 'page' => 'developer/webhooks'],
            ['label' => 'nav.themes', 'page' => 'developer/themes'],
            ['label' => 'nav.plugins', 'page' => 'developer/plugins'],
            ['label' => 'nav.debug', 'page' => 'developer/debug'],
        ],
    ],
];

/**
 * Erlaubte Seiten für Router
 * Automatisch generiert aus $menu
 */
$allowedPages = [];
foreach ($menu as $key => $group) {
    if (isset($group['items'])) {
        foreach ($group['items'] as $item) {
            if (isset($item['page'])) {
                $allowedPages[] = $item['page'];
            }
        }
    }
}

/**
 * Zusätzliche Seiten (Detail, Edit, Create)
 * Diese erscheinen nicht im Menü, sind aber über Links erreichbar
 */
$additionalPages = [
    // Katalog
    'catalog/product_detail',
    'catalog/product_edit',
    'catalog/product_create',
    'catalog/category_detail',
    'catalog/category_edit',
    'catalog/category_create',
    'catalog/attribute_detail',
    'catalog/attribute_edit',
    'catalog/attribute_create',
    'catalog/bundle_detail',
    'catalog/bundle_edit',
    'catalog/bundle_create',

    // Kunden
    'customers/customer_detail',
    'customers/customer_edit',
    'customers/customer_create',
    'customers/group_detail',
    'customers/group_edit',
    'customers/group_create',

    // Bestellungen
    'orders/order_detail',
    'orders/order_edit',
    'orders/fulfillment_detail',
    'orders/return_detail',

    // Commerce
    'commerce/discount_detail',
    'commerce/discount_edit',
    'commerce/discount_create',
    'commerce/pricing_detail',
    'commerce/pricing_create',
    'commerce/payment_detail',
    'commerce/payment_create',
    'commerce/shipping_detail',
    'commerce/shipping_create',
    'commerce/subscription_detail',
    'commerce/subscription_create',

    // Finanzen
    'finance/invoice_detail',
    'finance/credit_note_detail',

    // Administration
    'administration/user_detail',
    'administration/user_edit',
    'administration/user_create',
    'administration/role_detail',
    'administration/role_create',

    // Authentication
    'login',
    'logout',

    // CMS Pages
    'shop/cms_create',
    'shop/cms_edit',
    'shop/cms_settings',

    // Navigation
    'shop/navigation_item_edit',
    'shop/mega_menu_editor',
    'shop/navigation_settings',
    'shop/mega_menu_settings',
    'shop/preview_header',

    // Geo-Location
    'shop/geo_popup_editor',
];

$allowedPages = array_merge($allowedPages, $additionalPages);

/**
 * Hilfsfunktion: Asset-URL generieren
 */
function asset($path)
{
    global $config;
    return $config['assets_path'] . '/' . ltrim($path, '/');
}

/**
 * Hilfsfunktion: Aktuelle Seite ermitteln
 */
function currentPage()
{
    return isset($_GET['page']) ? $_GET['page'] : 'dashboard';
}

/**
 * Hilfsfunktion: Prüfen ob Menüpunkt aktiv
 */
function isActive($page)
{
    $current = currentPage();
    if ($page === 'dashboard' && $current === 'dashboard') {
        return true;
    }
    return $current === $page;
}

/**
 * Hilfsfunktion: Prüfen ob Menügruppe aktiv
 */
function isGroupActive($groupKey, $items)
{
    $current = currentPage();
    foreach ($items as $item) {
        if (isset($item['page']) && $item['page'] === $current) {
            return true;
        }
    }
    return false;
}

/**
 * Hilfsfunktion: Feature prüfen
 */
function isFeatureEnabled($feature)
{
    global $features;
    return isset($features[$feature]) && $features[$feature] === true;
}

/**
 * Hilfsfunktion: Entwicklungsumgebung prüfen
 */
function isDev()
{
    global $config;
    return $config['environment'] === 'dev';
}
