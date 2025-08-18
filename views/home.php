<?php
/** @var string $appName */
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title><?= htmlspecialchars($appName) ?></title>

    <link rel="preload" href="/css/output.css" as="style">
    <link rel="stylesheet" href="/css/output.css" media="all">

    <script src="/js/htmx.min.js"></script>
    <script src="/js/preload.js"></script>
    <script src="/js/images.js"></script>
</head>

<body hx-ext="preload" data-htmx-log-level="debug">
	<h1>Welcome to <?= htmlspecialchars($appName) ?></h1>
    <h1 class="text-3xl font-bold underline text-red-400">    Hello world!  </h1>
    <a href="/user/1" preload="mouseover">About</a>

    <?php
    $svg = '
    <svg xmlns="http://www.w3.org/2000/svg" width="250" height="350" viewBox="0 0 250 350">
      <rect x="0" y="0" width="250" height="350" rx="15" ry="15" fill="#007bff"/>
    </svg>';
    $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
    echo '<img src="' . $dataUri . '" alt="SVG circle" data-src="assets/test.png" />';
    ?>
    <?php view('admin/login') ?>
</body>

</html>
