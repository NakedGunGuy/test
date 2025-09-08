<div id="cart-badge" hx-swap-oob="true">
    <a href="/cart">🛒 Cart
        <span><?= array_sum(array_column($cart, 'quantity')); ?></span>
    </a>
</div>

