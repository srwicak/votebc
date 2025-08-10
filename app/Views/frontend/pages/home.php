<section class="bg-primary text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Sistem E-Voting BEM Universitas</h1>
        <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">Platform voting digital yang aman, transparan, dan berbasis blockchain</p>
        <?php if (!$isLoggedIn): ?>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="<?= base_url('/login') ?>" class="inline-flex items-center px-6 py-3 bg-white text-primary font-medium rounded-md shadow-sm hover:bg-gray-100 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
                <a href="<?= base_url('/register') ?>" class="inline-flex items-center px-6 py-3 border border-white text-white font-medium rounded-md hover:bg-white hover:bg-opacity-10 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </a>
            </div>
        <?php else: ?>
            <div class="mt-8">
                <a href="<?= base_url('/dashboard') ?>" class="inline-flex items-center px-6 py-3 bg-white text-primary font-medium rounded-md shadow-sm hover:bg-gray-100 transition-colors">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="bg-white rounded-lg shadow-md p-8 transform transition-transform hover:scale-105">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary bg-opacity-10 rounded-full mb-4">
                    <i class="fas fa-shield-alt text-3xl text-primary"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Aman & Terpercaya</h3>
                <p class="text-gray-600">Teknologi blockchain memastikan setiap vote tidak dapat diubah dan transparan</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-8 transform transition-transform hover:scale-105">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-4">
                    <i class="fas fa-bolt text-3xl text-emerald-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Cepat & Efisien</h3>
                <p class="text-gray-600">Proses voting yang cepat dengan hasil real-time dan mudah diakses</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-8 transform transition-transform hover:scale-105">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <i class="fas fa-chart-bar text-3xl text-blue-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Transparan</h3>
                <p class="text-gray-600">Statistik voting dapat dilihat secara real-time oleh semua pengguna</p>
            </div>
        </div>
    </div>
</section>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold mb-6 text-gray-800">Tentang E-Voting BEM</h2>
                <p class="text-gray-600 mb-4">Sistem E-Voting BEM Universitas adalah platform digital yang dirancang khusus untuk memfasilitasi proses pemilihan umum dalam lingkungan kampus. Dengan menggabungkan teknologi modern dan keamanan blockchain, kami memastikan setiap suara yang diberikan memiliki nilai yang sama dan tidak dapat dipalsukan.</p>
                <p class="text-gray-600">Platform ini dikembangkan dengan tujuan meningkatkan partisipasi mahasiswa dalam proses demokrasi kampus serta memberikan transparansi penuh terhadap hasil pemilihan.</p>
            </div>
            <div class="flex justify-center">
                <img src="https://www.coe.int/documents/14181903/15917751/e-voting.jpg/a335973b-ad02-a9e5-8a77-ccc5c7e08e8b?t=1654858273000"
                     alt="E-Voting System" class="rounded-lg shadow-lg max-w-full h-auto">
            </div>
        </div>
    </div>
</section>