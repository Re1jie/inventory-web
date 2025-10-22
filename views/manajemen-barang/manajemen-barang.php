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
                <?php
                    // [DIRUBAH] Tentukan path gambar
                    $imageUrl = !empty($b['gambar']) 
                                ? BASE_PATH . $b['gambar'] 
                                : BASE_PATH . '/assets/images/kardusataulogo.png'; // Fallback ke logo kardus
                ?>
                <div class="bg-white shadow-md rounded-lg p-4 flex flex-col">
                    <div class="w-full h-40 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                         <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($b['nama']) ?>" class="w-full h-full object-cover">
                    </div>

                    <div class="mt-3 flex-grow">
                        <h3 class="font-semibold text-lg"><?= htmlspecialchars($b['nama']) ?></h3>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($b['kategori']) ?></p>
                        <p class="text-sm text-gray-800 mt-1">Stok: <?= htmlspecialchars($b['stok']) . ' ' . htmlspecialchars($b['satuan']) ?></p>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <a href="<?= BASE_PATH ?>/barang/edit?id=<?= $b['id'] ?>" class="bg-yellow-400 px-3 py-1 rounded text-white text-sm">Edit</a>
                        <a href="<?= BASE_PATH ?>/barang/hapus?id=<?= $b['id'] ?>" onclick="return confirm('Hapus barang ini? Ini juga akan menghapus gambarnya.')" class="bg-red-500 px-3 py-1 rounded text-white text-sm">Hapus</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div> 
</main>