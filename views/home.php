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
</head>

<body hx-ext="preload" data-htmx-log-level="debug">
	<h1>Welcome to <?= htmlspecialchars($appName) ?></h1>
    <h1 class="text-3xl font-bold underline">    Hello world!  </h1>
    <a href="/user/1" preload="mouseover">About</a>
</body>

</html>
