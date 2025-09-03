<tr id="product-<?= $product['id'] ?>" hx-swap-oob="true">
    <td><?= htmlspecialchars($product['name']); ?></td>
    <td><?= htmlspecialchars($product['set_name']); ?></td>
    <td><?= htmlspecialchars($product['price']); ?>€</td>
    <td>
        <div id="quantity-<?= $product['id'] ?>"><?= $product['quantity'] ?></div>
    </td>
    <td><?= $product['is_foil'] ? 'Yes' : 'No'; ?></td>
    <td>
        <form
                hx-post="/cart/add"
                hx-swap="outerHTML">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>">
            <button type="submit">Add to Cart</button>
        </form>
    </td>
</tr>
