<?php start_section('title'); ?>Reset Password - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>
<?php start_section('page_title'); ?>Reset Password<?php end_section('page_title'); ?>

<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Header Section -->
        <div class="auth-header">
            <div class="auth-icon">🔐</div>
            <h1 class="auth-title"><?= t('auth.reset_password_title') ?></h1>
            <p class="auth-description"><?= t('auth.reset_password_description') ?></p>
        </div>

        <!-- Reset Password Form -->
        <div class="section auth-form">
            <form
                hx-post="<?= url('reset-password') ?>"
                hx-target="body"
                hx-swap="none"
                data-toast="Password reset successfully"
                class="form-container"
            >
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                <div class="form-group">
                    <label class="form-label" for="password">
                        <span class="form-label-with-icon">
                            <span>🔑</span><?= t('auth.new_password') ?>
                        </span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-input"
                        placeholder="<?= t('placeholder.create_password') ?>"
                        minlength="6"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">
                        <span class="form-label-with-icon">
                            <span>🔑</span><?= t('auth.confirm_password') ?>
                        </span>
                    </label>
                    <input
                        type="password"
                        name="confirm_password"
                        id="confirm_password"
                        class="form-input"
                        placeholder="<?= t('placeholder.confirm_password') ?>"
                        minlength="6"
                        required>
                </div>

                <button type="submit" class="btn blue btn-full auth-button">
                    <?= t('auth.reset_password_button') ?>
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
