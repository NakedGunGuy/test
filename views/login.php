<?php start_section('title'); ?>Login - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>
<?php start_section('page_title'); ?><?= t('nav.login') ?><?php end_section('page_title'); ?>

<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Header Section -->
        <div class="auth-header">
            <div class="auth-icon">🔐</div>
            <h1 class="auth-title"><?= t('auth.welcome_back') ?></h1>
            <p class="auth-description"><?= t('auth.sign_in_description') ?></p>
        </div>

        <!-- Login Form -->
        <div class="section auth-form">
            <form
                hx-post="<?= url('login') ?>"
                hx-target="body"
                hx-swap="none"
                data-toast="<?= t('toast.login_successful') ?>"
                class="form-container"
            >
                <div class="form-group">
                    <label class="form-label" for="username">
                        <span class="form-label-with-icon">
                            <span>👤</span><?= t('form.username') ?>
                        </span>
                    </label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        class="form-input"
                        placeholder="<?= t('placeholder.enter_username') ?>"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <span class="form-label-with-icon">
                            <span>🔑</span><?= t('form.password') ?>
                        </span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-input"
                        placeholder="<?= t('placeholder.enter_password') ?>"
                        required>
                </div>

                <div class="remember-forgot-row">
                    <label class="remember-label">
                        <input type="checkbox" style="width: 16px; height: 16px;"> Remember me
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn blue btn-full auth-button">
                    🚀 Sign In
                </button>
            </form>
            
            <div class="auth-footer">
                <p class="auth-footer-text">Don't have an account? 
                    <a href="<?= url('register') ?>" class="auth-footer-link">Sign up here</a>
                </p>
            </div>
        </div>

        <canvas id="c" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.1;"></canvas>
    </div>
</div>
