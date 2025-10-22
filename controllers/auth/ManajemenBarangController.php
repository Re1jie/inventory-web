<?php
require_once 'models/Barang.php';
// Mulai session untuk menyimpan pesan error validasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$barangModel = new Barang();

// [MODIFIKASI] Fungsi helper untuk cek hak akses
function cekAksesManajemen() {
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'superadmin'])) {
        http_response_code(403);
        echo "<h1>403 Forbidden</h1><p>Anda tidak memiliki hak akses untuk melakukan aksi ini.</p>";
        exit;
    }
}


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

/**
 * [FUNGSI BARU]
 * Fungsi untuk menangani upload file gambar.
 * Mengembalikan path gambar jika berhasil, atau null jika tidak ada file.
 * Melempar Exception jika terjadi error.
 */
function handleUpload($file) {
    // Cek jika tidak ada file yang di-upload atau ada error
    if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        return null; // Tidak ada file di-upload, ini bukan error
    }

    if ($file['error'] != UPLOAD_ERR_OK) {
        throw new Exception("Error saat meng-upload file. Kode: " . $file['error']);
    }

    // Tentukan folder upload (sesuai Langkah 1)
    $uploadDir = __DIR__ . '/../../assets/uploads/';
    
    // Buat nama file unik untuk menghindari tumpang tindih
    $fileName = uniqid() . '-' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    
    // Dapatkan path yang akan disimpan ke database (relatif dari root web)
    // Ini PENTING agar bisa diakses dari <img src="">
    $dbPath = '/assets/uploads/' . $fileName;

    // Validasi tipe file (hanya izinkan gambar)
    $imageType = getimagesize($file['tmp_name']);
    if (!$imageType || !in_array($imageType['mime'], ['image/jpeg', 'image/png', 'image/gif'])) {
        throw new Exception("File yang di-upload bukan gambar (izinkan: jpg, png, gif).");
    }

    // Validasi ukuran file (misal: maks 2MB)
    if ($file['size'] > 2 * 1024 * 1024) { // 2 Megabytes
        throw new Exception("Ukuran file terlalu besar (maks 2MB).");
    }

    // Pindahkan file dari temp ke folder upload
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $dbPath; // Kembalikan path untuk disimpan ke DB
    } else {
        throw new Exception("Gagal memindahkan file yang di-upload.");
    }
}


switch ($uri) {
    case '/barang':
        // 'petugas' BISA melihat halaman ini
        $barangs = $barangModel->getAll();
        require 'views/manajemen-barang/manajemen-barang.php'; 
        break;

    case '/barang/tambah':
        // [MODIFIKASI] Cek akses
        cekAksesManajemen();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // 1. Handle upload gambar
                $imagePath = handleUpload($_FILES['gambar'] ?? null);
                $_POST['gambar'] = $imagePath; // Tambahkan path gambar ke data POST

                // 2. Validasi sisa data
                $errors = validateBarang($_POST);
                if (empty($errors)) {
                    // 3. Simpan ke database
                    $barangModel->create($_POST); 
                    header('Location: ' . BASE_PATH . '/barang');
                    exit();
                } else {
                    $_SESSION['form_errors'] = $errors;
                    $barang = $_POST; 
                    $categories = $barangModel->getAllCategories(); // [DITAMBAHKAN]
                    require 'views/barang/form.php';
                }
            } catch (Exception $e) {
                // Tangkap error dari handleUpload()
                $_SESSION['form_errors'] = [$e->getMessage()];
                $barang = $_POST;
                $categories = $barangModel->getAllCategories(); // [DITAMBAHKAN]
                require 'views/barang/form.php';
            }
        } else {
            // Tampilkan form kosong
            $barang = null; 
            $categories = $barangModel->getAllCategories(); // [DITAMBAHKAN]
            require 'views/barang/form.php';
        }
        break;

    case '/barang/edit':
        // [MODIFIKASI] Cek akses
        cekAksesManajemen();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_PATH . '/barang');
            exit();
        }

        // Ambil data lama dulu (termasuk path gambar lama)
        $barang = $barangModel->find($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // 1. Handle upload gambar baru
                $newImagePath = handleUpload($_FILES['gambar'] ?? null);
                
                if ($newImagePath) {
                    // Jika ada gambar baru, gunakan path baru
                    $_POST['gambar'] = $newImagePath;
                    // [OPSIONAL] Hapus gambar lama
                    if (!empty($barang['gambar']) && file_exists(__DIR__ . '/../..' . $barang['gambar'])) {
                        @unlink(__DIR__ . '/../..' . $barang['gambar']);
                    }
                } else {
                    // Jika tidak ada gambar baru, gunakan path gambar lama dari DB
                    $_POST['gambar'] = $barang['gambar'] ?? null;
                }

                // 2. Validasi sisa data
                $errors = validateBarang($_POST);
                if (empty($errors)) {
                    // 3. Update database
                    $barangModel->update($id, $_POST);
                    header('Location: ' . BASE_PATH . '/barang');
                    exit();
                } else {
                    $_SESSION['form_errors'] = $errors;
                    $barang = array_merge($barang, $_POST); 
                    $barang['id'] = $id; 
                    $categories = $barangModel->getAllCategories(); // [DITAMBAHKAN]
                    require 'views/barang/form.php';
                }
            } catch (Exception $e) {
                // Tangkap error dari handleUpload()
                $_SESSION['form_errors'] = [$e->getMessage()];
                $barang = array_merge($barang, $_POST);
                $barang['id'] = $id;
                $categories = $barangModel->getAllCategories(); // [DITAMBAHKAN]
                require 'views/barang/form.php';
            }

        } else {
            // Tampilkan form dengan data yang ada
            $categories = $barangModel->getAllCategories(); // [DITAMBAHKAN]
            require 'views/barang/form.php';
        }
        break;

    case '/barang/hapus':
        // [MODIFIKASI] Cek akses
        cekAksesManajemen();

        $id = $_GET['id'] ?? null;
        if ($id) {
            // Model delete() sekarang juga akan menghapus file
            $barangModel->delete($id);
        }
        header('Location: ' . BASE_PATH . '/barang');
        exit();
        break;
}