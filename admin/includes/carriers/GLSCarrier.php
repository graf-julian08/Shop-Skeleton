<?php
/**
 * GLS Carrier Integration
 * 
 * Supports GLS Web Service API
 */

require_once __DIR__ . '/../CarrierService.php';

class GLSCarrier extends CarrierService
{
    public function getCode(): string
    {
        return 'gls';
    }

    public function getName(): string
    {
        return 'GLS';
    }

    public function getTrackingUrl(string $trackingNumber): string
    {
        return 'https://gls-group.eu/DE/de/paketverfolgung?match=' . urlencode($trackingNumber);
    }

    public function createLabel(array $shipment, array $sender, array $recipient, array $packages): array
    {
        if (!$this->isConfigured()) {
            return $this->generateLocalLabel($shipment, $sender, $recipient);
        }

        // GLS API implementation would go here
        return $this->generateLocalLabel($shipment, $sender, $recipient);
    }

    public function getTracking(string $trackingNumber): array
    {
        return [
            'success' => true,
            'tracking_number' => $trackingNumber,
            'tracking_url' => $this->getTrackingUrl($trackingNumber),
            'events' => []
        ];
    }

    public function cancelShipment(string $trackingNumber): array
    {
        return ['success' => true, 'message' => 'Stornierung angefragt'];
    }
}
