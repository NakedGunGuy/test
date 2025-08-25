<?php partial('partials/dialog_header', [
    'title' => 'Add Product',
]) ?>
<!-- Modal body -->
<div class="p-4 md:p-5 space-y-4">
    <table border="1" cellpadding="8" cellspacing="0" width="100%" class="mt-4">
    <thead>
        <tr>
            <th>Name</th>
            <th>Edition</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Foil?</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="products-table">
        <?php partial('admin/products/partials/products_table_body', ['products' => $products, 'actions' => ['edit', 'delete']]); ?>
    </tbody>
</table>
</div>
<!-- Modal footer -->
<div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
    <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
    <button type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
</div>