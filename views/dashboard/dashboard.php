<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<main class="p-6 bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Dashboard Sederhana</h2>
        <nav class="mb-6">
            <ul class="flex space-x-4">
                <li><a href="#" class="text-blue-600 hover:underline">Beranda</a></li>
                <li><a href="#" class="text-blue-600 hover:underline">Profil</a></li>
                <li><a href="#" class="text-blue-600 hover:underline">Laporan</a></li>
                <li><a href="#" class="text-blue-600 hover:underline">Pengaturan</a></li>
            </ul>
        </nav>
        <section class="bg-white p-4 rounded shadow">
            <p>Ini adalah konten dashboard sederhana untuk testing dengan Tailwind CSS.</p>
        </section>
    </div>
</main>
