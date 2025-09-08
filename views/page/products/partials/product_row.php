<?php
$svg = '
<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg">
  <rect width="40" height="40" rx="12" ry="12" fill="rgb(39, 39, 39)" />
</svg>';
$dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
?>

<tr id="product-<?= $product['id'] ?>" hx-swap-oob="true">
    <td>
        <img 
            hx-get="/cards/image/<?= $product['edition_slug'] ?>"
            hx-target="#dialog"
            hx-trigger="click"
            height="40" width="40" src="<?= $dataUri ?>" alt="SVG circle" data-src="https://api.gatcg.com/cards/images/<?= $product['edition_slug'] ?>.jpg" />
        <span><?= htmlspecialchars($product['name']); ?></span>
    </td>
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
