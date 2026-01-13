<?php
/**
 * Category Model
 * Handles product categories from the categories table
 */
class Category
{

    /**
     * Get all categories for a shop
     */
    public static function allForShop(int $shopId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT id, parent_id, name, slug, is_active, level, sort_order
            FROM categories 
            WHERE shop_id = ? 
            ORDER BY sort_order ASC, name ASC
        ");
        $stmt->execute([$shopId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active categories for a shop (for dropdowns)
     */
    public static function activeForShop(int $shopId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT id, parent_id, name, slug, level
            FROM categories 
            WHERE shop_id = ? AND is_active = 1
            ORDER BY level ASC, sort_order ASC, name ASC
        ");
        $stmt->execute([$shopId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a category by ID
     */
    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get URL for a category
     */
    public static function getUrl(array $category): string
    {
        return '/kategorie/' . ($category['slug'] ?? $category['id']);
    }

    /**
     * Build a flat list with indentation for display
     */
    public static function buildFlatList(array $categories): array
    {
        // Build tree first
        $tree = [];
        $map = [];

        foreach ($categories as $cat) {
            $cat['children'] = [];
            $map[$cat['id']] = $cat;
        }

        foreach ($map as $id => $cat) {
            if (!empty($cat['parent_id']) && isset($map[$cat['parent_id']])) {
                $map[$cat['parent_id']]['children'][] = &$map[$id];
            } else {
                $tree[] = &$map[$id];
            }
        }

        // Flatten with indentation
        $flat = [];
        self::flattenTree($tree, $flat, 0);
        return $flat;
    }

    private static function flattenTree(array $nodes, array &$flat, int $level): void
    {
        foreach ($nodes as $node) {
            $node['_level'] = $level;
            $node['_indent'] = str_repeat('— ', $level);
            $children = $node['children'] ?? [];
            unset($node['children']);
            $flat[] = $node;

            if (!empty($children)) {
                self::flattenTree($children, $flat, $level + 1);
            }
        }
    }
}
