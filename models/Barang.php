<?php
require_once 'config/database.php';

class Barang {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM barang");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM barang WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO barang (nama, kategori, stok, satuan, gambar) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['nama'], $data['kategori'], $data['stok'], $data['satuan'], $data['gambar'] ?? null]);
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE barang SET nama=?, kategori=?, stok=?, satuan=?, gambar=? WHERE id=?");
        return $stmt->execute([$data['nama'], $data['kategori'], $data['stok'], $data['satuan'], $data['gambar'] ?? null, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM barang WHERE id=?");
        return $stmt->execute([$id]);
    }
}
