<?php
/**
 * DHL Carrier Integration
 * 
 * Supports DHL Geschäftskundenversand API (Paket, Express)
 * Documentation: https://developer.dhl.com
 */

require_once __DIR__ . '/../CarrierService.php';

class DHLCarrier extends CarrierService
{
    private $apiBaseUrl = 'https://api.dhl.com';
    private $sandboxUrl = 'https://api-sandbox.dhl.com';

    public function getCode(): string
    {
        return 'dhl';
    }

    public function getName(): string
    {
        return 'DHL';
    }

    public function getTrackingUrl(string $trackingNumber): string
    {
        return 'https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?piececode=' . urlencode($trackingNumber);
    }

    /**
     * Create DHL Shipping Label
     */
    public function createLabel(array $shipment, array $sender, array $recipient, array $packages): array
    {
        if (!$this->isConfigured()) {
            // Generate local label if API not configured
            return $this->generateLocalLabel($shipment, $sender, $recipient);
        }

        $baseUrl = $this->testMode ? $this->sandboxUrl : $this->apiBaseUrl;

        // Build DHL API request
        $requestData = [
            'profile' => 'STANDARD_GRUPPENPROFIL',
            'shipments' => [
                [
                    'product' => 'V01PAK', // DHL Paket
                    'billingNumber' => $this->accountNumber . '01', // Billing number + procedure
                    'refNo' => $shipment['shipment_number'] ?? '',
                    'shipper' => [
                        'name1' => $sender['company'] ?? $sender['name'] ?? '',
                        'addressStreet' => $sender['street'] ?? '',
                        'addressHouse' => $sender['house_number'] ?? '',
                        'postalCode' => $sender['postal_code'] ?? '',
                        'city' => $sender['city'] ?? '',
                        'country' => $sender['country_code'] ?? 'DE',
                        'email' => $sender['email'] ?? ''
                    ],
                    'consignee' => [
                        'name1' => $recipient['name'] ?? ($recipient['first_name'] . ' ' . $recipient['last_name']),
                        'addressStreet' => $recipient['street'] ?? '',
                        'addressHouse' => $recipient['house_number'] ?? '',
                        'postalCode' => $recipient['postal_code'] ?? '',
                        'city' => $recipient['city'] ?? '',
                        'country' => $recipient['country_code'] ?? 'DE',
                        'email' => $recipient['email'] ?? ''
                    ],
                    'details' => [
                        'weight' => [
                            'uom' => 'kg',
                            'value' => $packages[0]['weight'] ?? 1.0
                        ]
                    ]
                ]
            ]
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret)
        ];

        $response = $this->httpRequest(
            $baseUrl . '/parcel/de/shipping/v2/orders',
            'POST',
            $requestData,
            $headers
        );

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => 'DHL API Fehler: ' . ($response['data']['detail'] ?? 'Unbekannter Fehler'),
                'status_code' => $response['status_code']
            ];
        }

        $data = $response['data'];
        $shipmentData = $data['items'][0] ?? [];

        return [
            'success' => true,
            'tracking_number' => $shipmentData['shipmentNo'] ?? '',
            'label_url' => $shipmentData['label']['url'] ?? '',
            'label_data' => $shipmentData['label']['b64'] ?? '',
            'label_format' => 'PDF',
            'carrier_response' => $shipmentData
        ];
    }

    /**
     * Get tracking information from DHL
     */
    public function getTracking(string $trackingNumber): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'API nicht konfiguriert'
            ];
        }

        $baseUrl = $this->testMode ? $this->sandboxUrl : $this->apiBaseUrl;

        $headers = [
            'Accept' => 'application/json',
            'DHL-API-Key' => $this->apiKey
        ];

        $response = $this->httpRequest(
            $baseUrl . '/track/shipments?trackingNumber=' . urlencode($trackingNumber),
            'GET',
            [],
            $headers
        );

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => 'Tracking-Abfrage fehlgeschlagen',
                'tracking_url' => $this->getTrackingUrl($trackingNumber)
            ];
        }

        $data = $response['data'];
        $shipment = $data['shipments'][0] ?? [];
        $events = $shipment['events'] ?? [];

        return [
            'success' => true,
            'tracking_number' => $trackingNumber,
            'status' => $shipment['status']['status'] ?? 'unknown',
            'status_text' => $shipment['status']['statusCode'] ?? '',
            'estimated_delivery' => $shipment['estimatedDeliveryTime'] ?? null,
            'events' => array_map(fn($e) => [
                'timestamp' => $e['timestamp'] ?? '',
                'location' => $e['location']['address']['addressLocality'] ?? '',
                'description' => $e['description'] ?? ''
            ], $events),
            'tracking_url' => $this->getTrackingUrl($trackingNumber)
        ];
    }

    /**
     * Cancel DHL shipment
     */
    public function cancelShipment(string $trackingNumber): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'API nicht konfiguriert'];
        }

        $baseUrl = $this->testMode ? $this->sandboxUrl : $this->apiBaseUrl;

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret)
        ];

        $response = $this->httpRequest(
            $baseUrl . '/parcel/de/shipping/v2/orders/' . urlencode($trackingNumber),
            'DELETE',
            [],
            $headers
        );

        return [
            'success' => $response['success'],
            'message' => $response['success'] ? 'Sendung storniert' : 'Stornierung fehlgeschlagen'
        ];
    }

    /**
     * Test DHL API connection
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API-Zugangsdaten nicht konfiguriert'];
        }

        // Try to get tracking for a test number
        $baseUrl = $this->testMode ? $this->sandboxUrl : $this->apiBaseUrl;

        $headers = [
            'Accept' => 'application/json',
            'DHL-API-Key' => $this->apiKey
        ];

        $response = $this->httpRequest(
            $baseUrl . '/track/shipments?trackingNumber=00340434161094042557',
            'GET',
            [],
            $headers
        );

        if ($response['status_code'] === 401) {
            return ['success' => false, 'message' => 'Authentifizierung fehlgeschlagen'];
        }

        return [
            'success' => $response['status_code'] < 500,
            'message' => $response['status_code'] < 500 ? 'Verbindung erfolgreich' : 'Serverfehler'
        ];
    }
}
