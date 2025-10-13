<div class="grid-row" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr;">
    <div class="grid-cell">
        <div style="display: flex; flex-direction: column; gap: 4px; width: 100%;">
            <div style="font-weight: 600;"><?= htmlspecialchars($product['name']) ?></div>
            <?php if ($product['is_custom']): ?>
                <div style="font-size: 12px; color: #C0C0D1;">Custom Product</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid-cell">
        <span style="color: #C0C0D1;"><?= htmlspecialchars($product['set_name']) ?> #<?= htmlspecialchars($product['edition_number']) ?></span>
    </div>

    <div class="grid-cell">
        <span style="font-weight: 700; color: #00AEEF;">
            €<?= number_format($product['price'], 2) ?>
        </span>
    </div>

    <div class="grid-cell">
        <div style="display: flex; align-items: center; gap: 6px;">
            <span style="font-weight: 600;"><?= $product['quantity'] ?></span>
            <?php if ($product['in_carts'] > 0): ?>
                <span style="color: #FFB800; font-size: 12px; font-weight: 600;">(+<?= $product['in_carts'] ?>)</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid-cell">
        <?php if ($product['is_foil']): ?>
            <span style="color: #FFB800; font-weight: 600;">✨ Foil</span>
        <?php else: ?>
            <span style="color: #C0C0D1; font-weight: 500;">Regular</span>
        <?php endif; ?>
    </div>

    <div class="grid-cell actions-cell">
        <?php
            if (!empty($actions)) {
                partial('admin/products/partials/product_actions', ['product_id' => $product['id'], 'actions' => $actions]);
            }
        ?>
    </div>
</div>