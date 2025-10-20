<?php
// File: controllers/OutgoingGoodsController.php
include __DIR__ . '/../config/database.php';
require __DIR__ . '/../vendor/autoload.php'; // untuk Dompdf

use Dompdf\Dompdf;

$action = $_GET['action'] ?? '';

// === Fungsi untuk ambil data dengan filter ===
function getFilteredData($conn, $filters) {
    $query = "SELECT * FROM barang_keluar WHERE 1=1";
    $params = [];
    $types = "";

    // Filter nama pelanggan
    if (!empty($filters['nama_pelanggan'])) {
        $query .= " AND nama_pelanggan LIKE ?";
        $params[] = "%" . $filters['nama_pelanggan'] . "%";
        $types .= "s";
    }

    // Filter tanggal
    if (!empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai'])) {
        $query .= " AND tanggal_keluar BETWEEN ? AND ?";
        $params[] = $filters['tanggal_mulai'];
        $params[] = $filters['tanggal_selesai'];
        $types .= "ss";
    }

    // Filter tipe
    if (!empty($filters['tipe']) && $filters['tipe'] !== 'Semua Tipe') {
        $query .= " AND tipe = ?";
        $params[] = $filters['tipe'];
        $types .= "s";
    }

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// === Action: Export PDF ===
if ($action === 'export') {
    $filters = [
        'nama_pelanggan' => $_GET['nama_pelanggan'] ?? '',
        'tanggal_mulai' => $_GET['tanggal_mulai'] ?? '',
        'tanggal_selesai' => $_GET['tanggal_selesai'] ?? '',
        'tipe' => $_GET['tipe'] ?? '',
    ];

    $rows = getFilteredData($conn, $filters);

    // Buat HTML untuk PDF
    $html = '<h2 style="text-align:center;">Laporan Barang Keluar</h2>';
    $html .= '<table border="1" cellspacing="0" cellpadding="5" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal Keluar</th>
                        <th>Nama Pelanggan</th>
                        <th>Tipe</th>
                        <th>ID Item</th>
                        <th>ID Petugas</th>
                        <th>Jumlah</th>
                        <th>Tujuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>';

    $no = 1;
    foreach ($rows as $r) {
        $html .= '<tr>
                    <td>' . $no++ . '</td>
                    <td>' . htmlspecialchars($r['tanggal_keluar']) . '</td>
                    <td>' . htmlspecialchars($r['nama_pelanggan']) . '</td>
                    <td>' . htmlspecialchars($r['tipe']) . '</td>
                    <td>' . htmlspecialchars($r['id_item']) . '</td>
                    <td>' . htmlspecialchars($r['id_petugas']) . '</td>
                    <td>' . htmlspecialchars($r['jumlah']) . '</td>
                    <td>' . htmlspecialchars($r['tujuan']) . '</td>
                    <td>' . htmlspecialchars($r['keterangan']) . '</td>
                  </tr>';
    }

    $html .= '</tbody></table>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('laporan_barang_keluar.pdf', ['Attachment' => true]);
    exit;
}

// === Default: Tampilkan View ===
$filters = [
    'nama_pelanggan' => $_GET['nama_pelanggan'] ?? '',
    'tanggal_mulai' => $_GET['tanggal_mulai'] ?? '',
    'tanggal_selesai' => $_GET['tanggal_selesai'] ?? '',
    'tipe' => $_GET['tipe'] ?? '',
];

$rows = getFilteredData($conn, $filters);
include __DIR__ . '/../views/report/OutgoingGoodsReport.php';
