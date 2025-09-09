<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= section('title', htmlspecialchars($_ENV['APP_NAME'])) ?></title>

<link rel="stylesheet" href="/css/default.css" media="all">


    <?= section('css') ?>
</head>
<body hx-ext="preload" data-htmx-log-level="debug">
<!-- <header>
    <?php
    $user = get_logged_in_user();
    $cart = $user ? get_cart_items(get_user_cart_id($user['id'])) : [];
    partial('shop/partials/cart_badge', ['cart' => $cart]);
    ?>

    <?php
        $currentUrl = $_SERVER['REQUEST_URI'];
    ?>
</header> -->
    <nav>
        <section>
            <div><a href="/">Cardpoint</a></div>
            <ul>
                <li>
                    <a href="/discover" class="<?= $currentUrl === '/discover' ? 'active' : '' ?>">Discover</a>    
                </li>
            </ul>
        </section>
        <section>
            <ul>
                <li>
                    <a>Search</a>
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
                    <a href="/profile">Account</a>
                </li>
            </ul>
        </section>
    </nav>

<div>
    <header>
        <section>
            <div>Cardpoint</div>
        </section>
        <section>
            <ul>
                <li>
                    <a class="btn black" href="/login">Login</a>
                </li>
                <li>
                    <a class="btn blue" href="/register">Sign Up</a>
                </li>
            </ul>
        </section>
    </header>
    <main>
        <?= $content ?>
    </main>

</div>

<dialog id="dialog" class="dialog"></dialog>

<script src="/js/htmx.min.js"></script>
<script src="/js/images.js"></script>
<script src="/js/dialog.js"></script>
<?= section('js') ?>
</body>
</html>