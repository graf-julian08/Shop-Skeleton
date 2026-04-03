<?php
/**
 * ============================================
 * ADMIN PANEL - HAUPTEINSTIEG (INDEX)
 * ============================================
 * EnthÃ¤lt:
 * - Gesamtes Layout
 * - Sidebar
 * - Topbar
 * - Dashboard-Content (als Default)
 * 
 * Nutzt router.php NUR fÃ¼r Subpages
 * ============================================
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';

// Initialize Database first (needed for settings)
Database::configure($database);

// Load and apply system settings (debug mode, timezone, etc.)
require_once __DIR__ . '/includes/system_settings.php';
applySystemSettings();

require_once __DIR__ . '/includes/Auth.php';
require_once __DIR__ . '/includes/RateLimiter.php';
require_once __DIR__ . '/includes/FileUpload.php';
require_once __DIR__ . '/includes/components.php';
require_once __DIR__ . '/includes/translations.php';

// Models
require_once __DIR__ . '/models/Shop.php';
require_once __DIR__ . '/models/ShopDesign.php';
require_once __DIR__ . '/models/CmsPage.php';
require_once __DIR__ . '/models/NavigationMenu.php';
require_once __DIR__ . '/models/NavigationItem.php';
require_once __DIR__ . '/models/Category.php';

// Controllers
require_once __DIR__ . '/controllers/ShopController.php';
require_once __DIR__ . '/controllers/CmsController.php';
require_once __DIR__ . '/controllers/NavigationController.php';

// Initialize Auth (starts session, loads user if fully verified)
Auth::init();

$currentPage = currentPage();

// Login page is rendered standalone (no layout)
if ($currentPage === 'login') {
    require_once __DIR__ . '/pages/login.php';
    exit;
}

// Logout is handled directly
if ($currentPage === 'logout') {
    require_once __DIR__ . '/pages/logout.php';
    exit;
}

// Live Preview is rendered standalone (no layout)
if ($currentPage === 'shop/preview_header') {
    require_once __DIR__ . '/pages/shop/preview_header.php';
    exit;
}

// All other pages require FULL authentication (password + 2FA verified)
if (!Auth::isFullyVerified()) {
    header('Location: ?page=login');
    exit;
}

// Get current user for header display
$currentUser = Auth::user();

// ============================================
// AJAX REQUEST INTERCEPTION
// Must run BEFORE any HTML output
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    // Clear any output buffers
    while (ob_get_level())
        ob_end_clean();

    $currentPage = currentPage();

    // Navigation AJAX
    if ($currentPage === 'shop/navigation' && $_POST['ajax_action'] === 'update_order') {
        $result = NavigationController::handleUpdateOrder();
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    // CMS Settings AJAX
    if ($currentPage === 'shop/cms_settings' && $_POST['ajax_action'] === 'update_order') {
        // Handle CMS page order update if needed
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'OK']);
        exit;
    }

    // Add more AJAX handlers here as needed
}

// Now include router
require_once __DIR__ . '/router.php';

// Initialize translations for admin panel
initAdminTranslations(1);
$adminLocale = getAdminLocale();
$adminLangCode = getAdminLangCode();


?>
<!DOCTYPE html>
<html lang="<?= $adminLangCode ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">
    <meta http-equiv="Content-Language" content="<?= $adminLangCode ?>">
    <title><?php echo $config['admin_title']; ?> - <?php echo $config['shop_name']; ?></title>
    <script>
        // Theme sofort setzen um Flicker zu verhindern
        (function () {
            var savedTheme = localStorage.getItem('theme');
            var systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            var theme = savedTheme ? savedTheme : systemTheme;
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        })();

        // Admin translations for JavaScript
        window.AdminTranslations = <?= json_encode($GLOBALS['_admin_translations'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
        window.AdminLocale = '<?= $adminLocale ?>';
        window.AdminLangCode = '<?= $adminLangCode ?>';

        // Translation helper function
        window.__ = function (key, params) {
            var translation = window.AdminTranslations[key] || key;
            if (params) {
                for (var param in params) {
                    translation = translation.replace(':' + param, params[param]);
                }
            }
            return translation;
        };
    </script>
    <link rel="stylesheet" href="<?php echo asset('css/admin.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/media-picker.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/mega-menu-builder.css'); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet">
    <style>
        .material-symbols-rounded {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
        }
    </style>
    <script src="<?php echo asset('js/media-picker.js'); ?>" defer></script>
    <script src="<?php echo asset('js/mega-menu-builder.js'); ?>" defer></script>
    <script src="<?php echo asset('js/admin-modal.js'); ?>" defer></script>
</head>

<body>
    <div class="admin-layout">
        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="logo-icon"><span class="material-symbols-rounded">storefront</span></div>
                    <span><?php echo $config['shop_name']; ?></span>
                </div>
                <button class="sidebar-toggle" id="sidebar-toggle">
                    <span class="material-symbols-rounded">menu</span>
                </button>
            </div>
            <nav class="sidebar-menu">
                <!-- Dashboard (Direct link) -->
                <a href="index.php" class="menu-direct <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <span class="material-symbols-rounded menu-icon">space_dashboard</span>
                    <span class="menu-text"><?php echo __('nav.dashboard'); ?></span>
                </a>

                <!-- Menu groups from $menu Array -->
                <?php foreach ($menu as $key => $group): ?>
                    <?php if ($key === 'dashboard')
                        continue; ?>
                    <?php $isOpen = isset($group['items']) && isGroupActive($key, $group['items']); ?>
                    <div class="menu-group <?php echo $isOpen ? 'open' : ''; ?>">
                        <button class="menu-group-header">
                            <span class="material-symbols-rounded menu-icon"><?php echo $group['icon']; ?></span>
                            <span class="menu-text"><?php echo __($group['label']); ?></span>
                            <?php if (isset($group['badge'])): ?>
                                <span class="menu-badge"><?php echo $group['badge']; ?></span>
                            <?php endif; ?>
                            <span class="material-symbols-rounded menu-arrow">keyboard_arrow_down</span>
                        </button>
                        <?php if (isset($group['items'])): ?>
                            <div class="menu-group-items">
                                <?php foreach ($group['items'] as $item): ?>
                                    <a href="?page=<?php echo $item['page']; ?>"
                                        class="menu-item <?php echo isActive($item['page']) ? 'active' : ''; ?>">
                                        <?php echo __($item['label']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </nav>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <button class="header-btn mobile-menu-btn" id="mobile-menu-toggle">
                    <span class="material-symbols-rounded">menu</span>
                </button>
                <div class="search-box">
                    <span class="material-symbols-rounded">search</span>
                    <input type="text" placeholder="<?= __('header.search') ?>">
                </div>
                <div class="header-actions">
                    <button class="header-btn" id="theme-toggle" title="Dark/Light Mode">
                        <span class="material-symbols-rounded">dark_mode</span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div class="header-dropout-wrapper">
                        <button class="header-btn" id="notifications-toggle">
                            <span class="material-symbols-rounded">notifications</span>
                            <span class="badge-dot"></span>
                        </button>
                        <div class="header-dropdown" id="notifications-dropdown">
                            <div class="dropdown-header"><?= __('header.notifications') ?> <a
                                    href="#"><?= __('header.mark_all_read') ?></a></div>
                            <div class="dropdown-items">
                                <a href="#" class="dropdown-item unread">
                                    <div class="item-icon success"><span
                                            class="material-symbols-rounded">shopping_bag</span></div>
                                    <div class="item-content">
                                        <div class="item-title"><?= __('notifications.new_order') ?> #10045</div>
                                        <div class="item-time">5 <?= __('common.minutes') ?? 'minutes' ?></div>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item unread">
                                    <div class="item-icon warning"><span
                                            class="material-symbols-rounded">inventory_2</span></div>
                                    <div class="item-content">
                                        <div class="item-title"><?= __('notifications.low_stock_alert') ?></div>
                                        <div class="item-time">2 <?= __('common.hours') ?? 'hours' ?></div>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <div class="item-icon info"><span class="material-symbols-rounded">person_add</span>
                                    </div>
                                    <div class="item-content">
                                        <div class="item-title"><?= __('notifications.new_customer') ?></div>
                                        <div class="item-time"><?= __('common.yesterday') ?></div>
                                    </div>
                                </a>
                            </div>
                            <div class="dropdown-footer"><a href="?page=system/logs"><?= __('header.view_all') ?></a>
                            </div>
                        </div>
                    </div>

                    <!-- Help Dropdown -->
                    <div class="header-dropout-wrapper">
                        <button class="header-btn" id="help-toggle">
                            <span class="material-symbols-rounded">help</span>
                        </button>
                        <div class="header-dropdown" id="help-dropdown">
                            <div class="dropdown-items">
                                <a href="#" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">menu_book</span>
                                    <?= __('header.documentation') ?></a>
                                <a href="#" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">support</span>
                                    <?= __('header.contact_support') ?></a>
                                <a href="#" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">keyboard</span>
                                    <?= __('header.keyboard_shortcuts') ?></a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">info</span> <?= __('header.about') ?>
                                    v<?php echo $config['admin_version']; ?></a>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <?php
                    $userInitials = strtoupper(substr($currentUser['name'] ?? 'U', 0, 2));
                    $roles = Auth::roles();
                    $roleName = !empty($roles) ? $roles[0]['name'] : 'User';
                    ?>
                    <div class="header-dropout-wrapper">
                        <div class="user-menu" id="user-menu-toggle">
                            <div class="user-avatar"><?= htmlspecialchars($userInitials) ?></div>
                            <span><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></span>
                            <span class="material-symbols-rounded arrow">keyboard_arrow_down</span>
                        </div>
                        <div class="header-dropdown user-dropdown" id="user-dropdown">
                            <div class="user-info-header">
                                <div class="user-avatar large"><?= htmlspecialchars($userInitials) ?></div>
                                <div>
                                    <div class="user-name"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></div>
                                    <div class="user-role"><?= htmlspecialchars($roleName) ?></div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-items">
                                <a href="?page=administration/users" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">person</span> <?= __('header.profile') ?></a>
                                <a href="?page=system/settings" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">settings</span>
                                    <?= __('header.settings') ?></a>
                                <a href="?page=system/security" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">security</span>
                                    <?= __('header.security') ?></a>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="?page=logout" class="dropdown-item compact logout"><span
                                    class="material-symbols-rounded">logout</span> <?= __('header.logout') ?></a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <?php
                // Router aufrufen - gibt 'dashboard', 'error' oder 'page' zurÃ¼ck
                $result = routePage();

                // Dashboard Content (wenn Router null zurÃ¼ckgibt)
                if ($result === 'dashboard'):
                    ?>
                    <!-- ===== DASHBOARD ===== -->
                    <style>
                        .dashboard-header-row {
                            display: flex;
                            justify-content: space-between;
                            align-items: flex-start;
                            gap: 16px;
                            flex-wrap: wrap;
                        }

                        .dashboard-filters {
                            display: flex;
                            gap: 12px;
                            align-items: center;
                        }

                        .kpi-grid {
                            display: grid;
                            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                            gap: 20px;
                            margin-bottom: 24px;
                        }

                        .kpi-card {
                            background: var(--bg-secondary);
                            border: 1px solid var(--border-color);
                            border-radius: var(--radius-lg);
                            padding: 20px;
                        }

                        .kpi-header {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            margin-bottom: 12px;
                        }

                        .kpi-title {
                            font-size: 13px;
                            color: var(--text-muted);
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        }

                        .kpi-link {
                            font-size: 12px;
                            color: var(--accent);
                        }

                        .kpi-value {
                            font-size: 28px;
                            font-weight: 700;
                            color: var(--text-primary);
                            margin-bottom: 8px;
                        }

                        .kpi-change {
                            display: inline-flex;
                            align-items: center;
                            gap: 4px;
                            font-size: 13px;
                            font-weight: 500;
                            padding: 4px 8px;
                            border-radius: 6px;
                        }

                        .kpi-change.positive {
                            background: rgba(16, 185, 129, 0.15);
                            color: #10b981;
                        }

                        .kpi-change.negative {
                            background: rgba(239, 68, 68, 0.15);
                            color: #ef4444;
                        }

                        .kpi-change.neutral {
                            background: rgba(107, 114, 128, 0.15);
                            color: #6b7280;
                        }

                        .kpi-change .material-symbols-rounded {
                            font-size: 16px;
                        }

                        .dashboard-grid {
                            display: grid;
                            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
                            gap: 20px;
                            margin-bottom: 24px;
                        }

                        @media (max-width: 900px) {
                            .dashboard-grid {
                                grid-template-columns: 1fr;
                            }
                        }

                        .product-item {
                            display: flex;
                            align-items: center;
                            gap: 12px;
                            padding: 12px 16px;
                            border-bottom: 1px solid var(--border-color);
                        }

                        .product-item:last-child {
                            border-bottom: none;
                        }

                        .product-image {
                            width: 48px;
                            height: 48px;
                            background: var(--bg-tertiary);
                            border-radius: var(--radius-md);
                            overflow: hidden;
                        }

                        .product-image img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }

                        .product-info {
                            flex: 1;
                        }

                        .product-name {
                            font-weight: 500;
                            display: block;
                        }

                        .product-sku {
                            font-size: 12px;
                            color: var(--text-muted);
                        }

                        .product-stats {
                            text-align: right;
                            min-width: 100px;
                            flex-shrink: 0;
                        }

                        .product-sold {
                            display: block;
                            font-size: 12px;
                            color: var(--text-muted);
                        }

                        .product-revenue {
                            font-weight: 600;
                            color: var(--success);
                        }

                        .quick-actions {
                            display: grid;
                            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                            gap: 12px;
                        }

                        .quick-action {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            gap: 8px;
                            padding: 20px;
                            background: var(--bg-tertiary);
                            border: 1px solid var(--border-color);
                            border-radius: var(--radius-md);
                            text-align: center;
                            transition: all 0.2s;
                        }

                        .quick-action:hover {
                            background: var(--accent);
                            color: white;
                            border-color: var(--accent);
                        }

                        .quick-action .material-symbols-rounded {
                            font-size: 28px;
                        }

                        .alert {
                            display: flex;
                            align-items: center;
                            gap: 12px;
                            padding: 16px;
                            border-radius: var(--radius-md);
                            margin-bottom: 20px;
                        }

                        .alert-warning {
                            background: rgba(245, 158, 11, 0.15);
                            border: 1px solid rgba(245, 158, 11, 0.3);
                        }

                        .alert-warning .material-symbols-rounded {
                            color: #f59e0b;
                        }

                        .alert-content {
                            flex: 1;
                        }

                        .alert-content a {
                            color: var(--accent);
                            font-weight: 500;
                        }

                        .alert-close {
                            background: none;
                            border: none;
                            cursor: pointer;
                            color: var(--text-muted);
                        }

                        .loading-spinner {
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                            color: var(--text-muted);
                        }

                        @keyframes spin {
                            to {
                                transform: rotate(360deg);
                            }
                        }

                        .spinning {
                            animation: spin 1s linear infinite;
                        }

                        .empty-state {
                            text-align: center;
                            padding: 40px 20px;
                            color: var(--text-muted);
                        }

                        .empty-state .material-symbols-rounded {
                            font-size: 48px;
                            display: block;
                            margin-bottom: 8px;
                        }
                    </style>

                    <div class="page-header">
                        <div class="page-header-content">
                            <h1><?= __('dashboard.title') ?></h1>
                            <p class="page-subtitle"><?= __('dashboard.subtitle') ?></p>
                        </div>
                        <div class="dashboard-filters">
                            <select id="currencySelector" class="form-select" title="<?= __('common.currency') ?>">
                                <option value=""><?= __('common.loading') ?></option>
                            </select>
                            <select id="periodSelector" class="form-select">
                                <option value="today"><?= __('dashboard.today') ?></option>
                                <option value="week"><?= __('dashboard.last_week') ?></option>
                                <option value="month" selected><?= __('dashboard.last_month') ?></option>
                                <option value="year"><?= __('dashboard.this_year') ?></option>
                                <option value="all"><?= __('dashboard.all_time') ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    <div id="lowStockAlert" class="alert alert-warning" style="display:none;">
                        <span class="material-symbols-rounded">inventory_2</span>
                        <div class="alert-content">
                            <strong id="lowStockCount">0</strong> products have low stock.
                            <a href="?page=catalog/inventory">Check now</a>
                        </div>
                        <button class="alert-close" onclick="document.getElementById('lowStockAlert').style.display='none'">
                            <span class="material-symbols-rounded">close</span>
                        </button>
                    </div>

                    <!-- KPI Grid -->
                    <div class="kpi-grid">
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <span class="kpi-title"><?= __('dashboard.revenue') ?></span>
                            </div>
                            <div class="kpi-value" id="kpi-revenue">
                                <span class="loading-spinner"><span
                                        class="material-symbols-rounded spinning">sync</span></span>
                            </div>
                            <div class="kpi-change neutral" id="kpi-revenue-trend">--</div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <span class="kpi-title"><?= __('dashboard.orders') ?></span>
                                <a href="?page=orders/orders" class="kpi-link"><?= __('common.all') ?></a>
                            </div>
                            <div class="kpi-value" id="kpi-orders">
                                <span class="loading-spinner"><span
                                        class="material-symbols-rounded spinning">sync</span></span>
                            </div>
                            <div class="kpi-change neutral" id="kpi-orders-trend">--</div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <span class="kpi-title"><?= __('dashboard.customers') ?></span>
                                <a href="?page=customers/customers" class="kpi-link"><?= __('common.all') ?></a>
                            </div>
                            <div class="kpi-value" id="kpi-customers">
                                <span class="loading-spinner"><span
                                        class="material-symbols-rounded spinning">sync</span></span>
                            </div>
                            <div class="kpi-change neutral" id="kpi-customers-trend">--</div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <span class="kpi-title"><?= __('dashboard.aov') ?></span>
                            </div>
                            <div class="kpi-value" id="kpi-aov">
                                <span class="loading-spinner"><span
                                        class="material-symbols-rounded spinning">sync</span></span>
                            </div>
                            <div class="kpi-change neutral" id="kpi-aov-trend">--</div>
                        </div>
                    </div>

                    <!-- Dashboard Grid -->
                    <div class="dashboard-grid">
                        <!-- Aktuelle Bestellungen -->
                        <div class="card">
                            <div class="card-header">
                                <h3><?= __('dashboard.recent_orders') ?></h3>
                                <a href="?page=orders/orders" class="btn btn-sm"><?= __('dashboard.view_all') ?></a>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?= __('orders.order_number') ?></th>
                                            <th><?= __('orders.customer') ?></th>
                                            <th><?= __('common.amount') ?></th>
                                            <th><?= __('common.status') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentOrdersBody">
                                        <tr>
                                            <td colspan="4" class="empty-state">
                                                <span class="material-symbols-rounded spinning">sync</span>
                                                <p><?= __('dashboard.loading_orders') ?></p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Top Products -->
                        <div class="card">
                            <div class="card-header">
                                <h3><?= __('dashboard.top_products') ?></h3>
                                <a href="?page=catalog/products" class="btn btn-sm"><?= __('dashboard.view_all') ?></a>
                            </div>
                            <div class="card-body">
                                <div class="product-list" id="topProductsList">
                                    <div class="empty-state">
                                        <span class="material-symbols-rounded spinning">sync</span>
                                        <p><?= __('dashboard.loading_products') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3><?= __('dashboard.quick_actions') ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="?page=catalog/products" class="quick-action">
                                    <span class="material-symbols-rounded">inventory_2</span>
                                    <span><?= __('dashboard.manage_products') ?></span>
                                </a>
                                <a href="?page=orders/orders" class="quick-action">
                                    <span class="material-symbols-rounded">receipt_long</span>
                                    <span><?= __('dashboard.manage_orders') ?></span>
                                </a>
                                <a href="?page=customers/customers" class="quick-action">
                                    <span class="material-symbols-rounded">groups</span>
                                    <span><?= __('dashboard.manage_customers') ?></span>
                                </a>
                                <a href="?page=catalog/inventory" class="quick-action">
                                    <span class="material-symbols-rounded">warehouse</span>
                                    <span><?= __('dashboard.manage_inventory') ?></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <script>
                        const Dashboard = {
                            apiBase: 'api/dashboard.php',
                            shopId: 1,
                            displayCurrency: null,
                            currencySymbol: 'â‚¬',

                            async init() {
                                await this.loadCurrencies();
                                this.setupEventListeners();
                                await this.loadAllData();
                            },

                            async loadCurrencies() {
                                try {
                                    const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}`);
                                    const data = await res.json();
                                    if (data.success && data.available_currencies) {
                                        const select = document.getElementById('currencySelector');
                                        // Always prefer EUR as default for German shop
                                        const preferredDefault = 'EUR';
                                        const hasEur = data.available_currencies.some(c => c.code === preferredDefault);
                                        const defaultCode = hasEur ? preferredDefault : (data.currency.default_code || 'EUR');
                                        select.innerHTML = data.available_currencies.map(c =>
                                            `<option value="${c.code}" ${c.code === defaultCode ? 'selected' : ''}>${c.code} (${c.symbol})</option>`
                                        ).join('');
                                        this.displayCurrency = defaultCode;
                                        this.currencySymbol = data.available_currencies.find(c => c.code === defaultCode)?.symbol || 'â‚¬';
                                        select.value = defaultCode;
                                    }
                                } catch (e) { console.error('Error loading currencies:', e); }
                            },

                            setupEventListeners() {
                                document.getElementById('currencySelector').addEventListener('change', (e) => {
                                    this.displayCurrency = e.target.value;
                                    this.loadAllData();
                                });

                                document.getElementById('periodSelector').addEventListener('change', () => {
                                    this.loadAllData();
                                });
                            },

                            async loadAllData() {
                                await Promise.all([
                                    this.loadStats(),
                                    this.loadRecentOrders(),
                                    this.loadTopProducts(),
                                    this.loadLowStock()
                                ]);
                            },

                            async loadStats() {
                                const period = document.getElementById('periodSelector').value;
                                const currency = this.displayCurrency || '';

                                try {
                                    const res = await fetch(`${this.apiBase}?action=get_stats&shop_id=${this.shopId}&period=${period}&display_currency=${currency}`);
                                    const data = await res.json();

                                    if (data.success) {
                                        const s = data.stats;
                                        this.currencySymbol = data.currency.symbol;

                                        // Update KPIs
                                        document.getElementById('kpi-revenue').textContent = data.currency.symbol + this.formatNumber(s.revenue);
                                        document.getElementById('kpi-orders').textContent = this.formatNumber(s.orders, 0);
                                        document.getElementById('kpi-customers').textContent = this.formatNumber(s.customers, 0);
                                        document.getElementById('kpi-aov').textContent = data.currency.symbol + this.formatNumber(s.aov);

                                        // Update trends
                                        this.updateTrend('kpi-revenue-trend', s.revenue_trend);
                                        this.updateTrend('kpi-orders-trend', s.orders_trend);
                                        this.updateTrend('kpi-customers-trend', s.customers_trend);
                                        this.updateTrend('kpi-aov-trend', s.aov_trend);
                                    }
                                } catch (e) { console.error('Error loading stats:', e); }
                            },

                            updateTrend(elementId, trend) {
                                const el = document.getElementById(elementId);
                                const isPositive = trend > 0;
                                const isNegative = trend < 0;

                                el.className = 'kpi-change ' + (isPositive ? 'positive' : (isNegative ? 'negative' : 'neutral'));
                                el.innerHTML = `
                                <span class="material-symbols-rounded">${isPositive ? 'trending_up' : (isNegative ? 'trending_down' : 'trending_flat')}</span>
                                ${isPositive ? '+' : ''}${trend.toFixed(1)}%
                            `;
                            },

                            async loadRecentOrders() {
                                const currency = this.displayCurrency || '';
                                const tbody = document.getElementById('recentOrdersBody');

                                try {
                                    const res = await fetch(`${this.apiBase}?action=get_recent_orders&shop_id=${this.shopId}&display_currency=${currency}&limit=5`);
                                    const data = await res.json();

                                    if (data.success) {
                                        if (data.orders.length === 0) {
                                            tbody.innerHTML = '<tr><td colspan="4" class="empty-state"><span class="material-symbols-rounded">inbox</span><p>No orders</p></td></tr>';
                                            return;
                                        }

                                        tbody.innerHTML = data.orders.map(o => `
                                        <tr>
                                            <td><a href="?page=orders/order_detail&id=${o.id}">${o.order_number || '#' + o.id}</a></td>
                                            <td>${this.escapeHtml(o.customer_name)}</td>
                                            <td>${o.display_symbol}${this.formatNumber(o.display_total)}</td>
                                            <td>${this.getStatusBadge(o.status)}</td>
                                        </tr>
                                    `).join('');
                                    }
                                } catch (e) {
                                    console.error('Error loading orders:', e);
                                    tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Error loading data</td></tr>';
                                }
                            },

                            async loadTopProducts() {
                                const period = document.getElementById('periodSelector').value;
                                const currency = this.displayCurrency || '';
                                const container = document.getElementById('topProductsList');

                                try {
                                    const res = await fetch(`${this.apiBase}?action=get_top_products&shop_id=${this.shopId}&period=${period}&display_currency=${currency}&limit=5`);
                                    const data = await res.json();

                                    if (data.success) {
                                        if (data.products.length === 0) {
                                            container.innerHTML = '<div class="empty-state"><span class="material-symbols-rounded">inventory_2</span><p>No sales in this period</p></div>';
                                            return;
                                        }

                                        container.innerHTML = data.products.map(p => `
                                        <div class="product-item">
                                            <div class="product-image">
                                                ${p.thumbnail ? `<img src="${p.thumbnail}" alt="${this.escapeHtml(p.name)}">` : ''}
                                            </div>
                                            <div class="product-info">
                                                <span class="product-name">${this.escapeHtml(p.name)}</span>
                                                <span class="product-sku">SKU: ${p.sku || '-'}</span>
                                            </div>
                                            <div class="product-stats">
                                                <span class="product-sold">${p.total_sold} sold</span>
                                                <span class="product-revenue">${p.display_symbol}${this.formatNumber(p.display_revenue)}</span>
                                            </div>
                                        </div>
                                    `).join('');
                                    }
                                } catch (e) {
                                    console.error('Error loading products:', e);
                                    container.innerHTML = '<div class="empty-state">Error loading data</div>';
                                }
                            },

                            async loadLowStock() {
                                try {
                                    const res = await fetch(`${this.apiBase}?action=get_low_stock&shop_id=${this.shopId}`);
                                    const data = await res.json();

                                    if (data.success && data.total_count > 0) {
                                        document.getElementById('lowStockCount').textContent = data.total_count;
                                        document.getElementById('lowStockAlert').style.display = 'flex';
                                    } else {
                                        document.getElementById('lowStockAlert').style.display = 'none';
                                    }
                                } catch (e) { console.error('Error loading low stock:', e); }
                            },

                            getStatusBadge(status) {
                                const statusMap = {
                                    'pending': { label: 'Pending', class: 'warning' },
                                    'paid': { label: 'Paid', class: 'success' },
                                    'processing': { label: 'Processing', class: 'info' },
                                    'shipped': { label: 'Shipped', class: 'info' },
                                    'delivered': { label: 'Delivered', class: 'success' },
                                    'cancelled': { label: 'Cancelled', class: 'danger' },
                                    'refunded': { label: 'Refunded', class: 'default' }
                                };
                                const s = statusMap[status] || { label: status, class: 'default' };
                                return `<span class="badge badge-${s.class}">${s.label}</span>`;
                            },

                            formatNumber(num, decimals = 2) {
                                return new Intl.NumberFormat('de-DE', {
                                    minimumFractionDigits: decimals,
                                    maximumFractionDigits: decimals
                                }).format(num || 0);
                            },

                            escapeHtml(text) {
                                const div = document.createElement('div');
                                div.textContent = text || '';
                                return div.innerHTML;
                            }
                        };

                        document.addEventListener('DOMContentLoaded', () => Dashboard.init());
                    </script>

                <?php elseif ($result === 'error'): ?>
                    <!-- ===== ERROR PAGE ===== -->
                    <div class="error-page">
                        <span class="material-symbols-rounded"
                            style="font-size:64px;color:var(--accent)">error_outline</span>
                        <h1><?= __('error.page_not_found') ?></h1>
                        <p><?= __('error.page_not_found_desc') ?></p>
                        <a href="index.php" class="btn btn-primary"><?= __('error.back_to_dashboard') ?></a>
                    </div>
                <?php endif; ?>
                <!-- Subpages werden via routePage() per include eingebunden -->
            </div>
        </main>
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
    </div>
    <script src="<?php echo asset('js/admin.js'); ?>"></script>

    <?php if ($adminLangCode !== 'en'): ?>
        <!-- Hidden Google Translate Integration -->
        <style>
            /* Hide all Google Translate UI elements */
            .goog-te-banner-frame,
            .skiptranslate,
            #goog-gt-tt,
            .goog-te-balloon-frame,
            .goog-te-gadget,
            .goog-te-spinner-pos,
            .goog-tooltip,
            .goog-tooltip:hover,
            div.goog-text-highlight {
                display: none !important;
            }

            body {
                top: 0 !important;
            }

            .goog-te-menu-value {
                display: none !important;
            }

            /* Fix any translation styling issues */
            font {
                font-family: inherit !important;
            }

            .translated-ltr,
            .translated-rtl {
                margin-top: 0 !important;
            }
        </style>
        <script>
            // Auto-translate to selected admin language
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    includedLanguages: '<?= $adminLangCode ?>',
                    autoDisplay: false,
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                }, 'google_translate_element');

                // Auto-trigger translation after element loads
                setTimeout(function () {
                    var select = document.querySelector('.goog-te-combo');
                    if (select) {
                        select.value = '<?= $adminLangCode ?>';
                        select.dispatchEvent(new Event('change'));
                    }
                }, 500);
            }
        </script>
        <div id="google_translate_element" style="display:none;"></div>
        <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <?php endif; ?>
</body>

</html>
