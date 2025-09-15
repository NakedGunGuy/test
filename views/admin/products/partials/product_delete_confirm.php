<?php partial('partials/dialog_header', [
    'title' => 'Confirm Delete',
]) ?>

<div class="dialog-content">
    <div class="message-container" style="text-align: center; padding: 2rem 1rem;">
        <div class="message-icon error" style="margin: 0 auto 1rem;">
            ⚠️
        </div>
        <h3 style="color: #FF6B6B; margin-bottom: 0.5rem;">Delete Product</h3>
        <p style="color: #C0C0D1;">Are you sure you want to delete this product? This action cannot be undone.</p>
    </div>
</div>

<div class="form-actions">
    <button type="button"
        hx-post="/admin/products/delete/<?= $product_id ?>"
        hx-target="#products-table"
        hx-swap="outerHTML"
        data-toast="Product deleted successfully!"
        data-close-modal="true"
        class="btn red">
        Delete Product
    </button>
    <button type="button" onclick="closeDialog()" class="btn black">
        Cancel
    </button>
</div>