<?php partial('partials/dialog_header', [
        'title' => 'Add Product',
]) ?>
<!-- Modal body -->
<div class="p-4 md:p-5 space-y-4">
    <label class="block">
        Search edition
        <input
            name="q"
            hx-get="/admin/editions/search"
            hx-trigger="keyup changed delay:200ms"
            hx-target="#edition-results"
            hx-swap="innerHTML"
            class="border rounded p-2 w-full"
        >
    </label>
    <div id="edition-results" class="absolute bg-white border rounded mt-1 w-full max-h-60 overflow-y-auto z-50"></div>
</div>
<!-- Modal footer -->
<div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
    <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
    <button type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
</div>