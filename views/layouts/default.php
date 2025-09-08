<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= section('title', htmlspecialchars($_ENV['APP_NAME'])) ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

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
</header> -->
    <nav>
        <section>
            <div>Cardpoint</div>
            <ul>
                <li>
                    <a>Discover</a>    
                </li>
            </ul>
        </section>
        <section>
            <ul>
                <li>
                    <a>Search</a>
                </li>
                <li>
                    <a>Account</a>
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
                    <a class="btn black">Login</a>
                </li>
                <li>
                    <a class="btn blue">Sign Up</a>
                </li>
            </ul>
        </section>
    </header>
    <main>
        <?= $content ?>
    </main>

</div>

<dialog id="dialog" class="w-full max-w-2xl max-h-full top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-lg shadow-sm bg-gray-700 overflow-hidden"></dialog>

<script src="/js/htmx.min.js"></script>
<script src="/js/images.js"></script>
<script src="/js/dialog.js"></script>
<?= section('js') ?>
</body>
</html>