<?php
/**
 * ============================================
 * DASHBOARD API
 * ============================================
 * Provides real-time dashboard data:
 * - KPIs with trend calculation
 * - Recent orders
 * - Top products
 * - Low stock alerts
 * - Currency conversion
 * ============================================
 */

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Initialize database
Database::configure($database);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$shopId = (int) ($_GET['shop_id'] ?? $_POST['shop_id'] ?? 1);

switch ($action) {
    case 'get_stats':
        handleGetStats($shopId);
        break;
    case 'get_recent_orders':
        handleGetRecentOrders($shopId);
        break;
    case 'get_top_products':
        handleGetTopProducts($shopId);
        break;
    case 'get_low_stock':
        handleGetLowStock($shopId);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

// =====================================================================
// GET STATS - Main KPIs with trend calculation
// =====================================================================
function handleGetStats($shopId)
{
    $period = $_GET['period'] ?? 'month';
    $displayCurrency = $_GET['display_currency'] ?? null;

    // Build date ranges
    $dateRanges = getDateRanges($period);
    $currentStart = $dateRanges['current']['start'];
    $currentEnd = $dateRanges['current']['end'];
    $previousStart = $dateRanges['previous']['start'];
    $previousEnd = $dateRanges['previous']['end'];

    // Get currencies and exchange rates
    $currencies = Database::fetchAll(
        "SELECT code, symbol, exchange_rate, is_default FROM currencies WHERE shop_id = ? ORDER BY CASE WHEN is_default = 1 THEN 0 ELSE 1 END, name",
        [$shopId]
    );

    $exchangeRates = [];
    $currencySymbols = [];
    $defaultCurrencyCode = 'EUR';
    $defaultCurrencySymbol = '€';

    foreach ($currencies as $c) {
        $exchangeRates[$c['code']] = (float) $c['exchange_rate'];
        $currencySymbols[$c['code']] = $c['symbol'];
        if ((int) $c['is_default'] === 1) {
            $defaultCurrencyCode = $c['code'];
            $defaultCurrencySymbol = $c['symbol'];
        }
    }

    $displayCurrencyCode = $displayCurrency ?: $defaultCurrencyCode;
    $displayCurrencySymbol = $currencySymbols[$displayCurrencyCode] ?? $defaultCurrencySymbol;
    $displayRate = $exchangeRates[$displayCurrencyCode] ?? 1;

    // Orders are stored in EUR (currency_code = 'EUR')
    // To convert: (amount / sourceRate) * targetRate
    $eurRate = $exchangeRates['EUR'] ?? 1;

    // ===== CURRENT PERIOD STATS =====
    $currentStats = Database::fetch("
        SELECT 
            COALESCE(SUM(CASE WHEN status != 'cancelled' THEN grand_total ELSE 0 END), 0) as revenue,
            COUNT(*) as order_count,
            COUNT(DISTINCT customer_id) as unique_customers
        FROM orders O
        WHERE O.shop_id = ? AND O.created_at BETWEEN ? AND ?
    ", [$shopId, $currentStart, $currentEnd]);

    // Total customers (new in period)
    $newCustomers = Database::fetch("
        SELECT COUNT(*) as count FROM customers 
        WHERE shop_id = ? AND created_at BETWEEN ? AND ?
    ", [$shopId, $currentStart, $currentEnd]);

    // ===== PREVIOUS PERIOD STATS (for trend) =====
    $previousStats = Database::fetch("
        SELECT 
            COALESCE(SUM(CASE WHEN status != 'cancelled' THEN grand_total ELSE 0 END), 0) as revenue,
            COUNT(*) as order_count,
            COUNT(DISTINCT customer_id) as unique_customers
        FROM orders O
        WHERE O.shop_id = ? AND O.created_at BETWEEN ? AND ?
    ", [$shopId, $previousStart, $previousEnd]);

    $prevNewCustomers = Database::fetch("
        SELECT COUNT(*) as count FROM customers 
        WHERE shop_id = ? AND created_at BETWEEN ? AND ?
    ", [$shopId, $previousStart, $previousEnd]);

    // ===== AVERAGE ORDER VALUE (AOV) =====
    // Real metric: total revenue / number of orders
    $currentOrderCount = (int) $currentStats['order_count'];
    $previousOrderCount = (int) $previousStats['order_count'];

    $currentAOV = $currentOrderCount > 0 ? (float) $currentStats['revenue'] / $currentOrderCount : 0;
    $previousAOV = $previousOrderCount > 0 ? (float) $previousStats['revenue'] / $previousOrderCount : 0;

    // ===== CALCULATE TRENDS =====
    $revenueTrend = calculateTrend((float) $currentStats['revenue'], (float) $previousStats['revenue']);
    $ordersTrend = calculateTrend($currentOrderCount, $previousOrderCount);
    $customersTrend = calculateTrend((int) $newCustomers['count'], (int) $prevNewCustomers['count']);
    $aovTrend = calculateTrend($currentAOV, $previousAOV);

    // Convert revenue and AOV from EUR to display currency
    // Formula: (amount / sourceRate) * targetRate
    $rawRevenue = (float) $currentStats['revenue'];
    $revenueConverted = ($rawRevenue / $eurRate) * $displayRate;
    $aovConverted = ($currentAOV / $eurRate) * $displayRate;

    // ===== TOTAL CUSTOMERS (all time) =====
    $totalCustomers = Database::fetch("SELECT COUNT(*) as count FROM customers WHERE shop_id = ?", [$shopId]);

    echo json_encode([
        'success' => true,
        'stats' => [
            'revenue' => $revenueConverted,
            'revenue_trend' => $revenueTrend,
            'orders' => $currentOrderCount,
            'orders_trend' => $ordersTrend,
            'customers' => (int) $totalCustomers['count'],
            'new_customers' => (int) $newCustomers['count'],
            'customers_trend' => $customersTrend,
            'aov' => $aovConverted,
            'aov_trend' => $aovTrend
        ],
        'currency' => [
            'code' => $displayCurrencyCode,
            'symbol' => $displayCurrencySymbol,
            'default_code' => $defaultCurrencyCode
        ],
        'available_currencies' => $currencies,
        'period' => $period
    ]);
}

// =====================================================================
// GET RECENT ORDERS
// =====================================================================
function handleGetRecentOrders($shopId)
{
    $limit = (int) ($_GET['limit'] ?? 5);
    $displayCurrency = $_GET['display_currency'] ?? null;

    // Get currency info
    $currencyInfo = getCurrencyInfo($shopId, $displayCurrency);
    $displayRate = $currencyInfo['rate'];
    $displaySymbol = $currencyInfo['symbol'];

    $orders = Database::fetchAll("
        SELECT 
            o.id,
            o.order_number,
            o.grand_total,
            o.status,
            o.created_at,
            COALESCE(CONCAT(c.first_name, ' ', c.last_name), c.email, 'Gast') as customer_name
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        WHERE o.shop_id = ?
        ORDER BY o.created_at DESC
        LIMIT ?
    ", [$shopId, $limit]);

    // Convert amounts - orders stored in EUR, need (amount / eurRate) * targetRate
    $eurRate = $currencyInfo['eur_rate'];
    foreach ($orders as &$order) {
        $order['display_total'] = ((float) $order['grand_total'] / $eurRate) * $displayRate;
        $order['display_symbol'] = $displaySymbol;
    }

    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'currency' => [
            'symbol' => $displaySymbol,
            'code' => $currencyInfo['code']
        ]
    ]);
}

// =====================================================================
// GET TOP PRODUCTS
// =====================================================================
function handleGetTopProducts($shopId)
{
    $period = $_GET['period'] ?? 'month';
    $limit = (int) ($_GET['limit'] ?? 5);
    $displayCurrency = $_GET['display_currency'] ?? null;

    $dateRanges = getDateRanges($period);
    $currentStart = $dateRanges['current']['start'];
    $currentEnd = $dateRanges['current']['end'];

    $currencyInfo = getCurrencyInfo($shopId, $displayCurrency);
    $displayRate = $currencyInfo['rate'];
    $displaySymbol = $currencyInfo['symbol'];

    $products = Database::fetchAll("
        SELECT 
            p.id,
            p.name,
            p.sku,
            COALESCE(
                (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.sort_order ASC LIMIT 1),
                ''
            ) as thumbnail,
            COALESCE(SUM(oi.quantity), 0) as total_sold,
            COALESCE(SUM(oi.total_price), 0) as total_revenue
        FROM products p
        INNER JOIN order_items oi ON oi.product_id = p.id
        INNER JOIN orders o ON o.id = oi.order_id AND o.created_at BETWEEN ? AND ?
        WHERE p.shop_id = ?
        GROUP BY p.id, p.name, p.sku
        HAVING total_sold > 0
        ORDER BY total_sold DESC
        LIMIT ?
    ", [$currentStart, $currentEnd, $shopId, $limit]);

    // Convert amounts - stored in EUR, need (amount / eurRate) * targetRate
    $eurRate = $currencyInfo['eur_rate'];
    foreach ($products as &$product) {
        $product['display_revenue'] = ((float) $product['total_revenue'] / $eurRate) * $displayRate;
        $product['display_symbol'] = $displaySymbol;
    }

    echo json_encode([
        'success' => true,
        'products' => $products,
        'currency' => [
            'symbol' => $displaySymbol,
            'code' => $currencyInfo['code']
        ]
    ]);
}

// =====================================================================
// GET LOW STOCK PRODUCTS
// =====================================================================
function handleGetLowStock($shopId)
{
    $limit = (int) ($_GET['limit'] ?? 10);

    // Low stock: products with quantity > 0 and <= low_stock_threshold
    $products = Database::fetchAll("
        SELECT 
            p.id,
            p.name,
            p.sku,
            p.quantity as stock,
            p.low_stock_threshold as threshold,
            COALESCE(
                (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.sort_order ASC LIMIT 1),
                ''
            ) as thumbnail
        FROM products p
        WHERE p.shop_id = ? 
          AND p.status = 'active'
          AND p.manage_stock = 1
          AND p.quantity <= p.low_stock_threshold
        ORDER BY p.quantity ASC
        LIMIT ?
    ", [$shopId, $limit]);

    // Count total low stock
    $totalCount = Database::fetch("
        SELECT COUNT(*) as count
        FROM products p
        WHERE p.shop_id = ? 
          AND p.status = 'active'
          AND p.manage_stock = 1
          AND p.quantity <= p.low_stock_threshold
    ", [$shopId]);

    echo json_encode([
        'success' => true,
        'products' => $products,
        'total_count' => (int) $totalCount['count']
    ]);
}

// =====================================================================
// HELPER FUNCTIONS
// =====================================================================

function getDateRanges($period)
{
    $now = new DateTime();
    $today = $now->format('Y-m-d');

    switch ($period) {
        case 'today':
            $currentStart = $today . ' 00:00:00';
            $currentEnd = $today . ' 23:59:59';
            $prevStart = (clone $now)->modify('-1 day')->format('Y-m-d') . ' 00:00:00';
            $prevEnd = (clone $now)->modify('-1 day')->format('Y-m-d') . ' 23:59:59';
            break;

        case 'week':
            $currentStart = (clone $now)->modify('-7 days')->format('Y-m-d') . ' 00:00:00';
            $currentEnd = $today . ' 23:59:59';
            $prevStart = (clone $now)->modify('-14 days')->format('Y-m-d') . ' 00:00:00';
            $prevEnd = (clone $now)->modify('-7 days')->format('Y-m-d') . ' 23:59:59';
            break;

        case 'month':
            $currentStart = (clone $now)->modify('-30 days')->format('Y-m-d') . ' 00:00:00';
            $currentEnd = $today . ' 23:59:59';
            $prevStart = (clone $now)->modify('-60 days')->format('Y-m-d') . ' 00:00:00';
            $prevEnd = (clone $now)->modify('-30 days')->format('Y-m-d') . ' 23:59:59';
            break;

        case 'year':
            $currentStart = (clone $now)->modify('-1 year')->format('Y-m-d') . ' 00:00:00';
            $currentEnd = $today . ' 23:59:59';
            $prevStart = (clone $now)->modify('-2 years')->format('Y-m-d') . ' 00:00:00';
            $prevEnd = (clone $now)->modify('-1 year')->format('Y-m-d') . ' 23:59:59';
            break;

        default: // all
            $currentStart = '2000-01-01 00:00:00';
            $currentEnd = $today . ' 23:59:59';
            $prevStart = '1990-01-01 00:00:00';
            $prevEnd = '1999-12-31 23:59:59';
    }

    return [
        'current' => ['start' => $currentStart, 'end' => $currentEnd],
        'previous' => ['start' => $prevStart, 'end' => $prevEnd]
    ];
}

function calculateTrend($current, $previous)
{
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    return round((($current - $previous) / $previous) * 100, 1);
}

function getCurrencyInfo($shopId, $displayCurrency = null)
{
    $currencies = Database::fetchAll(
        "SELECT code, symbol, exchange_rate, is_default FROM currencies WHERE shop_id = ?",
        [$shopId]
    );

    $defaultCode = 'EUR';
    $defaultSymbol = '€';
    $rates = [];
    $symbols = [];

    foreach ($currencies as $c) {
        $rates[$c['code']] = (float) $c['exchange_rate'];
        $symbols[$c['code']] = $c['symbol'];
        if ((int) $c['is_default'] === 1) {
            $defaultCode = $c['code'];
            $defaultSymbol = $c['symbol'];
        }
    }

    $code = $displayCurrency ?: $defaultCode;
    $symbol = $symbols[$code] ?? $defaultSymbol;
    $rate = $rates[$code] ?? 1;
    $eurRate = $rates['EUR'] ?? 1; // For converting from EUR stored values

    return [
        'code' => $code,
        'symbol' => $symbol,
        'rate' => $rate,
        'eur_rate' => $eurRate,
        'default_code' => $defaultCode
    ];
}
