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
        'label' => 'Dashboard',
        'icon' => 'space_dashboard',
        'url' => 'index.php',
    ],

    // ===== SHOP =====
    'shop' => [
        'label' => 'Shop',
        'icon' => 'storefront',
        'items' => [
            ['label' => 'Allgemein', 'page' => 'shop/general'],
            ['label' => 'Design', 'page' => 'shop/design'],
            ['label' => 'CMS', 'page' => 'shop/cms'],
            ['label' => 'Navigation', 'page' => 'shop/navigation'],
            ['label' => 'Lokalisierung', 'page' => 'shop/localization'],
            ['label' => 'SEO', 'page' => 'shop/seo'],
            ['label' => 'Personalisierung', 'page' => 'shop/personalization'],
        ],
    ],

    // ===== KATALOG =====
    // HINWEIS: Medien entfernt - werden kontextuell in Produkten/Kategorien/CMS verwaltet
    'catalog' => [
        'label' => 'Katalog',
        'icon' => 'inventory_2',
        'items' => [
            ['label' => 'Produkte', 'page' => 'catalog/products'],
            ['label' => 'Kategorien', 'page' => 'catalog/categories'],
            ['label' => 'Attribute', 'page' => 'catalog/attributes'],
            ['label' => 'Bundles', 'page' => 'catalog/bundles'],
            ['label' => 'Konfigurator', 'page' => 'catalog/configurator'],
            ['label' => 'Inventar', 'page' => 'catalog/inventory'],
        ],
    ],

    // ===== KUNDEN (nur Shop-Kunden, KEINE Admins) =====
    'customers' => [
        'label' => 'Kunden',
        'icon' => 'group',
        'items' => [
            ['label' => 'Kundenliste', 'page' => 'customers/customers'],
            ['label' => 'Kundengruppen', 'page' => 'customers/groups'],
            ['label' => 'Kundenhistorie', 'page' => 'customers/history'],
        ],
    ],

    // ===== BESTELLUNGEN =====
    'orders' => [
        'label' => 'Bestellungen',
        'icon' => 'receipt_long',
        'items' => [
            ['label' => 'Bestellungen', 'page' => 'orders/orders'],
            ['label' => 'Fulfillment', 'page' => 'orders/fulfillment'],
            ['label' => 'Retouren', 'page' => 'orders/returns'],
            ['label' => 'Stornierungen', 'page' => 'orders/cancellations'],
        ],
    ],

    // ===== COMMERCE (logisch gegliedert) =====
    'commerce' => [
        'label' => 'Commerce',
        'icon' => 'shopping_bag',
        'items' => [
            // Checkout Flow
            ['label' => 'Checkout', 'page' => 'commerce/checkout'],
            ['label' => 'Warenkörbe', 'page' => 'commerce/carts'],
            // Preisgestaltung
            ['label' => 'Preisregeln', 'page' => 'commerce/pricing'],
            ['label' => 'Rabatte', 'page' => 'commerce/discounts'],
            ['label' => 'Steuern', 'page' => 'commerce/taxes'],
            // Versand & Zahlung
            ['label' => 'Versand', 'page' => 'commerce/shipping'],
            ['label' => 'Zahlungen', 'page' => 'commerce/payments'],
            // Erweitert
            ['label' => 'Abonnements', 'page' => 'commerce/subscriptions'],
            ['label' => 'Digitale Lieferung', 'page' => 'commerce/digital_delivery'],
        ],
    ],

    // ===== FINANZEN =====
    'finance' => [
        'label' => 'Finanzen',
        'icon' => 'account_balance_wallet',
        'items' => [
            ['label' => 'Rechnungen', 'page' => 'finance/invoices'],
            ['label' => 'Gutschriften', 'page' => 'finance/credit_notes'],
            ['label' => 'Auszahlungen', 'page' => 'finance/payouts'],
            ['label' => 'Buchhaltung', 'page' => 'finance/accounting'],
            ['label' => 'Abstimmung', 'page' => 'finance/reconciliation'],
        ],
    ],

    // ===== MARKETING (ohne Analytics - wurde zu Reports verschoben) =====
    'marketing' => [
        'label' => 'Marketing',
        'icon' => 'campaign',
        'items' => [
            ['label' => 'Kampagnen', 'page' => 'marketing/campaigns'],
            ['label' => 'Gutscheine', 'page' => 'marketing/coupons'],
            ['label' => 'Newsletter', 'page' => 'marketing/newsletter'],
            ['label' => 'Bewertungen', 'page' => 'marketing/reviews'],
        ],
    ],

    // ===== REPORTS (NEU - von Marketing getrennt) =====
    'reports' => [
        'label' => 'Reports',
        'icon' => 'analytics',
        'items' => [
            ['label' => 'Umsatz', 'page' => 'reports/revenue'],
            ['label' => 'Bestellungen', 'page' => 'reports/orders'],
            ['label' => 'Kunden', 'page' => 'reports/customers'],
            ['label' => 'Produkte', 'page' => 'reports/products'],
            ['label' => 'Marketing', 'page' => 'reports/marketing'],
        ],
    ],

    // ===== ADMINISTRATION (NEU - Admin-User strikt getrennt von Kunden) =====
    'administration' => [
        'label' => 'Administration',
        'icon' => 'admin_panel_settings',
        'items' => [
            ['label' => 'Benutzer', 'page' => 'administration/users'],
            ['label' => 'Rollen', 'page' => 'administration/roles'],
            ['label' => 'Berechtigungen', 'page' => 'administration/permissions'],
        ],
    ],

    // ===== SYSTEM =====
    'system' => [
        'label' => 'System',
        'icon' => 'tune',
        'items' => [
            ['label' => 'Einstellungen', 'page' => 'system/settings'],
            ['label' => 'Sicherheit', 'page' => 'system/security'],
            ['label' => 'Logs', 'page' => 'system/logs'],
            ['label' => 'Backups', 'page' => 'system/backups'],
            ['label' => 'E-Mail', 'page' => 'system/email'],
            ['label' => 'Integrationen', 'page' => 'system/integrations'],
        ],
    ],

    // ===== ENTWICKLER (Advanced) =====
    'developer' => [
        'label' => 'Entwickler',
        'icon' => 'terminal',
        'badge' => 'Advanced', // Visueller Indikator
        'items' => [
            ['label' => 'API', 'page' => 'developer/api'],
            ['label' => 'Webhooks', 'page' => 'developer/webhooks'],
            ['label' => 'Themes', 'page' => 'developer/themes'],
            ['label' => 'Plugins', 'page' => 'developer/plugins'],
            ['label' => 'Debug', 'page' => 'developer/debug'],
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
