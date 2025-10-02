<div class="order-details-header">
    <div>
        <h4>Order #<?= $order['id'] ?></h4>
        <p>Placed on <?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
    </div>
    <span class="status-badge status-<?= $order['status'] ?>">
        <?= ucfirst($order['status']) ?>
    </span>
</div>

<!-- Order Items -->
<div class="order-items-section">
    <h5>Items Ordered</h5>
    <div class="order-items-list">
        <?php foreach ($order['items'] as $item): ?>
            <div class="order-item">
                <div class="item-info">
                    <h6>
                        <?php if ($item['card_name']): ?>
                            <?= htmlspecialchars($item['card_name']) ?>
                            <?php if ($item['set_name']): ?>
                                <span class="item-meta">(<?= htmlspecialchars($item['set_name']) ?>)</span>
                            <?php endif; ?>
                            <?php if ($item['collector_number']): ?>
                                <span class="item-meta">#<?= htmlspecialchars($item['collector_number']) ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?= htmlspecialchars($item['product_name']) ?>
                        <?php endif; ?>
                    </h6>
                    <p class="quantity">Quantity: <?= $item['quantity'] ?></p>
                </div>
                <div class="item-price">
                    €<?= number_format($item['price'] * $item['quantity'], 2) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Order Total -->
<div class="order-total-section">
    <span>Total</span>
    <span>€<?= number_format($order['total_amount'], 2) ?></span>
</div>

<!-- Shipping Address -->
<?php if (!empty($order['shipping_address'])): ?>
    <div class="order-section">
        <h5>Shipping Address</h5>
        <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
    </div>
<?php endif; ?>

<!-- Notes -->
<?php if (!empty($order['notes'])): ?>
    <div class="order-section">
        <h5>Order Notes</h5>
        <p><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
    </div>
<?php endif; ?>