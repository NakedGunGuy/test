<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= section('title', 'Admin Dashboard') ?></title>

    <!-- Default CSS -->

    <!-- Additional CSS per page -->
    <?= section('css') ?>
</head>
<body>
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

<!-- Default JS -->

<!-- Additional JS per page -->
<?= section('js') ?>
</body>
</html>
