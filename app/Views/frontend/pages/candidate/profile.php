<div class="container mx-auto py-6 px-4">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Profil Kandidat</h1>
            
            <?php if(session()->has('notification')): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <p><?= session('notification') ?></p>
            </div>
            <?php endif; ?>
            
            <?php if(session()->has('success')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?= session('success') ?></p>
            </div>
            <?php endif; ?>
            
            <?php if(session()->has('error')): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p><?= session('error') ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Jika user memiliki lebih dari satu kandidat, tampilkan opsi untuk beralih -->
            <?php if(count($allCandidates) > 1): ?>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Pemilihan</label>
                <div class="flex flex-wrap">
                    <?php foreach($allCandidates as $c): ?>
                    <a href="<?= base_url('candidate-profile/switch/'.$c['id']) ?>" class="mr-2 mb-2 <?= ($c['id'] == $candidate['id']) ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' ?> hover:bg-blue-600 font-semibold py-2 px-4 rounded-full">
                        <?php 
                        $electionModel = new \App\Models\ElectionModel();
                        $elec = $electionModel->find($c['election_id']);
                        echo esc($elec['title']);
                        ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Informasi Kandidat -->
                <div class="w-full md:w-1/3">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Informasi Pemilihan</h2>
                        
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm">Pemilihan</p>
                            <p class="font-semibold"><?= esc($election['title']) ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm">Periode</p>
                            <p class="font-semibold">
                                <?= date('d M Y H:i', strtotime($election['start_time'])) ?> - 
                                <?= date('d M Y H:i', strtotime($election['end_time'])) ?>
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm">Status</p>
                            <p class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                                <?= $election['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($election['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') ?>">
                                <?= strtoupper($election['status']) ?>
                            </p>
                        </div>
                        
                        <h2 class="text-xl font-semibold mt-6 mb-4">Pasangan Kandidat</h2>
                        
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm">Kandidat Ketua</p>
                            <p class="font-semibold"><?= esc($candidate['candidate_name']) ?> (<?= esc($candidate['candidate_nim']) ?>)</p>
                            <p class="text-sm"><?= esc($candidate['candidate_department_name'] ?? 'Tidak ada jurusan') ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm">Kandidat Wakil</p>
                            <p class="font-semibold"><?= esc($candidate['vice_candidate_name']) ?> (<?= esc($candidate['vice_candidate_nim']) ?>)</p>
                            <p class="text-sm"><?= esc($candidate['vice_candidate_department_name'] ?? 'Tidak ada jurusan') ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Form Edit Profil -->
                <div class="w-full md:w-2/3">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Edit Profil Kandidat</h2>
                        
                        <form action="<?= base_url('candidate-profile/update') ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                            
                            <!-- Foto Pasangan -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="photo">
                                    Foto Pasangan
                                </label>
                                
                                <?php if (!empty($candidate['photo'])): ?>
                                <div class="mb-2">
                                    <img src="<?= $candidate['photo'] ?>" alt="Foto Kandidat" class="w-48 h-auto mb-2 border">
                                    <p class="text-sm text-gray-500">Foto yang sudah ada</p>
                                </div>
                                <?php endif; ?>
                                
                                <input type="file" name="photo" id="photo" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <p class="text-sm text-gray-500 mt-1">Upload foto pasangan kandidat. Format: JPG, PNG. Max: 2MB.</p>
                            </div>
                            
                            <!-- Visi -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="vision">
                                    Visi
                                </label>
                                <textarea name="vision" id="vision" rows="5" class="p-4 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?= $candidate['vision'] ?? '' ?></textarea>
                                <p class="text-sm text-gray-500 mt-1">Tuliskan visi pasangan kandidat secara jelas dan ringkas.</p>
                            </div>
                            
                            <!-- Misi -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="mission">
                                    Misi
                                </label>
                                <textarea name="mission" id="mission" rows="5" class="p-4 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?= $candidate['mission'] ?? '' ?></textarea>
                                <p class="text-sm text-gray-500 mt-1">Tuliskan misi pasangan kandidat dalam poin-poin yang jelas.</p>
                            </div>
                            
                            <!-- Program Kerja -->
                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="programs">
                                    Program Kerja (Opsional)
                                </label>
                                <textarea name="programs" id="programs" rows="5" class="p-4 mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?= $candidate['programs'] ?? '' ?></textarea>
                                <p class="text-sm text-gray-500 mt-1">Tuliskan program kerja unggulan jika ada.</p>
                            </div>
                            
                            <div class="flex items-center justify-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Catatan Kolaborasi -->
            <div class="mt-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h3 class="font-bold text-blue-700"><i class="fas fa-info-circle mr-2"></i>Catatan Penting</h3>
                <p class="text-blue-600 mt-2">
                    Profil kandidat ini dapat diedit oleh <strong>KEDUA anggota pasangan</strong> (ketua dan wakil).
                    Perubahan yang dilakukan oleh salah satu anggota akan terlihat oleh keduanya.
                </p>
                <p class="text-blue-600 mt-2">
                    <?php if($isMainCandidate): ?>
                    Anda adalah <strong>Kandidat Ketua</strong> dalam pasangan ini. Koordinasikan dengan wakil Anda untuk melengkapi profil.
                    <?php else: ?>
                    Anda adalah <strong>Kandidat Wakil</strong> dalam pasangan ini. Koordinasikan dengan ketua Anda untuk melengkapi profil.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>
