<?php
include __DIR__ . '/../config/database.php';

// ambil semua barang masuk
function getDistribusiMasuk($pdo, $search = '')
{
    $sql = "
        SELECT d.*, 
               i.nama_barang, 
               i.stok, 
               c.nama_kategori,
               u.username AS nama_petugas
        FROM distributions d
        JOIN items i ON d.id_item = i.id
        JOIN categories c ON i.id_kategori = c.id
        JOIN users u ON d.id_petugas = u.id
        WHERE d.tipe = 'masuk' 
          AND (i.nama_barang LIKE :search OR d.tanggal LIKE :search)
        ORDER BY d.tanggal DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => "%$search%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ambil data dropdown barang & petugas
function getItems($pdo)
{
    return $pdo->query("SELECT id, nama_barang FROM items ORDER BY nama_barang")->fetchAll(PDO::FETCH_ASSOC);
}

function getUsers($pdo)
{
    return $pdo->query("SELECT id, username FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
}

// tambah barang masuk
function tambahBarangMasuk($pdo, $id_item, $id_petugas, $tanggal, $jumlah, $keterangan)
{
    // 1. insert ke distributions
    $stmt = $pdo->prepare("
        INSERT INTO distributions (tipe, id_item, id_petugas, tanggal, jumlah, keterangan)
        VALUES ('masuk', ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$id_item, $id_petugas, $tanggal, $jumlah, $keterangan]);

    // 2. update stok
    $pdo->prepare("UPDATE items SET stok = stok + ? WHERE id = ?")->execute([$jumlah, $id_item]);
}
?>
