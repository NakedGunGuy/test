<?php /** @var array $products */ ?>
<?php /** @var array $seo_data */ ?>

<?php if (isset($seo_data)): ?>
    <?php start_section('seo_data'); echo serialize($seo_data); end_section('seo_data'); ?>
<?php endif; ?>

<?php start_section('page_title'); ?><?= t('nav.discover') ?><?php end_section('page_title'); ?>

<div class="section" style="margin-bottom: 2rem; z-index: 5; position:relative; ">
    <h3 class="section-header">
        <span class="section-header-icon"><?= icon('search') ?></span><?= t('products.search_filter') ?>
    </h3>

    <!-- Mobile/Tablet Filter Toggle Button -->
    <button type="button" class="filter-toggle-btn" id="filter-toggle-btn">
        <span>
            <span class="filter-icon"><?= icon('search') ?></span>
            <span><?= t('products.search_filter') ?></span>
        </span>
        <span class="filter-arrow">▼</span>
    </button>

    <form method="get" class="search-form" id="search-form" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto auto;">
        <div class="form-group" style="position: relative; margin-bottom: 0;">
            <label class="form-label"><?= t('products.search_cards') ?></label>
            <input
                id="search-input"
                class="form-input"
                name="name"
                hx-get="<?= url('products/search') ?>"
                hx-trigger="keyup changed delay:300ms"
                hx-target="#product-results"
                hx-swap="innerHTML"
                value="<?= htmlspecialchars($_GET['name'] ?? '') ?>"
                placeholder="<?= t('products.enter_card_name') ?>"
                autocomplete="off"
            >
            <div id="product-results" class="search-results" style="display: none;"></div>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label"><?= t('products.set') ?></label>
            <select class="form-input" name="set_id">
                <option value=""><?= t('common.all') ?></option>
                <?php foreach ($sets as $set): ?>
                    <option value="<?= $set['id'] ?>" <?= (isset($_GET['set_id']) && $_GET['set_id'] == $set['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($set['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label"><?= t('products.foil') ?></label>
            <select class="form-input" name="is_foil">
                <option value=""><?= t('common.all') ?></option>
                <option value="yes" <?= (isset($_GET['is_foil']) && $_GET['is_foil'] === 'yes') ? 'selected' : '' ?>><?= t('common.yes') ?></option>
                <option value="no" <?= (isset($_GET['is_foil']) && $_GET['is_foil'] === 'no') ? 'selected' : '' ?>><?= t('common.no') ?></option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label"><?= t('products.min_price') ?></label>
            <input class="form-input" type="number" name="min_price" placeholder="€0" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"/>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label"><?= t('products.max_price') ?></label>
            <input class="form-input" type="number" name="max_price" placeholder="€1000" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"/>
        </div>

        <button type="submit" class="btn blue filter-button" style="box-sizing: border-box; height: auto; line-height: normal;"><?= t('common.filter') ?></button>

        <a href="<?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?>" class="btn black reset-button" style="box-sizing: border-box; height: auto; line-height: normal; display: inline-flex; align-items: center;"><?= t('common.reset') ?></a>
    </form>
</div>

<div class="section">
    <div class="products-stats">
        <h3 class="products-count"><?= icon('file') ?> <?= t('products.products_found', ['count' => $pagination['total_products']]) ?><?= $pagination['total_pages'] > 1 ? ', page ' . $pagination['current_page'] . ' of ' . $pagination['total_pages'] : '' ?></h3>
        <div class="products-controls">
            <div class="per-page-control">
                <select aria-label="<?= t('aria.show_products_per_page') ?>" id="per_page_select" onchange="changePerPage(this.value)" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #C0C0D133; background: #1E1E27; color: #fff; font-size: 14px;">
                    <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $pagination['per_page'] == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $pagination['per_page'] == 100 ? 'selected' : '' ?>>100</option>
                </select>
            </div>
            <div class="view-toggle">
                <span class="view-toggle-label"><?= t('products.view') ?></span>
                <button class="view-toggle-btn <?= ($_SESSION['view_preference'] ?? 'grid') === 'grid' ? 'active' : 'inactive' ?>" data-view="grid"><?= t('products.grid') ?></button>
                <button class="view-toggle-btn <?= ($_SESSION['view_preference'] ?? 'grid') === 'list' ? 'active' : 'inactive' ?>" data-view="list"><?= t('products.list') ?></button>
                <button class="view-toggle-btn <?= ($_SESSION['view_preference'] ?? 'grid') === 'box' ? 'active' : 'inactive' ?>" data-view="box"><?= t('products.box') ?></button>
            </div>
        </div>
    </div>
    
    <!-- Grid View -->
    <div id="grid-view" class="view-container <?= ($_SESSION['view_preference'] ?? 'grid') === 'grid' ? '' : 'hidden' ?>">
        <div class="grid">
            <div class="grid-header" style="grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr;">
                <div class="header-cell">
                    <span class="grid-header-with-icon">
                        <span>🃏</span><?= t('products.card_name') ?>
                    </span>
                </div>
                <div class="header-cell"><?= t('products.edition') ?></div>
                <div class="header-cell"><?= t('products.price') ?></div>
                <div class="header-cell"><?= t('products.stock') ?></div>
                <div class="header-cell"><?= t('products.foil') ?></div>
                <div class="header-cell"><?= t('products.actions') ?></div>
            </div>
            <div id="products-table" class="grid-body">
                <?php partial('page/products/partials/products_table_body', ['products' => $products]); ?>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div id="list-view" class="view-container <?= ($_SESSION['view_preference'] ?? 'grid') === 'list' ? '' : 'hidden' ?>">
        <div class="list-container">
            <?php foreach ($products as $product): ?>
                <?php
                $svg = '
                <svg width="60" height="60" xmlns="http://www.w3.org/2000/svg">
                  <rect width="60" height="60" rx="8" ry="8" fill="rgb(39, 39, 39)" />
                </svg>';
                $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
                ?>
                <div class="list-item">
                    <div class="list-item-content">
                        <div class="list-item-image">
                            <img
                                hx-get="<?= url('cards/image/' . $product['edition_slug']) ?>"
                                hx-target="#dialog"
                                hx-trigger="click"
                                height="60" width="60"
                                src="<?= $dataUri ?>"
                                alt="<?= t('placeholder.card_image') ?>"
                                data-src="<?= card_image($product['edition_slug']) ?>"
                            />
                        </div>
                        <div class="list-item-main">
                            <h3 class="list-item-title">
                                <a href="<?= url('product/' . $product['id']) ?>"><?= htmlspecialchars($product['name']) ?></a>
                            </h3>
                            <p class="list-item-details"><?= htmlspecialchars($product['set_name']) ?> • €<?= number_format($product['price'], 2) ?></p>
                            <div class="list-item-meta">
                                <span class="stock-badge <?= $product['quantity'] > 0 ? ($product['quantity'] <= 5 ? 'low' : 'in-stock') : 'out-of-stock' ?>">
                                    <?= $product['quantity'] > 0 ? $product['quantity'] . ' in stock' : 'Out of stock' ?>
                                </span>
                                <?php if ($product['is_foil']): ?>
                                    <span class="foil-badge">✨ Foil</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="list-item-actions">
                            <?php if ($product['quantity'] > 0): ?>
                                <form hx-post="<?= url('cart/add') ?>" hx-swap="outerHTML" class="add-to-cart-form">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <div class="quantity-selector">
                                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" class="qty-input">
                                        <button type="submit" class="btn blue btn-small"><?= t('button.add_to_cart') ?></button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Box View -->
    <div id="box-view" class="view-container <?= ($_SESSION['view_preference'] ?? 'grid') === 'box' ? '' : 'hidden' ?>">
        <div class="box-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php foreach ($products as $product): ?>
                <?php
                $svg = '
                <svg width="100%" height="280" xmlns="http://www.w3.org/2000/svg">
                  <rect width="100%" height="280" rx="8" ry="8" fill="rgb(39, 39, 39)" />
                </svg>';
                $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
                ?>
                <div class="product-card">
                    <div class="product-card-image">
                        <img
                            hx-get="<?= url('cards/image/' . $product['edition_slug']) ?>"
                            hx-target="#dialog"
                            hx-trigger="click"
                            src="<?= $dataUri ?>"
                            alt="Card image"
                            data-src="<?= card_image($product['edition_slug']) ?>"
                        />
                    </div>

                    <div class="product-card-header">
                        <h3 class="product-card-title">
                            <a href="<?= url('product/' . $product['id']) ?>"><?= htmlspecialchars($product['name']) ?></a>
                        </h3>
                        <div class="product-card-subtitle"><?= htmlspecialchars($product['set_name']) ?></div>
                    </div>

                    <div class="product-card-body">
                        <div class="product-card-price">€<?= number_format($product['price'], 2) ?></div>

                        <div class="product-card-details">
                            <div class="stock-info">
                                <span class="stock-badge <?= $product['quantity'] > 0 ? ($product['quantity'] <= 5 ? 'low' : 'in-stock') : 'out-of-stock' ?>">
                                    <?= $product['quantity'] > 0 ? $product['quantity'] . ' in stock' : 'Out of stock' ?>
                                </span>
                            </div>

                            <?php if ($product['is_foil']): ?>
                                <div class="foil-info">
                                    <span class="foil-badge">✨ Foil</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-card-footer">
                        <?php if ($product['quantity'] > 0): ?>
                            <form hx-post="<?= url('cart/add') ?>" hx-swap="outerHTML" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <div class="quantity-selector" style="display: flex; gap: 0.5rem; align-items: center;">
                                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" class="qty-input" style="width: 60px;">
                                    <button type="submit" class="btn blue btn-small" style="flex: 1;"><?= t('button.add_to_cart') ?></button>
                                </div>
                            </form>
                        <?php else: ?>
                            <button disabled class="btn btn-disabled" style="width: 100%;"><?= t('status.out_of_stock') ?></button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php partial('partials/pagination', ['pagination' => $pagination]); ?>
</div>

<?php start_section('js'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle filter toggle on mobile/tablet
    const filterToggleBtn = document.getElementById('filter-toggle-btn');
    const searchForm = document.getElementById('search-form');

    if (filterToggleBtn && searchForm) {
        filterToggleBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            searchForm.classList.toggle('active');
        });
    }

    // Handle view toggle buttons
    const viewButtons = document.querySelectorAll('.view-toggle-btn');
    const viewContainers = {
        grid: document.getElementById('grid-view'),
        list: document.getElementById('list-view'),
        box: document.getElementById('box-view')
    };

    // Add click event listeners to all view toggle buttons
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const selectedView = this.getAttribute('data-view');

            // Update button states
            viewButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('inactive');
            });
            this.classList.remove('inactive');
            this.classList.add('active');

            // Show/hide view containers
            Object.keys(viewContainers).forEach(view => {
                if (view === selectedView) {
                    viewContainers[view].classList.remove('hidden');
                } else {
                    viewContainers[view].classList.add('hidden');
                }
            });

            // Save preference to session
            fetch('<?= url('set-view-preference') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'view=' + encodeURIComponent(selectedView)
            }).catch(error => {
                console.error('Error saving view preference:', error);
            });
        });
    });

    // Handle search results
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('product-results');

    if (searchInput && searchResults) {
        // Show search results when they get content
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    if (searchResults.innerHTML.trim() !== '') {
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.style.display = 'none';
                    }
                }
            });
        });
        observer.observe(searchResults, { childList: true, subtree: true });

        // Hide search results when clicking away
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.style.display = 'none';
            }
        });

        // Show search results when focusing input (if it has content)
        searchInput.addEventListener('focus', function() {
            if (searchResults.innerHTML.trim() !== '') {
                searchResults.style.display = 'block';
            }
        });

        // Hide search results when input is cleared
        searchInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                searchResults.style.display = 'none';
            }
        });
    }
});
</script>
<?php end_section('js'); ?>