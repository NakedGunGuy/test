<div class="product-row">
    <div class="product-cell">
        <div style="font-weight: 600;"><?= htmlspecialchars($product['name']) ?></div>
        <?php if ($product['is_custom']): ?>
            <div style="font-size: 12px; color: #C0C0D1;">Custom Product</div>
        <?php else: ?>
            <div style="font-size: 12px; color: #C0C0D1;"><?= htmlspecialchars($product['set_name']) ?> #<?= htmlspecialchars($product['edition_number']) ?></div>
        <?php endif; ?>
    </div>
    
    <div class="product-cell">
        <?= htmlspecialchars($product['set_name'] ?? 'Custom') ?>
    </div>
    
    <div class="product-cell">
        <span style="font-weight: 600; color: #01AFFC;">
            $<?= number_format($product['price'], 2) ?>
        </span>
    </div>
    
    <div class="product-cell">
        <div style="font-weight: 600;">
            <?= $product['quantity'] ?>
            <?php if ($product['in_carts'] > 0): ?>
                <span style="color: #FFB800; font-size: 12px;">(+<?= $product['in_carts'] ?>)</span>
            <?php endif; ?>
        </div>
        <?php if ($product['in_carts'] > 0): ?>
            <div style="font-size: 11px; color: #C0C0D1;"><?= $product['in_carts'] ?> in carts</div>
        <?php endif; ?>
    </div>
    
    <div class="product-cell">
        <?php if ($product['is_foil']): ?>
            <span style="color: #FFB800;">✨ Foil</span>
        <?php else: ?>
            <span style="color: #C0C0D1;">Regular</span>
        <?php endif; ?>
    </div>
    
    <div class="product-cell">
        <?php
            if (!empty($actions)) {
                partial('admin/products/partials/product_actions', ['product_id' => $product['id'], 'actions' => $actions]); 
            }
        ?>
    </div>
</div>