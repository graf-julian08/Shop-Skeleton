<?php
/**
 * FulfillmentHelper - Easy Shop Integration
 * 
 * Use this class in your shop frontend to easily integrate with the fulfillment system.
 * Simply include this file and call the static methods.
 * 
 * Example Usage:
 * 
 * // After a successful order is placed:
 * require_once 'admin/includes/FulfillmentHelper.php';
 * FulfillmentHelper::onOrderPlaced($orderId);
 * 
 * // To auto-create shipment when order is paid:
 * FulfillmentHelper::onOrderPaid($orderId);
 */

require_once __DIR__ . '/Database.php';

class FulfillmentHelper
{
    /**
     * Hook: Called when an order is placed
     * Prepares the order for fulfillment tracking
     */
    public static function onOrderPlaced(int $orderId): array
    {
        // Update order status to pending if not already
        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }

        return ['success' => true, 'order_id' => $orderId, 'status' => $order['status']];
    }

    /**
     * Hook: Called when an order is paid
     * Automatically creates a shipment for the order
     */
    public static function onOrderPaid(int $orderId, bool $autoCreateShipment = true): array
    {
        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }

        // Update payment status
        Database::update('orders', ['payment_status' => 'paid'], 'id = ?', [$orderId]);

        // Auto-create shipment if enabled
        if ($autoCreateShipment) {
            return self::createShipmentForOrder($orderId);
        }

        return ['success' => true, 'order_id' => $orderId, 'message' => 'Order marked as paid'];
    }

    /**
     * Create a shipment for an order
     */
    public static function createShipmentForOrder(int $orderId, int $carrierId = 0, int $warehouseId = 0): array
    {
        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }

        $shopId = $order['shop_id'];

        // Check if shipment already exists
        $existing = Database::fetch(
            "SELECT id FROM shipments WHERE order_id = ? AND status NOT IN ('failed', 'returned')",
            [$orderId]
        );
        if ($existing) {
            return ['success' => true, 'shipment_id' => $existing['id'], 'message' => 'Shipment already exists'];
        }

        // Get default carrier
        if ($carrierId <= 0) {
            $defaultCarrier = Database::fetch(
                "SELECT id FROM carriers WHERE shop_id = ? AND is_default = 1 AND is_active = 1",
                [$shopId]
            );
            $carrierId = $defaultCarrier ? (int) $defaultCarrier['id'] : null;
        }

        // Get default warehouse
        if ($warehouseId <= 0) {
            $defaultWarehouse = Database::fetch(
                "SELECT id FROM warehouses WHERE shop_id = ? AND is_default = 1",
                [$shopId]
            );
            $warehouseId = $defaultWarehouse ? (int) $defaultWarehouse['id'] : null;
        }

        // Generate shipment number
        $shipmentNumber = 'SHP-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Create shipment
        $shipmentId = Database::insert('shipments', [
            'shop_id' => $shopId,
            'order_id' => $orderId,
            'shipment_number' => $shipmentNumber,
            'warehouse_id' => $warehouseId,
            'carrier_id' => $carrierId,
            'status' => 'pending',
            'estimated_delivery' => date('Y-m-d', strtotime('+3 days'))
        ]);

        // Add all order items to shipment
        $orderItems = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
        foreach ($orderItems as $item) {
            Database::insert('shipment_items', [
                'shipment_id' => $shipmentId,
                'order_item_id' => $item['id'],
                'product_id' => $item['product_id'],
                'sku' => $item['sku'],
                'name' => $item['name'],
                'quantity' => $item['quantity']
            ]);
        }

        // Add history entry
        Database::insert('shipment_status_history', [
            'shipment_id' => $shipmentId,
            'status' => 'Sendung erstellt',
            'comment' => 'Automatisch erstellt bei Zahlung'
        ]);

        // Update order status
        Database::update('orders', ['status' => 'processing'], 'id = ?', [$orderId]);

        return [
            'success' => true,
            'shipment_id' => $shipmentId,
            'shipment_number' => $shipmentNumber,
            'message' => 'Shipment created successfully'
        ];
    }

    /**
     * Get tracking info for customer
     */
    public static function getTrackingInfo(int $orderId): array
    {
        $shipment = Database::fetch("
            SELECT s.*, c.name as carrier_name, c.tracking_url_template
            FROM shipments s
            LEFT JOIN carriers c ON c.id = s.carrier_id
            WHERE s.order_id = ? AND s.status NOT IN ('failed', 'returned')
            ORDER BY s.created_at DESC
            LIMIT 1
        ", [$orderId]);

        if (!$shipment) {
            return ['success' => false, 'error' => 'No shipment found'];
        }

        $trackingUrl = null;
        if ($shipment['tracking_number'] && $shipment['tracking_url_template']) {
            $trackingUrl = str_replace('{tracking}', $shipment['tracking_number'], $shipment['tracking_url_template']);
        }

        return [
            'success' => true,
            'shipment_number' => $shipment['shipment_number'],
            'tracking_number' => $shipment['tracking_number'],
            'tracking_url' => $trackingUrl,
            'carrier' => $shipment['carrier_name'],
            'status' => $shipment['status'],
            'shipped_at' => $shipment['shipped_at'],
            'estimated_delivery' => $shipment['estimated_delivery']
        ];
    }

    /**
     * Get shipment status for customer display
     */
    public static function getStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'In Bearbeitung',
            'picking' => 'Wird kommissioniert',
            'packed' => 'Verpackt',
            'shipped' => 'Versendet',
            'in_transit' => 'Unterwegs',
            'out_for_delivery' => 'In Zustellung',
            'delivered' => 'Zugestellt',
            'failed' => 'Zustellung fehlgeschlagen',
            'returned' => 'Zurückgesendet'
        ];
        return $labels[$status] ?? $status;
    }

    /**
     * Check if order is shippable
     */
    public static function isOrderShippable(int $orderId): bool
    {
        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
        if (!$order) {
            return false;
        }

        // Must be paid
        if ($order['payment_status'] !== 'paid') {
            return false;
        }

        // Must not be cancelled/completed
        if (in_array($order['status'], ['cancelled', 'refunded', 'completed'])) {
            return false;
        }

        // Must not already have a non-failed shipment
        $existing = Database::fetch(
            "SELECT id FROM shipments WHERE order_id = ? AND status NOT IN ('failed', 'returned')",
            [$orderId]
        );
        if ($existing) {
            return false;
        }

        return true;
    }

    /**
     * Create a return/RMA request
     */
    public static function createReturnRequest(int $orderId, string $reason, array $items = []): array
    {
        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }

        // Generate return number
        $returnNumber = 'RMA-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Create return
        $returnId = Database::insert('returns', [
            'shop_id' => $order['shop_id'],
            'order_id' => $orderId,
            'return_number' => $returnNumber,
            'status' => 'requested',
            'reason' => $reason,
            'return_type' => 'refund'
        ]);

        // Add items
        if (empty($items)) {
            // Return all items
            $orderItems = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
            foreach ($orderItems as $item) {
                Database::insert('return_items', [
                    'return_id' => $returnId,
                    'order_item_id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'sku' => $item['sku'],
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'item_condition' => 'used'
                ]);
            }
        } else {
            foreach ($items as $item) {
                Database::insert('return_items', [
                    'return_id' => $returnId,
                    'order_item_id' => $item['order_item_id'] ?? null,
                    'product_id' => $item['product_id'] ?? null,
                    'sku' => $item['sku'] ?? '',
                    'name' => $item['name'] ?? '',
                    'quantity' => $item['quantity'] ?? 1,
                    'item_condition' => $item['condition'] ?? 'used'
                ]);
            }
        }

        return [
            'success' => true,
            'return_id' => $returnId,
            'return_number' => $returnNumber,
            'message' => 'Rückgabe-Anfrage erstellt'
        ];
    }
}
