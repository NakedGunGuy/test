<?php /** @var array $products */ ?>
<?php start_section('title'); ?>Discover Cards - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>
<?php start_section('page_title'); ?>Discover<?php end_section('page_title'); ?>

<div class="section" style="margin-bottom: 2rem;">
    <h3 class="section-header">
        <span class="section-header-icon">🔍</span>Search & Filter
    </h3>
    <form method="get" class="search-form">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Search Cards</label>
            <input
                class="form-input"
                name="name"
                hx-get="/admin/products/search"
                hx-trigger="keyup changed delay:300ms"
                hx-target="#product-results"
                hx-swap="innerHTML"
                value="<?= htmlspecialchars($_GET['name'] ?? '') ?>"
                placeholder="Enter card name..."
            >
            <div id="product-results" class="search-results"></div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Min Price</label>
            <input class="form-input" type="number" name="min_price" placeholder="€0" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"/>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Max Price</label>
            <input class="form-input" type="number" name="max_price" placeholder="€1000" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"/>
        </div>
        
        <button type="submit" class="btn blue filter-button">Filter</button>
        
        <a href="<?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?>" class="btn black reset-button">Reset</a>
    </form>
</div>

<div class="section">
    <div class="products-stats">
        <h3 class="products-count">📄 Products (<?= $pagination['total_products'] ?> found<?= $pagination['total_pages'] > 1 ? ', page ' . $pagination['current_page'] . ' of ' . $pagination['total_pages'] : '' ?>)</h3>
        <div class="products-controls">
            <div class="per-page-control">
                <select aria-label="Show products per page" id="per_page_select" onchange="changePerPage(this.value)" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #C0C0D133; background: #1E1E27; color: #fff; font-size: 14px;">
                    <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $pagination['per_page'] == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $pagination['per_page'] == 100 ? 'selected' : '' ?>>100</option>
                </select>
            </div>
            <div class="view-toggle">
                <span class="view-toggle-label">View:</span>
                <button class="view-toggle-btn active">Grid</button>
                <button class="view-toggle-btn inactive">List</button>
            </div>
        </div>
    </div>
    
    <div class="grid">
        <div class="grid-header">
            <div class="header-cell">
                <span class="grid-header-with-icon">
                    <span>🃏</span>Card Name
                </span>
            </div>
            <div class="header-cell">Edition</div>
            <div class="header-cell">Price</div>
            <div class="header-cell">Stock</div>
            <div class="header-cell">Foil</div>
            <div class="header-cell">Actions</div>
        </div>
        <div id="products-table" class="grid-body">
            <?php partial('page/products/partials/products_table_body', ['products' => $products]); ?>
        </div>
    </div>
    
    <?php partial('partials/pagination', ['pagination' => $pagination]); ?>
</div>