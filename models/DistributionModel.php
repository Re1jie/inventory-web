<?php
class Distribusi {
    private $conn;
    private $table_name = "distributions"; // sesuai tabel kamu

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ambil semua data barang masuk (JOIN)
    public function getAll($search = '') {
        $query = "
            SELECT d.*, 
                   i.nama_barang, 
                   k.nama_kategori,
                   u.username AS nama_petugas,
                   i.stok AS stok_terkini
            FROM {$this->table_name} d
            JOIN items i ON d.id_item = i.id
            JOIN kategori k ON i.id_kategori = k.id
            JOIN users u ON d.id_petugas = u.id
            WHERE d.tipe = 'masuk'
        ";

        if (!empty($search)) {
            $query .= " AND (i.nama_barang LIKE :search OR d.tanggal LIKE :search)";
        }

        $stmt = $this->conn->prepare($query);
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil semua barang (dropdown)
    public function getAllItems() {
        $stmt = $this->conn->prepare("SELECT * FROM items");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil semua petugas (dropdown)
    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = 'petugas'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tambah data distribusi
    public function create($data) {
        $query = "
            INSERT INTO {$this->table_name} 
            (tanggal, id_item, id_petugas, tipe, jumlah, mitra, keterangan)
            VALUES (:tanggal, :id_item, :id_petugas, :tipe, :jumlah, :mitra, :keterangan)
        ";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    // Ambil data by ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table_name} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update data
    public function update($id, $data, $oldData) {
        $query = "
            UPDATE {$this->table_name}
            SET tanggal = :tanggal, id_item = :id_item, id_petugas = :id_petugas,
                jumlah = :jumlah, mitra = :mitra, keterangan = :keterangan
            WHERE id = :id
        ";
        $stmt = $this->conn->prepare($query);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    // Hapus data
    public function delete($id, $data) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table_name} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
