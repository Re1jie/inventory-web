<?php
require_once __DIR__ . '/../../config/database.php'; // pastikan koneksi ke DB
require_once __DIR__ . '/../../middleware/auth.php';

class BarangController
{
    public function index()
    {
        global $pdo;
        $barangs = $pdo->query("SELECT * FROM barang")->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../../views/barang/manajemen-barang.php';
    }

    public function tambah()
    {
        include __DIR__ . '/../../views/barang/tambah-barang.php';
    }

    public function simpan()
    {
        global $pdo;

        $nama = $_POST['nama'];
        $kategori = $_POST['kategori'];
        $stok = $_POST['stok'];
        $satuan = $_POST['satuan'];

        $gambarPath = null;
        if (!empty($_FILES['gambar']['name'])) {
            $uploadDir = __DIR__ . '/../../../public/uploads/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileName = time() . '_' . basename($_FILES['gambar']['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
                $gambarPath = '/uploads/' . $fileName;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO barang (nama, kategori, stok, satuan, gambar) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $kategori, $stok, $satuan, $gambarPath]);

        header("Location: " . BASE_PATH . "/barang");
        exit;
    }
}
