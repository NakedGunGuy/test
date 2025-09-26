<?php /** @var array $products */ ?>
<?php start_section('title'); ?>
Products - Admin - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<!-- Page Header -->
<div class="section" style="margin-bottom: 2rem;">
    <a href="<?= url('admin') ?>" class="btn text back">← Back to Dashboard</a>
    <h1 class="section-title" style="margin-top: 0;">Products Management</h1>
    <p style="color: #C0C0D1;">Manage your store inventory and product listings</p>
</div>

<!-- Products Section -->
<div class="section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 class="section-subtitle" style="margin-bottom: 0;">Product Inventory</h2>
            <div style="display: flex; gap: 12px;">
                <a href="<?= url('admin/products/bulk') ?>" class="btn">
                    📦 Bulk Add
                </a>
                <button
                    hx-get="<?= url('admin/products/add') ?>"
                    hx-target="#dialog"
                    hx-trigger="click"
                    class="btn blue"
                >
                    + Add Product
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <form method="get" style="margin-bottom: 2rem;">
            <div class="grid form" style="grid-template-columns: 1fr 1fr;">
                <div style="position: relative;">
                    <label class="form-label">Search Products</label>
                    <input
                        name="name"
                        hx-get="<?= url('admin/products/search') ?>"
                        hx-trigger="keyup changed delay:200ms"
                        hx-target="#product-results"
                        hx-swap="innerHTML"
                        class="form-input"
                        placeholder="Search by name..."
                        value="<?= htmlspecialchars($_GET['name'] ?? '') ?>"
                    >
                    <div id="product-results" style="position: absolute; top: 100%; left: 0; right: 0; background: #1E1E27; border: 1px solid #C0C0D133; border-radius: 12px; margin-top: 4px; max-height: 300px; overflow-y: auto; z-index: 50; display: none;"></div>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <div style="flex: 1;">
                        <label class="form-label">Min Price</label>
                        <input type="number" name="min_price" placeholder="0.00" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" class="form-input" step="0.01" />
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label">Max Price</label>
                        <input type="number" name="max_price" placeholder="999.99" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" class="form-input" step="0.01" />
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn blue">Apply Filters</button>
                <?php $baseUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
                <a href="<?= $baseUrl ?>" class="btn black">Reset</a>
            </div>
        </form>

        <!-- Products Table -->
        <div class="grid">
            <div class="grid-header" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr;">
                <div class="header-cell">Product</div>
                <div class="header-cell">Edition</div>
                <div class="header-cell">Price</div>
                <div class="header-cell">Stock</div>
                <div class="header-cell">Foil</div>
                <div class="header-cell">Actions</div>
            </div>
            <div class="grid-body" id="products-table">
                <?php partial('admin/products/partials/products_table_body', ['products' => $products]); ?>
            </div>
        </div>
</div>