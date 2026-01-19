<?php
/**
 * CarrierService - Abstract base class for carrier integrations
 * 
 * Each carrier (DHL, DPD, UPS, etc.) extends this class
 * and implements the required methods.
 */

abstract class CarrierService
{
    protected $apiKey = '';
    protected $apiSecret = '';
    protected $accountNumber = '';
    protected $testMode = true;
    protected $settings = [];

    /**
     * Initialize carrier with credentials
     */
    public function __construct(array $config = [])
    {
        $this->apiKey = $config['api_key'] ?? '';
        $this->apiSecret = $config['api_secret'] ?? '';
        $this->accountNumber = $config['account_number'] ?? '';
        $this->testMode = $config['test_mode'] ?? true;
        $this->settings = $config['settings'] ?? [];
    }

    /**
     * Load carrier instance from database
     */
    public static function load(int $carrierId): ?CarrierService
    {
        require_once __DIR__ . '/Database.php';

        $carrier = Database::fetch("SELECT * FROM carriers WHERE id = ?", [$carrierId]);
        if (!$carrier) {
            return null;
        }

        $config = [
            'api_key' => $carrier['api_key'] ?? '',
            'api_secret' => $carrier['api_secret'] ?? '',
            'account_number' => $carrier['account_number'] ?? '',
            'test_mode' => ($carrier['test_mode'] ?? 1) == 1,
            'settings' => json_decode($carrier['settings'] ?? '{}', true)
        ];

        $code = strtolower($carrier['code'] ?? 'generic');

        switch ($code) {
            case 'dhl':
                require_once __DIR__ . '/carriers/DHLCarrier.php';
                return new DHLCarrier($config);
            case 'dpd':
                require_once __DIR__ . '/carriers/DPDCarrier.php';
                return new DPDCarrier($config);
            case 'ups':
                require_once __DIR__ . '/carriers/UPSCarrier.php';
                return new UPSCarrier($config);
            case 'gls':
                require_once __DIR__ . '/carriers/GLSCarrier.php';
                return new GLSCarrier($config);
            default:
                require_once __DIR__ . '/carriers/GenericCarrier.php';
                return new GenericCarrier($config);
        }
    }

    /**
     * Get carrier code (e.g., 'dhl', 'dpd')
     */
    abstract public function getCode(): string;

    /**
     * Get carrier display name
     */
    abstract public function getName(): string;

    /**
     * Create shipping label
     * 
     * @param array $shipment Shipment data
     * @param array $sender Sender address
     * @param array $recipient Recipient address
     * @param array $packages Package details (weight, dimensions)
     * @return array Result with 'success', 'tracking_number', 'label_data', 'label_url'
     */
    abstract public function createLabel(array $shipment, array $sender, array $recipient, array $packages): array;

    /**
     * Get tracking information
     * 
     * @param string $trackingNumber
     * @return array Tracking events and status
     */
    abstract public function getTracking(string $trackingNumber): array;

    /**
     * Cancel a shipment
     * 
     * @param string $trackingNumber
     * @return array Result with 'success' and 'message'
     */
    abstract public function cancelShipment(string $trackingNumber): array;

    /**
     * Validate delivery address
     * 
     * @param array $address Address to validate
     * @return array Result with 'valid', 'suggestions', 'errors'
     */
    public function validateAddress(array $address): array
    {
        // Default implementation - basic validation
        $errors = [];

        if (empty($address['street'])) {
            $errors[] = 'Straße fehlt';
        }
        if (empty($address['city'])) {
            $errors[] = 'Stadt fehlt';
        }
        if (empty($address['postal_code'])) {
            $errors[] = 'PLZ fehlt';
        }
        if (empty($address['country_code'])) {
            $errors[] = 'Land fehlt';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'suggestions' => []
        ];
    }

    /**
     * Get tracking URL for customer
     */
    public function getTrackingUrl(string $trackingNumber): string
    {
        return '';
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API-Zugangsdaten nicht konfiguriert'];
        }

        return ['success' => true, 'message' => 'Verbindung erfolgreich'];
    }

    /**
     * Make HTTP request to carrier API
     */
    protected function httpRequest(string $url, string $method = 'GET', array $data = [], array $headers = []): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", array_map(fn($k, $v) => "$k: $v", array_keys($headers), $headers)),
                'content' => $method !== 'GET' ? json_encode($data) : null,
                'timeout' => 30,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        $statusCode = 0;

        if (isset($http_response_header[0])) {
            preg_match('/\d{3}/', $http_response_header[0], $matches);
            $statusCode = (int) ($matches[0] ?? 0);
        }

        return [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'status_code' => $statusCode,
            'body' => $response,
            'data' => json_decode($response, true)
        ];
    }

    /**
     * Generate local label without API (fallback)
     */
    protected function generateLocalLabel(array $shipment, array $sender, array $recipient): array
    {
        require_once __DIR__ . '/LabelGenerator.php';

        $generator = new LabelGenerator();
        $labelData = $generator->generate([
            'shipment' => $shipment,
            'sender' => $sender,
            'recipient' => $recipient,
            'carrier' => $this->getName(),
            'tracking_number' => $shipment['tracking_number'] ?? 'LOCAL-' . time()
        ]);

        return [
            'success' => true,
            'tracking_number' => $shipment['tracking_number'] ?? 'LOCAL-' . time(),
            'label_data' => $labelData,
            'label_format' => 'PDF',
            'is_local' => true
        ];
    }
}
