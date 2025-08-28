<form
    hx-post="/profile/update"
    hx-target="#profile-info"
    hx-swap="outerHTML"
    data-toast="Profile updated successfully!"
    class="space-y-4"
>
    <div>
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="border px-2 py-1 w-full" />
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="border px-2 py-1 w-full" />
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
</form>
