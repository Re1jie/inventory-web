<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<main class="ml-64 p-6 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Inventaris</h2>

        <!-- Summary Cards -->
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
            <!-- Ditambahkan id dan cursor-pointer -->
            <div id="card-masuk" class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300 cursor-pointer">
                <h3 class="text-lg font-semibold text-gray-500">Barang Masuk</h3>
                <p class="text-4xl font-extrabold text-green-600 mt-2"><?= htmlspecialchars($summaryData['total_masuk']) ?></p>
                <p class="text-sm text-gray-400 mt-1">Unit Diterima</p>
            </div>
            <!-- Ditambahkan id dan cursor-pointer -->
            <div id="card-keluar" class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300 cursor-pointer">
                <h3 class="text-lg font-semibold text-gray-500">Barang Keluar</h3>
                <p class="text-4xl font-extrabold text-red-600 mt-2"><?= htmlspecialchars($summaryData['total_keluar']) ?></p>
                <p class="text-sm text-gray-400 mt-1">Unit Terdistribusi</p>
            </div>
        </div>
        
        <!-- Detail Dropdown (Bagian yang ditambahkan) -->
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
        
        <!-- Main Grid for Charts & Visuals -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- <div class="lg:col-span-3 bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Aktivitas 30 Hari Terakhir</h3>
                <canvas id="lineChart"></canvas>
            </div> -->
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
    // --- SCRIPT UNTUK DROPDOWN (Bagian yang ditambahkan) ---
    const cardMasuk = document.getElementById('card-masuk');
    const detailMasuk = document.getElementById('detail-masuk');
    const cardKeluar = document.getElementById('card-keluar');
    const detailKeluar = document.getElementById('detail-keluar');

    cardMasuk.addEventListener('click', () => {
        detailMasuk.classList.toggle('hidden');
        detailKeluar.classList.add('hidden');
    });

    cardKeluar.addEventListener('click', () => {
        detailKeluar.classList.toggle('hidden');
        detailMasuk.classList.add('hidden');
    });

    // --- SCRIPT UNTUK CHARTS ---
    const pieChartData = <?= json_encode($stockByCategory) ?>;
    const barChartData = <?= json_encode($topItems) ?>;
    const trendData = <?= json_encode($transactionTrend) ?>;

    // 1. Konfigurasi Pie Chart
    if (pieChartData.length > 0) {
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: pieChartData.map(item => item.nama_kategori),
                datasets: [{
                    label: 'Total Stok',
                    data: pieChartData.map(item => item.total_stok_kategori),
                    backgroundColor: ['#3B82F6', '#EF4444', '#F59E0B', '#10B981', '#6366F1'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'top' } } }
        });
    }

    // 2. Konfigurasi Bar Chart
    if (barChartData.length > 0) {
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: barChartData.map(item => item.nama_barang),
                datasets: [{
                    label: 'Jumlah Stok',
                    data: barChartData.map(item => item.stok),
                    backgroundColor: 'rgba(22, 163, 74, 0.7)',
                    borderColor: 'rgba(22, 163, 74, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, indexAxis: 'y', scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
        });
    }

    // 3. Konfigurasi Line Chart
    if (trendData.length > 0) {
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
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
    }
</script>

