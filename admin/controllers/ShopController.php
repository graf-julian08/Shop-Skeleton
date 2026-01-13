<?php
/**
 * Shop Controller
 * Handles form processing for shop settings
 */

class ShopController
{
    /**
     * Handle general settings form submission
     */
    public static function handleGeneralSettings(): array
    {
        $result = ['success' => false, 'message' => '', 'errors' => []];

        // Check permission
        if (!Auth::can('shop.settings')) {
            $result['message'] = 'Keine Berechtigung für diese Aktion.';
            return $result;
        }

        // Validate required fields
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $result['errors']['name'] = 'Shop-Name ist erforderlich.';
        }

        // Get current shop
        $shop = Shop::getDefault();
        if (!$shop) {
            $result['message'] = 'Shop nicht gefunden.';
            return $result;
        }

        // If validation errors, return early
        if (!empty($result['errors'])) {
            $result['message'] = 'Bitte korrigieren Sie die Fehler.';
            return $result;
        }

        // Prepare data for update
        $data = [
            'name' => $name,
            'domain' => trim($_POST['domain'] ?? $shop['domain']),
            'description' => trim($_POST['description'] ?? ''),
            'email' => trim($_POST['email'] ?? $shop['email']),
            'phone' => trim($_POST['phone'] ?? $shop['phone']),
            'default_currency' => $_POST['default_currency'] ?? $shop['default_currency'],
            'timezone' => $_POST['timezone'] ?? $shop['timezone'],
            'date_format' => $_POST['date_format'] ?? $shop['date_format'],
            'weight_unit' => $_POST['weight_unit'] ?? $shop['weight_unit'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
        ];

        // Update shop
        if (Shop::update($shop['id'], $data)) {
            $result['success'] = true;
            $result['message'] = 'Einstellungen wurden gespeichert.';

            // Log activity
            self::logActivity('shop.settings.updated', $shop['id']);
        } else {
            $result['message'] = 'Fehler beim Speichern.';
        }

        return $result;
    }

    /**
     * Handle design settings form submission
     */
    public static function handleDesignSettings(): array
    {
        $result = ['success' => false, 'message' => '', 'errors' => []];

        // Check permission
        if (!Auth::can('shop.design')) {
            $result['message'] = 'Keine Berechtigung für diese Aktion.';
            return $result;
        }

        // Get current design settings
        $design = ShopDesign::getDefault();
        if (!$design) {
            $result['message'] = 'Design-Einstellungen nicht gefunden.';
            return $result;
        }

        // Validate color fields
        $colorFields = ['color_primary', 'color_secondary', 'color_accent', 'color_text'];
        foreach ($colorFields as $field) {
            $value = trim($_POST[$field] ?? '');
            if (!empty($value) && !ShopDesign::isValidHexColor($value)) {
                $result['errors'][$field] = 'Ungültiges Farbformat (z.B. #7c3aed)';
            }
        }

        // Handle logo upload
        $logoPath = $design['logo_path'];
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $logoUpload = FileUpload::upload('logo_file', 'image', 'logos');
            if ($logoUpload['success']) {
                // Delete old logo if exists
                if (!empty($design['logo_path'])) {
                    FileUpload::delete($design['logo_path']);
                }
                $logoPath = $logoUpload['path'];
            } elseif ($logoUpload['error']) {
                $result['errors']['logo_file'] = $logoUpload['error'];
            }
        }

        // Handle favicon upload
        $faviconPath = $design['favicon_path'];
        if (isset($_FILES['favicon_file']) && $_FILES['favicon_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $faviconUpload = FileUpload::upload('favicon_file', 'favicon', 'favicons');
            if ($faviconUpload['success']) {
                // Delete old favicon if exists
                if (!empty($design['favicon_path'])) {
                    FileUpload::delete($design['favicon_path']);
                }
                $faviconPath = $faviconUpload['path'];
            } elseif ($faviconUpload['error']) {
                $result['errors']['favicon_file'] = $faviconUpload['error'];
            }
        }

        // If validation errors, return early
        if (!empty($result['errors'])) {
            $result['message'] = 'Bitte korrigieren Sie die Fehler.';
            return $result;
        }

        // Prepare data for update
        $data = [
            'color_primary' => trim($_POST['color_primary'] ?? $design['color_primary']),
            'color_secondary' => trim($_POST['color_secondary'] ?? $design['color_secondary']),
            'color_accent' => trim($_POST['color_accent'] ?? $design['color_accent']),
            'color_text' => trim($_POST['color_text'] ?? $design['color_text']),
            'logo_path' => $logoPath,
            'favicon_path' => $faviconPath,
            'font_heading' => $_POST['font_heading'] ?? $design['font_heading'],
            'font_body' => $_POST['font_body'] ?? $design['font_body'],
            'header_style' => $_POST['header_style'] ?? $design['header_style'],
            'footer_style' => $_POST['footer_style'] ?? $design['footer_style'],
        ];

        // Update design
        if (ShopDesign::update($design['id'], $data)) {
            $result['success'] = true;
            $result['message'] = 'Design-Einstellungen wurden gespeichert.';

            // Log activity
            self::logActivity('shop.design.updated', $design['shop_id']);
        } else {
            $result['message'] = 'Fehler beim Speichern.';
        }

        return $result;
    }

    /**
     * Log activity to activity_logs table
     */
    private static function logActivity(string $action, int $entityId): void
    {
        try {
            Database::insert('activity_logs', [
                'shop_id' => $entityId,
                'user_type' => 'admin',
                'user_id' => Auth::id(),
                'action' => $action,
                'entity_type' => 'shop',
                'entity_id' => $entityId,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (Exception $e) {
            // Silently fail - logging should not break the main operation
        }
    }
}

