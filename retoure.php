<?php
/**
 * Public Return Request Form
 * 
 * This page allows customers to submit return requests.
 * Include in your shop frontend at /retoure or /returns
 */

require_once __DIR__ . '/admin/config.php';
require_once __DIR__ . '/admin/includes/Database.php';

Database::configure($database);

$message = '';
$messageType = '';
$orderData = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'lookup_order') {
        // Look up order by number and email
        $orderNumber = trim($_POST['order_number'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $order = Database::fetch("
            SELECT o.*, 
                   c.email as customer_email,
                   (SELECT GROUP_CONCAT(CONCAT(oi.name, ' (', oi.quantity, 'x)') SEPARATOR ', ') 
                    FROM order_items oi WHERE oi.order_id = o.id) as items_summary,
                   (SELECT GROUP_CONCAT(CONCAT(oi.id, '|', oi.name, '|', oi.quantity) SEPARATOR '||') 
                    FROM order_items oi WHERE oi.order_id = o.id) as items_data
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE o.order_number = ? 
            AND c.email = ?
        ", [$orderNumber, $email]);

        if ($order) {
            // Check if return already exists
            $existingReturn = Database::fetch(
                "SELECT * FROM returns WHERE order_id = ? AND status NOT IN ('rejected')",
                [$order['id']]
            );

            if ($existingReturn) {
                $message = "Für diese Bestellung existiert bereits eine Retoure ({$existingReturn['return_number']}).";
                $messageType = 'warning';
            } else {
                $orderData = $order;
            }
        } else {
            $message = "Bestellung nicht gefunden. Bitte prüfen Sie Bestellnummer und E-Mail.";
            $messageType = 'error';
        }
    }

    if ($_POST['action'] === 'submit_return') {
        $orderId = (int) $_POST['order_id'];
        $reason = $_POST['reason'] ?? 'other';
        $reasonText = trim($_POST['reason_text'] ?? '');
        $returnType = $_POST['return_type'] ?? 'refund';
        $selectedItems = $_POST['items'] ?? [];

        // Validate
        if (empty($selectedItems)) {
            $message = "Bitte wählen Sie mindestens einen Artikel aus.";
            $messageType = 'error';
        } else {
            // Get shop_id from order
            $order = Database::fetch("SELECT shop_id FROM orders WHERE id = ?", [$orderId]);
            $shopId = $order['shop_id'] ?? 1;

            // Generate return number
            $returnNumber = 'RMA-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Create return
            $returnId = Database::insert('returns', [
                'shop_id' => $shopId,
                'order_id' => $orderId,
                'return_number' => $returnNumber,
                'status' => 'requested',
                'reason' => $reason,
                'notes' => $reasonText,
                'return_type' => $returnType
            ]);

            // Add items
            foreach ($selectedItems as $itemId) {
                $item = Database::fetch("SELECT * FROM order_items WHERE id = ?", [$itemId]);
                if ($item) {
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
            }

            $message = "Ihre Retoure-Anfrage wurde erfolgreich eingereicht! Ihre RMA-Nummer: <strong>{$returnNumber}</strong>";
            $messageType = 'success';
            $orderData = null; // Reset form
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retoure einreichen</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            padding: 40px 20px;
            color: #fff;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 24px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 8px;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            transition: border-color 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #6366f1;
        }

        select option {
            background: #1a1a2e;
            color: #fff;
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        .message {
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            text-align: center;
        }

        .message.success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .message.error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .message.warning {
            background: rgba(245, 158, 11, 0.2);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .order-info {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .order-info h3 {
            font-size: 18px;
            margin-bottom: 12px;
        }

        .order-detail {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-detail:last-child {
            border-bottom: none;
        }

        .items-list {
            margin: 20px 0;
        }

        .item-checkbox {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .item-checkbox:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .item-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #6366f1;
        }

        .steps {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 32px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.4);
        }

        .step.active {
            color: #6366f1;
        }

        .step-number {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .step.active .step-number {
            background: #6366f1;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>📦 Retoure einreichen</h1>
            <p class="subtitle">Einfach und schnell Ihre Rückgabe beantragen</p>

            <div class="steps">
                <div class="step <?= !$orderData ? 'active' : '' ?>">
                    <span class="step-number">1</span>
                    <span>Bestellung finden</span>
                </div>
                <div class="step <?= $orderData ? 'active' : '' ?>">
                    <span class="step-number">2</span>
                    <span>Artikel auswählen</span>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="message <?= $messageType ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <?php if (!$orderData): ?>
                <!-- Step 1: Find Order -->
                <form method="POST">
                    <input type="hidden" name="action" value="lookup_order">

                    <div class="form-group">
                        <label for="order_number">Bestellnummer</label>
                        <input type="text" id="order_number" name="order_number" placeholder="z.B. #10029" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-Mail-Adresse</label>
                        <input type="email" id="email" name="email" placeholder="ihre@email.de" required>
                    </div>

                    <button type="submit" class="btn">Bestellung suchen</button>
                </form>
            <?php else: ?>
                <!-- Step 2: Select Items & Reason -->
                <div class="order-info">
                    <h3>Bestellung
                        <?= htmlspecialchars($orderData['order_number']) ?>
                    </h3>
                    <div class="order-detail">
                        <span>Datum:</span>
                        <span>
                            <?= date('d.m.Y', strtotime($orderData['created_at'])) ?>
                        </span>
                    </div>
                    <div class="order-detail">
                        <span>Betrag:</span>
                        <span>
                            <?= number_format($orderData['grand_total'], 2, ',', '.') ?> €
                        </span>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="submit_return">
                    <input type="hidden" name="order_id" value="<?= $orderData['id'] ?>">

                    <div class="form-group">
                        <label>Welche Artikel möchten Sie zurückgeben?</label>
                        <div class="items-list">
                            <?php
                            $items = explode('||', $orderData['items_data']);
                            foreach ($items as $item):
                                $parts = explode('|', $item);
                                if (count($parts) >= 3):
                                    ?>
                                    <label class="item-checkbox">
                                        <input type="checkbox" name="items[]" value="<?= $parts[0] ?>">
                                        <span>
                                            <?= htmlspecialchars($parts[1]) ?> (
                                            <?= $parts[2] ?>x)
                                        </span>
                                    </label>
                                <?php endif; endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason">Grund der Rückgabe</label>
                        <select id="reason" name="reason" required>
                            <option value="">Bitte wählen...</option>
                            <option value="wrong_size">Größe passt nicht</option>
                            <option value="not_as_described">Nicht wie beschrieben</option>
                            <option value="defective">Defekt / Beschädigt</option>
                            <option value="changed_mind">Gefällt mir nicht</option>
                            <option value="wrong_item">Falscher Artikel geliefert</option>
                            <option value="other">Sonstiges</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reason_text">Weitere Details (optional)</label>
                        <textarea id="reason_text" name="reason_text" rows="3"
                            placeholder="Beschreiben Sie optional den Grund genauer..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="return_type">Was möchten Sie?</label>
                        <select id="return_type" name="return_type" required>
                            <option value="refund">Rückerstattung</option>
                            <option value="exchange">Umtausch</option>
                        </select>
                    </div>

                    <button type="submit" class="btn">Retoure beantragen</button>
                </form>

                <p style="text-align: center; margin-top: 16px;">
                    <a href="?" style="color: rgba(255,255,255,0.6);">← Andere Bestellung suchen</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>