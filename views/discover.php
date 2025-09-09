<?php /** @var array $products */ ?>
<h1>Products</h1>

<form method="get">
    <label class="block">
        Search product
        <input
            name="name"
            hx-get="/admin/products/search"
            hx-trigger="keyup changed delay:200ms"
            hx-target="#product-results"
            hx-swap="innerHTML"
            value="<?= htmlspecialchars($_GET['name'] ?? '') ?>"
        >
    </label>
    <div id="product-results"></div>
    <input type="number" name="min_price" placeholder="Min price" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"/>
    <input type="number" name="max_price" placeholder="Max price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"/>
    
    <button type="submit">Filter</button>
    <?php
$baseUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<a href="<?= $baseUrl ?>" class="btn black">Reset</a>

</form>

<div class="products-grid">
    <div class="products-header">
        <div class="header-cell">
            <span>Name</span><img class="sort" src="assets/sort.svg" alt="Sort items in ascending or descending order">
        </div>
        <div class="header-cell">Edition</div>
        <div class="header-cell">Price</div>
        <div class="header-cell">Quantity</div>
        <div class="header-cell">Foil?</div>
        <div class="header-cell">Actions</div>
    </div>
    <div id="products-table" class="products-body">
        <?php partial('page/products/partials/products_table_body', ['products' => $products]); ?>
    </div>
</div>