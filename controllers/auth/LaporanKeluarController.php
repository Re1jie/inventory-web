<?php
session_start();
include __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';

class OutgoingGoodsReportController {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Ambil data laporan barang keluar
    public function index($search = '', $start_date = '', $end_date = '') {
        $where = [];

        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $where[] = "(b.nama_barang LIKE '%$search%' 
                        OR u.nama_petugas LIKE '%$search%' 
                        OR m.nama_mitra LIKE '%$search%')";
        }

        if (!empty($start_date) && !empty($end_date)) {
            $where[] = "d.tanggal BETWEEN '$start_date' AND '$end_date'";
        } elseif (!empty($start_date)) {
            $where[] = "d.tanggal >= '$start_date'";
        } elseif (!empty($end_date)) {
            $where[] = "d.tanggal <= '$end_date'";
        }

        $whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Contoh query join antar tabel (silakan sesuaikan dengan database kamu)
        $sql = "
            SELECT 
                d.tanggal,
                u.nama_petugas,
                m.nama_mitra,
                b.nama_barang,
                b.kategori,
                d.jumlah,
                b.stok AS stok_terkini,
                d.keterangan
            FROM distributions_out d
            LEFT JOIN users u ON d.id_petugas = u.id_petugas
            LEFT JOIN mitra m ON d.id_mitra = m.id_mitra
            LEFT JOIN items b ON d.id_item = b.id_item
            $whereSQL
            ORDER BY d.tanggal DESC
        ";

        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Export PDF
    public function exportPDF($rows, $filters = []) {
        $pdf = new TCPDF();
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Laporan Barang Keluar', 0, 1, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('helvetica', '', 10);
        $filterText = "Filter: ";
        $filterParts = [];

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $filterParts[] = ucfirst(str_replace('_', ' ', $key)) . ": $value";
            }
        }

        $filterText .= !empty($filterParts) ? implode(', ', $filterParts) : "Tidak ada filter.";
        $pdf->MultiCell(0, 6, $filterText, 0, 'L');
        $pdf->Ln(4);

        $pdf->SetFont('helvetica', 'B', 10);
        $headers = ['#', 'Tanggal', 'Petugas', 'Mitra', 'Nama Barang', 'Kategori', 'Jumlah', 'Stok Terkini', 'Keterangan'];
        $widths = [10, 20, 25, 25, 30, 25, 15, 20, 30];
        foreach ($headers as $i => $h) {
            $pdf->Cell($widths[$i], 7, $h, 1, 0, 'C');
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 10);
        $no = 1;
        $total = 0;
        foreach ($rows as $r) {
            $pdf->Cell(10, 7, $no++, 1);
            $pdf->Cell(20, 7, $r['tanggal'], 1);
            $pdf->Cell(25, 7, $r['nama_petugas'], 1);
            $pdf->Cell(25, 7, $r['nama_mitra'], 1);
            $pdf->Cell(30, 7, $r['nama_barang'], 1);
            $pdf->Cell(25, 7, $r['kategori'], 1);
            $pdf->Cell(15, 7, $r['jumlah'], 1, 0, 'C');
            $pdf->Cell(20, 7, $r['stok_terkini'], 1, 0, 'C');
            $pdf->Cell(30, 7, $r['keterangan'], 1);
            $pdf->Ln();
            $total += $r['jumlah'];
        }

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(135, 7, 'Total Barang Keluar', 1);
        $pdf->Cell(15, 7, $total, 1, 0, 'C');
        $pdf->Cell(70, 7, '', 1);
        $pdf->Ln();

        $pdf->Output('Laporan_Barang_Keluar.pdf', 'D');
    }
}

// ====== Eksekusi controller ======
$laporan = new OutgoingGoodsReportController($conn);

$search = $_GET['search'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Export PDF
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $rows = $laporan->index($search, $start_date, $end_date);
    $laporan->exportPDF($rows, compact('search', 'start_date', 'end_date'));
    exit;
}

// Ambil data untuk view
$rows = $laporan->index($search, $start_date, $end_date);

// Panggil view
include __DIR__ . '/../views/report/OutgoingGoodsReport.php';
