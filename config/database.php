<?php

// koneksi ke database
$host = 'localhost';
$username = 'root';
$password = '';
$db = 'inventory';


// === KONEKSI MYSQLI (LAMA) ===
// Digunakan oleh DashboardController, LoginController, UserManagementController
$conn = mysqli_connect($host, $username, $password, $db);

if ($conn->connect_error) {
    die("Connection failed (MySQLi): " . $conn->connect_error);
}


// === FUNGSI KONEKSI PDO (BARU) ===
// Digunakan oleh models/Barang.php dan models/DistributionModel.php
function getConnection() {
    // Ambil variabel koneksi dari scope global
    global $host, $db, $username, $password; 
    
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed (PDO): " . $e->getMessage());
    }
}


// === FUNGSI QUERYDATA (LAMA) ===
// Menggunakan koneksi $conn (mysqli)
function queryData($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
?>