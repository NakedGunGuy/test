<?php
    // if (!empty($actions)) {
    //     foreach ($actions as $action) {
    //         if ($action == 'edit') {
    //             partial('page/products/partials/product_edit', ['product_id' => $product_id]);
    //         }
    //         if ($action == 'delete') {
    //             partial('page/products/partials/product_delete', ['product_id' => $product_id]);
    //         }
    //     }
    // }
?>

<form
        hx-post="/cart/add"
        hx-swap="outerHTML">
    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
    <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>">
    <button type="submit">Add to Cart</button>
</form>



