<?php
// admin/pages/shop/preview_header.php

// Include system
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/Database.php';
Database::configure($database);

// 1. Get Settings (Shop ID 1)
$navSettings = Database::fetch("SELECT * FROM navigation_settings WHERE shop_id = 1");
$megaSettings = Database::fetch("SELECT * FROM mega_menu_settings WHERE shop_id = 1");

// Defaults if missing (consistency with settings pages)
if (!$navSettings) {
    $navSettings = [
        'menu_type' => 'header_links',
        'side_menu_style' => 'side_by_side',
        'hamburger_mode' => 'animated',
        'side_menu_direction' => 'left',
        'side_menu_backdrop' => 1,
        'side_menu_backdrop_color' => '#000000',
        'side_menu_backdrop_opacity' => 50
    ];
}

if (!$megaSettings) {
    $megaSettings = [
        'header_mega_trigger' => 'hover',
        'header_mega_animation' => 'fade',
        'header_mega_animation_speed' => 200,
        'header_mega_delay' => 100,
        'side_mega_trigger' => 'hover',
        'side_mega_animation' => 'slide',
        'side_mega_animation_speed' => 250,
        'mega_background_color' => '#ffffff',
        'mega_text_color' => '#333333',
        'mega_border_radius' => 0,
        'mega_shadow' => 1
    ];
}

// 2. Map Hamburger Mode
$hamburgerMode = $navSettings['hamburger_mode'] ?? 'animated';

// 3. Get Navigation Items (Root)
$navItemId = intval($_GET['id'] ?? 0);
$currentNav = Database::fetch("SELECT * FROM navigation_items WHERE id = ?", [$navItemId]);

// Fetch ALL items to render the full bar (more realistic)
$menuItems = Database::fetchAll("SELECT * FROM navigation_items WHERE menu_id = ? AND parent_id IS NULL ORDER BY sort_order ASC", [$currentNav['menu_id'] ?? 1]);

// Get Mega Menu Elements for this item
$elements = Database::fetchAll(
    "SELECT * FROM mega_menu_elements WHERE navigation_item_id = ? ORDER BY z_index ASC",
    [$navItemId]
);

// 4. Helper for Classes
function getVisibilityClasses($element) {
    $style = json_decode($element['style_json'] ?? '{}', true);
    $breakpoints = $style['breakpoints'] ?? ['desktop', 'tablet', 'mobile'];
    
    $classes = [];
    if (!in_array('desktop', $breakpoints)) $classes[] = 'hide-desktop';
    if (!in_array('tablet', $breakpoints)) $classes[] = 'hide-tablet';
    if (!in_array('mobile', $breakpoints)) $classes[] = 'hide-mobile';
    
    return implode(' ', $classes);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vorschau: <?= htmlspecialchars($currentNav['label'] ?? 'Mega Menu') ?></title>

    <!-- Dynamic Variables -->
    <style>
        :root {
            /* Mega Menu Appearance */
            --mega-bg: <?= $megaSettings['mega_background_color'] ?>;
            --mega-text: <?= $megaSettings['mega_text_color'] ?>;
            --mega-radius: <?= $megaSettings['mega_border_radius'] ?>px;
            --mega-shadow: <?= $megaSettings['mega_shadow'] ? '0 10px 40px -10px rgba(0,0,0,0.1)' : 'none' ?>;
            
            /* Animations (Header) */
            --header-speed: <?= $megaSettings['header_mega_animation_speed'] ?>ms;
            --header-delay: <?= $megaSettings['header_mega_delay'] ?>ms;
            
            /* Animations (Sidebar) */
            --side-speed: <?= $megaSettings['side_mega_animation_speed'] ?>ms;

            /* Side Menu Backdrop */
            --backdrop-color: <?= $navSettings['side_menu_backdrop_color'] ?>;
            --backdrop-opacity: <?= $navSettings['side_menu_backdrop_opacity'] / 100 ?>;
        }

        /* Mode-Specific Settings provided via body class */
    </style>
    <link rel="stylesheet" href="<?= asset('css/shop-preview.css') ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body class="mode-<?= $navSettings['menu_type'] ?> hamburger-<?= $hamburgerMode ?> trigger-<?= $megaSettings['header_mega_trigger'] ?>">

    <!-- Mock Shop Header -->
    <header class="shop-header">
        <div class="mobile-toggle">
            <?php if ($hamburgerMode === 'animated'): ?>
                <div class="hamburger-icon animated">
                    <span></span><span></span><span></span>
                </div>
            <?php elseif ($hamburgerMode === 'custom' && !empty($navSettings['hamburger_custom_icon_media_id'])): ?>
                <!-- Load custom icon if available, otherwise fallback -->
                 <span class="material-symbols-rounded">menu</span>
            <?php else: ?>
                <span class="material-symbols-rounded">menu</span>
            <?php endif; ?>
        </div>
        
        <a href="#" class="shop-logo">Shop Logo</a>

        <!-- Navigation -->
        <nav class="shop-nav">
            <?php foreach ($menuItems as $item): ?>
                <?php 
                $isActive = ($item['id'] == $navItemId); 
                ?>
                <div class="nav-item <?= $isActive ? 'active' : '' ?>" data-id="<?= $item['id'] ?>">
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    
                    <!-- Render Mega Menu ONLY for the active item we are previewing -->
                    <?php if ($isActive): ?>
                        <div class="mega-menu-overlay">
                            <div class="mega-canvas">
                                <?php foreach ($elements as $el): ?>
                                    <?php 
                                        $content = json_decode($el['content_json'] ?? '{}', true);
                                        $visClasses = getVisibilityClasses($el);
                                        $uid = 'el-' . $el['id'];
                                        
                                        // Position Data
                                        $Desktop = [
                                            'x' => $el['pos_x'] ?? 0, 'y' => $el['pos_y'] ?? 0, 
                                            'w' => $el['width'] ?? 100, 'h' => $el['height'] ?? 50
                                        ];
                                        $Tablet = [
                                            'x' => $el['tablet_pos_x'] ?? $Desktop['x'], 'y' => $el['tablet_pos_y'] ?? $Desktop['y'],
                                            'w' => $el['tablet_width'] ?? $Desktop['w'], 'h' => $el['tablet_height'] ?? $Desktop['h']
                                        ];
                                        $Mobile = [
                                            'x' => $el['mobile_pos_x'] ?? $Desktop['x'], 'y' => $el['mobile_pos_y'] ?? $Desktop['y'],
                                            'w' => $el['mobile_width'] ?? $Desktop['w'], 'h' => $el['mobile_height'] ?? $Desktop['h']
                                        ];
                                    ?>
                                    
                                    <style>
                                        /* Desktop (Default) */
                                        #<?= $uid ?> {
                                            left: <?= $Desktop['x'] ?>px; top: <?= $Desktop['y'] ?>px;
                                            width: <?= $Desktop['w'] ?>px; height: <?= $Desktop['h'] ?>px;
                                        }
                                        
                                        /* Tablet */
                                        @media (max-width: 1024px) {
                                            #<?= $uid ?> {
                                                left: <?= $Tablet['x'] ?>px; top: <?= $Tablet['y'] ?>px;
                                                width: <?= $Tablet['w'] ?>px; height: <?= $Tablet['h'] ?>px;
                                            }
                                        }
                                        
                                        /* Mobile */
                                        @media (max-width: 480px) {
                                            #<?= $uid ?> {
                                                left: <?= $Mobile['x'] ?>px; top: <?= $Mobile['y'] ?>px;
                                                width: <?= $Mobile['w'] ?>px; height: <?= $Mobile['h'] ?>px;
                                            }
                                        }
                                    </style>
                                    
                                    <div id="<?= $uid ?>" class="mega-element <?= $visClasses ?>" style="z-index: <?= $el['z_index'] ?>;">
                                        <?php if ($el['element_type'] == 'text'): ?>
                                            <div style="color: <?= $content['color'] ?? '#333' ?>; font-size: <?= $content['fontSize'] ?? 14 ?>px;">
                                                <?= htmlspecialchars($content['text'] ?? 'Text') ?>
                                            </div>
                                            
                                        <?php elseif ($el['element_type'] == 'image'): ?>
                                            <?php if (!empty($content['src'])): ?>
                                                <img src="<?= htmlspecialchars($content['src']) ?>" alt="">
                                            <?php else: ?>
                                                <div style="background:#eee;width:100%;height:100%;display:flex;align-items:center;justify-content:center;">IMG</div>
                                            <?php endif; ?>
                                            
                                        <?php elseif ($el['element_type'] == 'button'): ?>
                                            <a href="#" class="btn" style="background:<?= $content['backgroundColor'] ?? '#000' ?>; color:<?= $content['color'] ?? '#fff' ?>; padding: 8px 16px; text-decoration:none; border-radius:4px;">
                                                <?= htmlspecialchars($content['text'] ?? 'Button') ?>
                                            </a>
                                            
                                        <?php elseif ($el['element_type'] == 'linkgroup'): ?>
                                            <div class="mega-link-group">
                                                <h4><?= htmlspecialchars($content['title'] ?? 'Title') ?></h4>
                                                <ul>
                                                    <?php foreach ($content['links'] ?? [] as $link): ?>
                                                        <li><a href="#"><?= htmlspecialchars($link['label']) ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </nav>

        <!-- Use Icons -->
        <div class="shop-icons">
            <div class="icon-btn"><span class="material-symbols-rounded">search</span></div>
            <div class="icon-btn"><span class="material-symbols-rounded">person</span></div>
            <div class="icon-btn"><span class="material-symbols-rounded">shopping_bag</span></div>
        </div>
    </header>

    <div style="padding: 40px; text-align: center; color: #999;">
        <h1>Seiteninhalt (Platzhalter)</h1>
        <p>Bewege die Maus über "<?= htmlspecialchars($currentNav['label'] ?? 'Mega Menu') ?>", um das Mega-Menu zu sehen.</p>
        <p>Dies ist eine Live-Vorschau. Ändere die Fenstergröße, um responsive Breakpoints zu testen.</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const mobileToggle = document.querySelector('.mobile-toggle');
            const shopNav = document.querySelector('.shop-nav');

            // 1. Mobile & Side Menu Toggle
            if (mobileToggle) {
                mobileToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = shopNav.classList.toggle('mobile-open');
                    
                    // Animate Hamburger if exists
                    const animatedIcon = mobileToggle.querySelector('.hamburger-icon.animated');
                    if (animatedIcon) animatedIcon.classList.toggle('open', isOpen);
                });
            }

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (shopNav.classList.contains('mobile-open') && 
                    !shopNav.contains(e.target) && 
                    !mobileToggle.contains(e.target)) {
                    
                    shopNav.classList.remove('mobile-open');
                    const animatedIcon = mobileToggle.querySelector('.hamburger-icon.animated');
                    if (animatedIcon) animatedIcon.classList.remove('open');
                }
            });

            // 2. Click Trigger Logic (For Desktop Header Mode)
            if (body.classList.contains('trigger-click') && body.classList.contains('mode-header-links')) {
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.addEventListener('click', (e) => {
                        // If it has a mega menu, toggle it
                        if (item.querySelector('.mega-menu-overlay')) {
                            // Only toggle if we are in desktop mode ( > 1024px)
                            // Mobile/Sidebar logic is different (always expands)
                            if (window.innerWidth > 1024) {
                                e.preventDefault(); // Prevent link nav if we are just opening menu
                                e.stopPropagation();
                                
                                // Close others
                                document.querySelectorAll('.nav-item.open').forEach(other => {
                                    if (other !== item) other.classList.remove('open');
                                });
                                
                                item.classList.toggle('open');
                            }
                        }
                    });
                });
                
                // Close click-menus when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.nav-item')) {
                        document.querySelectorAll('.nav-item.open').forEach(el => el.classList.remove('open'));
                    }
                });
            }
        });
    </script>

</body>
</html>
