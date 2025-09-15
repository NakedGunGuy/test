<?php
start_section('title');
echo 'Shipping Settings';
end_section('title');

$countries = get_all_shipping_countries();
$weight_tiers = get_all_shipping_weight_tiers();

// Group weight tiers by country
$tiers_by_country = [];
foreach ($weight_tiers as $tier) {
    $tiers_by_country[$tier['country_id']][] = $tier;
}
?>

<div class="container">
    <div class="section-header">
        <span class="section-header-icon">🚚</span>
        Shipping Settings
    </div>

    <!-- Quick Add Section -->
    <div class="section">
        <h3 class="section-subtitle">Add New Weight Tier</h3>
        <div class="card">
            <form hx-post="/admin/shipping/weight-tiers/add" hx-swap="none" hx-on="htmx:afterRequest: if(event.detail.xhr.status === 200) window.location.reload()">
                <div class="grid form" style="grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group">
                        <label for="country_id" class="form-label">Country</label>
                        <select id="country_id" name="country_id" class="form-input" required>
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $country): ?>
                                <?php if ($country['is_enabled']): ?>
                                <option value="<?= $country['id'] ?>"><?= htmlspecialchars($country['country_name']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tier_name" class="form-label">Tier Name</label>
                        <input type="text" id="tier_name" name="tier_name" class="form-input" placeholder="Up to 0.5kg" required>
                    </div>
                    <div class="form-group">
                        <label for="max_weight_kg" class="form-label">Max Weight (kg)</label>
                        <input type="number" id="max_weight_kg" name="max_weight_kg" class="form-input" step="0.01" min="0.01" placeholder="0.5" required>
                    </div>
                    <div class="form-group">
                        <label for="price" class="form-label">Price (USD)</label>
                        <input type="number" id="price" name="price" class="form-input" step="0.01" min="0" placeholder="4.99" required>
                    </div>
                    <button type="submit" class="btn blue">Add Tier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Weight Tiers by Country -->
    <div class="section">
        <h3 class="section-subtitle">Weight Tiers by Country</h3>
        <p class="form-help">Manage shipping costs for each country. Cards weigh 2g each on average.</p>
        
        <?php foreach ($countries as $country): ?>
            <?php if ($country['is_enabled']): ?>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="country-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #C0C0D133;">
                    <h4 style="margin: 0; color: #01AFFC; display: flex; align-items: center; gap: 0.5rem;">
                        🌍 <?= htmlspecialchars($country['country_name']) ?> 
                        <span style="background: #1E1E27; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; color: #C0C0D1;"><?= htmlspecialchars($country['country_code']) ?></span>
                    </h4>
                    <div style="color: #C0C0D1; font-size: 0.9rem;">
                        Delivery: <?= $country['estimated_days_min'] ?>-<?= $country['estimated_days_max'] ?> days
                    </div>
                </div>
                
                <?php if (isset($tiers_by_country[$country['id']]) && !empty($tiers_by_country[$country['id']])): ?>
                    <div class="tier-list">
                        <?php foreach ($tiers_by_country[$country['id']] as $tier): ?>
                        <div class="tier-item" style="background: #1E1E27; border: 1px solid #C0C0D133; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                            <form hx-post="/admin/shipping/weight-tiers/update/<?= $tier['id'] ?>" hx-swap="none" hx-on="htmx:afterRequest: if(event.detail.xhr.status === 200) window.location.reload()">
                                <input type="hidden" name="country_id" value="<?= $tier['country_id'] ?>">
                                <div class="grid form" style="grid-template-columns: 2fr 1fr 1fr auto auto auto; gap: 1rem; align-items: center;">
                                    <div class="form-group" style="margin: 0;">
                                        <label class="form-label">Tier Name</label>
                                        <input type="text" name="tier_name" class="form-input" value="<?= htmlspecialchars($tier['tier_name']) ?>" required>
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label class="form-label">Max Weight</label>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <input type="number" name="max_weight_kg" class="form-input" step="0.01" min="0.01" value="<?= $tier['max_weight_kg'] ?>" required>
                                            <span style="color: #C0C0D1; font-size: 0.9rem;">kg</span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label class="form-label">Price</label>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <input type="number" name="price" class="form-input" step="0.01" min="0" value="<?= $tier['price'] ?>" required>
                                            <span style="color: #C0C0D1; font-size: 0.9rem;">USD</span>
                                        </div>
                                    </div>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                                        <input type="checkbox" name="is_enabled" value="1" <?= $tier['is_enabled'] ? 'checked' : '' ?>>
                                        <span class="form-label">Enabled</span>
                                    </label>
                                    <button type="submit" class="btn-small blue">Save</button>
                                    <button type="button" 
                                            hx-post="/admin/shipping/weight-tiers/delete/<?= $tier['id'] ?>" 
                                            hx-confirm="Delete this shipping tier?" 
                                            hx-on="htmx:afterRequest: if(event.detail.xhr.status === 200) window.location.reload()"
                                            class="btn-small red">Delete</button>
                                </div>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 2rem; color: #C0C0D1; background: #1E1E27; border-radius: 8px; border: 1px dashed #C0C0D133;">
                        No weight tiers configured for <?= htmlspecialchars($country['country_name']) ?>
                        <br>
                        <small>Use the form above to add weight tiers for this country</small>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Countries Management -->
    <div class="section">
        <h3 class="section-subtitle">Shipping Countries</h3>
        <p class="form-help">Configure supported shipping destinations and delivery estimates.</p>
        
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 1rem;">
            <?php foreach ($countries as $country): ?>
            <div class="card">
                <form hx-post="/admin/shipping/countries/update/<?= $country['id'] ?>" hx-swap="none" hx-on="htmx:afterRequest: if(event.detail.xhr.status === 200) window.location.reload()">
                    <div style="margin-bottom: 1rem;">
                        <h4 style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <?= $country['country_code'] ?> - <?= htmlspecialchars($country['country_name']) ?>
                            <span style="background: <?= $country['is_enabled'] ? '#059669' : '#DC2626' ?>; color: white; padding: 0.125rem 0.5rem; border-radius: 4px; font-size: 0.7rem;">
                                <?= $country['is_enabled'] ? 'ENABLED' : 'DISABLED' ?>
                            </span>
                        </h4>
                        <input type="hidden" name="country_name" value="<?= htmlspecialchars($country['country_name']) ?>">
                    </div>
                    
                    <div class="grid form" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label">Min Delivery Days</label>
                            <input type="number" name="estimated_days_min" class="form-input" min="1" value="<?= $country['estimated_days_min'] ?>" required>
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label">Max Delivery Days</label>
                            <input type="number" name="estimated_days_max" class="form-input" min="1" value="<?= $country['estimated_days_max'] ?>" required>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                            <input type="checkbox" name="is_enabled" value="1" <?= $country['is_enabled'] ? 'checked' : '' ?>>
                            <span class="form-label">Enabled for shipping</span>
                        </label>
                        <button type="submit" class="btn-small blue">Update</button>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Shipping Calculator -->
    <div class="section">
        <h3 class="section-subtitle">Shipping Calculator</h3>
        <p class="form-help">Test shipping calculations for different weights and countries.</p>
        
        <div class="card">
            <div class="grid form" style="grid-template-columns: auto auto auto 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                <div class="form-group">
                    <label for="test_cards" class="form-label">Number of Cards</label>
                    <input type="number" id="test_cards" class="form-input" min="1" value="25" placeholder="25" style="width: 120px;">
                    <div class="form-help">@ 2g each = <span id="calculated-weight">50g</span></div>
                </div>
                <div class="form-group">
                    <label for="test_country" class="form-label">Country</label>
                    <select id="test_country" class="form-input" style="width: 200px;">
                        <option value="">Select Country</option>
                        <?php foreach ($countries as $country): ?>
                            <?php if ($country['is_enabled']): ?>
                            <option value="<?= $country['country_code'] ?>"><?= htmlspecialchars($country['country_name']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" 
                        hx-post="/admin/shipping/calculate" 
                        hx-include="#test_cards, #test_country"
                        hx-target="#shipping-result"
                        class="btn blue">Calculate Shipping</button>
                <div></div>
            </div>
            <div id="shipping-result" class="form-help" style="padding: 1rem; background: #1E1E27; border-radius: 8px; border: 1px solid #C0C0D133;">
                Click calculate to see shipping cost and delivery estimate
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('test_cards').addEventListener('input', function() {
    const cards = parseInt(this.value) || 0;
    const weight = cards * 2;
    document.getElementById('calculated-weight').textContent = weight + 'g';
});
</script>