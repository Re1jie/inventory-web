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
            <a href="?action=export<?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date=' . $_GET['start_date'] : '' ?><?= isset($_GET['end_date']) ? '&end_date=' . $_GET['end_date'] : '' ?><?= isset($_GET['tipe']) ? '&tipe=' . $_GET['tipe'] : '' ?>" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
               ⬇️ Download PDF
            </a>
        </div>

        <!-- Filter & Search -->
        <form method="GET" action="" class="mb-4 flex flex-wrap items-center gap-3">
            <input type="text" name="search" 
                   placeholder="Cari nama pelanggan..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                   class="border p-2 rounded w-60">

            <input type="date" name="start_date" 
                   value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" 
                   class="border p-2 rounded">

            <input type="date" name="end_date" 
                   value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" 
                   class="border p-2 rounded">

            <select name="tipe" class="border p-2 rounded">
                <option value="">Semua Tipe</option>
                <option value="Retail" <?= (($_GET['tipe'] ?? '') === 'Retail') ? 'selected' : '' ?>>Retail</option>
                <option value="Grosir" <?= (($_GET['tipe'] ?? '') === 'Grosir') ? 'selected' : '' ?>>Grosir</option>
            </select>

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
                        <th class="border p-2">Nama Pelanggan</th>
                        <th class="border p-2">Tipe</th>
                        <th class="border p-2">ID Item</th>
                        <th class="border p-2">ID Petugas</th>
                        <th class="border p-2">Jumlah</th>
                        <th class="border p-2">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)): $no=1; $total=0; foreach ($rows as $r): $total += $r['jumlah']; ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border p-2 text-center"><?= $no++ ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['tanggal']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['tipe']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['id_item']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['id_petugas']) ?></td>
                            <td class="border p-2 text-center"><?= htmlspecialchars($r['jumlah']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['keterangan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="6" class="border p-2 text-right">Total Barang Masuk:</td>
                            <td class="border p-2 text-center"><?= $total ?></td>
                            <td class="border p-2"></td>
                        </tr>
                    <?php else: ?>
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
