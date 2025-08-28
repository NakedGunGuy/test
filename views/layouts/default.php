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
</header>

<main>
    <?= $content ?>
</main>

<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50"></div>
<dialog id="dialog" class="w-full max-w-2xl max-h-full top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-lg shadow-sm bg-gray-700 overflow-hidden"></dialog>

<script src="/js/htmx.min.js"></script>
<script src="/js/images.js"></script>
<script src="/js/dialog.js"></script>
<?= section('js') ?>
</body>
</html>
