<?php if (empty($orders)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
        </div>
        <h3>No orders yet</h3>
        <p>Start shopping to see your orders here.</p>
        <a href="/discover" class="btn blue">Browse Products</a>
    </div>
<?php else: ?>
    <div class="orders-list">
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-info">
                        <h4>Order #<?= $order['id'] ?></h4>
                        <p><?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
                    </div>
                    <span class="status-badge status-<?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                
                <div class="order-details">
                    <div class="order-summary">
                        <?= $order['item_count'] ?? 0 ?> item<?= ($order['item_count'] ?? 0) !== 1 ? 's' : '' ?>
                    </div>
                    <div class="order-total">
                        $<?= number_format($order['total_amount'] ?? 0, 2) ?>
                    </div>
                </div>
                
                <div class="order-footer">
                    <div class="status-<?= $order['status'] ?>">
                        <?php if ($order['status'] === 'delivered'): ?>
                            <span class="order-status-text">✓ Delivered</span>
                        <?php elseif ($order['status'] === 'shipped'): ?>
                            <span class="order-status-text">📦 Shipped</span>
                        <?php elseif ($order['status'] === 'processing'): ?>
                            <span class="order-status-text">⏳ Processing</span>
                        <?php else: ?>
                            <span class="order-status-text">📋 Pending</span>
                        <?php endif; ?>
                    </div>
                    
                    <button 
                        class="btn-text"
                        hx-get="/profile/order/<?= $order['id'] ?>"
                        hx-target="#order-details-content"
                        onclick="document.getElementById('order-details-modal').classList.remove('hidden')"
                    >
                        View Details →
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Order Details Modal -->
    <div id="order-details-modal" class="modal-overlay hidden">
        <div class="modal">
            <div class="modal-header">
                <h3>Order Details</h3>
                <button 
                    class="modal-close"
                    onclick="document.getElementById('order-details-modal').classList.add('hidden')"
                >
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-content" id="order-details-content">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
<?php endif; ?>