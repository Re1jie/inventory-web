<?php
$basePath = '/inventory-web';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = substr($uri, strlen($basePath));

if ($uri === '' || $uri === '/index.php') {
    $uri = '/';
}

switch ($uri) {
    case '/':
        // PERBAIKAN: Tambahkan $basePath sebelum /login
        header('Location: ' . $basePath . '/login');
        exit(); // Tambahkan exit() untuk menghentikan eksekusi script
        break;
    case '/login':
        require 'views/login/LoginPage.php';
        break;
    case '/login-post':
        require 'controllers/auth/login.php';
        break;
    case '/logout':
        require 'controllers/auth/logout.php';
        break;
    case '/dashboard':
        require 'middleware/auth.php';
        require 'views/dashboard/index.php';
        break;
    default:
        http_response_code(404);
        echo "404 - Halaman tidak ditemukan";
        break;
}