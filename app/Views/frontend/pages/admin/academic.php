<main class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-university mr-2"></i> Manajemen Akademik</h2>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <ul class="flex flex-wrap border-b border-gray-200">
            <li class="mr-2">
                <a href="#" class="inline-block py-2 px-4 text-primary border-b-2 border-primary font-medium active-tab" id="tab-faculty" data-target="faculty-content">Fakultas</a>
            </li>
            <li class="mr-2">
                <a href="#" class="inline-block py-2 px-4 text-gray-500 hover:text-primary font-medium" id="tab-department" data-target="department-content">Jurusan</a>
            </li>
        </ul>
    </div>

    <!-- Faculty Tab Content -->
    <div id="faculty-content" class="tab-content">
        <div class="flex flex-wrap">
            <div class="w-full">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                        <h5 class="font-semibold mb-0"><i class="fas fa-list mr-2"></i> Daftar Fakultas</h5>
                        <button class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors"
                                onclick="openCreateFacultyModal()">
                            <i class="fas fa-plus mr-2"></i> Tambah Fakultas
                        </button>
                    </div>
                    <div class="p-6">
                        <!-- Faculty Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Fakultas</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Jurusan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($faculties as $faculty): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($faculty['code']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($faculty['name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= count($faculty['departments'] ?? []) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex space-x-2">
                                                    <button class="inline-flex items-center p-1.5 border border-primary text-primary hover:bg-primary hover:text-white rounded transition-colors"
                                                            onclick="editFaculty(<?= $faculty['id'] ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="inline-flex items-center p-1.5 border border-red-500 text-red-500 hover:bg-red-500 hover:text-white rounded transition-colors"
                                                            onclick="deleteFaculty(<?= $faculty['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Tab Content -->
    <div id="department-content" class="tab-content hidden">
        <div class="flex flex-wrap">
            <div class="w-full">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                        <h5 class="font-semibold mb-0"><i class="fas fa-list mr-2"></i> Daftar Jurusan</h5>
                        <button class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors"
                                onclick="openCreateDepartmentModal()">
                            <i class="fas fa-plus mr-2"></i> Tambah Jurusan
                        </button>
                    </div>
                    <div class="p-6">
                        <!-- Department Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jurusan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fakultas</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($departments as $department): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($department['code']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($department['name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($department['faculty_name']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex space-x-2">
                                                    <button class="inline-flex items-center p-1.5 border border-primary text-primary hover:bg-primary hover:text-white rounded transition-colors"
                                                            onclick="editDepartment(<?= $department['id'] ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="inline-flex items-center p-1.5 border border-red-500 text-red-500 hover:bg-red-500 hover:text-white rounded transition-colors"
                                                            onclick="deleteDepartment(<?= $department['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Create Faculty Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="createFacultyModal" tabindex="-1" aria-labelledby="createFacultyModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900" id="createFacultyModalLabel">Tambah Fakultas Baru</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal('createFacultyModal')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createFacultyForm">
                    <div class="mb-4">
                        <label for="faculty_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Fakultas *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="faculty_name" name="name" required>
                    </div>
                    <div class="mb-4">
                        <label for="faculty_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Fakultas *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="faculty_code" name="code" required>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="createFaculty()">Simpan</button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('createFacultyModal')">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Faculty Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="editFacultyModal" tabindex="-1" aria-labelledby="editFacultyModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900" id="editFacultyModalLabel">Edit Fakultas</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal('editFacultyModal')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editFacultyForm">
                    <input type="hidden" id="edit_faculty_id" name="id">
                    <div class="mb-4">
                        <label for="edit_faculty_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Fakultas *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_faculty_name" name="name" required>
                    </div>
                    <div class="mb-4">
                        <label for="edit_faculty_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Fakultas *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_faculty_code" name="code" required>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="updateFaculty()">Simpan</button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('editFacultyModal')">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Department Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900" id="createDepartmentModalLabel">Tambah Jurusan Baru</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal('createDepartmentModal')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createDepartmentForm">
                    <div class="mb-4">
                        <label for="department_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Jurusan *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="department_name" name="name" required>
                    </div>
                    <div class="mb-4">
                        <label for="department_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Jurusan *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="department_code" name="code" required>
                    </div>
                    <div class="mb-4">
                        <label for="department_faculty_id" class="block text-sm font-medium text-gray-700 mb-1">Fakultas *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="department_faculty_id" name="faculty_id" required>
                            <option value="">Pilih Fakultas</option>
                            <?php foreach ($faculties as $faculty): ?>
                                <option value="<?= $faculty['id'] ?>"><?= esc($faculty['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="createDepartment()">Simpan</button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('createDepartmentModal')">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900" id="editDepartmentModalLabel">Edit Jurusan</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal('editDepartmentModal')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editDepartmentForm">
                    <input type="hidden" id="edit_department_id" name="id">
                    <div class="mb-4">
                        <label for="edit_department_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Jurusan *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_department_name" name="name" required>
                    </div>
                    <div class="mb-4">
                        <label for="edit_department_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Jurusan *</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_department_code" name="code" required>
                    </div>
                    <div class="mb-4">
                        <label for="edit_department_faculty_id" class="block text-sm font-medium text-gray-700 mb-1">Fakultas *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="edit_department_faculty_id" name="faculty_id" required>
                            <option value="">Pilih Fakultas</option>
                            <?php foreach ($faculties as $faculty): ?>
                                <option value="<?= $faculty['id'] ?>"><?= esc($faculty['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="updateDepartment()">Simpan</button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('editDepartmentModal')">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup tab switching
    const tabs = document.querySelectorAll('[id^="tab-"]');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            tabs.forEach(t => {
                t.classList.remove('text-primary', 'border-b-2', 'border-primary', 'active-tab');
                t.classList.add('text-gray-500');
            });
            
            // Show the selected tab content
            const targetId = this.getAttribute('data-target');
            document.getElementById(targetId).classList.remove('hidden');
            
            // Add active class to the clicked tab
            this.classList.remove('text-gray-500');
            this.classList.add('text-primary', 'border-b-2', 'border-primary', 'active-tab');
        });
    });
});

// Modal functions
function openCreateFacultyModal() {
    document.getElementById('createFacultyModal').classList.remove('hidden');
    // Clear form
    document.getElementById('createFacultyForm').reset();
}

function openCreateDepartmentModal() {
    document.getElementById('createDepartmentModal').classList.remove('hidden');
    // Clear form
    document.getElementById('createDepartmentForm').reset();
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('[id$="Modal"]');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

function createFaculty() {
    const form = document.getElementById('createFacultyForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validation
    if (!data.name || !data.code) {
        alert('Nama dan Kode Fakultas harus diisi');
        return;
    }
    
    fetch('<?= base_url('/api/admin/faculties') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Fakultas berhasil ditambahkan!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan fakultas');
    });
    
    // Close the modal
    closeModal('createFacultyModal');
}

function editFaculty(facultyId) {
    // Fetch faculty data
    fetch(`<?= base_url('/api/admin/faculties/') ?>${facultyId}`, {
        headers: {
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            // Populate form
            document.getElementById('edit_faculty_id').value = result.id;
            document.getElementById('edit_faculty_name').value = result.name;
            document.getElementById('edit_faculty_code').value = result.code;
            
            // Show modal
            document.getElementById('editFacultyModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengambil data fakultas');
    });
}

function updateFaculty() {
    const form = document.getElementById('editFacultyForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validation
    if (!data.name || !data.code) {
        alert('Nama dan Kode Fakultas harus diisi');
        return;
    }
    
    fetch(`<?= base_url('/api/admin/faculties/') ?>${data.id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        },
        body: JSON.stringify({
            name: data.name,
            code: data.code
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Fakultas berhasil diperbarui!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui fakultas');
    });
    
    // Close the modal
    closeModal('editFacultyModal');
}

function deleteFaculty(facultyId) {
    if (!confirm('Apakah Anda yakin ingin menghapus fakultas ini? Semua jurusan yang terkait juga akan dihapus.')) {
        return;
    }
    
    fetch(`<?= base_url('/api/admin/faculties/') ?>${facultyId}`, {
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Fakultas berhasil dihapus!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus fakultas');
    });
}

function createDepartment() {
    const form = document.getElementById('createDepartmentForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validation
    if (!data.name || !data.code || !data.faculty_id) {
        alert('Nama, Kode, dan Fakultas harus diisi');
        return;
    }
    
    fetch('<?= base_url('/api/admin/departments') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Jurusan berhasil ditambahkan!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan jurusan');
    });
    
    // Close the modal
    closeModal('createDepartmentModal');
}

function editDepartment(departmentId) {
    // Fetch department data
    fetch(`<?= base_url('/api/admin/departments/') ?>${departmentId}`, {
        headers: {
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            // Populate form
            document.getElementById('edit_department_id').value = result.id;
            document.getElementById('edit_department_name').value = result.name;
            document.getElementById('edit_department_code').value = result.code;
            document.getElementById('edit_department_faculty_id').value = result.faculty_id;
            
            // Show modal
            document.getElementById('editDepartmentModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengambil data jurusan');
    });
}

function updateDepartment() {
    const form = document.getElementById('editDepartmentForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validation
    if (!data.name || !data.code || !data.faculty_id) {
        alert('Nama, Kode, dan Fakultas harus diisi');
        return;
    }
    
    fetch(`<?= base_url('/api/admin/departments/') ?>${data.id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        },
        body: JSON.stringify({
            name: data.name,
            code: data.code,
            faculty_id: data.faculty_id
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Jurusan berhasil diperbarui!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui jurusan');
    });
    
    // Close the modal
    closeModal('editDepartmentModal');
}

function deleteDepartment(departmentId) {
    if (!confirm('Apakah Anda yakin ingin menghapus jurusan ini?')) {
        return;
    }
    
    fetch(`<?= base_url('/api/admin/departments/') ?>${departmentId}`, {
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Jurusan berhasil dihapus!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus jurusan');
    });
}
</script>

<!-- Debug Script for Academic Page -->
<script>
console.log('Academic page script loading...');

// Define functions globally immediately
window.openCreateFacultyModal = function() {
    console.log('openCreateFacultyModal called');
    const modal = document.getElementById('createFacultyModal');
    const form = document.getElementById('createFacultyForm');
    
    if (modal && form) {
        modal.classList.remove('hidden');
        form.reset();
        console.log('Faculty modal opened successfully');
    } else {
        console.error('Faculty modal or form not found');
    }
};

window.openCreateDepartmentModal = function() {
    console.log('openCreateDepartmentModal called');
    const modal = document.getElementById('createDepartmentModal');
    const form = document.getElementById('createDepartmentForm');
    
    if (modal && form) {
        modal.classList.remove('hidden');
        form.reset();
        console.log('Department modal opened successfully');
    } else {
        console.error('Department modal or form not found');
    }
};

window.closeModal = function(modalId) {
    console.log('closeModal called for:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        console.log('Modal closed successfully');
    } else {
        console.error('Modal not found:', modalId);
    }
};

window.editFaculty = function(facultyId) {
    console.log('editFaculty called for ID:', facultyId);
    alert('Edit Faculty function called for ID: ' + facultyId);
};

window.deleteFaculty = function(facultyId) {
    console.log('deleteFaculty called for ID:', facultyId);
    if (confirm('Apakah Anda yakin ingin menghapus fakultas ini?')) {
        alert('Delete Faculty function called for ID: ' + facultyId);
    }
};

window.editDepartment = function(departmentId) {
    console.log('editDepartment called for ID:', departmentId);
    alert('Edit Department function called for ID: ' + departmentId);
};

window.deleteDepartment = function(departmentId) {
    console.log('deleteDepartment called for ID:', departmentId);
    if (confirm('Apakah Anda yakin ingin menghapus jurusan ini?')) {
        alert('Delete Department function called for ID: ' + departmentId);
    }
};

console.log('Academic page functions defined globally');

// Setup tab functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, testing tab functionality...');
    
    // Setup tab switching
    const tabs = document.querySelectorAll('[id^="tab-"]');
    console.log('Found tabs:', tabs.length);
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Tab clicked:', this.id);
            
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            tabs.forEach(t => {
                t.classList.remove('text-primary', 'border-b-2', 'border-primary', 'active-tab');
                t.classList.add('text-gray-500');
            });
            
            // Show the selected tab content
            const targetId = this.getAttribute('data-target');
            console.log('Target:', targetId);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.classList.remove('hidden');
                console.log('Tab content shown successfully');
            } else {
                console.error('Target element not found:', targetId);
            }
            
            // Add active class to the clicked tab
            this.classList.remove('text-gray-500');
            this.classList.add('text-primary', 'border-b-2', 'border-primary', 'active-tab');
        });
    });
    
    console.log('Tab functionality setup complete');
});
</script>