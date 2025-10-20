<?php
require_once __DIR__ . '/../../middleware/auth.php';
include __DIR__ . '/../layouts/header.php';
//include __DIR__ . '/../../controllers/DistribusiController.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<!-- ✅ Konten utama diberi margin kiri agar tidak tertutup sidebar -->
<div class="ml-64 p-6 min-h-screen bg-gray-100">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold flex items-center gap-2">
                📦 Barang Keluar
            </h2>
            <button onclick="toggleModal()" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Tambah Barang Keluar
            </button>
        </div>

        <!-- Form Search -->
        <form method="GET" action="" class="mb-4 flex items-center gap-2">
            <input type="text" name="search" 
                   placeholder="Cari barang atau tanggal..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                   class="border p-2 rounded w-80">
            <button type="submit" 
                    class="bg-gray-700 text-white px-3 py-2 rounded">
                Search
            </button>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border p-2">#</th>
                        <th class="border p-2">Tanggal</th>
                        <th class="border p-2">Petugas</th>
                        <th class="border p-2">Mitra</th>
                        <th class="border p-2">Nama Barang</th>
                        <th class="border p-2">Kategori</th>
                        <th class="border p-2">Jumlah</th>
                        <th class="border p-2">Stok Terkini</th>
                        <th class="border p-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)): $no=1; foreach ($rows as $r): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border p-2"><?= $no++ ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['tanggal']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_barang']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_kategori']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['jumlah']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['stok']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_petugas']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['keterangan']) ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" class="text-center p-4 text-gray-500">
                                Tidak ada data ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Barang -->
<div id="modalTambah" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-lg w-96">
        <h3 class="text-xl font-semibold mb-4">Tambah Barang Masuk</h3>
        <form method="POST" action="../../controllers/DistribusiController.php">
            <div class="mb-3">
                <label class="block text-sm font-medium">Tanggal</label>
                <input type="date" name="tanggal" class="border p-2 rounded w-full" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Barang</label>
                <select name="id_item" class="border p-2 rounded w-full" required>
                    <option value="">Pilih Barang</option>
                    <?php foreach ($items as $i): ?>
                        <option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['nama_barang']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Petugas</label>
                <select name="id_petugas" class="border p-2 rounded w-full" required>
                    <option value="">Pilih Petugas</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Jumlah</label>
                <input type="number" name="jumlah" class="border p-2 rounded w-full" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Mitra</label>
                <textarea name="keterangan" class="border p-2 rounded w-full"></textarea>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium">Keterangan</label>
                <textarea name="keterangan" class="border p-2 rounded w-full"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</button>
                <button type="submit" name="tambah" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal() {
    document.getElementById('modalTambah').classList.toggle('hidden');
}
</script>
