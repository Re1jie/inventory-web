<?php
require_once 'models/Barang.php';

$barangModel = new Barang();

switch ($uri) {
    case '/barang':
        $barangs = $barangModel->getAll();
        require 'views/barang/index.php';
        break;

    case '/barang/tambah':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $barangModel->create($_POST);
            header('Location: ' . BASE_PATH . '/barang');
            exit();
        } else {
            require 'views/barang/form.php';
        }
        break;

    case '/barang/edit':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_PATH . '/barang');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $barangModel->update($id, $_POST);
            header('Location: ' . BASE_PATH . '/barang');
            exit();
        } else {
            $barang = $barangModel->find($id);
            require 'views/barang/form.php';
        }
        break;

    case '/barang/hapus':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $barangModel->delete($id);
        }
        header('Location: ' . BASE_PATH . '/barang');
        exit();
        break;
}
