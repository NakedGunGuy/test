<form
    hx-post="/profile/password"
    hx-target="#profile-password"
    hx-swap="outerHTML"
    data-toast="Password changed successfully!"
    class="space-y-4"
>
    <div>
        <label>Current Password</label>
        <input type="password" name="current_password" class="border px-2 py-1 w-full" />
    </div>
    <div>
        <label>New Password</label>
        <input type="password" name="new_password" class="border px-2 py-1 w-full" />
    </div>
    <div>
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" class="border px-2 py-1 w-full" />
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Change Password</button>
</form>
