<?php start_section('title'); ?>
Analytics - Admin - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<!-- Page Header -->
<div class="section" style="margin-bottom: 2rem;">
    <a href="/admin" class="btn text back">← Back to Dashboard</a>
    <h1 class="section-title" style="margin-top: 0;">Store Analytics</h1>
    <p style="color: #C0C0D1;">View sales reports and store metrics</p>
</div>

<!-- Overview Statistics -->
<div class="section">
    <h2 class="section-subtitle">Store Overview</h2>
    <div class="grid stats">
        <div class="card stat">
            <div class="icon">📋</div>
            <div class="info">
                <div class="number"><?= number_format($overview['total_orders'] ?? 0) ?></div>
                <div class="label">Total Orders</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">👥</div>
            <div class="info">
                <div class="number"><?= number_format($overview['total_customers'] ?? 0) ?></div>
                <div class="label">Customers</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">📦</div>
            <div class="info">
                <div class="number">
                    <?= number_format($overview['products_in_stock'] ?? 0) ?>
                    <?php if (($overview['products_in_carts'] ?? 0) > 0): ?>
                        <span style="color: #FFB800; font-size: 0.8em;">(+<?= number_format($overview['products_in_carts']) ?>)</span>
                    <?php endif; ?>
                </div>
                <div class="label">
                    Products in Stock
                    <?php if (($overview['products_in_carts'] ?? 0) > 0): ?>
                        <div style="font-size: 10px; color: #C0C0D1; margin-top: 2px;">
                            <?= number_format($overview['products_in_carts']) ?> in carts
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">📊</div>
            <div class="info">
                <div class="number">
                    <?= number_format($overview['total_units_available'] ?? 0) ?>
                    <?php if (($overview['total_units_in_carts'] ?? 0) > 0): ?>
                        <span style="color: #FFB800; font-size: 0.8em;">(+<?= number_format($overview['total_units_in_carts']) ?>)</span>
                    <?php endif; ?>
                </div>
                <div class="label">
                    Total Units
                    <?php if (($overview['total_units_in_carts'] ?? 0) > 0): ?>
                        <div style="font-size: 10px; color: #C0C0D1; margin-top: 2px;">
                            <?= number_format($overview['total_units_in_carts']) ?> in carts
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">⚠️</div>
            <div class="info">
                <div class="number"><?= number_format($overview['low_stock_products'] ?? 0) ?></div>
                <div class="label">Low Stock Items</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">💰</div>
            <div class="info">
                <div class="number">$<?= number_format($overview['total_revenue'] ?? 0, 2) ?></div>
                <div class="label">Total Revenue</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">💳</div>
            <div class="info">
                <div class="number">$<?= number_format($overview['avg_order_value'] ?? 0, 2) ?></div>
                <div class="label">Avg Order Value</div>
            </div>
        </div>
    </div>
</div>

<!-- Daily Sales (Last 30 Days) -->
<div class="section">
    <h2 class="section-subtitle">Daily Sales (Last 30 Days)</h2>
    
    <?php if (empty($daily_sales)): ?>
        <div class="empty">
            <div class="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h3>No sales data</h3>
            <p>Sales data will appear here once orders are placed.</p>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <div class="products-header">
                <div class="header-cell">Date</div>
                <div class="header-cell">Orders</div>
                <div class="header-cell">Revenue</div>
                <div class="header-cell">Avg Order</div>
            </div>
            <div class="products-body">
                <?php foreach ($daily_sales as $day): ?>
                    <div class="product-row">
                        <div class="product-cell">
                            <?= date('M j, Y', strtotime($day['date'])) ?>
                        </div>
                        <div class="product-cell">
                            <span style="font-weight: 600;"><?= $day['orders_count'] ?></span>
                        </div>
                        <div class="product-cell">
                            <span style="font-weight: 600; color: #01AFFC;">
                                $<?= number_format($day['daily_revenue'], 2) ?>
                            </span>
                        </div>
                        <div class="product-cell">
                            $<?= number_format($day['daily_revenue'] / $day['orders_count'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Top Selling Products -->
<div class="section">
    <h2 class="section-subtitle">Top Selling Products (Last 30 Days)</h2>
    
    <?php if (empty($top_products)): ?>
        <div class="empty">
            <div class="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <h3>No product sales</h3>
            <p>Top selling products will appear here once orders are placed.</p>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <div class="products-header">
                <div class="header-cell">Product</div>
                <div class="header-cell">Units Sold</div>
                <div class="header-cell">Revenue</div>
                <div class="header-cell">Avg Price</div>
            </div>
            <div class="products-body">
                <?php foreach ($top_products as $product): ?>
                    <div class="product-row">
                        <div class="product-cell">
                            <span style="font-weight: 600;"><?= htmlspecialchars($product['name']) ?></span>
                        </div>
                        <div class="product-cell">
                            <span style="font-weight: 600; color: #01AFFC;"><?= number_format($product['total_sold']) ?></span>
                        </div>
                        <div class="product-cell">
                            $<?= number_format($product['revenue'], 2) ?>
                        </div>
                        <div class="product-cell">
                            $<?= number_format($product['revenue'] / $product['total_sold'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>