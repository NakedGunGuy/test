<form hx-post="<?= url('admin/shipping/countries/update/' . $country['id']) ?>" 
      hx-swap="outerHTML" 
      hx-target="this">
    <input type="hidden" name="country_name" value="<?= htmlspecialchars($country['country_name']) ?>">
    <div class="grid form" style="grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
        <div class="form-group">
            <label class="form-label">Min Delivery Days</label>
            <input type="number" name="estimated_days_min" class="form-input" min="1" value="<?= $country['estimated_days_min'] ?>" required>
        </div>
        <div class="form-group">
            <label class="form-label">Max Delivery Days</label>
            <input type="number" name="estimated_days_max" class="form-input" min="1" value="<?= $country['estimated_days_max'] ?>" required>
        </div>
        <div class="form-group" style="padding-bottom: 0.75rem;">
            <label class="checkbox-wrapper">
                <input type="checkbox" name="is_enabled" value="1" <?= $country['is_enabled'] ? 'checked' : '' ?>>
                <span class="form-label" style="margin: 0;">Enabled for shipping</span>
            </label>
        </div>
        <button type="submit" class="btn-small blue form-group">Update</button>
    </div>
</form>