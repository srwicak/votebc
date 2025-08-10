<nav class="bg-primary shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex-shrink-0 flex items-center">
                <a href="<?= base_url('/') ?>" class="flex items-center">
                    <i class="fas fa-vote-yea text-white text-xl mr-2"></i>
                    <span class="text-white font-bold text-xl">E-Voting BEM</span>
                </a>
            </div>
            
            <!-- Mobile menu button -->
            <div class="flex items-center -mr-2 sm:hidden">
                <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <?php if ($isLoggedIn): ?>
                    <!-- Main Navigation -->
                    <div class="flex space-x-4 mr-4">
                        <a href="<?= base_url('/dashboard') ?>"
                           class="<?= ($page == 'home' || $page == 'user-dashboard') ? 'bg-primary-hover' : 'hover:bg-primary-hover' ?> text-white px-3 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                        <a href="<?= base_url('/elections') ?>"
                           class="<?= ($page == 'elections' || $page == 'election-detail') ? 'bg-primary-hover' : 'hover:bg-primary-hover' ?> text-white px-3 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-vote-yea mr-1"></i> Pemilihan
                        </a>
                        
                        <?php if (isset($user) && isset($user['role']) && ($user['role'] === 'admin' || $user['role'] === 'operator')): ?>
                            <!-- Admin Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.away="open = false" class="text-white hover:bg-primary-hover px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center">
                                    <i class="fas fa-cog mr-1"></i> Admin
                                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                </button>
                                <div x-show="open" class="absolute left-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <a href="<?= base_url('/admin/elections') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pemilihan</a>
                                        <a href="<?= base_url('/admin/users') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Users</a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('/admin/academic') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Akademik</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center text-white hover:bg-primary-hover px-3 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-user mr-1"></i>
                            <span><?= isset($user) && isset($user['name']) ? esc($user['name']) : 'User' ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div x-show="open" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                            <a href="<?= base_url('/profile') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-circle mr-2"></i> Profil
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="<?= base_url('/logout') ?>" onclick="logout()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Login/Register Links -->
                    <div class="flex space-x-2">
                        <a href="<?= base_url('/login') ?>" class="text-white hover:bg-primary-hover px-3 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                        <a href="<?= base_url('/register') ?>" class="bg-white text-primary hover:bg-gray-100 px-3 py-2 rounded-md text-sm font-medium transition-all">
                            <i class="fas fa-user-plus mr-1"></i> Register
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state -->
    <div id="mobile-menu" class="sm:hidden hidden">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <?php if ($isLoggedIn): ?>
                <a href="<?= base_url('/dashboard') ?>"
                   class="<?= ($page == 'home' || $page == 'user-dashboard') ? 'bg-primary-hover' : '' ?> text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-home mr-1"></i> Dashboard
                </a>
                <a href="<?= base_url('/elections') ?>"
                   class="<?= ($page == 'elections' || $page == 'election-detail') ? 'bg-primary-hover' : '' ?> text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-vote-yea mr-1"></i> Pemilihan
                </a>
                
                <?php if (isset($user) && isset($user['role']) && ($user['role'] === 'admin' || $user['role'] === 'operator')): ?>
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="text-white w-full text-left px-3 py-2 rounded-md text-base font-medium flex justify-between items-center">
                            <span><i class="fas fa-cog mr-1"></i> Admin</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" class="pl-4 pb-2">
                            <?php if ($user['role'] === 'admin'): ?>
                                <a href="<?= base_url('/admin/elections') ?>" class="text-white block px-3 py-2 rounded-md text-base font-medium">Pemilihan</a>
                                <a href="<?= base_url('/admin/users') ?>" class="text-white block px-3 py-2 rounded-md text-base font-medium">Users</a>
                            <?php endif; ?>
                            <a href="<?= base_url('/admin/academic') ?>" class="text-white block px-3 py-2 rounded-md text-base font-medium">Akademik</a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="border-t border-primary-hover my-2"></div>
                
                <a href="<?= base_url('/profile') ?>" class="text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-user-circle mr-1"></i> Profil
                </a>
                <a href="<?= base_url('/logout') ?>" onclick="logout()" class="text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            <?php else: ?>
                <a href="<?= base_url('/login') ?>" class="text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </a>
                <a href="<?= base_url('/register') ?>" class="text-white block px-3 py-2 rounded-md text-base font-medium">
                    <i class="fas fa-user-plus mr-1"></i> Register
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Alerts -->
<?php if (session()->get('message')): ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-emerald-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm"><?= session()->get('message') ?></p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex rounded-md p-1.5 text-emerald-500 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (session()->get('error')): ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm"><?= session()->get('error') ?></p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    mobileMenu.classList.toggle('hidden');
});

function logout() {
    // Hapus session server-side
    fetch('<?= base_url('/logout') ?>', {
        method: 'GET'
    }).finally(() => {
        // Redirect ke login
        window.location.href = '<?= base_url('/login') ?>';
    });
}
</script>