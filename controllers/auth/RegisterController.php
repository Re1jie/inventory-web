<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validasi sederhana
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Semua field wajib diisi.";
        header('Location: ' . BASE_PATH . '/register');
        exit;
    }

    // Cek apakah username atau email sudah ada
    $query_check = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $_SESSION['error'] = "Username atau email sudah terdaftar.";
        header('Location: ' . BASE_PATH . '/register');
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Role default adalah 'petugas' dan status default adalah 'pending'
    $role = 'petugas';

    $query_insert = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($query_insert);
    $stmt_insert->bind_param("ssss", $username, $email, $hashedPassword, $role);

    if ($stmt_insert->execute()) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan tunggu persetujuan dari Superadmin.";
    } else {
        $_SESSION['error'] = "Registrasi gagal, terjadi kesalahan.";
    }

    header('Location: ' . BASE_PATH . '/register');
    exit;
}
?>