<?php
// Memanggil file koneksi database dan fungsi queryData
require_once __DIR__ . '/../config/database.php';

// Fungsi untuk mengambil data summary
function getDashboardSummary() {
    global $conn;
    
    // 1. Query untuk total stok dari semua barang di tabel 'items'
    $stok_query = "SELECT SUM(stok) as total_stok FROM items";
    $stok_result = queryData($stok_query);
    $total_stok = $stok_result[0]['total_stok'] ?? 0;

    // 2. Query untuk jumlah barang masuk dari tabel 'distributions'
    $masuk_query = "SELECT SUM(jumlah) as total_masuk FROM distributions WHERE tipe = 'masuk'";
    $masuk_result = queryData($masuk_query);
    $total_masuk = $masuk_result[0]['total_masuk'] ?? 0;

    // 3. Query untuk jumlah barang keluar dari tabel 'distributions'
    $keluar_query = "SELECT SUM(jumlah) as total_keluar FROM distributions WHERE tipe = 'keluar'";
    $keluar_result = queryData($keluar_query);
    $total_keluar = $keluar_result[0]['total_keluar'] ?? 0;

    // 4. (Opsional) Query untuk jumlah jenis barang unik
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

// Di dalam file controllers/DashboardController.php

// ... (setelah fungsi getDashboardSummary)

function getRecentItems($type, $limit = 5) {
    $query = "
        SELECT d.tanggal, i.nama_barang, d.jumlah
        FROM distributions d
        JOIN items i ON d.id_item = i.id
        WHERE d.tipe = '$type'
        ORDER BY d.tanggal DESC
        LIMIT $limit
    ";
    return queryData($query);
}

// Ambil data summary dan data terkini
$summaryData = getDashboardSummary();
$recentMasuk = getRecentItems('masuk');
$recentKeluar = getRecentItems('keluar');

// ... (panggil view)

// Ambil data summary
$summaryData = getDashboardSummary();

// Panggil file view dashboard dan kirimkan data summary
require __DIR__ . '/../views/dashboard/dashboard.php';

?>