<?php
/**
 * MegaMenu Model
 * Handles mega menu columns and blocks for navigation items
 */

require_once __DIR__ . '/../includes/Database.php';

class MegaMenu
{
    /**
     * Get all columns and blocks for a navigation item
     */
    public static function getByNavigationItem(int $navItemId): array
    {
        $columns = Database::fetchAll(
            "SELECT * FROM mega_menu_columns WHERE navigation_item_id = ? ORDER BY column_order",
            [$navItemId]
        );

        foreach ($columns as &$column) {
            $column['blocks'] = Database::fetchAll(
                "SELECT * FROM mega_menu_blocks WHERE column_id = ? ORDER BY block_order",
                [$column['id']]
            );

            // Decode JSON config for each block
            foreach ($column['blocks'] as &$block) {
                $block['config'] = json_decode($block['config_json'] ?? '{}', true);
            }
        }

        return $columns;
    }

    /**
     * Get a single column by ID
     */
    public static function getColumn(int $columnId): ?array
    {
        return Database::fetch(
            "SELECT * FROM mega_menu_columns WHERE id = ?",
            [$columnId]
        );
    }

    /**
     * Get a single block by ID
     */
    public static function getBlock(int $blockId): ?array
    {
        $block = Database::fetch(
            "SELECT * FROM mega_menu_blocks WHERE id = ?",
            [$blockId]
        );

        if ($block) {
            $block['config'] = json_decode($block['config_json'] ?? '{}', true);
        }

        return $block;
    }

    // ========== COLUMN OPERATIONS ==========

    /**
     * Create a new column
     */
    public static function createColumn(int $navItemId, int $widthPercent = 25): int
    {
        // Get max order
        $maxOrder = Database::fetch(
            "SELECT MAX(column_order) as max_order FROM mega_menu_columns WHERE navigation_item_id = ?",
            [$navItemId]
        );
        $order = ($maxOrder['max_order'] ?? -1) + 1;

        return Database::insert('mega_menu_columns', [
            'navigation_item_id' => $navItemId,
            'column_order' => $order,
            'width_percent' => $widthPercent
        ]);
    }

    /**
     * Update column width
     */
    public static function updateColumnWidth(int $columnId, int $widthPercent): bool
    {
        return Database::update(
            'mega_menu_columns',
            ['width_percent' => $widthPercent],
            'id = ?',
            [$columnId]
        ) !== false;
    }

    /**
     * Update column order
     */
    public static function updateColumnOrder(int $columnId, int $order): bool
    {
        return Database::update(
            'mega_menu_columns',
            ['column_order' => $order],
            'id = ?',
            [$columnId]
        ) !== false;
    }

    /**
     * Delete a column (and all its blocks)
     */
    public static function deleteColumn(int $columnId): bool
    {
        return Database::delete('mega_menu_columns', 'id = ?', [$columnId]) > 0;
    }

    /**
     * Reorder columns
     */
    public static function reorderColumns(int $navItemId, array $columnIds): bool
    {
        foreach ($columnIds as $order => $columnId) {
            Database::update(
                'mega_menu_columns',
                ['column_order' => $order],
                'id = ? AND navigation_item_id = ?',
                [$columnId, $navItemId]
            );
        }
        return true;
    }

    // ========== BLOCK OPERATIONS ==========

    /**
     * Create a new block
     */
    public static function createBlock(int $columnId, string $blockType, array $config = []): int
    {
        // Get max order
        $maxOrder = Database::fetch(
            "SELECT MAX(block_order) as max_order FROM mega_menu_blocks WHERE column_id = ?",
            [$columnId]
        );
        $order = ($maxOrder['max_order'] ?? -1) + 1;

        return Database::insert('mega_menu_blocks', [
            'column_id' => $columnId,
            'block_type' => $blockType,
            'block_order' => $order,
            'config_json' => json_encode($config)
        ]);
    }

    /**
     * Update block config
     */
    public static function updateBlock(int $blockId, array $config): bool
    {
        return Database::update(
            'mega_menu_blocks',
            ['config_json' => json_encode($config)],
            'id = ?',
            [$blockId]
        ) !== false;
    }

    /**
     * Update block order
     */
    public static function updateBlockOrder(int $blockId, int $order): bool
    {
        return Database::update(
            'mega_menu_blocks',
            ['block_order' => $order],
            'id = ?',
            [$blockId]
        ) !== false;
    }

    /**
     * Move block to different column
     */
    public static function moveBlock(int $blockId, int $newColumnId, int $order): bool
    {
        return Database::update(
            'mega_menu_blocks',
            ['column_id' => $newColumnId, 'block_order' => $order],
            'id = ?',
            [$blockId]
        ) !== false;
    }

    /**
     * Delete a block
     */
    public static function deleteBlock(int $blockId): bool
    {
        return Database::delete('mega_menu_blocks', 'id = ?', [$blockId]) > 0;
    }

    /**
     * Reorder blocks within a column
     */
    public static function reorderBlocks(int $columnId, array $blockIds): bool
    {
        foreach ($blockIds as $order => $blockId) {
            Database::update(
                'mega_menu_blocks',
                ['block_order' => $order],
                'id = ? AND column_id = ?',
                [$blockId, $columnId]
            );
        }
        return true;
    }

    // ========== PRESET LAYOUTS ==========

    /**
     * Apply a preset layout (clears existing and creates new columns)
     */
    public static function applyLayout(int $navItemId, string $layout): bool
    {
        // Delete existing columns
        Database::delete('mega_menu_columns', 'navigation_item_id = ?', [$navItemId]);

        // Create new columns based on layout
        $layouts = [
            '2-col' => [50, 50],
            '3-col' => [33, 33, 34],
            '4-col' => [25, 25, 25, 25],
            '2-1' => [33, 67],
            '1-2' => [67, 33],
            '1-1-2' => [25, 25, 50],
            '2-1-1' => [50, 25, 25],
        ];

        $widths = $layouts[$layout] ?? [100];

        foreach ($widths as $index => $width) {
            Database::insert('mega_menu_columns', [
                'navigation_item_id' => $navItemId,
                'column_order' => $index,
                'width_percent' => $width
            ]);
        }

        return true;
    }

    /**
     * Export mega menu as JSON (for preview or API)
     */
    public static function exportAsJson(int $navItemId): array
    {
        $columns = self::getByNavigationItem($navItemId);

        return [
            'navigation_item_id' => $navItemId,
            'columns' => array_map(function ($col) {
                return [
                    'id' => $col['id'],
                    'width' => $col['width_percent'],
                    'blocks' => array_map(function ($block) {
                        return [
                            'id' => $block['id'],
                            'type' => $block['block_type'],
                            'config' => $block['config']
                        ];
                    }, $col['blocks'])
                ];
            }, $columns)
        ];
    }
}
