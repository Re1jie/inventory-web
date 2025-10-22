<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php'; // Pastikan config.php di-load untuk BASE_PATH

$username = $_POST['username'];
$password = $_POST['password'];


$remember = isset($_POST['remember']);

$token_expiry = $remember ? time() + (86400 * 30) : time() + 3600; // 86400 = 1 hari

$session_cookie_lifetime = $remember ? (86400 * 30) : 0;

session_set_cookie_params($session_cookie_lifetime, '/', '', true, true);


$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if ($user['status'] !== 'approved') {
        $_SESSION['error'] = "Akun Anda belum aktif atau ditolak. Hubungi admin.";
        header('Location: ' . BASE_PATH . '/login');
        exit;
    }
    
    if (password_verify($password, $user['password'])) {
        // Regenerasi session ID untuk keamanan
        // Ini akan menggunakan pengaturan cookie_params yang baru saja kita atur
        session_regenerate_id(true);

        // Buat token unik
        $token = bin2hex(random_bytes(32)); // Contoh token: LqgW9gj4n2HAH5uqUrzT-A2YY6YYKhr1AEyOFikufuY

        // Simpan data user dan token ke dalam session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'], // [DITAMBAHKAN] Menyimpan email ke session
            'role' => $user['role']
        ];
        $_SESSION['auth_token'] = $token;

        // 5. Kirim token yang sama ke cookie pengguna DENGAN MASA BERLAKU BARU
        // Cookie berlaku selama $token_expiry (1 jam atau 30 hari)
        setcookie('auth_token', $token, $token_expiry, '/', '', true, true); // secure dan httponly

        // Arahkan ke dashboard
        header('Location: ' . BASE_PATH . '/dashboard');
        exit;
    }
}

// Jika login gagal
$_SESSION['error'] = "Login gagal. Username atau password salah.";
header('Location: ' . BASE_PATH . '/login');
exit;
?>