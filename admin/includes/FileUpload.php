<?php
/**
 * FileUpload Helper
 * Handles file upload validation and storage
 */

class FileUpload {
    
    // Upload base directory (relative to admin root)
    private static string $uploadDir = 'uploads';
    
    // Allowed mime types per category
    private static array $allowedTypes = [
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
        'favicon' => ['image/x-icon', 'image/png', 'image/vnd.microsoft.icon'],
        'document' => ['application/pdf'],
    ];
    
    // Max file sizes in bytes
    private static array $maxSizes = [
        'image' => 5 * 1024 * 1024,  // 5MB
        'favicon' => 1 * 1024 * 1024, // 1MB
        'document' => 10 * 1024 * 1024, // 10MB
    ];
    
    /**
     * Handle file upload
     * 
     * @param string $inputName - Name of the file input field
     * @param string $category - Category (image, favicon, document)
     * @param string $subFolder - Subfolder within uploads (e.g., 'logos', 'products')
     * @return array - ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    public static function upload(string $inputName, string $category = 'image', string $subFolder = ''): array {
        $result = ['success' => false, 'path' => null, 'error' => null];
        
        // Check if file was uploaded
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
            // No file uploaded - not an error, just skip
            return $result;
        }
        
        $file = $_FILES[$inputName];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['error'] = self::getUploadError($file['error']);
            return $result;
        }
        
        // Validate file type
        $mimeType = mime_content_type($file['tmp_name']);
        $allowedMimes = self::$allowedTypes[$category] ?? self::$allowedTypes['image'];
        
        if (!in_array($mimeType, $allowedMimes)) {
            $result['error'] = 'Ungültiger Dateityp. Erlaubt: ' . implode(', ', $allowedMimes);
            return $result;
        }
        
        // Validate file size
        $maxSize = self::$maxSizes[$category] ?? self::$maxSizes['image'];
        if ($file['size'] > $maxSize) {
            $result['error'] = 'Datei zu groß. Maximum: ' . self::formatBytes($maxSize);
            return $result;
        }
        
        // Generate unique filename
        $extension = self::getExtensionFromMime($mimeType);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        
        // Build destination path
        $destDir = __DIR__ . '/../' . self::$uploadDir;
        if ($subFolder) {
            $destDir .= '/' . trim($subFolder, '/');
        }
        
        // Create directory if not exists
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        
        $destPath = $destDir . '/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            // Return relative path from admin root
            $relativePath = self::$uploadDir . ($subFolder ? '/' . trim($subFolder, '/') : '') . '/' . $filename;
            $result['success'] = true;
            $result['path'] = $relativePath;
        } else {
            $result['error'] = 'Fehler beim Speichern der Datei.';
        }
        
        return $result;
    }
    
    /**
     * Delete a file by path
     */
    public static function delete(string $path): bool {
        $fullPath = __DIR__ . '/../' . ltrim($path, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
    
    /**
     * Get upload error message
     */
    private static function getUploadError(int $errorCode): string {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Datei überschreitet php.ini Upload-Limit.',
            UPLOAD_ERR_FORM_SIZE => 'Datei überschreitet Form Upload-Limit.',
            UPLOAD_ERR_PARTIAL => 'Datei wurde nur teilweise hochgeladen.',
            UPLOAD_ERR_NO_FILE => 'Keine Datei hochgeladen.',
            UPLOAD_ERR_NO_TMP_DIR => 'Temporäres Verzeichnis fehlt.',
            UPLOAD_ERR_CANT_WRITE => 'Fehler beim Schreiben auf Festplatte.',
            UPLOAD_ERR_EXTENSION => 'Upload durch PHP-Extension gestoppt.',
        ];
        
        return $errors[$errorCode] ?? 'Unbekannter Upload-Fehler.';
    }
    
    /**
     * Get file extension from mime type
     */
    private static function getExtensionFromMime(string $mimeType): string {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/x-icon' => 'ico',
            'image/vnd.microsoft.icon' => 'ico',
            'application/pdf' => 'pdf',
        ];
        
        return $map[$mimeType] ?? 'bin';
    }
    
    /**
     * Format bytes to human readable
     */
    private static function formatBytes(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Get web-accessible URL for uploaded file
     */
    public static function getUrl(string $path): string {
        if (empty($path)) {
            return '';
        }
        return $path; // Already relative from admin root
    }
}
