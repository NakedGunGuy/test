<?php /** @var array $products */ ?>
<h1>Products</h1>

<form method="get" class="flex gap-2">
    <label class="block">
        Search product
        <input
            name="name"
            hx-get="/admin/products/search"
            hx-trigger="keyup changed delay:200ms"
            hx-target="#product-results"
            hx-swap="innerHTML"
            class="border rounded p-2 w-full"
            value="<?= htmlspecialchars($_GET['name'] ?? '') ?>"
        >
    </label>
    <div id="product-results" class="absolute bg-white border rounded mt-1 w-full max-h-60 overflow-y-auto z-50"></div>
    <input type="number" name="min_price" placeholder="Min price" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" class="border px-2 py-1" />
    <input type="number" name="max_price" placeholder="Max price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" class="border px-2 py-1" />
    
    <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">Filter</button>
    <?php
$baseUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<a href="<?= $baseUrl ?>" class="bg-gray-300 text-gray-800 px-4 py-1 rounded hover:bg-gray-400">Reset</a>

</form>

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
        <?php partial('page/products/partials/products_table_body', ['products' => $products]); ?>
    </tbody>
</table>