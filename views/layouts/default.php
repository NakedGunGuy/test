<!DOCTYPE html>
<html lang="<?= get_current_language() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/assets/logo.png">
    <link rel="apple-touch-icon" href="/assets/logo.png">

    <?php
    // Get SEO data for current page
    $seo_data_raw = section('seo_data');
    if ($seo_data_raw) {
        $seo_data = unserialize($seo_data_raw);
    } else {
        $seo_data = get_seo_meta('home');
    }

    $schemas_raw = section('schemas');
    if ($schemas_raw) {
        $schemas = unserialize($schemas_raw);
    } else {
        $schemas = [
            generate_schema_markup('organization'),
            generate_schema_markup('website')
        ];
    }
    ?>

    <title><?= htmlspecialchars($seo_data['title']) ?></title>

    <?= render_meta_tags($seo_data) ?>

    <link rel="stylesheet" href="/css/default.css" media="all">

    <?= section('css') ?>

    <?= render_schema_markup(array_filter($schemas)) ?>
</head>
<body hx-ext="preload" data-htmx-log-level="debug">
    <?php
        $currentUrl = get_uri_without_language();
    ?>
    <nav>
        <section>
            <a href="<?= url('') ?>" class="brand-container">
                <img height="64" src="/assets/logo.png">
                <span>CARD</span>
                <span>POINT</span>
            </a>
            <ul>
                <li>
                    <a href="<?= url('discover') ?>" class="<?= str_starts_with($currentUrl, '/discover') ? 'active' : '' ?>">
                        <?= icon('search', 'nav-icon') ?><?= t('nav.discover') ?>
                    </a>
                </li>
                <?php
                // Get CMS pages for navigation
                try {
                    $cms_pages = build_page_tree();
                    $lang = get_current_language();

                    // Build hierarchical structure
                    $top_level_pages = [];
                    $children = [];

                    foreach ($cms_pages as $slug => $page) {
                        if (empty($page['parent'])) {
                            $top_level_pages[$slug] = $page;
                        } else {
                            if (!isset($children[$page['parent']])) {
                                $children[$page['parent']] = [];
                            }
                            $children[$page['parent']][$slug] = $page;
                        }
                    }

                    // Display top-level pages with their children
                    foreach ($top_level_pages as $slug => $page):
                        $title = $page['translations'][$lang]['title'] ?? $page['translations']['en']['title'] ?? $slug;
                        $has_children = isset($children[$slug]);
                        $is_active = str_starts_with($currentUrl, $page['full_path']);
                ?>
                <li class="nav-item <?= $has_children ? 'has-submenu' : '' ?>">
                    <a href="<?= url($page['full_path']) ?>" class="<?= $is_active ? 'active' : '' ?>">
                        <?= icon('file', 'nav-icon') ?><?= htmlspecialchars($title) ?>
                    </a>
                    <?php if ($has_children): ?>
                        <ul class="submenu">
                            <?php foreach ($children[$slug] as $child_slug => $child_page):
                                $child_title = $child_page['translations'][$lang]['title'] ?? $child_page['translations']['en']['title'] ?? $child_slug;
                                $child_active = str_starts_with($currentUrl, $child_page['full_path']);
                            ?>
                                <li>
                                    <a href="<?= url($child_page['full_path']) ?>" class="<?= $child_active ? 'active' : '' ?>">
                                        <?= htmlspecialchars($child_title) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
                <?php
                    endforeach;
                } catch (Exception $e) {
                    // If YAML pages fail to load, skip silently
                }
                ?>
            </ul>
        </section>
        <section>
            <ul>
                <li>
                    <div class="search-box"
                         hx-get="<?= url('search-dialog') ?>"
                         hx-target="#dialog"
                         hx-swap="innerHTML">
                        <?= icon('search', 'search-icon') ?>
                        <span class="search-placeholder"><?= t('common.search_cards') ?></span>
                    </div>
                </li>
                <li>
                    <?php
                    $user = get_logged_in_user();
                    if ($user) {
                        $cart = get_cart_items(get_user_cart_id($user['id']));
                        partial('shop/partials/cart_badge', ['cart' => $cart]);
                    }
                    ?>
                </li>
                <li>
                    <a href="<?= url('profile') ?>" class="<?= str_starts_with($currentUrl, '/profile') ? 'active' : '' ?>">
                        <?= icon('user', 'nav-icon') ?><?= t('nav.account') ?>
                    </a>
                </li>
            </ul>
        </section>
    </nav>

<div>
    <header>
        <section>
            <div class="page-title"><?= section('page_title', t('common.home')) ?></div>
        </section>
        <section>
            <ul style="display: flex; gap: 1rem; align-items: center;">
                <li>
                    <?php partial('partials/language_toggle'); ?>
                </li>
                <?php $user = get_logged_in_user(); if ($user): ?>
                    <li>
                        <a class="btn black" href="<?= url('profile') ?>"><?= t('nav.profile') ?></a>
                    </li>
                <?php else: ?>
                    <li>
                        <a class="btn black" href="<?= url('login') ?>"><?= t('nav.login') ?></a>
                    </li>
                    <li>
                        <a class="btn blue" href="<?= url('register') ?>"><?= t('nav.signup') ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </section>
    </header>
    <main>
        <?= $content ?>
    </main>

</div>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav">
    <div class="mobile-nav-container">
        <a href="<?= url('') ?>" class="mobile-nav-item">
            <span class="mobile-nav-icon"><img height="64" src="/assets/logo.png"></span>
        </a>
        <a href="<?= url('discover') ?>" class="mobile-nav-item <?= str_starts_with($currentUrl, '/discover') ? 'active' : '' ?>">
            <?= icon('search', 'mobile-nav-icon') ?>
            <span class="mobile-nav-text"><?= t('nav.discover') ?></span>
        </a>
        <?php if (get_logged_in_user()): ?>
            <a href="<?= url('cart') ?>" class="mobile-nav-item <?= str_starts_with($currentUrl, '/cart') ? 'active' : '' ?>">
                <?= icon('cart', 'mobile-nav-icon') ?>
                <span class="mobile-nav-text"><?= t('nav.cart') ?></span>
            </a>
        <?php endif; ?>
        <a href="<?= url('profile') ?>" class="mobile-nav-item <?= str_starts_with($currentUrl, '/profile') ? 'active' : '' ?>">
            <?= icon('user', 'mobile-nav-icon') ?>
            <span class="mobile-nav-text"><?= t('nav.account') ?></span>
        </a>
        <button class="hamburger-menu-btn" id="hamburger-menu-btn" aria-label="Menu">
            <div class="hamburger-icon">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <span class="mobile-nav-text">MENU</span>
        </button>
    </div>
</div>

<!-- Mobile Menu Overlay and Panel -->
<div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>
<div class="mobile-menu-panel" id="mobile-menu-panel">
    <div class="mobile-menu-header">
        <a href="<?= url('') ?>" class="mobile-menu-brand">
            <img src="/assets/logo.png" alt="Cardpoint">
            <span>CARDPOINT</span>
        </a>
        <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Close menu">&times;</button>
    </div>

    <ul class="mobile-menu-nav">
        <li>
            <a href="<?= url('discover') ?>" class="<?= str_starts_with($currentUrl, '/discover') ? 'active' : '' ?>">
                <?= icon('search', 'nav-icon') ?><?= t('nav.discover') ?>
            </a>
        </li>
        <?php
        // Get CMS pages for mobile menu
        try {
            $cms_pages = build_page_tree();
            $lang = get_current_language();

            // Build hierarchical structure
            $top_level_pages = [];
            $children = [];

            foreach ($cms_pages as $slug => $page) {
                if (empty($page['parent'])) {
                    $top_level_pages[$slug] = $page;
                } else {
                    if (!isset($children[$page['parent']])) {
                        $children[$page['parent']] = [];
                    }
                    $children[$page['parent']][$slug] = $page;
                }
            }

            // Display top-level pages with their children
            foreach ($top_level_pages as $slug => $page):
                $title = $page['translations'][$lang]['title'] ?? $page['translations']['en']['title'] ?? $slug;
                $has_children = isset($children[$slug]);
                $is_active = str_starts_with($currentUrl, $page['full_path']);
        ?>
        <li>
            <a href="<?= url($page['full_path']) ?>" class="<?= $is_active ? 'active' : '' ?>">
                <?= icon('file', 'nav-icon') ?><?= htmlspecialchars($title) ?>
            </a>
            <?php if ($has_children): ?>
                <ul class="mobile-menu-submenu">
                    <?php foreach ($children[$slug] as $child_slug => $child_page):
                        $child_title = $child_page['translations'][$lang]['title'] ?? $child_page['translations']['en']['title'] ?? $child_slug;
                        $child_active = str_starts_with($currentUrl, $child_page['full_path']);
                    ?>
                        <li>
                            <a href="<?= url($child_page['full_path']) ?>" class="<?= $child_active ? 'active' : '' ?>">
                                <?= htmlspecialchars($child_title) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
        <?php
            endforeach;
        } catch (Exception $e) {
            // If YAML pages fail to load, skip silently
        }
        ?>
        <?php if (get_logged_in_user()): ?>
        <li>
            <a href="<?= url('cart') ?>" class="<?= str_starts_with($currentUrl, '/cart') ? 'active' : '' ?>">
                <?= icon('cart', 'nav-icon') ?><?= t('nav.cart') ?>
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="<?= url('profile') ?>" class="<?= str_starts_with($currentUrl, '/profile') ? 'active' : '' ?>">
                <?= icon('user', 'nav-icon') ?><?= t('nav.account') ?>
            </a>
        </li>
    </ul>

    <div class="mobile-menu-search">
        <div class="mobile-menu-search-box" onclick="alert('Search feature coming soon!')">
            <?= icon('search', 'mobile-menu-search-icon') ?>
            <span class="mobile-menu-search-placeholder"><?= t('common.search_cards') ?></span>
        </div>
    </div>
</div>

<dialog id="dialog" class="dialog"></dialog>

<script src="/js/htmx.min.js"></script>
<script src="/js/images.js"></script>
<script src="/js/dialog.js"></script>
<script src="/js/quantity.js"></script>
<script src="/js/general.js"></script>
<script src="/js/hamburger.js"></script>

<?= section('js') ?>
</body>
</html>