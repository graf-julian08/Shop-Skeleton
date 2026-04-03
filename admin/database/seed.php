<?php
/**
 * Database Seeder
 * Creates initial required data for the application to function
 * 
 * Usage: php admin/database/seed.php
 * 
 * Configuration:
 * - Set environment variables or edit the config below
 */

// Configuration (can be overridden by environment variables)
$config = [
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_name' => getenv('DB_NAME') ?: 'bagisto_admin',
    'db_user' => getenv('DB_USER') ?: 'root',
    'db_pass' => getenv('DB_PASS') ?: '',

    // Default admin user (CHANGE THESE!)
    'admin_name' => getenv('ADMIN_NAME') ?: 'Administrator',
    'admin_email' => getenv('ADMIN_EMAIL') ?: 'nevio.weishaupt@ksb-sg.ch',
    'admin_password' => getenv('ADMIN_PASSWORD') ?: 'admin123',

    // Default shop
    'shop_name' => getenv('SHOP_NAME') ?: 'Mein Online Shop',
    'shop_email' => getenv('SHOP_EMAIL') ?: 'info@meinshop.de',
];

// Include Database class
require_once __DIR__ . '/../includes/Database.php';

// Configure database
Database::configure([
    'host' => $config['db_host'],
    'database' => $config['db_name'],
    'username' => $config['db_user'],
    'password' => $config['db_pass'],
]);

echo "=== Database Seeder ===\n\n";

try {
    Database::beginTransaction();

    // =========================================
    // 1. Create Default Shop
    // =========================================
    echo "1. Creating default shop...\n";

    $existingShop = Database::fetch("SELECT id FROM shops WHERE code = 'default'");

    if (!$existingShop) {
        $shopId = Database::insert('shops', [
            'code' => 'default',
            'name' => $config['shop_name'],
            'description' => 'Ihr Premium Online Shop für hochwertige Produkte.',
            'email' => $config['shop_email'],
            'phone' => '+49 123 456789',
            'default_locale' => 'de_DE',
            'default_currency' => 'EUR',
            'timezone' => 'Europe/Berlin',
            'date_format' => 'DD.MM.YYYY',
            'time_format' => '24h',
            'weight_unit' => 'kg',
            'dimension_unit' => 'cm',
            'is_active' => 1,
            'maintenance_mode' => 0,
        ]);
        echo "   ✓ Created shop (ID: {$shopId})\n";
    }
    else {
        $shopId = $existingShop['id'];
        echo "   ⊘ Shop already exists (ID: {$shopId})\n";
    }

    // =========================================
    // 2. Create Shop Design
    // =========================================
    echo "2. Creating shop design settings...\n";

    $existingDesign = Database::fetch("SELECT id FROM shop_design WHERE shop_id = ?", [$shopId]);

    if (!$existingDesign) {
        Database::insert('shop_design', [
            'shop_id' => $shopId,
            'color_primary' => '#7c3aed',
            'color_secondary' => '#1a1a1a',
            'color_accent' => '#22c55e',
            'color_text' => '#ffffff',
            'color_background' => '#ffffff',
            'color_surface' => '#f5f5f5',
            'font_heading' => 'Inter',
            'font_body' => 'Inter',
            'header_style' => 'default',
            'footer_style' => 'columns',
        ]);
        echo "   ✓ Created shop design\n";
    }
    else {
        echo "   ⊘ Shop design already exists\n";
    }

    // =========================================
    // 3. Create Default Language
    // =========================================
    echo "3. Creating default language...\n";

    $existingLang = Database::fetch("SELECT id FROM languages WHERE shop_id = ? AND code = 'de_DE'", [$shopId]);

    if (!$existingLang) {
        Database::insert('languages', [
            'shop_id' => $shopId,
            'code' => 'de_DE',
            'name' => 'German',
            'native_name' => 'Deutsch',
            'is_default' => 1,
            'is_active' => 1,
        ]);
        echo "   ✓ Created German language\n";
    }
    else {
        echo "   ⊘ Language already exists\n";
    }

    // =========================================
    // 4. Create Default Currency
    // =========================================
    echo "4. Creating default currency...\n";

    $existingCurrency = Database::fetch("SELECT id FROM currencies WHERE shop_id = ? AND code = 'EUR'", [$shopId]);

    if (!$existingCurrency) {
        Database::insert('currencies', [
            'shop_id' => $shopId,
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 1.000000,
            'decimal_places' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'symbol_position' => 'after',
            'is_default' => 1,
            'is_active' => 1,
        ]);
        echo "   ✓ Created EUR currency\n";
    }
    else {
        echo "   ⊘ Currency already exists\n";
    }

    // =========================================
    // 5. Create Permissions
    // =========================================
    echo "5. Creating permissions...\n";

    $permissions = [
        // Dashboard
        ['dashboard.view', 'View Dashboard', 'dashboard'],

        // Catalog
        ['products.view', 'View Products', 'catalog'],
        ['products.create', 'Create Products', 'catalog'],
        ['products.edit', 'Edit Products', 'catalog'],
        ['products.delete', 'Delete Products', 'catalog'],
        ['categories.view', 'View Categories', 'catalog'],
        ['categories.create', 'Create Categories', 'catalog'],
        ['categories.edit', 'Edit Categories', 'catalog'],
        ['categories.delete', 'Delete Categories', 'catalog'],
        ['attributes.view', 'View Attributes', 'catalog'],
        ['attributes.manage', 'Manage Attributes', 'catalog'],

        // Customers
        ['customers.view', 'View Customers', 'customers'],
        ['customers.create', 'Create Customers', 'customers'],
        ['customers.edit', 'Edit Customers', 'customers'],
        ['customers.delete', 'Delete Customers', 'customers'],

        // Orders
        ['orders.view', 'View Orders', 'orders'],
        ['orders.edit', 'Edit Orders', 'orders'],
        ['orders.cancel', 'Cancel Orders', 'orders'],
        ['orders.refund', 'Process Refunds', 'orders'],

        // Commerce
        ['commerce.settings', 'Manage Commerce Settings', 'commerce'],
        ['discounts.manage', 'Manage Discounts', 'commerce'],
        ['taxes.manage', 'Manage Taxes', 'commerce'],
        ['shipping.manage', 'Manage Shipping', 'commerce'],
        ['payments.manage', 'Manage Payments', 'commerce'],

        // Finance
        ['finance.view', 'View Finance', 'finance'],
        ['invoices.manage', 'Manage Invoices', 'finance'],

        // Marketing
        ['marketing.view', 'View Marketing', 'marketing'],
        ['campaigns.manage', 'Manage Campaigns', 'marketing'],
        ['reviews.moderate', 'Moderate Reviews', 'marketing'],

        // Reports
        ['reports.view', 'View Reports', 'reports'],

        // Administration
        ['admin_users.view', 'View Admin Users', 'administration'],
        ['admin_users.manage', 'Manage Admin Users', 'administration'],
        ['roles.manage', 'Manage Roles', 'administration'],

        // Shop Settings
        ['shop.settings', 'Manage Shop Settings', 'shop'],
        ['shop.design', 'Manage Design', 'shop'],
        ['cms.manage', 'Manage CMS Pages', 'shop'],
        ['navigation.manage', 'Manage Navigation', 'shop'],

        // System
        ['system.settings', 'System Settings', 'system'],
        ['system.logs', 'View Logs', 'system'],
        ['system.backups', 'Manage Backups', 'system'],

        // Developer
        ['developer.api', 'API Access', 'developer'],
        ['developer.webhooks', 'Manage Webhooks', 'developer'],
        ['developer.debug', 'Debug Mode', 'developer'],
    ];

    $permissionIds = [];
    foreach ($permissions as $perm) {
        $existing = Database::fetch("SELECT id FROM permissions WHERE key_name = ?", [$perm[0]]);
        if (!$existing) {
            $permId = Database::insert('permissions', [
                'key_name' => $perm[0],
                'display_name' => $perm[1],
                'permission_group' => $perm[2],
            ]);
            $permissionIds[$perm[0]] = $permId;
        }
        else {
            $permissionIds[$perm[0]] = $existing['id'];
        }
    }
    echo "   ✓ Created/verified " . count($permissions) . " permissions\n";

    // =========================================
    // 6. Create Default Roles
    // =========================================
    echo "6. Creating default roles...\n";

    // Super Admin Role
    $existingSuperAdmin = Database::fetch("SELECT id FROM roles WHERE shop_id = ? AND name = 'Super Admin'", [$shopId]);

    if (!$existingSuperAdmin) {
        $superAdminRoleId = Database::insert('roles', [
            'shop_id' => $shopId,
            'name' => 'Super Admin',
            'description' => 'Full access to all features',
            'is_system' => 1,
        ]);

        // Assign all permissions to Super Admin
        foreach ($permissionIds as $permId) {
            Database::insert('role_permissions', [
                'role_id' => $superAdminRoleId,
                'permission_id' => $permId,
            ]);
        }
        echo "   ✓ Created Super Admin role with all permissions\n";
    }
    else {
        $superAdminRoleId = $existingSuperAdmin['id'];
        echo "   ⊘ Super Admin role already exists\n";
    }

    // Editor Role
    $existingEditor = Database::fetch("SELECT id FROM roles WHERE shop_id = ? AND name = 'Editor'", [$shopId]);

    if (!$existingEditor) {
        $editorRoleId = Database::insert('roles', [
            'shop_id' => $shopId,
            'name' => 'Editor',
            'description' => 'Can manage catalog and content',
            'is_system' => 0,
        ]);

        $editorPerms = ['dashboard.view', 'products.view', 'products.create', 'products.edit',
            'categories.view', 'categories.create', 'categories.edit',
            'cms.manage', 'navigation.manage'];
        foreach ($editorPerms as $permKey) {
            if (isset($permissionIds[$permKey])) {
                Database::insert('role_permissions', [
                    'role_id' => $editorRoleId,
                    'permission_id' => $permissionIds[$permKey],
                ]);
            }
        }
        echo "   ✓ Created Editor role\n";
    }
    else {
        echo "   ⊘ Editor role already exists\n";
    }

    // =========================================
    // 7. Create Default Admin User
    // =========================================
    echo "7. Creating default admin user...\n";

    $existingAdmin = Database::fetch("SELECT id FROM admin_users WHERE email = ?", [$config['admin_email']]);

    if (!$existingAdmin) {
        $adminId = Database::insert('admin_users', [
            'shop_id' => $shopId,
            'email' => $config['admin_email'],
            'password' => password_hash($config['admin_password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'name' => $config['admin_name'],
            'is_active' => 1,
            'locale' => 'de_DE',
            'timezone' => 'Europe/Berlin',
            'dark_mode' => 0,
        ]);

        // Assign Super Admin role
        Database::insert('admin_user_roles', [
            'admin_user_id' => $adminId,
            'role_id' => $superAdminRoleId,
        ]);

        echo "   ✓ Created admin user: {$config['admin_email']}\n";
    }
    else {
        echo "   ⊘ Admin user already exists\n";
    }

    // =========================================
    // 8. Create Default Customer Groups
    // =========================================
    echo "8. Creating default customer groups...\n";

    $groups = [
        ['general', 'Allgemein', 'Standard-Kundengruppe', 0.00, 1],
        ['wholesale', 'Großhandel', 'Großhandelskunden mit Rabatt', 10.00, 0],
        ['vip', 'VIP', 'VIP-Kunden mit Sonderkonditionen', 15.00, 0],
    ];

    foreach ($groups as $group) {
        $existing = Database::fetch("SELECT id FROM customer_groups WHERE shop_id = ? AND code = ?", [$shopId, $group[0]]);
        if (!$existing) {
            Database::insert('customer_groups', [
                'shop_id' => $shopId,
                'code' => $group[0],
                'name' => $group[1],
                'description' => $group[2],
                'discount_percent' => $group[3],
                'is_default' => $group[4],
            ]);
        }
    }
    echo "   ✓ Created/verified " . count($groups) . " customer groups\n";

    // =========================================
    // 9. Create Default Tax Classes
    // =========================================
    echo "9. Creating default tax classes...\n";

    $taxClasses = [
        ['standard', 'Standard', 1],
        ['reduced', 'Ermäßigt', 0],
        ['zero', 'Steuerfrei', 0],
    ];

    foreach ($taxClasses as $tc) {
        $existing = Database::fetch("SELECT id FROM tax_classes WHERE shop_id = ? AND code = ?", [$shopId, $tc[0]]);
        if (!$existing) {
            Database::insert('tax_classes', [
                'shop_id' => $shopId,
                'code' => $tc[0],
                'name' => $tc[1],
                'is_default' => $tc[2],
            ]);
        }
    }
    echo "   ✓ Created/verified " . count($taxClasses) . " tax classes\n";

    // =========================================
    // 10. Create Default Navigation Menus
    // =========================================
    echo "10. Creating default navigation menus...\n";

    $menus = [
        ['main', 'Hauptmenü'],
        ['footer', 'Footer-Menü'],
        ['mobile', 'Mobile Navigation'],
    ];

    foreach ($menus as $menu) {
        $existing = Database::fetch("SELECT id FROM navigation_menus WHERE shop_id = ? AND code = ?", [$shopId, $menu[0]]);
        if (!$existing) {
            Database::insert('navigation_menus', [
                'shop_id' => $shopId,
                'code' => $menu[0],
                'name' => $menu[1],
                'is_active' => 1,
            ]);
        }
    }
    echo "   ✓ Created/verified " . count($menus) . " navigation menus\n";

    Database::commit();

    echo "\n=== Seeding Complete ===\n";
    echo "\nLogin credentials:\n";
    echo "  Email:    {$config['admin_email']}\n";
    echo "  Password: {$config['admin_password']}\n";
    echo "\n⚠️  Please change the password after first login!\n";


}
catch (Exception $e) {
    Database::rollback();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
