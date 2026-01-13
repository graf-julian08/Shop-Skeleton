<?php
/**
 * NavigationMenu Model
 * Manages navigation menu definitions (main, footer, mobile)
 */

class NavigationMenu
{

    /**
     * Find menu by ID
     */
    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT * FROM navigation_menus WHERE id = ?",
            [$id]
        );
    }

    /**
     * Find menu by code for specific shop
     */
    public static function findByCode(string $code, int $shopId): ?array
    {
        return Database::fetch(
            "SELECT * FROM navigation_menus WHERE code = ? AND shop_id = ?",
            [$code, $shopId]
        );
    }

    /**
     * Get all menus for a shop (excluding mobile - deprecated)
     */
    public static function allForShop(int $shopId): array
    {
        return Database::fetchAll(
            "SELECT * FROM navigation_menus WHERE shop_id = ? AND code != 'mobile' ORDER BY FIELD(code, 'main', 'footer')",
            [$shopId]
        );
    }

    /**
     * Create a new menu
     */
    public static function create(array $data): int
    {
        return Database::insert('navigation_menus', [
            'shop_id' => $data['shop_id'],
            'name' => $data['name'],
            'code' => $data['code'],
            'is_active' => $data['is_active'] ?? 1,
        ]);
    }

    /**
     * Update a menu
     */
    public static function update(int $id, array $data): bool
    {
        $updateData = [];

        if (isset($data['name']))
            $updateData['name'] = $data['name'];
        if (isset($data['code']))
            $updateData['code'] = $data['code'];
        if (isset($data['is_active']))
            $updateData['is_active'] = $data['is_active'];

        if (empty($updateData)) {
            return true;
        }

        return Database::update('navigation_menus', $updateData, 'id = ?', [$id]);
    }

    /**
     * Delete a menu
     */
    public static function delete(int $id): bool
    {
        return Database::delete('navigation_menus', 'id = ?', [$id]);
    }

    /**
     * Ensure default menus exist for shop
     */
    public static function ensureDefaults(int $shopId): void
    {
        $defaults = [
            ['code' => 'main', 'name' => 'Hauptmenü'],
            ['code' => 'footer', 'name' => 'Footer-Menü'],
        ];

        foreach ($defaults as $menu) {
            $existing = self::findByCode($menu['code'], $shopId);
            if (!$existing) {
                self::create([
                    'shop_id' => $shopId,
                    'code' => $menu['code'],
                    'name' => $menu['name'],
                    'is_active' => 1,
                ]);
            }
        }
    }
}
