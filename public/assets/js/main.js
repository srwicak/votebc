// Main JavaScript for E-Voting App
const API_BASE_URL = '<?= base_url('/api') ?>';
let currentUser = null;
let authToken = localStorage.getItem('authToken');

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    checkAuthStatus();
    loadPageContent();
    setupEventListeners();
}

function checkAuthStatus() {
    if (authToken) {
        fetch(`${API_BASE_URL}/auth/profile`, {
            headers: {
                'Authorization': `Bearer ${authToken}`
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.error) {
                currentUser = data;
                updateUserInterface();
            } else {
                logout();
            }
        })
        .catch(() => {
            logout();
        });
    }
}

function updateUserInterface() {
    if (currentUser) {
        document.getElementById('auth-buttons').classList.add('d-none');
        document.getElementById('user-menu').classList.remove('d-none');
        document.getElementById('user-name').textContent = currentUser.name;
    } else {
        document.getElementById('auth-buttons').classList.remove('d-none');
        document.getElementById('user-menu').classList.add('d-none');
    }
}

function logout() {
    localStorage.removeItem('authToken');
    authToken = null;
    currentUser = null;
    updateUserInterface();
    window.location.href = '<?= base_url('/login') ?>';
}

function loadPageContent() {
    const path = window.location.pathname;
    
    if (path.includes('/elections')) {
        loadElections();
    } else if (path.includes('/profile')) {
        loadProfile();
    } else if (path.includes('/register')) {
        loadFaculties();
    }
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
        document.getElementById('faculty').addEventListener('change', loadDepartments);
    }
}

function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    fetch(`${API_BASE_URL}/auth/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Login failed: ' + data.error);
        } else {
            localStorage.setItem('authToken', data.token);
            window.location.href = '<?= base_url('/') ?>';
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function handleRegister(e) {
    e.preventDefault();
    
    const formData = {
        nim: document.getElementById('nim').value,
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        faculty_id: document.getElementById('faculty').value,
        department_id: document.getElementById('department').value
    };
    
    fetch(`${API_BASE_URL}/auth/register`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Registration failed: ' + data.error);
        } else {
            alert('Registration successful! Silakan login.');
            window.location.href = '<?= base_url('/login') ?>';
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function loadFaculties() {
    fetch(`${API_BASE_URL}/admin/faculties`)
        .then(response => response.json())
        .then(data => {
            const facultySelect = document.getElementById('faculty');
            facultySelect.innerHTML = '<option value="">Pilih Fakultas</option>';
            
            data.forEach(faculty => {
                const option = document.createElement('option');
                option.value = faculty.id;
                option.textContent = faculty.name;
                facultySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading faculties:', error));
}

function loadDepartments() {
    const facultyId = document.getElementById('faculty').value;
    if (!facultyId) return;
    
    fetch(`${API_BASE_URL}/admin/faculties/${facultyId}/departments`)
        .then(response => response.json())
        .then(data => {
            const departmentSelect = document.getElementById('department');
            departmentSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
            
            data.forEach(department => {
                const option = document.createElement('option');
                option.value = department.id;
                option.textContent = department.name;
                departmentSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading departments:', error));
}

function loadElections() {
    fetch(`${API_BASE_URL}/elections`)
        .then(response => response.json())
        .then(data => {
            displayElections(data);
        })
        .catch(error => {
            console.error('Error loading elections:', error);
            displayElections([]);
        });
}

function displayElections(elections) {
    const container = document.getElementById('elections-container');
    
    if (elections.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Tidak ada pemilihan aktif saat ini
                </div>
            </div>
        `;
        return;
    }
    
    let html = '';
    elections.forEach(election => {
        const isActive = election.status === 'active';
        const startTime = new Date(election.start_time);
        const endTime = new Date(election.end_time);
        const now = new Date();
        
        let statusBadge = '';
        let timeInfo = '';
        
        if (isActive && now >= startTime && now <= endTime) {
            statusBadge = '<span class="status-badge status-active">Aktif</span>';
            const timeLeft = Math.ceil((endTime - now) / (1000 * 60 * 60 * 24));
            timeInfo = `<div class="countdown-timer mt-2">
                <i class="fas fa-clock"></i> Berakhir dalam ${timeLeft} hari
            </div>`;
        } else if (election.status === 'draft') {
            statusBadge = '<span class="status-badge status-draft">Draft</span>';
        } else if (election.status === 'completed') {
            statusBadge = '<span class="status-badge status-completed">Selesai</span>';
        }
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card election-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">${election.title}</h5>
                            ${statusBadge}
                        </div>
                        <p class="card-text">${election.description || 'Tidak ada deskripsi'}</p>
                        <p class="text-muted">
                            <i class="fas fa-layer-group"></i> ${election.level}
                        </p>
                        <p class="text-muted">
                            <i class="fas fa-calendar"></i> 
                            ${startTime.toLocaleDateString('id-ID')} - ${endTime.toLocaleDateString('id-ID')}
                        </p>
                        ${timeInfo}
                        <button class="btn btn-primary w-100 mt-3" onclick="showElectionDetail(${election.id})">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function loadProfile() {
    if (!authToken) {
        window.location.href = '<?= base_url('/login') ?>';
        return;
    }
    
    fetch(`${API_BASE_URL}/auth/profile`, {
        headers: {
            'Authorization': `Bearer ${authToken}`
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.error) {
            document.getElementById('profile-name').textContent = data.name;
            document.getElementById('profile-email').textContent = data.email;
            document.getElementById('profile-nim').textContent = data.nim;
            document.getElementById('profile-role').textContent = data.role;
            document.getElementById('profile-faculty').textContent = data.faculty_name || '-';
            document.getElementById('profile-department').textContent = data.department_name || '-';
            document.getElementById('profile-status').textContent = data.status;
        }
    });
}