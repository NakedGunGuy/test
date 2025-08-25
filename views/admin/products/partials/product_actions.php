<?php
    if (!empty($actions)) {
        foreach ($actions as $action) {
            switch ($action) {
                case 'edit':
                    echo partial('admin/products/partials/product_edit', ['product_id' => $product_id]);
                    break;
                case 'delete':
                    echo partial('admin/products/partials/product_delete', ['product_id' => $product_id]);
                    break;
                // Add more cases as needed 
            }
        }
    }
?>
