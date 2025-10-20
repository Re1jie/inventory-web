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
                i.satuan
                -- i.gambar (kolom gambar tidak ada di tabel 'items')
            FROM items i
            LEFT JOIN categories c ON i.id_kategori = c.id
            ORDER BY i.nama_barang
        ";
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
                i.satuan
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
            INSERT INTO items (nama_barang, id_kategori, stok, satuan) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['nama'], 
            $id_kategori, 
            $data['stok'], 
            $data['satuan']
            // $data['gambar'] ?? null (kolom gambar tidak ada)
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
            SET nama_barang = ?, id_kategori = ?, stok = ?, satuan = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nama'], 
            $id_kategori, 
            $data['stok'], 
            $data['satuan'],
            $id
        ]);
    }

    /**
     * Menghapus data barang
     */
    public function delete($id) {
        // Ganti 'barang' menjadi 'items'
        $stmt = $this->conn->prepare("DELETE FROM items WHERE id=?");
        return $stmt->execute([$id]);
    }
}