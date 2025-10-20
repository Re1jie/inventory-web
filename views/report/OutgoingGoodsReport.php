<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="ml-64 p-6 min-h-screen bg-gray-100">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold flex items-center gap-2">📦 Laporan Barang Keluar</h2>
            <a href="?action=export&nama_pelanggan=<?= urlencode($_GET['nama_pelanggan'] ?? '') ?>&tanggal_mulai=<?= $_GET['tanggal_mulai'] ?? '' ?>&tanggal_selesai=<?= $_GET['tanggal_selesai'] ?? '' ?>&tipe=<?= $_GET['tipe'] ?? '' ?>"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                ⬇️ Download PDF
            </a>
        </div>

        <!-- Filter -->
        <form method="GET" action="" class="flex flex-wrap gap-2 mb-4">
            <input type="text" name="nama_pelanggan" placeholder="Cari nama pelanggan..." 
                value="<?= htmlspecialchars($_GET['nama_pelanggan'] ?? '') ?>" 
                class="border p-2 rounded w-64">

            <input type="date" name="tanggal_mulai" 
                value="<?= htmlspecialchars($_GET['tanggal_mulai'] ?? '') ?>" 
                class="border p-2 rounded">

            <input type="date" name="tanggal_selesai" 
                value="<?= htmlspecialchars($_GET['tanggal_selesai'] ?? '') ?>" 
                class="border p-2 rounded">

            <select name="tipe" class="border p-2 rounded">
                <option>Semua Tipe</option>
                <option value="Retail" <?= (($_GET['tipe'] ?? '') === 'Retail') ? 'selected' : '' ?>>Retail</option>
                <option value="Grosir" <?= (($_GET['tipe'] ?? '') === 'Grosir') ? 'selected' : '' ?>>Grosir</option>
            </select>

            <button type="submit" class="bg-gray-700 text-white px-3 py-2 rounded">🔍 Filter</button>
            <a href="?" class="bg-gray-400 text-white px-3 py-2 rounded">🔄 Reset</a>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border p-2">#</th>
                        <th class="border p-2">Tanggal Keluar</th>
                        <th class="border p-2">Nama Pelanggan</th>
                        <th class="border p-2">Tipe</th>
                        <th class="border p-2">ID Item</th>
                        <th class="border p-2">ID Petugas</th>
                        <th class="border p-2">Jumlah</th>
                        <th class="border p-2">Tujuan</th>
                        <th class="border p-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)): $no=1; foreach ($rows as $r): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border p-2"><?= $no++ ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['tanggal_keluar']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['tipe']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['id_item']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['id_petugas']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['jumlah']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['tujuan']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['keterangan']) ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="9" class="text-center p-4 text-gray-500">Tidak ada data ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
