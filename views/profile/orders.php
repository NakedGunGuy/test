<?php start_section('title'); ?>
Order History - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<div class="container wide">
    <div class="container profile">
        <!-- Navigation & Stats -->
        <div>
            <!-- Back Navigation -->
            <div class="section">
                <a href="<?= url('profile') ?>" class="btn text back">← Back to Profile</a>
                <h1 class="product-title">Order History</h1>
                <p class="user-email">Track your purchases and order status</p>
            </div>

            <!-- Order Filters/Stats -->
            <div class="section">
                <h3 class="section-subtitle">Order Summary</h3>
                <div class="grid orders" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                    <div class="stat order">
                        <span class="number"><?= count($orders) ?></span>
                        <span class="label">Total Orders</span>
                    </div>
                    <?php if (!empty($orders)): ?>
                    <div class="stat order">
                        <span class="number">$<?= number_format(array_sum(array_column($orders, 'total_amount')), 2) ?></span>
                        <span class="label">Total Spent</span>
                    </div>
                    <div class="stat order">
                        <?php 
                        $recent_order = $orders[0] ?? null;
                        $status = $recent_order ? ucfirst($recent_order['status']) : 'None';
                        ?>
                        <span class="number"><?= $status ?></span>
                        <span class="label">Latest Status</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="section">
        <h2 class="section-title">Your Orders</h2>
        
        <?php if (empty($orders)): ?>
            <div class="empty">
                <div class="icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h3><?= t('profile.no_orders_yet') ?></h3>
                <p><?= t('profile.start_shopping') ?></p>
                <a href="<?= url('discover') ?>" class="btn blue"><?= t('button.browse_products') ?></a>
            </div>
        <?php else: ?>
            <div class="grid orders list" style="grid-template-columns: 1fr;">
                <?php foreach ($orders as $order): ?>
                    <div class="card order">
                        <div class="header">
                            <div class="info">
                                <h4><?= t('profile.order_number', ['id' => $order['id']]) ?></h4>
                                <p><?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
                            </div>
                            <span class="badge status status-<?= $order['status'] ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                        
                        <div class="details">
                            <div class="summary">
                                <?= $order['item_count'] ?? 0 ?> item<?= ($order['item_count'] ?? 0) !== 1 ? 's' : '' ?>
                            </div>
                            <div class="total">
                                $<?= number_format($order['total_amount'] ?? 0, 2) ?>
                            </div>
                        </div>
                        
                        <div class="footer">
                            <div class="status-<?= $order['status'] ?>">
                                <?php if ($order['status'] === 'delivered'): ?>
                                    <span class="status-text">✓ Delivered</span>
                                <?php elseif ($order['status'] === 'shipped'): ?>
                                    <span class="status-text">📦 Shipped</span>
                                <?php elseif ($order['status'] === 'processing'): ?>
                                    <span class="status-text">⏳ Processing</span>
                                <?php else: ?>
                                    <span class="status-text">📋 Pending</span>
                                <?php endif; ?>
                            </div>
                            
                            <span class="status-text">Order #<?= $order['id'] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        
    </div>
    </div>
</div>