<?php partial('partials/dialog_header', [
        'title' => 'Edit Product',
]) ?>

<div class="content">
    <form
        id="product_edit_form"
        hx-post="/admin/products/update/<?= $product['id'] ?>" 
        hx-target="#products-table"
        hx-swap="outerHTML"
        data-toast="Product updated successfully!"
        data-close-modal="true">
        
        <div class="form-group">
            <label class="form-label">Product Name</label>
            <input 
                type="text" 
                name="name" 
                value="<?= htmlspecialchars($product['name']) ?>" 
                class="form-input"
                required
            >
        </div>
        
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea
                name="description"
                class="form-input"
                rows="3"
                placeholder="Product description..."
            ><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        
        <div class="grid form" style="grid-template-columns: 1fr 1fr;">
            <div class="form-group">
                <label class="form-label">Price ($)</label>
                <input 
                    type="number" 
                    name="price" 
                    value="<?= $product['price'] ?>" 
                    step="0.01" 
                    min="0"
                    class="form-input"
                    <?= $product['in_carts'] > 0 ? 'readonly title="Cannot change price while in carts"' : '' ?>
                    required
                >
                <?php if ($product['in_carts'] > 0): ?>
                    <div class="form-help">Price locked - product is in customer carts</div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label class="form-label">Quantity</label>
                <input 
                    type="number" 
                    name="quantity"
                    value="<?= $product['quantity'] ?>" 
                    min="<?= $product['in_carts'] ?>" 
                    class="form-input"
                    <?= $product['can_edit_quantity'] ? '' : 'readonly title="Quantity cannot be lower than items in carts"' ?>
                    required
                >
                <?php if ($product['in_carts'] > 0): ?>
                    <div class="form-help"><?= $product['in_carts'] ?> items currently in customer carts</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input
                    name="is_foil"
                    type="checkbox"
                    <?= $product['is_foil'] ? 'checked' : '' ?>
                >
                <span class="form-label" style="margin-bottom: 0;">Foil Version</span>
            </label>
        </div>
    </form>
</div>

<div class="form-actions" style="border-top: 1px solid #C0C0D133; padding-top: 1.5rem;">
    <button type="submit"
            form="product_edit_form"
            class="btn blue">
        Update Product
    </button>
    <button type="button" 
            onclick="closeDialog()" 
            class="btn black">
        Cancel
    </button>
</div>