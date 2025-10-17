<?php
// ====== Data barang per kategori ======
$barang = [
    // ATK
    ['id_kategori' => 1, 'nama_barang' => 'Pulpen', 'stok' => 100, 'satuan' => 'pcs'],
    ['id_kategori' => 1, 'nama_barang' => 'Pensil', 'stok' => 150, 'satuan' => 'pcs'],
    ['id_kategori' => 1, 'nama_barang' => 'Buku Tulis', 'stok' => 200, 'satuan' => 'pcs'],

    // Elektronik
    ['id_kategori' => 2, 'nama_barang' => 'Laptop', 'stok' => 10, 'satuan' => 'unit'],
    ['id_kategori' => 2, 'nama_barang' => 'Printer', 'stok' => 5, 'satuan' => 'unit'],
    ['id_kategori' => 2, 'nama_barang' => 'Proyektor', 'stok' => 3, 'satuan' => 'unit'],

    // Perabotan
    ['id_kategori' => 3, 'nama_barang' => 'Kursi', 'stok' => 50, 'satuan' => 'unit'],
    ['id_kategori' => 3, 'nama_barang' => 'Meja', 'stok' => 30, 'satuan' => 'unit'],
    ['id_kategori' => 3, 'nama_barang' => 'Lemari', 'stok' => 20, 'satuan' => 'unit']
];

// ====== Generate ID barang otomatis dan tampilkan query ======
$id_barang = 1;
foreach ($barang as $b) {
    echo "INSERT INTO items (id, id_kategori, nama_barang, stok, satuan) VALUES ($id_barang, {$b['id_kategori']}, '{$b['nama_barang']}', {$b['stok']}, '{$b['satuan']}');\n";
    $id_barang++;
}
?>
