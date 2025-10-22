<?php
$page_type = 'keluar';
include __DIR__ . '/../../controllers/DistributionController.php';
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$search = $_GET['search'] ?? '';

// Tentukan role user untuk hak akses
$userRole = $_SESSION['user']['role'] ?? 'petugas';
$bisaManajemen = in_array($userRole, ['admin', 'superadmin']);
?>

<div class="ml-64 p-6 min-h-screen bg-gray-100">
    <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="flex items-center gap-2">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    📤 Barang Keluar
                </h2>
            </div>
    <div class="flex flex-wrap gap-2">                
            <a href="barang-keluar?action=download&type=pdf&page=keluar&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&search=<?= $search ?>" 
   class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
    <i class="fas fa-file-pdf"></i> Download PDF
</a>

<a href="barang-keluar?action=download&type=excel&page=keluar&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&search=<?= $search ?>" 
   class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
    <i class="fas fa-file-excel"></i> Download Excel
</a>


                <button onclick="toggleModal()"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                    <i class="fas fa-plus"></i> Tambah Barang Keluar
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-700 border border-green-300 rounded-lg">
                ✅ <?= $_SESSION['success_message'] ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-lg">
                ⚠️ <?= $_SESSION['error_message'] ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="GET" action="" class="mb-5 flex flex-wrap items-center gap-2">
            <input type="text" name="search"
                placeholder="🔍 Cari barang, tanggal, petugas, atau mitra..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                class="border border-gray-300 focus:ring-2 focus:ring-blue-400 p-2 rounded-lg w-80">

            <label class="text-sm font-medium text-gray-700">Dari:</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>"
                class="border border-gray-300 p-2 rounded-lg">

            <label class="text-sm font-medium text-gray-700">Sampai:</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>"
                class="border border-gray-300 p-2 rounded-lg">

            <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg transition duration-200">
                Filter
            </button>
        <a href="barang-keluar?page=keluar&reset=true" 
        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg shadow transition duration-200">
        <i class="fas fa-undo"></i> Reset
        </a>
        </form>

        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full border-collapse text-sm text-gray-700">
                <thead class="bg-gray-800 text-white text-center">
                    <tr>
                        <th class="p-3">No.</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Petugas</th>
                        <th class="p-3">Mitra</th>
                        <th class="p-3">Nama Barang</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Jumlah</th>
                        <th class="p-3">Stok Terkini</th>
                        <th class="p-3">Keterangan</th>
                        <?php if ($bisaManajemen): ?>
                            <th class="p-3">Aksi</th>
                        <?php endif; ?>
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
                            <td class="border p-2 text-center text-red-600 font-medium">
                                -<?= htmlspecialchars($r['jumlah']) ?>
                            </td>
                            <td class="border p-2 text-center font-bold">
                                <?= htmlspecialchars($r['stok_terkini']) ?>
                            </td>
                            <td class="border p-2"><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
                            
                            <?php if ($bisaManajemen): ?>
                                <td class="border p-2 text-center whitespace-nowrap">
                                    <button 
                                       type="button"
                                       onclick="openEditModal({
                                           id: '<?= $r['id_distribusi'] ?>',
                                           tanggal: '<?= htmlspecialchars($r['tanggal']) ?>',
                                           id_petugas: '<?= $r['id_petugas_asli'] ?? '' ?>', 
                                           id_item: '<?= $r['id_item_asli'] ?? '' ?>', 
                                           jumlah: '<?= htmlspecialchars($r['jumlah']) ?>',
                                           mitra: '<?= htmlspecialchars($r['mitra'] ?? $r['nama_mitra'] ?? '', ENT_QUOTES) ?>',
                                           keterangan: '<?= htmlspecialchars($r['keterangan'] ?? '', ENT_QUOTES) ?>'
                                       })"
                                       class="text-sm bg-yellow-400 text-white px-2 py-1 rounded hover:bg-yellow-500 transition duration-200">
                                       <i class="fas fa-pen"></i>
                                    </button>
                                    
                                    <a href="barang-keluar?action=delete&id=<?= $r['id_distribusi'] ?>&page=keluar" 
                                       onclick="return confirm('Anda yakin ingin menghapus data ini? Stok barang akan dikembalikan.')" 
                                       class="text-sm bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition duration-200">
                                       <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                        <tr class="bg-gray-100 font-semibold">
                             <td colspan="<?= $bisaManajemen ? '7' : '6' ?>" class="border p-2 text-right">Total Barang Keluar:</td>
                            <td class="border p-2 text-center text-red-600">-<?= $total ?></td>
                             <td colspan="<?= $bisaManajemen ? '3' : '2' ?>" class="border p-2"></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $bisaManajemen ? '10' : '9' ?>" class="text-center p-6 text-gray-500 italic">
                                Tidak ada data ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalTambah" class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-96 animate-fadeIn">
        <h3 class="text-xl font-bold mb-4 text-gray-800 text-center">Tambah Barang Keluar</h3>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" class="border border-gray-300 p-2 rounded-lg w-full" 
                       value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Petugas</label>
                <select name="id_petugas" class="border border-gray-300 p-2 rounded-lg w-full" required>
                    <option value="">Pilih Petugas</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Nama Mitra</label>
                <input type="text" name="nama_pelanggan" class="border border-gray-300 p-2 rounded-lg w-full"
                       placeholder="Contoh: PT. Maju Mundur">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Barang</label>
                <select name="id_item" class="border border-gray-300 p-2 rounded-lg w-full" required>
                    <option value="">Pilih Barang</option>
                    <?php foreach ($items as $i): ?>
                        <option value="<?= $i['id'] ?>">
                            <?= htmlspecialchars($i['nama_barang']) ?> (Stok: <?= $i['stok'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                <input type="number" name="jumlah" class="border border-gray-300 p-2 rounded-lg w-full"
                       required min="1" placeholder="Jumlah barang keluar...">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                <textarea name="keterangan" class="border border-gray-300 p-2 rounded-lg w-full"
                          placeholder="Contoh: Penjualan ke mitra..."></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="toggleModal()" 
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg transition duration-200">
                    Batal
                </button>
                <button type="submit" name="tambah_keluar" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>


<div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-96 animate-fadeIn">
        <h3 class="text-xl font-bold mb-4 text-gray-800 text-center">Edit Data Barang Keluar</h3>
        <form method="POST" action="">
            <input type="hidden" name="id_distribusi" id="edit_id_distribusi">
            <input type="hidden" name="page_type" value="<?= $page_type ?>">

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" id="edit_tanggal" class="border border-gray-300 p-2 rounded-lg w-full" required>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Petugas</label>
                <select name="id_petugas" id="edit_id_petugas" class="border border-gray-300 p-2 rounded-lg w-full" required>
                    <option value="">Pilih Petugas</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Nama Mitra</label>
                <input type="text" name="nama_pelanggan" id="edit_mitra" class="border border-gray-300 p-2 rounded-lg w-full"
                       placeholder="Contoh: PT. Maju Mundur">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Barang</label>
                <select name="id_item" id="edit_id_item" class="border border-gray-300 p-2 rounded-lg w-full" required>
                    <option value="">Pilih Barang</option>
                    <?php foreach ($items as $i): ?>
                        <option value="<?= $i['id'] ?>">
                            <?= htmlspecialchars($i['nama_barang']) ?> (Stok: <?= $i['stok'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                <input type="number" name="jumlah" id="edit_jumlah" class="border border-gray-300 p-2 rounded-lg w-full"
                       required min="1" placeholder="Jumlah barang keluar...">
            </div>

            <div class_ = "mb-3">
                <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                <textarea name="keterangan" id="edit_keterangan" class="border border-gray-300 p-2 rounded-lg w-full"
                          placeholder="Contoh: Penjualan ke mitra..."></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="toggleEditModal()" 
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg transition duration-200">
                    Batal
                </button>
                <button type="submit" name="edit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>


<script>
// Fungsi untuk modal 'Tambah' (sudah ada)
function toggleModal() {
    document.getElementById('modalTambah').classList.toggle('hidden');
}

// Fungsi untuk modal 'Edit' (BARU)
function toggleEditModal() {
    document.getElementById('modalEdit').classList.toggle('hidden');
}

// Fungsi untuk membuka modal 'Edit' dan mengisi datanya (BARU)
function openEditModal(data) {
    // Mengisi form di dalam modalEdit
    document.getElementById('edit_id_distribusi').value = data.id;
    document.getElementById('edit_tanggal').value = data.tanggal;
    document.getElementById('edit_id_petugas').value = data.id_petugas;
    document.getElementById('edit_id_item').value = data.id_item;
    document.getElementById('edit_jumlah').value = data.jumlah;
    document.getElementById('edit_mitra').value = data.mitra;
    document.getElementById('edit_keterangan').value = data.keterangan;
    
    // Tampilkan modal edit
    toggleEditModal();
}
</script>

<style>
@keyframes fadeIn {
  from {opacity: 0; transform: scale(0.95);}
  to {opacity: 1; transform: scale(1);}
}
.animate-fadeIn {
  animation: fadeIn 0.25s ease-out;
}
</style>