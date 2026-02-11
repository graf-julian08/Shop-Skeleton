<?php
/**
 * Public API — No Authentication Required
 * Serves content to the Yves-Frontend (collaborations, CMS, settings)
 * 
 * Usage: api/public.php?action=get_collaborations
 *        api/public.php?action=get_cms_section&slug=about-me
 *        api/public.php?action=get_site_settings
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';

Database::configure($database);

$action = $_GET['action'] ?? '';
$shopId = 1;

try {
    switch ($action) {
        case 'get_collaborations':
            handlePublicCollaborations($shopId);
            break;
        case 'get_cms_section':
            handleCmsSection($shopId);
            break;
        case 'get_cms_page':
            handleCmsPage($shopId);
            break;
        case 'get_site_settings':
            handleSiteSettings($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

// =====================================================================
// GET PUBLIC COLLABORATIONS (only active ones)
// =====================================================================
function handlePublicCollaborations(int $shopId): void
{
    $collaborations = Database::fetchAll(
        "SELECT c.id, c.name, c.slug, c.short_description, c.description, c.video_url, c.is_featured, c.sort_order,
                (SELECT ci.image_url FROM collaboration_images ci 
                 WHERE ci.collaboration_id = c.id ORDER BY ci.sort_order ASC LIMIT 1) as thumbnail
         FROM collaborations c
         WHERE c.shop_id = ? AND c.status = 'active'
         ORDER BY c.is_featured DESC, c.sort_order ASC, c.created_at DESC",
        [$shopId]
    );

    // Get all images for each collaboration
    foreach ($collaborations as &$collab) {
        $collab['images'] = Database::fetchAll(
            "SELECT image_url, alt_text FROM collaboration_images 
             WHERE collaboration_id = ? ORDER BY sort_order ASC",
            [$collab['id']]
        );
    }

    echo json_encode(['success' => true, 'collaborations' => $collaborations]);
}

// =====================================================================
// GET CMS SECTION BY SLUG (e.g., "about-me", "hero", "stats")
// =====================================================================
function handleCmsSection(int $shopId): void
{
    $slug = $_GET['slug'] ?? '';
    if (empty($slug)) {
        echo json_encode(['success' => false, 'error' => 'Slug required']);
        return;
    }

    $section = Database::fetch(
        "SELECT id, title, slug, content, meta_title, meta_description, status 
         FROM cms_pages 
         WHERE shop_id = ? AND slug = ? AND status = 'published'",
        [$shopId, $slug]
    );

    if (!$section) {
        echo json_encode(['success' => false, 'error' => 'Section not found']);
        return;
    }

    // Parse the content as JSON if it's JSON, otherwise return as HTML
    $content = $section['content'];
    $parsed = json_decode($content, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $section['content_data'] = $parsed;
    }

    echo json_encode(['success' => true, 'section' => $section]);
}

// =====================================================================
// GET CMS PAGE BY SLUG
// =====================================================================
function handleCmsPage(int $shopId): void
{
    $slug = $_GET['slug'] ?? '';
    if (empty($slug)) {
        echo json_encode(['success' => false, 'error' => 'Slug required']);
        return;
    }

    $page = Database::fetch(
        "SELECT title, slug, content, meta_title, meta_description 
         FROM cms_pages WHERE shop_id = ? AND slug = ? AND status = 'published'",
        [$shopId, $slug]
    );

    if (!$page) {
        echo json_encode(['success' => false, 'error' => 'Page not found']);
        return;
    }

    echo json_encode(['success' => true, 'page' => $page]);
}

// =====================================================================
// GET SITE SETTINGS (design, general)
// =====================================================================
function handleSiteSettings(int $shopId): void
{
    // Get design settings
    $design = Database::fetch(
        "SELECT color_primary, color_secondary, color_accent, 
                logo_url, favicon_url, font_heading, font_body
         FROM shop_design WHERE shop_id = ?",
        [$shopId]
    );

    // Get general settings from EAV table
    $settingsRows = Database::fetchAll(
        "SELECT setting_key, setting_value FROM settings WHERE shop_id = ? AND setting_key IN 
         ('shop_name', 'shop_description', 'social_instagram', 'social_linkedin', 'social_twitter', 
          'contact_email', 'footer_copyright')",
        [$shopId]
    );

    $settings = [];
    foreach ($settingsRows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    echo json_encode([
        'success' => true,
        'design' => $design ?: [],
        'settings' => $settings,
    ]);
}
