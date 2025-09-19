<div id="countries-table" class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Country</th>
                <th>Code</th>
                <th>Delivery Estimate</th>
                <th>Status</th>
                <th>Weight Tiers</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($countries)): ?>
            <tr>
                <td colspan="6" class="no-data">No shipping countries configured</td>
            </tr>
            <?php else: ?>
                <?php foreach ($countries as $country): ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <div class="product-name">🌍 <?= htmlspecialchars($country['country_name']) ?></div>
                        </div>
                    </td>
                    <td>
                        <span class="badge <?= $country['is_enabled'] ? 'blue' : 'gray' ?>">
                            <?= htmlspecialchars($country['country_code']) ?>
                        </span>
                    </td>
                    <td>
                        <?= $country['estimated_days_min'] ?>-<?= $country['estimated_days_max'] ?> days
                    </td>
                    <td>
                        <?php if ($country['is_enabled']): ?>
                        <span class="badge green">Enabled</span>
                        <?php else: ?>
                        <span class="badge gray">Disabled</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        // Get tier count for this country
                        $db = db();
                        $stmt = $db->prepare("SELECT COUNT(*) as tier_count FROM shipping_weight_tiers WHERE country_id = :id");
                        $stmt->execute([':id' => $country['id']]);
                        $tier_count = $stmt->fetch(PDO::FETCH_ASSOC)['tier_count'];
                        ?>
                        <?php if ($tier_count > 0): ?>
                        <span class="badge blue"><?= $tier_count ?> tiers</span>
                        <?php else: ?>
                        <span class="badge gray">No tiers</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="actions">
                            <?php if ($country['is_enabled']): ?>
                            <button type="button"
                                    hx-post="<?= url('admin/shipping/countries/update/' . $country['id']) ?>"
                                    hx-vals='{"is_enabled": false, "country_name": "<?= htmlspecialchars($country['country_name']) ?>", "estimated_days_min": <?= $country['estimated_days_min'] ?>, "estimated_days_max": <?= $country['estimated_days_max'] ?>}'
                                    hx-target="#countries-table"
                                    hx-swap="outerHTML"
                                    class="btn-small gray"
                                    title="Disable Country">
                                🚫 Disable
                            </button>
                            <?php else: ?>
                            <button type="button"
                                    hx-post="<?= url('admin/shipping/countries/update/' . $country['id']) ?>"
                                    hx-vals='{"is_enabled": true, "country_name": "<?= htmlspecialchars($country['country_name']) ?>", "estimated_days_min": <?= $country['estimated_days_min'] ?>, "estimated_days_max": <?= $country['estimated_days_max'] ?>}'
                                    hx-target="#countries-table"
                                    hx-swap="outerHTML"
                                    class="btn-small green"
                                    title="Enable Country">
                                ✅ Enable
                            </button>
                            <?php endif; ?>

                            <?php
                            // Check if country has orders
                            $stmt = $db->prepare("SELECT COUNT(*) as order_count FROM orders WHERE shipping_country = :code");
                            $stmt->execute([':code' => $country['country_code']]);
                            $order_count = $stmt->fetch(PDO::FETCH_ASSOC)['order_count'];
                            ?>

                            <?php if ($order_count == 0): ?>
                            <button type="button"
                                    hx-post="<?= url('admin/shipping/countries/delete/' . $country['id']) ?>"
                                    hx-target="#countries-table"
                                    hx-swap="outerHTML"
                                    hx-confirm="Are you sure you want to delete <?= htmlspecialchars($country['country_name']) ?>? This will also delete all weight tiers for this country."
                                    class="btn-small red"
                                    title="Delete Country">
                                🗑️ Delete
                            </button>
                            <?php else: ?>
                            <span class="btn-small gray disabled" title="Cannot delete - has <?= $order_count ?> existing orders">
                                🗑️ Delete
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>