<div class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <h2 class="text-2xl font-bold mb-6"><i class="fas fa-user-circle mr-2"></i> Profil Pengguna</h2>
        </div>
    </div>
    
    <?php if (session()->has('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= session()->get('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= session()->get('error') ?></span>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap -mx-3">
        <div class="w-full md:w-1/3 px-3 mb-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 text-center">
                    <?php if (!empty($user['face_image'])): ?>
                        <img src="<?= base_url($user['face_image']) ?>"
                             alt="Profile Picture"
                             class="rounded-full mx-auto mb-4 w-36 h-36 object-cover">
                    <?php else: ?>
                        <img src="<?= base_url('assets/img/user-placeholder.svg') ?>"
                             alt="Profile Picture"
                             class="rounded-full mx-auto mb-4 w-36 h-36 object-cover">
                    <?php endif; ?>
                    <h4 class="text-xl font-semibold mb-1"><?= esc($user['name']) ?></h4>
                    <p class="text-gray-500 mb-3"><?= esc($user['nim']) ?></p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white"><?= ucfirst($user['role']) ?></span>
                </div>
            </div>
        </div>
        
        <div class="w-full md:w-2/3 px-3 mb-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h5 class="font-semibold">Informasi Akun</h5>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">NAMA</label>
                                <p class="text-gray-900"><?= esc($user['name']) ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                                <p class="text-gray-900"><?= esc($user['faculty_name'] ?: '-') ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <p>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Aktif</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Nonaktif</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="w-full md:w-1/2 px-3">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                                <p class="text-gray-900"><?= esc($user['nim']) ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                                <p class="text-gray-900"><?= esc($user['department_name'] ?: '-') ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bergabung</label>
                                <p class="text-gray-900"><?= date('d M Y', strtotime($user['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-6 border-gray-200">
                    
                    <h6 class="text-lg font-semibold mb-4">Ubah Password</h6>
                    <form id="changePasswordForm">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full md:w-1/2 px-3 mb-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                                    <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="current_password" name="current_password">
                                </div>
                            </div>
                            <div class="w-full md:w-1/2 px-3 mb-4">
                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                    <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="new_password" name="new_password" minlength="6">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors">
                            <i class="fas fa-key mr-1"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    // Cek apakah user adalah kandidat utama
    $candidateModel = new \App\Models\CandidateModel();
    $isCandidate = $candidateModel->where('candidate_id', $user['id'])->findAll();

    // Cek apakah user adalah runningmate
    $isRunningmate = $candidateModel->where('vice_candidate_id', $user['id'])->findAll();

    if (!empty($isCandidate) or !empty($isRunningmate)):
    ?>
    <div class="w-full px-3 mb-6">
        <div class="bg-white rounded-lg shadow-md">
            <div class="border-b border-gray-200 px-6 py-4">
                <h5 class="font-semibold">Informasi Kandidat</h5>
            </div>
            <div class="p-6">
                <p class="mb-4">Anda terdaftar sebagai kandidat dalam pemilihan. Silakan kelola profil kandidat Anda untuk mengubah visi, misi, dan foto.</p>
                
                <a href="<?= base_url('candidate/profile') ?>" class="inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition">
                    <i class="fas fa-user-tie mr-1"></i> Kelola Profil Kandidat
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>