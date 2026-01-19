<?php
/**
 * Email API
 * Email settings management and sending
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Mailer.php';

Database::configure($database);
Auth::init();

// =====================================================================
// AUTO-MIGRATION: Create email tables
// =====================================================================
try {
    Database::query("
        CREATE TABLE IF NOT EXISTS email_settings (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            smtp_host VARCHAR(255) DEFAULT 'smtp.example.com',
            smtp_port INT DEFAULT 587,
            smtp_user VARCHAR(255),
            smtp_password VARCHAR(255),
            smtp_encryption ENUM('tls', 'ssl', 'none') DEFAULT 'tls',
            from_name VARCHAR(255) DEFAULT 'Mein Online Shop',
            from_email VARCHAR(255) DEFAULT 'noreply@example.com',
            reply_to VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_shop (shop_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    Database::query("
        CREATE TABLE IF NOT EXISTS email_templates (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            template_type VARCHAR(100) NOT NULL,
            name VARCHAR(255) NOT NULL,
            subject VARCHAR(500) NOT NULL,
            body_html LONGTEXT,
            body_text TEXT,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_template (shop_id, template_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    Database::query("
        CREATE TABLE IF NOT EXISTS email_log (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            recipient VARCHAR(255) NOT NULL,
            subject VARCHAR(500),
            status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
            error_message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_shop_created (shop_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

} catch (Exception $e) {
    // Tables might already exist
}

// =====================================================================
// Insert default email settings if none exist
// =====================================================================
try {
    $count = Database::fetch("SELECT COUNT(*) as cnt FROM email_settings WHERE shop_id = 1");
    if ((int) ($count['cnt'] ?? 0) === 0) {
        Database::insert('email_settings', [
            'shop_id' => 1,
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_user' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'from_name' => 'Mein Online Shop',
            'from_email' => 'noreply@example.com',
            'reply_to' => 'support@example.com'
        ]);
    }
} catch (Exception $e) {
    // Not critical
}

// =====================================================================
// ROUTE ACTIONS
// =====================================================================
$action = $_REQUEST['action'] ?? 'get_settings';
$shopId = 1;

switch ($action) {
    case 'get_settings':
        handleGetSettings($shopId);
        break;
    case 'save_settings':
        handleSaveSettings($shopId);
        break;
    case 'get_templates':
        handleGetTemplates($shopId);
        break;
    case 'save_template':
        handleSaveTemplate($shopId);
        break;
    case 'send_test':
        handleSendTest($shopId);
        break;
    case 'get_log':
        handleGetLog($shopId);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
}

// =====================================================================
// GET SETTINGS
// =====================================================================
function handleGetSettings(int $shopId): void
{
    $settings = Database::fetch("SELECT * FROM email_settings WHERE shop_id = ?", [$shopId]);

    if (!$settings) {
        $settings = [
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_user' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'from_name' => 'Mein Online Shop',
            'from_email' => 'noreply@example.com',
            'reply_to' => 'support@example.com'
        ];
    }

    // Mask password
    if (!empty($settings['smtp_password'])) {
        $settings['smtp_password'] = '••••••••';
        $settings['has_password'] = true;
    } else {
        $settings['has_password'] = false;
    }

    echo json_encode(['success' => true, 'settings' => $settings]);
}

// =====================================================================
// SAVE SETTINGS
// =====================================================================
function handleSaveSettings(int $shopId): void
{
    $smtpHost = trim($_POST['smtp_host'] ?? '');
    $smtpPort = (int) ($_POST['smtp_port'] ?? 587);
    $smtpUser = trim($_POST['smtp_user'] ?? '');
    $smtpPassword = $_POST['smtp_password'] ?? '';
    $smtpEncryption = $_POST['smtp_encryption'] ?? 'tls';
    $fromName = trim($_POST['from_name'] ?? '');
    $fromEmail = trim($_POST['from_email'] ?? '');
    $replyTo = trim($_POST['reply_to'] ?? '');

    $data = [
        'smtp_host' => $smtpHost,
        'smtp_port' => $smtpPort,
        'smtp_user' => $smtpUser,
        'smtp_encryption' => $smtpEncryption,
        'from_name' => $fromName,
        'from_email' => $fromEmail,
        'reply_to' => $replyTo
    ];

    // Only update password if not masked placeholder
    if ($smtpPassword !== '••••••••' && $smtpPassword !== '') {
        $data['smtp_password'] = $smtpPassword;
    }

    $existing = Database::fetch("SELECT id FROM email_settings WHERE shop_id = ?", [$shopId]);

    if ($existing) {
        Database::update('email_settings', $data, 'shop_id = ?', [$shopId]);
    } else {
        $data['shop_id'] = $shopId;
        Database::insert('email_settings', $data);
    }

    echo json_encode(['success' => true, 'message' => 'Einstellungen gespeichert']);
}

// =====================================================================
// GET TEMPLATES
// =====================================================================
function handleGetTemplates(int $shopId): void
{
    $templates = Database::fetchAll("SELECT * FROM email_templates WHERE shop_id = ? ORDER BY name", [$shopId]);

    // Add default templates if none exist
    $defaultTypes = [
        'order_confirmation' => ['Bestellbestätigung', 'Ihre Bestellung #{order_id}'],
        'shipment_notification' => ['Versandbenachrichtigung', 'Ihre Bestellung wurde versendet'],
        'delivery_confirmation' => ['Lieferbestätigung', 'Ihre Bestellung wurde zugestellt'],
        'password_reset' => ['Passwort zurücksetzen', 'Passwort zurücksetzen'],
        'welcome' => ['Willkommens-E-Mail', 'Willkommen bei {shop_name}']
    ];

    $existingTypes = array_column($templates, 'template_type');

    foreach ($defaultTypes as $type => $info) {
        if (!in_array($type, $existingTypes)) {
            $templates[] = [
                'id' => null,
                'template_type' => $type,
                'name' => $info[0],
                'subject' => $info[1],
                'body_html' => null,
                'is_active' => 1,
                'is_default' => true
            ];
        }
    }

    echo json_encode(['success' => true, 'templates' => $templates]);
}

// =====================================================================
// SAVE TEMPLATE
// =====================================================================
function handleSaveTemplate(int $shopId): void
{
    $templateType = trim($_POST['template_type'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $bodyHtml = $_POST['body_html'] ?? '';
    $bodyText = $_POST['body_text'] ?? '';
    $isActive = (int) ($_POST['is_active'] ?? 1);

    if (!$templateType || !$name || !$subject) {
        echo json_encode(['success' => false, 'error' => 'Name, Typ und Betreff sind erforderlich']);
        return;
    }

    $existing = Database::fetch("SELECT id FROM email_templates WHERE shop_id = ? AND template_type = ?", [$shopId, $templateType]);

    $data = [
        'name' => $name,
        'subject' => $subject,
        'body_html' => $bodyHtml,
        'body_text' => $bodyText,
        'is_active' => $isActive
    ];

    if ($existing) {
        Database::update('email_templates', $data, 'id = ?', [$existing['id']]);
    } else {
        $data['shop_id'] = $shopId;
        $data['template_type'] = $templateType;
        Database::insert('email_templates', $data);
    }

    echo json_encode(['success' => true, 'message' => 'Vorlage gespeichert']);
}

// =====================================================================
// SEND TEST EMAIL
// =====================================================================
function handleSendTest(int $shopId): void
{
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Ungültige E-Mail-Adresse']);
        return;
    }

    $htmlBody = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h1 style="color: #667eea;">✉️ Test E-Mail</h1>
    <p>Diese Test-E-Mail wurde erfolgreich versendet.</p>
    <p>Zeitstempel: ' . date('d.m.Y H:i:s') . '</p>
    <p>Falls Sie diese E-Mail erhalten haben, sind Ihre SMTP-Einstellungen korrekt konfiguriert.</p>
    <hr style="border: 1px solid #eee;">
    <p style="color: #666; font-size: 12px;">Gesendet von Ihrem Shop-Admin-Panel</p>
</body>
</html>';

    $result = Mailer::send($email, 'Test E-Mail - Shop Admin', $htmlBody);

    echo json_encode($result);
}

// =====================================================================
// GET EMAIL LOG
// =====================================================================
function handleGetLog(int $shopId): void
{
    $limit = (int) ($_GET['limit'] ?? 50);
    $offset = (int) ($_GET['offset'] ?? 0);

    $logs = Database::fetchAll("
        SELECT * FROM email_log 
        WHERE shop_id = ? 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
    ", [$shopId, $limit, $offset]);

    $total = Database::fetch("SELECT COUNT(*) as cnt FROM email_log WHERE shop_id = ?", [$shopId]);

    echo json_encode([
        'success' => true,
        'logs' => $logs,
        'total' => (int) ($total['cnt'] ?? 0)
    ]);
}
