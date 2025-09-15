<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= section('title', htmlspecialchars($_ENV['APP_NAME'])) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/default.css" media="all">

    <?= section('css') ?>
</head>
<body hx-ext="preload" data-htmx-log-level="debug">

<nav>
    <section>
        <a href="/" class="brand-container">
            <img height="64" src="/assets/logo.png">
            <span>CARD</span>
            <span>POINT</span>
        </a>
        <ul>
            <li><a href="/admin" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin') && $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>Dashboard</a></li>
            <li><a href="/admin/products" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/products') ? 'active' : '' ?>">
                <span class="nav-icon">📦</span>Products</a></li>
            <li><a href="/admin/orders" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/orders') ? 'active' : '' ?>">
                <span class="nav-icon">📋</span>Orders</a></li>
            <li><a href="/admin/analytics" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/analytics') ? 'active' : '' ?>">
                <span class="nav-icon">📈</span>Analytics</a></li>
            <li><a href="/admin/cache-images" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/cache-images') ? 'active' : '' ?>">
                <span class="nav-icon">🖼️</span>Image Cache</a></li>
            <li><a href="/admin/shipping" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/shipping') ? 'active' : '' ?>">
                <span class="nav-icon">🚚</span>Shipping</a></li>
            <li><a href="/admin/settings" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/settings') ? 'active' : '' ?>">
                <span class="nav-icon">⚙️</span>Settings</a></li>
        </ul>
    </section>
    <section>
        <div class="admin-profile">
            <div class="admin-info">
                <div class="admin-avatar">A</div>
                <div>
                    <div class="admin-name">Admin User</div>
                    <div class="admin-role">Administrator</div>
                </div>
            </div>
            <a href="/admin/logout" class="logout-link">🚪 Logout</a>
        </div>
    </section>
</nav>

<div>
    <header>
        <section>
            <div class="header-title">Admin Dashboard</div>
        </section>
        <section>
            <div class="status-container">
                <div class="status-indicator">
                    <div class="status-dot"></div>
                </div>
                <span class="status-text">System Status: Online</span>
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
        <a href="/admin" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin') && $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
            <span class="mobile-nav-icon">📊</span>
            <span class="mobile-nav-text">Dashboard</span>
        </a>
        <a href="/admin/products" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/products') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">📦</span>
            <span class="mobile-nav-text">Products</span>
        </a>
        <a href="/admin/orders" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/orders') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">📋</span>
            <span class="mobile-nav-text">Orders</span>
        </a>
        <a href="/admin/analytics" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/analytics') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">📈</span>
            <span class="mobile-nav-text">Analytics</span>
        </a>
        <a href="/admin/settings" class="mobile-nav-item <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/settings') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">⚙️</span>
            <span class="mobile-nav-text">Settings</span>
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
