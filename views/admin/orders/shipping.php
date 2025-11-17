<?php start_section('title'); ?>
<?= t('shipping.title') ?> - Admin - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<!-- Page Header -->
<div class="section" style="margin-bottom: 2rem;">
    <a href="<?= url('admin/orders/preparation') ?>" class="btn text back">← <?= t('shipping.back_to_preparation') ?></a>
    <h1 class="section-title" style="margin-top: 0;"><?= t('shipping.title') ?></h1>
    <p style="color: #C0C0D1;"><?= t('shipping.description') ?></p>
</div>

<?php if (empty($orders)): ?>
    <!-- No Prepared Items -->
    <div class="section">
        <div class="empty">
            <div class="icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <h3><?= t('shipping.no_items_prepared') ?></h3>
            <p><?= t('shipping.prepare_items_first') ?> <a href="<?= url('admin/orders/preparation') ?>" style="color: #01AFFC;"><?= t('shipping.order_preparation') ?></a> <?= t('shipping.view') ?></p>
        </div>
    </div>
<?php else: ?>
    <!-- Orders with Prepared Items -->
    <div class="section">
        <h2 class="section-subtitle"><?= t('shipping.orders_ready') ?></h2>
        <p style="color: #C0C0D1; margin-bottom: 1rem;"><?= t('shipping.pending_orders_info') ?></p>

        <div class="grid">
            <div class="grid-header" style="grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;">
                <div class="header-cell"><?= t('shipping.order') ?></div>
                <div class="header-cell"><?= t('shipping.customer') ?></div>
                <div class="header-cell"><?= t('shipping.status') ?></div>
                <div class="header-cell"><?= t('shipping.prepared_items') ?></div>
                <div class="header-cell"><?= t('shipping.completion') ?></div>
                <div class="header-cell"><?= t('shipping.total') ?></div>
                <div class="header-cell"><?= t('shipping.actions') ?></div>
            </div>
            <div class="grid-body">
                <?php foreach ($orders as $order): ?>
                    <?php
                    $completion_percentage = ($order['prepared_quantity'] / $order['total_quantity']) * 100;
                    $is_fully_prepared = $order['fully_prepared_item_types'] == $order['total_item_types'];
                    ?>
                    <div class="grid-row" style="grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;">
                        <div class="grid-cell">
                            <div style="font-weight: 600;">#<?= $order['order_id'] ?></div>
                            <div style="font-size: 12px; color: #C0C0D1; margin-top: 2px;">
                                <?= date('M j, Y', strtotime($order['created_at'])) ?>
                            </div>
                        </div>

                        <div class="grid-cell">
                            <div style="font-weight: 600;"><?= htmlspecialchars($order['username']) ?></div>
                            <div style="font-size: 12px; color: #C0C0D1; margin-top: 2px;">
                                <?= htmlspecialchars($order['email']) ?>
                            </div>
                        </div>

                        <div class="grid-cell">
                            <span class="badge status status-<?= $order['status'] ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>

                        <div class="grid-cell">
                            <div style="font-weight: 600;">
                                <?= $order['prepared_quantity'] ?> / <?= $order['total_quantity'] ?>
                            </div>
                            <div style="font-size: 12px; color: #C0C0D1;">
                                <?= t('shipping.items_prepared') ?>
                            </div>
                        </div>

                        <div class="grid-cell">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="background: rgba(0, 174, 239, 0.1); border-radius: 4px; height: 8px; flex: 1; overflow: hidden;">
                                    <div style="background: <?= $is_fully_prepared ? '#28A745' : '#01AFFC' ?>; height: 100%; width: <?= $completion_percentage ?>%; transition: width 0.3s ease;"></div>
                                </div>
                                <span style="font-size: 12px; font-weight: 600; color: <?= $is_fully_prepared ? '#28A745' : '#01AFFC' ?>;">
                                    <?= round($completion_percentage) ?>%
                                </span>
                            </div>
                            <?php if ($is_fully_prepared): ?>
                                <div style="font-size: 11px; color: #28A745; margin-top: 2px;">
                                    <?= icon('check') ?> <?= t('shipping.ready_to_ship') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="grid-cell">
                            <span style="font-weight: 600; color: #01AFFC;">
                                €<?= number_format($order['total_amount'], 2) ?>
                            </span>
                        </div>

                        <div class="grid-cell">
                            <div style="display: flex; gap: 4px; flex-direction: column;">
                                <button class="btn blue btn-small"
                                        hx-get="<?= url('admin/orders/' . $order['order_id'] . '/prepared-items') ?>"
                                        hx-target="#order-details-<?= $order['order_id'] ?>"
                                        hx-swap="innerHTML"
                                        onclick="this.textContent = this.textContent === '<?= t('shipping.hide_items') ?>' ? '<?= t('shipping.show_items') ?>' : '<?= t('shipping.hide_items') ?>'">
                                    <?= t('shipping.show_items') ?>
                                </button>
                                <?php if ($is_fully_prepared): ?>
                                    <button type="button"
                                            class="btn black btn-small"
                                            onclick="document.getElementById('shipping-form-<?= $order['order_id'] ?>').style.display = document.getElementById('shipping-form-<?= $order['order_id'] ?>').style.display === 'none' ? 'flex' : 'none'; this.style.display = 'none';">
                                        <?= t('shipping.mark_shipped') ?>
                                    </button>
                                    <form id="shipping-form-<?= $order['order_id'] ?>"
                                          style="display: none; gap: 4px; flex-direction: column;"
                                          hx-post="<?= url('admin/orders/' . $order['order_id'] . '/status') ?>"
                                          hx-swap="none">
                                        <input type="hidden" name="status" value="shipped">
                                        <input type="text"
                                               name="tracking_number"
                                               placeholder="<?= t('shipping.tracking_number') ?>"
                                               class="form-input"
                                               style="padding: 4px 6px; font-size: 12px;">
                                        <div style="display: flex; gap: 4px;">
                                            <button type="submit" class="btn blue btn-small" style="flex: 1;">
                                                <?= icon('check') ?>
                                            </button>
                                            <button type="button" class="btn text btn-small" style="flex: 1;"
                                                    onclick="this.closest('form').style.display = 'none'; this.closest('.grid-cell').querySelector('.btn.black').style.display = 'block';">
                                                ✕
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Expandable Order Details -->
                    <div class="grid-row" style="grid-column: 1 / -1; display: none;" id="order-details-row-<?= $order['order_id'] ?>">
                        <div id="order-details-<?= $order['order_id'] ?>" style="padding: 1rem; background: rgba(0, 174, 239, 0.05); border-radius: 8px; margin: 8px 0;">
                            <!-- Prepared items will be loaded here -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
    // Show/hide order details
    document.addEventListener('htmx:afterRequest', function(event) {
        if (event.detail.target.id.startsWith('order-details-')) {
            const orderId = event.detail.target.id.split('-')[2];
            const detailsRow = document.getElementById('order-details-row-' + orderId);
            if (detailsRow) {
                detailsRow.style.display = detailsRow.style.display === 'none' ? 'block' : 'none';
            }
        }
    });
    </script>
<?php endif; ?>