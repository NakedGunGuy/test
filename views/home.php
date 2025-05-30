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
</head>

<body>
	<h1>Welcome to <?= htmlspecialchars($appName) ?></h1>
    <h1 class="text-3xl font-bold underline">    Hello world!  </h1>
</body>

</html>
