<?php
include __DIR__ . '/../models/DistribusiModel.php';

$search = $_GET['search'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $id_item = $_POST['id_item'];
    $id_petugas = $_POST['id_petugas'];
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    tambahBarangMasuk($pdo, $id_item, $id_petugas, $tanggal, $jumlah, $keterangan);
    header("Location: ../views/distribution/barang_masuk.php");
    exit;
}

$rows = getDistribusiMasuk($pdo, $search);
$items = getItems($pdo);
$users = getUsers($pdo);
?>
