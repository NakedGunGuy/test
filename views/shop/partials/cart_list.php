<?php if (empty($cart)): ?>
    <p hx-swap-oob="true" id="cart-list">Your cart is empty.</p>
<?php else: ?>
    <table class="w-full text-left border" hx-swap-oob="true" id="cart-list">
        <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cart as $item): ?>
            <tr id="cart-item-<?= $item['product_id'] ?>">
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td><div id="quantity-<?= $item['id'] ?>"><?= $item['quantity'] ?></div></td>
                <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                <td>
                    <form
                        hx-post="/cart/remove"
                        hx-swap="outerHTML">
                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                        <button type="submit" class="text-red-600">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
