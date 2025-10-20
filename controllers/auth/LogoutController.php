<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

// Hapus semua data session
$_SESSION = [];

// Hapus cookie token
if (isset($_COOKIE['auth_token'])) {
    setcookie('auth_token', '', time() - 3600, '/');
}

// [ BARIS TAMBAHAN ] Start
// Hapus juga cookie session (PHPSESSID)
// Ini penting agar fitur "Remember Me" ikut ter-reset
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
// [ BARIS TAMBAHAN ] End

session_destroy();

header('Location: ' . BASE_PATH . '/login');
exit;
?>