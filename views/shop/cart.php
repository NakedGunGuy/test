<?php start_section('title'); ?>
Cart - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<?php partial('shop/partials/cart_list', ['cart' => $cart]); ?>
