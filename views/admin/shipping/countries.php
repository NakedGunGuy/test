<?php
start_section('title');
echo 'Shipping Countries Management';
end_section('title');
?>

<div class="container">
    <div class="section-header">
        <span class="section-header-icon">🌍</span>
        Shipping Countries Management
    </div>

    <!-- Add New Country -->
    <div class="section">
        <h3 class="section-subtitle">Add New Shipping Country</h3>
            <form hx-post="<?= url('admin/shipping/countries/add') ?>" hx-target="#countries-table" hx-swap="outerHTML">
                <div class="grid form" style="grid-template-columns: 1fr 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group">
                        <label for="country_code" class="form-label">Country Code</label>
                        <input type="text"
                               id="country_code"
                               name="country_code"
                               class="form-input"
                               placeholder="US"
                               maxlength="2"
                               style="text-transform: uppercase;"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="country_name" class="form-label">Country Name</label>
                        <input type="text"
                               id="country_name"
                               name="country_name"
                               class="form-input"
                               placeholder="United States"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="estimated_days_min" class="form-label">Min Days</label>
                        <input type="number"
                               id="estimated_days_min"
                               name="estimated_days_min"
                               class="form-input"
                               value="7"
                               min="1"
                               max="90"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="estimated_days_max" class="form-label">Max Days</label>
                        <input type="number"
                               id="estimated_days_max"
                               name="estimated_days_max"
                               class="form-input"
                               value="14"
                               min="1"
                               max="90"
                               required>
                    </div>
                    <button type="submit" class="btn blue form-group">Add Country</button>
                </div>
            </form>
    </div>

    <!-- Countries List -->
    <div class="section">
        <h3 class="section-subtitle">Available Shipping Countries</h3>
        <?php partial('admin/shipping/countries/partials/countries_table', ['countries' => $countries]); ?>
    </div>

    <!-- Info Section -->
    <div class="section">
        <h4>📋 Country Management Notes</h4>
        <ul style="margin: 1rem 0;">
            <li><strong>Country Code:</strong> Must be a valid ISO 3166-1 alpha-2 code (2 letters)</li>
            <li><strong>Delivery Estimates:</strong> Used for customer shipping estimates</li>
            <li><strong>Disable vs Delete:</strong> Disable countries to stop new orders, delete only if no existing orders</li>
            <li><strong>Weight Tiers:</strong> Configure shipping costs per country in the main shipping settings</li>
            <li><strong>Order Safety:</strong> Countries with existing orders cannot be deleted</li>
        </ul>
    </div>
</div>

<script>
// Auto-uppercase country code
document.getElementById('country_code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Auto-clear form after successful add
document.body.addEventListener('htmx:afterRequest', function(evt) {
    if (evt.detail.xhr.status === 200 && evt.target.matches('form[hx-post*="/countries/add"]')) {
        evt.target.reset();
    }
});
</script>