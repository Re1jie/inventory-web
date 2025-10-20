<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Mengambil data distribusi (barang masuk / keluar)
 * 
 * @param string $tipe 'masuk' atau 'keluar'
 */
function getDistributions($tipe) {
    global $conn;

    $query = "
        SELECT 
            d.id,
            d.tanggal,
            u.nama AS nama_petugas,
            d.mitra,
            i.nama_barang,
            c.nama_kategori,
            d.jumlah,
            i.stok AS stok_terkini,
            d.keterangan
        FROM distributions d
        JOIN users u ON d.id_petugas = u.id
        JOIN items i ON d.id_item = i.id
        JOIN categories c ON i.id_kategori = c.id
        WHERE d.tipe = ?
        ORDER BY d.tanggal DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tipe);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}
function getAllItems() {
    global $conn;
    $query = "SELECT id, nama_barang FROM items ORDER BY nama_barang ASC";
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Untuk menampilkan halaman barang masuk
 */
function showBarangMasuk() {
    $data = getDistributions('masuk');
    require __DIR__ . '/../views/distribution/barang_masuk.php';
}

/**
 * Untuk menampilkan halaman barang keluar
 */
function showBarangKeluar() {
    $data = getDistributions('keluar');
    require __DIR__ . '/../views/distribution/barang_keluar.php';
}
