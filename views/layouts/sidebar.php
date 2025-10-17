<?php
// Perbaikan: Mulai sesi jika belum ada sesi yang aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php'; 

// Dapatkan path URL saat ini untuk menentukan halaman aktif
$current_page = $_SERVER['REQUEST_URI'];
?>

<!-- Mengubah div utama menjadi flex container -->
<aside class="w-64 min-h-screen bg-gray-800 text-white fixed flex flex-col justify-between">
    
    <!-- Bagian Atas: Logo dan Menu Utama -->
    <div>
        <div class="p-6 text-2xl font-semibold border-b border-gray-700 text-center">
            My Inventory
        </div>
        <nav class="flex flex-col mt-4 space-y-1">
            
            <!-- Link Dashboard -->
            <a href="<?= BASE_PATH ?>/dashboard" class="flex items-center gap-3 px-6 py-3 rounded-lg mx-2 transition-colors <?php echo (strpos($current_page, '/dashboard') !== false) ? 'bg-indigo-600' : 'hover:bg-gray-700'; ?>">
                <!-- Ikon Dashboard -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Link Barang Masuk -->
            <a href="<?= BASE_PATH ?>/views/distribution/barang_masuk.php" class="flex items-center gap-3 px-6 py-3 rounded-lg mx-2 transition-colors <?php echo (strpos($current_page, 'barang_masuk') !== false) ? 'bg-indigo-600' : 'hover:bg-gray-700'; ?>">
                <!-- Ikon Barang Masuk -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h12" />
                </svg>
                <span>Barang Masuk</span>
            </a>

            <!-- Link Barang Keluar -->
            <a href="<?= BASE_PATH ?>/views/distribution/barang_keluar.php" class="flex items-center gap-3 px-6 py-3 rounded-lg mx-2 transition-colors <?php echo (strpos($current_page, 'barang_keluar') !== false) ? 'bg-indigo-600' : 'hover:bg-gray-700'; ?>">
                <!-- Ikon Barang Keluar BARU -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                <span>Barang Keluar</span>
            </a>

            <!-- Link Manajemen User -->
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'superadmin'): ?>
                <a href="<?= BASE_PATH ?>/user-management" class="flex items-center gap-3 px-6 py-3 rounded-lg mx-2 transition-colors <?php echo (strpos($current_page, 'user-management') !== false) ? 'bg-indigo-600' : 'hover:bg-gray-700'; ?>">
                    <!-- Ikon Manajemen User BARU -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.125-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.125-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Manajemen User</span>
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Bagian Bawah: Tombol Logout -->
    <div class="p-4 border-t border-gray-700">
        <a href="<?= BASE_PATH ?>/logout" class="flex items-center justify-center gap-3 w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition-colors">
            <!-- Ikon Logout -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Logout</span>
        </a>
    </div>
</aside>

