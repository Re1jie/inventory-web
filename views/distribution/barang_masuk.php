<?php
$page_type = 'masuk';
include __DIR__ . '/../../controllers/DistributionController.php';
require_once __DIR__ . '/../../middleware/auth.php';
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="ml-64 p-6 min-h-screen bg-gray-100">
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold flex items-center gap-2">
                📦 Barang Masuk
            </h2>
            <div class="flex gap-2">
                <a href="?action=export<?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date=' . $_GET['start_date'] : '' ?><?= isset($_GET['end_date']) ? '&end_date=' . $_GET['end_date'] : '' ?>" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                   ⬇️ Download PDF
                </a>
                <button onclick="toggleModal()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Tambah Barang Masuk
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-700 border border-green-300 rounded">
                <?= $_SESSION['success_message'] ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded">
                <?= $_SESSION['error_message'] ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Filter & Search -->
        <form method="GET" action="" class="mb-4 flex flex-wrap items-center gap-3">
            <input type="text" name="search" 
                   placeholder="Cari petugas / mitra / barang..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                   class="border p-2 rounded w-60">

            <input type="date" name="start_date" 
                   value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" 
                   class="border p-2 rounded"
                   placeholder="Tanggal Mulai">

            <input type="date" name="end_date" 
                   value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" 
                   class="border p-2 rounded"
                   placeholder="Tanggal Akhir">

            <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">
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
                            <td class="border p-2 text-center"><?= htmlspecialchars(date('d-m-Y', strtotime($r['tanggal']))) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['petugas'] ?? $r['nama_petugas'] ?? '-') ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['mitra'] ?? $r['nama_mitra'] ?? '-') ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['nama_barang']) ?></td>
                            <td class="border p-2"><?= htmlspecialchars($r['kategori'] ?? '-') ?></td>
                            <td class="border p-2 text-center text-green-600 font-medium">
                                +<?= htmlspecialchars($r['jumlah']) ?>
                            </td>
                            <td class="border p-2 text-center font-bold">
                                <?= htmlspecialchars($r['stok_terkini']) ?>
                            </td>
                            <td class="border p-2"><?= htmlspecialchars($r['keterangan'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                        <!-- Total Row -->
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="6" class="border p-2 text-right">Total Barang Masuk:</td>
                            <td class="border p-2 text-center text-green-600">+<?= $total ?></td>
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

<!-- Modal Tambah Barang Masuk -->
<div id="modalTambah" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-lg w-96">
        <h3 class="text-xl font-semibold mb-4">Tambah Barang Masuk</h3>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="block text-sm font-medium">Tanggal</label>
                <input type="date" name="tanggal" class="border p-2 rounded w-full"
                       value="<?= date('Y-m-d') ?>" required>
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
                <label class="block text-sm font-medium">Nama Mitra / Supplier</label>
                <input type="text" name="nama_pelanggan" class="border p-2 rounded w-full"
                       placeholder="Contoh: CV. Sumber Jaya">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Barang</label>
                <select name="id_item" class="border p-2 rounded w-full" required>
                    <option value="">Pilih Barang</option>
                    <?php foreach ($items as $i): ?>
                        <option value="<?= $i['id'] ?>">
                            <?= htmlspecialchars($i['nama_barang']) ?> (Stok: <?= $i['stok'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Jumlah</label>
                <input type="number" name="jumlah" class="border p-2 rounded w-full"
                       required min="1" placeholder="Jumlah barang masuk...">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Keterangan</label>
                <textarea name="keterangan" class="border p-2 rounded w-full"
                          placeholder="Contoh: Pembelian stok baru, retur barang..."></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleModal()" 
                        class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</button>
                <button type="submit" name="tambah" 
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal() {
    document.getElementById('modalTambah').classList.toggle('hidden');
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>