<div class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <h2 class="text-2xl font-bold mb-6"><i class="fas fa-user-tie mr-2"></i> Profil Kandidat</h2>
        </div>
    </div>

    <?php if (session()->has('candidate_notification')): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium"><?= session()->get('candidate_notification') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    // Calculate profile completion percentage
    $completionItems = [
        !empty($candidate['vision']),
        !empty($candidate['mission']),
        !empty($candidate['programs']),
        !empty($candidate['photo'])
    ];
    $completedItems = array_filter($completionItems, function($item) { return $item === true; });
    $completionPercentage = count($completedItems) / count($completionItems) * 100;
    $isProfileIncomplete = $completionPercentage < 100;
    ?>

    <?php if ($isProfileIncomplete): ?>
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">Profil kandidat Anda belum lengkap</p>
                    <p class="text-sm mt-1">Lengkapi profil Anda untuk meningkatkan peluang terpilih dalam pemilihan.</p>
                    <div class="mt-2">
                        <div class="w-full bg-blue-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?= $completionPercentage ?>%"></div>
                        </div>
                        <p class="text-xs mt-1 text-right"><?= round($completionPercentage) ?>% lengkap</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

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
                    <?php if (!empty($candidate['photo'])): ?>
                        <img src="<?= $candidate['photo'] ?>"
                             alt="Candidate Photo"
                             class="rounded-full mx-auto mb-4 w-36 h-36 object-cover">
                    <?php else: ?>
                        <img src="<?= base_url('assets/img/user-placeholder.svg') ?>"
                             alt="Candidate Photo"
                             class="rounded-full mx-auto mb-4 w-36 h-36 object-cover">
                    <?php endif; ?>
                    <h4 class="text-xl font-semibold mb-1"><?= esc($user['name']) ?></h4>
                    <p class="text-gray-500 mb-3"><?= esc($user['nim']) ?></p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white">Kandidat</span>
                </div>
                
                <?php if (!empty($runningMate)): ?>
                <div class="border-t border-gray-200 p-6 text-center">
                    <h5 class="font-semibold mb-3">Wakil Kandidat</h5>
                    <?php if (!empty($runningMate['photo'])): ?>
                        <img src="<?= $runningMate['photo'] ?>"
                             alt="Vice Candidate Photo"
                             class="rounded-full mx-auto mb-4 w-24 h-24 object-cover">
                    <?php else: ?>
                        <img src="<?= base_url('assets/img/user-placeholder.svg') ?>"
                             alt="Vice Candidate Photo"
                             class="rounded-full mx-auto mb-4 w-24 h-24 object-cover">
                    <?php endif; ?>
                    <h4 class="text-lg font-semibold mb-1"><?= esc($runningMate['name']) ?></h4>
                    <p class="text-gray-500 mb-1"><?= esc($runningMate['nim']) ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-white rounded-lg shadow-md mt-4 p-6">
                <h5 class="font-semibold mb-3">Informasi Pemilihan</h5>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pemilihan</label>
                    <p class="text-gray-900"><?= esc($election['title']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <p class="text-gray-900"><?= ucfirst($election['level']) ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <p>
                        <?php if ($election['status'] === 'active'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Aktif</span>
                        <?php elseif ($election['status'] === 'completed'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Selesai</span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="w-full md:w-2/3 px-3 mb-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h5 class="font-semibold">Informasi Kandidat</h5>
                    <?php if ($isProfileIncomplete): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Perlu Dilengkapi
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Lengkap
                        </span>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <?php if ($isProfileIncomplete): ?>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mb-6">
                        <h3 class="text-sm font-medium text-yellow-800">Petunjuk Pengisian Profil Kandidat</h3>
                        <ul class="mt-2 text-sm text-yellow-700 list-disc pl-5 space-y-1">
                            <li>Visi: Tuliskan visi Anda sebagai kandidat dalam 1-2 paragraf</li>
                            <li>Misi: Jelaskan misi Anda dalam poin-poin yang jelas</li>
                            <li>Program Kerja: Uraikan program kerja yang akan Anda laksanakan jika terpilih</li>
                            <li>Foto: Unggah foto formal Anda dengan latar belakang polos</li>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form id="updateCandidateForm" action="<?= base_url('candidate/update/' . $candidate['id']) ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="vision" class="block text-sm font-medium text-gray-700 mb-1">
                                Visi <span class="text-red-500">*</span>
                                <?php if (empty($candidate['vision'])): ?>
                                <span class="text-red-500 text-xs font-normal ml-1">(Belum diisi)</span>
                                <?php endif; ?>
                            </label>
                            <textarea id="vision" name="vision" rows="3" class="w-full px-4 py-2 border <?= empty($candidate['vision']) ? 'border-red-300 bg-red-50' : 'border-gray-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Tuliskan visi Anda sebagai kandidat..."><?= esc($candidate['vision']) ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Contoh: "Menjadikan kampus sebagai pusat inovasi dan pengembangan karakter mahasiswa yang unggul."</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="mission" class="block text-sm font-medium text-gray-700 mb-1">
                                Misi <span class="text-red-500">*</span>
                                <?php if (empty($candidate['mission'])): ?>
                                <span class="text-red-500 text-xs font-normal ml-1">(Belum diisi)</span>
                                <?php endif; ?>
                            </label>
                            <textarea id="mission" name="mission" rows="5" class="w-full px-4 py-2 border <?= empty($candidate['mission']) ? 'border-red-300 bg-red-50' : 'border-gray-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Tuliskan misi-misi yang akan Anda lakukan..."><?= esc($candidate['mission']) ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Contoh: "1. Meningkatkan kualitas pembelajaran melalui program inovatif. 2. Mengembangkan soft skill mahasiswa melalui kegiatan ekstrakurikuler."</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="programs" class="block text-sm font-medium text-gray-700 mb-1">
                                Program Kerja
                                <?php if (empty($candidate['programs'])): ?>
                                <span class="text-red-500 text-xs font-normal ml-1">(Belum diisi)</span>
                                <?php endif; ?>
                            </label>
                            <textarea id="programs" name="programs" rows="5" class="w-full px-4 py-2 border <?= empty($candidate['programs']) ? 'border-red-300 bg-red-50' : 'border-gray-300' ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tuliskan program kerja yang akan Anda laksanakan jika terpilih..."><?= esc($candidate['programs']) ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Jelaskan program kerja konkret yang akan Anda laksanakan jika terpilih.</p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">
                                Foto
                                <?php if (empty($candidate['photo'])): ?>
                                <span class="text-red-500 text-xs font-normal ml-1">(Belum diisi)</span>
                                <?php endif; ?>
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="w-24 h-24 bg-gray-100 rounded-full overflow-hidden flex items-center justify-center">
                                    <img id="photoPreview" src="<?= !empty($candidate['photo']) ? $candidate['photo'] : base_url('assets/img/user-placeholder.svg') ?>" alt="Preview" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <input type="file" id="photo" name="photo" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Ukuran maksimal: 2MB.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mt-6">
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                            
                            <?php if ($isProfileIncomplete): ?>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i> Lengkapi profil untuk meningkatkan peluang terpilih
                            </span>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle photo preview
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>