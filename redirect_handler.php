<?php
/**
 * Redirect Handler
 * Checks if the current URL matches any active redirects and performs the redirect.
 * Include this file at the beginning of your index.php or router.
 */

function checkAndApplyRedirects(PDO $db, int $shopId = 1): void
{
    // Get the current request URI
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

    // Remove query string for matching
    $path = parse_url($requestUri, PHP_URL_PATH);

    // Get all active redirects
    $stmt = $db->prepare(
        "SELECT id, source_url, target_url, redirect_type 
         FROM url_redirects 
         WHERE shop_id = ? AND is_active = 1"
    );
    $stmt->execute([$shopId]);
    $redirects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($redirects as $redirect) {
        $source = $redirect['source_url'];
        $target = $redirect['target_url'];
        $type = $redirect['redirect_type'];

        // Check for exact match
        if ($path === $source) {
            // Update hit counter
            updateRedirectHits($db, $redirect['id']);

            // Perform redirect
            header("Location: $target", true, (int) $type);
            exit;
        }

        // Check for wildcard match
        if (strpos($source, '*') !== false) {
            // Convert wildcard to regex
            $pattern = '/^' . str_replace(['/', '*'], ['\/', '(.*)'], $source) . '$/';

            if (preg_match($pattern, $path, $matches)) {
                // Update hit counter
                updateRedirectHits($db, $redirect['id']);

                // Replace wildcards in target
                $redirectTarget = $target;
                if (isset($matches[1])) {
                    $redirectTarget = str_replace('*', $matches[1], $target);
                }

                // Perform redirect
                header("Location: $redirectTarget", true, (int) $type);
                exit;
            }
        }
    }
}

function updateRedirectHits(PDO $db, int $redirectId): void
{
    try {
        $stmt = $db->prepare("UPDATE url_redirects SET hits = COALESCE(hits, 0) + 1 WHERE id = ?");
        $stmt->execute([$redirectId]);
    } catch (Exception $e) {
        // Silently fail - don't break the redirect if hits update fails
    }
}

// =========================================================================
// Standalone execution check
// If this file is accessed directly, it will check and apply redirects
// =========================================================================
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    // This file is being accessed directly (for testing or direct includes)
    require_once __DIR__ . '/admin/config.php';
    require_once __DIR__ . '/admin/includes/Database.php';

    Database::configure($database);
    checkAndApplyRedirects(Database::getInstance());
}
