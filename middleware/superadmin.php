<?php
// Pastikan sesi sudah dimulai di file yang memanggil middleware ini
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    http_response_code(403);
    // Redirect atau tampilkan pesan error
    echo "<h1>403 Forbidden</h1>";
    echo "Anda tidak memiliki hak akses ke halaman ini.";
    exit;
}
?>