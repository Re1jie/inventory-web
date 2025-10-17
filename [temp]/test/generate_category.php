<?php
// ====== Data kategori ======
$kategori = [
    ['id' => 1, 'nama_kategori' => 'atk'],
    ['id' => 2, 'nama_kategori' => 'elektronik'],
    ['id' => 3, 'nama_kategori' => 'perabotan']
];

// ====== Tampilkan query INSERT kategori ======
foreach ($kategori as $k) {
    echo "INSERT INTO categories (id, nama_kategori) VALUES ({$k['id']}, '{$k['nama_kategori']}');\n";
}
?>
