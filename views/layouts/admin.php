<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= section('title', htmlspecialchars($_ENV['APP_NAME'])) ?></title>

    <link rel="stylesheet" href="/css/default.css" media="all">

    <?= section('css') ?>
</head>
<body hx-ext="preload" data-htmx-log-level="debug">

<nav>
    <section>
        <div><a href="/">Cardpoint</a></div>
        <ul>
            <li>
                <a href="/admin">Dashboard</a>
            </li>
        </ul>
    </section>
    <section>
        <ul>
            <li>
                <a href="/admin/logout">Logout</a>
            </li>
        </ul>
    </section>
</nav>

<div>
    <header>
        <section>
            <div>Admin Dashboard</div>
        </section>
    </header>
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
<dialog id="dialog" class="dialog"></dialog>

<script src="/js/htmx.min.js"></script>
<script src="/js/images.js"></script>
<script src="/js/dialog.js"></script>
<?= section('js') ?>
</body>
</html>
