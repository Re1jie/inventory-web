<?php
// Jangan panggil 'config/database.php' di sini, 
// karena sudah dipanggil oleh controller yang memuat model ini.
// require_once 'config/database.php'; 
require_once __DIR__ . '/../config/database.php';
class Barang {
    private $conn;

    public function __construct() {
        // Panggil fungsi getConnection() yang sudah kita buat
        $this->conn = getConnection(); 
    }

    /**
     * Mengambil semua barang, digabung dengan nama kategorinya
     */
    public function getAll() {
        $query = "
            SELECT 
                i.id, 
                i.nama_barang AS nama, 
                c.nama_kategori AS kategori, 
                i.stok, 
                i.satuan,
                i.gambar -- [DITAMBAHKAN]
            FROM items i
            LEFT JOIN categories c ON i.id_kategori = c.id
            ORDER BY i.nama_barang
        ";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCategories() {
        $query = "SELECT id, nama_kategori FROM categories ORDER BY nama_kategori ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mencari satu barang berdasarkan ID, digabung dengan nama kategorinya
     */
    public function find($id) {
        $query = "
            SELECT 
                i.id, 
                i.nama_barang AS nama, 
                c.nama_kategori AS kategori, 
                i.stok, 
                i.satuan,
                i.gambar -- [DITAMBAHKAN]
            FROM items i
            LEFT JOIN categories c ON i.id_kategori = c.id
            WHERE i.id = ?
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Helper function untuk mencari atau membuat kategori
     * Ini menerjemahkan nama kategori (string) menjadi ID (int)
     */
    private function getKategoriId($nama_kategori) {
        // 1. Coba cari kategori yang ada
        $stmt = $this->conn->prepare("SELECT id FROM categories WHERE nama_kategori = ?");
        $stmt->execute([$nama_kategori]);
        $kategori = $stmt->fetch();

        if ($kategori) {
            return $kategori['id'];
        } else {
            // 2. Jika tidak ada, buat kategori baru
            $stmt = $this->conn->prepare("INSERT INTO categories (nama_kategori) VALUES (?)");
            $stmt->execute([$nama_kategori]);
            return $this->conn->lastInsertId();
        }
    }

    /**
     * Membuat data barang baru
     */
    public function create($data) {
        // Dapatkan ID kategori (int) dari nama kategori (string)
        $id_kategori = $this->getKategoriId($data['kategori']);

        $stmt = $this->conn->prepare("
            INSERT INTO items (nama_barang, id_kategori, stok, satuan, gambar) 
            VALUES (?, ?, ?, ?, ?) -- [DITAMBAHKAN]
        ");
        
        return $stmt->execute([
            $data['nama'], 
            $id_kategori, 
            $data['stok'], 
            $data['satuan'],
            $data['gambar'] ?? null // [DITAMBAHKAN]
        ]);
    }

    /**
     * Memperbarui data barang
     */
    public function update($id, $data) {
        // Dapatkan ID kategori (int) dari nama kategori (string)
        $id_kategori = $this->getKategoriId($data['kategori']);

        $stmt = $this->conn->prepare("
            UPDATE items 
            SET nama_barang = ?, id_kategori = ?, stok = ?, satuan = ?, gambar = ?
            WHERE id = ?
        "); // [DITAMBAHKAN]
        
        return $stmt->execute([
            $data['nama'], 
            $id_kategori, 
            $data['stok'], 
            $data['satuan'],
            $data['gambar'] ?? null, // [DITAMBAHKAN]
            $id
        ]);
    }

    /**
     * Menghapus data barang
     */
    public function delete($id) {
        // [OPSIONAL] Hapus file gambar sebelum menghapus data DB
        $barang = $this->find($id);
        if (!empty($barang['gambar'])) {
            $filePath = __DIR__ . '/..' . $barang['gambar'];
            if (file_exists($filePath)) {
                @unlink($filePath); // Gunakan @ untuk menekan error jika file tidak ditemukan
            }
        }

        $stmt = $this->conn->prepare("DELETE FROM items WHERE id=?");
        return $stmt->execute([$id]);
    }
}