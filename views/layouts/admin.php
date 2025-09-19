<!DOCTYPE html>
<html lang="<?= get_current_language() ?>">
<head>
    <meta charset="UTF-8">
    <title><?= section('title', htmlspecialchars($_ENV['APP_NAME'])) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/assets/logo.png">
    <link rel="apple-touch-icon" href="/assets/logo.png">
    <link rel="stylesheet" href="/css/default.css" media="all">

    <?= section('css') ?>
</head>
<body hx-ext="preload" data-htmx-log-level="debug">

<nav>
    <section>
        <a href="<?= url('') ?>" class="brand-container">
            <img height="64" src="/assets/logo.png">
            <span>CARD</span>
            <span>POINT</span>
        </a>
        <ul>
            <li><a href="<?= url('admin') ?>" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin') && $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span><?= t('admin.dashboard') ?></a></li>
            <li><a href="<?= url('admin/products') ?>" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/products') ? 'active' : '' ?>">
                <span class="nav-icon">📦</span><?= t('admin.products') ?></a></li>
            <li><a href="<?= url('admin/orders') ?>" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/orders') ? 'active' : '' ?>">
                <span class="nav-icon">📋</span><?= t('admin.orders') ?></a></li>
            <li><a href="<?= url('admin/analytics') ?>" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/analytics') ? 'active' : '' ?>">
                <span class="nav-icon">📈</span><?= t('admin.analytics') ?></a></li>
            <li><a href="<?= url('admin/cache-images') ?>" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/cache-images') ? 'active' : '' ?>">
                <span class="nav-icon">🖼️</span><?= t('admin.image_cache') ?></a></li>
            <li><a href="<?= url('admin/shipping') ?>" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/shipping') ? 'active' : '' ?>">
                <span class="nav-icon">🚚</span><?= t('admin.shipping') ?></a></li>
            <li><a href="<?= url('admin/seo') ?>" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/seo') ? 'active' : '' ?>">
                <span class="nav-icon">🔍</span><?= t('admin.seo') ?></a></li>
            <li><a href="<?= url('admin/settings') ?>" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/settings') ? 'active' : '' ?>">
                <span class="nav-icon">⚙️</span><?= t('admin.settings') ?></a></li>
        </ul>
    </section>
    <section>
        <div class="admin-profile">
            <div class="admin-info">
                <div class="admin-avatar">A</div>
                <div>
                    <div class="admin-name"><?= t('admin.admin_user') ?></div>
                    <div class="admin-role"><?= t('admin.administrator') ?></div>
                </div>
            </div>
            <a href="<?= url('admin/logout') ?>" class="logout-link">🚪 <?= t('nav.logout') ?></a>
        </div>
    </section>
</nav>

<div>
    <header>
        <section>
            <div class="header-title"><?= t('admin.dashboard') ?></div>
        </section>
        <section>
            <div class="status-container">
                <div class="status-indicator">
                    <div class="status-dot"></div>
                </div>
                <span class="status-text"><?= t('admin.system_status') ?></span>
            </div>
        </section>
    </header>
    <style>
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    </style>
<main>
    <?php if (session_get('success')): ?>
        <div class="alert success" style="margin-bottom: 2rem;">
            ✅ <?= htmlspecialchars(session_get('success')) ?>
        </div>
    <?php endif; ?>
    
    <?php if (session_get('error')): ?>
        <div class="alert error" style="margin-bottom: 2rem;">
            ❌ <?= htmlspecialchars(session_get('error')) ?>
        </div>
    <?php endif; ?>
    
    <?= $content ?>
</main>
</div>

<!-- Mobile Bottom Navigation for Admin -->
<div class="mobile-bottom-nav">
    <div class="mobile-nav-container">
        <a href="<?= url('admin') ?>" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin') && $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
            <span class="mobile-nav-icon">📊</span>
            <span class="mobile-nav-text"><?= t('admin.dashboard') ?></span>
        </a>
        <a href="<?= url('admin/products') ?>" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/products') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">📦</span>
            <span class="mobile-nav-text"><?= t('admin.products') ?></span>
        </a>
        <a href="<?= url('admin/orders') ?>" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/orders') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">📋</span>
            <span class="mobile-nav-text"><?= t('admin.orders') ?></span>
        </a>
        <a href="<?= url('admin/analytics') ?>" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/analytics') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">📈</span>
            <span class="mobile-nav-text"><?= t('admin.analytics') ?></span>
        </a>
        <a href="<?= url('admin/settings') ?>" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/settings') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">⚙️</span>
            <span class="mobile-nav-text"><?= t('admin.settings') ?></span>
        </a>
    </div>
</div>

<dialog id="dialog" class="dialog"></dialog>

<script src="/js/htmx.min.js"></script>
<script src="/js/images.js"></script>
<script src="/js/dialog.js"></script>
<?= section('js') ?>
</body>
</html>
