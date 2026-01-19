<?php
/**
 * Mailer Service Class
 * 
 * Handles all email sending functionality with SMTP support
 * and template management.
 */

class Mailer
{
    private static $settings = null;

    /**
     * Load email settings from database
     */
    private static function loadSettings()
    {
        if (self::$settings !== null) {
            return self::$settings;
        }

        try {
            $row = Database::fetch("SELECT * FROM email_settings WHERE shop_id = 1");
            if ($row) {
                self::$settings = $row;
                return self::$settings;
            }
        } catch (Exception $e) {
            // Table might not exist
        }

        // Default settings
        self::$settings = [
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_user' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'from_name' => 'Mein Online Shop',
            'from_email' => 'noreply@example.com',
            'reply_to' => 'support@example.com'
        ];

        return self::$settings;
    }

    /**
     * Send an email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $htmlBody HTML body
     * @param string $textBody Optional plain text body
     * @param array $attachments Optional attachments
     * @return array Result with success status
     */
    public static function send($to, $subject, $htmlBody, $textBody = '', $attachments = [])
    {
        $settings = self::loadSettings();

        // Build headers
        $fromName = $settings['from_name'] ?? 'Shop';
        $fromEmail = $settings['from_email'] ?? 'noreply@example.com';
        $replyTo = $settings['reply_to'] ?? $fromEmail;

        $boundary = md5(time());

        $headers = [
            'MIME-Version: 1.0',
            'From: ' . self::formatEmail($fromName, $fromEmail),
            'Reply-To: ' . $replyTo,
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            'X-Mailer: ShopAdmin/1.0'
        ];

        // Build message body
        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $body .= ($textBody ?: strip_tags($htmlBody)) . "\r\n\r\n";

        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $body .= $htmlBody . "\r\n\r\n";

        $body .= "--{$boundary}--";

        // Use SMTP if configured, otherwise use PHP mail()
        if (!empty($settings['smtp_host']) && !empty($settings['smtp_user'])) {
            $result = self::sendSMTP($to, $subject, $body, $headers, $settings);
        } else {
            $result = mail($to, $subject, $body, implode("\r\n", $headers));
        }

        // Log email
        self::logEmail($to, $subject, $result ? 'sent' : 'failed');

        return [
            'success' => $result,
            'message' => $result ? 'E-Mail erfolgreich gesendet' : 'E-Mail konnte nicht gesendet werden'
        ];
    }

    /**
     * Send email via SMTP
     */
    private static function sendSMTP($to, $subject, $body, $headers, $settings)
    {
        $host = $settings['smtp_host'];
        $port = (int) ($settings['smtp_port'] ?? 587);
        $user = $settings['smtp_user'];
        $password = $settings['smtp_password'];
        $encryption = $settings['smtp_encryption'] ?? 'tls';
        $fromEmail = $settings['from_email'];

        try {
            // Open connection
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            $prefix = ($encryption === 'ssl') ? 'ssl://' : '';
            $socket = stream_socket_client(
                "{$prefix}{$host}:{$port}",
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$socket) {
                error_log("SMTP Connect Error: {$errstr}");
                return false;
            }

            // Read greeting
            fgets($socket);

            // EHLO
            fwrite($socket, "EHLO localhost\r\n");
            self::readSMTPResponse($socket);

            // STARTTLS if needed
            if ($encryption === 'tls') {
                fwrite($socket, "STARTTLS\r\n");
                self::readSMTPResponse($socket);
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                fwrite($socket, "EHLO localhost\r\n");
                self::readSMTPResponse($socket);
            }

            // AUTH
            fwrite($socket, "AUTH LOGIN\r\n");
            self::readSMTPResponse($socket);
            fwrite($socket, base64_encode($user) . "\r\n");
            self::readSMTPResponse($socket);
            fwrite($socket, base64_encode($password) . "\r\n");
            self::readSMTPResponse($socket);

            // MAIL FROM
            fwrite($socket, "MAIL FROM:<{$fromEmail}>\r\n");
            self::readSMTPResponse($socket);

            // RCPT TO
            fwrite($socket, "RCPT TO:<{$to}>\r\n");
            self::readSMTPResponse($socket);

            // DATA
            fwrite($socket, "DATA\r\n");
            self::readSMTPResponse($socket);

            // Send headers and body
            fwrite($socket, "To: {$to}\r\n");
            fwrite($socket, "Subject: {$subject}\r\n");
            foreach ($headers as $header) {
                fwrite($socket, "{$header}\r\n");
            }
            fwrite($socket, "\r\n{$body}\r\n.\r\n");
            self::readSMTPResponse($socket);

            // QUIT
            fwrite($socket, "QUIT\r\n");
            fclose($socket);

            return true;
        } catch (Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            return false;
        }
    }

    private static function readSMTPResponse($socket)
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ')
                break;
        }
        return $response;
    }

    private static function formatEmail($name, $email)
    {
        return "=?UTF-8?B?" . base64_encode($name) . "?= <{$email}>";
    }

    /**
     * Log sent emails
     */
    private static function logEmail($to, $subject, $status)
    {
        try {
            Database::query("
                INSERT INTO email_log (shop_id, recipient, subject, status, created_at)
                VALUES (1, ?, ?, ?, NOW())
            ", [$to, $subject, $status]);
        } catch (Exception $e) {
            // Logging failed, not critical
        }
    }

    /**
     * Send shipment notification email
     */
    public static function sendShipmentNotification($shipment, $order, $customer)
    {
        $template = self::getTemplate('shipment_notification');

        $trackingUrl = '';
        if (!empty($shipment['tracking_number'])) {
            $trackingUrl = self::buildTrackingUrl(
                $shipment['carrier'] ?? $shipment['carrier_name'] ?? 'DHL',
                $shipment['tracking_number']
            );
        }

        // Prepare variables for template
        $vars = [
            '{customer_name}' => ($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''),
            '{order_number}' => $order['order_number'] ?? ('#' . $order['id']),
            '{shipment_number}' => $shipment['shipment_number'] ?? '',
            '{carrier_name}' => $shipment['carrier'] ?? $shipment['carrier_name'] ?? 'Versanddienstleister',
            '{tracking_number}' => $shipment['tracking_number'] ?? 'Noch nicht verfügbar',
            '{tracking_url}' => $trackingUrl,
            '{estimated_delivery}' => $shipment['estimated_delivery'] ?? 'In Kürze',
            '{shop_name}' => 'Mein Online Shop',
            '{current_year}' => date('Y')
        ];

        $subject = str_replace(array_keys($vars), array_values($vars), $template['subject']);
        $htmlBody = str_replace(array_keys($vars), array_values($vars), $template['body_html']);

        $to = $customer['email'] ?? '';
        if (empty($to)) {
            return ['success' => false, 'message' => 'Keine E-Mail-Adresse vorhanden'];
        }

        return self::send($to, $subject, $htmlBody);
    }

    /**
     * Send delivery confirmation email
     */
    public static function sendDeliveryConfirmation($shipment, $order, $customer)
    {
        $template = self::getTemplate('delivery_confirmation');

        $vars = [
            '{customer_name}' => ($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''),
            '{order_number}' => $order['order_number'] ?? ('#' . $order['id']),
            '{shipment_number}' => $shipment['shipment_number'] ?? '',
            '{delivery_date}' => date('d.m.Y', strtotime($shipment['delivered_at'] ?? 'now')),
            '{shop_name}' => 'Mein Online Shop',
            '{current_year}' => date('Y')
        ];

        $subject = str_replace(array_keys($vars), array_values($vars), $template['subject']);
        $htmlBody = str_replace(array_keys($vars), array_values($vars), $template['body_html']);

        $to = $customer['email'] ?? '';
        if (empty($to)) {
            return ['success' => false, 'message' => 'Keine E-Mail-Adresse vorhanden'];
        }

        return self::send($to, $subject, $htmlBody);
    }

    /**
     * Build tracking URL for carrier
     */
    private static function buildTrackingUrl($carrier, $trackingNumber)
    {
        $urls = [
            'DHL' => 'https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?piececode=',
            'DPD' => 'https://tracking.dpd.de/status/de_DE/parcel/',
            'UPS' => 'https://www.ups.com/track?tracknum=',
            'Hermes' => 'https://www.myhermes.de/empfangen/sendungsverfolgung/sendungsinformation?tracking=',
            'Swiss Post' => 'https://www.post.ch/swisspost-tracking?formattedParcelCodes=',
            'GLS' => 'https://gls-group.eu/DE/de/paketverfolgung?match=',
            'FedEx' => 'https://www.fedex.com/fedextrack/?trknbr=',
            'Austrian Post' => 'https://www.post.at/sv/sendungssuche?snr='
        ];

        $baseUrl = $urls[strtoupper($carrier)] ?? $urls['DHL'];
        return $baseUrl . urlencode($trackingNumber);
    }

    /**
     * Get email template
     */
    private static function getTemplate($type)
    {
        try {
            $template = Database::fetch("
                SELECT * FROM email_templates WHERE template_type = ? AND shop_id = 1
            ", [$type]);

            if ($template) {
                return $template;
            }
        } catch (Exception $e) {
            // Use default
        }

        // Default templates
        $templates = [
            'shipment_notification' => [
                'subject' => 'Ihre Bestellung {order_number} wurde versendet',
                'body_html' => self::getDefaultShipmentTemplate()
            ],
            'delivery_confirmation' => [
                'subject' => 'Ihre Bestellung {order_number} wurde zugestellt',
                'body_html' => self::getDefaultDeliveryTemplate()
            ]
        ];

        return $templates[$type] ?? ['subject' => 'Benachrichtigung', 'body_html' => '<p>Keine Vorlage verfügbar</p>'];
    }

    /**
     * Default shipment notification template
     */
    private static function getDefaultShipmentTemplate()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; }
        .tracking-box { background: #f8f9fa; border: 2px solid #667eea; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
        .tracking-number { font-size: 24px; font-weight: bold; color: #667eea; letter-spacing: 2px; }
        .btn { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        .btn:hover { background: #5a6fd6; }
        .footer { background: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 8px 8px; }
        .icon { font-size: 48px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">📦</div>
            <h1>Ihre Bestellung ist unterwegs!</h1>
        </div>
        <div class="content">
            <p>Hallo {customer_name},</p>
            <p>Gute Nachrichten! Ihre Bestellung <strong>{order_number}</strong> wurde versendet und ist auf dem Weg zu Ihnen.</p>
            
            <div class="tracking-box">
                <p style="margin: 0 0 10px 0; color: #666;">Sendungsnummer:</p>
                <div class="tracking-number">{tracking_number}</div>
                <p style="margin: 10px 0 0 0; color: #666;">Versand über: <strong>{carrier_name}</strong></p>
            </div>
            
            <p style="text-align: center;">
                <a href="{tracking_url}" class="btn">📍 Sendung verfolgen</a>
            </p>
            
            <p><strong>Voraussichtliche Lieferung:</strong> {estimated_delivery}</p>
            
            <p>Bei Fragen zu Ihrer Sendung können Sie uns jederzeit kontaktieren.</p>
            
            <p>Mit freundlichen Grüßen,<br>Ihr {shop_name} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {current_year} {shop_name}. Alle Rechte vorbehalten.</p>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Default delivery confirmation template
     */
    private static function getDefaultDeliveryTemplate()
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; }
        .success-box { background: #e8f5e9; border: 2px solid #4caf50; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
        .btn { display: inline-block; background: #11998e; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        .footer { background: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 8px 8px; }
        .icon { font-size: 48px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">✅</div>
            <h1>Ihre Bestellung wurde zugestellt!</h1>
        </div>
        <div class="content">
            <p>Hallo {customer_name},</p>
            <p>Wir freuen uns, Ihnen mitteilen zu können, dass Ihre Bestellung <strong>{order_number}</strong> erfolgreich zugestellt wurde.</p>
            
            <div class="success-box">
                <p style="margin: 0; font-size: 18px;">✓ Zugestellt am {delivery_date}</p>
                <p style="margin: 10px 0 0 0; color: #666;">Sendung: {shipment_number}</p>
            </div>
            
            <p>Wir hoffen, Sie sind mit Ihrer Bestellung zufrieden. Falls Sie Fragen haben oder Hilfe benötigen, stehen wir Ihnen gerne zur Verfügung.</p>
            
            <p>Vielen Dank für Ihren Einkauf!</p>
            
            <p>Mit freundlichen Grüßen,<br>Ihr {shop_name} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {current_year} {shop_name}. Alle Rechte vorbehalten.</p>
        </div>
    </div>
</body>
</html>';
    }
}
