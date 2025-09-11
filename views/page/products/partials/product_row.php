<?php
$svg = '
<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg">
  <rect width="40" height="40" rx="12" ry="12" fill="rgb(39, 39, 39)" />
</svg>';
$dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
?>

<div id="product-<?= $product['id'] ?>" class="grid-row" hx-swap-oob="true">
    <div class="grid-cell" style="border-color: rgba(0, 174, 239, 0.15);">
        <img 
            hx-get="/cards/image/<?= $product['edition_slug'] ?>"
            hx-target="#dialog"
            hx-trigger="click"
            height="40" width="40" 
            src="<?= $dataUri ?>" 
            alt="Card image" 
            data-src="https://api.gatcg.com/cards/images/<?= $product['edition_slug'] ?>.jpg" 
        />
        <span>
            <a href="/product/<?= $product['id'] ?>">
                <?= htmlspecialchars($product['name']); ?>
            </a>
        </span>
    </div>
    <div class="grid-cell edition"><?= htmlspecialchars($product['set_name']); ?></div>
    <div class="grid-cell price">€<?= number_format($product['price'], 2); ?></div>
    <div class="grid-cell">
        <div id="quantity-<?= $product['id'] ?>" style="<?= $product['quantity'] > 0 ? ($product['quantity'] <= 5 ? 'color: #FFB800; font-weight: 600;' : 'color: #10b981; font-weight: 600;') : 'color: #FF6B6B; font-weight: 600;' ?>">
            <?= $product['quantity'] > 0 ? $product['quantity'] . ' in stock' : 'Out of stock' ?>
        </div>
    </div>
    <div class="grid-cell" style="border-color: rgba(0, 174, 239, 0.15);">
        <?php if ($product['is_foil']): ?>
            <span>✨ Foil</span>
        <?php else: ?>
            <span style="color: #666; font-weight: 500;">Regular</span>
        <?php endif; ?>
    </div>
    <div class="grid-cell" style="border-color: rgba(0, 174, 239, 0.15);">
        <?php if ($product['quantity'] > 0): ?>
        <form
                hx-post="/cart/add"
                hx-swap="outerHTML"
                class="add-to-cart-form" style="gap: 0.5rem;">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <div class="quantity-selector" style="margin-bottom: 0.5rem;">
                <button type="button" class="qty-btn" onclick="changeQuantity(<?= $product['id'] ?>, -1)" style="background: linear-gradient(145deg, #0a0a0a 0%, #1a1a1a 100%); border-color: rgba(0, 174, 239, 0.3);">-</button>
                <input type="number" id="qty-<?= $product['id'] ?>" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" class="qty-input" readonly style="background: linear-gradient(145deg, #0a0a0a 0%, #1a1a1a 100%); border-color: rgba(0, 174, 239, 0.3);">
                <button type="button" class="qty-btn" onclick="changeQuantity(<?= $product['id'] ?>, 1)" style="background: linear-gradient(145deg, #0a0a0a 0%, #1a1a1a 100%); border-color: rgba(0, 174, 239, 0.3);">+</button>
            </div>
            <button type="submit" class="btn blue btn-small" style="font-size: 0.75rem; padding: 6px 12px;">🛍 Add to Cart</button>
        </form>
        <?php else: ?>
            <span style="color: #666; font-size: 0.875rem; font-style: italic;">Unavailable</span>
        <?php endif; ?>
    </div>
</div>
