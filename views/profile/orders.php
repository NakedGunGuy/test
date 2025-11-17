<?php start_section('title'); ?>
Order History - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<div class="container wide">
    <div class="container profile">
        <!-- Navigation & Stats -->
        <div>
            <!-- Back Navigation -->
            <div class="section">
                <a href="<?= url('profile') ?>" class="btn text back"><?= t('profile.back_to_profile') ?></a>
                <h1 class="product-title"><?= t('profile.order_history') ?></h1>
                <p class="user-email"><?= t('profile.track_purchases') ?></p>
            </div>

            <!-- Order Filters/Stats -->
            <div class="section">
                <h3 class="section-subtitle"><?= t('profile.order_summary') ?></h3>
                <div class="grid orders" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                    <div class="stat order">
                        <span class="number"><?= count($orders) ?></span>
                        <span class="label"><?= t('profile.total_orders') ?></span>
                    </div>
                    <?php if (!empty($orders)): ?>
                    <div class="stat order">
                        <span class="number">€<?= number_format(array_sum(array_column($orders, 'total_amount')), 2) ?></span>
                        <span class="label"><?= t('profile.total_spent') ?></span>
                    </div>
                    <div class="stat order">
                        <?php
                        $recent_order = $orders[0] ?? null;
                        $status = $recent_order ? t('status.' . $recent_order['status']) : 'None';
                        ?>
                        <span class="number"><?= $status ?></span>
                        <span class="label"><?= t('profile.latest_status') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="section">
        <h2 class="section-title"><?= t('profile.your_orders') ?></h2>

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
                    <div class="card order"
                         onclick="loadOrderDetails(<?= $order['id'] ?>)"
                         style="cursor: pointer;">
                        <div class="header">
                            <div class="info">
                                <h4><?= t('profile.order_number', ['id' => $order['id']]) ?></h4>
                                <p><?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
                            </div>
                            <span class="badge status status-<?= $order['status'] ?>">
                                <?= t('status.' . $order['status']) ?>
                            </span>
                        </div>

                        <div class="details">
                            <div class="summary">
                                <?php
                                $item_count = $order['item_count'] ?? 0;
                                $item_text = pluralize(
                                    $item_count,
                                    t('profile.item'),           // 1 artikel / item
                                    t('profile.item_dual'),      // 2 artikla
                                    get_current_language() === 'si'
                                        ? (($item_count % 100 == 3 || $item_count % 100 == 4)
                                            ? t('profile.item_plural_few')   // 3, 4 artikli
                                            : t('profile.item_plural_many')) // 5+ artiklov
                                        : t('profile.item') . 's'            // items (English)
                                );
                                ?>
                                <?= $item_count ?> <?= $item_text ?>
                            </div>
                            <div class="total">
                                €<?= number_format($order['total_amount'] ?? 0, 2) ?>
                            </div>
                        </div>

                        <div class="footer">
                            <div class="status-<?= $order['status'] ?>">
                                <span class="status-text"><?= t('status.' . $order['status'] . '_icon') ?></span>
                            </div>

                            <span style="color: #00AEEF; font-size: 0.9rem;">
                                <?= t('order.view_details') ?> →
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>


    </div>
    </div>
</div>

<?php start_section('js'); ?>
<script>
function loadOrderDetails(orderId) {
    const dialog = document.getElementById('dialog');
    const url = '<?= url('profile/order/') ?>' + orderId;

    console.log('Fetching order details from:', url);

    // Fetch order details
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.text();
        })
        .then(html => {
            console.log('Order details loaded successfully');
            dialog.innerHTML = html;
            dialog.showModal();
        })
        .catch(error => {
            console.error('Error loading order details:', error);
            alert('Failed to load order details: ' + error.message);
        });
}
</script>
<?php end_section('js'); ?>