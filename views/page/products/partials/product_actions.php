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
        hx-swap="outerHTML"
        class="add-to-cart-form">
    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
    <div class="quantity-selector">
        <button type="button" class="qty-btn" onclick="changeQuantity('actions-<?= $product['id'] ?>', -1)">-</button>
        <input type="number" id="qty-actions-<?= $product['id'] ?>" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" class="qty-input" readonly>
        <button type="button" class="qty-btn" onclick="changeQuantity('actions-<?= $product['id'] ?>', 1)">+</button>
    </div>
    <button type="submit" class="btn blue btn-small">Add to Cart</button>
</form>



