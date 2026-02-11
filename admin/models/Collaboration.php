<?php
/**
 * Collaboration Model
 * CRUD operations for the collaborations table
 */
class Collaboration
{
    /**
     * Get all collaborations for a shop with optional filters
     */
    public static function allForShop(int $shopId, array $filters = []): array
    {
        $where = ['c.shop_id = ?'];
        $params = [$shopId];

        if (!empty($filters['status'])) {
            $where[] = 'c.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(c.name LIKE ? OR c.short_description LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $where);
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = strtoupper($filters['sort_dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

        $validSorts = ['name', 'created_at', 'updated_at', 'status', 'sort_order'];
        if (!in_array($sortBy, $validSorts)) {
            $sortBy = 'created_at';
        }

        return Database::fetchAll(
            "SELECT c.*,
                    (SELECT ci.image_url FROM collaboration_images ci 
                     WHERE ci.collaboration_id = c.id ORDER BY ci.sort_order ASC LIMIT 1) as thumbnail
             FROM collaborations c
             WHERE {$whereClause}
             ORDER BY c.{$sortBy} {$sortDir}",
            $params
        );
    }

    /**
     * Count collaborations for a shop
     */
    public static function countForShop(int $shopId): int
    {
        $result = Database::fetch(
            "SELECT COUNT(*) as total FROM collaborations WHERE shop_id = ?",
            [$shopId]
        );
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Get stats (counts by status)
     */
    public static function getStats(int $shopId): array
    {
        $stats = ['all' => 0, 'active' => 0, 'draft' => 0, 'archived' => 0];
        $rows = Database::fetchAll(
            "SELECT status, COUNT(*) as count FROM collaborations WHERE shop_id = ? GROUP BY status",
            [$shopId]
        );
        $total = 0;
        foreach ($rows as $row) {
            $stats[$row['status']] = (int) $row['count'];
            $total += (int) $row['count'];
        }
        $stats['all'] = $total;
        return $stats;
    }

    /**
     * Get a single collaboration by ID
     */
    public static function find(int $id, int $shopId): ?array
    {
        $collab = Database::fetch(
            "SELECT * FROM collaborations WHERE id = ? AND shop_id = ?",
            [$id, $shopId]
        );
        if (!$collab)
            return null;

        // Attach images
        $collab['images'] = Database::fetchAll(
            "SELECT * FROM collaboration_images WHERE collaboration_id = ? ORDER BY sort_order ASC",
            [$id]
        );

        return $collab;
    }

    /**
     * Create a new collaboration
     */
    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return Database::insert('collaborations', $data);
    }

    /**
     * Update a collaboration
     */
    public static function update(int $id, int $shopId, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        Database::update('collaborations', $data, 'id = ? AND shop_id = ?', [$id, $shopId]);
    }

    /**
     * Delete a collaboration and its images
     */
    public static function delete(int $id, int $shopId): bool
    {
        // Images cascade-delete via FK, but also delete physical files
        $images = Database::fetchAll(
            "SELECT image_url FROM collaboration_images WHERE collaboration_id = ?",
            [$id]
        );
        foreach ($images as $img) {
            $filePath = __DIR__ . '/../' . $img['image_url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $deleted = Database::delete('collaborations', 'id = ? AND shop_id = ?', [$id, $shopId]);
        return $deleted > 0;
    }

    /**
     * Generate a unique slug
     */
    public static function generateSlug(int $shopId, string $name, int $excludeId = 0): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        if (empty($slug))
            $slug = 'collaboration';

        $original = $slug;
        $counter = 1;

        while (true) {
            $existing = Database::fetch(
                "SELECT id FROM collaborations WHERE shop_id = ? AND slug = ? AND id != ?",
                [$shopId, $slug, $excludeId]
            );
            if (!$existing)
                break;
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }
}
