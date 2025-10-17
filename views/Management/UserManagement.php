<?php
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../layouts/sidebar.php';
?>

<div class="ml-64 p-6"> <div class="bg-white shadow-lg rounded-2xl p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Manajemen User</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-4 p-3 rounded <?= strpos($_SESSION['message'], 'berhasil') !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-md">
                <thead class="bg-gray-100">
                    <tr class="text-left text-gray-700">
                        <th class="px-4 py-2 border">Username</th>
                        <th class="px-4 py-2 border">Email</th>
                        <th class="px-4 py-2 border">Role</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($user['username']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                                <td class="px-4 py-2 border">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        <?= $user['status'] == 'approved' ? 'bg-green-200 text-green-800' : '' ?>
                                        <?= $user['status'] == 'pending' ? 'bg-yellow-200 text-yellow-800' : '' ?>
                                        <?= $user['status'] == 'rejected' ? 'bg-red-200 text-red-800' : '' ?>
                                    ">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 border space-x-2">
                                    <?php if ($user['status'] == 'pending'): ?>
                                        <a href="<?= BASE_PATH ?>/user-management?action=approve&id=<?= $user['id'] ?>" class="text-sm bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Approve</a>
                                        <a href="<?= BASE_PATH ?>/user-management?action=reject&id=<?= $user['id'] ?>" class="text-sm bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Reject</a>
                                    <?php endif; ?>
                                    <button onclick="openEditModal('<?= $user['id'] ?>', '<?= $user['role'] ?>')" class="text-sm bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Edit Role</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-3 text-gray-500">Tidak ada data user.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="editRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-semibold mb-4">Ubah Role Pengguna</h3>
        <form action="<?= BASE_PATH ?>/user-management" method="POST">
            <input type="hidden" name="action" value="update_role">
            <input type="hidden" name="user_id" id="editUserId">
            
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium">Role Baru</label>
                <select name="role" id="editUserRole" class="w-full border px-3 py-2 rounded-md">
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                    <option value="superadmin">Superadmin</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-3 py-1 border rounded-md text-gray-600">Batal</button>
                <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('editRoleModal');
    const userIdInput = document.getElementById('editUserId');
    const userRoleSelect = document.getElementById('editUserRole');

    function openEditModal(userId, currentRole) {
        userIdInput.value = userId;
        userRoleSelect.value = currentRole;
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        modal.classList.add('hidden');
    }
</script>