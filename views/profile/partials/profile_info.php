<form
    hx-post="<?= url('profile/update') ?>"
    hx-target="#profile-info"
    hx-swap="outerHTML"
    data-toast="<?= t('toast.profile_updated') ?>"
    class="profile-form"
>
    <div class="form-grid">
        <div class="form-group">
            <label for="username" class="form-label"><?= t('form.username') ?></label>
            <input 
                type="text" 
                id="username"
                name="username" 
                value="<?= is_array($user) ? htmlspecialchars($user['username']) : '' ?>" 
                class="form-input" 
                required
            />
        </div>
        <div class="form-group">
            <label for="email" class="form-label"><?= t('form.email_address') ?></label>
            <input 
                type="email" 
                id="email"
                name="email" 
                value="<?= is_array($user) ? htmlspecialchars($user['email'] ?? '') : '' ?>" 
                class="form-input" 
                required
            />
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn blue">
            <?= t('button.update_profile') ?>
        </button>
    </div>
</form>
