<?php start_section('title'); ?>
Admin Dashboard - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<!-- Quick Stats -->
<div class="section">
    <h2 class="section-subtitle">Store Overview</h2>
    <div class="grid stats" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="card stat">
            <?= icon('package') ?>
            <div class="info">
                <div class="number">
                    <?= number_format($total_products ?? 0) ?>
                    <?php if (($products_in_carts ?? 0) > 0): ?>
                        <span style="color: #FFB800; font-size: 0.8em;">(+<?= number_format($products_in_carts) ?>)</span>
                    <?php endif; ?>
                </div>
                <div class="label">
                    Products in Stock
                    <?php if (($products_in_carts ?? 0) > 0): ?>
                        <div style="font-size: 10px; color: #C0C0D1; margin-top: 2px;">
                            <?= number_format($products_in_carts) ?> in carts
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card stat">
            <?= icon('bar-chart') ?>
            <div class="info">
                <div class="number">
                    <?= number_format($total_units_available ?? 0) ?>
                    <?php if (($total_units_in_carts ?? 0) > 0): ?>
                        <span style="color: #FFB800; font-size: 0.8em;">(+<?= number_format($total_units_in_carts) ?>)</span>
                    <?php endif; ?>
                </div>
                <div class="label">
                    Total Units
                    <?php if (($total_units_in_carts ?? 0) > 0): ?>
                        <div style="font-size: 10px; color: #C0C0D1; margin-top: 2px;">
                            <?= number_format($total_units_in_carts) ?> in carts
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card stat">
            <?= icon('clipboard') ?>
            <div class="info">
                <div class="number"><?= $pending_orders ?? 0 ?></div>
                <div class="label">Pending Orders</div>
            </div>
        </div>
        <div class="card stat">
            <?= icon('alert-triangle') ?>
            <div class="info">
                <div class="number"><?= $low_stock ?? 0 ?></div>
                <div class="label">Low Stock Items</div>
            </div>
        </div>
        <div class="card stat">
            <?= icon('dollar-sign') ?>
            <div class="info">
                <div class="number">€<?= number_format($revenue ?? 0, 2) ?></div>
                <div class="label">Monthly Revenue</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="section">
    <h2 class="section-subtitle">Quick Actions</h2>
    <div class="grid actions" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <a href="<?= url('admin/products') ?>" class="card action">
            <?= icon('package') ?>
            <div class="content">
                <div class="title">Manage Products</div>
                <div class="desc">Add, edit, and manage your product inventory</div>
            </div>
            <span class="arrow">→</span>
        </a>

        <a href="<?= url('admin/orders') ?>" class="card action">
            <?= icon('clipboard') ?>
            <div class="content">
                <div class="title">View Orders</div>
                <div class="desc">Process and track customer orders</div>
            </div>
            <span class="arrow">→</span>
        </a>

        <a href="<?= url('admin/analytics') ?>" class="card action">
            <?= icon('bar-chart') ?>
            <div class="content">
                <div class="title">Analytics</div>
                <div class="desc">View sales reports and store metrics</div>
            </div>
            <span class="arrow">→</span>
        </a>

        <a href="<?= url('admin/settings') ?>" class="card action">
            <?= icon('settings') ?>
            <div class="content">
                <div class="title">Store Settings</div>
                <div class="desc">Configure store settings and preferences</div>
            </div>
            <span class="arrow">→</span>
        </a>
    </div>
</div>

