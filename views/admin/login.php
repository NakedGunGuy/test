<?php start_section('title'); ?>Admin Login - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>

<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Header Section -->
        <div class="auth-header">
            <div class="auth-icon admin-auth-icon">⚡</div>
            <h1 class="auth-title"><?= t('auth.admin_portal') ?></h1>
            <p class="auth-description">Secure access to administrative functions</p>
        </div>

        <!-- Admin Login Form -->
        <div class="section auth-form admin-auth-form">
            
            <?php if (session_get('error')): ?>
                <div class="alert error" style="margin-bottom: 1.5rem;">
                    ⚠️ <?= htmlspecialchars(session_get('error')) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" class="form-container">
                <div class="form-group">
                    <label class="form-label" for="username">
                        <span class="form-label-with-icon">
                            <span>👤</span><?= t('auth.admin_username') ?>
                        </span>
                    </label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        class="form-input"
                        placeholder="<?= t('placeholder.enter_admin_username') ?>"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <span class="form-label-with-icon">
                            <span>🔐</span><?= t('auth.admin_password') ?>
                        </span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-input"
                        placeholder="<?= t('placeholder.enter_secure_password') ?>"
                        required>
                </div>
                <button type="submit" class="btn blue btn-full admin-auth-button">
                    <?= t('auth.access_admin_portal') ?>
                </button>
            </form>
            
            <div class="auth-footer admin-auth-footer">
                <a href="<?= url('') ?>" class="return-link">← Return to main site</a>
            </div>
        </div>
    </div>
</div>