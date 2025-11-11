<?php start_section('title'); ?>Forgot Password - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>
<?php start_section('page_title'); ?>Forgot Password<?php end_section('page_title'); ?>

<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Header Section -->
        <div class="auth-header">
            <div class="auth-icon">🔑</div>
            <h1 class="auth-title"><?= t('auth.forgot_password_title') ?></h1>
            <p class="auth-description"><?= t('auth.forgot_password_description') ?></p>
        </div>

        <!-- Forgot Password Form -->
        <div class="section auth-form">
            <?php if (session_get('success')): ?>
                <div class="alert success" style="margin-bottom: 1.5rem; word-break: break-all;">
                    <?= icon('check') ?> <?= htmlspecialchars(session_get('success')) ?>
                </div>
            <?php endif; ?>

            <form
                hx-post="<?= url('forgot-password') ?>"
                hx-target="body"
                hx-swap="none"
                data-toast="Password reset link generated"
                class="form-container"
            >
                <div class="form-group">
                    <label class="form-label" for="email">
                        <span class="form-label-with-icon">
                            <span>📧</span><?= t('form.email_address') ?>
                        </span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-input"
                        placeholder="<?= t('placeholder.enter_email') ?>"
                        required>
                </div>

                <button type="submit" class="btn blue btn-full auth-button">
                    <?= t('auth.send_reset_link') ?>
                </button>
            </form>

            <div class="auth-footer">
                <p class="auth-footer-text"><?= t('auth.remember_password') ?>
                    <a href="<?= url('login') ?>" class="auth-footer-link"><?= t('auth.sign_in') ?></a>
                </p>
            </div>
        </div>

        <canvas id="c" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.1;"></canvas>
    </div>
</div>
