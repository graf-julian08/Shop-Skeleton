<?php
/**
 * Customer Seeder - Adds test customer data
 */
require_once __DIR__ . '/../admin/config.php';
require_once __DIR__ . '/../admin/includes/Database.php';

Database::configure($database);

// Add test customers
$customers = [
    [
        'first_name' => 'Max',
        'last_name' => 'Mustermann',
        'email' => 'max@example.com',
        'phone' => '+49 123 456789',
        'is_active' => 1,
        'orders_count' => 12,
        'total_spent' => 1890.00,
        'customer_group_id' => 2,
        'subscribed_to_newsletter' => 1,
        'last_order_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
    ],
    [
        'first_name' => 'Anna',
        'last_name' => 'Schmidt',
        'email' => 'anna.schmidt@example.com',
        'phone' => '+49 234 567890',
        'is_active' => 1,
        'orders_count' => 8,
        'total_spent' => 1245.50,
        'customer_group_id' => 1,
        'subscribed_to_newsletter' => 0,
        'last_order_at' => date('Y-m-d H:i:s', strtotime('-15 days'))
    ],
    [
        'first_name' => 'Peter',
        'last_name' => 'Weber',
        'email' => 'p.weber@company.de',
        'phone' => '+49 345 678901',
        'is_active' => 1,
        'orders_count' => 45,
        'total_spent' => 12450.00,
        'customer_group_id' => 3,
        'subscribed_to_newsletter' => 1,
        'admin_notes' => 'Großkunde, bevorzugter Versand',
        'last_order_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
    ],
    [
        'first_name' => 'Lisa',
        'last_name' => 'Müller',
        'email' => 'lisa.m@email.de',
        'phone' => '+49 456 789012',
        'is_active' => 0,
        'orders_count' => 3,
        'total_spent' => 289.00,
        'customer_group_id' => 1,
        'subscribed_to_newsletter' => 0,
        'admin_notes' => 'Inaktiv seit 6 Monaten'
    ],
    [
        'first_name' => 'Thomas',
        'last_name' => 'Braun',
        'email' => 'thomas.braun@gmail.com',
        'phone' => '+49 567 890123',
        'is_active' => 1,
        'orders_count' => 25,
        'total_spent' => 4567.80,
        'customer_group_id' => 2,
        'subscribed_to_newsletter' => 1,
        'last_order_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
    ],
    [
        'first_name' => 'Julia',
        'last_name' => 'Fischer',
        'email' => 'julia.f@web.de',
        'phone' => '+49 678 901234',
        'is_active' => 1,
        'orders_count' => 6,
        'total_spent' => 876.40,
        'customer_group_id' => 1,
        'subscribed_to_newsletter' => 1,
        'last_order_at' => date('Y-m-d H:i:s', strtotime('-30 days'))
    ]
];

$created = 0;
$exists = 0;

foreach ($customers as $data) {
    $existing = Database::fetch('SELECT id FROM customers WHERE email = ? AND shop_id = 1', [$data['email']]);
    if (!$existing) {
        $data['shop_id'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s', strtotime('-' . rand(30, 365) . ' days'));
        Database::insert('customers', $data);
        echo "✓ Created: {$data['first_name']} {$data['last_name']}\n";
        $created++;
    } else {
        echo "○ Exists: {$data['first_name']} {$data['last_name']}\n";
        $exists++;
    }
}

echo "\n=== Summary ===\n";
echo "Created: $created\n";
echo "Already existed: $exists\n";
echo "Done!\n";
