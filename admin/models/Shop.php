<?php
/**
 * Shop Model
 * Handles database operations for the shops table
 */

class Shop
{
    /**
     * Get shop by ID
     */
    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM shops WHERE id = ?", [$id]);
    }

    /**
     * Get shop by code
     */
    public static function findByCode(string $code): ?array
    {
        return Database::fetch("SELECT * FROM shops WHERE code = ?", [$code]);
    }

    /**
     * Get default shop (code = 'default')
     */
    public static function getDefault(): ?array
    {
        return self::findByCode('default');
    }

    /**
     * Update shop by ID
     */
    public static function update(int $id, array $data): bool
    {
        // Filter to allowed fields only
        $allowed = [
            'name',
            'domain',
            'description',
            'email',
            'phone',
            'default_locale',
            'default_currency',
            'timezone',
            'date_format',
            'time_format',
            'weight_unit',
            'dimension_unit',
            'is_active',
            'maintenance_mode',
            'maintenance_message'
        ];

        $filtered = array_intersect_key($data, array_flip($allowed));

        if (empty($filtered)) {
            return false;
        }

        return Database::update('shops', $filtered, 'id = ?', [$id]) >= 0;
    }

    /**
     * Create new shop
     */
    public static function create(array $data): int
    {
        $defaults = [
            'code' => 'shop_' . time(),
            'name' => 'New Shop',
            'default_locale' => 'de_DE',
            'default_currency' => 'EUR',
            'timezone' => 'Europe/Berlin',
            'date_format' => 'DD.MM.YYYY',
            'time_format' => '24h',
            'weight_unit' => 'kg',
            'dimension_unit' => 'cm',
            'is_active' => 1,
            'maintenance_mode' => 0,
        ];

        $merged = array_merge($defaults, $data);

        return Database::insert('shops', $merged);
    }

    /**
     * Delete shop by ID
     */
    public static function delete(int $id): bool
    {
        return Database::delete('shops', 'id = ?', [$id]) > 0;
    }

    /**
     * Get all shops
     */
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM shops ORDER BY name");
    }

    /**
     * Get available timezones (subset for EU)
     */
    public static function getTimezones(): array
    {
        return [
            'Europe/Berlin' => 'Europe/Berlin (DE)',
            'Europe/Vienna' => 'Europe/Vienna (AT)',
            'Europe/Zurich' => 'Europe/Zurich (CH)',
            'Europe/Paris' => 'Europe/Paris (FR)',
            'Europe/London' => 'Europe/London (UK)',
            'Europe/Amsterdam' => 'Europe/Amsterdam (NL)',
            'America/New_York' => 'America/New_York (US East)',
            'America/Los_Angeles' => 'America/Los_Angeles (US West)',
        ];
    }

    /**
     * Get available date formats
     */
    public static function getDateFormats(): array
    {
        return [
            'DD.MM.YYYY' => 'DD.MM.YYYY (31.12.2024)',
            'MM/DD/YYYY' => 'MM/DD/YYYY (12/31/2024)',
            'YYYY-MM-DD' => 'YYYY-MM-DD (2024-12-31)',
        ];
    }

    /**
     * Get available weight units
     */
    public static function getWeightUnits(): array
    {
        return [
            'kg' => 'Kilogramm (kg)',
            'g' => 'Gramm (g)',
            'lb' => 'Pfund (lb)',
            'oz' => 'Unze (oz)',
        ];
    }
}
