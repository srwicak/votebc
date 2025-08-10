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

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10 border border-gray-200">
            <div class="mb-6 text-center">
                <h3 class="text-xl font-semibold text-gray-800">
                    <i class="fas fa-sign-in-alt text-primary mr-2"></i> Login
                </h3>
            </div>
            
            <form id="loginForm" class="space-y-6">
                <div>
                    <label for="nim" class="block text-sm font-medium text-gray-700">
                        NIM
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

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="pl-10 block w-full pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary focus:border-primary transition duration-150 ease-in-out sm:text-sm"
                            placeholder="Masukkan password">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-primary-hover group-hover:text-white"></i>
                        </span>
                        Login
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
                        Belum punya akun?
                        <a href="<?= base_url('/register') ?>" class="font-medium text-primary hover:text-primary-hover transition-colors">
                            Register
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>