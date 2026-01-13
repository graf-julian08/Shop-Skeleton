<?php
/**
 * NavigationItem Model
 * Manages individual navigation menu items with hierarchy support
 */

class NavigationItem
{

    // Link types
    const TYPE_CATEGORY = 'category';
    const TYPE_PRODUCT = 'product';
    const TYPE_PAGE = 'page';
    const TYPE_URL = 'url';
    const TYPE_CUSTOM = 'custom';

    /**
     * Find item by ID
     */
    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT * FROM navigation_items WHERE id = ?",
            [$id]
        );
    }

    /**
     * Get all items for a menu (flat list)
     */
    public static function allForMenu(int $menuId): array
    {
        return Database::fetchAll(
            "SELECT * FROM navigation_items WHERE menu_id = ? ORDER BY sort_order ASC",
            [$menuId]
        );
    }

    /**
     * Get items as hierarchical tree
     */
    public static function treeForMenu(int $menuId): array
    {
        $items = self::allForMenu($menuId);
        return self::buildTree($items);
    }

    /**
     * Build tree structure from flat array
     */
    public static function buildTree(array $items, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = self::buildTree($items, $item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }

        return $tree;
    }

    /**
     * Count items for a menu
     */
    public static function countForMenu(int $menuId): int
    {
        $result = Database::fetch(
            "SELECT COUNT(*) as count FROM navigation_items WHERE menu_id = ?",
            [$menuId]
        );
        return $result['count'] ?? 0;
    }

    /**
     * Get next sort order for a menu
     */
    public static function getNextSortOrder(int $menuId, ?int $parentId = null): int
    {
        $sql = "SELECT MAX(sort_order) as max_order FROM navigation_items WHERE menu_id = ?";
        $params = [$menuId];

        if ($parentId !== null) {
            $sql .= " AND parent_id = ?";
            $params[] = $parentId;
        } else {
            $sql .= " AND parent_id IS NULL";
        }

        $result = Database::fetch($sql, $params);
        return ($result['max_order'] ?? -1) + 1;
    }

    /**
     * Create a new item
     */
    public static function create(array $data): int
    {
        $sortOrder = $data['sort_order'] ?? self::getNextSortOrder(
            $data['menu_id'],
            $data['parent_id'] ?? null
        );

        // Core required fields
        $insertData = [
            'menu_id' => $data['menu_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'label' => $data['label'],
            'type' => $data['type'] ?? 'url',
            'reference_id' => $data['reference_id'] ?? null,
            'url' => $data['url'] ?? null,
            'target' => $data['target'] ?? '_self',
            'is_active' => $data['is_active'] ?? 1,
            'sort_order' => $sortOrder,
        ];

        // Try to add optional columns - catch error if columns don't exist
        try {
            return Database::insert('navigation_items', $insertData);
        } catch (Exception $e) {
            // If insert fails, log and re-throw
            error_log("NavigationItem::create error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an item
     */
    public static function update(int $id, array $data): bool
    {
        $updateData = [];

        // Core fields + styling fields
        $fields = [
            'parent_id',
            'label',
            'type',
            'reference_id',
            'url',
            'target',
            'is_active',
            'sort_order',
            // Styling fields
            'icon',
            'icon_position',
            'custom_icon_url',
            'custom_color',
            'bg_color',
            'font_weight',
            'text_decoration',
            'badge_text',
            'badge_color',
            'css_class',
            // Mega menu fields
            'mega_enabled',
            'mega_columns',
            'mega_width',
            'mega_image',
            'mega_promo_title',
            'mega_promo_text',
            'mega_promo_link',
            'click_behavior',
            'mega_animation'
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            return true;
        }

        return Database::update('navigation_items', $updateData, 'id = ?', [$id]);
    }

    /**
     * Delete an item (and move children to parent)
     */
    public static function delete(int $id): bool
    {
        // Get item to find its parent
        $item = self::find($id);
        if (!$item) {
            return false;
        }

        // Move children to item's parent
        Database::query(
            "UPDATE navigation_items SET parent_id = ? WHERE parent_id = ?",
            [$item['parent_id'], $id]
        );

        // Delete the item
        return Database::delete('navigation_items', 'id = ?', [$id]);
    }

    /**
     * Bulk update sort order (for drag and drop)
     */
    public static function updateOrder(array $order): bool
    {
        foreach ($order as $index => $itemData) {
            $updateData = ['sort_order' => $index];

            // Handle parent_id if provided
            if (isset($itemData['parent_id'])) {
                $updateData['parent_id'] = $itemData['parent_id'] ?: null;
            }

            $id = is_array($itemData) ? $itemData['id'] : $itemData;
            self::update(intval($id), $updateData);
        }
        return true;
    }

    /**
     * Get URL for an item based on its type
     */
    public static function getResolvedUrl(array $item): string
    {
        switch ($item['type']) {
            case self::TYPE_PAGE:
                if ($item['reference_id']) {
                    $page = CmsPage::find($item['reference_id']);
                    return $page ? '/' . $page['slug'] : '#';
                }
                return $item['url'] ?? '#';

            case self::TYPE_CATEGORY:
                // TODO: Implement when Category model exists
                return $item['url'] ?? '/kategorie/' . $item['reference_id'];

            case self::TYPE_PRODUCT:
                // TODO: Implement when Product model exists
                return $item['url'] ?? '/produkt/' . $item['reference_id'];

            case self::TYPE_URL:
            case self::TYPE_CUSTOM:
            default:
                return $item['url'] ?? '#';
        }
    }

    /**
     * Validate item data - Strict: no dead links allowed
     */
    public static function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['label'] ?? ''))) {
            $errors['label'] = 'Label ist erforderlich.';
        }

        $type = $data['type'] ?? '';

        if (!in_array($type, ['category', 'product', 'page', 'url', 'custom'])) {
            $errors['type'] = 'Ungültiger Link-Typ.';
        }

        // URL required for url/custom types
        if (in_array($type, ['url', 'custom'])) {
            if (empty(trim($data['url'] ?? ''))) {
                $errors['url'] = 'URL ist erforderlich.';
            }
        }

        // Reference ID required for page/category/product types - no dead links!
        if (in_array($type, ['page', 'category', 'product'])) {
            if (empty($data['reference_id'])) {
                $typeNames = ['page' => 'CMS-Seite', 'category' => 'Kategorie', 'product' => 'Produkt'];
                $errors['reference_id'] = ($typeNames[$type] ?? 'Ziel') . ' muss ausgewählt werden.';
            }
        }

        return $errors;
    }
}
