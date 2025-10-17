<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';

// Fungsi untuk update status
function updateUserStatus($userId, $status) {
    global $conn;
    $query = "UPDATE users SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $userId);
    return $stmt->execute();
}

// FUNGSI BARU: untuk update role
function updateUserRole($userId, $role) {
    global $conn;
    $query = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $role, $userId);
    return $stmt->execute();
}

// Logika untuk menangani aksi dari form (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_role') {
        $userId = (int)$_POST['user_id'];
        $newRole = $_POST['role'];

        // Validasi role
        if (in_array($newRole, ['petugas', 'admin', 'superadmin'])) {
            if (updateUserRole($userId, $newRole)) {
                $_SESSION['message'] = "Role user berhasil diperbarui.";
            } else {
                $_SESSION['message'] = "Gagal memperbarui role user.";
            }
        } else {
            $_SESSION['message'] = "Role tidak valid.";
        }
    }
    header('Location: ' . BASE_PATH . '/user-management');
    exit;
}


// Logika untuk menangani aksi dari link (GET request)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $userId = (int)$_GET['id'];

    if ($action === 'approve') {
        if (updateUserStatus($userId, 'approved')) {
            $_SESSION['message'] = "User berhasil di-approve.";
        } else {
            $_SESSION['message'] = "Gagal meng-approve user.";
        }
    } elseif ($action === 'reject') {
        if (updateUserStatus($userId, 'rejected')) {
            $_SESSION['message'] = "User berhasil di-reject.";
        } else {
            $_SESSION['message'] = "Gagal me-reject user.";
        }
    }
    
    // Redirect kembali ke halaman manajemen user untuk refresh data
    header('Location: ' . BASE_PATH . '/user-management');
    exit;
}

// Logika untuk menampilkan daftar user (default)
$users = queryData("SELECT id, username, email, role, status FROM users");

// Memuat view
require __DIR__ . '/../../views/management/UserManagement.php';
?>