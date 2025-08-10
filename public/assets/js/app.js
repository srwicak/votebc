// Main App JavaScript
// Gunakan konstanta yang sudah didefinisikan di scripts.php

document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    setupEventListeners();
    loadPageSpecificScripts();
}

function setupEventListeners() {
    // Login form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    // Register form
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
        const facultySelect = document.getElementById('faculty');
        if (facultySelect) {
            facultySelect.addEventListener('change', loadDepartments);
        }
    }
    
    // Vote buttons
    const voteButtons = document.querySelectorAll('.vote-button');
    voteButtons.forEach(button => {
        button.addEventListener('click', handleVote);
    });
}

function handleLogin(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    // Log untuk debugging
    console.log('Login data:', data);
    
    fetch(`${API_BASE_URL}/auth/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        console.log('Login result:', result);
        if (result.error) {
            alert('Login gagal: ' + result.error);
        } else {
            // Special handling for seed user
            if (data.nim === 'seed') {
                console.log('Seed user login detected');
            }
            
            // Store token in session (via AJAX)
            fetch(`${BASE_URL}/set-session`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({token: result.token})
            })
            .then(response => {
                console.log('Session set response:', response);
                return response.json();
            })
            .then(sessionResult => {
                console.log('Session set result:', sessionResult);
                window.location.href = `${BASE_URL}/dashboard`;
            })
            .catch(error => {
                console.error('Session error:', error);
                // For seed user, we'll still redirect
                window.location.href = `${BASE_URL}/dashboard`;
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat login');
    });
}

function handleRegister(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    // Ensure all required fields are present and have values
    const requiredFields = ['nim', 'name', 'password', 'faculty_id', 'department_id'];
    const missingFields = requiredFields.filter(field => !data[field]);
    
    if (missingFields.length > 0) {
        console.error('Missing required fields:', missingFields);
        alert('Semua field harus diisi: ' + missingFields.join(', '));
        return;
    }
    
    // Validate passwords match
    if (data.password !== data.password_confirm) {
        alert('Password dan konfirmasi password tidak cocok!');
        return;
    }
    
    // Remove password_confirm field before sending to API
    delete data.password_confirm;
    
    // Enhanced logging for debugging
    console.log('Register data:', data);
    console.log('API URL:', `${API_BASE_URL}/auth/register`);
    
    // Check if API_BASE_URL is defined
    if (!API_BASE_URL) {
        console.error('API_BASE_URL is not defined!');
        alert('Konfigurasi aplikasi tidak lengkap. Silakan hubungi administrator.');
        return;
    }
    
    fetch(`${API_BASE_URL}/auth/register`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers.entries()]);
        
        // Check if the response is ok before parsing JSON
        if (!response.ok) {
            console.error('Server returned error status:', response.status);
            return response.text().then(text => {
                console.error('Error response text:', text);
                try {
                    // Try to parse as JSON anyway
                    return JSON.parse(text);
                } catch (e) {
                    // If it's not JSON, create an error object
                    throw new Error(`Server error: ${response.status} ${text}`);
                }
            });
        }
        
        return response.json().catch(err => {
            console.error('JSON parse error:', err);
            throw new Error('Failed to parse JSON response');
        });
    })
    .then(result => {
        console.log('Register result:', result);
        if (result.error) {
            // Handle error object properly
            let errorMessage = 'Register gagal: ';
            
            if (typeof result.error === 'object') {
                // If it's an object, format the error messages
                errorMessage += Object.values(result.error).join(', ');
            } else {
                // If it's a string, just append it
                errorMessage += result.error;
            }
            
            alert(errorMessage);
        } else {
            alert('Register berhasil! Silakan login.');
            window.location.href = `${BASE_URL}/login`;
        }
    })
    .catch(error => {
        console.error('Registration error:', error);
        console.error('Error details:', error.message);
        
        // Try to get more details if it's a network error
        if (error instanceof TypeError && error.message.includes('NetworkError')) {
            console.error('Network error - check if the server is running and accessible');
        }
        
        alert('Terjadi kesalahan saat register: ' + error.message);
    });
}

function loadDepartments() {
    const facultyId = this.value;
    const departmentSelect = document.getElementById('department');
    
    if (!facultyId) {
        departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
        return;
    }
    
    departmentSelect.innerHTML = '<option value="">Loading...</option>';
    
    // Enhanced debugging
    console.log(`Fetching departments for faculty ID: ${facultyId}`);
    console.log(`API URL: ${BASE_URL}/api/departments/${facultyId}`);
    
    // Add a small delay to ensure the DOM is updated
    setTimeout(() => {
        fetch(`${BASE_URL}/api/departments/${facultyId}`)
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers.entries()]);
                return response.json().catch(err => {
                    console.error('JSON parse error:', err);
                    throw new Error('Failed to parse JSON response');
                });
            })
            .then(departments => {
                console.log('Departments data:', departments);
                departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
                
                // Check if departments is an array
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
    }, 100);
}

function handleVote(e) {
    const button = e.target.closest('.vote-button');
    const electionId = button.dataset.election;
    const candidateId = button.dataset.candidate;
    
    if (!confirm('Apakah Anda yakin ingin memberikan vote?')) {
        return;
    }
    
    button.disabled = true;
    button.innerHTML = '<span class="loading"></span> Memproses...';
    
    fetch(`${API_BASE_URL}/votes`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${AUTH_TOKEN}`
        },
        body: JSON.stringify({
            election_id: electionId,
            candidate_id: candidateId
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-vote-yea"></i> Vote';
        } else {
            alert('Vote berhasil dicatat!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat voting');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-vote-yea"></i> Vote';
    });
}

function loadPageSpecificScripts() {
    // Load scripts based on current page
    const path = window.location.pathname;
    
    if (path.includes('/elections')) {
        loadElectionsData();
    }
}

function loadElectionsData() {
    // Load elections data for the page
    console.log('Loading elections data...');
}

// Tambahkan di akhir file app.js

// Auto-check authentication on page load (jika diperlukan)
document.addEventListener('DOMContentLoaded', function() {
    // Hanya untuk halaman tertentu
    const protectedPages = ['/dashboard', '/profile', '/elections', '/election/'];
    const currentPath = window.location.pathname;
    
    const isProtectedPage = protectedPages.some(page => currentPath.startsWith(BASE_URL + page));
    
    if (isProtectedPage && !AUTH_TOKEN) {
        // Redirect ke login jika mencoba akses halaman yang dilindungi tanpa token
        window.location.href = `${BASE_URL}/login`;
    }
});

// Export functions yang mungkin dibutuhkan di halaman lain
window.App = {
    handleLogin: handleLogin,
    handleRegister: handleRegister,
    handleVote: handleVote,
    logout: logout,
    checkAuth: checkAuth
};