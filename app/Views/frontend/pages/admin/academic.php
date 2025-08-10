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
                                data-bs-toggle="modal" data-bs-target="#createFacultyModal">
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
                                data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
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
                    <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
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
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-bs-dismiss="modal">Batal</button>
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
                    <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
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
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-bs-dismiss="modal">Batal</button>
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
                    <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
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
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-bs-dismiss="modal">Batal</button>
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
                    <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
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
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-bs-dismiss="modal">Batal</button>
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
                t.classList.remove('text-primary', 'border-b-2', 'border-primary');
                t.classList.add('text-gray-500');
            });
            
            // Show the selected tab content
            const targetId = this.getAttribute('data-target');
            document.getElementById(targetId).classList.remove('hidden');
            
            // Add active class to the clicked tab
            this.classList.remove('text-gray-500');
            this.classList.add('text-primary', 'border-b-2', 'border-primary');
        });
    });
    
    // Setup modal functionality
    setupModals();
});

function setupModals() {
    // Get all modals
    const modals = document.querySelectorAll('[id$="Modal"]');
    
    // Get all buttons that open modals
    const btns = document.querySelectorAll('[data-bs-toggle="modal"]');
    
    // Get all elements that close modals
    const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
    
    // When the user clicks a button, open the corresponding modal
    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-bs-target');
            document.querySelector(modalId).classList.remove('hidden');
        });
    });
    
    // When the user clicks on a close button, close the modal
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.fixed');
            modal.classList.add('hidden');
        });
    });
    
    // When the user clicks anywhere outside of a modal, close it
    window.addEventListener('click', function(event) {
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
}

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
    document.getElementById('createFacultyModal').classList.add('hidden');
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
    document.getElementById('editFacultyModal').classList.add('hidden');
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
    document.getElementById('createDepartmentModal').classList.add('hidden');
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
    document.getElementById('editDepartmentModal').classList.add('hidden');
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