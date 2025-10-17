<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = substr($uri, strlen(BASE_PATH));

if ($uri === '' || $uri === '/index.php') {
    $uri = '/';
}

switch ($uri) {
    case '/':
        header('Location: ' . BASE_PATH . '/login');
        exit();
        break;
    case '/login':
        require 'views/login/LoginPage.php';
        break;
    case '/register':
        require 'views/register/RegisterPage.php';
        break;    
    case '/login-post':
        require 'controllers/auth/LoginController.php';
        break;
    case '/register-post':
        require 'controllers/auth/RegisterController.php';
        break;
    case '/logout':
        require 'controllers/auth/LogoutController.php';
        break;
    case '/dashboard':
        require 'middleware/auth.php';
        require 'controllers/DashboardController.php';
        break;

    case '/user-management':
        require 'middleware/auth.php'; // Pastikan login
        require 'middleware/superadmin.php'; // Pastikan superadmin
        require 'controllers/management/UserManagementController.php';
        break;
    case '/barang':
    case '/barang/tambah':
    case '/barang/edit':
    case '/barang/hapus':
        require 'middleware/auth.php';
        require 'controllers/ManajemenBarangController.php';
        break;

    // Routing laporan (dipertahankan)
    case '/laporan/barang-masuk':
        require 'middleware/auth.php';
        require 'views/report/IncomingGoodsReport.php';
        break;
    case '/laporan/barang-keluar':
        require 'middleware/auth.php';
        require 'views/report/OutgoingGoodsReport.php';
        break;

    default:
        http_response_code(404);
        echo "404 - Halaman tidak ditemukan";
        break;
}
