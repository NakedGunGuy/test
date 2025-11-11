<div class="empty-state">
    <div class="empty-icon"><?= icon('package') ?></div>
    <p>No weight tiers configured for <?= htmlspecialchars($country['country_name']) ?></p>
    <button type="button" 
            hx-post="<?= url('admin/shipping/weight-tiers/bulk-add/' . $country['id']) ?>" 
            hx-confirm="Add default weight tiers (0.5kg, 1kg, 2kg, 5kg) for <?= htmlspecialchars($country['country_name']) ?>?"
            hx-swap="outerHTML"
            hx-target="closest .weight-tiers"
            class="btn-small blue">
        Add Default Tiers
    </button>
</div>