<?php start_section('title'); ?>
Account Settings - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<div class="container wide">
    <!-- Back Navigation -->
    <div class="section" style="margin-bottom: 2rem;">
        <a href="/profile" class="btn text back">← Back to Profile</a>
        <h1 class="product-title">Account Settings</h1>
        <p class="user-email">Manage your account information and security</p>
    </div>

    <!-- Settings Forms -->
    <div class="section">
        
        <!-- Profile Information -->
        <div class="settings-section">
            <h2 class="section-title">Profile Information</h2>
            <div id="profile-info">
                <?php partial('profile/partials/profile_info', ['user' => $user]); ?>
            </div>
        </div>

        <!-- Password Security -->
        <div class="settings-section">
            <h2 class="section-title">Password & Security</h2>
            <div id="profile-password">
                <?php partial('profile/partials/profile_password'); ?>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="settings-section">
            <h3 class="section-subtitle">Account Actions</h3>
            <div class="footer-actions">
                <a href="/logout" class="btn red">Sign Out</a>
            </div>
        </div>
        
    </div>
</div>