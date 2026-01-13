<?php
/**
 * ShopDesign Model
 * Handles database operations for the shop_design table
 */

class ShopDesign {
    /**
     * Get design settings by shop ID
     */
    public static function findByShopId(int $shopId): ?array {
        return Database::fetch("SELECT * FROM shop_design WHERE shop_id = ?", [$shopId]);
    }
    
    /**
     * Get design for default shop
     */
    public static function getDefault(): ?array {
        $shop = Shop::getDefault();
        if (!$shop) {
            return null;
        }
        return self::findByShopId($shop['id']);
    }
    
    /**
     * Update design settings
     */
    public static function update(int $id, array $data): bool {
        // Filter to allowed fields only
        $allowed = [
            'color_primary', 'color_secondary', 'color_accent', 'color_text',
            'color_background', 'color_surface',
            'logo_path', 'logo_dark_path', 'favicon_path',
            'font_heading', 'font_body',
            'header_style', 'footer_style',
            'custom_css'
        ];
        
        $filtered = array_intersect_key($data, array_flip($allowed));
        
        if (empty($filtered)) {
            return false;
        }
        
        return Database::update('shop_design', $filtered, 'id = ?', [$id]) >= 0;
    }
    
    /**
     * Create design settings for a shop
     */
    public static function create(int $shopId, array $data = []): int {
        $defaults = [
            'shop_id' => $shopId,
            'color_primary' => '#7c3aed',
            'color_secondary' => '#1a1a1a',
            'color_accent' => '#22c55e',
            'color_text' => '#ffffff',
            'color_background' => '#ffffff',
            'color_surface' => '#f5f5f5',
            'font_heading' => 'Inter',
            'font_body' => 'Inter',
            'header_style' => 'default',
            'footer_style' => 'columns',
        ];
        
        $merged = array_merge($defaults, $data);
        $merged['shop_id'] = $shopId; // Ensure shop_id is not overwritten
        
        return Database::insert('shop_design', $merged);
    }
    
    /**
     * Get available fonts
     */
    public static function getFonts(): array {
        return [
            'Inter' => 'Inter',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Playfair Display' => 'Playfair Display',
            'Montserrat' => 'Montserrat',
            'Poppins' => 'Poppins',
            'Source Sans Pro' => 'Source Sans Pro',
        ];
    }
    
    /**
     * Get available header styles
     */
    public static function getHeaderStyles(): array {
        return [
            'default' => 'Standard',
            'transparent' => 'Transparent',
            'sticky' => 'Sticky',
        ];
    }
    
    /**
     * Get available footer styles
     */
    public static function getFooterStyles(): array {
        return [
            'simple' => 'Einfach',
            'columns' => 'Spalten',
            'minimal' => 'Minimal',
        ];
    }
    
    /**
     * Validate hex color format
     */
    public static function isValidHexColor(string $color): bool {
        return preg_match('/^#[a-fA-F0-9]{6}$/', $color) === 1;
    }
}
