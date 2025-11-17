<div id="preparation-content">
<!-- Preparation Statistics -->
<div class="section">
    <h2 class="section-subtitle"><?= t('preparation.summary') ?></h2>
    <div class="grid stats" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="card stat">
            <?= icon('clipboard') ?>
            <div class="info">
                <div class="number"><?= $stats['unprepared_orders'] ?? 0 ?></div>
                <div class="label"><?= t('preparation.orders_to_prepare') ?></div>
            </div>
        </div>
        <div class="card stat">
            <?= icon('package') ?>
            <div class="info">
                <div class="number"><?= $stats['total_items_remaining'] ?? 0 ?></div>
                <div class="label"><?= t('preparation.items_remaining') ?></div>
            </div>
        </div>
        <div class="card stat">
            <?= icon('check') ?>
            <div class="info">
                <div class="number"><?= $stats['total_items_prepared'] ?? 0 ?></div>
                <div class="label"><?= t('preparation.items_prepared') ?></div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">🎴</div>
            <div class="info">
                <div class="number"><?= $stats['products_needing_prep'] ?? 0 ?></div>
                <div class="label"><?= t('preparation.products_need_prep') ?></div>
            </div>
        </div>
        <?php if ($stats['total_items_prepared'] > 0): ?>
        <div class="card stat">
            <?= icon('truck') ?>
            <div class="info">
                <div class="number">
                    <a href="<?= url('admin/orders/shipping') ?>" style="color: #01AFFC; text-decoration: none;">
                        <?= t('preparation.view_shipping') ?> →
                    </a>
                </div>
                <div class="label"><?= t('preparation.ready_to_ship') ?></div>
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
            <h3><?= t('preparation.all_caught_up') ?></h3>
            <p><?= t('preparation.no_orders_waiting') ?></p>
        </div>
    </div>
<?php else: ?>
    <!-- Items Grouped by Set -->
    <?php foreach ($grouped_items as $set_name => $items): ?>
        <div class="section">
            <h2 class="section-subtitle"><?= htmlspecialchars($set_name) ?></h2>

            <div class="grid">
                <div class="grid-header" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr 2fr;">
                    <div class="header-cell"><?= t('preparation.card') ?></div>
                    <div class="header-cell"><?= t('preparation.collector_number') ?></div>
                    <div class="header-cell"><?= t('preparation.rarity') ?></div>
                    <div class="header-cell"><?= t('preparation.need_total') ?></div>
                    <div class="header-cell"><?= t('preparation.orders') ?></div>
                    <div class="header-cell"><?= t('preparation.unit_price') ?></div>
                    <div class="header-cell"><?= t('preparation.mark_prepared') ?></div>
                    <div class="header-cell"><?= t('preparation.order_ids') ?></div>
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
                                        <?= htmlspecialchars($item['card_name']) ?>
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
                                    <span style="font-family: monospace; font-size: 11px; color: #C0C0D1;">
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
                                    <?= t('preparation.of') ?> <?= $item['total_quantity'] ?>
                                </div>
                            </div>

                            <div class="grid-cell">
                                <span class="badge">
                                    <?= $item['order_count'] ?> <?= $item['order_count'] != 1 ? t('preparation.order_plural') : t('preparation.order_singular') ?>
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
                                           style="width: 60px; padding: 4px 6px; font-size: 13px; text-align: center;"
                                           class="form-input">
                                    <button type="submit" class="btn blue" style="padding: 4px 12px; font-size: 13px;"><?= t('preparation.mark_button') ?></button>
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
            <h3 style="margin-top: 0; color: #01AFFC;"><?= icon('clipboard') ?> <?= t('preparation.instructions_title') ?></h3>
            <ul style="margin: 0; padding-left: 1.5rem;">
                <li><?= t('preparation.instructions_1') ?></li>
                <li><?= t('preparation.instructions_2') ?></li>
                <li><?= t('preparation.instructions_3') ?></li>
                <li><?= t('preparation.instructions_4') ?> <a href="<?= url('admin/orders') ?>" style="color: #01AFFC;"><?= t('preparation.orders_management') ?></a></li>
            </ul>
        </div>
    </div>
<?php endif; ?>
</div> <!-- End preparation-content -->