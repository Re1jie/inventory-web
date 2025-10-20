<?php
require_once 'models/Barang.php';
// Mulai session untuk menyimpan pesan error validasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$barangModel = new Barang();

// Fungsi validasi sederhana (sesuai gambar, tanpa lokasi)
function validateBarang($data) {
    $errors = [];
    if (empty($data['nama'])) {
        $errors[] = "Nama barang (nama) wajib diisi.";
    }
    if (empty($data['kategori'])) {
        $errors[] = "Kategori (jenis) wajib diisi.";
    }
    if (!isset($data['stok']) || $data['stok'] === '' || !is_numeric($data['stok']) || $data['stok'] < 0) {
        $errors[] = "Stok (jumlah) harus berupa angka valid dan tidak boleh negatif.";
    }
    if (empty($data['satuan'])) {
        $errors[] = "Satuan wajib diisi.";
    }
    return $errors;
}


switch ($uri) {
    case '/barang':
        $barangs = $barangModel->getAll();
        // PERBAIKI PATH VIEW: dari 'views/barang/index.php'
        // Menjadi path yang benar sesuai file Anda
        require 'views/manajemen-barang/manajemen-barang.php'; 
        break;

    case '/barang/tambah':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // TAMBAHKAN VALIDASI INPUT
            $errors = validateBarang($_POST);
            if (empty($errors)) {
                $barangModel->create($_POST); // Model Anda sudah benar
                header('Location: ' . BASE_PATH . '/barang');
                exit();
            } else {
                // Jika validasi gagal, simpan error ke session dan kembali ke form
                $_SESSION['form_errors'] = $errors;
                // Kirim data yang sudah diinput kembali ke form
                $barang = $_POST; 
                require 'views/barang/form.php';
            }

        } else {
            // Tampilkan form kosong
            $barang = null; // Pastikan $barang null agar form tahu ini mode tambah
            require 'views/barang/form.php';
        }
        break;

    case '/barang/edit':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_PATH . '/barang');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // TAMBAHKAN VALIDASI INPUT
            $errors = validateBarang($_POST);
            if (empty($errors)) {
                $barangModel->update($id, $_POST); // Model Anda sudah benar
                header('Location: ' . BASE_PATH . '/barang');
                exit();
            } else {
                // Jika validasi gagal, simpan error dan kembali ke form
                $_SESSION['form_errors'] = $errors;
                // Ambil data barang yang ada, tapi timpa dengan data POST yang gagal
                $barang = $barangModel->find($id);
                $barang = array_merge($barang, $_POST); // Timpa dengan data yang salah
                $barang['id'] = $id; // Pastikan ID tetap ada
                require 'views/barang/form.php';
            }

        } else {
            // Tampilkan form dengan data yang ada
            $barang = $barangModel->find($id);
            require 'views/barang/form.php';
        }
        break;

    case '/barang/hapus':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $barangModel->delete($id);
        }
        header('Location: ' . BASE_PATH . '/barang');
        exit();
        break;
}