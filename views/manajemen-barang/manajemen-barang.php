<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<main class="ml-64 p-6 bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Daftar Barang</h2>
            <a href="<?= BASE_PATH ?>/barang/tambah" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Barang</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($barangs as $b): ?>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <img src="<?= $b['gambar'] ?? 'https://via.placeholder.com/150' ?>" class="w-full h-40 object-cover rounded">
                    <h3 class="mt-3 font-semibold text-lg"><?= htmlspecialchars($b['nama']) ?></h3>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($b['kategori']) ?></p>
                    <p class="text-sm text-gray-800 mt-1">Stok: <?= htmlspecialchars($b['stok']) . ' ' . htmlspecialchars($b['satuan']) ?></p>
                    <div class="mt-4 flex gap-2">
                        <a href="<?= BASE_PATH ?>/barang/edit?id=<?= $b['id'] ?>" class="bg-yellow-400 px-3 py-1 rounded text-white text-sm">Edit</a>
                        <a href="<?= BASE_PATH ?>/barang/hapus?id=<?= $b['id'] ?>" onclick="return confirm('Hapus barang ini?')" class="bg-red-500 px-3 py-1 rounded text-white text-sm">Hapus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div> </main>