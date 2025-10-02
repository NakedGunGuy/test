<?php /** @var array $product */ ?>

<div id="product-purchase-<?= $product['id'] ?>" class="purchase-card" hx-swap-oob="true">
    <div class="price">
        €<?= number_format($product['price'], 2) ?>
    </div>
    
    <div class="stock-info">
        <?php if ($product['quantity'] > 0): ?>
            <?= t('products.in_stock', ['count' => $product['quantity']]) ?>
        <?php else: ?>
            <span class="out-of-stock"><?= t('products.out_of_stock') ?></span>
        <?php endif; ?>
    </div>
    
    <?php if ($product['quantity'] > 0): ?>
        <form 
            hx-post="<?= url('cart/add') ?>"
            hx-swap="outerHTML"
            data-toast="<?= t('toast.added_to_cart') ?>"
            class="quantity-form"
        >
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <label><?= t('cart.quantity') ?></label>
            <div class="quantity-selector large">
                <button type="button" class="qty-btn" onclick="changeQuantity('purchase-<?= $product['id'] ?>', -1)">-</button>
                <input type="number" id="qty-purchase-<?= $product['id'] ?>" name="quantity" value="1" min="1" max="<?= min(10, $product['quantity']) ?>" class="qty-input" readonly>
                <button type="button" class="qty-btn" onclick="changeQuantity('purchase-<?= $product['id'] ?>', 1)">+</button>
            </div>
            <button type="submit" class="btn blue btn-full">
                <?= t('button.add_to_cart') ?>
            </button>
        </form>
    <?php else: ?>
        <div class="btn-disabled"><?= t('status.out_of_stock') ?></div>
    <?php endif; ?>
</div>