<?php foreach ($products as $product): ?>
    <?php partial('admin/products/partials/product_row', ['product' => $product, 'actions' => $actions]); ?>
<?php endforeach; ?>
<!-- TODO change actions to be more dynamic based on user permissions (check if the product can be deleted - if a user has the product in cart, etc. the product cannot be deleted only changed quantity to match the carts) -->