<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';

// Menentukan apakah ini form edit atau tambah
$isEdit = isset($barang) && $barang;
$formAction = $isEdit ? (BASE_PATH . '/barang/edit?id=' . $barang['id']) : (BASE_PATH . '/barang/tambah');
$pageTitle = $isEdit ? 'Edit Barang' : 'Tambah Barang Baru';

// Tentukan path gambar yang ada (jika ada)
$existingImage = ($isEdit && !empty($barang['gambar'])) ? (BASE_PATH . $barang['gambar']) : '';
?>

<div class="ml-64 p-6 min-h-screen bg-gray-100">
    <div class="bg-white shadow-lg rounded-2xl p-6 max-w-2xl mx-auto">
        <h2 class="text-2xl font-semibold mb-4"><?= $pageTitle ?></h2>

        <?php if (isset($_SESSION['form_errors'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul>
                    <?php foreach ($_SESSION['form_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['form_errors']); ?>
        <?php endif; ?>

        <form action="<?= $formAction ?>" method="POST" enctype="multipart/form-data">
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Gambar Barang (Opsional)</label>
                <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 transition">
                    <input type="file" name="gambar" id="fileInput" class="hidden" accept="image/jpeg, image/png, image/gif">
                    
                    <img id="imagePreview" src="<?= $existingImage ?>" alt="Preview Gambar" class="max-h-40 mx-auto mb-4 <?= $existingImage ? '' : 'hidden' ?>">
                    
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                    <p id="dropText" class="mt-2 text-sm text-gray-600">
                        Seret & lepas file di sini, atau <span class="text-blue-600 font-semibold">klik untuk memilih file</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Maks 2MB (JPG, PNG, GIF)</p>
                </div>
            </div>

            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium">Nama Barang</label>
                <input type="text" id="nama" name="nama" 
                       value="<?= htmlspecialchars($barang['nama'] ?? '') ?>" 
                       class="mt-1 border p-2 rounded w-full" required>
            </div>
            <div class="mb-4">
                <label for="kategori" class="block text-sm font-medium">Kategori (Jenis)</label>
                <select id="kategori" name="kategori" class="mt-1 border p-2 rounded w-full" required>
                    <option value="">-- Pilih Kategori --</option>
                    
                    <?php 
                    // Pastikan variabel $categories ada
                    if (isset($categories)): 
                        // Loop data kategori dari controller
                        foreach ($categories as $cat): 
                            // Cek apakah ini mode edit dan kategori barang sama dengan kategori di loop
                            $isSelected = ($isEdit && isset($barang['kategori']) && $barang['kategori'] == $cat['nama_kategori']);
                    ?>
                            <option value="<?= htmlspecialchars($cat['nama_kategori']) ?>"
                                <?= $isSelected ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($cat['nama_kategori'])) ?>
                            </option>
                        <?php 
                        endforeach; 
                    endif; 
                    ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="stok" class="block text-sm font-medium">Jumlah (Stok)</label>
                    <input type="number" id="stok" name="stok" 
                           value="<?= htmlspecialchars($barang['stok'] ?? 0) ?>" 
                           class="mt-1 border p-2 rounded w-full" required>
                </div>
                <div>
                    <label for="satuan" class="block text-sm font-medium">Satuan (cth: pcs, unit)</label>
                    <input type="text" id="satuan" name="satuan" 
                           value="<?= htmlspecialchars($barang['satuan'] ?? '') ?>" 
                           class="mt-1 border p-2 rounded w-full" required>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <a href="<?= BASE_PATH ?>/barang" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const imagePreview = document.getElementById('imagePreview');
    const dropText = document.getElementById('dropText');
    const existingImageSrc = '<?= $existingImage ?>';

    // Klik drop zone memicu file input
    dropZone.addEventListener('click', (e) => {
        // Hindari memicu klik jika gambar preview diklik
        if(e.target.id !== 'imagePreview') {
            fileInput.click();
        }
    });

    // Event saat file dipilih lewat dialog
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFiles(fileInput.files);
        }
    });

    // Event Drag & Drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            // [PENTING] Masukkan file yang di-drop ke file input
            fileInput.files = files; 
            handleFiles(files);
        }
    });

    // Fungsi untuk menampilkan preview
    function handleFiles(files) {
        const file = files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                dropText.textContent = `File dipilih: ${file.name}`;
            };
            
            reader.readAsDataURL(file);
        } else if (existingImageSrc) {
            // Jika file tidak valid, kembalikan ke gambar awal (jika ada)
            imagePreview.src = existingImageSrc;
            imagePreview.classList.remove('hidden');
            dropText.textContent = 'Seret & lepas file di sini, atau klik untuk memilih file';
        } else {
            // Jika tidak valid dan tidak ada gambar awal
            imagePreview.src = '';
            imagePreview.classList.add('hidden');
            dropText.textContent = 'File tidak valid. Seret & lepas file di sini, atau klik untuk memilih file';
        }
    }
</script>