<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$username = $_POST['username'];
$password = $_POST['password'];


$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        header("Location: /dashboard");
        exit;
    }
}

echo "Login gagal. Username atau password salah.";