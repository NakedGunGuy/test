<div class="relative rounded-lg shadow-sm bg-gray-700">
    <?php partial('partials/dialog_header', [
        'title' => 'Title',
    ]) ?>
    <!-- Modal body -->
    <div class="p-4 md:p-5 space-y-4">
        <form 
            id="product_form"
            method="POST"
            hx-post="/admin/products/create" 
            hx-target="#products-table" 
            hx-swap="outerHTML"
            data-toast="Product created successfully!"
            data-close-modal="true"
        >
            <input type="hidden" name="edition_id" value="<?= htmlspecialchars($edition['id'] ?? '') ?>" />
            <input type="hidden" id="name" name="name" placeholder="Name" class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline" value="<?= htmlspecialchars($edition['card_name'] ?? '') ?>" />
            <span>
                <?= htmlspecialchars($edition['card_name']) ?>
    (<?= htmlspecialchars($edition['set_name']) ?>, #<?= htmlspecialchars($edition['collector_number']) ?> - <?= htmlspecialchars($edition['rarity']) ?>)
            </span>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="description">
                    Description
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline" id="description" name="description" placeholder="Description"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="price">
                    Price
                </label>
                <input 
                    type="number" 
                    id="price" 
                    name="price" 
                    min="0" 
                    step="0.01" 
                    required 
                    inputmode="decimal" 
                    placeholder="0.00"
                >
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="quantity">
                    Quantity
                </label>
                <input type="number" id="quantity" name="quantity" placeholder="Quantity" class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline" />
            </div>
        </form>
    </div>
    <!-- Modal footer -->
    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
        <button type="submit" form="product_form"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none 
                focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center 
                dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            I accept
        </button>
        <button type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
    </div>
</div>