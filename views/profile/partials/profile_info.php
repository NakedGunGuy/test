<form
    hx-post="/profile/update"
    hx-target="#profile-info"
    hx-swap="outerHTML"
    data-toast="Profile updated successfully!"
    class="profile-form"
>
    <div class="form-grid">
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
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
            <label for="email" class="form-label">Email Address</label>
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
            Update Profile
        </button>
    </div>
</form>
