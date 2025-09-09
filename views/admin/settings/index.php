<?php start_section('title'); ?>
Settings - Admin - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<!-- Page Header -->
<div class="section" style="margin-bottom: 2rem;">
    <a href="/admin" class="btn text back">← Back to Dashboard</a>
    <h1 class="section-title" style="margin-top: 0;">Store Settings</h1>
    <p style="color: #C0C0D1;">Configure store settings and preferences</p>
</div>

<!-- Store Information -->
<div class="section">
    <h2 class="section-subtitle">Store Information</h2>
    <form class="grid form">
        <div class="form-group">
            <label class="form-label">Store Name</label>
            <input type="text" name="store_name" class="form-input" value="<?= htmlspecialchars($_ENV['APP_NAME'] ?? 'Cardpoint') ?>" readonly>
            <div class="form-help">Store name is configured in environment settings</div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Store Status</label>
            <select name="store_status" class="form-input">
                <option value="open">Open</option>
                <option value="maintenance">Maintenance Mode</option>
                <option value="closed">Closed</option>
            </select>
            <div class="form-help">Control whether customers can place orders</div>
        </div>
    </form>
</div>

<!-- Order Settings -->
<div class="section">
    <h2 class="section-subtitle">Order Settings</h2>
    <form class="grid form">
        <div class="form-group">
            <label class="form-label">Default Order Status</label>
            <select name="default_order_status" class="form-input">
                <option value="pending" selected>Pending</option>
                <option value="processing">Processing</option>
            </select>
            <div class="form-help">Status assigned to new orders</div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Low Stock Threshold</label>
            <input type="number" name="low_stock_threshold" class="form-input" value="5" min="0">
            <div class="form-help">Alert when product quantity falls below this number</div>
        </div>
    </form>
</div>

<!-- Email Settings -->
<div class="section">
    <h2 class="section-subtitle">Email Settings</h2>
    <form class="grid form">
        <div class="form-group">
            <label class="form-label">Order Notification Email</label>
            <input type="email" name="notification_email" class="form-input" placeholder="admin@cardpoint.com">
            <div class="form-help">Email address to receive order notifications</div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Enable Email Notifications</label>
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-top: 0.5rem;">
                <input type="checkbox" name="email_notifications" checked>
                <span style="font-size: 14px;">Send email notifications for new orders</span>
            </label>
        </div>
    </form>
</div>

<!-- System Information -->
<div class="section">
    <h2 class="section-subtitle">System Information</h2>
    <div class="grid stats" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="card stat">
            <div class="icon">🗄️</div>
            <div class="info">
                <div class="number">SQLite</div>
                <div class="label">Database</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">🐘</div>
            <div class="info">
                <div class="number"><?= phpversion() ?></div>
                <div class="label">PHP Version</div>
            </div>
        </div>
        <div class="card stat">
            <div class="icon">📁</div>
            <div class="info">
                <div class="number"><?= number_format(diskfreespace('.') / 1024 / 1024 / 1024, 1) ?>GB</div>
                <div class="label">Disk Space</div>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Actions -->
<div class="section">
    <h2 class="section-subtitle">Maintenance</h2>
    <div class="grid actions" style="max-width: 500px;">
        <div class="card action">
            <span class="icon">🗑️</span>
            <div class="content">
                <div class="title">Clear Cache</div>
                <div class="desc">Clear system cache and temporary files</div>
            </div>
            <button class="btn blue" style="min-width: auto; padding: 8px 12px;">Clear</button>
        </div>
        
        <div class="card action">
            <span class="icon">💾</span>
            <div class="content">
                <div class="title">Backup Database</div>
                <div class="desc">Create a backup of the database</div>
            </div>
            <button class="btn blue" style="min-width: auto; padding: 8px 12px;">Backup</button>
        </div>
        
        <div class="card action">
            <span class="icon">🔄</span>
            <div class="content">
                <div class="title">Import Cards</div>
                <div class="desc">Update card database from external API</div>
            </div>
            <button class="btn blue" style="min-width: auto; padding: 8px 12px;">Import</button>
        </div>
    </div>
</div>

<!-- Save Settings -->
<div class="section">
    <div class="form-actions">
        <button class="btn blue">Save Settings</button>
        <button class="btn black">Reset to Defaults</button>
    </div>
</div>