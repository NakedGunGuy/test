<?php partial('partials/dialog_header', [
    'title' => 'Add Product',
]) ?>

<div class="dialog-content">
    <form
        id="product_form"
        method="POST"
        hx-post="<?= url('admin/products/create') ?>"
        hx-target="#products-table"
        hx-swap="innerHTML"
        data-toast="Product created successfully!"
        data-close-modal="true"
    >
        <input type="hidden" name="edition_id" value="<?= htmlspecialchars($edition['id'] ?? '') ?>" />
        <input type="hidden" id="name" name="name" value="<?= htmlspecialchars($edition['card_name'] ?? '') ?>" />

        <div class="form-group">
            <div style="font-weight: 600; color: white; margin-bottom: 1rem;">
                <?= htmlspecialchars($edition['card_name']) ?>
                <div style="font-size: 14px; color: #C0C0D1;">
                    <?= htmlspecialchars($edition['set_name']) ?>, #<?= htmlspecialchars($edition['collector_number']) ?> - <?= htmlspecialchars($edition['rarity']) ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea
                class="form-input"
                id="description"
                name="description"
                placeholder="Product description..."
                rows="3"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="price">Price (€)</label>
            <input
                type="number"
                id="price"
                name="price"
                min="0"
                step="0.01"
                required
                inputmode="decimal"
                placeholder="0.00"
                class="form-input"
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="quantity">Quantity</label>
            <input
                type="number"
                id="quantity"
                name="quantity"
                placeholder="Quantity"
                min="0"
                required
                class="form-input"
            />
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" id="is_foil" name="is_foil" />
                <span class="form-label" style="margin-bottom: 0;">Foil Version</span>
            </label>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" id="is_used" name="is_used" />
                <span class="form-label" style="margin-bottom: 0;">Used</span>
            </label>
        </div>
    </form>
</div>

<div class="form-actions">
    <button type="submit" form="product_form" class="btn blue">
        Create Product
    </button>
    <form method="dialog">
        <button type="submit" class="btn black">
            Cancel
        </button>
    </form>
</div>