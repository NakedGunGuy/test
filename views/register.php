<?php start_section('title'); ?>Register - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>
<?php start_section('page_title'); ?><?= t('nav.signup') ?><?php end_section('page_title'); ?>

<div class="auth-container">
    <div class="auth-wrapper">
        <div class="auth-header">
            <div class="auth-icon">
                👤
            </div>
            <h1 class="auth-title"><?= t('auth.create_account') ?></h1>
            <p class="auth-description"><?= t('auth.join_tcg_community') ?></p>
        </div>

        <div class="auth-form">
            <form
                hx-post="<?= url('register') ?>"
                hx-target="body"
                hx-swap="none"
                class="form-container"
                data-toast="<?= t('toast.success') ?>"
            >
                <div class="form-group">
                    <label class="form-label" for="email"><?= t('form.email_address') ?></label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-input"
                        required
                        placeholder="<?= t('placeholder.enter_email') ?>"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="username"><?= t('form.username') ?></label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-input"
                        required
                        placeholder="<?= t('placeholder.choose_username') ?>"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password"><?= t('form.password') ?></label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-input"
                        required
                        placeholder="<?= t('placeholder.create_password') ?>"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password"><?= t('form.confirm_password') ?></label>
                    <input
                        type="password"
                        name="confirm_password"
                        id="confirm_password"
                        class="form-input"
                        required
                        placeholder="<?= t('placeholder.confirm_password') ?>"
                    >
                </div>

                <button type="submit" class="btn blue auth-button">
                    <?= t('auth.create_account') ?>
                </button>
            </form>

            <div class="auth-footer">
                <p class="auth-footer-text">
                    <?= t('auth.already_have_account') ?>
                    <a href="<?= url('login') ?>" class="auth-footer-link"><?= t('auth.sign_in') ?></a>
                </p>
            </div>
        </div>
    </div>
</div>
