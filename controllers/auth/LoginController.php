<?php
// JANGAN panggil session_start() dulu
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php'; // Pastikan config.php di-load untuk BASE_PATH

$username = $_POST['username'];
$password = $_POST['password'];


$remember = isset($_POST['remember']);

// 1. Tentukan masa berlaku cookie
$token_expiry = $remember ? time() + (86400 * 30) : time() + 3600; // 86400 = 1 hari
$session_cookie_lifetime = $remember ? (86400 * 30) : 0; // 0 = sampai browser ditutup

// 2. Atur parameter cookie session SEBELUM session_start()
session_set_cookie_params($session_cookie_lifetime, '/', '', true, true);

// 3. SEKARANG baru panggil session_start()
session_start();


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
        session_regenerate_id(true);

        // Buat token unik
        $token = bin2hex(random_bytes(32)); 

        // Simpan data user dan token ke dalam session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        $_SESSION['auth_token'] = $token;

        // 5. Kirim token yang sama ke cookie pengguna
        setcookie('auth_token', $token, $token_expiry, '/', '', true, true); // secure dan httponly


        // --- [MODIFIKASI BARU] ---
        // 6. Atur cookie untuk 'remember_username'
        if ($remember) {
            // Ingat username selama 30 hari
            setcookie('remember_username', $username, time() + (86400 * 30), '/', '', true, true);
        } else {
            // Jika "remember" tidak dicentang, hapus cookie username
            setcookie('remember_username', '', time() - 3600, '/');
        }
        // --- [AKHIR MODIFIKASI BARU] ---


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