<?php start_section('title'); ?>
Order #<?= $order['id'] ?> - Admin - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<!-- Page Header -->
<div class="section" style="margin-bottom: 2rem;">
    <a href="/admin/orders" class="btn text back">← Back to Orders</a>
    <h1 class="section-title" style="margin-top: 0;">Order #<?= $order['id'] ?></h1>
    <p style="color: #C0C0D1;">Order placed on <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></p>
</div>

<!-- Order Status Banner -->
<div class="section" style="margin-bottom: 2rem;">
    <div class="order-status-banner status-<?= $order['status'] ?>">
        <div class="status-info">
            <div class="status-icon">
                <?php if ($order['status'] === 'pending'): ?>📋
                <?php elseif ($order['status'] === 'processing'): ?>⚙️
                <?php elseif ($order['status'] === 'shipped'): ?>🚚
                <?php elseif ($order['status'] === 'delivered'): ?>✅
                <?php elseif ($order['status'] === 'cancelled'): ?>❌
                <?php endif; ?>
            </div>
            <div>
                <div class="status-title">Status: <?= ucfirst($order['status']) ?></div>
                <div class="status-desc">
                    <?php if ($order['status'] === 'pending'): ?>Order received and awaiting processing
                    <?php elseif ($order['status'] === 'processing'): ?>Order is being prepared for shipment
                    <?php elseif ($order['status'] === 'shipped'): ?>Order has been shipped
                    <?php elseif ($order['status'] === 'delivered'): ?>Order has been delivered
                    <?php elseif ($order['status'] === 'cancelled'): ?>Order has been cancelled
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="status-actions">
            <form style="display: flex; gap: 10px; align-items: center;" 
                  hx-post="/admin/orders/<?= $order['id'] ?>/status" 
                  hx-trigger="submit"
                  data-toast="Order status updated!">
                <select name="status" class="form-input" style="padding: 8px 12px;" onchange="toggleTrackingField(this)">
                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <input type="text" 
                       name="tracking_number" 
                       id="tracking_field" 
                       placeholder="Tracking number (optional)" 
                       class="form-input" 
                       style="padding: 8px 12px; display: <?= $order['status'] === 'shipped' ? 'block' : 'none' ?>;">
                <button type="submit" class="btn btn-small blue">Update</button>
            </form>
            
            <script>
                function toggleTrackingField(select) {
                    const trackingField = document.getElementById('tracking_field');
                    if (select.value === 'shipped') {
                        trackingField.style.display = 'block';
                        trackingField.focus();
                    } else {
                        trackingField.style.display = 'none';
                        trackingField.value = '';
                    }
                }
            </script>
        </div>
    </div>
</div>

<div class="order-layout">
    <!-- Customer Information -->
    <div class="order-section">
        <h2 class="section-subtitle">Customer Information</h2>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span class="info-value"><?= htmlspecialchars($order['username']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?= htmlspecialchars($order['email']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Total:</span>
                <span class="info-value total">€<?= number_format($order['total_amount'], 2) ?></span>
            </div>
        </div>
    </div>

    <!-- Shipping Information -->
    <?php if ($shipping_address): ?>
    <div class="order-section">
        <h2 class="section-subtitle">Shipping Address</h2>
        <div class="info-card">
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value"><?= htmlspecialchars($shipping_address['full_name'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?= htmlspecialchars($shipping_address['email'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value"><?= htmlspecialchars($shipping_address['address'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">City:</span>
                <span class="info-value"><?= htmlspecialchars($shipping_address['city'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">State:</span>
                <span class="info-value"><?= htmlspecialchars($shipping_address['state'] ?? '') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">ZIP:</span>
                <span class="info-value"><?= htmlspecialchars($shipping_address['zip'] ?? '') ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Items to Ship -->
<div class="section">
    <h2 class="section-subtitle">Items to Ship (<?= count($items) ?> product<?= count($items) != 1 ? 's' : '' ?>)</h2>
    
    <div class="shipping-list">
        <?php foreach ($items as $item): ?>
            <div class="shipping-item">
                <div class="item-info">
                    <div class="item-name">
                        <?= htmlspecialchars($item['name']) ?>
                        <?php if ($item['card_name']): ?>
                            <div class="item-details">
                                <?= htmlspecialchars($item['card_name']) ?>
                                <?php if ($item['set_name']): ?>
                                    - <?= htmlspecialchars($item['set_name']) ?>
                                <?php endif; ?>
                                <?php if ($item['collector_number']): ?>
                                    #<?= htmlspecialchars($item['collector_number']) ?>
                                <?php endif; ?>
                                <?php if ($item['is_foil']): ?>
                                    <span class="foil-badge">✨ Foil</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="item-quantity">
                    <div class="quantity-badge">×<?= $item['quantity'] ?></div>
                    <div class="item-price">€<?= number_format($item['price'], 2) ?> each</div>
                </div>
                
                <div class="item-total">
                    €<?= number_format($item['price'] * $item['quantity'], 2) ?>
                </div>
                
                <?php if ($item['current_product_name']): ?>
                    <div class="stock-info">
                        <div class="stock-status <?= $item['current_stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                            <?= $item['current_stock'] > 0 ? "✅ {$item['current_stock']} in stock" : '❌ Out of stock' ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="stock-info">
                        <div class="stock-status deleted">⚠️ Product deleted</div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="order-summary">
        <div class="summary-total">
            <span>Total: €<?= number_format($order['total_amount'], 2) ?></span>
        </div>
    </div>
</div>

<style>
.order-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.order-section h2 {
    margin-bottom: 15px;
}

.info-card {
    background: #1E1E27;
    border: 1px solid #C0C0D133;
    border-radius: 12px;
    padding: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #C0C0D133;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: #C0C0D1;
    font-weight: 500;
}

.info-value {
    color: #fff;
    font-weight: 600;
}

.info-value.total {
    color: #01AFFC;
    font-size: 18px;
}

.order-status-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-radius: 12px;
    border: 2px solid;
}

.order-status-banner.status-pending {
    background: rgba(255, 193, 7, 0.1);
    border-color: #FFC107;
    color: #FFC107;
}

.order-status-banner.status-processing {
    background: rgba(23, 162, 184, 0.1);
    border-color: #17A2B8;
    color: #17A2B8;
}

.order-status-banner.status-shipped {
    background: rgba(40, 167, 69, 0.1);
    border-color: #28A745;
    color: #28A745;
}

.order-status-banner.status-delivered {
    background: rgba(40, 167, 69, 0.1);
    border-color: #28A745;
    color: #28A745;
}

.order-status-banner.status-cancelled {
    background: rgba(220, 53, 69, 0.1);
    border-color: #DC3545;
    color: #DC3545;
}

.status-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.status-icon {
    font-size: 24px;
}

.status-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 4px;
}

.status-desc {
    font-size: 14px;
    opacity: 0.8;
}

.shipping-list {
    background: #1E1E27;
    border: 1px solid #C0C0D133;
    border-radius: 12px;
    overflow: hidden;
}

.shipping-item {
    display: grid;
    grid-template-columns: 1fr auto auto auto;
    gap: 20px;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #C0C0D133;
}

.shipping-item:last-child {
    border-bottom: none;
}

.item-name {
    font-weight: 600;
    color: #fff;
}

.item-details {
    font-size: 14px;
    color: #C0C0D1;
    margin-top: 4px;
}

.foil-badge {
    background: linear-gradient(45deg, #FFD700, #FFA500, #FFD700);
    color: #000;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 8px;
}

.quantity-badge {
    background: #01AFFC;
    color: #fff;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 4px;
}

.item-price {
    font-size: 12px;
    color: #C0C0D1;
    text-align: center;
}

.item-total {
    font-weight: 600;
    color: #01AFFC;
    font-size: 16px;
}

.stock-status {
    font-size: 12px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
    text-align: center;
}

.stock-status.in-stock {
    background: rgba(40, 167, 69, 0.2);
    color: #28A745;
}

.stock-status.out-of-stock {
    background: rgba(220, 53, 69, 0.2);
    color: #DC3545;
}

.stock-status.deleted {
    background: rgba(255, 193, 7, 0.2);
    color: #FFC107;
}

.order-summary {
    background: #1E1E27;
    border: 1px solid #C0C0D133;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.summary-total {
    text-align: right;
    font-size: 20px;
    font-weight: 600;
    color: #01AFFC;
}

@media (max-width: 768px) {
    .order-layout {
        grid-template-columns: 1fr;
    }
    
    .order-status-banner {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .shipping-item {
        grid-template-columns: 1fr;
        gap: 10px;
        text-align: center;
    }
    
    .item-total {
        font-size: 18px;
        border-top: 1px solid #C0C0D133;
        padding-top: 10px;
        margin-top: 10px;
    }
}
</style>