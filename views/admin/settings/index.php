<?php start_section('title'); ?>
Settings - Admin - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<!-- Page Header -->
<div class="section" style="margin-bottom: 2rem;">
    <a href="/admin" class="btn text back">← Back to Dashboard</a>
    <h1 class="section-title" style="margin-top: 0;">Store Settings</h1>
    <p style="color: #C0C0D1;">Configure store settings and preferences</p>
</div>

<!-- Settings Form -->
<form id="settings-form" method="POST" action="/admin/settings">
    <!-- Store Information -->
    <div class="section">
        <h2 class="section-subtitle">Store Information</h2>
        <div class="grid form" style="grid-template-columns: 1fr 1fr;">
            <div class="form-group">
                <label class="form-label">Store Name</label>
                <input type="text" name="store_name" class="form-input" value="<?= htmlspecialchars($_ENV['APP_NAME'] ?? 'Cardpoint') ?>" readonly>
                <div class="form-help">Store name is configured in environment settings</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Store Status</label>
                <select name="store_status" class="form-input">
                    <option value="open" <?= ($settings['store_status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="maintenance" <?= ($settings['store_status'] ?? 'open') === 'maintenance' ? 'selected' : '' ?>>Maintenance Mode</option>
                    <option value="closed" <?= ($settings['store_status'] ?? 'open') === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
                <div class="form-help">Control whether customers can place orders</div>
            </div>
        </div>
    </div>

    <!-- Order Settings -->
    <div class="section">
        <h2 class="section-subtitle">Order Settings</h2>
        <div class="grid form" style="grid-template-columns: 1fr 1fr;">
            <div class="form-group">
                <label class="form-label">Default Order Status</label>
                <select name="default_order_status" class="form-input">
                    <option value="pending" <?= ($settings['default_order_status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= ($settings['default_order_status'] ?? 'pending') === 'processing' ? 'selected' : '' ?>>Processing</option>
                </select>
                <div class="form-help">Status assigned to new orders</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Low Stock Threshold</label>
                <input type="number" name="low_stock_threshold" class="form-input" value="<?= htmlspecialchars($settings['low_stock_threshold'] ?? 5) ?>" min="0">
                <div class="form-help">Alert when product quantity falls below this number</div>
            </div>
        </div>
    </div>

    <!-- Email Settings -->
    <div class="section">
        <h2 class="section-subtitle">Email Settings</h2>
        <div class="grid form" style="grid-template-columns: 1fr 1fr;">
            <div class="form-group">
                <label class="form-label">Order Notification Email</label>
                <input type="email" name="notification_email" class="form-input" value="<?= htmlspecialchars($settings['notification_email'] ?? '') ?>" placeholder="admin@cardpoint.com">
                <div class="form-help">Email address to receive order notifications</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Enable Email Notifications</label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-top: 0.5rem;">
                    <input type="checkbox" name="email_notifications" <?= ($settings['email_notifications'] ?? 1) ? 'checked' : '' ?>>
                    <span style="font-size: 14px;">Send email notifications for new orders</span>
                </label>
            </div>
        </div>
    </div>
</form>

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
    <div class="grid actions" style="max-width: 500px; grid-template-columns: 1fr;">
        <div class="card action">
            <span class="icon">🗑️</span>
            <div class="content">
                <div class="title">Clear Cache</div>
                <div class="desc">Clear system cache and temporary files</div>
            </div>
            <form method="POST" action="/admin/settings/clear-cache" style="display: inline;">
                <button type="submit" class="btn blue" style="min-width: auto; padding: 8px 12px;">Clear</button>
            </form>
        </div>
        
        <div class="card action">
            <span class="icon">💾</span>
            <div class="content">
                <div class="title">Backup Database</div>
                <div class="desc">Create a backup of the database</div>
            </div>
            <form method="POST" action="/admin/settings/backup-database" style="display: inline;">
                <button type="submit" class="btn blue" style="min-width: auto; padding: 8px 12px;">Backup</button>
            </form>
        </div>
        
        <div class="card action">
            <span class="icon">🔄</span>
            <div class="content">
                <div class="title">Import Cards</div>
                <div class="desc">Update card database from external API</div>
            </div>
            <form method="POST" action="/admin/settings/import-cards" style="display: inline;">
                <button type="submit" class="btn blue" style="min-width: auto; padding: 8px 12px;">Import</button>
            </form>
        </div>
    </div>
</div>

<!-- Save Settings -->
<div class="section">
    <div class="form-actions">
        <button type="submit" form="settings-form" class="btn blue">Save Settings</button>
        <button type="reset" form="settings-form" class="btn black">Reset to Defaults</button>
    </div>
</div>