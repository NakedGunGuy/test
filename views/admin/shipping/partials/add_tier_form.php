<form hx-post="/admin/shipping/weight-tiers/add"
      hx-target="this"
      hx-swap="none"
      id="add-tier-form">
    <div class="grid form" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1rem; align-items: end;">
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
            <input type="text" id="tier_name" name="tier_name" class="form-input" placeholder="e.g., Up to 0.5kg" required>
        </div>
        <div class="form-group">
            <label for="max_weight_kg" class="form-label">Max Weight (kg)</label>
            <input type="number" id="max_weight_kg" name="max_weight_kg" class="form-input" step="0.01" min="0.01" placeholder="0.5" required>
        </div>
        <div class="form-group">
            <label for="price" class="form-label">Price (EUR)</label>
            <input type="number" id="price" name="price" class="form-input" step="0.01" min="0" placeholder="4.99" required>
        </div>
        <button type="submit" class="btn blue" style="margin-bottom: 1.5rem;">Add Tier</button>
    </div>
    <div id="form-error" class="form-error"></div>
</form>