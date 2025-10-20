<?php
session_start();
include __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';

class LaporanController {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index($search = '') {
        // Search bisa berdasarkan nama_pelanggan, tipe, atau tanggal
        $searchQuery = $search ? "WHERE nama_pelanggan LIKE '%$search%' OR tipe LIKE '%$search%' OR tanggal LIKE '%$search%'" : "";

        $sql = "SELECT id, nama_pelanggan, tipe, id_item, id_petugas, tanggal, jumlah, keterangan
                FROM distributions
                $searchQuery
                ORDER BY tanggal DESC";

        $result = mysqli_query($this->conn, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $rows;
    }

    public function exportPDF($rows) {
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Laporan Barang Masuk', 0, 1, 'C');
        $pdf->Ln(5);

        // Table Header
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(10, 7, '#', 1);
        $pdf->Cell(30, 7, 'Tanggal', 1);
        $pdf->Cell(35, 7, 'Nama Pelanggan', 1);
        $pdf->Cell(25, 7, 'Tipe', 1);
        $pdf->Cell(15, 7, 'ID Item', 1, 0, 'C');
        $pdf->Cell(20, 7, 'ID Petugas', 1, 0, 'C');
        $pdf->Cell(15, 7, 'Jumlah', 1, 0, 'C');
        $pdf->Cell(40, 7, 'Keterangan', 1);
        $pdf->Ln();

        // Table Body
        $pdf->SetFont('helvetica', '', 10);
        $no = 1;
        foreach ($rows as $r) {
            $pdf->Cell(10, 7, $no++, 1);
            $pdf->Cell(30, 7, $r['tanggal'], 1);
            $pdf->Cell(35, 7, $r['nama_pelanggan'], 1);
            $pdf->Cell(25, 7, $r['tipe'], 1);
            $pdf->Cell(15, 7, $r['id_item'], 1, 0, 'C');
            $pdf->Cell(20, 7, $r['id_petugas'], 1, 0, 'C');
            $pdf->Cell(15, 7, $r['jumlah'], 1, 0, 'C');
            $pdf->Cell(40, 7, $r['keterangan'], 1);
            $pdf->Ln();
        }

        $pdf->Output('Laporan_Barang_Masuk.pdf', 'D');
    }
}

// Controller instance
$laporan = new LaporanController($conn);

// Handle PDF export
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $rows = $laporan->index($_GET['search'] ?? '');
    $laporan->exportPDF($rows);
    exit;
}

// Ambil data untuk view
$rows = $laporan->index($_GET['search'] ?? '');
