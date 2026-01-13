<?php
/**
 * ============================================
 * ADMIN PANEL - HAUPTEINSTIEG (INDEX)
 * ============================================
 * Enthält:
 * - Gesamtes Layout
 * - Sidebar
 * - Topbar
 * - Dashboard-Content (als Default)
 * 
 * Nutzt router.php NUR für Subpages
 * ============================================
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Auth.php';
require_once __DIR__ . '/includes/FileUpload.php';
require_once __DIR__ . '/includes/components.php';

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

// Initialize Database
Database::configure($database);

// Initialize Auth (starts session, loads user)
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

// All other pages require authentication
if (!Auth::check()) {
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
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <!-- Dashboard (Direktlink) -->
                <a href="index.php" class="menu-direct <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <span class="material-symbols-rounded menu-icon">space_dashboard</span>
                    <span class="menu-text">Dashboard</span>
                </a>

                <!-- Menügruppen aus $menu Array -->
                <?php foreach ($menu as $key => $group): ?>
                    <?php if ($key === 'dashboard')
                        continue; ?>
                    <?php $isOpen = isset($group['items']) && isGroupActive($key, $group['items']); ?>
                    <div class="menu-group <?php echo $isOpen ? 'open' : ''; ?>">
                        <button class="menu-group-header">
                            <span class="material-symbols-rounded menu-icon"><?php echo $group['icon']; ?></span>
                            <span class="menu-text"><?php echo $group['label']; ?></span>
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
                                        <?php echo $item['label']; ?>
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
                    <input type="text" placeholder="Suchen...">
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
                            <div class="dropdown-header">Benachrichtigungen <a href="#">Alle markieren</a></div>
                            <div class="dropdown-items">
                                <a href="#" class="dropdown-item unread">
                                    <div class="item-icon success"><span
                                            class="material-symbols-rounded">shopping_bag</span></div>
                                    <div class="item-content">
                                        <div class="item-title">Neue Bestellung #10045</div>
                                        <div class="item-time">Vor 5 Minuten</div>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item unread">
                                    <div class="item-icon warning"><span
                                            class="material-symbols-rounded">inventory_2</span></div>
                                    <div class="item-content">
                                        <div class="item-title">Niedriger Lagerbestand</div>
                                        <div class="item-time">Vor 2 Stunden</div>
                                    </div>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <div class="item-icon info"><span class="material-symbols-rounded">person_add</span>
                                    </div>
                                    <div class="item-content">
                                        <div class="item-title">Neuer Kunde registriert</div>
                                        <div class="item-time">Gestern</div>
                                    </div>
                                </a>
                            </div>
                            <div class="dropdown-footer"><a href="?page=system/logs">Alle anzeigen</a></div>
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
                                        class="material-symbols-rounded">menu_book</span> Dokumentation</a>
                                <a href="#" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">support</span> Support kontaktieren</a>
                                <a href="#" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">keyboard</span> Tastenkürzel</a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">info</span> Über
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
                                        class="material-symbols-rounded">person</span> Profil</a>
                                <a href="?page=system/settings" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">settings</span> Einstellungen</a>
                                <a href="?page=system/security" class="dropdown-item compact"><span
                                        class="material-symbols-rounded">security</span> Sicherheit</a>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="?page=logout" class="dropdown-item compact logout"><span
                                    class="material-symbols-rounded">logout</span> Abmelden</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <?php
                // Router aufrufen - gibt 'dashboard', 'error' oder 'page' zurück
                $result = routePage();

                // Dashboard Content (wenn Router null zurückgibt)
                if ($result === 'dashboard'):
                    ?>
                    <!-- ===== DASHBOARD ===== -->
                    <div class="page-header">
                        <div class="page-header-content">
                            <h1>Dashboard</h1>
                            <p class="page-subtitle">Willkommen zurück! Hier ist Ihre Shop-Übersicht.</p>
                        </div>
                        <div class="page-header-actions">
                            <select class="form-select">
                                <option>Heute</option>
                                <option>7 Tage</option>
                                <option>30 Tage</option>
                                <option>Dieses Jahr</option>
                            </select>
                        </div>
                    </div>

                    <!-- KPI Grid -->
                    <div class="kpi-grid">
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <span class="kpi-title">Gesamtumsatz</span>
                                <a href="?page=finance/reports" class="kpi-link">Bericht</a>
                            </div>
                            <div class="kpi-value">€24.580,00</div>
                            <div class="kpi-change positive">
                                <span class="material-symbols-rounded">trending_up</span>+12,5%
                            </div>
                            <div class="kpi-chart">
                                <div class="chart-placeholder">
                                    <div class="chart-bar" style="height:40%"></div>
                                    <div class="chart-bar" style="height:60%"></div>
                                    <div class="chart-bar" style="height:80%"></div>
                                    <div class="chart-bar" style="height:65%"></div>
                                    <div class="chart-bar" style="height:90%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <span class="kpi-title">Bestellungen</span>
                                <a href="?page=orders/orders" class="kpi-link">Alle</a>
                            </div>
                            <div class="kpi-value">156</div>
                            <div class="kpi-change positive">
                                <span class="material-symbols-rounded">trending_up</span>+8,3%
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <span class="kpi-title">Kunden</span>
                                <a href="?page=customers/customers" class="kpi-link">Alle</a>
                            </div>
                            <div class="kpi-value">1.284</div>
                            <div class="kpi-change positive">
                                <span class="material-symbols-rounded">trending_up</span>+15,2%
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <span class="kpi-title">Conversion</span>
                                <a href="?page=marketing/analytics" class="kpi-link">Details</a>
                            </div>
                            <div class="kpi-value">3,24%</div>
                            <div class="kpi-change negative">
                                <span class="material-symbols-rounded">trending_down</span>-0,5%
                            </div>
                        </div>
                    </div>

                    <!-- Alert -->
                    <div class="alert alert-info">
                        <span class="material-symbols-rounded">info</span>
                        <div class="alert-content">
                            <strong>5 Produkte</strong> haben niedrigen Lagerbestand.
                            <a href="?page=catalog/inventory">Jetzt prüfen</a>
                        </div>
                        <button class="alert-close"><span class="material-symbols-rounded">close</span></button>
                    </div>

                    <!-- Dashboard Grid -->
                    <div class="dashboard-grid">
                        <!-- Aktuelle Bestellungen -->
                        <div class="card">
                            <div class="card-header">
                                <h3>Aktuelle Bestellungen</h3>
                                <a href="?page=orders/orders" class="btn btn-sm">Alle anzeigen</a>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Bestellung</th>
                                            <th>Kunde</th>
                                            <th>Betrag</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><a href="#">#10045</a></td>
                                            <td>Max Mustermann</td>
                                            <td>€129,99</td>
                                            <td><span class="badge badge-warning">Ausstehend</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">#10044</a></td>
                                            <td>Anna Schmidt</td>
                                            <td>€89,50</td>
                                            <td><span class="badge badge-success">Bezahlt</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">#10043</a></td>
                                            <td>Peter Weber</td>
                                            <td>€245,00</td>
                                            <td><span class="badge badge-info">Versendet</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Top Produkte -->
                        <div class="card">
                            <div class="card-header">
                                <h3>Top Produkte</h3>
                                <a href="?page=catalog/products" class="btn btn-sm">Alle anzeigen</a>
                            </div>
                            <div class="card-body">
                                <div class="product-list">
                                    <div class="product-item">
                                        <div class="product-image"></div>
                                        <div class="product-info">
                                            <span class="product-name">Premium Lederjacke</span>
                                            <span class="product-sku">SKU: LJ-001</span>
                                        </div>
                                        <div class="product-stats">
                                            <span class="product-sold">45 verkauft</span>
                                            <span class="product-revenue">€4.050</span>
                                        </div>
                                    </div>
                                    <div class="product-item">
                                        <div class="product-image"></div>
                                        <div class="product-info">
                                            <span class="product-name">Designer Sneaker</span>
                                            <span class="product-sku">SKU: DS-023</span>
                                        </div>
                                        <div class="product-stats">
                                            <span class="product-sold">38 verkauft</span>
                                            <span class="product-revenue">€3.420</span>
                                        </div>
                                    </div>
                                    <div class="product-item">
                                        <div class="product-image"></div>
                                        <div class="product-info">
                                            <span class="product-name">Cashmere Pullover</span>
                                            <span class="product-sku">SKU: CP-112</span>
                                        </div>
                                        <div class="product-stats">
                                            <span class="product-sold">32 verkauft</span>
                                            <span class="product-revenue">€2.880</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schnellaktionen -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Schnellaktionen</h3>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="?page=catalog/products" class="quick-action">
                                    <span class="material-symbols-rounded">add_box</span>
                                    <span>Produkt hinzufügen</span>
                                </a>
                                <a href="?page=orders/orders" class="quick-action">
                                    <span class="material-symbols-rounded">local_shipping</span>
                                    <span>Bestellung bearbeiten</span>
                                </a>
                                <a href="?page=marketing/coupons" class="quick-action">
                                    <span class="material-symbols-rounded">confirmation_number</span>
                                    <span>Gutschein erstellen</span>
                                </a>
                                <a href="?page=finance/reports" class="quick-action">
                                    <span class="material-symbols-rounded">assessment</span>
                                    <span>Bericht generieren</span>
                                </a>
                            </div>
                        </div>
                    </div>

                <?php elseif ($result === 'error'): ?>
                    <!-- ===== ERROR PAGE ===== -->
                    <div class="error-page">
                        <span class="material-symbols-rounded"
                            style="font-size:64px;color:var(--accent)">error_outline</span>
                        <h1>Seite nicht gefunden</h1>
                        <p>Die angeforderte Seite existiert nicht oder Sie haben keine Berechtigung.</p>
                        <a href="index.php" class="btn btn-primary">Zurück zum Dashboard</a>
                    </div>
                <?php endif; ?>
                <!-- Subpages werden via routePage() per include eingebunden -->
            </div>
        </main>
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
    </div>
    <script src="<?php echo asset('js/admin.js'); ?>"></script>
</body>

</html>