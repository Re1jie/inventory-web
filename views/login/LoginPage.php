<?php
include __DIR__ . '/../layouts/header.php'; 
// [ TAMBAHAN ] Memulai session untuk membaca notifikasi error
session_start(); 

// --- [MODIFIKASI BARU] ---
// Baca cookie 'remember_username' yang mungkin sudah kita atur
$remembered_user = $_COOKIE['remember_username'] ?? '';
// --- [AKHIR MODIFIKASI BARU] ---
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100">
  <div class="flex flex-col md:flex-row bg-white rounded-3xl shadow-2xl overflow-hidden w-11/12 md:w-4/5 lg:w-3/4">
    
    <div class="bg-[#FFC0CB] text-[#631A13] md:w-1/2 flex flex-col justify-center items-center p-10 space-y-6">
      <div class="text-left w-full">
        <h1 class="text-3xl font-bold leading-snug">
          Kelola barang, <br> Lapor barang, <br> Semua jadi satu.
        </h1>
      </div>
      <div class="flex justify-center">
        <img src="<?php echo BASE_PATH; ?>/assets/images/catabar.png" alt="Gambar Kardus atau Logo" class="w-120">
      </div>
      <p class="text-sm opacity-80">Mulai perjalanan efisiensi inventori Anda hari ini.</p>
    </div>

    <div class="md:w-1/2 bg-white px-10 py-12 flex flex-col justify-center">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Login untuk Memulai</h2>

      <?php 
      // Cek notifikasi session timeout (dari query parameter)
      if (isset($_GET['reason']) && $_GET['reason'] === 'timeout'): 
      ?>
          <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
              <span class="block sm:inline">Sesi Anda telah berakhir. Silakan login kembali.</span>
          </div>
      <?php 
      endif; 
  
      // Cek notifikasi error login gagal (dari session)
      if (isset($_SESSION['error'])): 
      ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
              <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']); ?></span>
          </div>
          <?php unset($_SESSION['error']); // Hapus notifikasi setelah ditampilkan ?>
      <?php endif; ?>
      <form action="<?php echo BASE_PATH; ?>/login-post" method="post" class="space-y-4">
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-indigo-400">
            <i class="fa fa-user text-gray-400 mr-2"></i>
            <div class="border-l border-gray-300 h-5 mr-2"></div>
            
            <input type="text" name="username" placeholder="Masukkan username"
                   value="<?= htmlspecialchars($remembered_user); ?>"
                   class="w-full focus:outline-none text-gray-700" required>
                   
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-indigo-400">
            <i class="fa fa-lock text-gray-400 mr-2"></i>
            <div class="border-l border-gray-300 h-5 mr-2"></div>
            <input id="password" type="password" name="password" placeholder="Masukkan password"
                   class="w-full focus:outline-none text-gray-700" required>
            <button id="togglePwd" type="button"
                     class="ml-2 text-gray-500 hover:text-gray-700 focus:outline-none"
                     aria-label="Show password" title="Show password" aria-pressed="false">
              <i class="fa fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between text-sm text-gray-600">
          <label class="flex items-center space-x-2">
          
            <input type="checkbox" name="remember" class="rounded border-gray-300"
                   <?= !empty($remembered_user) ? 'checked' : ''; ?>>
                   
            <span>Remember me</span>
          </label>
          </div>

        <button id="loginBtn" type="submit" 
        class="w-full bg-[#F1C045] text-white py-2 rounded-lg hover:bg-indigo-700 transition font-medium shadow flex justify-center items-center gap-2">
          <span id="loginText">Login</span>
          <span id="loginSpinner" class="hidden">
            <i class="fa fa-spinner fa-spin"></i>
          </span>
        </button>

      </form>

      <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
          Belum punya akun? 
          <a href="<?php echo BASE_PATH; ?>/register" class="text-indigo-600 hover:underline">Daftar di sini</a>
        </p>
      </div>
    </div>
  </div>
</div>

<script>
  (function(){
    const pwdInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePwd');
    const icon = toggleBtn.querySelector('i');

    toggleBtn.addEventListener('click', function(){
      const isHidden = pwdInput.type === 'password';
      if(isHidden){
        pwdInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        toggleBtn.setAttribute('aria-label', 'Hide password');
        toggleBtn.title = 'Hide password';
        toggleBtn.setAttribute('aria-pressed', 'true');
      } else {
        pwdInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        toggleBtn.setAttribute('aria-label', 'Show password');
        toggleBtn.title = 'Show password';
        toggleBtn.setAttribute('aria-pressed', 'false');
      }
    });
    // loading animasi saat submit
    const form = document.querySelector('form');
    const loginBtn = document.getElementById('loginBtn');
    const loginText = document.getElementById('loginText');
    const loginSpinner = document.getElementById('loginSpinner');

    form.addEventListener('submit', function() {
      loginBtn.disabled = true;
      loginText.classList.add('hidden');
      loginSpinner.classList.remove('hidden');
    });

  })();
</script>

<?php
include __DIR__ . '/../layouts/footer.php'; 
?>