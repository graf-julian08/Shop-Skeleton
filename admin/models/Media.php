<?php
/**
 * Media Library Model
 * Handles all media upload, storage, and retrieval operations
 */

require_once __DIR__ . '/../includes/Database.php';

class Media
{
    // Allowed mime types
    private static $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg'
    ];

    // Max file size (10MB)
    private static $maxFileSize = 10 * 1024 * 1024;

    // Thumbnail sizes
    private static $thumbnailSize = 150;
    private static $mediumSize = 600;

    /**
     * Upload a file to the media library
     */
    public static function upload(array $file, int $shopId = 1, string $folder = 'general'): array
    {
        // Validate file
        $validation = self::validateFile($file);
        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['error']];
        }

        // Generate unique filename
        $extension = self::$allowedTypes[$file['type']];
        $storedFilename = self::generateUUID() . '.' . $extension;

        // Get upload paths
        $basePath = self::getBasePath($shopId);
        $originalPath = $basePath . '/original/' . $storedFilename;

        // Ensure directories exist
        self::ensureDirectories($shopId);

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $originalPath)) {
            return ['success' => false, 'error' => 'Fehler beim Speichern der Datei'];
        }

        // Get image dimensions
        $dimensions = self::getImageDimensions($originalPath, $file['type']);

        // Generate thumbnails (skip for SVG)
        if ($file['type'] !== 'image/svg+xml') {
            self::generateThumbnail($originalPath, $basePath . '/thumbnails/' . $storedFilename, self::$thumbnailSize);
            self::generateThumbnail($originalPath, $basePath . '/medium/' . $storedFilename, self::$mediumSize);
        }

        // Insert into database
        try {
            $mediaId = Database::insert('media_library', [
                'shop_id' => $shopId,
                'filename' => $file['name'],
                'stored_filename' => $storedFilename,
                'mime_type' => $file['type'],
                'file_size' => $file['size'],
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
                'folder' => $folder
            ]);

            return [
                'success' => true,
                'media' => self::getById($mediaId)
            ];
        } catch (Exception $e) {
            // Cleanup file on DB error
            @unlink($originalPath);
            return ['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()];
        }
    }

    /**
     * Get media by ID
     */
    public static function getById(int $id): ?array
    {
        $media = Database::fetch("SELECT * FROM media_library WHERE id = ?", [$id]);
        if ($media) {
            $media = self::addUrls($media);
        }
        return $media;
    }

    /**
     * Get all media for a shop, optionally filtered by folder
     */
    public static function getAll(int $shopId = 1, ?string $folder = null, int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT * FROM media_library WHERE shop_id = ?";
        $params = [$shopId];

        if ($folder && $folder !== 'all') {
            $sql .= " AND folder = ?";
            $params[] = $folder;
        }

        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $media = Database::fetchAll($sql, $params);
        return array_map([self::class, 'addUrls'], $media);
    }

    /**
     * Get folder counts
     */
    public static function getFolderCounts(int $shopId = 1): array
    {
        return Database::fetchAll(
            "SELECT folder, COUNT(*) as count FROM media_library WHERE shop_id = ? GROUP BY folder ORDER BY folder",
            [$shopId]
        );
    }

    /**
     * Update media metadata
     */
    public static function update(int $id, array $data): bool
    {
        $allowed = ['alt_text', 'title', 'folder'];
        $updateData = array_intersect_key($data, array_flip($allowed));

        if (empty($updateData)) {
            return true;
        }

        return Database::update('media_library', $updateData, 'id = ?', [$id]);
    }

    /**
     * Delete media
     */
    public static function delete(int $id): array
    {
        $media = self::getById($id);
        if (!$media) {
            return ['success' => false, 'error' => 'Media nicht gefunden'];
        }

        // Check if media is in use
        $inUse = self::checkUsage($id);
        if ($inUse) {
            return ['success' => false, 'error' => 'Media ist noch in Verwendung', 'usage' => $inUse];
        }

        // Delete files
        $basePath = self::getBasePath($media['shop_id']);
        @unlink($basePath . '/original/' . $media['stored_filename']);
        @unlink($basePath . '/thumbnails/' . $media['stored_filename']);
        @unlink($basePath . '/medium/' . $media['stored_filename']);

        // Delete from database
        Database::delete('media_library', 'id = ?', [$id]);

        return ['success' => true];
    }

    /**
     * Check if media is used anywhere
     */
    private static function checkUsage(int $mediaId): ?array
    {
        // Check navigation items
        $navUsage = Database::fetch(
            "SELECT id, label FROM navigation_items WHERE mega_image = ? OR custom_icon_url = ?",
            [$mediaId, $mediaId]
        );
        if ($navUsage) {
            return ['type' => 'navigation', 'item' => $navUsage];
        }

        // Add more usage checks as needed (products, categories, CMS, etc.)

        return null;
    }

    /**
     * Validate uploaded file
     */
    private static function validateFile(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'Datei zu groß (Server-Limit)',
                UPLOAD_ERR_FORM_SIZE => 'Datei zu groß (Form-Limit)',
                UPLOAD_ERR_PARTIAL => 'Datei nur teilweise hochgeladen',
                UPLOAD_ERR_NO_FILE => 'Keine Datei hochgeladen',
            ];
            return ['valid' => false, 'error' => $errors[$file['error']] ?? 'Upload-Fehler'];
        }

        if ($file['size'] > self::$maxFileSize) {
            return ['valid' => false, 'error' => 'Datei zu groß (max. 10MB)'];
        }

        if (!isset(self::$allowedTypes[$file['type']])) {
            return ['valid' => false, 'error' => 'Dateityp nicht erlaubt (nur JPG, PNG, GIF, WebP, SVG)'];
        }

        // Verify it's actually an image
        if ($file['type'] !== 'image/svg+xml') {
            $imageInfo = @getimagesize($file['tmp_name']);
            if (!$imageInfo) {
                return ['valid' => false, 'error' => 'Ungültige Bilddatei'];
            }
        }

        return ['valid' => true];
    }

    /**
     * Generate thumbnail
     */
    private static function generateThumbnail(string $sourcePath, string $destPath, int $maxSize): bool
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        list($width, $height, $type) = $imageInfo;

        // Calculate new dimensions
        if ($width > $height) {
            $newWidth = min($width, $maxSize);
            $newHeight = intval($height * ($newWidth / $width));
        } else {
            $newHeight = min($height, $maxSize);
            $newWidth = intval($width * ($newHeight / $height));
        }

        // Create source image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if (!$source) {
            return false;
        }

        // Create destination image
        $dest = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
            imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save
        $result = false;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($dest, $destPath, 85);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($dest, $destPath, 8);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($dest, $destPath);
                break;
            case IMAGETYPE_WEBP:
                $result = imagewebp($dest, $destPath, 85);
                break;
        }

        imagedestroy($source);
        imagedestroy($dest);

        return $result;
    }

    /**
     * Get image dimensions
     */
    private static function getImageDimensions(string $path, string $mimeType): array
    {
        if ($mimeType === 'image/svg+xml') {
            // SVG dimensions are not easily parseable, return null
            return ['width' => null, 'height' => null];
        }

        $info = getimagesize($path);
        return [
            'width' => $info[0] ?? null,
            'height' => $info[1] ?? null
        ];
    }

    /**
     * Add URL paths to media record
     */
    private static function addUrls(array $media): array
    {
        $basePath = '/uploads/media/' . $media['shop_id'];
        $filename = $media['stored_filename'];

        $media['url'] = $basePath . '/original/' . $filename;
        $media['thumbnail_url'] = $basePath . '/thumbnails/' . $filename;
        $media['medium_url'] = $basePath . '/medium/' . $filename;

        // SVG doesn't have thumbnails
        if ($media['mime_type'] === 'image/svg+xml') {
            $media['thumbnail_url'] = $media['url'];
            $media['medium_url'] = $media['url'];
        }

        return $media;
    }

    /**
     * Get base path for shop uploads
     */
    private static function getBasePath(int $shopId): string
    {
        return dirname(__DIR__, 2) . '/uploads/media/' . $shopId;
    }

    /**
     * Ensure upload directories exist
     */
    private static function ensureDirectories(int $shopId): void
    {
        $basePath = self::getBasePath($shopId);
        $dirs = ['original', 'thumbnails', 'medium'];

        foreach ($dirs as $dir) {
            $path = $basePath . '/' . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * Generate UUID v4
     */
    private static function generateUUID(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
