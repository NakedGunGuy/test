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