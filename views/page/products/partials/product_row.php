<?php
$svg = '
<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg">
  <rect width="40" height="40" rx="12" ry="12" fill="rgb(39, 39, 39)" />
</svg>';
$dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
?>

<div id="product-<?= $product['id'] ?>" class="product-row" hx-swap-oob="true">
    <div class="product-cell">
        <img 
            hx-get="/cards/image/<?= $product['edition_slug'] ?>"
            hx-target="#dialog"
            hx-trigger="click"
            height="40" width="40" src="<?= $dataUri ?>" alt="SVG circle" data-src="https://api.gatcg.com/cards/images/<?= $product['edition_slug'] ?>.jpg" />
        <span><a href="/product/<?= $product['id'] ?>"><?= htmlspecialchars($product['name']); ?></a></span>
    </div>
    <div class="product-cell"><?= htmlspecialchars($product['set_name']); ?></div>
    <div class="product-cell"><?= htmlspecialchars($product['price']); ?>€</div>
    <div class="product-cell">
        <div id="quantity-<?= $product['id'] ?>"><?= $product['quantity'] ?></div>
    </div>
    <div class="product-cell"><?= $product['is_foil'] ? 'Yes' : 'No'; ?></div>
    <div class="product-cell">
        <form
                hx-post="/cart/add"
                hx-swap="outerHTML">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>">
            <button type="submit">Add to Cart</button>
        </form>
    </div>
</div>
