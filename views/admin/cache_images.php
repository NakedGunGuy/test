<?php
start_section('title');
echo 'Card Image Cache';
end_section('title');

$stats = get_card_image_cache_stats();
?>

<div class="container">
    <div class="section-header">
        <span class="section-header-icon">🖼️</span>
        Card Image Cache Management
    </div>

    <!-- Cache Statistics -->
    <div class="section">
        <h3 class="section-subtitle">Cache Statistics</h3>
        <div class="grid stats">
            <div class="card stat">
                <div class="icon">📁</div>
                <div class="info">
                    <span class="number"><?= $stats['total_files'] ?></span>
                    <div class="label">Cached Images</div>
                </div>
            </div>
            <div class="card stat">
                <div class="icon">💾</div>
                <div class="info">
                    <span class="number"><?= $stats['total_size_mb'] ?> MB</span>
                    <div class="label">Cache Size</div>
                </div>
            </div>
        </div>
        
        <?php if ($stats['total_files'] > 0): ?>
        <div class="grid form" style="grid-template-columns: 1fr 1fr; margin-top: 1rem;">
            <div>
                <strong>Oldest Image:</strong><br>
                <span class="status-text"><?= $stats['oldest_file'] ?? 'N/A' ?></span>
            </div>
            <div>
                <strong>Newest Image:</strong><br>
                <span class="status-text"><?= $stats['newest_file'] ?? 'N/A' ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Cache Actions -->
    <div class="section">
        <h3 class="section-subtitle">Cache Management</h3>
        <div class="grid actions">
            <form hx-post="/admin/cache-images/refresh" hx-swap="outerHTML" hx-target="this">
                <button type="submit" class="btn blue">
                    🔄 Cache All Missing Images
                </button>
                <div class="form-help">Download and cache any missing card images from the API</div>
            </form>

            <form hx-post="/admin/cache-images/clear-old" hx-swap="outerHTML" hx-target="this">
                <div class="form-group">
                    <label for="max_age" class="form-label">Clear Old Images</label>
                    <select name="max_age" id="max_age" class="form-input">
                        <option value="30">Older than 30 days</option>
                        <option value="7">Older than 7 days</option>
                        <option value="1">Older than 1 day</option>
                    </select>
                </div>
                <button type="submit" class="btn black">
                    🗑️ Clear Old Images
                </button>
                <div class="form-help">Remove cached images older than the selected time period</div>
            </form>

            <form hx-post="/admin/cache-images/clear-all" hx-swap="outerHTML" hx-target="this" 
                  hx-confirm="Are you sure you want to clear ALL cached images? This cannot be undone.">
                <button type="submit" class="btn red">
                    ⚠️ Clear All Cache
                </button>
                <div class="form-help">Remove all cached images (requires confirmation)</div>
            </form>
        </div>
    </div>

    <!-- Usage Instructions -->
    <div class="section">
        <h3 class="section-subtitle">Usage Information</h3>
        <div class="grid form">
            <div>
                <h4>Automatic Caching</h4>
                <p class="status-text">Images are automatically cached when first accessed. If an image isn't cached, it will be downloaded from the API and stored locally.</p>
            </div>
            <div>
                <h4>Console Commands</h4>
                <p class="status-text">You can also use the console command: <code>php console/cache_card_images.php</code> to batch cache all images.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Show progress for cache refresh
document.body.addEventListener('htmx:afterRequest', function(evt) {
    if (evt.detail.xhr.status === 200 && evt.target.closest('form[hx-post*="cache-images"]')) {
        // Refresh page after successful cache operation
        setTimeout(() => window.location.reload(), 1000);
    }
});
</script>