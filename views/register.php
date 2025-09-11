<?php start_section('title'); ?>Register - <?= htmlspecialchars($_ENV['APP_NAME']) ?><?php end_section('title'); ?>
<?php start_section('page_title'); ?>Register<?php end_section('page_title'); ?>

<div class="max-w-md mx-auto mt-20 p-6 border rounded shadow">
    <h1 class="text-xl font-bold mb-4">Register</h1>

    <form
        hx-post="/register"
        hx-target="body"
        hx-swap="none"
        class="space-y-4"
        data-toast="Success!"
    >
        <div>
            <label class="block mb-1" for="email">Username</label>
            <input type="email" name="email" id="email" class="border rounded px-2 py-1 w-full" required>
        </div>

        <div>
            <label class="block mb-1" for="username">Username</label>
            <input type="text" name="username" id="username" class="border rounded px-2 py-1 w-full" required>
        </div>

        <div>
            <label class="block mb-1" for="password">Password</label>
            <input type="password" name="password" id="password" class="border rounded px-2 py-1 w-full" required>
        </div>

        <div>
            <label class="block mb-1" for="confirm_password">Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="border rounded px-2 py-1 w-full" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Login
        </button>
    </form>
</div>
