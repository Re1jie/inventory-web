<?php
// Data akun
$username_super = 'superadmin';
$email_super = 'superadmin@gmail.com';
$passwordPlain_super = 'superadmin123';
$role_super = 'superadmin';

$username_admin = 'admin';
$email_admin = 'admin@gmail.com';
$passwordPlain_admin = 'admin123';
$role_admin = 'admin';

$username_petugas = 'petugas';
$email_petugas = 'petugas@gmail.com';
$passwordPlain_petugas = 'petugas123';
$role_petugas = 'petugas';

// Hash masing-masing password
$hashedPassword_super = password_hash($passwordPlain_super, PASSWORD_DEFAULT);
$hashedPassword_admin = password_hash($passwordPlain_admin, PASSWORD_DEFAULT);
$hashedPassword_petugas = password_hash($passwordPlain_petugas, PASSWORD_DEFAULT);

// Tampilkan query INSERT siap pakai
echo "INSERT INTO users (username, email, password, role) VALUES ('$username_super', '$email_super', '$hashedPassword_super', '$role_super');\n";
echo "INSERT INTO users (username, email, password, role) VALUES ('$username_admin', '$email_admin', '$hashedPassword_admin', '$role_admin');\n";
echo "INSERT INTO users (username, email, password, role) VALUES ('$username_petugas', '$email_petugas', '$hashedPassword_petugas', '$role_petugas');\n";
?>
