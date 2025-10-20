<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';

// Menentukan apakah ini form edit atau tambah
$isEdit = isset($barang) && $barang;
$formAction = $isEdit ? (BASE_PATH . '/barang/edit?id=' . $barang['id']) : (BASE_PATH . '/barang/tambah');
$pageTitle = $isEdit ? 'Edit Barang' : 'Tambah Barang Baru';
?>

<div class="ml-64 p-6 min-h-screen bg-gray-100">
    <div class="bg-white shadow-lg rounded-2xl p-6 max-w-2xl mx-auto">
        <h2 class="text-2xl font-semibold mb-4"><?= $pageTitle ?></h2>

        <?php if (isset($_SESSION['form_errors'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul>
                    <?php foreach ($_SESSION['form_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['form_errors']); ?>
        <?php endif; ?>

        <form action="<?= $formAction ?>" method="POST">
            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium">Nama Barang</label>
                <input type="text" id="nama" name="nama" 
                       value="<?= htmlspecialchars($barang['nama'] ?? '') ?>" 
                       class="mt-1 border p-2 rounded w-full" required>
            </div>
            <div class="mb-4">
                <label for="kategori" class="block text-sm font-medium">Kategori (Jenis)</label>
                <input type="text" id="kategori" name="kategori" 
                       value="<?= htmlspecialchars($barang['kategori'] ?? '') ?>" 
                       class="mt-1 border p-2 rounded w-full" required>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="stok" class="block text-sm font-medium">Jumlah (Stok)</label>
                    <input type="number" id="stok" name="stok" 
                           value="<?= htmlspecialchars($barang['stok'] ?? 0) ?>" 
                           class="mt-1 border p-2 rounded w-full" required>
                </div>
                <div>
                    <label for="satuan" class="block text-sm font-medium">Satuan (cth: pcs, unit)</label>
                    <input type="text" id="satuan" name="satuan" 
                           value="<?= htmlspecialchars($barang['satuan'] ?? '') ?>" 
                           class="mt-1 border p-2 rounded w-full" required>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <a href="<?= BASE_PATH ?>/barang" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>