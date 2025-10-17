<?php
// Wajib: Memuat Model (Asumsi ada di models/LaporanModel.php)
require_once __DIR__ . '/../models/LaporanModel.php'; 
// Hapus atau Komen out: require_once __DIR__ . '/../../helpers/auth_helper.php'; 
// Jika Anda sudah menjalankan middleware/auth.php di routes.php, require ini tidak diperlukan di sini.

class LaporanController {
    private $model;

    // Pastikan Controller menerima koneksi DB ($conn)
    public function __construct($db) {
        // Cek otentikasi sudah diurus oleh routes.php sebelum memanggil Controller ini.
        
        // Inisialisasi Model
        $this->model = new LaporanModel($db); 
    }

    public function index() {
        // Memastikan sesi sudah dimulai untuk membaca notifikasi flash, yang seharusnya sudah dilakukan oleh middleware/auth.php
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Dapatkan query pencarian dan siapkan data
        $search = $_GET['search'] ?? '';
        
        // Dapatkan data dari Model
        $histori_rows = $this->model->getHistoriBarangMasuk($search);

        // Siapkan variabel untuk View
        $search_query = $search;

        // Muat View
        $view_path = __DIR__ . '/../views/laporan/Laporan_Barang_Masuk.php';
        
        if (file_exists($view_path)) {
            // Include View, membuat variabel di atas tersedia di dalamnya
            include $view_path;
        } else {
            // Tampilkan error jika file View tidak ditemukan
            echo "Error: View Laporan_Barang_Masuk.php tidak ditemukan.";
        }
    }
    
    // Fungsi untuk menghapus
    public function hapus() {
        // Memastikan sesi sudah dimulai untuk menulis notifikasi flash
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_GET['id'])) {
            // 🚨 PERBAIKAN: Gunakan BASE_PATH dan URL Path yang benar
            header('Location: ' . BASE_PATH . '/laporan-barang-masuk');
            exit;
        }
        
        $id = $_GET['id'];
        
        if ($this->model->deleteBarangMasuk($id)) {
            // Set pesan sukses
            $_SESSION['message'] = "Data barang masuk berhasil dihapus.";
        } else {
            // Set pesan gagal
            $_SESSION['error'] = "Gagal menghapus data barang masuk.";
        }
        
        // 🚨 PERBAIKAN: Gunakan BASE_PATH dan URL Path yang benar
        header('Location: ' . BASE_PATH . '/laporan-barang-masuk');
        exit;
    }

    // ... fungsi lain seperti edit() atau download()
}
?>