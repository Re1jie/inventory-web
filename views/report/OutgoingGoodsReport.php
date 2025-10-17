<?php
// Catatan: File ini berasumsi variabel $histori_keluar_rows dan $search_query telah didefinisikan 
// oleh LaporanKeluarController.php sebelum include ini dijalankan.

include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 ml-64">
    <div class="bg-white shadow-xl rounded-xl p-6">
        <!-- Notifikasi (Optional: Jika Anda menggunakan session message dari Controller) -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= $_SESSION['message']; unset($_SESSION['message']); ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-gray-800">📊 Histori Barang Keluar</h2>
            
            <!-- Tombol Download (Aksi yang mungkin diarahkan ke Controller) -->
            <button onclick="alert('Fungsi Unduh Laporan Barang Keluar')"
                class="p-2 bg-gray-100 rounded-full shadow-sm text-gray-600 hover:bg-gray-200 transition"
                title="Unduh Laporan Keluar">
                <span class="material-icons text-xl">cloud_download</span>
            </button>
        </div>

        <!-- 🔍 Form Search (Menggunakan BASE_PATH untuk route yang benar) -->
        <!-- Perhatikan: Asumsikan Anda akan membuat route baru: /laporan-barang-keluar -->
        <form method="GET" action="<?= BASE_PATH ?>/laporan-barang-keluar"> 
            <!-- Tidak perlu hidden input 'page' karena kita menggunakan URL Path -->
            <div class="flex mb-4 max-w-lg">
                <input type="text"
                    name="search"
                    placeholder="Cari barang, ID, atau tanggal..."
                    value="<?= htmlspecialchars($search_query ?? '') ?>"
                    class="flex-grow px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700 transition">
                    Search
                </button>
            </div>
        </form>

        <!-- 📋 Tabel Data Histori -->
        <div class="overflow-x-auto mt-4">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-gray-200">
                    <tr class="text-left uppercase text-sm font-medium text-gray-600">
                        <th class="px-4 py-3 border-b-2 border-gray-300">No</th>
                        <th class="px-4 py-3 border-b-2 border-gray-300">Tanggal</th>
                        <th class="px-4 py-3 border-b-2 border-gray-300">ID Keluar</th>
                        <th class="px-4 py-3 border-b-2 border-gray-300">Nama Barang</th>
                        <th class="px-4 py-3 border-b-2 border-gray-300">Jumlah Keluar</th>
                        <th class="px-4 py-3 border-b-2 border-gray-300">Diterima Oleh</th> <!-- Kolom berubah -->
                        <th class="px-4 py-3 border-b-2 border-gray-300">Keterangan</th>
                        <th class="px-4 py-3 border-b-2 border-gray-300 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $histori_keluar_rows = $histori_keluar_rows ?? []; // Fallback jika variabel belum terdefinisi ?>
                <?php if (!empty($histori_keluar_rows)): ?>
                    <?php $no = 1; foreach ($histori_keluar_rows as $r): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm"><?= $no++ ?></td>
                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($r['tanggal'] ?? '-') ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-blue-600"><?= htmlspecialchars($r['id_keluar'] ?? '-') ?></td> <!-- Kolom berubah -->
                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($r['nama_barang'] ?? '-') ?></td>
                            <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($r['jumlah'] ?? '-') ?></td>
                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($r['penerima'] ?? '-') ?></td> <!-- Kolom berubah -->
                            <td class="px-4 py-3 text-sm text-gray-500"><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
                            
                            <td class="px-4 py-3 text-center flex space-x-2 justify-center">
                                <!-- PERBAIKAN: Arahkan ke /laporan-barang-keluar dengan action=edit -->
                                <a href="<?= BASE_PATH ?>/laporan-barang-keluar?action=edit&id=<?= urlencode($r['id'] ?? '') ?>" title="Edit"
                                    class="bg-yellow-100 text-yellow-600 p-2 rounded-lg hover:bg-yellow-200 transition duration-150">
                                    <span class="material-icons text-base">edit</span>
                                </a>
                                <!-- PERBAIKAN: Arahkan ke /laporan-barang-keluar dengan action=hapus -->
                                <button onclick="if(confirm('Yakin hapus transaksi <?= htmlspecialchars($r['id_keluar'] ?? '') ?>?')) { window.location.href='<?= BASE_PATH ?>/laporan-barang-keluar?action=hapus&id=<?= urlencode($r['id'] ?? '') ?>' }" 
                                            title="Hapus"
                                            class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200 transition duration-150">
                                    <span class="material-icons text-base">delete</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500 text-lg">
                            <span class="font-semibold">⚠️ Tidak ada data Histori Barang Keluar ditemukan.</span>
                            <?php if (!empty($search_query)): ?>
                                <p class="text-sm mt-1">Tidak ada hasil untuk "<?= htmlspecialchars($search_query) ?>".</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
