<?php start_section('title'); ?>
Cart - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>
<?php start_section('page_title'); ?>Cart<?php end_section('page_title'); ?>

<?php partial('shop/partials/cart_list', ['cart' => $cart]); ?>
