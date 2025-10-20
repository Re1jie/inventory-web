<?php
session_start();
include __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';

class LaporanController {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index($search = '', $start_date = '', $end_date = '', $tipe = '') {
        $where = [];

        if (!empty($search)) {
            $search = mysqli_real_escape_string($this->conn, $search);
            $where[] = "(nama_pelanggan LIKE '%$search%' OR tipe LIKE '%$search%' OR tanggal LIKE '%$search%')";
        }

        if (!empty($start_date) && !empty($end_date)) {
            $where[] = "tanggal BETWEEN '$start_date' AND '$end_date'";
        } elseif (!empty($start_date)) {
            $where[] = "tanggal >= '$start_date'";
        } elseif (!empty($end_date)) {
            $where[] = "tanggal <= '$end_date'";
        }

        if (!empty($tipe)) {
            $where[] = "tipe = '$tipe'";
        }

        $whereSQL = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT id, nama_pelanggan, tipe, id_item, id_petugas, tanggal, jumlah, keterangan
                FROM distributions
                $whereSQL
                ORDER BY tanggal DESC";

        $result = mysqli_query($this->conn, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $rows;
    }

    public function exportPDF($rows, $filters = []) {
        $pdf = new TCPDF();
        $pdf->AddPage();

        // Judul
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Laporan Barang Masuk', 0, 1, 'C');
        $pdf->Ln(4);

        // Tampilkan filter aktif
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

        // Table Header
        $pdf->SetFont('helvetica', 'B', 10);
        $headers = ['#', 'Tanggal', 'Nama Pelanggan', 'Tipe', 'ID Item', 'ID Petugas', 'Jumlah', 'Keterangan'];
        $widths = [10, 25, 35, 20, 15, 20, 15, 40];
        foreach ($headers as $i => $h) {
            $pdf->Cell($widths[$i], 7, $h, 1, 0, 'C');
        }
        $pdf->Ln();

        // Table Body
        $pdf->SetFont('helvetica', '', 10);
        $no = 1; $total = 0;
        foreach ($rows as $r) {
            $pdf->Cell(10, 7, $no++, 1);
            $pdf->Cell(25, 7, $r['tanggal'], 1);
            $pdf->Cell(35, 7, $r['nama_pelanggan'], 1);
            $pdf->Cell(20, 7, $r['tipe'], 1);
            $pdf->Cell(15, 7, $r['id_item'], 1, 0, 'C');
            $pdf->Cell(20, 7, $r['id_petugas'], 1, 0, 'C');
            $pdf->Cell(15, 7, $r['jumlah'], 1, 0, 'C');
            $pdf->Cell(40, 7, $r['keterangan'], 1);
            $pdf->Ln();
            $total += $r['jumlah'];
        }

        // Total
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(125, 7, 'Total Barang Masuk', 1);
        $pdf->Cell(15, 7, $total, 1, 0, 'C');
        $pdf->Cell(40, 7, '', 1);
        $pdf->Ln();

        $pdf->Output('Laporan_Barang_Masuk.pdf', 'D');
    }
}

// Controller instance
$laporan = new LaporanController($conn);

$search = $_GET['search'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$tipe = $_GET['tipe'] ?? '';

// Handle PDF export
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $rows = $laporan->index($search, $start_date, $end_date, $tipe);
    $laporan->exportPDF($rows, compact('search', 'start_date', 'end_date', 'tipe'));
    exit;
}

// Ambil data untuk view
$rows = $laporan->index($search, $start_date, $end_date, $tipe);
