<?php if (empty($cart)): ?>
    <div hx-swap-oob="true" id="cart-list">
        <div class="empty">
            <div class="icon">🛒</div>
            <h3><?= t('cart.empty') ?></h3>
            <p><?= t('cart.add_products') ?></p>
            <a href="<?= url('discover') ?>" class="btn blue"><?= t('button.browse_products') ?></a>
        </div>
    </div>
<?php else: ?>
    <?php
    $cart_total = 0;
    foreach ($cart as $item) {
        $cart_total += $item['price'] * $item['quantity'];
    }
    ?>
    <div hx-swap-oob="true" id="cart-list">
        <div class="">
            <h2 class="section-subtitle"><?= t('cart.shopping_cart') ?></h2>
            <div class="cart-grid">
                <div class="cart-header" style="grid-template-columns: 2fr 1fr 1fr 1fr auto;">
                    <div class="header-cell">Product</div>
                    <div class="header-cell">Price</div>
                    <div class="header-cell">Qty</div>
                    <div class="header-cell">Total</div>
                    <div class="header-cell"></div>
                </div>
                <div class="cart-body">
                <?php foreach ($cart as $item): ?>
                    <div class="cart-row" id="cart-item-<?= $item['product_id'] ?>" style="grid-template-columns: 2fr 1fr 1fr 1fr auto;">
                        <div class="cart-cell" data-label="Product">
                            <div class="product-name"><?= htmlspecialchars($item['name']) ?></div>
                        </div>
                        <div class="cart-cell" data-label="Price">$<?= number_format($item['price'], 2) ?></div>
                        <div class="cart-cell" data-label="Qty">
                            <div class="quantity-controls">
                                <form hx-post="<?= url('cart/update-quantity') ?>" hx-swap="outerHTML" class="quantity-form">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <button type="submit" name="action" value="decrease" class="qty-btn">-</button>
                                    <span id="quantity-<?= $item['id'] ?>" class="quantity-display"><?= $item['quantity'] ?></span>
                                    <button type="submit" name="action" value="increase" class="qty-btn">+</button>
                                </form>
                            </div>
                        </div>
                        <div class="cart-cell" data-label="Total">
                            <span class="item-total">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </div>
                        <div class="cart-cell" data-label="Action">
                            <form hx-post="<?= url('cart/remove') ?>" hx-swap="outerHTML" class="remove-form">
                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                <button type="submit" class="btn text red"><?= t('cart.remove_all') ?></button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Cart Summary -->
        <div class="">
            <div class="cart-summary">
                <div class="summary-row">
                    <span class="summary-label">Subtotal:</span>
                    <span class="summary-value">$<?= number_format($cart_total, 2) ?></span>
                </div>
                <div class="summary-row total">
                    <span class="summary-label">Total:</span>
                    <span class="summary-value">$<?= number_format($cart_total, 2) ?></span>
                </div>
                <div class="cart-actions">
                    <a href="/products" class="btn black">Continue Shopping</a>
                    <a href="/checkout" class="btn blue">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
