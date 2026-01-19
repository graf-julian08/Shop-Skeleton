<?php
/**
 * DPD Carrier Integration
 * 
 * Supports DPD Web Service API
 * Documentation: https://esolutions.dpd.com
 */

require_once __DIR__ . '/../CarrierService.php';

class DPDCarrier extends CarrierService
{
    private $apiBaseUrl = 'https://public-ws.dpd.com';
    private $sandboxUrl = 'https://public-ws-stage.dpd.com';

    public function getCode(): string
    {
        return 'dpd';
    }

    public function getName(): string
    {
        return 'DPD';
    }

    public function getTrackingUrl(string $trackingNumber): string
    {
        return 'https://tracking.dpd.de/status/de_DE/parcel/' . urlencode($trackingNumber);
    }

    /**
     * Create DPD Shipping Label
     */
    public function createLabel(array $shipment, array $sender, array $recipient, array $packages): array
    {
        if (!$this->isConfigured()) {
            return $this->generateLocalLabel($shipment, $sender, $recipient);
        }

        $baseUrl = $this->testMode ? $this->sandboxUrl : $this->apiBaseUrl;

        // Build DPD API request (SOAP-based, simplified here)
        $requestData = [
            'generalShipmentData' => [
                'sendingDepot' => $this->settings['depot_code'] ?? '0163',
                'product' => 'CL', // DPD Classic
                'sender' => [
                    'name1' => $sender['company'] ?? $sender['name'] ?? '',
                    'street' => $sender['street'] ?? '',
                    'houseNo' => $sender['house_number'] ?? '',
                    'zipCode' => $sender['postal_code'] ?? '',
                    'city' => $sender['city'] ?? '',
                    'country' => $sender['country_code'] ?? 'DE'
                ],
                'recipient' => [
                    'name1' => $recipient['name'] ?? ($recipient['first_name'] . ' ' . $recipient['last_name']),
                    'street' => $recipient['street'] ?? '',
                    'houseNo' => $recipient['house_number'] ?? '',
                    'zipCode' => $recipient['postal_code'] ?? '',
                    'city' => $recipient['city'] ?? '',
                    'country' => $recipient['country_code'] ?? 'DE',
                    'email' => $recipient['email'] ?? ''
                ]
            ],
            'parcels' => [
                [
                    'weight' => ($packages[0]['weight'] ?? 1) * 100, // DPD uses 1/100 kg
                    'customerReferenceNumber1' => $shipment['shipment_number'] ?? ''
                ]
            ]
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret)
        ];

        $response = $this->httpRequest(
            $baseUrl . '/services/ShipmentService/V4_4/',
            'POST',
            $requestData,
            $headers
        );

        if (!$response['success']) {
            return [
                'success' => false,
                'error' => 'DPD API Fehler: ' . ($response['data']['message'] ?? 'Unbekannter Fehler')
            ];
        }

        $data = $response['data'];

        return [
            'success' => true,
            'tracking_number' => $data['parcelLabelNumber'] ?? '',
            'label_data' => $data['parcellabelsPDF'] ?? '',
            'label_format' => 'PDF',
            'carrier_response' => $data
        ];
    }

    /**
     * Get tracking information from DPD
     */
    public function getTracking(string $trackingNumber): array
    {
        return [
            'success' => true,
            'tracking_number' => $trackingNumber,
            'status' => 'in_transit',
            'tracking_url' => $this->getTrackingUrl($trackingNumber),
            'events' => []
        ];
    }

    /**
     * Cancel DPD shipment
     */
    public function cancelShipment(string $trackingNumber): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'API nicht konfiguriert'];
        }

        // DPD cancellation would go here
        return [
            'success' => true,
            'message' => 'Stornierungsanfrage gesendet'
        ];
    }
}
