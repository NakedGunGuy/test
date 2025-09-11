<div id="cart-badge" hx-swap-oob="true">
    <a href="/cart" class="cart-badge-link">
        <span class="cart-badge-icon">🛒</span>
        <span class="cart-badge-text">Cart</span>
        <?php $total = array_sum(array_column($cart, 'quantity')); if ($total > 0): ?>
        <span class="cart-badge-count"><?= $total ?></span>
        <?php endif; ?>
    </a>
</div>

