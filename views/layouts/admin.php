<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= section('title', htmlspecialchars($_ENV['APP_NAME'])) ?></title>

    <link rel="stylesheet" href="/css/default.css" media="all">

    <?= section('css') ?>
</head>
<body hx-ext="preload" data-htmx-log-level="debug">
<header>
    <h1>Admin Panel</h1>
    <nav>
        <a href="/admin">Dashboard</a>
        <a href="/admin/logout">Logout</a>
    </nav>
</header>

<main>
    <?= $content ?>
</main>

<dialog id="dialog" class="dialog"></dialog>

<script src="/js/htmx.min.js"></script>
<script src="/js/images.js"></script>
<script src="/js/dialog.js"></script>
<?= section('js') ?>
</body>
</html>
