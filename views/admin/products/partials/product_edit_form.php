<?php partial('partials/dialog_header', [
        'title' => 'Edit Product',
]) ?>
<!-- Modal body -->
<div class="p-4 md:p-5 space-y-4">
    <form
        id="product_edit_form"
        hx-post="/admin/products/update/<?= $product['id'] ?>" 
        hx-target="#products-table"
        hx-swap="outerHTML"
        data-toast="Product updated successfully!"
        data-close-modal="true">
    <div>
        <input 
            type="text" 
            name="name" 
            value="<?= $product['name'] ?>" 
        >
    </div>
    <div>
        <textarea
            name="description"
        ><?= $product['description'] ?></textarea>
    </div>
    <div>
        <input
            name="is_foil"
            type="checkbox"
            <?= $product['is_foil'] ? 'checked' : '' ?>
        >
    </div>
    <div>
        <input 
            type="number" 
            name="price" 
            value="<?= $product['price'] ?>" 
            step="0.01" 
            <?= $product['in_carts'] > 0 ? 'readonly title="Cannot change price while in carts"' : '' ?>
        >
    </div>
    <div>
        <input 
            type="number" 
            name="quantity"
            value="<?= $product['quantity'] ?>" 
            min="<?= $product['in_carts'] ?>" 
            <?= $product['can_edit_quantity'] ? '' : 'readonly title="Quantity cannot be lower than items in carts"' ?>
        >
        <?php if ($product['in_carts'] > 0): ?>
            <small>(<?= $product['in_carts'] ?> in carts)</small>
        <?php endif; ?>
    </div>
    
    </form>
</div>
<!-- Modal footer -->
<div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
    <button type="submit"
            form="product_edit_form"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
    <button type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
</div>