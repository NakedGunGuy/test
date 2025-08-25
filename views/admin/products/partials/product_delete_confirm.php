<?php partial('partials/dialog_header', [
        'title' => 'Confirm Delete',
]) ?>
<!-- Modal body -->
<div class="p-4 md:p-5 space-y-4">
    <p>Are you sure you want to delete this product?</p>
</div>
<!-- Modal footer -->
<div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
    <button type="button"
        hx-post="/admin/products/delete/<?php echo $product_id; ?>" 
        hx-target="#products-table"
        hx-swap="outerHTML"
        data-toast="Product deleted successfully!"
        data-close-modal="true" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
    <button type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
</div>