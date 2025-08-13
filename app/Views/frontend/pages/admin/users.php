<main class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-users mr-2"></i> Manajemen Pengguna</h2>
                <button class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors"
                        data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-user-plus mr-2"></i> Tambah User
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h5 class="font-semibold mb-0"><i class="fas fa-list mr-2"></i> Daftar Pengguna</h5>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-4 md:mb-0">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                                       placeholder="Cari pengguna..." id="searchUser">
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                                    id="filterRole">
                                <option value="">Semua Role</option>
                                <option value="admin">Admin</option>
                                <option value="mahasiswa">Mahasiswa</option>
                            </select>
                        </div>
                    </div>
                    
                    <?php if (empty($users)): ?>
                        <div class="text-center py-10">
                            <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                            <h4 class="text-xl font-semibold mb-2">Belum ada pengguna</h4>
                            <p class="text-gray-500">Klik tombol "Tambah User" untuk menambahkan pengguna pertama</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fakultas</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th> -->
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($users as $user): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($user['nim']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($user['name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($user['faculty_name'] ?? '-') ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($user['department_name'] ?? '-') ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <?php
                                                $roleClass = '';
                                                if ($user['role'] === 'admin') {
                                                    $roleClass = 'bg-primary text-white';
                                                } elseif ($user['role'] === 'kandidat') {
                                                    $roleClass = 'bg-emerald-100 text-emerald-800';
                                                } else {
                                                    $roleClass = 'bg-gray-100 text-gray-800';
                                                }
                                                ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $roleClass ?>">
                                                    <?= ucfirst($user['role']) ?>
                                                </span>
                                            </td>
                                            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $user['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' ?>">
                                                    <?= ucfirst($user['status']) ?>
                                                </span>
                                            </td> -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex space-x-2">
                                                    <button class="inline-flex items-center p-1.5 border border-primary text-primary hover:bg-primary hover:text-white rounded transition-colors"
                                                            onclick="editUser(<?= $user['id'] ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if (!$user['is_super_admin']): ?>
                                                        <button class="inline-flex items-center p-1.5 border border-<?= $user['role'] === 'admin' ? 'gray-500' : 'primary' ?> text-<?= $user['role'] === 'admin' ? 'gray-500' : 'primary' ?> hover:bg-<?= $user['role'] === 'admin' ? 'gray-500' : 'primary' ?> hover:text-white rounded transition-colors"
                                                                onclick="changeUserRole(<?= $user['id'] ?>, '<?= $user['role'] === 'admin' ? 'mahasiswa' : 'admin' ?>')">
                                                            <?= $user['role'] === 'admin' ? 'Jadikan User' : 'Jadikan Admin' ?>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Create User Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900" id="createUserModalLabel">Tambah Pengguna Baru</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createUserForm">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="nim" class="block text-sm font-medium text-gray-700 mb-1">NIM *</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="nim" name="nim" required>
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="name" name="name" required>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                            <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="password" name="password" required minlength="6">
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="faculty_id" name="faculty_id">
                                <option value="">Pilih Fakultas</option>
                                <?php
                                $facultyModel = new \App\Models\FacultyModel();
                                $faculties = $facultyModel->findAll();
                                foreach ($faculties as $faculty): ?>
                                    <option value="<?= $faculty['id'] ?>"><?= esc($faculty['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="department" name="department_id">
                                <option value="">Pilih Jurusan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="role" name="role" required>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <!-- <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="status" name="status" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div> -->
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="createUser()">Simpan</button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900" id="editUserModalLabel">Edit Pengguna</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editUserForm" method="post">
                    <input type="hidden" id="edit_user_id" name="user_id">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="edit_nim" class="block text-sm font-medium text-gray-700 mb-1">NIM *</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_nim" name="nim" required>
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_name" name="name" required>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 mb-4">
                            <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-1">Password (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_password" name="password" minlength="6">
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                            <div id="faculty_display_container">
                                <div class="flex items-center">
                                    <span id="faculty_display_text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">-</span>
                                    <button type="button" class="ml-2 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors" onclick="editFaculty()">
                                        <i class="fas fa-edit"></i> Ubah
                                    </button>
                                </div>
                                <div id="faculty_edit_container" class="hidden">
                                    <div class="flex items-center">
                                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_faculty_id" name="faculty_id">
                                            <option value="">Pilih Fakultas</option>
                                            <?php
                                            $facultyModel = new \App\Models\FacultyModel();
                                            $faculties = $facultyModel->findAll();
                                            foreach ($faculties as $faculty): ?>
                                                <option value="<?= $faculty['id'] ?>"><?= esc($faculty['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="ml-2 px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition-colors" onclick="saveFaculty()">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="ml-1 px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors" onclick="cancelFacultyEdit()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                            <div id="department_display_container">
                                <div class="flex items-center">
                                    <span id="department_display_text" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">-</span>
                                    <button type="button" class="ml-2 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors" onclick="editDepartment()">
                                        <i class="fas fa-edit"></i> Ubah
                                    </button>
                                </div>
                                <div id="department_edit_container" class="hidden">
                                    <div class="flex items-center">
                                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_department_id" name="department_id">
                                            <option value="">Pilih Jurusan</option>
                                        </select>
                                        <button type="button" class="ml-2 px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition-colors" onclick="saveDepartment()">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="ml-1 px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors" onclick="cancelDepartmentEdit()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_role" name="role" required>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <!-- <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_status" name="status" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div> -->
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="updateUser()">Simpan</button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup event listeners
    document.getElementById('faculty_id').addEventListener('change', loadDepartments);
    document.getElementById('searchUser').addEventListener('input', filterUsers);
    document.getElementById('filterRole').addEventListener('change', filterUsers);
    
    // Setup modal functionality
    setupModal();
});

function setupModal() {
    // Get the modal
    const modal = document.getElementById('createUserModal');
    
    // Get all buttons that open the modal
    const btns = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#createUserModal"]');
    
    // Get all elements that close the modal
    const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
    
    // When the user clicks the button, open the modal
    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });
    });
    
    // When the user clicks on a close button, close the modal
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    });
    
    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
    
    // Setup faculty-department relationship in the create modal
    const facultySelect = document.getElementById('faculty_id');
    if (facultySelect) {
        facultySelect.addEventListener('change', function() {
            loadDepartments();
        });
    }
    
    // Setup edit modal functionality
    setupEditModal();
}

function setupEditModal() {
    // Get the edit modal
    const modal = document.getElementById('editUserModal');
    
    // Get all elements that close the modal
    const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
    
    // When the user clicks on a close button, close the modal
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    });
    
    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
}


function createUser() {
    const form = document.getElementById('createUserForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validasi
    if (!data.nim || !data.name || !data.password) {
        alert('NIM, Nama, dan Password harus diisi');
        return;
    }
    
    fetch('<?= base_url('/api/auth/register') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        console.log('Create result:', result); // Debug log
        
        if (result.error || result.status === 'error') {
            let errorMsg = result.error || result.message;
            alert('Error: ' + errorMsg);
        } else {
            alert('User berhasil ditambahkan!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan user');
    });
    
    // Close the modal
    document.getElementById('createUserModal').classList.add('hidden');
}

function editUser(userId) {
    // Debug token
    const token = '<?= session()->get('auth_token') ?>';
    console.log('Auth token:', token ? 'exists' : 'missing');
    
    // Fetch specific user data
    fetch(`<?= base_url('/api/admin/users/') ?>${userId}`, {
        headers: {
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                if (response.status === 401) {
                    alert('Token autentikasi tidak valid. Silakan login ulang.');
                    window.location.href = '<?= base_url('login') ?>';
                    return;
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            console.log('API Response:', result); // Debug log
            
            if (result.error || result.status === 'error') {
                alert('Error: ' + (result.error || result.message));
                return;
            }
            
            // Get user data from response
            let user = result.data || result;
            
            if (!user || !user.id) {
                alert('User tidak ditemukan');
                return;
            }
            
            console.log('User data:', user); // Debug log
            
            // Populate the edit form with user data - handle undefined values
            document.getElementById('edit_user_id').value = user.id || '';
            document.getElementById('edit_nim').value = user.nim || '';
            document.getElementById('edit_name').value = user.name || '';
            document.getElementById('edit_role').value = user.role || 'mahasiswa';
            
            // Only set status if the field exists (it's commented out in the form)
            const statusField = document.getElementById('edit_status');
            if (statusField) {
                statusField.value = user.status || 'active';
            }
            
            // Set faculty and department display
            updateFacultyDisplay(user.faculty_name || '-', user.faculty_id);
            updateDepartmentDisplay(user.department_name || '-', user.department_id);
            
            // Store original faculty data for cancellation
            window.currentUser = user;
            
            // Show the edit modal
            document.getElementById('editUserModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data user');
        });
}

function updateUser() {
    const userId = document.getElementById('edit_user_id').value;
    
    if (!userId) {
        alert('User ID tidak ditemukan');
        return;
    }

    // Ambil data secara manual dari setiap field
    const data = {};
    
    const nim = document.getElementById('edit_nim').value;
    const name = document.getElementById('edit_name').value;
    const password = document.getElementById('edit_password').value;
    const role = document.getElementById('edit_role').value;
    
    // Only get status if the field exists (it's commented out in the form)
    const statusField = document.getElementById('edit_status');
    const status = statusField ? statusField.value : null;

    // Hanya tambahkan field yang tidak kosong (kecuali password, karena bisa kosong)
    if (nim) data.nim = nim;
    if (name) data.name = name;
    if (password) data.password = password; // Password boleh kosong, tapi jika ada maka kirim
    if (role) data.role = role;
    if (status) data.status = status;

    // Validasi minimal data yang harus ada
    if (!data.nim || !data.name || !data.role) {
        alert('NIM, Nama, Role harus diisi');
        return;
    }

    // Kirim request
    fetch(`<?= base_url('/api/admin/users/') ?>${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        console.log('Update result:', result); // Debug log
        
        if (result.error || result.status === 'error') {
            let errorMsg = result.error || result.message;
            if (typeof errorMsg === 'object') {
                errorMsg = JSON.stringify(errorMsg);
            }
            alert('Error: ' + errorMsg);
        } else {
            alert('User berhasil diubah!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengubah user');
    });

    // Close the modal
    document.getElementById('editUserModal').classList.add('hidden');
}

function changeUserRole(userId, newRole) {
    if (!confirm(`Apakah Anda yakin ingin mengubah role user ini menjadi ${newRole}?`)) {
        return;
    }
    
    fetch(`<?= base_url('/api/admin/users/') ?>${userId}/role`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        },
        body: JSON.stringify({role: newRole})
    })
    .then(response => response.json())
    .then(result => {
        console.log('Role change result:', result); // Debug log
        
        if (result.error || result.status === 'error') {
            let errorMsg = result.error || result.message;
            alert('Error: ' + errorMsg + ' - ' + userId + ' - ' + newRole);
        } else {
            alert('Role user berhasil diubah!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengubah role user');
    });
}

// Faculty and Department Edit Functions
function updateFacultyDisplay(facultyName, facultyId) {
    document.getElementById('faculty_display_text').textContent = facultyName;
    window.currentFacultyId = facultyId;
    window.currentFacultyName = facultyName;
}

function updateDepartmentDisplay(departmentName, departmentId) {
    document.getElementById('department_display_text').textContent = departmentName;
    window.currentDepartmentId = departmentId;
    window.currentDepartmentName = departmentName;
}

function editFaculty() {
    // Hide display, show edit
    document.querySelector('#faculty_display_container > div:first-child').classList.add('hidden');
    document.getElementById('faculty_edit_container').classList.remove('hidden');
    
    // Set current value in dropdown
    const facultySelect = document.getElementById('edit_faculty_id');
    facultySelect.value = window.currentFacultyId || '';
}

function saveFaculty() {
    const facultySelect = document.getElementById('edit_faculty_id');
    const selectedOption = facultySelect.selectedOptions[0];
    const userId = document.getElementById('edit_user_id').value;
    
    if (!selectedOption || !selectedOption.value) {
        alert('Pilih fakultas terlebih dahulu');
        return;
    }
    
    // Save to database
    const data = {
        faculty_id: selectedOption.value
    };
    
    fetch(`<?= base_url('/api/admin/users/') ?>${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.error || result.status === 'error') {
            alert('Error: ' + (result.error || result.message));
        } else {
            // Update display
            updateFacultyDisplay(selectedOption.textContent, selectedOption.value);
            
            // Reset department since faculty changed
            updateDepartmentDisplay('-', null);
            
            // Hide edit, show display
            cancelFacultyEdit();
            
            alert('Fakultas berhasil diubah!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengubah fakultas');
    });
}

function cancelFacultyEdit() {
    // Hide edit, show display
    document.querySelector('#faculty_display_container > div:first-child').classList.remove('hidden');
    document.getElementById('faculty_edit_container').classList.add('hidden');
}

function editDepartment() {
    if (!window.currentFacultyId) {
        alert('Pilih fakultas terlebih dahulu');
        return;
    }
    
    // Hide display, show edit
    document.querySelector('#department_display_container > div:first-child').classList.add('hidden');
    document.getElementById('department_edit_container').classList.remove('hidden');
    
    // Load departments for current faculty
    loadDepartmentsForEdit(window.currentFacultyId, window.currentDepartmentId);
}

function loadDepartmentsForEdit(facultyId, selectedDepartmentId = null) {
    const departmentSelect = document.getElementById('edit_department_id');
    
    departmentSelect.innerHTML = '<option value="">Loading...</option>';
    
    fetch(`<?= base_url('/api/departments/') ?>${facultyId}`)
        .then(response => response.json())
        .then(departments => {
            departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
            
            if (Array.isArray(departments)) {
                departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    
                    if (selectedDepartmentId && dept.id == selectedDepartmentId) {
                        option.selected = true;
                    }
                    
                    departmentSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching departments:', error);
            departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
        });
}

function saveDepartment() {
    const departmentSelect = document.getElementById('edit_department_id');
    const selectedOption = departmentSelect.selectedOptions[0];
    const userId = document.getElementById('edit_user_id').value;
    
    if (!selectedOption || !selectedOption.value) {
        alert('Pilih jurusan terlebih dahulu');
        return;
    }
    
    // Save to database
    const data = {
        department_id: selectedOption.value
    };
    
    fetch(`<?= base_url('/api/admin/users/') ?>${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.error || result.status === 'error') {
            alert('Error: ' + (result.error || result.message));
        } else {
            // Update display
            updateDepartmentDisplay(selectedOption.textContent, selectedOption.value);
            
            // Hide edit, show display
            cancelDepartmentEdit();
            
            alert('Jurusan berhasil diubah!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengubah jurusan');
    });
}

function cancelDepartmentEdit() {
    // Hide edit, show display
    document.querySelector('#department_display_container > div:first-child').classList.remove('hidden');
    document.getElementById('department_edit_container').classList.add('hidden');
}

function loadDepartments() {
    const facultySelect = document.getElementById('faculty_id');
    const departmentSelect = document.getElementById('department');
    const facultyId = facultySelect.value;
    
    if (!facultyId) {
        departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
        return;
    }
    
    departmentSelect.innerHTML = '<option value="">Loading...</option>';
    
    fetch(`<?= base_url('/api/departments/') ?>${facultyId}`)
        .then(response => response.json())
        .then(departments => {
            departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
            
            if (Array.isArray(departments)) {
                departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    departmentSelect.appendChild(option);
                });
            } else {
                departmentSelect.innerHTML = '<option value="">Error: Invalid data format</option>';
            }
        })
        .catch(error => {
            console.error('Error fetching departments:', error);
            departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
        });
}

function filterUsers() {
    const searchTerm = document.getElementById('searchUser').value.toLowerCase();
    const roleFilter = document.getElementById('filterRole').value;
    
    // Implementasi filter client-side
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const nim = row.cells[0].textContent.toLowerCase();
        const name = row.cells[1].textContent.toLowerCase();
        const role = row.cells[4].textContent.toLowerCase();
        
        const matchesSearch = nim.includes(searchTerm) ||
                             name.includes(searchTerm);
                             
        const matchesRole = !roleFilter || role.includes(roleFilter.toLowerCase());
        
        if (matchesSearch && matchesRole) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
}
</script>