<?php start_section('title'); ?>
Orders - Admin - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<!-- Page Header -->
<div class="section" style="margin-bottom: 2rem;">
    <a href="/admin" class="btn text back">← Back to Dashboard</a>
    <h1 class="section-title" style="margin-top: 0;">Orders Management</h1>
    <p style="color: #C0C0D1;">Process and track customer orders</p>
</div>

<!-- Order Statistics -->
<div class="section">
    <h2 class="section-subtitle">Order Statistics (Last 30 Days)</h2>
    <div class="grid stats" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="card stat">
            <div class="icon">📋</div>
            <div class="info">
                <div class="number"><?= $stats['total_orders'] ?? 0 ?></div>
                <div class="label">Total Orders</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">⏳</div>
            <div class="info">
                <div class="number"><?= $stats['pending_count'] ?? 0 ?></div>
                <div class="label">Pending</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">⚙️</div>
            <div class="info">
                <div class="number"><?= $stats['processing_count'] ?? 0 ?></div>
                <div class="label">Processing</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">🚚</div>
            <div class="info">
                <div class="number"><?= $stats['shipped_count'] ?? 0 ?></div>
                <div class="label">Shipped</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">✅</div>
            <div class="info">
                <div class="number"><?= $stats['delivered_count'] ?? 0 ?></div>
                <div class="label">Delivered</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">💰</div>
            <div class="info">
                <div class="number">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
                <div class="label">Revenue</div>
            </div>
        </div>
    </div>
</div>

<!-- Orders List -->
<div class="section">
    <h2 class="section-subtitle">Recent Orders</h2>
    
    <?php if (empty($orders)): ?>
        <div class="empty">
            <div class="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3>No orders yet</h3>
            <p>Orders will appear here as customers make purchases.</p>
        </div>
    <?php else: ?>
        <div class="grid">
            <div class="grid-header" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr;">
                <div class="header-cell">Order</div>
                <div class="header-cell">Customer</div>
                <div class="header-cell">Items</div>
                <div class="header-cell">Total</div>
                <div class="header-cell">Status</div>
                <div class="header-cell">Actions</div>
                <div class="header-cell">Details</div>
            </div>
            <div class="grid-body">
                <?php foreach ($orders as $order): ?>
                    <div class="grid-row" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr;">
                        <div class="grid-cell">
                            <div style="font-weight: 600;">#<?= $order['id'] ?></div>
                            <div style="font-size: 12px; color: #C0C0D1; margin-top: 2px;">
                                <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?>
                            </div>
                        </div>
                        
                        <div class="grid-cell">
                            <div style="font-weight: 600;"><?= htmlspecialchars($order['username']) ?></div>
                            <div style="font-size: 12px; color: #C0C0D1; margin-top: 2px;">
                                <?= htmlspecialchars($order['email']) ?>
                            </div>
                        </div>
                        
                        <div class="grid-cell">
                            <?= $order['item_count'] ?> item<?= $order['item_count'] != 1 ? 's' : '' ?>
                        </div>
                        
                        <div class="grid-cell">
                            <span style="font-weight: 600; color: #01AFFC;">
                                $<?= number_format($order['total_amount'], 2) ?>
                            </span>
                        </div>
                        
                        <div class="grid-cell">
                            <span class="badge status status-<?= $order['status'] ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                        
                        <div class="grid-cell">
                            <form style="display: inline;" 
                                  hx-post="/admin/orders/<?= $order['id'] ?>/status" 
                                  hx-trigger="change"
                                  data-toast="Order status updated!">
                                <select name="status" class="form-input" style="width: auto; padding: 4px 8px; font-size: 12px;">
                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </form>
                        </div>
                        
                        <div class="grid-cell">
                            <a href="/admin/orders/<?= $order['id'] ?>" class="btn blue btn-small">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>