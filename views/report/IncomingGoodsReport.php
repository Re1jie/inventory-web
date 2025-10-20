<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="ml-64 p-6 min-h-screen bg-gray-100">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold flex items-center gap-2">
                📦 Laporan Barang Masuk
            </h2>
            <a href="?action=export<?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
               ⬇️ Download PDF
            </a>
        </div>

        <!-- Form Search -->
        <form method="GET" action="" class="mb-4 flex items-center gap-2">
            <input type="text" name="search" 
                   placeholder="Cari nama pelanggan, tipe, atau tanggal..." 
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
                        <th class="border p-2">Nama Pelanggan</th>
                        <th class="border p-2">Tipe</th>
                        <th class="border p-2">ID Item</th>
                        <th class="border p-2">ID Petugas</th>
                        <th class="border p-2">Jumlah</th>
                        <th class="border p-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)): $no=1; foreach ($rows as $r): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border p-2"><?= $no++ ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['tanggal']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['tipe']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['id_item']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['id_petugas']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['jumlah']) ?></td>
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
