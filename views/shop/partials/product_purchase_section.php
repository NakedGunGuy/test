<?php /** @var array $product */ ?>

<div id="product-purchase-<?= $product['id'] ?>" class="purchase-card" hx-swap-oob="true">
    <div class="price">
        $<?= number_format($product['price'], 2) ?>
    </div>
    
    <div class="stock-info">
        <?php if ($product['quantity'] > 0): ?>
            <?= $product['quantity'] ?> in stock
        <?php else: ?>
            <span class="out-of-stock">Out of stock</span>
        <?php endif; ?>
    </div>
    
    <?php if ($product['quantity'] > 0): ?>
        <form 
            hx-post="/cart/add"
            hx-swap="outerHTML"
            data-toast="Added to cart!"
            class="quantity-form"
        >
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <label>Quantity</label>
            <div class="quantity-selector large">
                <button type="button" class="qty-btn" onclick="changeQuantity('purchase-<?= $product['id'] ?>', -1)">-</button>
                <input type="number" id="qty-purchase-<?= $product['id'] ?>" name="quantity" value="1" min="1" max="<?= min(10, $product['quantity']) ?>" class="qty-input" readonly>
                <button type="button" class="qty-btn" onclick="changeQuantity('purchase-<?= $product['id'] ?>', 1)">+</button>
            </div>
            <button type="submit" class="btn blue btn-full">
                Add to Cart
            </button>
        </form>
    <?php else: ?>
        <div class="btn-disabled">Out of Stock</div>
    <?php endif; ?>
</div>