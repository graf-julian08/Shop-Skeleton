<?php
/**
 * NavigationController
 * Handles all navigation menu and item operations
 */

class NavigationController
{

    /**
     * Handle creating a new menu item
     */
    public static function handleCreateItem(): array
    {
        try {
            // Check permission
            Auth::requirePermission('navigation.manage');

            // Validate
            $errors = NavigationItem::validate($_POST);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Truncate label if too long
            $label = mb_substr(trim($_POST['label']), 0, 250);

            // Create item (core fields only)
            $itemId = NavigationItem::create([
                'menu_id' => intval($_POST['menu_id']),
                'parent_id' => !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null,
                'label' => $label,
                'type' => $_POST['type'] ?? 'url',
                'reference_id' => !empty($_POST['reference_id']) ? intval($_POST['reference_id']) : null,
                'url' => trim($_POST['url'] ?? ''),
                'target' => $_POST['target'] ?? '_self',
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
            ]);

            if (!$itemId) {
                return ['success' => false, 'message' => 'Fehler beim Erstellen des Menüpunkts.'];
            }

            self::logActivity('navigation.item.created', $itemId, ['label' => $label]);

            return ['success' => true, 'message' => 'Menüpunkt wurde erstellt.', 'id' => $itemId];
        } catch (Exception $e) {
            error_log("NavigationController::handleCreateItem error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Fehler beim Erstellen: ' . $e->getMessage()];
        }
    }

    /**
     * Handle updating a menu item
     */
    public static function handleUpdateItem(int $itemId): array
    {
        try {
            // Check permission
            Auth::requirePermission('navigation.manage');

            // Get existing item
            $item = NavigationItem::find($itemId);
            if (!$item) {
                return ['success' => false, 'message' => 'Menüpunkt nicht gefunden.'];
            }

            // Validate
            $errors = NavigationItem::validate($_POST);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Truncate label if too long
            $label = mb_substr(trim($_POST['label']), 0, 250);

            // Update item (core fields + styling)
            $success = NavigationItem::update($itemId, [
                'parent_id' => !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null,
                'label' => $label,
                'type' => $_POST['type'] ?? 'url',
                'reference_id' => !empty($_POST['reference_id']) ? intval($_POST['reference_id']) : null,
                'url' => trim($_POST['url'] ?? ''),
                'target' => $_POST['target'] ?? '_self',
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                // Icon fields
                'icon' => trim($_POST['icon'] ?? '') ?: null,
                'icon_position' => trim($_POST['icon_position'] ?? 'left') ?: 'left',
                'custom_icon_url' => trim($_POST['custom_icon_url'] ?? '') ?: null,
                // Styling fields
                'custom_color' => trim($_POST['custom_color'] ?? '') ?: null,
                'bg_color' => trim($_POST['bg_color'] ?? '') ?: null,
                'font_weight' => trim($_POST['font_weight'] ?? '') ?: null,
                'text_decoration' => trim($_POST['text_decoration'] ?? '') ?: null,
                'badge_text' => trim($_POST['badge_text'] ?? '') ?: null,
                'badge_color' => trim($_POST['badge_color'] ?? '') ?: null,
                // Mega menu fields
                'mega_enabled' => isset($_POST['mega_enabled']) ? 1 : 0,
                'mega_columns' => intval($_POST['mega_columns'] ?? 1),
                'mega_width' => $_POST['mega_width'] ?? 'auto',
                'mega_image' => trim($_POST['mega_image'] ?? '') ?: null,
                'mega_promo_title' => trim($_POST['mega_promo_title'] ?? '') ?: null,
                'mega_promo_text' => trim($_POST['mega_promo_text'] ?? '') ?: null,
                'mega_promo_link' => trim($_POST['mega_promo_link'] ?? '') ?: null,
            ]);

            if (!$success) {
                return ['success' => false, 'message' => 'Fehler beim Aktualisieren.'];
            }

            self::logActivity('navigation.item.updated', $itemId, ['label' => $label]);

            return ['success' => true, 'message' => 'Menüpunkt wurde aktualisiert.'];
        } catch (Exception $e) {
            error_log("NavigationController::handleUpdateItem error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Fehler beim Aktualisieren: ' . $e->getMessage()];
        }
    }

    /**
     * Handle deleting a menu item
     */
    public static function handleDeleteItem(int $itemId): array
    {
        // Check permission
        Auth::requirePermission('navigation.manage');

        // Get existing item
        $item = NavigationItem::find($itemId);
        if (!$item) {
            return ['success' => false, 'message' => 'Menüpunkt nicht gefunden.'];
        }

        $label = $item['label'];
        $success = NavigationItem::delete($itemId);

        if (!$success) {
            return ['success' => false, 'message' => 'Fehler beim Löschen.'];
        }

        self::logActivity('navigation.item.deleted', $itemId, ['label' => $label]);

        return ['success' => true, 'message' => 'Menüpunkt wurde gelöscht.'];
    }

    /**
     * Handle toggling item visibility
     */
    public static function handleToggleStatus(int $itemId): array
    {
        // Check permission
        Auth::requirePermission('navigation.manage');

        // Get existing item
        $item = NavigationItem::find($itemId);
        if (!$item) {
            return ['success' => false, 'message' => 'Menüpunkt nicht gefunden.'];
        }

        $newStatus = $item['is_active'] ? 0 : 1;
        $success = NavigationItem::update($itemId, ['is_active' => $newStatus]);

        if (!$success) {
            return ['success' => false, 'message' => 'Fehler beim Ändern des Status.'];
        }

        $statusText = $newStatus ? 'aktiviert' : 'deaktiviert';
        self::logActivity('navigation.item.toggled', $itemId, ['status' => $statusText]);

        return ['success' => true, 'message' => "Menüpunkt wurde {$statusText}."];
    }

    /**
     * Handle reordering items (AJAX)
     */
    public static function handleUpdateOrder(): array
    {
        // Check permission
        Auth::requirePermission('navigation.manage');

        $order = json_decode($_POST['order'] ?? '[]', true);

        if (!is_array($order) || empty($order)) {
            return ['success' => false, 'message' => 'Ungültige Daten.'];
        }

        $success = NavigationItem::updateOrder($order);

        if (!$success) {
            return ['success' => false, 'message' => 'Fehler beim Speichern der Reihenfolge.'];
        }

        return ['success' => true, 'message' => 'Reihenfolge gespeichert.'];
    }

    /**
     * Log activity
     */
    private static function logActivity(string $action, int $entityId, array $details = []): void
    {
        try {
            $userId = Auth::user()['id'] ?? null;
            Database::insert('activity_logs', [
                'admin_user_id' => $userId,
                'action' => $action,
                'entity_type' => 'navigation_item',
                'entity_id' => $entityId,
                'details' => json_encode($details),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (Exception $e) {
            // Silently fail - logging should not break functionality
        }
    }
}
