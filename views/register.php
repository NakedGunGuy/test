<?php start_section('title'); ?>Register - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>
<?php start_section('page_title'); ?>Register<?php end_section('page_title'); ?>

<div class="auth-container">
    <div class="auth-wrapper">
        <div class="auth-header">
            <div class="auth-icon">
                👤
            </div>
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-description">Join our TCG community</p>
        </div>

        <div class="auth-form">
            <form
                hx-post="/register"
                hx-target="body"
                hx-swap="none"
                class="form-container"
                data-toast="Success!"
            >
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-input"
                        required
                        placeholder="Enter your email"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-input"
                        required
                        placeholder="Choose a username"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-input"
                        required
                        placeholder="Create a password"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input
                        type="password"
                        name="confirm_password"
                        id="confirm_password"
                        class="form-input"
                        required
                        placeholder="Confirm your password"
                    >
                </div>

                <button type="submit" class="btn blue auth-button">
                    Create Account
                </button>
            </form>

            <div class="auth-footer">
                <p class="auth-footer-text">
                    Already have an account?
                    <a href="/login" class="auth-footer-link">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
