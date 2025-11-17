<?php partial('partials/dialog_header', [
    'title' => t('order.order_number', ['id' => $order['id']]),
]) ?>

<div class="dialog-content">
<div class="order-details-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <p style="color: #999;"><?= t('order.placed_on') ?> <?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
    </div>
    <span class="status-badge status-<?= $order['status'] ?>" style="padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600;">
        <?= t('status.' . $order['status']) ?>
    </span>
</div>

<!-- Order Items -->
<div class="order-items-section" style="margin-bottom: 1.5rem;">
    <h5 style="color: #fff; margin-bottom: 1rem; font-size: 1.1rem;"><?= t('order.items_ordered') ?></h5>
    <div class="order-items-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
        <?php foreach ($order['items'] as $item): ?>
            <div class="order-item" style="display: flex; justify-content: space-between; padding: 1rem; background: rgba(0, 174, 239, 0.05); border-radius: 8px; border: 1px solid rgba(0, 174, 239, 0.15);">
                <div class="item-info">
                    <h6 style="color: #fff; margin: 0 0 0.25rem 0; font-size: 0.95rem;">
                        <?php if ($item['card_name']): ?>
                            <?= htmlspecialchars($item['card_name']) ?>
                            <?php if ($item['set_name']): ?>
                                <span class="item-meta" style="color: #999; font-weight: normal;"> (<?= htmlspecialchars($item['set_name']) ?>)</span>
                            <?php endif; ?>
                            <?php if ($item['collector_number']): ?>
                                <span class="item-meta" style="color: #999; font-weight: normal;"> #<?= htmlspecialchars($item['collector_number']) ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?= htmlspecialchars($item['product_name']) ?>
                        <?php endif; ?>
                    </h6>
                    <p class="quantity" style="color: #999; margin: 0; font-size: 0.85rem;"><?= t('order.quantity') ?>: <?= $item['quantity'] ?></p>
                </div>
                <div class="item-price" style="color: #00AEEF; font-weight: 600; white-space: nowrap; margin-left: 1rem;">
                    €<?= number_format($item['price'] * $item['quantity'], 2) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Order Total -->
<div class="order-total-section" style="display: flex; justify-content: space-between; padding: 1rem; background: rgba(0, 174, 239, 0.1); border-radius: 8px; font-size: 1.2rem; font-weight: 600; margin-bottom: 1.5rem;">
    <span style="color: #fff;"><?= t('cart.total') ?></span>
    <span style="color: #00AEEF;">€<?= number_format($order['total_amount'], 2) ?></span>
</div>

<!-- Tracking Number -->
<?php if (!empty($order['tracking_number'])): ?>
    <div class="order-section" style="margin-bottom: 1.5rem;">
        <h5 style="color: #fff; margin-bottom: 0.5rem; font-size: 1rem;"><?= t('order.tracking_code') ?></h5>
        <p style="font-family: monospace; background: rgba(0, 174, 239, 0.1); padding: 0.75rem; border-radius: 8px; display: inline-block; color: #00AEEF; border: 1px solid rgba(0, 174, 239, 0.2); margin: 0;">
            <?= htmlspecialchars($order['tracking_number']) ?>
        </p>
    </div>
<?php endif; ?>

<!-- Shipping Address -->
<?php if (!empty($order['shipping_address'])): ?>
    <div class="order-section" style="margin-bottom: 1.5rem;">
        <h5 style="color: #fff; margin-bottom: 0.5rem; font-size: 1rem;"><?= t('order.shipping_address') ?></h5>
        <?php
        // Try to decode JSON if it's JSON, otherwise display as-is
        $address = $order['shipping_address'];
        $address_data = json_decode($address, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($address_data)) {
            // It's JSON, format it nicely
            echo '<p style="color: #C0C0D1; line-height: 1.6; margin: 0;">';
            if (!empty($address_data['name'])) {
                echo htmlspecialchars($address_data['name']) . '<br>';
            }
            if (!empty($address_data['address'])) {
                echo htmlspecialchars($address_data['address']) . '<br>';
            }
            if (!empty($address_data['city'])) {
                echo htmlspecialchars($address_data['city']);
            }
            if (!empty($address_data['postal_code'])) {
                echo ' ' . htmlspecialchars($address_data['postal_code']);
            }
            if (!empty($address_data['country'])) {
                echo '<br>' . htmlspecialchars($address_data['country']);
            }
            echo '</p>';
        } else {
            // It's plain text
            echo '<p style="color: #C0C0D1; line-height: 1.6; margin: 0;">' . nl2br(htmlspecialchars($address)) . '</p>';
        }
        ?>
    </div>
<?php endif; ?>

<!-- Notes -->
<?php if (!empty($order['notes'])): ?>
    <div class="order-section" style="margin-bottom: 1.5rem;">
        <h5 style="color: #fff; margin-bottom: 0.5rem; font-size: 1rem;"><?= t('order.notes') ?></h5>
        <p style="color: #C0C0D1; line-height: 1.6; margin: 0;"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
    </div>
<?php endif; ?>
</div>