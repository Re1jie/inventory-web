<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Hapus semua data session
$_SESSION = [];

// Hapus cookie token
if (isset($_COOKIE['auth_token'])) {
    setcookie('auth_token', '', time() - 3600, '/');
}

session_destroy();

header('Location: ' . BASE_PATH . '/login');
exit;
?>
