<?php foreach ($products as $product): ?>
    <?php
        $actions = ['edit']; // always allow edit
        if (!empty($product['can_be_deleted'])) {
            $actions[] = 'delete'; // only allow delete if no carts
        }
    ?>
    <?php partial('admin/products/partials/product_row', ['product' => $product, 'actions' => $actions]); ?>
<?php endforeach; ?>