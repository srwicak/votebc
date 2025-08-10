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
                    <li class="text-gray-700">Buat Pemilihan Baru</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-primary text-white px-6 py-4">
            <h4 class="text-xl font-semibold">Buat Pemilihan Baru</h4>
        </div>
        <div class="p-6">
            <form id="createElectionForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilihan <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Lingkup <span class="text-red-500">*</span></label>
                        <select id="level" name="level" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Lingkup</option>
                            <option value="universitas">Universitas</option>
                            <option value="fakultas">Fakultas</option>
                            <option value="jurusan">Jurusan</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="start_time" name="start_time" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="end_time" name="end_time" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="draft" selected>Draft</option>
                        <option value="active">Aktif</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <h5 class="text-lg font-semibold mb-4">Eligibilitas Pemilih</h5>
                    
                    <div id="eligibilityContainer">
                        <div class="mb-4">
                            <div class="flex items-center mb-2">
                                <input type="checkbox" id="allStudents" name="all_students" class="mr-2">
                                <label for="allStudents" class="text-sm font-medium text-gray-700">Semua Mahasiswa</label>
                            </div>
                            <p class="text-xs text-gray-500">Jika dicentang, semua mahasiswa dapat berpartisipasi dalam pemilihan ini.</p>
                        </div>
                        
                        <div id="specificEligibility" class="space-y-4">
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
                                <!-- Eligibility items will be added here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- <div class="border-t border-gray-200 pt-4">
                    <h5 class="text-lg font-semibold mb-4">Pengaturan Blockchain</h5>
                    
                    <div class="mb-4">
                        <div class="flex items-center mb-2">
                            <input type="checkbox" id="useBlockchain" name="use_blockchain" class="mr-2" checked>
                            <label for="useBlockchain" class="text-sm font-medium text-gray-700">Gunakan Blockchain</label>
                        </div>
                        <p class="text-xs text-gray-500">Jika dicentang, hasil pemilihan akan dicatat di blockchain untuk verifikasi.</p>
                    </div>
                </div> -->
                
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
            departmentSelect.disabled = false;
            departmentSelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`<?= base_url('api/departments') ?>/${facultyId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Network response was not ok: ${response.status}`);
                    }
                    return response.json();
                })
                .then(departments => {
                    departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
                    
                    if (Array.isArray(departments) && departments.length > 0) {
                        departments.forEach(dept => {
                            const option = document.createElement('option');
                            option.value = dept.id;
                            option.textContent = dept.name;
                            departmentSelect.appendChild(option);
                        });
                    } else {
                        departmentSelect.innerHTML = '<option value="">Tidak ada jurusan</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
                    departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
                });
        } else {
            departmentSelect.disabled = true;
            departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
        }
    });
    
    // Handle "All Students" checkbox
    document.getElementById('allStudents').addEventListener('change', function() {
        const specificEligibility = document.getElementById('specificEligibility');
        specificEligibility.style.display = this.checked ? 'none' : 'block';
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
    
    // Add validation for date fields
    document.getElementById('end_time').addEventListener('change', function() {
        validateDates();
    });
    
    document.getElementById('start_time').addEventListener('change', function() {
        validateDates();
    });
    
    function validateDates() {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        if (startTime && endTime) {
            const startDate = new Date(startTime);
            const endDate = new Date(endTime);
            
            if (endDate <= startDate) {
                document.getElementById('end_time').setCustomValidity('Waktu selesai harus lebih besar dari waktu mulai');
                
                let errorMsg = document.getElementById('end_time_error');
                if (!errorMsg) {
                    errorMsg = document.createElement('p');
                    errorMsg.id = 'end_time_error';
                    errorMsg.className = 'text-red-500 text-sm mt-1';
                    document.getElementById('end_time').parentNode.appendChild(errorMsg);
                }
                errorMsg.textContent = 'Waktu selesai harus lebih besar dari waktu mulai';
            } else {
                document.getElementById('end_time').setCustomValidity('');
                
                const errorMsg = document.getElementById('end_time_error');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        }
    }
    
    // Function to format datetime for backend
    function formatDateTimeForBackend(dateTimeString) {
        if (!dateTimeString) return '';
        const date = new Date(dateTimeString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }
    
    // Handle form submission
    document.getElementById('createElectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate dates before submission
        validateDates();
        
        // Check if form is valid
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }
        
        // Get form data
        const formData = new FormData(this);
        
        // Convert to JSON
        const jsonData = {};
        formData.forEach((value, key) => {
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
        
        // Format datetime fields properly
        if (jsonData.start_time) {
            jsonData.start_time = formatDateTimeForBackend(jsonData.start_time);
        }
        if (jsonData.end_time) {
            jsonData.end_time = formatDateTimeForBackend(jsonData.end_time);
        }

        // Convert checkbox to boolean
        jsonData.use_blockchain = jsonData.use_blockchain === 'on' ? true : false;
        jsonData.all_students = jsonData.all_students === 'on' ? true : false;

        // Add status field
        jsonData.status = document.getElementById('status').value;

        // Remove individual faculty_id and department_id if they exist (from form fields)
        delete jsonData.faculty_id;
        delete jsonData.department_id;

        // --- Tambahkan logika target_id untuk fakultas ---
        if (jsonData.level === 'fakultas' && Array.isArray(jsonData.eligibility) && jsonData.eligibility.length > 0) {
            jsonData.target_id = jsonData.eligibility[0].faculty_id;
        }
        if (jsonData.level === 'jurusan' && Array.isArray(jsonData.eligibility) && jsonData.eligibility.length > 0) {
            jsonData.target_id = jsonData.eligibility[0].department_id;
        }
        // --- END ---

        console.log('Sending data:', jsonData);
        
        // Send data to server
        fetch('<?= base_url('/api/admin/elections') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(result => {
            if (result.error) {
                let errorMessage = 'Error: ';
                if (typeof result.error === 'object') {
                    errorMessage += JSON.stringify(result.error, null, 2);
                } else {
                    errorMessage += result.error;
                }
                alert(errorMessage);
                console.log(errorMessage);
            } else {
                alert('Pemilihan berhasil dibuat');
                window.location.href = '<?= base_url('/admin/elections') ?>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan pemilihan: ' + (error.message || 'Unknown error'));
        });
    });
});
</script>