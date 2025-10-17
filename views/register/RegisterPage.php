<?php
include __DIR__ . '/../layouts/header.php';
session_start();
?>

<div class="min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-semibold text-center mb-6">Register Akun Baru</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $_SESSION['error']; ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $_SESSION['success']; ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form action="<?= BASE_PATH; ?>/register-post" method="post" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">Username</label>
        <input type="text" name="username" class="mt-1 w-full border border-gray-300 rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" class="mt-1 w-full border border-gray-300 rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" class="mt-1 w-full border border-gray-300 rounded px-3 py-2" required>
      </div>
      <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
        Register
      </button>
    </form>
    <div class="text-center mt-4">
        <a href="<?= BASE_PATH; ?>/login" class="text-sm text-blue-600 hover:underline">Sudah punya akun? Login di sini</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>