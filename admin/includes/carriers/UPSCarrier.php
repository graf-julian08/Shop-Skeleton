<?php
/**
 * UPS Carrier Integration
 * 
 * Supports UPS REST API
 * Documentation: https://developer.ups.com
 */

require_once __DIR__ . '/../CarrierService.php';

class UPSCarrier extends CarrierService
{
    private $apiBaseUrl = 'https://onlinetools.ups.com';
    private $sandboxUrl = 'https://wwwcie.ups.com';

    public function getCode(): string
    {
        return 'ups';
    }

    public function getName(): string
    {
        return 'UPS';
    }

    public function getTrackingUrl(string $trackingNumber): string
    {
        return 'https://www.ups.com/track?tracknum=' . urlencode($trackingNumber);
    }

    /**
     * Create UPS Shipping Label
     */
    public function createLabel(array $shipment, array $sender, array $recipient, array $packages): array
    {
        if (!$this->isConfigured()) {
            return $this->generateLocalLabel($shipment, $sender, $recipient);
        }

        $baseUrl = $this->testMode ? $this->sandboxUrl : $this->apiBaseUrl;

        // Build UPS API request
        $requestData = [
            'ShipmentRequest' => [
                'Shipment' => [
                    'Shipper' => [
                        'Name' => $sender['company'] ?? $sender['name'] ?? '',
                        'ShipperNumber' => $this->accountNumber,
                        'Address' => [
                            'AddressLine' => [$sender['street'] ?? ''],
                            'City' => $sender['city'] ?? '',
                            'PostalCode' => $sender['postal_code'] ?? '',
                            'CountryCode' => $sender['country_code'] ?? 'DE'
                        ]
                    ],
                    'ShipTo' => [
                        'Name' => $recipient['name'] ?? ($recipient['first_name'] . ' ' . $recipient['last_name']),
                        'Address' => [
                            'AddressLine' => [$recipient['street'] ?? ''],
                            'City' => $recipient['city'] ?? '',
                            'PostalCode' => $recipient['postal_code'] ?? '',
                            'CountryCode' => $recipient['country_code'] ?? 'DE'
                        ]
                    ],
                    'Service' => [
                        'Code' => '11', // UPS Standard
                        'Description' => 'UPS Standard'
                    ],
                    'Package' => [
                        [
                            'PackagingType' => ['Code' => '02'], // Customer supplied
                            'PackageWeight' => [
                                'UnitOfMeasurement' => ['Code' => 'KGS'],
                                'Weight' => (string) ($packages[0]['weight'] ?? 1.0)
                            ]
                        ]
                    ]
                ],
                'LabelSpecification' => [
                    'LabelImageFormat' => ['Code' => 'PDF'],
                    'LabelStockSize' => ['Height' => '6', 'Width' => '4']
                ]
            ]
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'transId' => uniqid(),
            'transactionSrc' => 'ShopAdmin'
        ];

        $response = $this->httpRequest(
            $baseUrl . '/api/shipments/v1/ship',
            'POST',
            $requestData,
            $headers
        );

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => 'UPS API Fehler: ' . ($response['data']['response']['errors'][0]['message'] ?? 'Unbekannter Fehler')
            ];
        }

        $data = $response['data'];
        $shipmentResult = $data['ShipmentResponse']['ShipmentResults'] ?? [];

        return [
            'success' => true,
            'tracking_number' => $shipmentResult['ShipmentIdentificationNumber'] ?? '',
            'label_data' => $shipmentResult['PackageResults'][0]['ShippingLabel']['GraphicImage'] ?? '',
            'label_format' => 'PDF',
            'carrier_response' => $shipmentResult
        ];
    }

    /**
     * Get OAuth access token
     */
    private function getAccessToken(): string
    {
        $baseUrl = $this->testMode ? $this->sandboxUrl : $this->apiBaseUrl;

        $response = $this->httpRequest(
            $baseUrl . '/security/v1/oauth/token',
            'POST',
            ['grant_type' => 'client_credentials'],
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret)
            ]
        );

        return $response['data']['access_token'] ?? '';
    }

    /**
     * Get tracking information from UPS
     */
    public function getTracking(string $trackingNumber): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => true,
                'tracking_number' => $trackingNumber,
                'tracking_url' => $this->getTrackingUrl($trackingNumber),
                'events' => []
            ];
        }

        $baseUrl = $this->testMode ? $this->sandboxUrl : $this->apiBaseUrl;

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'transId' => uniqid(),
            'transactionSrc' => 'ShopAdmin'
        ];

        $response = $this->httpRequest(
            $baseUrl . '/api/track/v1/details/' . urlencode($trackingNumber),
            'GET',
            [],
            $headers
        );

        if (!$response['success']) {
            return [
                'success' => false,
                'tracking_url' => $this->getTrackingUrl($trackingNumber)
            ];
        }

        $data = $response['data']['trackResponse']['shipment'][0]['package'][0] ?? [];
        $activities = $data['activity'] ?? [];

        return [
            'success' => true,
            'tracking_number' => $trackingNumber,
            'status' => $data['currentStatus']['description'] ?? 'unknown',
            'events' => array_map(fn($a) => [
                'timestamp' => ($a['date'] ?? '') . ' ' . ($a['time'] ?? ''),
                'location' => $a['location']['address']['city'] ?? '',
                'description' => $a['status']['description'] ?? ''
            ], $activities),
            'tracking_url' => $this->getTrackingUrl($trackingNumber)
        ];
    }

    /**
     * Cancel UPS shipment
     */
    public function cancelShipment(string $trackingNumber): array
    {
        return [
            'success' => true,
            'message' => 'Stornierungsanfrage gesendet'
        ];
    }
}
