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
        $currentUrl = $_SERVER['REQUEST_URI'];
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
                        <span class="nav-icon">🔍</span><?= t('nav.discover') ?>
                    </a>    
                </li>
            </ul>
        </section>
        <section>
            <ul>
                <li>
                    <div class="search-box">
                        <span class="search-icon">🔍</span>
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
                        <span class="nav-icon">👤</span><?= t('nav.account') ?>
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
            <span class="mobile-nav-icon">🔍</span>
            <span class="mobile-nav-text"><?= t('nav.discover') ?></span>
        </a>
        <?php if (get_logged_in_user()): ?>
            <a href="<?= url('cart') ?>" class="mobile-nav-item <?= str_starts_with($currentUrl, '/cart') ? 'active' : '' ?>">
                <span class="mobile-nav-icon">🛒</span>
                <span class="mobile-nav-text"><?= t('nav.cart') ?></span>
            </a>
        <?php endif; ?>
        <a href="<?= url('profile') ?>" class="mobile-nav-item <?= str_starts_with($currentUrl, '/profile') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">👤</span>
            <span class="mobile-nav-text"><?= t('nav.account') ?></span>
        </a>
    </div>
</div>

<dialog id="dialog" class="dialog"></dialog>

<script src="/js/htmx.min.js"></script>
<script src="/js/images.js"></script>
<script src="/js/dialog.js"></script>
<script src="/js/quantity.js"></script>
<script src="/js/general.js"></script>

<?= section('js') ?>
</body>
</html>