<div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <h2 class="text-center text-3xl font-extrabold text-gray-900">
                <i class="fas fa-vote-yea text-primary mr-2"></i>
                E-Voting BEM
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Platform voting digital yang aman dan transparan
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
        <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10 border border-gray-200">
            <div class="mb-6 text-center">
                <h3 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-user-plus text-primary mr-2"></i> Register Mahasiswa
                </h3>
                <p class="text-sm text-gray-500 mt-1">Daftar untuk mengikuti pemilihan BEM</p>
            </div>
            
            <form id="registerForm" class="space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <!-- NIM -->
                    <div>
                        <label for="nim" class="block text-sm font-medium text-gray-700">
                            NIM <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-400"></i>
                            </div>
                            <input type="text" id="nim" name="nim" required
                                class="pl-10 block w-full pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary transition duration-150 ease-in-out sm:text-sm"
                                placeholder="Masukkan NIM">
                        </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="name" name="name" required
                                class="pl-10 block w-full pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary transition duration-150 ease-in-out sm:text-sm"
                                placeholder="Masukkan nama lengkap">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password" required minlength="6"
                                class="pl-10 block w-full pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary transition duration-150 ease-in-out sm:text-sm"
                                placeholder="Masukkan password">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i> Password minimal 6 karakter
                        </p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password_confirm" name="password_confirm" required minlength="6"
                                class="pl-10 block w-full pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary transition duration-150 ease-in-out sm:text-sm"
                                placeholder="Konfirmasi password">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <!-- Fakultas -->
                    <div>
                        <label for="faculty" class="block text-sm font-medium text-gray-700">
                            Fakultas <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-university text-gray-400"></i>
                            </div>
                            <select id="faculty" name="faculty_id" required
                                class="pl-10 block w-full pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary transition duration-150 ease-in-out sm:text-sm appearance-none">
                                <option value="">Pilih Fakultas</option>
                                <?php foreach ($faculties as $faculty): ?>
                                    <option value="<?= $faculty['id'] ?>"><?= esc($faculty['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Jurusan -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700">
                            Jurusan <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-graduation-cap text-gray-400"></i>
                            </div>
                            <select id="department" name="department_id" required
                                class="pl-10 block w-full pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary transition duration-150 ease-in-out sm:text-sm appearance-none">
                                <option value="">Pilih Jurusan</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus text-primary-hover group-hover:text-white"></i>
                        </span>
                        Register
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            Atau
                        </span>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="<?= base_url('/login') ?>" class="font-medium text-primary hover:text-primary-hover transition-colors">
                            Login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Direct JavaScript for Faculty-Department Relationship -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get form elements
    const registerForm = document.getElementById('registerForm');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirm');
    const facultySelect = document.getElementById('faculty');
    const departmentSelect = document.getElementById('department');
    
    // Add password confirmation validation
    if (registerForm) {
        // Remove direct submit handler - let app.js handle it
        // Just keep the real-time validation
        
        // Add real-time validation for password confirmation
        confirmPasswordField.addEventListener('input', function() {
            if (passwordField.value === confirmPasswordField.value) {
                confirmPasswordField.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                confirmPasswordField.classList.add('border-green-500', 'focus:ring-green-500', 'focus:border-green-500');
            } else {
                confirmPasswordField.classList.remove('border-green-500', 'focus:ring-green-500', 'focus:border-green-500');
                confirmPasswordField.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            }
        });
    }
    
    // Faculty-department relationship
    if (facultySelect && departmentSelect) {
        console.log('Faculty and Department selects found');
        
        // Add event listener to faculty select
        facultySelect.addEventListener('change', function() {
            const facultyId = this.value;
            console.log('Faculty changed to:', facultyId);
            
            if (!facultyId) {
                departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
                return;
            }
            
            departmentSelect.innerHTML = '<option value="">Loading...</option>';
            
            // Fetch departments for selected faculty
            const url = `<?= base_url('api/departments') ?>/${facultyId}`;
            console.log('Fetching departments from:', url);
            
            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(departments => {
                    console.log('Departments data:', departments);
                    departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
                    
                    if (Array.isArray(departments)) {
                        if (departments.length === 0) {
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
                        console.error('Expected array but got:', typeof departments);
                        departmentSelect.innerHTML = '<option value="">Error: Invalid data format</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
                    departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
                });
        });
        
        console.log('Event listener attached to faculty select');
    } else {
        console.error('Faculty or Department select not found');
    }
});
</script>