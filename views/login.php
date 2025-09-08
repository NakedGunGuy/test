<!-- views/login.php -->
<div class="max-w-md mx-auto mt-20 p-6 border rounded shadow">
    <h1 class="text-xl font-bold mb-4">Login</h1>

    <canvas id="c"></canvas>


    <form
            hx-post="/login"
            hx-target="body"
            hx-swap="none"
            class="space-y-4"
            data-toast="Success!"
    >
        <div>
            <label class="block mb-1" for="username">Username</label>
            <input type="text" name="username" id="username" class="border rounded px-2 py-1 w-full" required>
        </div>

        <div>
            <label class="block mb-1" for="password">Password</label>
            <input type="password" name="password" id="password" class="border rounded px-2 py-1 w-full" required>
        </div>

        <button type="submit" class="btn blue">
            Login
        </button>
    </form>
</div>
