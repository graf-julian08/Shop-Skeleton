<?php
/**
 * Collaborations API
 * CRUD operations for collaboration management
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../models/Collaboration.php';

Database::configure($database);
Auth::init();

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_collaborations':
            handleGetCollaborations($shopId);
            break;
        case 'get_collaboration':
            handleGetCollaboration($shopId);
            break;
        case 'save_collaboration':
            handleSaveCollaboration($shopId);
            break;
        case 'delete_collaboration':
            handleDeleteCollaboration($shopId);
            break;
        case 'toggle_status':
            handleToggleStatus($shopId);
            break;
        case 'get_stats':
            handleGetStats($shopId);
            break;
        case 'bulk_action':
            handleBulkAction($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// GET ALL COLLABORATIONS
// =====================================================================
function handleGetCollaborations(int $shopId): void
{
    $filters = [
        'status' => $_GET['status'] ?? '',
        'search' => trim($_GET['search'] ?? ''),
        'sort_by' => $_GET['sort_by'] ?? 'created_at',
        'sort_dir' => $_GET['sort_dir'] ?? 'DESC',
    ];

    $collaborations = Collaboration::allForShop($shopId, $filters);

    echo json_encode([
        'success' => true,
        'collaborations' => $collaborations,
    ]);
}

// =====================================================================
// GET SINGLE COLLABORATION
// =====================================================================
function handleGetCollaboration(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid collaboration ID']);
        return;
    }

    $collab = Collaboration::find($id, $shopId);
    if (!$collab) {
        echo json_encode(['success' => false, 'error' => 'Collaboration not found']);
        return;
    }

    echo json_encode(['success' => true, 'collaboration' => $collab]);
}

// =====================================================================
// SAVE COLLABORATION (Create / Update)
// =====================================================================
function handleSaveCollaboration(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    // Validation
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Name ist erforderlich';
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        return;
    }

    $slug = Collaboration::generateSlug($shopId, $_POST['slug'] ?? $name, $id);

    $data = [
        'shop_id' => $shopId,
        'name' => $name,
        'slug' => $slug,
        'short_description' => trim($_POST['short_description'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'video_url' => trim($_POST['video_url'] ?? '') ?: null,
        'status' => $_POST['status'] ?? 'draft',
        'is_featured' => isset($_POST['is_featured']) ? (int) $_POST['is_featured'] : 0,
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'meta_title' => trim($_POST['meta_title'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
    ];

    if ($id > 0) {
        Collaboration::update($id, $shopId, $data);
        $collabId = $id;
        $message = 'Kollaboration aktualisiert';
    } else {
        $collabId = Collaboration::create($data);
        $message = 'Kollaboration erstellt';
    }

    // Handle image uploads
    $uploadDir = __DIR__ . '/../uploads/collaborations/' . $collabId . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Delete specified images
    if (!empty($_POST['delete_image_ids'])) {
        $deleteIds = json_decode($_POST['delete_image_ids'], true) ?: [];
        foreach ($deleteIds as $imgId) {
            $img = Database::fetch(
                "SELECT image_url FROM collaboration_images WHERE id = ? AND collaboration_id = ?",
                [(int) $imgId, $collabId]
            );
            if ($img) {
                $filePath = __DIR__ . '/../' . $img['image_url'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                Database::delete('collaboration_images', 'id = ?', [(int) $imgId]);
            }
        }
    }

    // Update sort order for existing images
    if (!empty($_POST['image_order'])) {
        $imageOrder = json_decode($_POST['image_order'], true) ?: [];
        foreach ($imageOrder as $order => $imgId) {
            if (is_numeric($imgId)) {
                Database::update(
                    'collaboration_images',
                    ['sort_order' => (int) $order],
                    'id = ? AND collaboration_id = ?',
                    [(int) $imgId, $collabId]
                );
            }
        }
    }

    // Get current max sort order
    $maxSort = Database::fetch(
        "SELECT COALESCE(MAX(sort_order), -1) as max_sort FROM collaboration_images WHERE collaboration_id = ?",
        [$collabId]
    );
    $sortOrder = ($maxSort['max_sort'] ?? -1) + 1;

    // Handle new image uploads
    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
        $files = $_FILES['images'];
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK)
                continue;
            if ($files['size'][$i] > 5 * 1024 * 1024)
                continue;

            $originalName = $files['name'][$i];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($ext, $allowedTypes))
                continue;

            $newFilename = uniqid('collab_') . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $newFilename;

            if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                $imageUrl = 'uploads/collaborations/' . $collabId . '/' . $newFilename;
                Database::insert('collaboration_images', [
                    'collaboration_id' => $collabId,
                    'image_url' => $imageUrl,
                    'alt_text' => pathinfo($originalName, PATHINFO_FILENAME),
                    'sort_order' => $sortOrder,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $sortOrder++;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'id' => $collabId,
    ]);
}

// =====================================================================
// DELETE COLLABORATION
// =====================================================================
function handleDeleteCollaboration(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid collaboration ID']);
        return;
    }

    $deleted = Collaboration::delete($id, $shopId);

    // Also try to remove the upload directory
    $uploadDir = __DIR__ . '/../uploads/collaborations/' . $id;
    if (is_dir($uploadDir)) {
        array_map('unlink', glob("$uploadDir/*"));
        @rmdir($uploadDir);
    }

    echo json_encode([
        'success' => $deleted,
        'message' => $deleted ? 'Kollaboration gelöscht' : 'Kollaboration nicht gefunden',
    ]);
}

// =====================================================================
// TOGGLE STATUS
// =====================================================================
function handleToggleStatus(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'draft';

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid ID']);
        return;
    }

    $validStatuses = ['draft', 'active', 'archived'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        return;
    }

    Collaboration::update($id, $shopId, ['status' => $status]);

    $labels = ['draft' => 'Entwurf', 'active' => 'Aktiv', 'archived' => 'Archiviert'];
    echo json_encode([
        'success' => true,
        'message' => 'Status geändert zu: ' . $labels[$status],
    ]);
}

// =====================================================================
// GET STATS
// =====================================================================
function handleGetStats(int $shopId): void
{
    $stats = Collaboration::getStats($shopId);
    echo json_encode(['success' => true, 'stats' => $stats]);
}

// =====================================================================
// BULK ACTION
// =====================================================================
function handleBulkAction(int $shopId): void
{
    $bulkAction = $_POST['bulk_action'] ?? '';
    $ids = json_decode($_POST['ids'] ?? '[]', true);

    if (empty($ids)) {
        echo json_encode(['success' => false, 'error' => 'No items selected']);
        return;
    }

    $count = 0;
    foreach ($ids as $id) {
        $id = (int) $id;
        if ($bulkAction === 'delete') {
            if (Collaboration::delete($id, $shopId))
                $count++;
        } elseif (in_array($bulkAction, ['activate', 'deactivate', 'archive'])) {
            $statusMap = ['activate' => 'active', 'deactivate' => 'draft', 'archive' => 'archived'];
            Collaboration::update($id, $shopId, ['status' => $statusMap[$bulkAction]]);
            $count++;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => $count . ' Kollaboration(en) aktualisiert',
    ]);
}
