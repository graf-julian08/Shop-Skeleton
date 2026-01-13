<?php
/**
 * CMS Controller
 * Handles form processing for CMS pages
 */

class CmsController {
    
    /**
     * Get default shop ID
     */
    private static function getShopId(): int {
        $shop = Shop::getDefault();
        return $shop['id'] ?? 1;
    }
    
    /**
     * Handle create page form submission
     */
    public static function handleCreate(): array {
        $result = ['success' => false, 'message' => '', 'errors' => [], 'page_id' => null];
        
        // Check permission
        if (!Auth::can('cms.manage')) {
            $result['message'] = 'Keine Berechtigung für diese Aktion.';
            return $result;
        }
        
        $shopId = self::getShopId();
        
        // Get form data
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $content = $_POST['content'] ?? '';
        $layout = $_POST['layout'] ?? 'default';
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDescription = trim($_POST['meta_description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        
        // Auto-generate slug if empty
        if (empty($slug) && !empty($title)) {
            $slug = CmsPage::generateSlug($title, $shopId);
        }
        
        // Validate
        $errors = CmsPage::validate(['title' => $title, 'slug' => $slug], $shopId);
        
        if (!empty($errors)) {
            $result['errors'] = $errors;
            $result['message'] = 'Bitte korrigieren Sie die Fehler.';
            return $result;
        }
        
        // Create page
        try {
            $pageId = CmsPage::create([
                'shop_id' => $shopId,
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'layout' => $layout,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'is_active' => $isActive,
                'sort_order' => $sortOrder,
            ]);
            
            $result['success'] = true;
            $result['page_id'] = $pageId;
            $result['message'] = 'Seite wurde erstellt.';
            
            self::logActivity('cms.page.created', $pageId);
            
        } catch (Exception $e) {
            $result['message'] = 'Fehler beim Erstellen der Seite.';
        }
        
        return $result;
    }
    
    /**
     * Handle update page form submission
     */
    public static function handleUpdate(int $pageId): array {
        $result = ['success' => false, 'message' => '', 'errors' => []];
        
        // Check permission
        if (!Auth::can('cms.manage')) {
            $result['message'] = 'Keine Berechtigung für diese Aktion.';
            return $result;
        }
        
        $shopId = self::getShopId();
        
        // Get existing page (shop-scoped)
        $page = CmsPage::findForShop($pageId, $shopId);
        if (!$page) {
            $result['message'] = 'Seite nicht gefunden.';
            return $result;
        }
        
        // Get form data
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $content = $_POST['content'] ?? '';
        $layout = $_POST['layout'] ?? 'default';
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDescription = trim($_POST['meta_description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        
        // Auto-generate slug if empty
        if (empty($slug) && !empty($title)) {
            $slug = CmsPage::generateSlug($title, $shopId, $pageId);
        }
        
        // Validate (exclude current page from slug uniqueness check)
        $errors = CmsPage::validate(['title' => $title, 'slug' => $slug], $shopId, $pageId);
        
        if (!empty($errors)) {
            $result['errors'] = $errors;
            $result['message'] = 'Bitte korrigieren Sie die Fehler.';
            return $result;
        }
        
        // Update page
        try {
            CmsPage::update($pageId, [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'layout' => $layout,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'is_active' => $isActive,
                'sort_order' => $sortOrder,
            ]);
            
            $result['success'] = true;
            $result['message'] = 'Seite wurde aktualisiert.';
            
            self::logActivity('cms.page.updated', $pageId);
            
        } catch (Exception $e) {
            $result['message'] = 'Fehler beim Aktualisieren der Seite.';
        }
        
        return $result;
    }
    
    /**
     * Handle delete page
     */
    public static function handleDelete(int $pageId): array {
        $result = ['success' => false, 'message' => ''];
        
        // Check permission
        if (!Auth::can('cms.manage')) {
            $result['message'] = 'Keine Berechtigung für diese Aktion.';
            return $result;
        }
        
        $shopId = self::getShopId();
        
        // Get existing page (shop-scoped)
        $page = CmsPage::findForShop($pageId, $shopId);
        if (!$page) {
            $result['message'] = 'Seite nicht gefunden.';
            return $result;
        }
        
        // Delete page
        try {
            if (CmsPage::delete($pageId)) {
                $result['success'] = true;
                $result['message'] = 'Seite wurde gelöscht.';
                
                self::logActivity('cms.page.deleted', $pageId);
            } else {
                $result['message'] = 'Fehler beim Löschen der Seite.';
            }
        } catch (Exception $e) {
            $result['message'] = 'Fehler beim Löschen der Seite.';
        }
        
        return $result;
    }
    
    /**
     * Toggle page status (active/inactive)
     */
    public static function handleToggleStatus(int $pageId): array {
        $result = ['success' => false, 'message' => ''];
        
        // Check permission
        if (!Auth::can('cms.manage')) {
            $result['message'] = 'Keine Berechtigung für diese Aktion.';
            return $result;
        }
        
        $shopId = self::getShopId();
        
        // Get existing page (shop-scoped)
        $page = CmsPage::findForShop($pageId, $shopId);
        if (!$page) {
            $result['message'] = 'Seite nicht gefunden.';
            return $result;
        }
        
        // Toggle status
        $newStatus = $page['is_active'] ? 0 : 1;
        
        try {
            CmsPage::update($pageId, ['is_active' => $newStatus]);
            
            $result['success'] = true;
            $result['message'] = $newStatus ? 'Seite veröffentlicht.' : 'Seite als Entwurf gespeichert.';
            
            self::logActivity('cms.page.status_changed', $pageId);
            
        } catch (Exception $e) {
            $result['message'] = 'Fehler beim Ändern des Status.';
        }
        
        return $result;
    }
    
    /**
     * Log activity to activity_logs table
     */
    private static function logActivity(string $action, int $entityId): void {
        try {
            $shopId = self::getShopId();
            Database::insert('activity_logs', [
                'shop_id' => $shopId,
                'user_type' => 'admin',
                'user_id' => Auth::id(),
                'action' => $action,
                'entity_type' => 'cms_page',
                'entity_id' => $entityId,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (Exception $e) {
            // Silently fail - logging should not break the main operation
        }
    }
}
