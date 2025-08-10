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
                    <li class="text-gray-700">Edit Pemilihan</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-primary text-white px-6 py-4">
            <h4 class="text-xl font-semibold">Edit Pemilihan</h4>
        </div>
        <div class="p-6">
            <form id="editElectionForm" class="space-y-6">
                <input type="hidden" id="election_id" name="id" value="<?= $election['id'] ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Pemilihan <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" value="<?= esc($election['title']) ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
                        <select id="level" name="level" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Level</option>
                            <option value="universitas" <?= $election['level'] === 'universitas' ? 'selected' : '' ?>>Universitas</option>
                            <option value="fakultas" <?= $election['level'] === 'fakultas' ? 'selected' : '' ?>>Fakultas</option>
                            <option value="jurusan" <?= $election['level'] === 'jurusan' ? 'selected' : '' ?>>Jurusan</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="start_time" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($election['start_time'])) ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="end_time" name="end_time" value="<?= date('Y-m-d\TH:i', strtotime($election['end_time'])) ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required><?= esc($election['description']) ?></textarea>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="draft" <?= $election['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="active" <?= $election['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="completed" <?= $election['status'] === 'completed' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <h5 class="text-lg font-semibold mb-4">Eligibilitas Pemilih</h5>
                    
                    <div id="eligibilityContainer">
                        <div class="mb-4">
                            <div class="flex items-center mb-2">
                                <input type="checkbox" id="allStudents" name="all_students" class="mr-2" <?= empty($eligibility) ? 'checked' : '' ?>>
                                <label for="allStudents" class="text-sm font-medium text-gray-700">Semua Mahasiswa</label>
                            </div>
                            <p class="text-xs text-gray-500">Jika dicentang, semua mahasiswa dapat berpartisipasi dalam pemilihan ini.</p>
                        </div>
                        
                        <div id="specificEligibility" class="space-y-4" style="display: <?= empty($eligibility) ? 'none' : 'block' ?>">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="faculty" class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                                    <select id="faculty" name="faculty_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih Fakultas</option>
                                        <?php foreach ($faculties as $faculty): ?>
                                            <option value="<?= $faculty['id'] ?>"><?= esc($faculty['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                                    <select id="department" name="department_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                                        <option value="">Pilih Jurusan</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button type="button" id="addEligibilityBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-plus mr-1"></i> Tambah Eligibilitas
                            </button>
                            
                            <div id="eligibilityList" class="mt-4">
                                <!-- Existing eligibility items -->
                                <?php if (!empty($eligibility)): ?>
                                    <?php foreach ($eligibility as $index => $item): ?>
                                        <?php 
                                            $facultyName = '';
                                            $departmentName = 'Semua Jurusan';
                                            
                                            // Find faculty name
                                            foreach ($faculties as $faculty) {
                                                if ($faculty['id'] == $item['faculty_id']) {
                                                    $facultyName = $faculty['name'];
                                                    break;
                                                }
                                            }
                                            
                                            // Find department name if department_id is set
                                            if (!empty($item['department_id'])) {
                                                foreach ($faculties as $faculty) {
                                                    if ($faculty['id'] == $item['faculty_id'] && isset($faculty['departments'])) {
                                                        foreach ($faculty['departments'] as $dept) {
                                                            if ($dept['id'] == $item['department_id']) {
                                                                $departmentName = $dept['name'];
                                                                break 2;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        ?>
                                        <div class="eligibility-item flex justify-between items-center bg-gray-50 p-3 rounded-lg mb-2" data-faculty-id="<?= $item['faculty_id'] ?>" data-department-id="<?= $item['department_id'] ?? '' ?>">
                                            <div>
                                                <p class="font-medium"><?= esc($facultyName) ?></p>
                                                <p class="text-sm text-gray-600"><?= esc($departmentName) ?></p>
                                                <input type="hidden" name="eligibility[<?= $index ?>][faculty_id]" value="<?= $item['faculty_id'] ?>">
                                                <input type="hidden" name="eligibility[<?= $index ?>][department_id]" value="<?= $item['department_id'] ?? '' ?>">
                                            </div>
                                            <button type="button" class="remove-eligibility text-red-500 hover:text-red-700">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
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
    // Handle faculty change to load departments
    document.getElementById('faculty').addEventListener('change', function() {
        const facultyId = this.value;
        const departmentSelect = document.getElementById('department');
        
        if (facultyId) {
            // Enable department select
            departmentSelect.disabled = false;
            
            // Clear current options
            departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
            
            console.log(`Fetching departments for faculty ID: ${facultyId}`);
            console.log(`API URL: ${BASE_URL}/api/departments/${facultyId}`);
            
            // Add loading indicator
            departmentSelect.innerHTML = '<option value="">Loading...</option>';
            
            // Fetch departments for selected faculty with improved error handling
            fetch(`<?= base_url('api/departments') ?>/${facultyId}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', [...response.headers.entries()]);
                    
                    if (!response.ok) {
                        throw new Error(`Network response was not ok: ${response.status}`);
                    }
                    
                    return response.json().catch(err => {
                        console.error('JSON parse error:', err);
                        throw new Error('Failed to parse JSON response');
                    });
                })
                .then(departments => {
                    console.log('Departments data:', departments);
                    departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
                    
                    if (Array.isArray(departments)) {
                        if (departments.length === 0) {
                            console.warn('No departments found for this faculty');
                            departmentSelect.innerHTML = '<option value="">Tidak ada jurusan</option>';
                        } else {
                            departments.forEach(dept => {
                                const option = document.createElement('option');
                                option.value = dept.id;
                                option.textContent = dept.name;
                                departmentSelect.appendChild(option);
                            });
                        }
                    } else {
                        console.error('Expected array but got:', typeof departments, departments);
                        departmentSelect.innerHTML = '<option value="">Error: Invalid data format</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
                    departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
                });
        } else {
            // Disable and reset department select
            departmentSelect.disabled = true;
            departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
        }
    });
    
    // Handle "All Students" checkbox
    document.getElementById('allStudents').addEventListener('change', function() {
        const specificEligibility = document.getElementById('specificEligibility');
        specificEligibility.style.display = this.checked ? 'none' : 'block';
        
        // Clear eligibility list if "All Students" is checked
        if (this.checked) {
            document.getElementById('eligibilityList').innerHTML = '';
        }
    });
    
    // Handle add eligibility button
    document.getElementById('addEligibilityBtn').addEventListener('click', function() {
        const facultySelect = document.getElementById('faculty');
        const departmentSelect = document.getElementById('department');
        
        const facultyId = facultySelect.value;
        const departmentId = departmentSelect.value;
        
        if (!facultyId) {
            alert('Silakan pilih fakultas');
            return;
        }
        
        const facultyText = facultySelect.options[facultySelect.selectedIndex].text;
        const departmentText = departmentId ? departmentSelect.options[departmentSelect.selectedIndex].text : 'Semua Jurusan';
        
        // Check if this eligibility already exists
        const existingItems = document.querySelectorAll('.eligibility-item');
        for (let i = 0; i < existingItems.length; i++) {
            const item = existingItems[i];
            const itemFacultyId = item.getAttribute('data-faculty-id');
            const itemDepartmentId = item.getAttribute('data-department-id') || '';
            
            if (itemFacultyId === facultyId && itemDepartmentId === departmentId) {
                alert('Eligibilitas ini sudah ditambahkan');
                return;
            }
        }
        
        // Create eligibility item
        const eligibilityList = document.getElementById('eligibilityList');
        const eligibilityItem = document.createElement('div');
        eligibilityItem.className = 'eligibility-item flex justify-between items-center bg-gray-50 p-3 rounded-lg mb-2';
        eligibilityItem.setAttribute('data-faculty-id', facultyId);
        eligibilityItem.setAttribute('data-department-id', departmentId);
        
        eligibilityItem.innerHTML = `
            <div>
                <p class="font-medium">${facultyText}</p>
                <p class="text-sm text-gray-600">${departmentText}</p>
                <input type="hidden" name="eligibility[${eligibilityList.children.length}][faculty_id]" value="${facultyId}">
                <input type="hidden" name="eligibility[${eligibilityList.children.length}][department_id]" value="${departmentId}">
            </div>
            <button type="button" class="remove-eligibility text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        eligibilityList.appendChild(eligibilityItem);

        // Hide the add button if at least one eligibility exists
        if (eligibilityList.children.length > 0) {
            document.getElementById('addEligibilityBtn').style.display = 'none';
        }

        // Add event listener to remove button
        eligibilityItem.querySelector('.remove-eligibility').addEventListener('click', function() {
            eligibilityItem.remove();
            // Show the add button if no eligibility exists
            if (eligibilityList.children.length === 0) {
                document.getElementById('addEligibilityBtn').style.display = '';
            }
        });
        
        // Reset selections
        facultySelect.value = '';
        departmentSelect.value = '';
        departmentSelect.disabled = true;
    });
    
    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-eligibility').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.eligibility-item').remove();
            // Show the add button if no eligibility exists
            if (document.getElementById('eligibilityList').children.length === 0) {
                document.getElementById('addEligibilityBtn').style.display = '';
            }
        });
    });

    // Hide add button on page load if eligibility exists
    if (document.getElementById('eligibilityList').children.length > 0) {
        document.getElementById('addEligibilityBtn').style.display = 'none';
    }
    
    // Handle form submission
    document.getElementById('editElectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        
        // Convert to JSON
        const jsonData = {};
        formData.forEach((value, key) => {
            // Handle eligibility array
            if (key.startsWith('eligibility')) {
                if (!jsonData.eligibility) {
                    jsonData.eligibility = [];
                }
                
                const matches = key.match(/eligibility\[(\d+)\]\[([^\]]+)\]/);
                if (matches) {
                    const index = parseInt(matches[1]);
                    const field = matches[2];
                    
                    if (!jsonData.eligibility[index]) {
                        jsonData.eligibility[index] = {};
                    }
                    
                    jsonData.eligibility[index][field] = value;
                }
            } else {
                jsonData[key] = value;
            }
        });
        
        // Send data to server
        fetch(`<?= base_url('/api/admin/elections') ?>/${jsonData.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.error) {
                alert('Error: ' + result.error);
            } else {
                alert('Pemilihan berhasil diperbarui');
                window.location.href = '<?= base_url('/admin/elections') ?>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memperbarui pemilihan');
        });
    });
});
</script>