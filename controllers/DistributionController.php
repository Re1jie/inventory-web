<?php
// Mulai session untuk feedback message (sukses/error)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Panggil file koneksi
require_once __DIR__ . '/../config/database.php';
// =================================================================
// 1. LOGIKA HANDLE FORM (TAMBAH BARANG MASUK)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    
    // Ambil data dari form
    $id_item = $_POST['id_item'];
    $id_petugas = $_POST['id_petugas'];
    $tanggal = $_POST['tanggal'];
    $jumlah = (int)$_POST['jumlah'];
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    $keterangan = $_POST['keterangan'] ?? null;

    $tipe = 'masuk';

    // Validasi sederhana
    if ($jumlah <= 0) {
        $_SESSION['error_message'] = "Jumlah barang harus lebih dari 0.";
        // PERBAIKAN: Gunakan $_SERVER['PHP_SELF'] atau path absolut dari root
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Langkah 1: Insert data ke tabel 'distributions'
        $sql_dist = "INSERT INTO distributions (nama_pelanggan, tipe, id_item, id_petugas, tanggal, jumlah, keterangan) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_dist = $pdo->prepare($sql_dist);
        $stmt_dist->execute([
            $nama_pelanggan, $tipe, $id_item, $id_petugas, $tanggal, $jumlah, $keterangan
        ]);

        // Langkah 2: Update (tambah) stok di tabel 'items'
        $sql_item = "UPDATE items SET stok = stok + ? WHERE id = ?";
        $stmt_item = $pdo->prepare($sql_item);
        $stmt_item->execute([$jumlah, $id_item]);

        $pdo->commit();
        $_SESSION['success_message'] = "Barang masuk berhasil ditambahkan.";

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Gagal menyimpan data: " . $e->getMessage();
    }

    // PERBAIKAN: Redirect ke halaman yang sama
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
// =================================================================
// 2. LOGIKA HANDLE FORM (TAMBAH BARANG KELUAR)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_keluar'])) {
    
    // Ambil data dari form
    $id_item = $_POST['id_item'];
    $id_petugas = $_POST['id_petugas'];
    $tanggal = $_POST['tanggal'];
    $jumlah = (int)$_POST['jumlah'];
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    $keterangan = $_POST['keterangan'] ?? null;
    
    $tipe = 'keluar';

    // Validasi
    if ($jumlah <= 0) {
        $_SESSION['error_message'] = "Jumlah barang harus lebih dari 0.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Langkah 1: Cek stok saat ini (PENTING untuk barang keluar)
        $stmt_check = $pdo->prepare("SELECT stok FROM items WHERE id = ? FOR UPDATE");
        $stmt_check->execute([$id_item]);
        $current_stok = $stmt_check->fetchColumn();

        if ($jumlah > $current_stok) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Gagal. Stok barang tidak mencukupi (Stok saat ini: $current_stok).";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        // Langkah 2: Insert data ke tabel 'distributions'
        $sql_dist = "INSERT INTO distributions (nama_pelanggan, tipe, id_item, id_petugas, tanggal, jumlah, keterangan) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_dist = $pdo->prepare($sql_dist);
        $stmt_dist->execute([
            $nama_pelanggan, $tipe, $id_item, $id_petugas, $tanggal, $jumlah, $keterangan
        ]);

        // Langkah 3: Update (kurangi) stok di tabel 'items'
        $sql_item = "UPDATE items SET stok = stok - ? WHERE id = ?";
        $stmt_item = $pdo->prepare($sql_item);
        $stmt_item->execute([$jumlah, $id_item]);

        $pdo->commit();
        $_SESSION['success_message'] = "Barang keluar berhasil ditambahkan.";

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Gagal menyimpan data: " . $e->getMessage();
    }

    // PERBAIKAN: Redirect ke halaman yang sama
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
// =================================================================
// 3. LOGIKA AMBIL DATA UNTUK TAMPILAN (VIEW)
// =================================================================

// Variabel $page_type ( 'masuk' atau 'keluar' ) 
// harus didefinisikan di file view SEBELUM include controller ini.

$items = [];
$users = [];
$rows = [];

try {
    $stmt_items = $pdo->query("SELECT id, nama_barang, stok FROM items ORDER BY nama_barang ASC");
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    $stmt_users = $pdo->query("SELECT id, username FROM users WHERE role = 'petugas' ORDER BY username ASC");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    $search = $_GET['search'] ?? '';

    // BARANG KELUAR (dengan mitra dari distributions.nama_pelanggan)
    if (isset($page_type) && $page_type == 'keluar') {
        $sql_rows = "SELECT 
                        d.id AS id_distribusi,
                        d.tanggal,
                        u.username AS petugas,
                        d.nama_pelanggan AS mitra,           -- <-- ambil mitra dari distributions
                        i.nama_barang,
                        cat.nama_kategori AS kategori,      -- kategori dari tabel categories
                        d.jumlah,
                        i.stok AS stok_terkini,
                        d.keterangan
                    FROM distributions d
                    JOIN items i ON d.id_item = i.id
                    JOIN users u ON d.id_petugas = u.id
                    LEFT JOIN categories cat ON i.id_kategori = cat.id
                    WHERE d.tipe = 'keluar'";

        if (!empty($search)) {
            $sql_rows .= " AND (i.nama_barang LIKE ? OR u.username LIKE ? OR d.nama_pelanggan LIKE ? OR d.tanggal LIKE ?)";
            $stmt_rows = $pdo->prepare($sql_rows . " ORDER BY d.tanggal DESC, d.id DESC");
            $stmt_rows->execute([
                "%$search%", "%$search%", "%$search%", "%$search%"
            ]);
        } else {
            $stmt_rows = $pdo->prepare($sql_rows . " ORDER BY d.tanggal DESC, d.id DESC");
            $stmt_rows->execute();
        }

        $rows = $stmt_rows->fetchAll(PDO::FETCH_ASSOC);
    }

    // BARANG MASUK (tetap bisa gunakan mitra = d.nama_pelanggan, biasanya null)
    elseif (isset($page_type) && $page_type == 'masuk') {
        $sql_rows = "SELECT 
                        d.id AS id_distribusi,
                        d.tanggal,
                        u.username AS petugas,
                        d.nama_pelanggan AS mitra,
                        i.nama_barang,
                        cat.nama_kategori AS kategori,
                        d.jumlah,
                        i.stok AS stok_terkini,
                        d.keterangan
                    FROM distributions d
                    JOIN items i ON d.id_item = i.id
                    JOIN users u ON d.id_petugas = u.id
                    LEFT JOIN categories cat ON i.id_kategori = cat.id
                    WHERE d.tipe = 'masuk'";

        if (!empty($search)) {
            $sql_rows .= " AND (i.nama_barang LIKE ? OR u.username LIKE ? OR d.nama_pelanggan LIKE ? OR d.tanggal LIKE ?)";
            $stmt_rows = $pdo->prepare($sql_rows . " ORDER BY d.tanggal DESC, d.id DESC");
            $stmt_rows->execute([
                "%$search%", "%$search%", "%$search%", "%$search%"
            ]);
        } else {
            $stmt_rows = $pdo->prepare($sql_rows . " ORDER BY d.tanggal DESC, d.id DESC");
            $stmt_rows->execute();
        }

        $rows = $stmt_rows->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}
?>