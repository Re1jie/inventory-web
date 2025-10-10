<?php
define('BASE_PATH', '/inventory-web');
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
    case '/login-post':
        require 'controllers/auth/LoginController.php';
        break;
    case '/logout':
        require 'controllers/auth/LogoutController.php';
        break;
    case '/dashboard':
        require 'middleware/auth.php';
        require 'views/dashboard/dashboard.php';
        break;
    default:
        http_response_code(404);
        echo "404 - Halaman tidak ditemukan";
        break;
}