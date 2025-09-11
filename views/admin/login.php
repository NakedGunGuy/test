<?php start_section('title'); ?>Admin Login - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>

<div class="auth-container" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(26, 26, 26, 0.8) 100%); backdrop-filter: blur(10px);">
    <div class="auth-wrapper">
        <!-- Header Section -->
        <div class="auth-header">
            <div class="auth-icon admin-auth-icon">⚡</div>
            <h1 class="auth-title">Admin Portal</h1>
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
                            <span>👤</span>Administrator Username
                        </span>
                    </label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        class="form-input"
                        placeholder="Enter admin username"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <span class="form-label-with-icon">
                            <span>🔐</span>Administrator Password
                        </span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-input"
                        placeholder="Enter secure password"
                        required>
                </div>

                <div class="admin-security-note">
                    <div class="online-dot"></div>
                    <span>Secure admin authentication required</span>
                </div>

                <button type="submit" class="btn blue btn-full admin-auth-button">
                    🚀 Access Admin Portal
                </button>
            </form>
            
            <div class="auth-footer admin-auth-footer">
                <p class="admin-footer-note">
                    <span>🔒</span>This is a secure administrative area
                </p>
                <a href="/" class="return-link">← Return to main site</a>
            </div>
        </div>
    </div>
</div>