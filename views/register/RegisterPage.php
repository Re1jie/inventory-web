<?php 
include __DIR__ . '/../layouts/header.php';
session_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100">
  <div class="flex flex-col md:flex-row bg-white rounded-3xl shadow-2xl overflow-hidden w-11/12 md:w-4/5 lg:w-3/4">

    <!-- Kiri: Informasi & Ilustrasi -->
    <div class="bg-indigo-500 text-white md:w-1/2 flex flex-col justify-center items-center p-10 space-y-6">
      <div class="text-left w-full">
        <h1 class="text-3xl font-bold leading-snug">
          Kelola barang, <br> Pantau stok, <br> Tingkatkan efisiensi.
        </h1>
      </div>
      <div class="flex justify-center">
        <img src="<?= BASE_PATH; ?>/assets/images/kardusataulogo.png" alt="Gambar Kardus atau Logo" class="w-60">
      </div>
      <p class="text-sm opacity-80">Daftarkan akun Anda dan mulai kelola inventori dengan mudah.</p>
    </div>

    <!-- Kanan: Form Register -->
    <div class="md:w-1/2 bg-white px-10 py-12 flex flex-col justify-center">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Buat Akun Baru</h2>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
          <span class="block sm:inline"><?= $_SESSION['error']; ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
          <span class="block sm:inline"><?= $_SESSION['success']; ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <form id="registerForm" action="<?= BASE_PATH; ?>/register-post" method="post" class="space-y-4">
        <!-- Username -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2">
            <i class="fa fa-user text-gray-500"></i>
            <span class="mx-2 h-5 border-l border-gray-300"></span>
            <input type="text" name="username" placeholder="Masukkan username"
                   class="flex-1 focus:outline-none" required>
          </div>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2">
            <i class="fa fa-envelope text-gray-500"></i>
            <span class="mx-2 h-5 border-l border-gray-300"></span>
            <input type="email" name="email" placeholder="Masukkan email"
                   class="flex-1 focus:outline-none" required>
          </div>
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2 relative">
            <i class="fa fa-lock text-gray-500"></i>
            <span class="mx-2 h-5 border-l border-gray-300"></span>
            <input id="password" type="password" name="password" placeholder="Masukkan password"
                   class="flex-1 focus:outline-none" required>
            <button type="button" id="togglePwd" class="absolute right-3 text-gray-500 focus:outline-none">
              <i class="fa fa-eye"></i>
            </button>
          </div>
        </div>

        <!-- Konfirmasi Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
          <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2 relative">
            <i class="fa fa-lock text-gray-500"></i>
            <span class="mx-2 h-5 border-l border-gray-300"></span>
            <input id="confirmPassword" type="password" name="confirm_password" placeholder="Ulangi password"
                   class="flex-1 focus:outline-none" required>
            <button type="button" id="toggleConfirmPwd" class="absolute right-3 text-gray-500 focus:outline-none">
              <i class="fa fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition font-medium shadow">
          Daftar Sekarang
        </button>
      </form>

      <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
          Sudah punya akun? 
          <a href="<?= BASE_PATH; ?>/login" class="text-indigo-600 hover:underline">Login di sini</a>
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Script Toggle Password + Verifikasi -->
<script>
  (function(){
    const pwd = document.getElementById('password');
    const confirmPwd = document.getElementById('confirmPassword');
    const togglePwd = document.getElementById('togglePwd');
    const toggleConfirmPwd = document.getElementById('toggleConfirmPwd');

    // fungsi toggle
    function setupToggle(button, input) {
      const icon = button.querySelector('i');
      button.addEventListener('click', function(){
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
      });
    }

    setupToggle(togglePwd, pwd);
    setupToggle(toggleConfirmPwd, confirmPwd);

    // validasi password
    document.getElementById('registerForm').addEventListener('submit', function(e){
      if(pwd.value !== confirmPwd.value){
        e.preventDefault();
        alert('Password tidak cocok!');
      }
    });
  })();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
