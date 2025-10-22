<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';

// [MODIFIKASI] Ambil data user dari session untuk ditampilkan
$username = $_SESSION['user']['username'] ?? 'Pengguna';
$email = $_SESSION['user']['email'] ?? 'email@anda.com';
?>

<main class="ml-64 p-6 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                Selamat datang, <?= htmlspecialchars($username) ?>!
            </h2>
            
            <div class="flex items-center gap-3 bg-white rounded-full py-2 px-4 shadow-md border border-gray-200">
                <span class="text-3xl text-indigo-500">
                    <i class="fa fa-user-circle"></i>
                </span>
                <div>
                    <div class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($username) ?></div>
                    <div class="text-xs text-gray-500"><?= htmlspecialchars($email) ?></div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
             <div class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-lg font-semibold text-gray-500">Total Stok Barang</h3>
                <p class="text-4xl font-extrabold text-blue-600 mt-2"><?= htmlspecialchars($summaryData['total_stok']) ?></p>
                <p class="text-sm text-gray-400 mt-1">Unit di Gudang</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300">
                <h3 class="text-lg font-semibold text-gray-500">Jenis Barang</h3>
                <p class="text-4xl font-extrabold text-indigo-600 mt-2"><?= htmlspecialchars($summaryData['jumlah_item']) ?></p>
                <p class="text-sm text-gray-400 mt-1">Varian Produk</p>
            </div>
            <div id="card-masuk" class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300 cursor-pointer">
                <h3 class="text-lg font-semibold text-gray-500">Barang Masuk</h3>
                <p class="text-4xl font-extrabold text-green-600 mt-2"><?= htmlspecialchars($summaryData['total_masuk']) ?></p>
                <p class="text-sm text-gray-400 mt-1">Unit Diterima</p>
            </div>
            <div id="card-keluar" class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300 cursor-pointer">
                <h3 class="text-lg font-semibold text-gray-500">Barang Keluar</h3>
                <p class="text-4xl font-extrabold text-red-600 mt-2"><?= htmlspecialchars($summaryData['total_keluar']) ?></p>
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
                                <span class="font-semibold"><?= htmlspecialchars(date('d-m-Y', strtotime($item['tanggal']))) ?></span> - 
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
                                <span class="font-semibold"><?= htmlspecialchars(date('d-m-Y', strtotime($item['tanggal']))) ?></span> - 
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
        
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Stok per Kategori</h3>
                <canvas id="pieChart"></canvas>
            </div>
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">10 Barang Stok Terbanyak</h3>
                <canvas id="barChart"></canvas>
            </div>
            <div class="lg:col-span-3 bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-semibold text-red-600 mb-4">⚠️ Peringatan Stok Rendah (<= 10)</h3>
                <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                    <?php if (!empty($lowStockItems)): ?>
                        <?php foreach ($lowStockItems as $item): ?>
                            <div class="flex justify-between items-center bg-red-50 p-3 rounded-lg">
                                <span class="font-medium text-gray-700"><?= htmlspecialchars($item['nama_barang']) ?></span>
                                <span class="font-bold text-red-700 text-lg"><?= htmlspecialchars($item['stok']) . ' ' . htmlspecialchars($item['satuan']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="flex items-center justify-center h-full">
                            <p class="text-gray-500 text-center py-10">👍 Semua stok aman.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // --- SCRIPT UNTUK DROPDOWN ---
    const cardMasuk = document.getElementById('card-masuk');
    const detailMasuk = document.getElementById('detail-masuk');
    const cardKeluar = document.getElementById('card-keluar');
    const detailKeluar = document.getElementById('detail-keluar');

    cardMasuk.addEventListener('click', () => {
        detailMasuk.classList.toggle('hidden');
        detailKeluar.classList.add('hidden'); // Tutup detail keluar jika detail masuk dibuka
    });

    cardKeluar.addEventListener('click', () => {
        detailKeluar.classList.toggle('hidden');
        detailMasuk.classList.add('hidden'); // Tutup detail masuk jika detail keluar dibuka
    });

    // --- SCRIPT UNTUK CHARTS ---
    const pieChartData = <?= json_encode($stockByCategory) ?>;
    const barChartData = <?= json_encode($topItems) ?>;
    const trendData = <?= json_encode($transactionTrend) ?>;

    // 1. Konfigurasi Pie Chart
    const pieCtx = document.getElementById('pieChart');
    if (pieCtx && pieChartData.length > 0) {
        new Chart(pieCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: pieChartData.map(item => item.nama_kategori),
                datasets: [{
                    label: 'Total Stok',
                    data: pieChartData.map(item => item.total_stok_kategori),
                    backgroundColor: ['#F1C045', '#FFC0CB', '#D86072', '#96C7B3', '#6398A9'], // Sesuaikan warna jika perlu
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'top' } } }
        });
    } else if(pieCtx) {
         pieCtx.parentNode.innerHTML = '<p class="text-center text-gray-500 italic">Data kategori belum tersedia.</p>';
    }

    // 2. Konfigurasi Bar Chart
    const barCtx = document.getElementById('barChart');
    if (barCtx && barChartData.length > 0) {
        new Chart(barCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: barChartData.map(item => item.nama_barang),
                datasets: [{
                    label: 'Jumlah Stok',
                    data: barChartData.map(item => item.stok),
                    backgroundColor: '#631A13', // Sesuaikan warna jika perlu
                    borderColor: '#FFC0CB',    // Sesuaikan warna jika perlu
                    borderWidth: 1
                }]
            },
            options: { responsive: true, indexAxis: 'y', scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
        });
    } else if(barCtx) {
        barCtx.parentNode.innerHTML = '<p class="text-center text-gray-500 italic">Data stok barang belum tersedia.</p>';
    }

    // 3. Konfigurasi Line Chart (jika canvasnya ada di HTML)
    const lineCtx = document.getElementById('lineChart');
    if (lineCtx && trendData.length > 0) {
        new Chart(lineCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: trendData.map(item => item.tanggal),
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: trendData.map(item => item.total_masuk),
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Barang Keluar',
                        data: trendData.map(item => item.total_keluar),
                        borderColor: 'rgba(239, 68, 68, 1)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    } else if (lineCtx) {
         lineCtx.parentNode.innerHTML = '<p class="text-center text-gray-500 italic">Data tren transaksi belum tersedia.</p>';
    }
</script>