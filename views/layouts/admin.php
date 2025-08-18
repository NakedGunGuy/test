<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= section('title', htmlspecialchars($_ENV['APP_NAME'])) ?></title>

    <link rel="preload" href="/css/output.css" as="style">
    <link rel="stylesheet" href="/css/output.css" media="all">

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

<script src="/js/htmx.min.js"></script>
<script src="/js/preload.js"></script>
<script src="/js/images.js"></script>
<?= section('js') ?>
</body>
</html>
