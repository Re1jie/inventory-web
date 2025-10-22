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

    try {
        $pdo->beginTransaction();

        $stmt_check = $pdo->prepare("SELECT stok FROM items WHERE id = ? FOR UPDATE");
        $stmt_check->execute([$id_item]);
        $current_stok = $stmt_check->fetchColumn();

        if ($jumlah > $current_stok) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Gagal. Stok barang tidak mencukupi (Stok saat ini: $current_stok).";
            header('Location: ' . $_SERVER['PHP_SELF']);
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
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
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
