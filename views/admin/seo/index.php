<?php /** @var string $sitemap_url */ ?>
<?php /** @var string $robots_url */ ?>
<?php /** @var int $product_count */ ?>

<?php start_section('title'); ?>SEO Management - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>

<div class="admin-container">
    <h1 class="section-title" style="margin-top: 0;">SEO Management</h1>

    <div class="admin-grid" style="grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <!-- Sitemap Management -->
        <div class="product-section">
            <div class="section-header" style="margin-bottom: 1rem;">
                <span class="section-header-icon">🗺️</span>
                <h2 class="section-subtitle" style="margin: 0;">XML Sitemap</h2>
            </div>

            <div class="stats-grid" style="margin-bottom: 1.5rem;">
                <div class="stat-card">
                    <div class="stat-number"><?= $product_count ?></div>
                    <div class="stat-label">Products in Sitemap</div>
                </div>
            </div>

            <div class="action-buttons" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="<?= htmlspecialchars($sitemap_url) ?>" target="_blank" class="btn blue">
                    View Sitemap
                </a>
                <a href="/admin/seo/sitemap/generate" class="btn black">
                    Download Sitemap
                </a>
            </div>

            <div class="help-text" style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8;">
                The XML sitemap is automatically generated and includes all active products,
                static pages, and proper SEO metadata for search engines.
            </div>
        </div>

        <!-- Robots.txt Management -->
        <div class="product-section">
            <div class="section-header" style="margin-bottom: 1rem;">
                <span class="section-header-icon">🤖</span>
                <h2 class="section-subtitle" style="margin: 0;">Robots.txt</h2>
            </div>

            <div class="action-buttons" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="<?= htmlspecialchars($robots_url) ?>" target="_blank" class="btn blue">
                    View Robots.txt
                </a>
            </div>

            <div class="help-text" style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8;">
                The robots.txt file guides search engine crawlers, blocking admin areas
                and private pages while allowing access to public content.
            </div>
        </div>
    </div>
</div>