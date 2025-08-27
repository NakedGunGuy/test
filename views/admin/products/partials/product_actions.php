<?php
    if (!empty($actions)) {
        foreach ($actions as $action) {
            if ($action == 'edit') {
                partial('admin/products/partials/product_edit', ['product_id' => $product_id]);
            }
            if ($action == 'delete') {
                partial('admin/products/partials/product_delete', ['product_id' => $product_id]);
            }
        }
    }
?>
