<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="ml-64 p-6 min-h-screen bg-gray-100">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold flex items-center gap-2">
                🚚 Laporan Barang Keluar
            </h2>
            <a href="?action=export<?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date=' . $_GET['start_date'] : '' ?><?= isset($_GET['end_date']) ? '&end_date=' . $_GET['end_date'] : '' ?>" 
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
               ⬇️ Download PDF
            </a>
        </div>

        <!-- Filter -->
        <form method="GET" action="" class="mb-4 flex flex-wrap items-center gap-3">
            <input type="text" name="search" 
                   placeholder="Cari petugas / mitra / barang..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                   class="border p-2 rounded w-60">

            <input type="date" name="start_date" 
                   value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" 
                   class="border p-2 rounded">

            <input type="date" name="end_date" 
                   value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" 
                   class="border p-2 rounded">

            <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded">
                🔍 Filter
            </button>

            <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" 
               class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
               🔄 Reset
            </a>
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
                    <?php if (!empty($rows)): $no=1; $total=0; foreach ($rows as $r): $total += $r['jumlah']; ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border p-2 text-center"><?= $no++ ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['tanggal']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_petugas'] ?? '-') ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_mitra'] ?? '-') ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_barang'] ?? '-') ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['kategori'] ?? '-') ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['jumlah']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['stok_terkini'] ?? '-') ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['keterangan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="6" class="border p-2 text-right">Total Barang Keluar:</td>
                            <td class="border p-2 text-center"><?= $total ?></td>
                            <td colspan="2" class="border p-2"></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center p-4 text-gray-500">
                                Tidak ada data ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
