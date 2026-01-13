<?php
/**
 * CmsPage Model
 * Handles database operations for the cms_pages table
 */

class CmsPage {
    /**
     * Get page by ID
     */
    public static function find(int $id): ?array {
        return Database::fetch("SELECT * FROM cms_pages WHERE id = ?", [$id]);
    }
    
    /**
     * Get page by ID for specific shop (shop-scoped)
     */
    public static function findForShop(int $id, int $shopId): ?array {
        return Database::fetch(
            "SELECT * FROM cms_pages WHERE id = ? AND shop_id = ?", 
            [$id, $shopId]
        );
    }
    
    /**
     * Get page by slug for specific shop
     */
    public static function findBySlug(string $slug, int $shopId): ?array {
        return Database::fetch(
            "SELECT * FROM cms_pages WHERE slug = ? AND shop_id = ?", 
            [$slug, $shopId]
        );
    }
    
    /**
     * Get all pages for a shop
     * @param string $orderBy Optional custom order (default: sort_order ASC, title ASC)
     */
    public static function allForShop(int $shopId, array $filters = [], string $orderBy = ''): array {
        $sql = "SELECT * FROM cms_pages WHERE shop_id = ?";
        $params = [$shopId];
        
        // Filter by status
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $sql .= " AND is_active = 1";
            } elseif ($filters['status'] === 'draft') {
                $sql .= " AND is_active = 0";
            }
        }
        
        // Search by title
        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE ? OR slug LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Custom order or default
        if (!empty($orderBy)) {
            $sql .= " ORDER BY " . $orderBy;
        } else {
            $sql .= " ORDER BY sort_order ASC, title ASC";
        }
        
        return Database::fetchAll($sql, $params);
    }
    
    /**
     * Count pages for a shop
     */
    public static function countForShop(int $shopId): int {
        return Database::count('cms_pages', 'shop_id = ?', [$shopId]);
    }
    
    /**
     * Create a new page
     */
    public static function create(array $data): int {
        $allowed = [
            'shop_id', 'title', 'slug', 'content', 'layout',
            'meta_title', 'meta_description', 'is_active', 'sort_order'
        ];
        
        $filtered = array_intersect_key($data, array_flip($allowed));
        
        // Set defaults
        $defaults = [
            'layout' => 'default',
            'is_active' => 0,
            'sort_order' => 0,
        ];
        
        $filtered = array_merge($defaults, $filtered);
        
        return Database::insert('cms_pages', $filtered);
    }
    
    /**
     * Update a page
     */
    public static function update(int $id, array $data): bool {
        $allowed = [
            'title', 'slug', 'content', 'layout',
            'meta_title', 'meta_description', 'is_active', 'sort_order'
        ];
        
        $filtered = array_intersect_key($data, array_flip($allowed));
        
        if (empty($filtered)) {
            return false;
        }
        
        return Database::update('cms_pages', $filtered, 'id = ?', [$id]) >= 0;
    }
    
    /**
     * Delete a page
     */
    public static function delete(int $id): bool {
        return Database::delete('cms_pages', 'id = ?', [$id]) > 0;
    }
    
    /**
     * Check if slug is unique for shop (excluding specific page ID)
     */
    public static function isSlugUnique(string $slug, int $shopId, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM cms_pages WHERE slug = ? AND shop_id = ?";
        $params = [$slug, $shopId];
        
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        return Database::fetch($sql, $params) === null;
    }
    
    /**
     * Generate unique slug from title
     */
    public static function generateSlug(string $title, int $shopId, ?int $excludeId = null): string {
        // Convert to lowercase and replace spaces with dashes
        $slug = strtolower(trim($title));
        
        // Replace German umlauts
        $replacements = [
            'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
            'Ä' => 'ae', 'Ö' => 'oe', 'Ü' => 'ue'
        ];
        $slug = str_replace(array_keys($replacements), array_values($replacements), $slug);
        
        // Remove special characters, keep alphanumeric and dashes
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        
        // Remove multiple consecutive dashes
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim dashes from ends
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $baseSlug = $slug;
        $counter = 1;
        
        while (!self::isSlugUnique($slug, $shopId, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Get available layouts
     */
    public static function getLayouts(): array {
        return [
            'default' => 'Standard',
            'full_width' => 'Volle Breite',
            'sidebar_left' => 'Sidebar Links',
            'sidebar_right' => 'Sidebar Rechts',
        ];
    }
    
    /**
     * Validate page data
     */
    public static function validate(array $data, int $shopId, ?int $excludeId = null): array {
        $errors = [];
        
        // Title required
        if (empty(trim($data['title'] ?? ''))) {
            $errors['title'] = 'Titel ist erforderlich.';
        }
        
        // Slug validation
        $slug = trim($data['slug'] ?? '');
        if (!empty($slug)) {
            // Check format
            if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
                $errors['slug'] = 'Slug darf nur Kleinbuchstaben, Zahlen und Bindestriche enthalten.';
            } elseif (!self::isSlugUnique($slug, $shopId, $excludeId)) {
                $errors['slug'] = 'Dieser Slug wird bereits verwendet.';
            }
        }
        
        return $errors;
    }
}
