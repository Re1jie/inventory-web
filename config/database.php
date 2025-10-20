<?php

// ===============================================================
// KONFIGURASI DATABASE
// ===============================================================
$host     = 'localhost';
$db       = 'inventory'; // Nama database
$username = 'root';
$password = '';
$charset  = 'utf8mb4';


// ===============================================================
// 1. KONEKSI PDO (UNTUK KODE BARU seperti DistributionController)
// ===============================================================
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Variabel $pdo digunakan oleh controller atau model baru
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Connection failed (PDO): " . $e->getMessage());
}


// ===============================================================
// 2. KONEKSI MYSQLi (UNTUK KODE LAMA seperti dashboard.php)
// ===============================================================
$conn = mysqli_connect($host, $username, $password, $db);

// Cek koneksi MySQLi
if ($conn->connect_error) {
    die("Connection failed (MySQLi): " . $conn->connect_error);
}

// Set charset MySQLi
mysqli_set_charset($conn, "utf8mb4");


// ===============================================================
// 3. FUNGSI KONEKSI (MENGGUNAKAN PDO) UNTUK MODEL BARU
// ===============================================================
function getConnection()
{
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


// ===============================================================
// 4. FUNGSI QUERY DATA (MENGGUNAKAN MYSQLi) UNTUK KODE LAMA
// ===============================================================
function queryData($query)
{
    global $conn; // menggunakan koneksi MySQLi
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query Error (MySQLi): " . mysqli_error($conn) . " | Query: " . $query);
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

?>
