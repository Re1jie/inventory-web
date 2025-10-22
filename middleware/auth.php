<?php
session_start();
require_once __DIR__ . '/../config/config.php'; // Load BASE_PATH

// Periksa apakah user sudah login DAN token-nya valid
if (!isset($_SESSION['user']) || !isset($_SESSION['auth_token']) || !isset($_COOKIE['auth_token'])) {
    // Jika salah satu tidak ada, paksa logout
    logout_and_redirect();
}

// Bandingkan token di session dengan token di cookie
if ($_SESSION['auth_token'] !== $_COOKIE['auth_token']) {
    // Jika token tidak cocok, sesi tidak valid
    logout_and_redirect();
}

// Fungsi untuk membersihkan session dan redirect ke login
function logout_and_redirect() {    
    // Hapus semua data session
    $_SESSION = [];
    
    // Hapus cookie
    if (isset($_COOKIE['auth_token'])) {
        setcookie('auth_token', '', time() - 3600, '/'); // Set waktu kedaluwarsa di masa lalu
    }
    
    session_destroy();
    
    header('Location: ' . BASE_PATH . '/login?reason=timeout');
    exit;
}
?>
