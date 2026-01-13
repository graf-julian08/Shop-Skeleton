<?php
/**
 * Mega Menu API Endpoint
 * Handles all mega menu builder AJAX requests
 */

header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Load dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
Database::configure($database);

require_once __DIR__ . '/../models/MegaMenu.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        // ========== GET OPERATIONS ==========
        case 'get':
            $navItemId = intval($_GET['nav_item_id'] ?? 0);
            if (!$navItemId) {
                throw new Exception('nav_item_id erforderlich');
            }
            echo json_encode([
                'success' => true,
                'data' => MegaMenu::exportAsJson($navItemId)
            ]);
            break;

        case 'get_column':
            $columnId = intval($_GET['column_id'] ?? 0);
            if (!$columnId) {
                throw new Exception('column_id erforderlich');
            }
            echo json_encode([
                'success' => true,
                'column' => MegaMenu::getColumn($columnId)
            ]);
            break;

        case 'get_block':
            $blockId = intval($_GET['block_id'] ?? 0);
            if (!$blockId) {
                throw new Exception('block_id erforderlich');
            }
            echo json_encode([
                'success' => true,
                'block' => MegaMenu::getBlock($blockId)
            ]);
            break;

        // ========== LAYOUT OPERATIONS ==========
        case 'apply_layout':
            $navItemId = intval($_POST['nav_item_id'] ?? 0);
            $layout = $_POST['layout'] ?? '3-col';
            if (!$navItemId) {
                throw new Exception('nav_item_id erforderlich');
            }
            MegaMenu::applyLayout($navItemId, $layout);
            echo json_encode([
                'success' => true,
                'data' => MegaMenu::exportAsJson($navItemId)
            ]);
            break;

        // ========== COLUMN OPERATIONS ==========
        case 'add_column':
            $navItemId = intval($_POST['nav_item_id'] ?? 0);
            $width = intval($_POST['width'] ?? 25);
            if (!$navItemId) {
                throw new Exception('nav_item_id erforderlich');
            }
            $columnId = MegaMenu::createColumn($navItemId, $width);
            echo json_encode([
                'success' => true,
                'column_id' => $columnId,
                'data' => MegaMenu::exportAsJson($navItemId)
            ]);
            break;

        case 'update_column_width':
            $columnId = intval($_POST['column_id'] ?? 0);
            $width = intval($_POST['width'] ?? 25);
            if (!$columnId) {
                throw new Exception('column_id erforderlich');
            }
            MegaMenu::updateColumnWidth($columnId, $width);
            echo json_encode(['success' => true]);
            break;

        case 'delete_column':
            $columnId = intval($_POST['column_id'] ?? 0);
            if (!$columnId) {
                throw new Exception('column_id erforderlich');
            }
            MegaMenu::deleteColumn($columnId);
            echo json_encode(['success' => true]);
            break;

        case 'reorder_columns':
            $navItemId = intval($_POST['nav_item_id'] ?? 0);
            $columnIds = json_decode($_POST['column_ids'] ?? '[]', true);
            if (!$navItemId || empty($columnIds)) {
                throw new Exception('nav_item_id und column_ids erforderlich');
            }
            MegaMenu::reorderColumns($navItemId, $columnIds);
            echo json_encode(['success' => true]);
            break;

        // ========== BLOCK OPERATIONS ==========
        case 'add_block':
            $columnId = intval($_POST['column_id'] ?? 0);
            $blockType = $_POST['block_type'] ?? 'links';
            $config = json_decode($_POST['config'] ?? '{}', true);
            if (!$columnId) {
                throw new Exception('column_id erforderlich');
            }
            $blockId = MegaMenu::createBlock($columnId, $blockType, $config);
            echo json_encode([
                'success' => true,
                'block_id' => $blockId,
                'block' => MegaMenu::getBlock($blockId)
            ]);
            break;

        case 'update_block':
            $blockId = intval($_POST['block_id'] ?? 0);
            $config = json_decode($_POST['config'] ?? '{}', true);
            if (!$blockId) {
                throw new Exception('block_id erforderlich');
            }
            MegaMenu::updateBlock($blockId, $config);
            echo json_encode([
                'success' => true,
                'block' => MegaMenu::getBlock($blockId)
            ]);
            break;

        case 'delete_block':
            $blockId = intval($_POST['block_id'] ?? 0);
            if (!$blockId) {
                throw new Exception('block_id erforderlich');
            }
            MegaMenu::deleteBlock($blockId);
            echo json_encode(['success' => true]);
            break;

        case 'reorder_blocks':
            $columnId = intval($_POST['column_id'] ?? 0);
            $blockIds = json_decode($_POST['block_ids'] ?? '[]', true);
            if (!$columnId || empty($blockIds)) {
                throw new Exception('column_id und block_ids erforderlich');
            }
            MegaMenu::reorderBlocks($columnId, $blockIds);
            echo json_encode(['success' => true]);
            break;

        case 'move_block':
            $blockId = intval($_POST['block_id'] ?? 0);
            $newColumnId = intval($_POST['new_column_id'] ?? 0);
            $order = intval($_POST['order'] ?? 0);
            if (!$blockId || !$newColumnId) {
                throw new Exception('block_id und new_column_id erforderlich');
            }
            MegaMenu::moveBlock($blockId, $newColumnId, $order);
            echo json_encode(['success' => true]);
            break;

        // ========== ELEMENT OPERATIONS (Fullpage Editor) ==========
        case 'get_elements':
            $navItemId = intval($_GET['nav_item_id'] ?? 0);
            if (!$navItemId) {
                throw new Exception('nav_item_id erforderlich');
            }
            $elements = Database::fetchAll(
                "SELECT * FROM mega_menu_elements WHERE navigation_item_id = ? ORDER BY z_index",
                [$navItemId]
            );
            echo json_encode(['success' => true, 'elements' => $elements]);
            break;

        case 'save_element':
            $navItemId = intval($_POST['navigation_item_id'] ?? 0);
            if (!$navItemId) {
                throw new Exception('navigation_item_id erforderlich');
            }

            $id = Database::insert('mega_menu_elements', [
                'navigation_item_id' => $navItemId,
                'element_type' => $_POST['element_type'] ?? 'text',
                'pos_x' => floatval($_POST['pos_x'] ?? 0),
                'pos_y' => floatval($_POST['pos_y'] ?? 0),
                'width' => floatval($_POST['width'] ?? 20),
                'height' => floatval($_POST['height'] ?? 15),
                'z_index' => intval($_POST['z_index'] ?? 0),
                'content_json' => $_POST['content_json'] ?? '{}',
                'style_json' => $_POST['style_json'] ?? '{}'
            ]);

            echo json_encode(['success' => true, 'id' => $id]);
            break;

        case 'delete_all_elements':
            $navItemId = intval($_POST['nav_item_id'] ?? 0);
            if (!$navItemId) {
                throw new Exception('nav_item_id erforderlich');
            }
            Database::delete('mega_menu_elements', 'navigation_item_id = ?', [$navItemId]);
            echo json_encode(['success' => true]);
            break;

        case 'save_all':
            // Batch save all elements (used by autosave)
            $navItemId = intval($_POST['navigation_item_id'] ?? 0);
            $elementsJson = $_POST['elements'] ?? '[]';

            if (!$navItemId) {
                throw new Exception('navigation_item_id erforderlich');
            }

            $elements = json_decode($elementsJson, true);
            if (!is_array($elements)) {
                throw new Exception('Ungültiges elements Format');
            }

            // Delete existing elements
            Database::delete('mega_menu_elements', 'navigation_item_id = ?', [$navItemId]);

            // Insert all new elements with breakpoint positions
            foreach ($elements as $el) {
                // Merge constraints into style for storage (until DB migration)
                $style = $el['style'] ?? [];
                if (isset($el['constraints'])) {
                    $style['constraints'] = $el['constraints'];
                }
                if (isset($el['locked'])) {
                    $style['locked'] = $el['locked'];
                }
                if (isset($el['anchor'])) {
                    $style['anchor'] = $el['anchor'];
                }

                Database::insert('mega_menu_elements', [
                    'navigation_item_id' => $navItemId,
                    'element_type' => $el['element_type'] ?? 'text',
                    // Desktop (default) positions
                    'pos_x' => floatval($el['pos_x'] ?? 0),
                    'pos_y' => floatval($el['pos_y'] ?? 0),
                    'width' => floatval($el['width'] ?? 100),
                    'height' => floatval($el['height'] ?? 60),
                    // Other data
                    'z_index' => intval($el['z_index'] ?? $style['zIndex'] ?? 0),
                    'content_json' => json_encode($el['content'] ?? []),
                    'style_json' => json_encode($style)
                ]);
            }

            echo json_encode(['success' => true, 'saved' => count($elements)]);
            break;

        // ========== TEMPLATE OPERATIONS ==========
        case 'get_templates':
            $templates = Database::fetchAll(
                "SELECT * FROM mega_menu_templates ORDER BY is_system DESC, name ASC"
            );
            echo json_encode(['success' => true, 'templates' => $templates]);
            break;

        case 'save_template':
            $name = $_POST['name'] ?? '';
            if (!$name) {
                throw new Exception('Name erforderlich');
            }

            $id = Database::insert('mega_menu_templates', [
                'shop_id' => 1,
                'name' => $name,
                'description' => $_POST['description'] ?? '',
                'elements_json' => $_POST['elements_json'] ?? '{}',
                'canvas_width' => intval($_POST['canvas_width'] ?? 800),
                'canvas_height' => intval($_POST['canvas_height'] ?? 300),
                'is_system' => 0
            ]);

            echo json_encode(['success' => true, 'id' => $id]);
            break;

        case 'delete_template':
            $templateId = intval($_POST['template_id'] ?? 0);
            if (!$templateId) {
                throw new Exception('template_id erforderlich');
            }
            // Don't delete system templates
            $template = Database::fetch("SELECT is_system FROM mega_menu_templates WHERE id = ?", [$templateId]);
            if ($template && $template['is_system']) {
                throw new Exception('System-Vorlagen können nicht gelöscht werden');
            }
            Database::delete('mega_menu_templates', 'id = ?', [$templateId]);
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Ungültige Aktion']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
