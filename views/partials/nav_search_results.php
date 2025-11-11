<?php if (empty($products)): ?>
    <div class="no-results" style="padding: 2rem; text-align: center; color: #666;">
        <?= t('products.no_results_found') ?>
    </div>
<?php else: ?>
    <?php foreach ($products as $product): ?>
    <a
        href="<?= url('product/' . $product['id']) ?>"
        class="search-result-item"
        style="display: block; padding: 12px 16px; cursor: pointer; border-bottom: 1px solid rgba(0, 174, 239, 0.15); transition: all 0.2s ease; text-decoration: none;"
        onmouseover="this.style.background='rgba(0, 174, 239, 0.1)'"
        onmouseout="this.style.background='transparent'"
        onclick="document.getElementById('dialog').close()"
    >
        <div style="display: flex; align-items: center; gap: 12px;">
            <?php if (!empty($product['image_url'])): ?>
                <img
                    src="<?= htmlspecialchars($product['image_url']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    style="width: 40px; height: 56px; object-fit: cover; border-radius: 4px;"
                >
            <?php endif; ?>
            <div style="flex: 1;">
                <div style="font-weight: 600; color: white; margin-bottom: 4px;">
                    <?= htmlspecialchars($product['name']) ?>
                </div>
                <div style="font-size: 0.85rem; color: #999;">
                    <?= htmlspecialchars($product['set_name'] ?? '') ?>
                    <?php if ($product['is_foil']): ?>
                        <span style="color: #FFB800; margin-left: 8px;">✦ Foil</span>
                    <?php endif; ?>
                </div>
            </div>
            <div style="font-weight: 600; color: #00AEEF;">
                €<?= number_format($product['price'], 2) ?>
            </div>
        </div>
    </a>
    <?php endforeach; ?>

    <div style="padding: 12px 16px; text-align: center; border-top: 1px solid rgba(0, 174, 239, 0.3); margin-top: 8px;">
        <a href="<?= url('discover?name=' . urlencode($_GET['name'] ?? '')) ?>"
           class="btn blue"
           style="display: inline-block;"
           onclick="document.getElementById('dialog').close()">
            <?= t('products.view_all_results') ?>
        </a>
    </div>
<?php endif; ?>
