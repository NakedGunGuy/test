<?php /** @var array $product */ ?>
<?php /** @var array $order_history */ ?>
<?php /** @var array $card_variants */ ?>
<?php /** @var array $seo_data */ ?>
<?php /** @var array $schemas */ ?>

<?php if (isset($seo_data)): ?>
    <?php start_section('seo_data'); echo serialize($seo_data); end_section('seo_data'); ?>
<?php endif; ?>

<?php if (isset($schemas)): ?>
    <?php start_section('schemas'); echo serialize($schemas); end_section('schemas'); ?>
<?php endif; ?>

<?php start_section('page_title'); ?>Product Details<?php end_section('page_title'); ?>

<div class="product-container" style="grid-template-columns: 2fr 1fr;">
    <!-- Product Image & Info -->
    <div>
        <h1 class="product-title"><?= htmlspecialchars($product['name'] ?? $product['card_name']) ?></h1>
        
        <?php if (!$product['is_custom']): ?>
            <div class="product-section product-image-row">
<?php
                    $svg = '
                    <svg width="250" height="349" xmlns="http://www.w3.org/2000/svg">
                      <rect width="250" height="349" rx="12" ry="12" fill="rgb(39, 39, 39)" />
                    </svg>';
                    $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
                    ?>
                <div class="product-image-container">
                    <img 
                        width="250" 
                        height="349" 
                        src="<?= $dataUri ?>" 
                        alt="<?= htmlspecialchars($product['name'] ?? $product['card_name']) ?>" 
                        data-src="<?= card_image($product['edition_slug']) ?>"
                    />
                </div>
                <div class="product-meta-container">
                    <div class="meta-item">
                        <div class="meta-label">Set</div>
                        <div><?= htmlspecialchars($product['set_name']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Number</div>
                        <div><?= htmlspecialchars($product['edition_number']) ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Rarity</div>
                        <div><?= htmlspecialchars($product['rarity'] ?? 'N/A') ?></div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Foil</div>
                        <div><?= $product['is_foil'] ? 'Yes' : 'No' ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($product['description']): ?>
            <div class="product-section">
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Description</h3>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Purchase Section -->
    <div>
        <?php partial('shop/partials/product_purchase_section', ['product' => $product]); ?>
        
        <div class="action-buttons">
            <a href="/discover" class="btn-outline">Continue Shopping</a>
            <a href="/cart" class="btn black" style="flex: 1; text-align: center;">View Cart</a>
        </div>
    </div>
</div>

<!-- Order History Section -->
<?php if (!empty($order_history)): ?>
    <h2 class="section-title">Recent Orders</h2>
    <table>
        <thead>
            <tr>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_history as $order): ?>
                <tr>
                    <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                    <td><?= $order['quantity'] ?></td>
                    <td>$<?= number_format($order['price'], 2) ?></td>
                    <td>
                        <span style="color: <?= $order['status'] === 'paid' ? '#01AFFC' : '#FFB800' ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Card Variants Section -->
<?php if (!empty($card_variants)): ?>
    <h2 class="section-title">Other Editions & Variants</h2>
    <div class="variants-grid">
        <?php foreach ($card_variants as $variant): ?>
            <div class="variant-card">
                <h3 class="variant-title">
                    <a href="/product/<?= $variant['id'] ?>">
                        <?= htmlspecialchars($variant['set_name']) ?> #<?= htmlspecialchars($variant['edition_number']) ?>
                    </a>
                </h3>
                <div class="variant-meta">
                    <div class="variant-meta-item">Rarity: <?= htmlspecialchars($variant['rarity'] ?? 'N/A') ?></div>
                    <div class="variant-meta-item">Foil: <?= $variant['is_foil'] ? 'Yes' : 'No' ?></div>
                    <div class="variant-meta-item">Price: <span class="variant-price">$<?= number_format($variant['price'], 2) ?></span></div>
                    <div class="variant-meta-item">Stock: <?= $variant['quantity'] > 0 ? $variant['quantity'] . ' available' : 'Out of stock' ?></div>
                </div>
                <a href="/product/<?= $variant['id'] ?>" class="btn blue btn-small">
                    View Details
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal Dialog -->
<dialog id="dialog" class="dialog"></dialog>