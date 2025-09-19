<div class="tier-card">
    <form hx-post="<?= url('admin/shipping/weight-tiers/update/' . $tier['id']) ?>" 
          hx-swap="outerHTML" 
          hx-target="this">
        <input type="hidden" name="country_id" value="<?= $tier['country_id'] ?>">
        <div class="tier-grid">
            <div class="form-group tier-name-group">
                <label class="form-label">Tier Name</label>
                <input type="text" name="tier_name" class="form-input" value="<?= htmlspecialchars($tier['tier_name']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Max Weight</label>
                <div class="input-with-unit">
                    <input type="number" name="max_weight_kg" class="form-input" step="0.01" min="0.01" value="<?= $tier['max_weight_kg'] ?>" required>
                    <span class="unit">kg</span>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Price</label>
                <div class="input-with-unit">
                    <input type="number" name="price" class="form-input" step="0.01" min="0" value="<?= $tier['price'] ?>" required>
                    <span class="unit">EUR</span>
                </div>
            </div>
            <div class="form-group tier-status" style="padding-bottom: 0.75rem;">
                <label class="checkbox-wrapper">
                    <input type="checkbox" name="is_enabled" value="1" <?= $tier['is_enabled'] ? 'checked' : '' ?>>
                    <span class="form-label" style="margin: 0;">Enabled</span>
                </label>
            </div>
            <div class="tier-actions form-group">
                <button type="submit" class="btn-small blue">Save</button>
                <button type="button" 
                        hx-post="<?= url('admin/shipping/weight-tiers/delete/' . $tier['id']) ?>" 
                        hx-confirm="Delete this shipping tier?"
                        hx-swap="delete"
                        hx-target="closest .tier-card"
                        class="btn-small red">Delete</button>
            </div>
        </div>
    </form>
</div>