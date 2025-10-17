<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<head>
    <meta charset="UTF-8">
    <title>Manajemen Barang</title>
<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white p-6">
        <h1 class="text-2xl font-bold mb-8">INVENTORY</h1>
        <ul>
            <li class="mb-3"><a href="<?= BASE_PATH ?>/dashboard" class="hover:text-blue-400">Dashboard</a></li>
            <li class="mb-3"><a href="<?= BASE_PATH ?>/barang" class="text-blue-400 font-semibold">Data Barang</a></li>
            <li class="mb-3"><a href="<?= BASE_PATH ?>/kategori" class="hover:text-blue-400">Kategori</a></li>
        </ul>
    </aside>

    <!-- Main -->
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Daftar Barang</h2>
            <a href="<?= BASE_PATH ?>/barang/tambah" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Barang</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($barangs as $b): ?>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <img src="<?= $b['gambar'] ?: 'https://via.placeholder.com/150' ?>" class="w-full h-40 object-cover rounded">
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
    </main>
</body>
</html>
