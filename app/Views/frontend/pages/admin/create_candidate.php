<div class="container mx-auto px-4 py-8">
    <div class="flex flex-wrap">
        <div class="w-full">
            <nav class="py-3" aria-label="breadcrumb">
                <ol class="flex text-sm">
                    <li class="flex items-center">
                        <a href="<?= base_url('/admin/dashboard') ?>" class="text-primary hover:text-primary-hover">Dashboard</a>
                        <svg class="h-4 w-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li class="flex items-center">
                        <a href="<?= base_url('/admin/elections') ?>" class="text-primary hover:text-primary-hover">Pemilihan</a>
                        <svg class="h-4 w-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li class="text-gray-700">Tambah Kandidat</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-primary text-white px-6 py-4">
            <h4 class="text-xl font-semibold">Tambah Kandidat Baru</h4>
            <p class="text-sm text-white opacity-80 mt-1">Pilih mahasiswa untuk menjadi kandidat. Mahasiswa yang dipilih akan diminta untuk melengkapi visi, misi, dan foto mereka sendiri.</p>
        </div>
        <div class="p-6">
            <form id="createCandidateForm" class="space-y-6" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="election_id" class="block text-sm font-medium text-gray-700 mb-1">Pemilihan <span class="text-red-500">*</span></label>
                        <select id="election_id" name="election_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Pemilihan</option>
                            <?php foreach ($elections as $election): ?>
                                <option value="<?= $election['id'] ?>" data-level="<?= $election['level'] ?>" data-target-id="<?= $election['target_id'] ?>"><?= esc($election['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Mahasiswa (Kandidat Utama) <span class="text-red-500">*</span></label>
                        <select id="user_id" name="user_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Mahasiswa</option>
                            <?php foreach ($users as $user): ?>
                                <?php if ($user['role'] === 'user'): ?>
                                    <option value="<?= $user['id'] ?>"
                                            data-faculty-id="<?= $user['faculty_id'] ?? '' ?>"
                                            data-department-id="<?= $user['department_id'] ?? '' ?>"
                                            class="user-option">
                                        <?= esc($user['name']) ?> (<?= esc($user['nim']) ?>)
                                        <?php if (!empty($user['faculty_name'])): ?>
                                            - <?= esc($user['faculty_name']) ?>
                                        <?php endif; ?>
                                        <?php if (!empty($user['department_name'])): ?>
                                            - <?= esc($user['department_name']) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="running_mate_id" class="block text-sm font-medium text-gray-700 mb-1">Running Mate (Wakil)</label>
                        <select id="running_mate_id" name="running_mate_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Running Mate (Opsional)</option>
                            <?php foreach ($users as $user): ?>
                                <?php if ($user['role'] === 'user'): ?>
                                    <option value="<?= $user['id'] ?>"
                                            data-faculty-id="<?= $user['faculty_id'] ?? '' ?>"
                                            data-department-id="<?= $user['department_id'] ?? '' ?>"
                                            class="running-mate-option">
                                        <?= esc($user['name']) ?> (<?= esc($user['nim']) ?>)
                                        <?php if (!empty($user['faculty_name'])): ?>
                                            - <?= esc($user['faculty_name']) ?>
                                        <?php endif; ?>
                                        <?php if (!empty($user['department_name'])): ?>
                                            - <?= esc($user['department_name']) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Kandidat yang dipilih akan menerima notifikasi untuk melengkapi profil mereka dengan:</p>
                                <ul class="list-disc pl-5 mt-1 space-y-1">
                                    <li>Visi dan misi</li>
                                    <li>Program kerja</li>
                                    <li>Foto profil</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <h5 class="text-lg font-semibold mb-4">Pengaturan Blockchain</h5>
                    
                    <div class="mb-4">
                        <div class="flex items-center mb-2">
                            <input type="checkbox" id="useBlockchain" name="use_blockchain" class="mr-2" checked>
                            <label for="useBlockchain" class="text-sm font-medium text-gray-700">Gunakan Blockchain</label>
                        </div>
                        <p class="text-xs text-gray-500">Jika dicentang, data kandidat akan dicatat di blockchain untuk verifikasi.</p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="<?= base_url('/admin/elections') ?>" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle form submission
    document.getElementById('createCandidateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const runningMateId = document.getElementById('running_mate_id').value;
        
        // Determine which endpoint to use based on whether a running mate is selected
        const endpoint = runningMateId ? '<?= base_url('/api/admin/candidates/paired') ?>' : '<?= base_url('/api/admin/candidates') ?>';
        
        // Send data to server
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
            },
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.error) {
                alert('Error: ' + result.error);
            } else {
                alert('Kandidat berhasil ditambahkan');
                window.location.href = '<?= base_url('/admin/elections') ?>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan kandidat');
        });
    });
    
    // Filter users based on election level for both primary candidate and running mate
    document.getElementById('election_id').addEventListener('change', function() {
        const electionId = this.value;
        const userSelect = document.getElementById('user_id');
        const runningMateSelect = document.getElementById('running_mate_id');
        const userOptions = userSelect.querySelectorAll('.user-option');
        const runningMateOptions = runningMateSelect.querySelectorAll('.running-mate-option');
        
        // If no election selected, show all users
        if (!electionId) {
            userOptions.forEach(option => {
                option.style.display = 'block';
            });
            runningMateOptions.forEach(option => {
                option.style.display = 'block';
            });
            return;
        }
        
        // Get selected election's level and target
        const selectedOption = this.options[this.selectedIndex];
        const level = selectedOption.getAttribute('data-level');
        const targetId = selectedOption.getAttribute('data-target-id');
        
        // Filter users based on election level
        userOptions.forEach(option => {
            // For university level, all users are eligible
            if (level === 'universitas') {
                option.style.display = 'block';
                return;
            }
            
            // For faculty level, only users from that faculty are eligible
            if (level === 'fakultas') {
                const userFacultyId = option.getAttribute('data-faculty-id');
                if (userFacultyId == targetId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
                return;
            }
            
            // For department level, only users from that department are eligible
            if (level === 'jurusan') {
                const userDepartmentId = option.getAttribute('data-department-id');
                if (userDepartmentId == targetId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
                return;
            }
            
            // Default: hide user
            option.style.display = 'none';
        });
        
        // Filter running mate users based on election level
        runningMateOptions.forEach(option => {
            // For university level, all users are eligible
            if (level === 'universitas') {
                option.style.display = 'block';
                return;
            }
            
            // For faculty level, only users from that faculty are eligible
            if (level === 'fakultas') {
                const userFacultyId = option.getAttribute('data-faculty-id');
                if (userFacultyId == targetId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
                return;
            }
            
            // For department level, only users from that department are eligible
            if (level === 'jurusan') {
                const userDepartmentId = option.getAttribute('data-department-id');
                if (userDepartmentId == targetId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
                return;
            }
            
            // Default: hide user
            option.style.display = 'none';
        });
    });
    
    // Prevent selecting the same user as both primary candidate and running mate
    document.getElementById('user_id').addEventListener('change', function() {
        const primaryUserId = this.value;
        const runningMateSelect = document.getElementById('running_mate_id');
        const runningMateOptions = runningMateSelect.querySelectorAll('.running-mate-option');
        
        runningMateOptions.forEach(option => {
            if (option.value === primaryUserId) {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });
    });
    
    // Prevent selecting the same user as both primary candidate and running mate
    document.getElementById('running_mate_id').addEventListener('change', function() {
        const runningMateId = this.value;
        const primaryUserSelect = document.getElementById('user_id');
        const primaryUserOptions = primaryUserSelect.querySelectorAll('.user-option');
        
        primaryUserOptions.forEach(option => {
            if (option.value === runningMateId) {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });
    });
});
</script>