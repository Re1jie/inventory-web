<?php
// Mulai session untuk feedback message (sukses/error)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Panggil file koneksi
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

// =================================================================
// 1. LOGIKA HANDLE FORM (TAMBAH BARANG MASUK)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $id_item = $_POST['id_item'];
    $id_petugas = $_POST['id_petugas'];
    $tanggal = $_POST['tanggal'];
    $jumlah = (int)$_POST['jumlah'];
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    $keterangan = $_POST['keterangan'] ?? null;
    $tipe = 'masuk';

    if ($jumlah <= 0) {
        $_SESSION['error_message'] = "Jumlah barang harus lebih dari 0.";
        // PERBAIKAN: Gunakan $_SERVER['PHP_SELF'] atau path absolut dari root
        header('Location: ' . BASE_PATH . '/barang-masuk');
        exit;
    }
}
// =================================================================
// 1. LOGIKA HANDLE FORM (TAMBAH BARANG MASUK)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $id_item = $_POST['id_item'];
    $id_petugas = $_POST['id_petugas'];
    $tanggal = $_POST['tanggal'];
    $jumlah = (int)$_POST['jumlah'];
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    $keterangan = $_POST['keterangan'] ?? null;
    $tipe = 'masuk';

    // *** TAMBAHKAN VALIDASI TANGGAL DI SINI ***
    $tanggal_sekarang = date('Y-m-d');
    if ($tanggal > $tanggal_sekarang) {
        $_SESSION['error_message'] = "Tanggal transaksi tidak boleh melebihi tanggal hari ini.";
        header('Location: ' . BASE_PATH . '/barang-masuk');
        exit;
    }
    // *** AKHIR VALIDASI TANGGAL ***

    if ($jumlah <= 0) {
        $_SESSION['error_message'] = "Jumlah barang harus lebih dari 0.";
        header('Location: ' . BASE_PATH . '/barang-masuk');
        exit;
    }

    try {
        $pdo->beginTransaction();

        $sql_dist = "INSERT INTO distributions (nama_pelanggan, tipe, id_item, id_petugas, tanggal, jumlah, keterangan) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_dist = $pdo->prepare($sql_dist);
        $stmt_dist->execute([$nama_pelanggan, $tipe, $id_item, $id_petugas, $tanggal, $jumlah, $keterangan]);

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
    header('Location: ' . BASE_PATH . '/barang-masuk');
    exit;
}
// =================================================================
// 2. LOGIKA HANDLE FORM (TAMBAH BARANG KELUAR)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_keluar'])) {

    $id_item = $_POST['id_item'];
    $id_petugas = $_POST['id_petugas'];
    $tanggal = $_POST['tanggal'];
    $jumlah = (int)$_POST['jumlah'];
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    $keterangan = $_POST['keterangan'] ?? null;
    $tipe = 'keluar';

    if ($jumlah <= 0) {
        $_SESSION['error_message'] = "Jumlah barang harus lebih dari 0.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
// =================================================================
// 2. LOGIKA HANDLE FORM (TAMBAH BARANG KELUAR)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_keluar'])) {

    $id_item = $_POST['id_item'];
    $id_petugas = $_POST['id_petugas'];
    $tanggal = $_POST['tanggal'];
    $jumlah = (int)$_POST['jumlah'];
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    $keterangan = $_POST['keterangan'] ?? null;
    $tipe = 'keluar';

    // *** TAMBAHKAN VALIDASI TANGGAL DI SINI ***
    $tanggal_sekarang = date('Y-m-d');
    if ($tanggal > $tanggal_sekarang) {
        $_SESSION['error_message'] = "Tanggal transaksi tidak boleh melebihi tanggal hari ini.";
        header('Location: ' . BASE_PATH . '/barang-keluar');
        exit;
    }
    // *** AKHIR VALIDASI TANGGAL ***

    if ($jumlah <= 0) {
        $_SESSION['error_message'] = "Jumlah barang harus lebih dari 0.";
        header('Location: ' . BASE_PATH . '/barang-keluar'); // Perbaikan: Redirect ke barang-keluar
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmt_check = $pdo->prepare("SELECT stok FROM items WHERE id = ? FOR UPDATE");
        $stmt_check->execute([$id_item]);
        $current_stok = $stmt_check->fetchColumn();

        if ($jumlah > $current_stok) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Gagal. Stok barang tidak mencukupi (Stok saat ini: $current_stok).";
            header('Location: ' . BASE_PATH . '/barang-keluar');
            exit;
        }

        $sql_dist = "INSERT INTO distributions (nama_pelanggan, tipe, id_item, id_petugas, tanggal, jumlah, keterangan) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_dist = $pdo->prepare($sql_dist);
        $stmt_dist->execute([$nama_pelanggan, $tipe, $id_item, $id_petugas, $tanggal, $jumlah, $keterangan]);

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
    header('Location: ' . BASE_PATH . '/barang-keluar');
    exit;
}
// =================================================================
// [ BLOK BARU UNTUK DITAMBAHKAN ]
// 2.A. LOGIKA HANDLE DELETE (BARANG MASUK / KELUAR)
// =================================================================
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // Periksa hak akses (hanya admin/superadmin)
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'superadmin'])) {
        $_SESSION['error_message'] = "Anda tidak memiliki hak akses untuk menghapus data.";
        // Tentukan halaman redirect berdasarkan 'page' dari URL
        $redirect_url = BASE_PATH . '/' . ($_GET['page'] == 'keluar' ? 'barang-keluar' : 'barang-masuk');
        header('Location: ' . $redirect_url);
        exit;
    }

    $id_distribusi = (int)$_GET['id'];
    
    // Tentukan halaman asal untuk redirect
    $page_type = $_GET['page'] ?? 'masuk'; // 'masuk' atau 'keluar'
    $redirect_url = BASE_PATH . '/' . ($page_type == 'masuk' ? 'barang-masuk' : 'barang-keluar');

    try {
        $pdo->beginTransaction();

        // 1. Ambil data distribusi yang akan dihapus
        $stmt_get = $pdo->prepare("SELECT id_item, jumlah, tipe FROM distributions WHERE id = ? FOR UPDATE");
        $stmt_get->execute([$id_distribusi]);
        $dist = $stmt_get->fetch(PDO::FETCH_ASSOC);

        if ($dist) {
            
            // 2. Kembalikan/Sesuaikan stok
            if ($dist['tipe'] == 'masuk') {
                // Jika hapus data BARANG MASUK, stok barang DIKURANGI
                
                // Validasi: pastikan stok cukup untuk dikurangi
                $stmt_check = $pdo->prepare("SELECT stok FROM items WHERE id = ?");
                $stmt_check->execute([$dist['id_item']]);
                $current_stok = $stmt_check->fetchColumn();

                if ($dist['jumlah'] > $current_stok) {
                     $pdo->rollBack();
                    $_SESSION['error_message'] = "Gagal hapus. Stok barang tidak mencukupi untuk dikembalikan (Stok saat ini: $current_stok).";
                    header('Location: ' . $redirect_url);
                    exit;
                }

                $stmt_item = $pdo->prepare("UPDATE items SET stok = stok - ? WHERE id = ?");
            } else {
                // Jika hapus data BARANG KELUAR, stok barang DITAMBAH (dikembalikan)
                $stmt_item = $pdo->prepare("UPDATE items SET stok = stok + ? WHERE id = ?");
            }
            $stmt_item->execute([$dist['jumlah'], $dist['id_item']]);

            // 3. Hapus data distribusi
            $stmt_del = $pdo->prepare("DELETE FROM distributions WHERE id = ?");
            $stmt_del->execute([$id_distribusi]);

            $pdo->commit();
            $_SESSION['success_message'] = "Data distribusi berhasil dihapus dan stok telah disesuaikan.";
        } else {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Data distribusi tidak ditemukan.";
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Gagal menghapus data: " . $e->getMessage();
    }

    header('Location: ' . $redirect_url);
    exit;
}
// =================================================================
// [ BLOK BARU UNTUK DITAMBAHKAN ]
// 2.B. LOGIKA HANDLE FORM (EDIT BARANG MASUK / KELUAR)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    // Periksa hak akses
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'superadmin'])) {
        $_SESSION['error_message'] = "Anda tidak memiliki hak akses untuk mengedit data.";
        $redirect_url = BASE_PATH . '/' . ($_POST['page_type'] ?? 'barang-masuk');
        header('Location: ' . $redirect_url);
        exit;
    }

    // Data baru dari form
    $id_distribusi = (int)$_POST['id_distribusi'];
    $id_item_baru = $_POST['id_item'];
    $id_petugas_baru = $_POST['id_petugas'];
    $tanggal_baru = $_POST['tanggal'];
    $jumlah_baru = (int)$_POST['jumlah'];
    $nama_pelanggan_baru = trim($_POST['nama_pelanggan'] ?? '');
    $keterangan_baru = $_POST['keterangan'] ?? null;
    $page_type = $_POST['page_type'] ?? 'masuk';
    $redirect_url = BASE_PATH . '/' . ($page_type == 'masuk' ? 'barang-masuk' : 'barang-keluar');

    if ($jumlah_baru <= 0) {
        $_SESSION['error_message'] = "Jumlah barang harus lebih dari 0.";
        header('Location: ' . $redirect_url);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Ambil data LAMA dari distribusi
        $stmt_get = $pdo->prepare("SELECT id_item, jumlah, tipe FROM distributions WHERE id = ? FOR UPDATE");
        $stmt_get->execute([$id_distribusi]);
        $dist_lama = $stmt_get->fetch(PDO::FETCH_ASSOC);

        if ($dist_lama) {
            $id_item_lama = $dist_lama['id_item'];
            $jumlah_lama = $dist_lama['jumlah'];
            $tipe = $dist_lama['tipe']; // 'masuk' atau 'keluar'

            // 2. REVERSAL (Kembalikan stok lama)
            if ($tipe == 'masuk') {
                // Jika ini adalah data 'masuk', kurangi stok lama
                $stmt_rev = $pdo->prepare("UPDATE items SET stok = stok - ? WHERE id = ?");
            } else {
                // Jika ini adalah data 'keluar', tambahkan (kembalikan) stok lama
                $stmt_rev = $pdo->prepare("UPDATE items SET stok = stok + ? WHERE id = ?");
            }
            $stmt_rev->execute([$jumlah_lama, $id_item_lama]);


            // 3. APLIKASI (Terapkan stok baru)
            if ($tipe == 'masuk') {
                // Tambahkan stok baru ke item baru
                $stmt_app = $pdo->prepare("UPDATE items SET stok = stok + ? WHERE id = ?");
            } else {
                // Kurangi stok baru dari item baru (Validasi dulu)
                $stmt_check = $pdo->prepare("SELECT stok FROM items WHERE id = ? FOR UPDATE");
                $stmt_check->execute([$id_item_baru]);
                $current_stok_baru = $stmt_check->fetchColumn();

                if ($jumlah_baru > $current_stok_baru && $id_item_lama != $id_item_baru) { 
                    // Jika stok tidak cukup (dan kita tidak mengedit item yg sama)
                    $pdo->rollBack();
                    $_SESSION['error_message'] = "Gagal. Stok barang baru tidak mencukupi (Stok: $current_stok_baru).";
                    header('Location: ' . $redirect_url);
                    exit;
                }
                $stmt_app = $pdo->prepare("UPDATE items SET stok = stok - ? WHERE id = ?");
            }
            $stmt_app->execute([$jumlah_baru, $id_item_baru]);

            // 4. Update data distribusi itu sendiri
            $sql_update_dist = "UPDATE distributions 
                                SET nama_pelanggan = ?, id_item = ?, id_petugas = ?, 
                                    tanggal = ?, jumlah = ?, keterangan = ?
                                WHERE id = ?";
            $stmt_update_dist = $pdo->prepare($sql_update_dist);
            $stmt_update_dist->execute([
                $nama_pelanggan_baru, $id_item_baru, $id_petugas_baru, 
                $tanggal_baru, $jumlah_baru, $keterangan_baru,
                $id_distribusi
            ]);

            $pdo->commit();
            $_SESSION['success_message'] = "Data distribusi berhasil diperbarui.";
        } else {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Data distribusi tidak ditemukan.";
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Gagal memperbarui data: " . $e->getMessage();
    }

    header('Location: ' . $redirect_url);
    exit;
}
// [ AKHIR BLOK BARU ]
// [ AKHIR BLOK BARU ]
// =================================================================
// 3. LOGIKA AMBIL DATA UNTUK TAMPILAN (VIEW) + FILTER TANGGAL
// =================================================================
$items = [];
$users = [];
$rows = [];

try {
    $stmt_items = $pdo->query("SELECT id, nama_barang, stok FROM items ORDER BY nama_barang ASC");
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    $stmt_users = $pdo->query("SELECT id, username FROM users WHERE role = 'petugas' ORDER BY username ASC");
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    $search = $_GET['search'] ?? '';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';

    $date_filter = '';
    $params = [];

    if (!empty($start_date) && !empty($end_date)) {
        $date_filter = " AND d.tanggal BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    } elseif (!empty($start_date)) {
        $date_filter = " AND d.tanggal >= ?";
        $params[] = $start_date;
    } elseif (!empty($end_date)) {
        $date_filter = " AND d.tanggal <= ?";
        $params[] = $end_date;
    }

    // BARANG KELUAR
    if (isset($page_type) && $page_type == 'keluar') {
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
                    WHERE d.tipe = 'keluar' $date_filter";

        if (!empty($search)) {
            $sql_rows .= " AND (i.nama_barang LIKE ? OR u.username LIKE ? OR d.nama_pelanggan LIKE ? OR d.tanggal LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
        }

        $sql_rows .= " ORDER BY d.tanggal DESC, d.id DESC";
        $stmt_rows = $pdo->prepare($sql_rows);
        $stmt_rows->execute($params);
        $rows = $stmt_rows->fetchAll(PDO::FETCH_ASSOC);
    }

    // BARANG MASUK
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
                    WHERE d.tipe = 'masuk' $date_filter";

        if (!empty($search)) {
            $sql_rows .= " AND (i.nama_barang LIKE ? OR u.username LIKE ? OR d.nama_pelanggan LIKE ? OR d.tanggal LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
        }

        $sql_rows .= " ORDER BY d.tanggal DESC, d.id DESC";
        $stmt_rows = $pdo->prepare($sql_rows);
        $stmt_rows->execute($params);
        $rows = $stmt_rows->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}

// =================================================================
// 4. LOGIKA DOWNLOAD PDF / EXCEL (ikut filter tanggal & search)
// =================================================================
if (isset($_GET['action']) && $_GET['action'] === 'download') {
    if (ob_get_length()) ob_clean();
    $download_type = $_GET['type'] ?? '';
    $page_type = $_GET['page'] ?? 'masuk'; // 'masuk' atau 'keluar'
    $search = $_GET['search'] ?? '';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    if (isset($_GET['reset'])) {
    $search = '';
    $start_date = '';
    $end_date = '';
}

    // Tentukan tipe laporan (untuk query & judul)
    $tipe = ($page_type === 'keluar') ? 'keluar' : 'masuk';
    $judul = ($tipe === 'keluar') ? 'Laporan Barang Keluar' : 'Laporan Barang Masuk';
    $filename_base = "Laporan_Barang_" . ucfirst($tipe);

    // Siapkan filter tanggal & parameter
    $date_filter = '';
    $params = [$tipe];

    if (!empty($start_date) && !empty($end_date)) {
        $date_filter = " AND d.tanggal BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
    } elseif (!empty($start_date)) {
        $date_filter = " AND d.tanggal >= ?";
        $params[] = $start_date;
    } elseif (!empty($end_date)) {
        $date_filter = " AND d.tanggal <= ?";
        $params[] = $end_date;
    }

    // Query data sesuai tipe
    $sql = "SELECT 
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
            WHERE d.tipe = ? $date_filter";

    // Filter search
    if (!empty($search)) {
        $sql .= " AND (
                    i.nama_barang LIKE ? 
                    OR u.username LIKE ? 
                    OR d.nama_pelanggan LIKE ? 
                    OR d.tanggal LIKE ?
                )";
        $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
    }

    $sql .= " ORDER BY d.tanggal DESC, d.id DESC";

    // Eksekusi query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // =============== PDF ===============
if ($download_type === 'pdf') {
    // Cegah output lain ikut ke PDF
    error_reporting(E_ALL);
ini_set('display_errors', 1);
    ob_start();

    $dompdf = new \Dompdf\Dompdf();
    $html = "<h2 style='text-align:center;'>{$judul}</h2>";
    $html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
                <thead>
                    <tr style='background:#eee;'>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Petugas</th>
                        <th>Mitra/Pelanggan</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Stok Terkini</th>
                        <th>Keterangan</th>
                    </tr>
                </thead><tbody>";

    $no = 1;
    foreach ($data as $row) {
        $html .= "<tr>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($row['tanggal']) . "</td>
                    <td>" . htmlspecialchars($row['petugas'] ?? '-') . "</td>
                    <td>" . htmlspecialchars($row['mitra'] ?? '-') . "</td>
                    <td>" . htmlspecialchars($row['nama_barang'] ?? '-') . "</td>
                    <td>" . htmlspecialchars($row['kategori'] ?? '-') . "</td>
                    <td>" . htmlspecialchars($row['jumlah']) . "</td>
                    <td>" . htmlspecialchars($row['stok_terkini']) . "</td>
                    <td>" . htmlspecialchars($row['keterangan'] ?? '-') . "</td>
                  </tr>";
        $no++;
    }

    $html .= "</tbody></table>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Bersihkan buffer sebelum output PDF
    ob_end_clean();

    // Stream ke browser
    $dompdf->stream("{$filename_base}_" . date('Ymd_His') . ".pdf", ["Attachment" => true]);
    exit;
}

    // =============== EXCEL ===============
    if ($download_type === 'excel') {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$filename_base}_" . date('Ymd_His') . ".xls");

        echo "<table border='1'>
                <thead>
                    <tr style='background:#ddd;'>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Petugas</th>
                        <th>Mitra/Pelanggan</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Stok Terkini</th>
                        <th>Keterangan</th>
                    </tr>
                </thead><tbody>";

        $no = 1;
        foreach ($data as $row) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($row['tanggal']) . "</td>
                    <td>" . htmlspecialchars($row['petugas'] ?? '-') . "</td>
                    <td>" . htmlspecialchars($row['mitra'] ?? '-') . "</td>
                    <td>" . htmlspecialchars($row['nama_barang'] ?? '-') . "</td>
                    <td>" . htmlspecialchars($row['kategori'] ?? '-') . "</td>
                    <td>" . htmlspecialchars($row['jumlah']) . "</td>
                    <td>" . htmlspecialchars($row['stok_terkini']) . "</td>
                    <td>" . htmlspecialchars($row['keterangan'] ?? '-') . "</td>
                  </tr>";
            $no++;
        }

        echo "</tbody></table>";
        exit;
    }
}
?>
