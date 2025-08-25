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

<form 
    id="filters-form"
    hx-get="/admin/products" 
    hx-target="#products-table" 
    hx-push-url="true"
    class="flex gap-2"
>
    <input type="text" name="name" placeholder="Name" class="border px-2 py-1" />
    <input type="number" name="min_price" placeholder="Min price" class="border px-2 py-1" />
    <input type="number" name="max_price" placeholder="Max price" class="border px-2 py-1" />
    
    <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">Filter</button>
</form>


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
        <?php partial('admin/products/partials/products_table_body', ['products' => $products]); ?>
    </tbody>
</table>