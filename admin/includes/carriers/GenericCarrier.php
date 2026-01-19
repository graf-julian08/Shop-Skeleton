<?php
/**
 * Generic Carrier - Fallback for carriers without API integration
 * 
 * Generates local labels without carrier API connection
 */

require_once __DIR__ . '/../CarrierService.php';

class GenericCarrier extends CarrierService
{
    private $carrierName = 'Versand';

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->carrierName = $config['name'] ?? 'Versand';
    }

    public function getCode(): string
    {
        return 'generic';
    }

    public function getName(): string
    {
        return $this->carrierName;
    }

    public function getTrackingUrl(string $trackingNumber): string
    {
        return '';
    }

    public function createLabel(array $shipment, array $sender, array $recipient, array $packages): array
    {
        return $this->generateLocalLabel($shipment, $sender, $recipient);
    }

    public function getTracking(string $trackingNumber): array
    {
        return [
            'success' => false,
            'error' => 'Tracking nicht verfügbar für diesen Carrier'
        ];
    }

    public function cancelShipment(string $trackingNumber): array
    {
        return ['success' => false, 'error' => 'Stornierung nicht verfügbar'];
    }

    public function isConfigured(): bool
    {
        return true; // Always configured since no API needed
    }
}
