<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= section('title', htmlspecialchars($_ENV['APP_NAME'])) ?></title>

    <link rel="stylesheet" href="/css/default.css" media="all">

    <?= section('css') ?>
</head>
<body hx-ext="preload" data-htmx-log-level="debug">
    <?php
        $currentUrl = $_SERVER['REQUEST_URI'];
    ?>
    <nav>
        <section>
            <a href="/" class="brand-container">
                <img height="64" src="/assets/logo.png">
                <span>CARD</span>
                <span>POINT</span>
            </a>
            <ul>
                <li>
                    <a href="/discover" class="<?= $currentUrl === '/discover' ? 'active' : '' ?>">
                        <span class="nav-icon">🔍</span>Discover
                    </a>    
                </li>
                <li>
                    <a href="/cards" class="<?= str_starts_with($currentUrl, '/cards') ? 'active' : '' ?>">
                        <span class="nav-icon">🃏</span>Cards
                    </a>
                </li>
            </ul>
        </section>
        <section>
            <ul>
                <li>
                    <div class="search-box">
                        <span class="search-icon">🔍</span>
                        <span class="search-placeholder">Search cards...</span>
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
                    <a href="/profile" class="<?= str_starts_with($currentUrl, '/profile') ? 'active' : '' ?>">
                        <span class="nav-icon">👤</span>Account
                    </a>
                </li>
            </ul>
        </section>
    </nav>

<div>
    <header>
        <section>
            <div class="page-title">Discover</div>
        </section>
        <section>
            <ul style="display: flex; gap: 1rem; align-items: center;">
                <?php $user = get_logged_in_user(); if ($user): ?>
                    <li class="welcome-user">
                        <div class="online-dot"></div>
                        Welcome, <?= htmlspecialchars($user['username']) ?>
                    </li>
                    <li>
                        <a class="btn black" href="/profile">Profile</a>
                    </li>
                <?php else: ?>
                    <li>
                        <a class="btn black" href="/login">Login</a>
                    </li>
                    <li>
                        <a class="btn blue" href="/register">Sign Up</a>
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
        <a href="/discover" class="mobile-nav-item <?= $currentUrl === '/discover' ? 'active' : '' ?>">
            <span class="mobile-nav-icon">🔍</span>
            <span class="mobile-nav-text">Discover</span>
        </a>
        <a href="/cards" class="mobile-nav-item <?= str_starts_with($currentUrl, '/cards') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">🃏</span>
            <span class="mobile-nav-text">Cards</span>
        </a>
        <?php if (get_logged_in_user()): ?>
            <a href="/cart" class="mobile-nav-item <?= str_starts_with($currentUrl, '/cart') ? 'active' : '' ?>">
                <span class="mobile-nav-icon">🛒</span>
                <span class="mobile-nav-text">Cart</span>
            </a>
        <?php endif; ?>
        <a href="/profile" class="mobile-nav-item <?= str_starts_with($currentUrl, '/profile') ? 'active' : '' ?>">
            <span class="mobile-nav-icon">👤</span>
            <span class="mobile-nav-text">Account</span>
        </a>
    </div>
</div>

<dialog id="dialog" class="dialog"></dialog>

<script src="/js/htmx.min.js"></script>
<script src="/js/images.js"></script>
<script src="/js/dialog.js"></script>
<script src="/js/quantity.js"></script>
<?= section('js') ?>
</body>
</html>