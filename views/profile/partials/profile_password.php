<form
    hx-post="<?= url('profile/password') ?>"
    hx-target="#profile-password"
    hx-swap="outerHTML"
    data-toast="<?= t('toast.password_changed') ?>"
    class="password-form"
>
    <div class="form-group">
        <label for="current_password" class="form-label"><?= t('form.current_password') ?></label>
        <input 
            type="password" 
            id="current_password"
            name="current_password" 
            class="form-input" 
            required
        />
    </div>
    
    <div class="form-group">
        <label for="new_password" class="form-label"><?= t('form.new_password') ?></label>
        <input 
            type="password" 
            id="new_password"
            name="new_password" 
            class="form-input" 
            required
            minlength="6"
        />
        <p class="form-help">Password must be at least 6 characters long</p>
    </div>
    
    <div class="form-group">
        <label for="confirm_password" class="form-label"><?= t('form.confirm_new_password') ?></label>
        <input 
            type="password" 
            id="confirm_password"
            name="confirm_password" 
            class="form-input" 
            required
            minlength="6"
        />
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn red">
            <?= t('button.change_password') ?>
        </button>
    </div>
</form>
