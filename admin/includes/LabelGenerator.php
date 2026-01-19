<?php
/**
 * LabelGenerator - PDF Shipping Label Generator
 * 
 * Generates shipping labels in standard 10x15cm format
 * Uses pure PHP without external libraries for maximum compatibility
 */

class LabelGenerator
{
    private $pageWidth = 283; // 10cm in points (72dpi)
    private $pageHeight = 425; // 15cm in points

    /**
     * Generate PDF shipping label
     * 
     * @param array $data Label data (shipment, sender, recipient, carrier, tracking_number)
     * @return string PDF content (base64 encoded)
     */
    public function generate(array $data): string
    {
        $shipment = $data['shipment'] ?? [];
        $sender = $data['sender'] ?? [];
        $recipient = $data['recipient'] ?? [];
        $carrier = $data['carrier'] ?? 'Versand';
        $trackingNumber = $data['tracking_number'] ?? '';

        // Build PDF content
        $pdf = $this->buildPDF($carrier, $trackingNumber, $sender, $recipient, $shipment);

        return base64_encode($pdf);
    }

    /**
     * Generate multiple labels as single PDF
     */
    public function generateBatch(array $shipments): string
    {
        $pdfs = [];
        foreach ($shipments as $data) {
            $pdfs[] = $this->generate($data);
        }

        // For now, return first label (proper PDF merge would require library)
        return $pdfs[0] ?? '';
    }

    /**
     * Build PDF manually (simple implementation)
     */
    private function buildPDF(string $carrier, string $trackingNumber, array $sender, array $recipient, array $shipment): string
    {
        $recipientName = $recipient['name'] ?? (($recipient['first_name'] ?? '') . ' ' . ($recipient['last_name'] ?? ''));
        $recipientStreet = $recipient['street'] ?? '';
        $recipientCity = ($recipient['postal_code'] ?? '') . ' ' . ($recipient['city'] ?? '');

        $senderName = $sender['company'] ?? $sender['name'] ?? 'Absender';
        $senderAddress = ($sender['street'] ?? '') . ', ' . ($sender['postal_code'] ?? '') . ' ' . ($sender['city'] ?? '');

        $shipmentNumber = $shipment['shipment_number'] ?? '';
        $date = date('d.m.Y');

        // Create simple PDF
        $objects = [];
        $objectCount = 0;

        // PDF Header
        $pdf = "%PDF-1.4\n";

        // Object 1: Catalog
        $objectCount++;
        $objects[$objectCount] = strlen($pdf);
        $pdf .= "{$objectCount} 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";

        // Object 2: Pages
        $objectCount++;
        $objects[$objectCount] = strlen($pdf);
        $pdf .= "{$objectCount} 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";

        // Object 3: Page
        $objectCount++;
        $objects[$objectCount] = strlen($pdf);
        $pdf .= "{$objectCount} 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->pageWidth} {$this->pageHeight}] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>\nendobj\n";

        // Object 4: Content stream
        $content = $this->buildContentStream($carrier, $trackingNumber, $recipientName, $recipientStreet, $recipientCity, $senderName, $senderAddress, $shipmentNumber, $date);
        $objectCount++;
        $objects[$objectCount] = strlen($pdf);
        $pdf .= "{$objectCount} 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream\nendobj\n";

        // Object 5: Font 1 (Helvetica Bold)
        $objectCount++;
        $objects[$objectCount] = strlen($pdf);
        $pdf .= "{$objectCount} 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>\nendobj\n";

        // Object 6: Font 2 (Helvetica)
        $objectCount++;
        $objects[$objectCount] = strlen($pdf);
        $pdf .= "{$objectCount} 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\nendobj\n";

        // XRef table
        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . ($objectCount + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $objectCount; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $objects[$i]);
        }

        // Trailer
        $pdf .= "trailer\n<< /Size " . ($objectCount + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF";

        return $pdf;
    }

    /**
     * Build PDF content stream
     */
    private function buildContentStream(string $carrier, string $trackingNumber, string $recipientName, string $recipientStreet, string $recipientCity, string $senderName, string $senderAddress, string $shipmentNumber, string $date): string
    {
        $stream = "";

        // Border
        $stream .= "q\n";
        $stream .= "2 w\n";
        $stream .= "5 5 273 415 re S\n";
        $stream .= "Q\n";

        // Carrier header
        $stream .= "BT\n";
        $stream .= "/F1 18 Tf\n";
        $stream .= "20 390 Td\n";
        $stream .= "(" . $this->escapePdf($carrier) . ") Tj\n";
        $stream .= "ET\n";

        // Horizontal line under carrier
        $stream .= "q\n1 w\n10 380 m 273 380 l S\nQ\n";

        // Sender (small, top section)
        $stream .= "BT\n";
        $stream .= "/F2 8 Tf\n";
        $stream .= "15 365 Td\n";
        $stream .= "(Abs: " . $this->escapePdf($senderName) . ") Tj\n";
        $stream .= "0 -10 Td\n";
        $stream .= "(" . $this->escapePdf($senderAddress) . ") Tj\n";
        $stream .= "ET\n";

        // Horizontal line
        $stream .= "q\n0.5 w\n10 340 m 273 340 l S\nQ\n";

        // Recipient (large, center section)
        $stream .= "BT\n";
        $stream .= "/F1 14 Tf\n";
        $stream .= "20 310 Td\n";
        $stream .= "(" . $this->escapePdf($recipientName) . ") Tj\n";
        $stream .= "ET\n";

        $stream .= "BT\n";
        $stream .= "/F2 12 Tf\n";
        $stream .= "20 290 Td\n";
        $stream .= "(" . $this->escapePdf($recipientStreet) . ") Tj\n";
        $stream .= "0 -18 Td\n";
        $stream .= "ET\n";

        $stream .= "BT\n";
        $stream .= "/F1 14 Tf\n";
        $stream .= "20 265 Td\n";
        $stream .= "(" . $this->escapePdf($recipientCity) . ") Tj\n";
        $stream .= "ET\n";

        // Horizontal line
        $stream .= "q\n0.5 w\n10 200 m 273 200 l S\nQ\n";

        // Tracking number (large, barcode area)
        $stream .= "BT\n";
        $stream .= "/F2 10 Tf\n";
        $stream .= "15 180 Td\n";
        $stream .= "(Sendungsnummer:) Tj\n";
        $stream .= "ET\n";

        $stream .= "BT\n";
        $stream .= "/F1 12 Tf\n";
        $stream .= "15 160 Td\n";
        $stream .= "(" . $this->escapePdf($trackingNumber) . ") Tj\n";
        $stream .= "ET\n";

        // Barcode placeholder (rectangle)
        $stream .= "q\n";
        $stream .= "0.5 w\n";
        $stream .= "15 100 m 268 100 l 268 140 l 15 140 l 15 100 l S\n";
        // Simple barcode lines
        for ($i = 0; $i < 50; $i++) {
            $x = 20 + ($i * 5);
            $height = ($i % 3 == 0) ? 35 : 25;
            $stream .= "{$x} 102 m {$x} " . (102 + $height) . " l S\n";
        }
        $stream .= "Q\n";

        // Bottom info
        $stream .= "BT\n";
        $stream .= "/F2 8 Tf\n";
        $stream .= "15 80 Td\n";
        $stream .= "(Sendung: " . $this->escapePdf($shipmentNumber) . ") Tj\n";
        $stream .= "0 -12 Td\n";
        $stream .= "(Datum: " . $this->escapePdf($date) . ") Tj\n";
        $stream .= "ET\n";

        // Footer
        $stream .= "BT\n";
        $stream .= "/F2 7 Tf\n";
        $stream .= "15 20 Td\n";
        $stream .= "(Generiert von Shop-Admin) Tj\n";
        $stream .= "ET\n";

        return $stream;
    }

    /**
     * Escape text for PDF
     */
    private function escapePdf(string $text): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        // Convert German umlauts to ASCII
        $text = str_replace(
            ['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'],
            ['ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss'],
            $text
        );
        return $text;
    }

    /**
     * Generate picklist PDF
     */
    public function generatePicklist(array $orders): string
    {
        $content = "PICKLISTE - " . date('d.m.Y H:i') . "\n\n";

        foreach ($orders as $order) {
            $content .= "Bestellung #" . ($order['order_number'] ?? $order['id']) . "\n";
            $content .= str_repeat('-', 40) . "\n";

            $items = $order['items'] ?? [];
            foreach ($items as $item) {
                $content .= "[ ] " . ($item['sku'] ?? '-') . " - " . ($item['name'] ?? 'Artikel') . " x" . ($item['quantity'] ?? 1) . "\n";
            }
            $content .= "\n";
        }

        // For a real implementation, this would generate a proper PDF
        // For now, return text content as base64
        return base64_encode($content);
    }
}
