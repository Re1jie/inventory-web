<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<main class="ml-64 p-6 bg-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Tambah Barang</h2>

        <form action="<?= BASE_PATH ?>/barang/simpan" method="POST" enctype="multipart/form-data" class="space-y-5">

            <!-- Nama Barang -->
            <div>
                <label class="block text-gray-700 mb-1">Nama Barang</label>
                <input type="text" name="nama" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-gray-700 mb-1">Kategori</label>
                <input type="text" name="kategori" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Stok dan Satuan -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-1">Stok</label>
                    <input type="number" name="stok" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- Upload Gambar -->
            <div>
                <label class="block text-gray-700 mb-2">Gambar Barang</label>
                <div class="flex items-center gap-4">
                    <button type="button" id="uploadBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Pilih Gambar</button>
                    <input type="file" id="gambarInput" name="gambar" accept="image/*" class="hidden">
                    <span id="fileName" class="text-gray-500 text-sm"></span>
                </div>

                <div class="mt-4">
                    <img id="previewImage" src="https://via.placeholder.com/150?text=Preview" alt="Preview Gambar" class="w-48 h-48 object-cover rounded-lg border border-gray-300 hidden">
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">Simpan Barang</button>
            </div>
        </form>
    </div>
</main>

<script>
    const uploadBtn = document.getElementById('uploadBtn');
    const gambarInput = document.getElementById('gambarInput');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');

    uploadBtn.addEventListener('click', () => gambarInput.click());

    gambarInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            fileName.textContent = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewImage.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            fileName.textContent = '';
            previewImage.src = '';
            previewImage.classList.add('hidden');
        }
    });
</script>
