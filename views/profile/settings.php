<?php start_section('title'); ?>
Account Settings - <?= htmlspecialchars($_ENV['APP_NAME']) ?>
<?php end_section('title'); ?>

<div class="container wide">
    <!-- Back Navigation -->
    <div class="section" style="margin-bottom: 2rem;">
        <a href="<?= url('profile') ?>" class="btn text back"><?= t('profile.back_to_profile') ?></a>
        <h1 class="product-title"><?= t('profile.account_settings_title') ?></h1>
        <p class="user-email"><?= t('profile.account_settings_description') ?></p>
    </div>

    <!-- Settings Forms -->
    <div class="section">

        <!-- Profile Information -->
        <div class="settings-section">
            <h2 class="section-title"><?= t('profile.profile_info') ?></h2>
            <div id="profile-info">
                <?php partial('profile/partials/profile_info', ['user' => $user]); ?>
            </div>
        </div>

        <!-- Password Security -->
        <div class="settings-section">
            <h2 class="section-title"><?= t('profile.password_security') ?></h2>
            <div id="profile-password">
                <?php partial('profile/partials/profile_password'); ?>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="settings-section">
            <h3 class="section-subtitle"><?= t('profile.account_actions') ?></h3>
            <div class="footer-actions">
                <a href="<?= url('logout') ?>" class="btn red"><?= t('profile.sign_out') ?></a>
            </div>
        </div>

    </div>
</div>