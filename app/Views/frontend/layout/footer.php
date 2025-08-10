        </main>

        <!-- Scripts Configuration -->
        <?= view('frontend/layout/scripts') ?>

        <footer class="bg-gray-800 text-white mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Top Footer Section -->
                <div class="py-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- About Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                <i class="fas fa-vote-yea text-secondary mr-2"></i>
                                Sistem E-Voting BEM Universitas
                            </h3>
                            <p class="text-gray-300 mb-4 text-sm">
                                Platform voting digital yang aman dan transparan berbasis blockchain.
                                Memastikan integritas dan keamanan dalam setiap pemilihan.
                            </p>
                            <div class="flex space-x-4 mt-6">
                                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                    <i class="fab fa-facebook-f text-lg"></i>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                    <i class="fab fa-twitter text-lg"></i>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                    <i class="fab fa-instagram text-lg"></i>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                    <i class="fab fa-youtube text-lg"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Contact Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                <i class="fas fa-address-card text-secondary mr-2"></i>
                                Kontak
                            </h3>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start">
                                    <i class="fas fa-envelope text-gray-400 mt-1 mr-3"></i>
                                    <span class="text-gray-300">bem@universitas.ac.id</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-phone text-gray-400 mt-1 mr-3"></i>
                                    <span class="text-gray-300">(021) 123-4567</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-gray-400 mt-1 mr-3"></i>
                                    <span class="text-gray-300">Gedung Rektorat Lt. 3, Kampus Universitas</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-clock text-gray-400 mt-1 mr-3"></i>
                                    <span class="text-gray-300">Senin - Jumat: 08.00 - 16.00 WIB</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="border-t border-gray-700 py-6">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-sm text-gray-400">
                            &copy; 2099 E-Voting BEM Universitas. Hak Cipta Dilindungi.
                        </p>
                        <div class="mt-4 md:mt-0">
                            <ul class="flex space-x-6 text-sm">
                                <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Kebijakan Privasi</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                                <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>