<?php if (empty($cart)): ?>
    <p hx-swap-oob="true" id="cart-list">Your cart is empty.</p>
<?php else: ?>
    <div class="cart-grid" hx-swap-oob="true" id="cart-list">
        <div class="cart-header">
            <div class="header-cell">Product</div>
            <div class="header-cell">Price</div>
            <div class="header-cell">Qty</div>
            <div class="header-cell">Total</div>
            <div class="header-cell"></div>
        </div>
        <div class="cart-body">
        <?php foreach ($cart as $item): ?>
            <div class="cart-row" id="cart-item-<?= $item['product_id'] ?>">
                <div class="cart-cell"><?= htmlspecialchars($item['name']) ?></div>
                <div class="cart-cell">$<?= number_format($item['price'], 2) ?></div>
                <div class="cart-cell"><div id="quantity-<?= $item['id'] ?>"><?= $item['quantity'] ?></div></div>
                <div class="cart-cell">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                <div class="cart-cell">
                    <form
                        hx-post="/cart/remove"
                        hx-swap="outerHTML">
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <button type="submit" class="text-red-600">Remove</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
