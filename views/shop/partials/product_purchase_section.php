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
            <select name="quantity">
                <?php for ($i = 1; $i <= min(10, $product['quantity']); $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn blue btn-full">
                Add to Cart
            </button>
        </form>
    <?php else: ?>
        <div class="btn-disabled">Out of Stock</div>
    <?php endif; ?>
</div>