<?php
/**
 * ============================================
 * CUSTOMERS API
 * ============================================
 * Complete CRUD operations for customer management
 * 100% functional with database integration
 * ============================================
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../admin/config.php';
require_once __DIR__ . '/../../admin/includes/Database.php';

Database::configure($database);

// Auto-migration: Add admin_notes column if not exists
try {
    $columns = Database::fetchAll("SHOW COLUMNS FROM customers LIKE 'admin_notes'");
    if (empty($columns)) {
        Database::query("ALTER TABLE customers ADD COLUMN admin_notes TEXT DEFAULT NULL AFTER loyalty_points");
    }
} catch (Exception $e) {
    // Ignore - column might already exist
}

// Ensure customer_groups table exists and has default entries
try {
    Database::query("
        CREATE TABLE IF NOT EXISTS customer_groups (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(100) NOT NULL,
            code VARCHAR(50) NOT NULL,
            description TEXT,
            discount_percent DECIMAL(5,2) DEFAULT 0.00,
            free_shipping TINYINT(1) DEFAULT 0,
            priority_support TINYINT(1) DEFAULT 0,
            early_access TINYINT(1) DEFAULT 0,
            auto_assign_type ENUM('disabled', 'min_spent', 'min_orders') DEFAULT 'disabled',
            auto_assign_threshold DECIMAL(12,2) DEFAULT 0.00,
            is_default TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_shop_id (shop_id)
        )
    ");

    // Add new columns if they don't exist (for existing installations)
    try {
        Database::query("ALTER TABLE customer_groups ADD COLUMN free_shipping TINYINT(1) DEFAULT 0");
        Database::query("ALTER TABLE customer_groups ADD COLUMN priority_support TINYINT(1) DEFAULT 0");
        Database::query("ALTER TABLE customer_groups ADD COLUMN early_access TINYINT(1) DEFAULT 0");
        Database::query("ALTER TABLE customer_groups ADD COLUMN auto_assign_type ENUM('disabled', 'min_spent', 'min_orders') DEFAULT 'disabled'");
        Database::query("ALTER TABLE customer_groups ADD COLUMN auto_assign_threshold DECIMAL(12,2) DEFAULT 0.00");
    } catch (Exception $e) {
        // Columns may already exist
    }

    // Insert default groups if none exist
    $existingGroups = Database::fetch("SELECT id FROM customer_groups WHERE shop_id = 1 LIMIT 1");
    if (!$existingGroups) {
        Database::insert('customer_groups', ['shop_id' => 1, 'name' => 'Allgemein', 'code' => 'general', 'description' => 'Standardgruppe für alle neuen Kunden', 'is_default' => 1]);
        Database::insert('customer_groups', ['shop_id' => 1, 'name' => 'VIP', 'code' => 'vip', 'description' => 'Treue Kunden mit hohem Bestellwert', 'discount_percent' => 10, 'free_shipping' => 1, 'priority_support' => 1, 'auto_assign_type' => 'min_spent', 'auto_assign_threshold' => 1000]);
        Database::insert('customer_groups', ['shop_id' => 1, 'name' => 'Großhandel', 'code' => 'wholesale', 'description' => 'B2B-Kunden und Wiederverkäufer', 'discount_percent' => 20, 'free_shipping' => 1]);
    }
} catch (Exception $e) {
    // Ignore migration errors
}

// Create customer_activity_log table for tracking customer activities
try {
    Database::query("
        CREATE TABLE IF NOT EXISTS customer_activity_log (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            shop_id BIGINT UNSIGNED NOT NULL,
            customer_id BIGINT UNSIGNED,
            activity_type ENUM('login', 'logout', 'order', 'cart_add', 'cart_remove', 'profile_update', 'password_reset', 'support_ticket', 'registration', 'newsletter_subscribe', 'newsletter_unsubscribe', 'review', 'wishlist', 'other') NOT NULL,
            description TEXT,
            metadata JSON,
            ip_address VARCHAR(45),
            user_agent VARCHAR(500),
            browser VARCHAR(100),
            os VARCHAR(100),
            device_type ENUM('desktop', 'mobile', 'tablet', 'unknown') DEFAULT 'unknown',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_shop_customer (shop_id, customer_id),
            INDEX idx_activity_type (activity_type),
            INDEX idx_created_at (created_at),
            INDEX idx_shop_created (shop_id, created_at DESC)
        )
    ");
} catch (Exception $e) {
    // Ignore - table may already exist
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

try {
    switch ($action) {
        case 'get_stats':
            handleGetStats($shopId);
            break;
        case 'get_customers':
            handleGetCustomers($shopId);
            break;
        case 'get_customer':
            handleGetCustomer($shopId);
            break;
        case 'get_groups':
            handleGetGroups($shopId);
            break;
        case 'get_all_groups':
            handleGetAllGroups($shopId);
            break;
        case 'get_group':
            handleGetGroup($shopId);
            break;
        case 'get_group_stats':
            handleGetGroupStats($shopId);
            break;
        case 'create_group':
            handleCreateGroup($shopId);
            break;
        case 'update_group':
            handleUpdateGroup($shopId);
            break;
        case 'delete_group':
            handleDeleteGroup($shopId);
            break;
        // Activity Log Actions
        case 'get_activity_log':
            handleGetActivityLog($shopId);
            break;
        case 'get_activity_stats':
            handleGetActivityStats($shopId);
            break;
        case 'log_activity':
            handleLogActivity($shopId);
            break;
        case 'export_activity':
            handleExportActivity($shopId);
            break;
        case 'update_customer':
            handleUpdateCustomer($shopId);
            break;
        case 'toggle_newsletter':
            handleToggleNewsletter($shopId);
            break;
        case 'update_notes':
            handleUpdateNotes($shopId);
            break;
        case 'toggle_status':
            handleToggleStatus($shopId);
            break;
        case 'delete_customer':
            handleDeleteCustomer($shopId);
            break;
        case 'export':
            handleExport($shopId);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// =====================================================================
// GET STATS - KPI Cards
// =====================================================================
function handleGetStats(int $shopId): void
{
    // Total customers
    $total = Database::fetch(
        "SELECT COUNT(*) as count FROM customers WHERE shop_id = ?",
        [$shopId]
    );

    // New this month
    $newThisMonth = Database::fetch(
        "SELECT COUNT(*) as count FROM customers WHERE shop_id = ? AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')",
        [$shopId]
    );

    // New last month (for comparison)
    $newLastMonth = Database::fetch(
        "SELECT COUNT(*) as count FROM customers WHERE shop_id = ? 
         AND created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01')
         AND created_at < DATE_FORMAT(NOW(), '%Y-%m-01')",
        [$shopId]
    );

    // Active customers (with orders OR logged in recently)
    $active = Database::fetch(
        "SELECT COUNT(*) as count FROM customers WHERE shop_id = ? AND is_active = 1 
         AND (last_order_at >= DATE_SUB(NOW(), INTERVAL 90 DAY) OR last_login_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) OR orders_count > 0)",
        [$shopId]
    );

    // Average customer value (total_spent / customers with orders)
    $avgCustomerValue = Database::fetch(
        "SELECT COALESCE(AVG(total_spent), 0) as avg FROM customers WHERE shop_id = ? AND orders_count > 0",
        [$shopId]
    );

    // Repeat purchase rate (customers with >1 order / total customers with orders)
    $repeatStats = Database::fetch(
        "SELECT 
            COUNT(DISTINCT CASE WHEN orders_count > 1 THEN id END) as repeat_customers,
            COUNT(DISTINCT CASE WHEN orders_count >= 1 THEN id END) as total_customers_with_orders
         FROM customers WHERE shop_id = ?",
        [$shopId]
    );

    $repeatRate = ($repeatStats['total_customers_with_orders'] ?? 0) > 0
        ? round(($repeatStats['repeat_customers'] ?? 0) / $repeatStats['total_customers_with_orders'] * 100)
        : 0;

    $monthlyChange = ($newThisMonth['count'] ?? 0) - ($newLastMonth['count'] ?? 0);

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_customers' => (int) ($total['count'] ?? 0),
            'new_this_month' => (int) ($newThisMonth['count'] ?? 0),
            'monthly_change' => $monthlyChange,
            'active_customers' => (int) ($active['count'] ?? 0),
            'avg_customer_value' => round((float) ($avgCustomerValue['avg'] ?? 0), 2),
            'repeat_rate' => $repeatRate
        ]
    ]);
}

// =====================================================================
// GET CUSTOMERS - List with search, filter, sort, pagination
// =====================================================================
function handleGetCustomers(int $shopId): void
{
    $search = trim($_GET['search'] ?? '');
    $groupId = (int) ($_GET['group_id'] ?? 0);
    $status = $_GET['status'] ?? '';
    $sortBy = $_GET['sort_by'] ?? 'created_at';
    $sortDir = strtoupper($_GET['sort_dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(100, max(10, (int) ($_GET['per_page'] ?? 25)));

    $where = ["c.shop_id = ?"];
    $params = [$shopId];

    if ($search) {
        $where[] = "(c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR CONCAT(c.first_name, ' ', c.last_name) LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }

    if ($groupId > 0) {
        $where[] = "c.customer_group_id = ?";
        $params[] = $groupId;
    }

    if ($status === 'active') {
        $where[] = "c.is_active = 1";
    } elseif ($status === 'inactive') {
        $where[] = "c.is_active = 0";
    }

    $whereClause = implode(' AND ', $where);

    // Allowed sort columns
    $allowedSorts = ['first_name', 'last_name', 'email', 'orders_count', 'total_spent', 'created_at', 'last_order_at'];
    if (!in_array($sortBy, $allowedSorts)) {
        $sortBy = 'created_at';
    }

    // Count total
    $countResult = Database::fetch(
        "SELECT COUNT(*) as total FROM customers c WHERE $whereClause",
        $params
    );
    $total = (int) ($countResult['total'] ?? 0);

    $offset = ($page - 1) * $perPage;

    // Get customers with group name
    $customers = Database::fetchAll("
        SELECT 
            c.id, c.email, c.first_name, c.last_name, c.phone,
            c.customer_group_id, c.is_active, c.is_verified,
            c.subscribed_to_newsletter, c.orders_count, c.total_spent,
            c.last_login_at, c.last_order_at, c.created_at,
            c.admin_notes,
            g.name as group_name, g.code as group_code
        FROM customers c
        LEFT JOIN customer_groups g ON c.customer_group_id = g.id
        WHERE $whereClause
        ORDER BY c.$sortBy $sortDir
        LIMIT $perPage OFFSET $offset
    ", $params);

    // Format customers
    foreach ($customers as &$customer) {
        $customer['full_name'] = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''));
        if (empty($customer['full_name'])) {
            $customer['full_name'] = explode('@', $customer['email'])[0];
        }
        $customer['total_spent'] = (float) $customer['total_spent'];
        $customer['orders_count'] = (int) $customer['orders_count'];
    }

    echo json_encode([
        'success' => true,
        'customers' => $customers,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ]
    ]);
}

// =====================================================================
// GET CUSTOMER - Single customer with details
// =====================================================================
function handleGetCustomer(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Kunden-ID']);
        return;
    }

    $customer = Database::fetch("
        SELECT 
            c.*,
            g.name as group_name, g.code as group_code
        FROM customers c
        LEFT JOIN customer_groups g ON c.customer_group_id = g.id
        WHERE c.id = ? AND c.shop_id = ?
    ", [$id, $shopId]);

    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Kunde nicht gefunden']);
        return;
    }

    // Get addresses
    $addresses = Database::fetchAll(
        "SELECT * FROM customer_addresses WHERE customer_id = ? ORDER BY is_default_billing DESC, is_default_shipping DESC",
        [$id]
    );

    // Get orders
    $orders = Database::fetchAll("
        SELECT id, order_number, status, payment_status, grand_total, currency_code, created_at
        FROM orders
        WHERE customer_id = ?
        ORDER BY created_at DESC
        LIMIT 50
    ", [$id]);

    $customer['full_name'] = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''));
    $customer['addresses'] = $addresses;
    $customer['orders'] = $orders;

    echo json_encode([
        'success' => true,
        'customer' => $customer
    ]);
}

// =====================================================================
// GET GROUPS - Customer groups
// =====================================================================
function handleGetGroups(int $shopId): void
{
    $groups = Database::fetchAll(
        "SELECT id, name, code, discount_percent, is_default FROM customer_groups WHERE shop_id = ? ORDER BY is_default DESC, name",
        [$shopId]
    );

    echo json_encode([
        'success' => true,
        'groups' => $groups
    ]);
}

// =====================================================================
// UPDATE CUSTOMER - Edit customer data
// =====================================================================
function handleUpdateCustomer(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Kunden-ID']);
        return;
    }

    // Verify customer belongs to shop
    $customer = Database::fetch("SELECT id FROM customers WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Kunde nicht gefunden']);
        return;
    }

    $updates = [];

    if (isset($_POST['first_name'])) {
        $updates['first_name'] = trim($_POST['first_name']);
    }
    if (isset($_POST['last_name'])) {
        $updates['last_name'] = trim($_POST['last_name']);
    }
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Ungültige E-Mail-Adresse']);
            return;
        }
        // Check if email is unique
        $existing = Database::fetch(
            "SELECT id FROM customers WHERE email = ? AND shop_id = ? AND id != ?",
            [$email, $shopId, $id]
        );
        if ($existing) {
            echo json_encode(['success' => false, 'error' => 'Diese E-Mail-Adresse wird bereits verwendet']);
            return;
        }
        $updates['email'] = $email;
    }
    if (isset($_POST['phone'])) {
        $updates['phone'] = trim($_POST['phone']);
    }
    if (isset($_POST['customer_group_id'])) {
        $updates['customer_group_id'] = (int) $_POST['customer_group_id'] ?: null;
    }
    if (isset($_POST['is_active'])) {
        $updates['is_active'] = $_POST['is_active'] ? 1 : 0;
    }
    if (isset($_POST['subscribed_to_newsletter'])) {
        $updates['subscribed_to_newsletter'] = $_POST['subscribed_to_newsletter'] ? 1 : 0;
    }

    if (empty($updates)) {
        echo json_encode(['success' => false, 'error' => 'Keine Änderungen']);
        return;
    }

    $updates['updated_at'] = date('Y-m-d H:i:s');

    Database::update('customers', $updates, 'id = ?', [$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Kunde aktualisiert'
    ]);
}

// =====================================================================
// TOGGLE NEWSLETTER
// =====================================================================
function handleToggleNewsletter(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Kunden-ID']);
        return;
    }

    $customer = Database::fetch(
        "SELECT subscribed_to_newsletter FROM customers WHERE id = ? AND shop_id = ?",
        [$id, $shopId]
    );

    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Kunde nicht gefunden']);
        return;
    }

    $newValue = $customer['subscribed_to_newsletter'] ? 0 : 1;

    Database::update('customers', [
        'subscribed_to_newsletter' => $newValue,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$id]);

    echo json_encode([
        'success' => true,
        'subscribed' => (bool) $newValue,
        'message' => $newValue ? 'Newsletter abonniert' : 'Newsletter abbestellt'
    ]);
}

// =====================================================================
// UPDATE NOTES
// =====================================================================
function handleUpdateNotes(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Kunden-ID']);
        return;
    }

    $customer = Database::fetch("SELECT id FROM customers WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Kunde nicht gefunden']);
        return;
    }

    Database::update('customers', [
        'admin_notes' => $notes,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Notizen gespeichert'
    ]);
}

// =====================================================================
// TOGGLE STATUS (Active/Blocked)
// =====================================================================
function handleToggleStatus(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Kunden-ID']);
        return;
    }

    $customer = Database::fetch(
        "SELECT is_active FROM customers WHERE id = ? AND shop_id = ?",
        [$id, $shopId]
    );

    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Kunde nicht gefunden']);
        return;
    }

    $newValue = $customer['is_active'] ? 0 : 1;

    Database::update('customers', [
        'is_active' => $newValue,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$id]);

    echo json_encode([
        'success' => true,
        'is_active' => (bool) $newValue,
        'message' => $newValue ? 'Kunde aktiviert' : 'Kunde gesperrt'
    ]);
}

// =====================================================================
// DELETE CUSTOMER
// =====================================================================
function handleDeleteCustomer(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Kunden-ID']);
        return;
    }

    $customer = Database::fetch("SELECT id, email FROM customers WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Kunde nicht gefunden']);
        return;
    }

    // Delete addresses first (FK constraint)
    Database::delete('customer_addresses', 'customer_id = ?', [$id]);

    // Delete customer
    Database::delete('customers', 'id = ? AND shop_id = ?', [$id, $shopId]);

    echo json_encode([
        'success' => true,
        'message' => 'Kunde gelöscht'
    ]);
}

// =====================================================================
// EXPORT - JSON or SQL
// =====================================================================
function handleExport(int $shopId): void
{
    $format = strtolower($_GET['format'] ?? 'json');
    $ids = isset($_GET['ids']) ? array_map('intval', explode(',', $_GET['ids'])) : [];

    $where = "c.shop_id = ?";
    $params = [$shopId];

    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $where .= " AND c.id IN ($placeholders)";
        $params = array_merge($params, $ids);
    }

    $customers = Database::fetchAll("
        SELECT 
            c.id, c.email, c.first_name, c.last_name, c.phone, c.company_name,
            c.is_active, c.is_verified, c.subscribed_to_newsletter,
            c.orders_count, c.total_spent, c.loyalty_points,
            c.preferred_locale, c.preferred_currency,
            c.created_at, c.last_login_at, c.last_order_at,
            g.name as group_name
        FROM customers c
        LEFT JOIN customer_groups g ON c.customer_group_id = g.id
        WHERE $where
        ORDER BY c.id
    ", $params);

    if ($format === 'sql') {
        // Generate SQL INSERT statements
        header('Content-Type: text/sql');
        header('Content-Disposition: attachment; filename="customers_export_' . date('Y-m-d') . '.sql"');

        echo "-- Customers Export " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($customers as $c) {
            $values = [
                "'" . addslashes($c['email']) . "'",
                $c['first_name'] ? "'" . addslashes($c['first_name']) . "'" : 'NULL',
                $c['last_name'] ? "'" . addslashes($c['last_name']) . "'" : 'NULL',
                $c['phone'] ? "'" . addslashes($c['phone']) . "'" : 'NULL',
                $c['is_active'],
                $c['subscribed_to_newsletter'],
                $c['orders_count'],
                $c['total_spent'],
                "'" . $c['created_at'] . "'"
            ];
            echo "INSERT INTO customers (email, first_name, last_name, phone, is_active, subscribed_to_newsletter, orders_count, total_spent, created_at) VALUES (" . implode(', ', $values) . ");\n";
        }
        exit;
    }

    // JSON export
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="customers_export_' . date('Y-m-d') . '.json"');

    echo json_encode([
        'exported_at' => date('Y-m-d H:i:s'),
        'total_count' => count($customers),
        'customers' => $customers
    ], JSON_PRETTY_PRINT);
    exit;
}

// =====================================================================
// GROUP MANAGEMENT FUNCTIONS
// =====================================================================

/**
 * Get all groups with full details and member counts
 */
function handleGetAllGroups(int $shopId): void
{
    $groups = Database::fetchAll("
        SELECT 
            g.*,
            (SELECT COUNT(*) FROM customers c WHERE c.customer_group_id = g.id AND c.shop_id = ?) as member_count,
            (SELECT COALESCE(SUM(c.total_spent), 0) FROM customers c WHERE c.customer_group_id = g.id AND c.shop_id = ?) as total_revenue,
            (SELECT COALESCE(AVG(c.total_spent), 0) FROM customers c WHERE c.customer_group_id = g.id AND c.shop_id = ? AND c.orders_count > 0) as avg_customer_value
        FROM customer_groups g
        WHERE g.shop_id = ?
        ORDER BY g.is_default DESC, g.name ASC
    ", [$shopId, $shopId, $shopId, $shopId]);

    echo json_encode([
        'success' => true,
        'groups' => $groups
    ]);
}

/**
 * Get single group with details
 */
function handleGetGroup(int $shopId): void
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Gruppen-ID']);
        return;
    }

    $group = Database::fetch("
        SELECT g.*,
            (SELECT COUNT(*) FROM customers c WHERE c.customer_group_id = g.id AND c.shop_id = ?) as member_count,
            (SELECT COALESCE(SUM(c.total_spent), 0) FROM customers c WHERE c.customer_group_id = g.id AND c.shop_id = ?) as total_revenue,
            (SELECT COALESCE(AVG(c.total_spent), 0) FROM customers c WHERE c.customer_group_id = g.id AND c.shop_id = ? AND c.orders_count > 0) as avg_customer_value,
            (SELECT COALESCE(AVG(c.orders_count), 0) FROM customers c WHERE c.customer_group_id = g.id AND c.shop_id = ?) as avg_orders_per_customer
        FROM customer_groups g
        WHERE g.id = ? AND g.shop_id = ?
    ", [$shopId, $shopId, $shopId, $shopId, $id, $shopId]);

    if (!$group) {
        echo json_encode(['success' => false, 'error' => 'Gruppe nicht gefunden']);
        return;
    }

    // Get members
    $members = Database::fetchAll("
        SELECT id, first_name, last_name, email, orders_count, total_spent, is_active, created_at
        FROM customers
        WHERE customer_group_id = ? AND shop_id = ?
        ORDER BY total_spent DESC
        LIMIT 50
    ", [$id, $shopId]);

    $group['members'] = $members;

    echo json_encode([
        'success' => true,
        'group' => $group
    ]);
}

/**
 * Get group statistics for KPI cards
 */
function handleGetGroupStats(int $shopId): void
{
    $totalGroups = Database::fetch("SELECT COUNT(*) as count FROM customer_groups WHERE shop_id = ?", [$shopId]);

    $totalCustomersInGroups = Database::fetch("
        SELECT COUNT(*) as count FROM customers WHERE shop_id = ? AND customer_group_id IS NOT NULL
    ", [$shopId]);

    $totalRevenue = Database::fetch("
        SELECT COALESCE(SUM(c.total_spent), 0) as total 
        FROM customers c 
        WHERE c.shop_id = ?
    ", [$shopId]);

    $avgDiscount = Database::fetch("
        SELECT AVG(discount_percent) as avg FROM customer_groups WHERE shop_id = ? AND discount_percent > 0
    ", [$shopId]);

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_groups' => (int) $totalGroups['count'],
            'customers_in_groups' => (int) $totalCustomersInGroups['count'],
            'total_revenue' => (float) $totalRevenue['total'],
            'avg_discount' => round((float) ($avgDiscount['avg'] ?? 0), 1)
        ]
    ]);
}

/**
 * Create a new customer group
 */
function handleCreateGroup(int $shopId): void
{
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $discountPercent = (float) ($_POST['discount_percent'] ?? 0);
    $freeShipping = (int) ($_POST['free_shipping'] ?? 0);
    $prioritySupport = (int) ($_POST['priority_support'] ?? 0);
    $earlyAccess = (int) ($_POST['early_access'] ?? 0);
    $autoAssignType = $_POST['auto_assign_type'] ?? 'disabled';
    $autoAssignThreshold = (float) ($_POST['auto_assign_threshold'] ?? 0);

    if (empty($name)) {
        echo json_encode(['success' => false, 'error' => 'Gruppenname ist erforderlich']);
        return;
    }

    // Auto-generate code from name if not provided
    if (empty($code)) {
        $code = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $name));
        $code = preg_replace('/_+/', '_', $code);
        $code = trim($code, '_');
    }

    // Check for duplicate code
    $existing = Database::fetch("SELECT id FROM customer_groups WHERE code = ? AND shop_id = ?", [$code, $shopId]);
    if ($existing) {
        $code = $code . '_' . time();
    }

    // Validate auto_assign_type
    if (!in_array($autoAssignType, ['disabled', 'min_spent', 'min_orders'])) {
        $autoAssignType = 'disabled';
    }

    $id = Database::insert('customer_groups', [
        'shop_id' => $shopId,
        'name' => $name,
        'code' => $code,
        'description' => $description,
        'discount_percent' => $discountPercent,
        'free_shipping' => $freeShipping,
        'priority_support' => $prioritySupport,
        'early_access' => $earlyAccess,
        'auto_assign_type' => $autoAssignType,
        'auto_assign_threshold' => $autoAssignThreshold,
        'is_default' => 0
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Gruppe erstellt',
        'group_id' => $id
    ]);
}

/**
 * Update an existing customer group
 */
function handleUpdateGroup(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $discountPercent = (float) ($_POST['discount_percent'] ?? 0);
    $freeShipping = (int) ($_POST['free_shipping'] ?? 0);
    $prioritySupport = (int) ($_POST['priority_support'] ?? 0);
    $earlyAccess = (int) ($_POST['early_access'] ?? 0);
    $autoAssignType = $_POST['auto_assign_type'] ?? 'disabled';
    $autoAssignThreshold = (float) ($_POST['auto_assign_threshold'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Gruppen-ID']);
        return;
    }

    if (empty($name)) {
        echo json_encode(['success' => false, 'error' => 'Gruppenname ist erforderlich']);
        return;
    }

    // Verify group exists
    $group = Database::fetch("SELECT id, is_default FROM customer_groups WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if (!$group) {
        echo json_encode(['success' => false, 'error' => 'Gruppe nicht gefunden']);
        return;
    }

    // Check for duplicate code (excluding current group)
    $existing = Database::fetch("SELECT id FROM customer_groups WHERE code = ? AND shop_id = ? AND id != ?", [$code, $shopId, $id]);
    if ($existing) {
        echo json_encode(['success' => false, 'error' => 'Der Gruppen-Code existiert bereits']);
        return;
    }

    // Validate auto_assign_type
    if (!in_array($autoAssignType, ['disabled', 'min_spent', 'min_orders'])) {
        $autoAssignType = 'disabled';
    }

    Database::update('customer_groups', [
        'name' => $name,
        'code' => $code,
        'description' => $description,
        'discount_percent' => $discountPercent,
        'free_shipping' => $freeShipping,
        'priority_support' => $prioritySupport,
        'early_access' => $earlyAccess,
        'auto_assign_type' => $autoAssignType,
        'auto_assign_threshold' => $autoAssignThreshold,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$id]);

    // Run auto-assignment if enabled
    if ($autoAssignType !== 'disabled' && $autoAssignThreshold > 0) {
        runAutoAssignment($shopId, $id, $autoAssignType, $autoAssignThreshold);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Gruppe aktualisiert'
    ]);
}

/**
 * Delete a customer group
 */
function handleDeleteGroup(int $shopId): void
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Ungültige Gruppen-ID']);
        return;
    }

    // Verify group exists and is not default
    $group = Database::fetch("SELECT id, is_default, name FROM customer_groups WHERE id = ? AND shop_id = ?", [$id, $shopId]);
    if (!$group) {
        echo json_encode(['success' => false, 'error' => 'Gruppe nicht gefunden']);
        return;
    }

    if ($group['is_default']) {
        echo json_encode(['success' => false, 'error' => 'Die Standardgruppe kann nicht gelöscht werden']);
        return;
    }

    // Get default group to reassign customers
    $defaultGroup = Database::fetch("SELECT id FROM customer_groups WHERE shop_id = ? AND is_default = 1", [$shopId]);
    $defaultGroupId = $defaultGroup ? $defaultGroup['id'] : null;

    // Move customers to default group
    if ($defaultGroupId) {
        Database::query("UPDATE customers SET customer_group_id = ? WHERE customer_group_id = ? AND shop_id = ?", [$defaultGroupId, $id, $shopId]);
    }

    // Delete the group
    Database::query("DELETE FROM customer_groups WHERE id = ? AND shop_id = ?", [$id, $shopId]);

    echo json_encode([
        'success' => true,
        'message' => 'Gruppe "' . $group['name'] . '" gelöscht. Kunden wurden zur Standardgruppe verschoben.'
    ]);
}

/**
 * Run auto-assignment for a specific group
 */
function runAutoAssignment(int $shopId, int $groupId, string $type, float $threshold): void
{
    if ($type === 'min_spent') {
        // Assign customers who have spent more than threshold
        Database::query("
            UPDATE customers 
            SET customer_group_id = ? 
            WHERE shop_id = ? 
            AND total_spent >= ? 
            AND (customer_group_id IS NULL OR customer_group_id != ?)
        ", [$groupId, $shopId, $threshold, $groupId]);
    } elseif ($type === 'min_orders') {
        // Assign customers who have more orders than threshold
        Database::query("
            UPDATE customers 
            SET customer_group_id = ? 
            WHERE shop_id = ? 
            AND orders_count >= ? 
            AND (customer_group_id IS NULL OR customer_group_id != ?)
        ", [$groupId, $shopId, (int) $threshold, $groupId]);
    }
}

// =====================================================================
// ACTIVITY LOG FUNCTIONS
// =====================================================================

/**
 * Get activity log with filtering and pagination
 */
function handleGetActivityLog(int $shopId): void
{
    $limit = (int) ($_GET['limit'] ?? 50);
    $offset = (int) ($_GET['offset'] ?? 0);
    $type = $_GET['type'] ?? 'all';
    $period = $_GET['period'] ?? '7d';
    $search = trim($_GET['search'] ?? '');
    $customerId = (int) ($_GET['customer_id'] ?? 0);

    // Limit to prevent abuse
    $limit = min($limit, 100);

    // Build WHERE conditions
    $where = ["a.shop_id = ?"];
    $params = [$shopId];

    // Type filter
    if ($type !== 'all') {
        $where[] = "a.activity_type = ?";
        $params[] = $type;
    }

    // Period filter
    $periodMap = [
        'today' => 'CURDATE()',
        '7d' => 'DATE_SUB(NOW(), INTERVAL 7 DAY)',
        '30d' => 'DATE_SUB(NOW(), INTERVAL 30 DAY)',
        'year' => 'DATE_SUB(NOW(), INTERVAL 1 YEAR)',
        'all' => null
    ];

    if (isset($periodMap[$period]) && $periodMap[$period]) {
        $where[] = "a.created_at >= " . $periodMap[$period];
    }

    // Customer filter
    if ($customerId > 0) {
        $where[] = "a.customer_id = ?";
        $params[] = $customerId;
    }

    // Search filter
    if (!empty($search)) {
        // Special case: searching for "Gast" should find activities with no customer OR empty customer names
        $searchLower = strtolower($search);
        if (strpos($searchLower, 'gast') !== false || strpos($searchLower, 'guest') !== false) {
            $where[] = "(c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR a.description LIKE ? OR a.customer_id IS NULL OR (c.first_name IS NULL AND c.last_name IS NULL) OR (c.first_name = '' AND c.last_name = ''))";
        } else {
            $where[] = "(c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR a.description LIKE ?)";
        }
        $searchTerm = "%$search%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }

    $whereClause = implode(' AND ', $where);

    // Get total count
    $total = Database::fetch("
        SELECT COUNT(*) as count 
        FROM customer_activity_log a
        LEFT JOIN customers c ON a.customer_id = c.id
        WHERE $whereClause
    ", $params);

    // Get activities
    $activities = Database::fetchAll("
        SELECT 
            a.id, a.customer_id, a.activity_type, a.description, a.metadata,
            a.ip_address, a.user_agent, a.browser, a.os, a.device_type, a.created_at,
            c.first_name, c.last_name, c.email
        FROM customer_activity_log a
        LEFT JOIN customers c ON a.customer_id = c.id
        WHERE $whereClause
        ORDER BY a.created_at DESC
        LIMIT ? OFFSET ?
    ", array_merge($params, [$limit, $offset]));

    // Parse metadata JSON
    foreach ($activities as &$activity) {
        if (!empty($activity['metadata'])) {
            $activity['metadata'] = json_decode($activity['metadata'], true);
        }
    }

    echo json_encode([
        'success' => true,
        'activities' => $activities,
        'total' => (int) $total['count'],
        'limit' => $limit,
        'offset' => $offset,
        'has_more' => ($offset + $limit) < (int) $total['count']
    ]);
}

/**
 * Get activity statistics
 */
function handleGetActivityStats(int $shopId): void
{
    $period = $_GET['period'] ?? '7d';

    $periodMap = [
        '7d' => 'DATE_SUB(NOW(), INTERVAL 7 DAY)',
        '30d' => 'DATE_SUB(NOW(), INTERVAL 30 DAY)',
        'year' => 'DATE_SUB(NOW(), INTERVAL 1 YEAR)',
        'all' => '1970-01-01'
    ];

    $dateFilter = $periodMap[$period] ?? $periodMap['7d'];

    // Total activities
    $total = Database::fetch("
        SELECT COUNT(*) as count FROM customer_activity_log 
        WHERE shop_id = ? AND created_at >= $dateFilter
    ", [$shopId]);

    // Breakdown by type
    $byType = Database::fetchAll("
        SELECT activity_type, COUNT(*) as count 
        FROM customer_activity_log 
        WHERE shop_id = ? AND created_at >= $dateFilter
        GROUP BY activity_type
        ORDER BY count DESC
    ", [$shopId]);

    // Today's activities
    $today = Database::fetch("
        SELECT COUNT(*) as count FROM customer_activity_log 
        WHERE shop_id = ? AND DATE(created_at) = CURDATE()
    ", [$shopId]);

    // Unique customers active
    $uniqueCustomers = Database::fetch("
        SELECT COUNT(DISTINCT customer_id) as count FROM customer_activity_log 
        WHERE shop_id = ? AND created_at >= $dateFilter AND customer_id IS NOT NULL
    ", [$shopId]);

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_activities' => (int) $total['count'],
            'today_activities' => (int) $today['count'],
            'unique_customers' => (int) $uniqueCustomers['count'],
            'by_type' => $byType
        ]
    ]);
}

/**
 * Log a customer activity (with rate limiting)
 */
function handleLogActivity(int $shopId): void
{
    $customerId = (int) ($_POST['customer_id'] ?? 0);
    $activityType = $_POST['activity_type'] ?? 'other';
    $description = trim($_POST['description'] ?? '');
    $metadata = $_POST['metadata'] ?? null;

    // Validate activity type
    $validTypes = [
        'login',
        'logout',
        'order',
        'cart_add',
        'cart_remove',
        'profile_update',
        'password_reset',
        'support_ticket',
        'registration',
        'newsletter_subscribe',
        'newsletter_unsubscribe',
        'review',
        'wishlist',
        'other'
    ];

    if (!in_array($activityType, $validTypes)) {
        $activityType = 'other';
    }

    // Get client info
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $browser = parseUserAgentBrowser($userAgent);
    $os = parseUserAgentOS($userAgent);
    $deviceType = parseDeviceType($userAgent);

    // Rate limiting: Max 100 logs per customer per hour
    if ($customerId > 0) {
        $recentCount = Database::fetch("
            SELECT COUNT(*) as count FROM customer_activity_log 
            WHERE shop_id = ? AND customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ", [$shopId, $customerId]);

        if ((int) $recentCount['count'] >= 100) {
            echo json_encode(['success' => false, 'error' => 'Rate limit exceeded']);
            return;
        }
    }

    // Batch insert protection: Max 1000 logs per shop per minute
    $shopRecentCount = Database::fetch("
        SELECT COUNT(*) as count FROM customer_activity_log 
        WHERE shop_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
    ", [$shopId]);

    if ((int) $shopRecentCount['count'] >= 1000) {
        echo json_encode(['success' => false, 'error' => 'Shop rate limit exceeded']);
        return;
    }

    // Insert activity
    $id = Database::insert('customer_activity_log', [
        'shop_id' => $shopId,
        'customer_id' => $customerId > 0 ? $customerId : null,
        'activity_type' => $activityType,
        'description' => $description ?: null,
        'metadata' => $metadata ? (is_string($metadata) ? $metadata : json_encode($metadata)) : null,
        'ip_address' => $ipAddress,
        'user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
        'browser' => $browser,
        'os' => $os,
        'device_type' => $deviceType
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Aktivität geloggt',
        'activity_id' => $id
    ]);
}

/**
 * Export activities as JSON or SQL
 */
function handleExportActivity(int $shopId): void
{
    $format = $_GET['format'] ?? 'json';
    $period = $_GET['period'] ?? 'all';
    $type = $_GET['type'] ?? 'all';

    // Period filter
    $periodMap = [
        '7d' => 'DATE_SUB(NOW(), INTERVAL 7 DAY)',
        '30d' => 'DATE_SUB(NOW(), INTERVAL 30 DAY)',
        'year' => 'DATE_SUB(NOW(), INTERVAL 1 YEAR)',
        'all' => null
    ];

    $where = ["a.shop_id = ?"];
    $params = [$shopId];

    if (isset($periodMap[$period]) && $periodMap[$period]) {
        $where[] = "a.created_at >= " . $periodMap[$period];
    }

    if ($type !== 'all') {
        $where[] = "a.activity_type = ?";
        $params[] = $type;
    }

    $whereClause = implode(' AND ', $where);

    $activities = Database::fetchAll("
        SELECT 
            a.id, a.customer_id, a.activity_type, a.description, a.metadata,
            a.ip_address, a.browser, a.os, a.device_type, a.created_at,
            c.first_name, c.last_name, c.email
        FROM customer_activity_log a
        LEFT JOIN customers c ON a.customer_id = c.id
        WHERE $whereClause
        ORDER BY a.created_at DESC
        LIMIT 10000
    ", $params);

    if ($format === 'sql') {
        header('Content-Type: text/sql');
        header('Content-Disposition: attachment; filename="activity_export_' . date('Y-m-d') . '.sql"');

        echo "-- Customer Activity Log Export " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($activities as $a) {
            $values = [
                (int) $a['customer_id'],
                "'" . addslashes($a['activity_type']) . "'",
                $a['description'] ? "'" . addslashes($a['description']) . "'" : 'NULL',
                $a['ip_address'] ? "'" . addslashes($a['ip_address']) . "'" : 'NULL',
                $a['browser'] ? "'" . addslashes($a['browser']) . "'" : 'NULL',
                $a['os'] ? "'" . addslashes($a['os']) . "'" : 'NULL',
                "'" . addslashes($a['device_type']) . "'",
                "'" . $a['created_at'] . "'"
            ];
            echo "INSERT INTO customer_activity_log (customer_id, activity_type, description, ip_address, browser, os, device_type, created_at) VALUES (" . implode(', ', $values) . ");\n";
        }
        exit;
    }

    // JSON export (default)
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="activity_export_' . date('Y-m-d') . '.json"');

    echo json_encode([
        'exported_at' => date('Y-m-d H:i:s'),
        'total_count' => count($activities),
        'activities' => $activities
    ], JSON_PRETTY_PRINT);
    exit;
}

/**
 * Helper: Parse browser from user agent
 */
function parseUserAgentBrowser(?string $userAgent): ?string
{
    if (!$userAgent)
        return null;

    if (strpos($userAgent, 'Firefox') !== false)
        return 'Firefox';
    if (strpos($userAgent, 'Edge') !== false)
        return 'Edge';
    if (strpos($userAgent, 'Chrome') !== false)
        return 'Chrome';
    if (strpos($userAgent, 'Safari') !== false)
        return 'Safari';
    if (strpos($userAgent, 'Opera') !== false)
        return 'Opera';
    if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false)
        return 'Internet Explorer';

    return 'Unknown';
}

/**
 * Helper: Parse OS from user agent
 */
function parseUserAgentOS(?string $userAgent): ?string
{
    if (!$userAgent)
        return null;

    if (strpos($userAgent, 'Windows') !== false)
        return 'Windows';
    if (strpos($userAgent, 'Mac OS') !== false || strpos($userAgent, 'Macintosh') !== false)
        return 'macOS';
    if (strpos($userAgent, 'Linux') !== false)
        return 'Linux';
    if (strpos($userAgent, 'Android') !== false)
        return 'Android';
    if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false)
        return 'iOS';

    return 'Unknown';
}

/**
 * Helper: Determine device type from user agent
 */
function parseDeviceType(?string $userAgent): string
{
    if (!$userAgent)
        return 'unknown';

    $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPod', 'BlackBerry', 'Windows Phone'];
    $tabletKeywords = ['iPad', 'Tablet', 'PlayBook', 'Kindle'];

    foreach ($tabletKeywords as $keyword) {
        if (strpos($userAgent, $keyword) !== false)
            return 'tablet';
    }

    foreach ($mobileKeywords as $keyword) {
        if (strpos($userAgent, $keyword) !== false)
            return 'mobile';
    }

    return 'desktop';
}
