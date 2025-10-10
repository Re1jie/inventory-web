<?php
require_once __DIR__ . '/../../config/config.php'; 
?>

<div class="w-64 min-h-screen bg-gray-800 text-white fixed">
  <div class="p-6 text-2xl font-semibold border-b border-gray-700">
    My Inventory
  </div>
  <nav class="flex flex-col mt-4 space-y-1">
    <a href="<?= BASE_PATH ?>/dashboard" class="px-6 py-3 hover:bg-gray-700">Dashboard</a>
    <a href="<?= BASE_PATH ?>/views/distribution/barang_keluar.php" class="px-6 py-3 hover:bg-gray-700">Barang Keluar</a>
    <a href="<?= BASE_PATH ?>/views/distribution/barang_masuk.php" class="px-6 py-3 hover:bg-gray-700">Barang Masuk</a>
    <a href="<?= BASE_PATH ?>/logout" class="px-6 py-3 hover:bg-gray-700">Logout</a>
  </nav>
</div>
