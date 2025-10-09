<?php
$basePath = '/inventory-web';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = substr($uri, strlen($basePath));

if ($uri === '' || $uri === '/index.php') {
    $uri = '/';
}

switch ($uri) {
    case '/':
        header('Location: ' . $basePath . '/login');
        exit();
        break;
    case '/login':
        require 'views/login/LoginPage.php';
        break;
    case '/login-post':
        require 'controllers/auth/LoginController.php';
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