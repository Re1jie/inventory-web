<?php
include __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-semibold text-center mb-6">Login Inventory</h2>
    <form action="<?php echo BASE_PATH; ?>/login-post" method="post" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">
            Username
        </label>
        <input type="text" name="username" class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500" required>
      </div>
      <div>
        <label class="block text-sm font-medium">
            Password
        </label>
        <input type="password" name="password" class="mt-1 w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500" required>
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
        Login
      </button>
    </form>
    <div class="text-center mt-4">
        <a href="<?php echo BASE_PATH; ?>/register" class="text-sm text-blue-600 hover:underline">Belum punya akun? Register di sini</a>
    </div>
  </div>
</div>

<?php
include __DIR__ . '/../layouts/footer.php'; ?>1
