<?php
// Memanggil file koneksi database
require_once __DIR__ . '/../config/database.php';

/**
 * Mengambil data summary utama untuk kartu di bagian atas.
 */
function getDashboardSummary() {
    global $conn;
    
    $stok_query = "SELECT SUM(stok) as total_stok FROM items";
    $stok_result = queryData($stok_query);
    $total_stok = $stok_result[0]['total_stok'] ?? 0;

    $masuk_query = "SELECT SUM(jumlah) as total_masuk FROM distributions WHERE tipe = 'masuk'";
    $masuk_result = queryData($masuk_query);
    $total_masuk = $masuk_result[0]['total_masuk'] ?? 0;

    $keluar_query = "SELECT SUM(jumlah) as total_keluar FROM distributions WHERE tipe = 'keluar'";
    $keluar_result = queryData($keluar_query);
    $total_keluar = $keluar_result[0]['total_keluar'] ?? 0;

    $item_query = "SELECT COUNT(id) as jumlah_item FROM items";
    $item_result = queryData($item_query);
    $jumlah_item = $item_result[0]['jumlah_item'] ?? 0;

    return [
        'total_stok' => $total_stok,
        'total_masuk' => $total_masuk,
        'total_keluar' => $total_keluar,
        'jumlah_item' => $jumlah_item
    ];
}

/**
 * Mengambil data stok berdasarkan kategori untuk Pie Chart.
 */
function getStockByCategory() {
    global $conn;
    $query = "
        SELECT c.nama_kategori, SUM(i.stok) as total_stok_kategori
        FROM items i
        JOIN categories c ON i.id_kategori = c.id
        GROUP BY c.nama_kategori
        ORDER BY total_stok_kategori DESC
    ";
    return queryData($query);
}

/**
 * Mengambil data 10 barang dengan stok terbanyak untuk Bar Chart.
 */
function getTopItems($limit = 10) {
    global $conn;
    $query = "
        SELECT nama_barang, stok
        FROM items
        ORDER BY stok DESC
        LIMIT $limit
    ";
    return queryData($query);
}

/**
 * Mengambil data barang dengan stok rendah (di bawah ambang batas) untuk daftar peringatan.
 */
function getLowStockItems($threshold = 10) {
    global $conn;
    $query = "
        SELECT nama_barang, stok, satuan
        FROM items
        WHERE stok <= $threshold
        ORDER BY stok ASC
    ";
    return queryData($query);
}

/**
 * Mengambil data tren transaksi (masuk vs keluar) selama 30 hari terakhir untuk Line Chart.
 */
function getTransactionTrend() {
    global $conn;
    $query = "
        SELECT
            tanggal,
            SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) as total_masuk,
            SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as total_keluar
        FROM distributions
        WHERE tanggal >= CURDATE() - INTERVAL 30 DAY
        GROUP BY tanggal
        ORDER BY tanggal ASC
    ";
    return queryData($query);
}

// ==================================================================
// [BARU] FUNGSI UNTUK MENGAMBIL 5 TRANSAKSI TERAKHIR
// ==================================================================
/**
 * Mengambil 5 data barang masuk terakhir.
 */
function getRecentMasuk($limit = 5) {
    global $conn;
    $query = "
        SELECT d.tanggal, i.nama_barang, d.jumlah 
        FROM distributions d
        JOIN items i ON d.id_item = i.id
        WHERE d.tipe = 'masuk'
        ORDER BY d.tanggal DESC, d.id DESC 
        LIMIT $limit
    ";
    return queryData($query);
}

/**
 * Mengambil 5 data barang keluar terakhir.
 */
function getRecentKeluar($limit = 5) {
    global $conn;
    $query = "
        SELECT d.tanggal, i.nama_barang, d.jumlah 
        FROM distributions d
        JOIN items i ON d.id_item = i.id
        WHERE d.tipe = 'keluar'
        ORDER BY d.tanggal DESC, d.id DESC 
        LIMIT $limit
    ";
    return queryData($query);
}


// ==================================================================
// MENGAMBIL SEMUA DATA UNTUK DASHBOARD
// ==================================================================
$summaryData      = getDashboardSummary();
$stockByCategory  = getStockByCategory();
$topItems         = getTopItems();
$lowStockItems    = getLowStockItems();
$transactionTrend = getTransactionTrend();
$recentMasuk      = getRecentMasuk();   // Panggil fungsi baru
$recentKeluar     = getRecentKeluar();  // Panggil fungsi baru


// Memanggil file view dan mengirimkan semua data yang sudah diambil
require __DIR__ . '/../views/dashboard/dashboard.php';

?>