// Authentication related functions

// Check if user is authenticated
function checkAuth() {
    // Gunakan AUTH_TOKEN dari konfigurasi global
    if (AUTH_TOKEN) {
        // Verify token with API
        fetch(`${API_BASE_URL}/auth/profile`, {
            headers: {
                'Authorization': `Bearer ${AUTH_TOKEN}`
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Redirect ke logout untuk membersihkan session
                window.location.href = `${BASE_URL}/logout`;
            }
        })
        .catch(() => {
            // Redirect ke logout untuk membersihkan session
            window.location.href = `${BASE_URL}/logout`;
        });
    }
}

// Logout function
function logout() {
    // Hapus session server-side (opsional)
    fetch(`${BASE_URL}/logout`, {
        method: 'GET'
    }).finally(() => {
        // Redirect ke login page
        window.location.href = `${BASE_URL}/login`;
    });
}