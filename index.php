<?php
/**
 * Main Entry Point
 * Handles URL redirects before routing to admin panel
 */

// Load admin config and database
require_once __DIR__ . '/admin/config.php';
require_once __DIR__ . '/admin/includes/Database.php';
Database::configure($database);

$shopId = 1; // Default shop

// Get current request path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

// Skip redirect check for admin, api, and static files
$skipPaths = ['/admin', '/api', '/uploads', '/sitemap.xml', '/robots.txt'];
$shouldCheck = true;
foreach ($skipPaths as $skip) {
    if (str_starts_with($path, $skip) || $path === '/favicon.ico') {
        $shouldCheck = false;
        break;
    }
}

if ($shouldCheck && $path !== '/') {
    // Check for matching redirects in database
    try {
        $redirects = Database::fetchAll(
            "SELECT id, source_url, target_url, redirect_type 
             FROM url_redirects 
             WHERE shop_id = ? AND is_active = 1",
            [$shopId]
        );

        foreach ($redirects as $redirect) {
            $source = $redirect['source_url'];
            $target = $redirect['target_url'];
            $type = (int) $redirect['redirect_type'];

            // Exact match
            if ($path === $source) {
                header("Location: $target", true, $type);
                exit;
            }

            // Wildcard match (source ends with *)
            if (str_ends_with($source, '*')) {
                $prefix = rtrim($source, '*');
                if (str_starts_with($path, $prefix)) {
                    $suffix = substr($path, strlen($prefix));
                    $finalTarget = str_contains($target, '*') ? str_replace('*', $suffix, $target) : $target;
                    header("Location: $finalTarget", true, $type);
                    exit;
                }
            }
        }
    } catch (Exception $e) {
        // Silently continue if DB error
    }
}

// Default: Forward to admin panel
if ($path === '/' || $path === '') {
    header('Location: /admin/index.php');
    exit;
}

// Handle non-admin paths (shop frontend would go here)
// For now, redirect unknown paths to admin
if (!str_starts_with($path, '/admin')) {
    // Check if this looks like an admin page request
    if (isset($_GET['page'])) {
        header('Location: /admin/index.php?' . $_SERVER['QUERY_STRING']);
        exit;
    }

    // Otherwise show 404
    http_response_code(404);
    echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head>";
    echo "<body style='font-family:system-ui;background:#0f172a;color:#e2e8f0;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;'>";
    echo "<div style='text-align:center;'>";
    echo "<h1 style='font-size:72px;margin:0;color:#3b82f6;'>404</h1>";
    echo "<p style='font-size:20px;opacity:0.7;'>Seite nicht gefunden</p>";
    echo "<a href='/admin/' style='color:#3b82f6;text-decoration:none;'>Zum Admin Panel →</a>";
    echo "</div></body></html>";
    exit;
}
