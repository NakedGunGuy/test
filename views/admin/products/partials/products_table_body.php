<?php foreach ($products as $product): ?>
    <?php partial('admin/products/partials/product_row', ['product' => $product]); ?>
<?php endforeach; ?>