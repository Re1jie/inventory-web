<?php
// File: controllers/OutgoingGoodsController.php
include __DIR__ . '/../config/database.php'; // koneksi ke MySQL
require __DIR__ . '/../vendor/autoload.php'; // jika pakai library PDF (misal Dompdf)

use Dompdf\Dompdf;

$action = $_GET['action'] ?? '';

switch ($action) {

    // Export PDF
    case 'export':
        $search = $_GET['search'] ?? '';
        $like = "%$search%";

        $stmt = $conn->prepare("SELECT * FROM barang_keluar 
                                WHERE nama_pelanggan LIKE ? 
                                OR tipe LIKE ? 
                                OR tanggal_keluar LIKE ?");
        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        // Buat HTML untuk PDF
        $html = '<h2>Laporan Barang Keluar</h2>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
        $html .= '<tr>
                    <th>#</th>
                    <th>Tanggal Keluar</th>
                    <th>Nama Pelanggan</th>
                    <th>Tipe</th>
                    <th>ID Item</th>
                    <th>ID Petugas</th>
                    <th>Jumlah</th>
                    <th>Tujuan</th>
                    <th>Keterangan</th>
                  </tr>';

        $no = 1;
        foreach ($rows as $r) {
            $html .= '<tr>
                        <td>'.$no++.'</td>
                        <td>'.$r['tanggal_keluar'].'</td>
                        <td>'.$r['nama_pelanggan'].'</td>
                        <td>'.$r['tipe'].'</td>
                        <td>'.$r['id_item'].'</td>
                        <td>'.$r['id_petugas'].'</td>
                        <td>'.$r['jumlah'].'</td>
                        <td>'.$r['tujuan'].'</td>
                        <td>'.$r['keterangan'].'</td>
                      </tr>';
        }
        $html .= '</table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan_barang_keluar.pdf', ['Attachment' => true]);
        exit;
        break;

    // Default: tampil view
    default:
        $search = $_GET['search'] ?? '';
        $like = "%$search%";

        $stmt = $conn->prepare("SELECT * FROM barang_keluar 
                                WHERE nama_pelanggan LIKE ? 
                                OR tipe LIKE ? 
                                OR tanggal_keluar LIKE ?");
        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        include __DIR__ . '/../views/report/OutgoingGoodsReport.php';
        break;
}
