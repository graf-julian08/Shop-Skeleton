<?php
/**
 * Media API Endpoint
 * Handles all media library AJAX requests
 * 
 * Endpoints:
 * POST   /admin/api/media.php?action=upload   - Upload file
 * GET    /admin/api/media.php?action=list     - List media
 * GET    /admin/api/media.php?action=get      - Get single media
 * POST   /admin/api/media.php?action=update   - Update metadata
 * POST   /admin/api/media.php?action=delete   - Delete media
 */

// Set JSON header
header('Content-Type: application/json');

// Allow CORS for same-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Load config and initialize database
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
Database::configure($database);

// Load Media model
require_once __DIR__ . '/../models/Media.php';

// Get action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'upload':
            handleUpload();
            break;

        case 'list':
            handleList();
            break;

        case 'get':
            handleGet();
            break;

        case 'update':
            handleUpdate();
            break;

        case 'delete':
            handleDelete();
            break;

        case 'folders':
            handleFolders();
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Ungültige Aktion']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server-Fehler: ' . $e->getMessage()]);
}

/**
 * Handle file upload
 */
function handleUpload(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Methode nicht erlaubt']);
        return;
    }

    if (!isset($_FILES['file'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Keine Datei hochgeladen']);
        return;
    }

    $shopId = intval($_POST['shop_id'] ?? 1);
    $folder = preg_replace('/[^a-z0-9_-]/i', '', $_POST['folder'] ?? 'general');

    $result = Media::upload($_FILES['file'], $shopId, $folder);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'media' => $result['media']
        ]);
    } else {
        http_response_code(400);
        echo json_encode($result);
    }
}

/**
 * Handle list media
 */
function handleList(): void
{
    $shopId = intval($_GET['shop_id'] ?? 1);
    $folder = $_GET['folder'] ?? null;
    $limit = min(100, intval($_GET['limit'] ?? 50));
    $offset = intval($_GET['offset'] ?? 0);

    $media = Media::getAll($shopId, $folder, $limit, $offset);
    $folders = Media::getFolderCounts($shopId);

    echo json_encode([
        'success' => true,
        'media' => $media,
        'folders' => $folders,
        'pagination' => [
            'limit' => $limit,
            'offset' => $offset,
            'hasMore' => count($media) === $limit
        ]
    ]);
}

/**
 * Handle get single media
 */
function handleGet(): void
{
    $id = intval($_GET['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID erforderlich']);
        return;
    }

    $media = Media::getById($id);

    if ($media) {
        echo json_encode(['success' => true, 'media' => $media]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Media nicht gefunden']);
    }
}

/**
 * Handle update metadata
 */
function handleUpdate(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Methode nicht erlaubt']);
        return;
    }

    $id = intval($_POST['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID erforderlich']);
        return;
    }

    $data = [
        'alt_text' => $_POST['alt_text'] ?? null,
        'title' => $_POST['title'] ?? null,
        'folder' => $_POST['folder'] ?? null
    ];

    // Remove null values
    $data = array_filter($data, fn($v) => $v !== null);

    $success = Media::update($id, $data);

    echo json_encode([
        'success' => $success,
        'media' => $success ? Media::getById($id) : null
    ]);
}

/**
 * Handle delete
 */
function handleDelete(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Methode nicht erlaubt']);
        return;
    }

    $id = intval($_POST['id'] ?? 0);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID erforderlich']);
        return;
    }

    $result = Media::delete($id);

    if (!$result['success']) {
        http_response_code(400);
    }

    echo json_encode($result);
}

/**
 * Handle get folders
 */
function handleFolders(): void
{
    $shopId = intval($_GET['shop_id'] ?? 1);
    $folders = Media::getFolderCounts($shopId);

    echo json_encode([
        'success' => true,
        'folders' => $folders
    ]);
}
