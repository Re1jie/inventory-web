<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<main class="ml-64 p-6">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Inventaris</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-lg font-semibold text-gray-500">Total Stok Barang</h3>
                <p class="text-4xl font-extrabold text-blue-600 mt-2">
                    <?= htmlspecialchars($summaryData['total_stok']) ?>
                </p>
                <p class="text-sm text-gray-400 mt-1">Unit di Gudang</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-lg font-semibold text-gray-500">Jenis Barang</h3>
                <p class="text-4xl font-extrabold text-indigo-600 mt-2">
                    <?= htmlspecialchars($summaryData['jumlah_item']) ?>
                </p>
                <p class="text-sm text-gray-400 mt-1">Varian Produk</p>
            </div>

            <div id="card-masuk" class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300 cursor-pointer">
                <h3 class="text-lg font-semibold text-gray-500">Barang Masuk</h3>
                <p class="text-4xl font-extrabold text-green-600 mt-2">
                    <?= htmlspecialchars($summaryData['total_masuk']) ?>
                </p>
                <p class="text-sm text-gray-400 mt-1">Unit Diterima</p>
            </div>

            <div id="card-keluar" class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300 cursor-pointer">
                <h3 class="text-lg font-semibold text-gray-500">Barang Keluar</h3>
                <p class="text-4xl font-extrabold text-red-600 mt-2">
                    <?= htmlspecialchars($summaryData['total_keluar']) ?>
                </p>
                <p class="text-sm text-gray-400 mt-1">Unit Terdistribusi</p>
            </div>
        </div>
        
        <div class="mt-8">
            <div id="detail-masuk" class="hidden bg-white p-6 rounded-2xl shadow-lg mb-6 transition-all duration-500">
                <h4 class="text-xl font-semibold text-green-700 mb-4">5 Barang Masuk Terakhir</h4>
                <?php if (!empty($recentMasuk)): ?>
                    <ul class="list-disc pl-5 text-gray-600 space-y-2">
                        <?php foreach ($recentMasuk as $item): ?>
                            <li>
                                <span class="font-semibold"><?= htmlspecialchars($item['tanggal']) ?></span> - 
                                <strong><?= htmlspecialchars($item['nama_barang']) ?></strong> 
                                (+<?= htmlspecialchars($item['jumlah']) ?> unit)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">Belum ada data barang masuk.</p>
                <?php endif; ?>
            </div>

            <div id="detail-keluar" class="hidden bg-white p-6 rounded-2xl shadow-lg transition-all duration-500">
                <h4 class="text-xl font-semibold text-red-700 mb-4">5 Barang Keluar Terakhir</h4>
                <?php if (!empty($recentKeluar)): ?>
                    <ul class="list-disc pl-5 text-gray-600 space-y-2">
                        <?php foreach ($recentKeluar as $item): ?>
                            <li>
                                <span class="font-semibold"><?= htmlspecialchars($item['tanggal']) ?></span> - 
                                <strong><?= htmlspecialchars($item['nama_barang']) ?></strong> 
                                (-<?= htmlspecialchars($item['jumlah']) ?> unit)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">Belum ada data barang keluar.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
    const cardMasuk = document.getElementById('card-masuk');
    const detailMasuk = document.getElementById('detail-masuk');
    
    const cardKeluar = document.getElementById('card-keluar');
    const detailKeluar = document.getElementById('detail-keluar');

    cardMasuk.addEventListener('click', () => {
        // Toggle (memunculkan/menyembunyikan) detail barang masuk
        detailMasuk.classList.toggle('hidden');
        // Sembunyikan detail barang keluar jika sedang terbuka
        detailKeluar.classList.add('hidden');
    });

    cardKeluar.addEventListener('click', () => {
        // Toggle detail barang keluar
        detailKeluar.classList.toggle('hidden');
        // Sembunyikan detail barang masuk jika sedang terbuka
        detailMasuk.classList.add('hidden');
    });
</script>