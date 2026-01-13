<?php
/**
 * SEO API
 * Handles Meta-Tags, Sitemap, Redirects, Robots.txt
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
Database::configure($database);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = intval($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        // ===== SEO Settings =====
        case 'get_settings':
            handleGetSettings($shopId);
            break;
        case 'save_settings':
            handleSaveSettings($shopId);
            break;

        // ===== Sitemap =====
        case 'get_sitemap_status':
            handleGetSitemapStatus($shopId);
            break;
        case 'generate_sitemap':
            handleGenerateSitemap($shopId);
            break;
        case 'download_sitemap':
            handleDownloadSitemap($shopId);
            break;
        case 'save_sitemap_settings':
            handleSaveSitemapSettings($shopId);
            break;

        // ===== Redirects =====
        case 'get_redirects':
            handleGetRedirects($shopId);
            break;
        case 'save_redirect':
            handleSaveRedirect($shopId);
            break;
        case 'delete_redirect':
            handleDeleteRedirect($shopId);
            break;
        case 'toggle_redirect':
            handleToggleRedirect($shopId);
            break;

        // ===== Robots.txt =====
        case 'get_robots':
            handleGetRobots($shopId);
            break;
        case 'save_robots':
            handleSaveRobots($shopId);
            break;
        case 'reset_robots':
            handleResetRobots($shopId);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action: ' . $action]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

// =====================================================================
// SEO SETTINGS HANDLERS
// =====================================================================

function handleGetSettings(int $shopId): void
{
    $settings = Database::fetch(
        "SELECT * FROM seo_settings WHERE shop_id = ?",
        [$shopId]
    );

    // Get extended settings from settings table
    $extended = Database::fetch(
        "SELECT setting_value FROM settings WHERE shop_id = ? AND scope = 'shop' AND setting_key = 'seo_extended'",
        [$shopId]
    );

    $extendedData = [];
    if ($extended && !empty($extended['setting_value'])) {
        $extendedData = json_decode($extended['setting_value'], true) ?? [];
    }

    // Default extended values
    $defaultExtended = [
        'meta_keywords' => 'premium, online shop, qualität, exklusiv',
        'sitemap_auto_generate' => 1,
        'include_products' => 1,
        'include_categories' => 1,
        'include_cms_pages' => 1,
        'include_blog' => 0,
        'organization_schema' => 1,
        'product_schema' => 1,
        'breadcrumb_schema' => 1,
        'faq_schema' => 0
    ];

    $extendedData = array_merge($defaultExtended, $extendedData);

    if (!$settings) {
        // Create default settings
        $defaultRobots = getDefaultRobotsTxt($shopId);
        $id = Database::insert('seo_settings', [
            'shop_id' => $shopId,
            'default_meta_title' => 'Mein Online Shop - Premium Produkte',
            'default_meta_description' => 'Entdecken Sie unsere exklusive Auswahl an Premium-Produkten. Hochwertige Qualität, schneller Versand und erstklassiger Kundenservice.',
            'title_separator' => '|',
            'enable_canonical' => 1,
            'robots_txt' => $defaultRobots,
            'enable_sitemap' => 1,
            'sitemap_frequency' => 'weekly',
            'google_analytics_id' => '',
            'facebook_pixel_id' => ''
        ]);

        // Save extended settings
        Database::insert('settings', [
            'shop_id' => $shopId,
            'scope' => 'shop',
            'setting_key' => 'seo_extended',
            'setting_value' => json_encode($defaultExtended)
        ]);

        $settings = Database::fetch("SELECT * FROM seo_settings WHERE id = ?", [$id]);
    }

    // Merge all settings
    $result = array_merge($settings ?: [], $extendedData);

    echo json_encode([
        'success' => true,
        'settings' => $result
    ]);
}

function handleSaveSettings(int $shopId): void
{
    // Core SEO settings (stored in seo_settings table)
    $coreData = [
        'default_meta_title' => trim($_POST['default_meta_title'] ?? ''),
        'default_meta_description' => trim($_POST['default_meta_description'] ?? ''),
        'title_separator' => trim($_POST['title_separator'] ?? '|'),
        'enable_canonical' => intval($_POST['enable_canonical'] ?? 1),
        'google_analytics_id' => trim($_POST['google_analytics_id'] ?? ''),
        'facebook_pixel_id' => trim($_POST['facebook_pixel_id'] ?? '')
    ];

    // Extended settings (stored in settings table as JSON)
    $extendedData = [
        'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
        'organization_schema' => intval($_POST['organization_schema'] ?? 0),
        'product_schema' => intval($_POST['product_schema'] ?? 0),
        'breadcrumb_schema' => intval($_POST['breadcrumb_schema'] ?? 0),
        'faq_schema' => intval($_POST['faq_schema'] ?? 0)
    ];

    $existing = Database::fetch("SELECT id FROM seo_settings WHERE shop_id = ?", [$shopId]);

    if ($existing) {
        Database::update('seo_settings', $coreData, 'shop_id = ?', [$shopId]);
    } else {
        $coreData['shop_id'] = $shopId;
        Database::insert('seo_settings', $coreData);
    }

    // Save extended settings
    $existingExtended = Database::fetch(
        "SELECT id FROM settings WHERE shop_id = ? AND scope = 'shop' AND setting_key = 'seo_extended'",
        [$shopId]
    );

    if ($existingExtended) {
        Database::update('settings', ['setting_value' => json_encode($extendedData)], 'id = ?', [$existingExtended['id']]);
    } else {
        Database::insert('settings', [
            'shop_id' => $shopId,
            'scope' => 'shop',
            'setting_key' => 'seo_extended',
            'setting_value' => json_encode($extendedData)
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'SEO-Einstellungen gespeichert']);
}

// =====================================================================
// SITEMAP HANDLERS
// =====================================================================

function handleGetSitemapStatus(int $shopId): void
{
    // Get core settings
    $settings = Database::fetch("SELECT * FROM seo_settings WHERE shop_id = ?", [$shopId]);

    // Get extended settings
    $extended = Database::fetch(
        "SELECT setting_value FROM settings WHERE shop_id = ? AND scope = 'shop' AND setting_key = 'sitemap_settings'",
        [$shopId]
    );

    $extendedData = [];
    if ($extended && !empty($extended['setting_value'])) {
        $extendedData = json_decode($extended['setting_value'], true) ?? [];
    }

    // Defaults
    $defaults = [
        'sitemap_auto_generate' => 1,
        'include_products' => 1,
        'include_categories' => 1,
        'include_cms_pages' => 1,
        'include_blog' => 0
    ];
    $extendedData = array_merge($defaults, $extendedData);

    // Check if sitemap file exists
    $sitemapPath = __DIR__ . '/../../sitemap.xml';
    $sitemapExists = file_exists($sitemapPath);
    $lastGenerated = null;
    $urlCount = 0;

    if ($sitemapExists) {
        $lastGenerated = date('d.m.Y H:i', filemtime($sitemapPath));
        // Count URLs in sitemap
        $content = file_get_contents($sitemapPath);
        $urlCount = substr_count($content, '<url>');
    }

    echo json_encode([
        'success' => true,
        'exists' => $sitemapExists,
        'last_generated' => $lastGenerated,
        'url_count' => $urlCount,
        'settings' => [
            'enable_sitemap' => $settings['enable_sitemap'] ?? 1,
            'sitemap_auto_generate' => $extendedData['sitemap_auto_generate'],
            'sitemap_frequency' => $settings['sitemap_frequency'] ?? 'weekly',
            'include_products' => $extendedData['include_products'],
            'include_categories' => $extendedData['include_categories'],
            'include_cms_pages' => $extendedData['include_cms_pages'],
            'include_blog' => $extendedData['include_blog']
        ]
    ]);
}

function handleSaveSitemapSettings(int $shopId): void
{
    // Core data for seo_settings table
    $coreData = [
        'enable_sitemap' => intval($_POST['enable_sitemap'] ?? 1),
        'sitemap_frequency' => $_POST['sitemap_frequency'] ?? 'weekly'
    ];

    // Extended data for settings table
    $extendedData = [
        'sitemap_auto_generate' => intval($_POST['sitemap_auto_generate'] ?? 1),
        'include_products' => intval($_POST['include_products'] ?? 1),
        'include_categories' => intval($_POST['include_categories'] ?? 1),
        'include_cms_pages' => intval($_POST['include_cms_pages'] ?? 1),
        'include_blog' => intval($_POST['include_blog'] ?? 0)
    ];

    $existing = Database::fetch("SELECT id FROM seo_settings WHERE shop_id = ?", [$shopId]);

    if ($existing) {
        Database::update('seo_settings', $coreData, 'shop_id = ?', [$shopId]);
    } else {
        $coreData['shop_id'] = $shopId;
        Database::insert('seo_settings', $coreData);
    }

    // Save extended settings
    $existingExtended = Database::fetch(
        "SELECT id FROM settings WHERE shop_id = ? AND scope = 'shop' AND setting_key = 'sitemap_settings'",
        [$shopId]
    );

    if ($existingExtended) {
        Database::update('settings', ['setting_value' => json_encode($extendedData)], 'id = ?', [$existingExtended['id']]);
    } else {
        Database::insert('settings', [
            'shop_id' => $shopId,
            'scope' => 'shop',
            'setting_key' => 'sitemap_settings',
            'setting_value' => json_encode($extendedData)
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Sitemap-Einstellungen gespeichert']);
}

function handleGenerateSitemap(int $shopId): void
{
    $settings = Database::fetch("SELECT * FROM seo_settings WHERE shop_id = ?", [$shopId]);
    $shop = Database::fetch("SELECT * FROM shops WHERE id = ?", [$shopId]);

    // Get extended settings
    $extended = Database::fetch(
        "SELECT setting_value FROM settings WHERE shop_id = ? AND scope = 'shop' AND setting_key = 'sitemap_settings'",
        [$shopId]
    );
    $extendedData = [];
    if ($extended && !empty($extended['setting_value'])) {
        $extendedData = json_decode($extended['setting_value'], true) ?? [];
    }
    $extendedData = array_merge([
        'include_products' => 1,
        'include_categories' => 1,
        'include_cms_pages' => 1,
        'include_blog' => 0
    ], $extendedData);

    $baseUrl = $shop['domain'] ?? 'http://localhost:8085';
    if (!str_starts_with($baseUrl, 'http')) {
        $baseUrl = 'https://' . $baseUrl;
    }

    $urls = [];

    // Always include homepage
    $urls[] = [
        'loc' => $baseUrl . '/',
        'priority' => '1.0',
        'changefreq' => 'daily'
    ];

    // Include products if enabled
    if ($extendedData['include_products']) {
        $products = Database::fetchAll(
            "SELECT slug, updated_at FROM products WHERE shop_id = ? AND status = 'active' AND is_visible = 1",
            [$shopId]
        );
        foreach ($products as $product) {
            $urls[] = [
                'loc' => $baseUrl . '/produkt/' . $product['slug'],
                'lastmod' => date('Y-m-d', strtotime($product['updated_at'])),
                'priority' => '0.8',
                'changefreq' => 'weekly'
            ];
        }
    }

    // Include categories if enabled
    if ($extendedData['include_categories']) {
        $categories = Database::fetchAll(
            "SELECT slug, updated_at FROM categories WHERE shop_id = ? AND is_active = 1",
            [$shopId]
        );
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => $baseUrl . '/kategorie/' . $category['slug'],
                'lastmod' => date('Y-m-d', strtotime($category['updated_at'])),
                'priority' => '0.7',
                'changefreq' => 'weekly'
            ];
        }
    }

    // Include CMS pages if enabled
    if ($extendedData['include_cms_pages']) {
        $cmsPages = Database::fetchAll(
            "SELECT slug, updated_at FROM cms_pages WHERE shop_id = ? AND is_active = 1",
            [$shopId]
        );
        foreach ($cmsPages as $page) {
            $urls[] = [
                'loc' => $baseUrl . '/seite/' . $page['slug'],
                'lastmod' => date('Y-m-d', strtotime($page['updated_at'])),
                'priority' => '0.5',
                'changefreq' => 'monthly'
            ];
        }
    }

    // Generate XML
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $url) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
        if (isset($url['lastmod'])) {
            $xml .= "    <lastmod>" . $url['lastmod'] . "</lastmod>\n";
        }
        $xml .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
        $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
        $xml .= "  </url>\n";
    }

    $xml .= '</urlset>';

    // Save to file
    $sitemapPath = __DIR__ . '/../../sitemap.xml';
    file_put_contents($sitemapPath, $xml);

    echo json_encode([
        'success' => true,
        'message' => 'Sitemap erfolgreich generiert',
        'url_count' => count($urls),
        'last_generated' => date('d.m.Y H:i')
    ]);
}

function handleDownloadSitemap(int $shopId): void
{
    $sitemapPath = __DIR__ . '/../../sitemap.xml';

    if (!file_exists($sitemapPath)) {
        echo json_encode(['success' => false, 'error' => 'Sitemap existiert nicht. Bitte zuerst generieren.']);
        return;
    }

    // Return the sitemap content for download
    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="sitemap.xml"');
    readfile($sitemapPath);
    exit;
}

// =====================================================================
// REDIRECTS HANDLERS
// =====================================================================

function handleGetRedirects(int $shopId): void
{
    $redirects = Database::fetchAll(
        "SELECT * FROM url_redirects WHERE shop_id = ? ORDER BY id DESC",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'redirects' => $redirects
    ]);
}

function handleSaveRedirect(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);
    $sourceUrl = trim($_POST['source_url'] ?? '');
    $targetUrl = trim($_POST['target_url'] ?? '');
    $redirectType = $_POST['redirect_type'] ?? '301';
    $isActive = intval($_POST['is_active'] ?? 1);

    if (empty($sourceUrl) || empty($targetUrl)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Quelle und Ziel sind erforderlich']);
        return;
    }

    // Normalize URLs (ensure they start with /)
    if (!str_starts_with($sourceUrl, '/') && !str_starts_with($sourceUrl, 'http')) {
        $sourceUrl = '/' . $sourceUrl;
    }
    if (!str_starts_with($targetUrl, '/') && !str_starts_with($targetUrl, 'http')) {
        $targetUrl = '/' . $targetUrl;
    }

    $data = [
        'shop_id' => $shopId,
        'source_url' => $sourceUrl,
        'target_url' => $targetUrl,
        'redirect_type' => $redirectType,
        'is_active' => $isActive
    ];

    if ($id) {
        unset($data['shop_id']);
        Database::update('url_redirects', $data, 'id = ? AND shop_id = ?', [$id, $shopId]);
        $resultId = $id;
    } else {
        $resultId = Database::insert('url_redirects', $data);
    }

    // Regenerate .htaccess with redirects
    regenerateHtaccess($shopId);

    echo json_encode([
        'success' => true,
        'id' => $resultId,
        'message' => 'Redirect gespeichert'
    ]);
}

function handleDeleteRedirect(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID erforderlich']);
        return;
    }

    Database::delete('url_redirects', 'id = ? AND shop_id = ?', [$id, $shopId]);

    // Regenerate .htaccess
    regenerateHtaccess($shopId);

    echo json_encode(['success' => true, 'message' => 'Redirect gelöscht']);
}

function handleToggleRedirect(int $shopId): void
{
    $id = intval($_POST['id'] ?? 0);
    $isActive = intval($_POST['is_active'] ?? 0);

    Database::query(
        "UPDATE url_redirects SET is_active = ? WHERE id = ? AND shop_id = ?",
        [$isActive, $id, $shopId]
    );

    // Regenerate .htaccess
    regenerateHtaccess($shopId);

    echo json_encode(['success' => true]);
}

function regenerateHtaccess(int $shopId): void
{
    // Get all active redirects
    $redirects = Database::fetchAll(
        "SELECT * FROM url_redirects WHERE shop_id = ? AND is_active = 1",
        [$shopId]
    );

    // Build redirect rules
    $rules = [];
    $rules[] = "# =============================================";
    $rules[] = "# AUTO-GENERATED REDIRECTS - DO NOT EDIT MANUALLY";
    $rules[] = "# Generated: " . date('Y-m-d H:i:s');
    $rules[] = "# =============================================";
    $rules[] = "";
    $rules[] = "RewriteEngine On";
    $rules[] = "";

    foreach ($redirects as $redirect) {
        $source = preg_quote($redirect['source_url'], '#');
        // Handle wildcards
        $source = str_replace('\*', '(.*)', $source);
        $target = $redirect['target_url'];
        $type = $redirect['redirect_type'];

        // Check if target uses captured group
        if (strpos($redirect['source_url'], '*') !== false) {
            $target = str_replace('*', '$1', $target);
        }

        $rules[] = "# Redirect: {$redirect['source_url']} -> {$redirect['target_url']}";
        $rules[] = "RewriteRule ^" . ltrim($source, '/') . "$ " . $target . " [R=$type,L]";
        $rules[] = "";
    }

    // Write to redirects file (separate from main .htaccess)
    $redirectsPath = __DIR__ . '/../../redirects.htaccess';
    file_put_contents($redirectsPath, implode("\n", $rules));
}

// =====================================================================
// ROBOTS.TXT HANDLERS
// =====================================================================

function handleGetRobots(int $shopId): void
{
    $settings = Database::fetch("SELECT robots_txt FROM seo_settings WHERE shop_id = ?", [$shopId]);

    $robotsTxt = $settings['robots_txt'] ?? getDefaultRobotsTxt($shopId);

    echo json_encode([
        'success' => true,
        'robots_txt' => $robotsTxt
    ]);
}

function handleSaveRobots(int $shopId): void
{
    $robotsTxt = $_POST['robots_txt'] ?? '';

    $existing = Database::fetch("SELECT id FROM seo_settings WHERE shop_id = ?", [$shopId]);

    if ($existing) {
        Database::update('seo_settings', ['robots_txt' => $robotsTxt], 'shop_id = ?', [$shopId]);
    } else {
        Database::insert('seo_settings', [
            'shop_id' => $shopId,
            'robots_txt' => $robotsTxt
        ]);
    }

    // Write to actual robots.txt file
    $robotsPath = __DIR__ . '/../../robots.txt';
    file_put_contents($robotsPath, $robotsTxt);

    echo json_encode(['success' => true, 'message' => 'Robots.txt gespeichert']);
}

function handleResetRobots(int $shopId): void
{
    $defaultRobots = getDefaultRobotsTxt($shopId);

    Database::update('seo_settings', ['robots_txt' => $defaultRobots], 'shop_id = ?', [$shopId]);

    // Write to actual robots.txt file
    $robotsPath = __DIR__ . '/../../robots.txt';
    file_put_contents($robotsPath, $defaultRobots);

    echo json_encode([
        'success' => true,
        'robots_txt' => $defaultRobots,
        'message' => 'Robots.txt auf Standard zurückgesetzt'
    ]);
}

function getDefaultRobotsTxt(int $shopId): string
{
    $shop = Database::fetch("SELECT domain FROM shops WHERE id = ?", [$shopId]);
    $domain = $shop['domain'] ?? 'localhost:8085';

    if (!str_starts_with($domain, 'http')) {
        $domain = 'https://' . $domain;
    }

    return "User-agent: *
Allow: /
Disallow: /admin/
Disallow: /warenkorb/
Disallow: /checkout/
Disallow: /konto/
Disallow: /suche/

# Sitemaps
Sitemap: {$domain}/sitemap.xml";
}
