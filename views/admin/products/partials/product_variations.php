<?php partial('partials/dialog_header', [
    'title' => 'Product Variations',
]) ?>

<div class="dialog-content">
    <div class="grid">
        <div class="grid-header" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr;">
            <div class="header-cell">Name</div>
            <div class="header-cell">Edition</div>
            <div class="header-cell">Price</div>
            <div class="header-cell">Quantity</div>
            <div class="header-cell">Foil?</div>
            <div class="header-cell">Actions</div>
        </div>
        <div class="grid-body" id="products-table">
            <?php partial('admin/products/partials/products_table_body', ['products' => $products, 'actions' => ['edit', 'delete']]); ?>
        </div>
    </div>
</div>

<div class="form-actions">
    <button
            class="btn blue"
            hx-get="<?= url('admin/products/edition/' . htmlspecialchars($edition_id) . '/new') ?>"
            hx-target="#dialog"
            hx-swap="innerHTML"
    >Create New</button>
    <form method="dialog">
        <button type="submit" class="btn black">
            Close
        </button>
    </form>
</div>