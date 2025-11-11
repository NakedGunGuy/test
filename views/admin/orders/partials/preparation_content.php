<div id="preparation-content">
<!-- Preparation Statistics -->
<div class="section">
    <h2 class="section-subtitle">Preparation Summary</h2>
    <div class="grid stats" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="card stat">
            <?= icon('clipboard') ?>
            <div class="info">
                <div class="number"><?= $stats['unprepared_orders'] ?? 0 ?></div>
                <div class="label">Orders to Prepare</div>
            </div>
        </div>
        <div class="card stat">
            <?= icon('package') ?>
            <div class="info">
                <div class="number"><?= $stats['total_items_remaining'] ?? 0 ?></div>
                <div class="label">Items Remaining</div>
            </div>
        </div>
        <div class="card stat">
            <?= icon('check') ?>
            <div class="info">
                <div class="number"><?= $stats['total_items_prepared'] ?? 0 ?></div>
                <div class="label">Items Prepared</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">🎴</div>
            <div class="info">
                <div class="number"><?= $stats['products_needing_prep'] ?? 0 ?></div>
                <div class="label">Products Need Prep</div>
            </div>
        </div>
        <?php if ($stats['total_items_prepared'] > 0): ?>
        <div class="card stat">
            <?= icon('truck') ?>
            <div class="info">
                <div class="number">
                    <a href="<?= url('admin/orders/shipping') ?>" style="color: #01AFFC; text-decoration: none;">
                        View Shipping →
                    </a>
                </div>
                <div class="label">Ready to Ship</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($grouped_items)): ?>
    <!-- No Items to Prepare -->
    <div class="section">
        <div class="empty">
            <div class="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3>All caught up!</h3>
            <p>No orders are currently waiting for preparation.</p>
        </div>
    </div>
<?php else: ?>
    <!-- Items Grouped by Set -->
    <?php foreach ($grouped_items as $set_name => $items): ?>
        <div class="section">
            <h2 class="section-subtitle"><?= htmlspecialchars($set_name) ?></h2>

            <div class="grid">
                <div class="grid-header" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr 2fr;">
                    <div class="header-cell">Card</div>
                    <div class="header-cell">Collector #</div>
                    <div class="header-cell">Rarity</div>
                    <div class="header-cell">Need / Total</div>
                    <div class="header-cell">Orders</div>
                    <div class="header-cell">Unit Price</div>
                    <div class="header-cell">Mark Prepared</div>
                    <div class="header-cell">Order IDs</div>
                </div>
                <div class="grid-body">
                    <?php foreach ($items as $item): ?>
                        <div class="grid-row" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr 2fr;">
                            <div class="grid-cell">
                                <div style="font-weight: 600;">
                                    <?= htmlspecialchars($item['card_name'] ?: $item['product_name']) ?>
                                </div>
                                <?php if ($item['edition_slug']): ?>
                                    <div style="font-size: 12px; color: #C0C0D1; margin-top: 2px;">
                                        <?= icon('file') ?> <?= htmlspecialchars($item['card_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="grid-cell">
                                <?php if ($item['collector_number']): ?>
                                    <span style="font-family: monospace; background: rgba(0, 174, 239, 0.1); padding: 2px 6px; border-radius: 4px;">
                                        <?= htmlspecialchars($item['collector_number']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #C0C0D1;">—</span>
                                <?php endif; ?>
                            </div>

                            <div class="grid-cell">
                                <?php if ($item['rarity']): ?>
                                    <span class="badge rarity rarity-<?= strtolower($item['rarity']) ?>">
                                        <?= htmlspecialchars($item['rarity']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #C0C0D1;">—</span>
                                <?php endif; ?>
                            </div>

                            <div class="grid-cell">
                                <div style="font-weight: 600; color: #01AFFC; font-size: 16px;">
                                    <?= $item['total_remaining'] ?>
                                </div>
                                <div style="font-size: 12px; color: #C0C0D1;">
                                    of <?= $item['total_quantity'] ?>
                                </div>
                            </div>

                            <div class="grid-cell">
                                <span class="badge">
                                    <?= $item['order_count'] ?> order<?= $item['order_count'] != 1 ? 's' : '' ?>
                                </span>
                            </div>

                            <div class="grid-cell">
                                <span style="font-weight: 600;">
                                    €<?= number_format($item['price'], 2) ?>
                                </span>
                            </div>

                            <div class="grid-cell">
                                <form style="display: flex; gap: 4px; align-items: center;"
                                      hx-post="<?= url('admin/orders/preparation/mark') ?>"
                                      hx-target="#preparation-content"
                                      hx-swap="outerHTML">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <input type="number"
                                           name="quantity_prepared"
                                           min="1"
                                           max="<?= $item['total_remaining'] ?>"
                                           value="<?= min(5, $item['total_remaining']) ?>"
                                           style="width: 50px; padding: 2px 4px; font-size: 12px;"
                                           class="form-input">
                                    <button type="submit" class="btn blue btn-small">✓</button>
                                </form>
                            </div>

                            <div class="grid-cell">
                                <div style="font-family: monospace; font-size: 12px; color: #C0C0D1;">
                                    <?php
                                    $order_ids = explode(',', $item['order_ids']);
                                    $display_ids = array_slice($order_ids, 0, 3);
                                    echo '#' . implode(', #', $display_ids);
                                    if (count($order_ids) > 3): ?>
                                        <span style="color: #01AFFC;">+<?= count($order_ids) - 3 ?> more</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Preparation Instructions -->
    <div class="section">
        <div class="card" style="background: rgba(0, 174, 239, 0.05); border: 1px solid rgba(0, 174, 239, 0.2);">
            <h3 style="margin-top: 0; color: #01AFFC;"><?= icon('clipboard') ?> Preparation Instructions</h3>
            <ul style="margin: 0; padding-left: 1.5rem;">
                <li>Items are grouped by set for easier collection</li>
                <li>Quantity shows total needed across all unprepared orders</li>
                <li>Order IDs help you track which orders need each item</li>
                <li>Once prepared, update individual order status to "Processing" or "Shipped" in the <a href="<?= url('admin/orders') ?>" style="color: #01AFFC;">Orders Management</a> page</li>
            </ul>
        </div>
    </div>
<?php endif; ?>
</div> <!-- End preparation-content -->