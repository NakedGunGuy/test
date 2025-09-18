<?php
start_section('title');
echo 'Shipping Settings';
end_section('title');

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

    <!-- Quick Add Weight Tier -->
    <div class="section">
        <h3 class="section-subtitle">Add New Weight Tier</h3>
        <?php partial('admin/shipping/partials/add_tier_form', ['countries' => $countries]); ?>
    </div>

    <!-- Weight Tiers by Country -->
    <div class="section">
        <h3 class="section-subtitle">Weight Tiers by Country</h3>
        <p class="form-help">Manage shipping costs for each country. Cards weigh 2g each on average.</p>
        
        <?php foreach ($countries as $country): ?>
            <?php if ($country['is_enabled']): ?>
            <div class="shipping-country-card">
                <div class="country-header">
                    <div class="country-info">
                        <h4>🌍 <?= htmlspecialchars($country['country_name']) ?> 
                            <span class="country-code"><?= htmlspecialchars($country['country_code']) ?></span>
                        </h4>
                        <div class="country-meta">
                            Delivery: <?= $country['estimated_days_min'] ?>-<?= $country['estimated_days_max'] ?> days
                        </div>
                    </div>
                    <div class="country-actions">
                        <button type="button" class="btn-small black" onclick="toggleCountrySettings('<?= $country['id'] ?>')">
                            ⚙️ Settings
                        </button>
                    </div>
                </div>

                <!-- Country Settings (Hidden by default) -->
                <div id="country-settings-<?= $country['id'] ?>" class="country-settings" style="display: none;">
                    <?php partial('admin/shipping/partials/country_settings', ['country' => $country]); ?>
                </div>

                <!-- Weight Tiers -->
                <div class="weight-tiers" data-country-tiers="<?= $country['id'] ?>" id="country-tiers-<?= $country['id'] ?>">
                    <?php if (isset($tiers_by_country[$country['id']]) && !empty($tiers_by_country[$country['id']])): ?>
                        <?php foreach ($tiers_by_country[$country['id']] as $tier): ?>
                            <?php partial('admin/shipping/partials/tier_card', ['tier' => $tier]); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php partial('admin/shipping/partials/empty_tiers', ['country' => $country]); ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
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
            <div id="shipping-result" class="calculation-result">
                Click calculate to see shipping cost and delivery estimate
            </div>
        </div>
    </div>
</div>

<script>
// Weight calculator
document.getElementById('test_cards').addEventListener('input', function() {
    const cards = parseInt(this.value) || 0;
    const weight = cards * 2;
    document.getElementById('calculated-weight').textContent = weight + 'g';
});

// Toggle country settings
function toggleCountrySettings(countryId) {
    const settings = document.getElementById('country-settings-' + countryId);
    if (settings.style.display === 'none') {
        settings.style.display = 'block';
    } else {
        settings.style.display = 'none';
    }
}

// Validate form before submission
document.body.addEventListener('htmx:configRequest', function(evt) {
    if (evt.target.matches('#add-tier-form')) {
        const countryId = evt.target.querySelector('[name=country_id]').value;
        if (!countryId) {
            document.getElementById('form-error').innerHTML = '<div class="error-message">Please select a country first</div>';
            evt.preventDefault();
            return;
        }

        // Clear any previous errors
        document.getElementById('form-error').innerHTML = '';
    }
});

// Auto-clear form after successful add
document.body.addEventListener('htmx:afterRequest', function(evt) {
    if (evt.detail.xhr.status === 200 && evt.target.matches('form[hx-post*="/weight-tiers/add"]')) {
        // Clear form inputs
        const form = evt.target;
        form.reset();

        // Clear any error messages
        document.getElementById('form-error').innerHTML = '';
    }
});
</script>