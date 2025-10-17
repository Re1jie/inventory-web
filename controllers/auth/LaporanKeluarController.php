<?php
// Wajib: Memuat Model (Menggunakan LaporanModel.php yang diasumsikan memiliki fungsi untuk data keluar)
// Jalur mundur dua tingkat dari controllers/auth/ ke folder root, lalu masuk ke models/
require_once __DIR__ . '/../../models/LaporanModel.php'; 
// Wajib: Memuat Helper atau fungsi otentikasi (jika diperlukan)
// require_once __DIR__ . '/../../../helpers/auth_helper.php'; // Hapus jika tidak perlu

class LaporanKeluarController {
    private $model;

    public function __construct($db) {
        // Inisialisasi Model
        $this->model = new LaporanModel($db); 
    }

    public function index() {
        // Pastikan session dimulai (penting untuk notifikasi error/success)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Ambil query pencarian dari URL
        $search = $_GET['search'] ?? '';
        
        // Dapatkan data barang keluar dari Model
        // Asumsi: Ada fungsi getHistoriBarangKeluar() di LaporanModel
        $histori_keluar_rows = $this->model->getHistoriBarangKeluar($search);

        // Siapkan variabel untuk View
        $search_query = $search;

        // Muat View Laporan Barang Keluar
        // Jalur mundur dua tingkat dari controllers/auth/ ke views/laporan/
        $view_path = __DIR__ . '/../../views/laporan/Laporan_Barang_Keluar.php';
        
        if (file_exists($view_path)) {
            // Include View, membuat variabel $histori_keluar_rows dan $search_query tersedia
            include $view_path;
        } else {
            echo "Error: View Laporan_Barang_Keluar.php tidak ditemukan.";
        }
    }
    
    // Fungsi untuk menghapus data barang keluar
    public function hapus() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_GET['id'])) {
            header('Location: ' . BASE_PATH . '/laporan-barang-keluar');
            exit;
        }
        
        $id = $_GET['id'];
        
        // Asumsi: Ada fungsi deleteBarangKeluar() di LaporanModel
        if ($this->model->deleteBarangKeluar($id)) {
            $_SESSION['message'] = "Data barang keluar berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus data barang keluar.";
        }
        
        // Redirect kembali ke halaman laporan menggunakan BASE_PATH
        header('Location: ' . BASE_PATH . '/laporan-barang-keluar');
        exit;
    }

    // Anda bisa menambahkan fungsi edit() atau download() di sini
}
