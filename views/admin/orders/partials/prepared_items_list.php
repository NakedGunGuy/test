<h4 style="margin: 0 0 1rem 0; color: #01AFFC;">Prepared Items for Order #<?= $order_id ?></h4>

<?php if (empty($items)): ?>
    <p style="color: #C0C0D1; margin: 0;">No items prepared for this order yet.</p>
<?php else: ?>
    <div style="display: grid; gap: 8px;">
        <?php
        $grouped_by_set = [];
        foreach ($items as $item) {
            $set_name = $item['set_name'] ?: 'Custom Products';
            if (!isset($grouped_by_set[$set_name])) {
                $grouped_by_set[$set_name] = [];
            }
            $grouped_by_set[$set_name][] = $item;
        }
        ?>

        <?php foreach ($grouped_by_set as $set_name => $set_items): ?>
            <div style="background: rgba(0, 0, 0, 0.2); padding: 12px; border-radius: 6px;">
                <h5 style="margin: 0 0 8px 0; color: #01AFFC; font-size: 14px;"><?= htmlspecialchars($set_name) ?></h5>

                <?php foreach ($set_items as $item): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: 1px solid rgba(0, 174, 239, 0.1);">
                        <div style="flex: 1;">
                            <span style="font-weight: 600;">
                                <?= htmlspecialchars($item['card_name'] ?: $item['product_name']) ?>
                            </span>
                            <?php if ($item['collector_number']): ?>
                                <span style="color: #C0C0D1; margin-left: 8px; font-size: 12px;">
                                    #<?= htmlspecialchars($item['collector_number']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($item['rarity']): ?>
                                <span class="badge rarity rarity-<?= strtolower($item['rarity']) ?>" style="margin-left: 8px; font-size: 10px;">
                                    <?= htmlspecialchars($item['rarity']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div style="display: flex; gap: 12px; align-items: center;">
                            <div style="text-align: center;">
                                <div style="font-weight: 600; color: #28A745;">
                                    <?= $item['prepared_quantity'] ?>
                                </div>
                                <div style="font-size: 11px; color: #C0C0D1;">
                                    of <?= $item['quantity'] ?>
                                </div>
                            </div>

                            <div style="text-align: right;">
                                <div style="font-weight: 600;">
                                    €<?= number_format($item['price'], 2) ?>
                                </div>
                                <?php if ($item['prepared_quantity'] > 1): ?>
                                    <div style="font-size: 11px; color: #C0C0D1;">
                                        (€<?= number_format($item['price'] * $item['prepared_quantity'], 2) ?> total)
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div style="margin-top: 12px; padding: 8px; background: rgba(40, 167, 69, 0.1); border-radius: 4px; text-align: center;">
            <span style="color: #28A745; font-weight: 600;">
                <?= icon('check') ?> <?= array_sum(array_column($items, 'prepared_quantity')) ?> items ready for this order
            </span>
        </div>
    </div>
<?php endif; ?>