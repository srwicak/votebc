<!-- Alpine.js for dropdowns and interactive UI components -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Tailwind Elements for additional components -->
<script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>

<!-- Global JavaScript Configuration -->
<script>
    // Global JavaScript Configuration
    const BASE_URL = 'http://localhost:8080';
    const API_BASE_URL = 'http://localhost:8080/api';
    const AUTH_TOKEN = '<?= session()->get('auth_token') ?>';
    
    // Debug info (hapus di production)
    console.log('Base URL:', BASE_URL);
    console.log('API Base URL:', API_BASE_URL);
    console.log('Auth Token:', AUTH_TOKEN ? 'Present' : 'Not present');
    
    // Tailwind-specific initialization
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any Tailwind-specific components here
        
        // Handle form validation styling with Tailwind
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('invalid', () => {
                    input.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                });
                
                input.addEventListener('input', () => {
                    if (input.validity.valid) {
                        input.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                        input.classList.add('border-gray-300', 'focus:ring-primary', 'focus:border-primary');
                    }
                });
            });
        });
    });
</script>

<!-- Application Scripts -->
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>