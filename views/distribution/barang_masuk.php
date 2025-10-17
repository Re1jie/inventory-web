<?php
session_start();
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="container mx-auto mt-10">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-gray-700">📦 Barang Masuk</h2>
            <!-- 🔹 Button Tambah -->
            <button id="btnTambah"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                + Tambah
            </button>
        </div>

        <!-- 🔍 Form Search -->
        <form method="GET" action="">
            <div class="flex mb-4">
                <input type="text"
                       name="search"
                       placeholder="Cari barang atau tanggal..."
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       class="flex-grow px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring focus:ring-blue-300">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">
                    Search
                </button>
            </div>
        </form>

        <!-- 📋 Tabel Data -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-md">
                <thead class="bg-gray-100">
                    <tr class="text-left text-gray-700">
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Tanggal</th>
                        <th class="px-4 py-2 border">ID Masuk</th>
                        <th class="px-4 py-2 border">Nama Barang</th>
                        <th class="px-4 py-2 border">Kategori</th>
                        <th class="px-4 py-2 border">Jumlah</th>
                        <th class="px-4 py-2 border">Stok Terkini</th>
                        <th class="px-4 py-2 border">Keterangan</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($rows)): ?>
                    <?php $no = 1; foreach ($rows as $r): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border"><?= $no++ ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($r['tanggal']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($r['id_masuk']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($r['nama_barang']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($r['nama_kategori']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($r['jumlah']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($r['stok']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($r['keterangan']) ?></td>
                            <td class="px-4 py-2 border text-center">
                                <a href="edit_barang_masuk.php?id=<?= urlencode($r['id_masuk']) ?>"
                                   class="bg-yellow-400 text-white px-3 py-1 rounded-md hover:bg-yellow-500 text-sm">
                                   Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-3 text-gray-500">Tidak ada data ditemukan.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 🟩 Modal Form Tambah -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-semibold mb-4">Tambah Barang Masuk</h3>
        <form action="../controllers/DistribusiController.php" method="POST">
            <input type="hidden" name="action" value="tambah">
            
            <div class="mb-3">
                <label class="block text-sm font-medium">Tanggal</label>
                <input type="date" name="tanggal" required class="w-full border px-3 py-2 rounded-md">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Nama Barang</label>
                <input type="text" name="nama_barang" required class="w-full border px-3 py-2 rounded-md">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Kategori</label>
                <input type="text" name="kategori" required class="w-full border px-3 py-2 rounded-md">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Jumlah</label>
                <input type="number" name="jumlah" required class="w-full border px-3 py-2 rounded-md">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Stok Terkini</label>
                <input type="number" name="stok" required class="w-full border px-3 py-2 rounded-md">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full border px-3 py-2 rounded-md"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" id="btnCancel" class="px-3 py-1 border rounded-md text-gray-600">Batal</button>
                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- 🧠 Script Modal -->
<script>
    const modal = document.getElementById('modalTambah');
    const btnTambah = document.getElementById('btnTambah');
    const btnCancel = document.getElementById('btnCancel');

    btnTambah.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    btnCancel.addEventListener('click', () => {
        modal.classList.add('hidden');
    });
</script>
