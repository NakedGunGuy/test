<?php /** @var array $products */ ?>
<h1>Products</h1>

<button 
    hx-get="/admin/products/add" 
    hx-target="#dialog" 
    hx-trigger="click"
    class="btn bg-blue-600 text-white px-4 py-2 rounded"
>
    + Add Product
</button>

<table border="1" cellpadding="8" cellspacing="0" width="100%" class="mt-4">
    <thead>
        <tr>
            <th>Name</th>
            <th>Edition</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="products-table">
    <?php foreach ($products as $product): ?>
        <?php view('partials/product_row'); ?>
    <?php endforeach; ?>
    </tbody>
</table>

<div id="dialog"></div>
