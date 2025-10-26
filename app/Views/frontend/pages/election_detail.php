<div class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <nav class="py-3" aria-label="breadcrumb">
                <ol class="flex text-sm">
                    <li class="flex items-center">
                        <a href="<?= base_url('/elections') ?>" class="text-primary hover:text-primary-hover">Pemilihan</a>
                        <svg class="h-4 w-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li class="text-gray-700"><?= esc($election['title']) ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-primary text-white px-6 py-4">
                    <h4 class="text-xl font-semibold"><?= esc($election['title']) ?></h4>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap -mx-4">
                        <div class="w-full md:w-2/3 px-4 mb-6 md:mb-0">
                            <p class="mb-4"><?= esc($election['description']) ?></p>
                            
                            <div class="flex flex-wrap -mx-3 mb-6">
                                <div class="w-full md:w-1/2 px-3 mb-4 md:mb-0">
                                    <p class="mb-2"><span class="font-semibold">Level:</span> <?= isset($election['level']) ? ucfirst($election['level']) : '<span class="text-red-600">(tidak ada data)</span>' ?></p>
                                    <p><span class="font-semibold">Status:</span>
                                        <?php
                                        switch ($election['status']) {
                                            case 'active': echo '<span class="text-emerald-600">Aktif</span>'; break;
                                            case 'draft': echo '<span class="text-amber-600">Draft</span>'; break;
                                            case 'completed': echo '<span class="text-gray-600">Selesai</span>'; break;
                                            default: echo ucfirst($election['status']);
                                        }
                                        ?>
                                    </p>
                                </div>
                                <div class="w-full md:w-1/2 px-3">
                                    <p class="mb-2"><span class="font-semibold">Mulai:</span> <?= date('d M Y H:i', strtotime($election['start_time'])) ?></p>
                                    <p><span class="font-semibold">Selesai:</span> <?= date('d M Y H:i', strtotime($election['end_time'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/3 px-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h6 class="font-semibold mb-2">Informasi</h6>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-user text-gray-500 mr-1"></i> Dibuat oleh: <?= esc($election['creator_name']) ?><br>
                                    <i class="fas fa-clock text-gray-500 mr-1"></i> Dibuat: <?= date('d M Y H:i', strtotime($election['created_at'])) ?>
                                </p>
                                
                                <?php if ($election['status'] === 'active' && time() <= strtotime($election['end_time'])): ?>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <h6 class="font-semibold text-sm mb-2">Sisa Waktu Pemilihan:</h6>
                                    <div class="bg-blue-50 text-blue-800 px-3 py-2 rounded-md text-center">
                                        <span id="election-countdown" class="font-mono text-lg"></span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Hasil pemilihan akan ditampilkan otomatis setelah waktu berakhir.</p>
                                </div>
                                <?php elseif ($election['status'] === 'completed' || time() > strtotime($election['end_time'])): ?>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="bg-emerald-50 text-emerald-800 px-3 py-2 rounded-md text-center">
                                        <span class="font-medium"><i class="fas fa-check-circle mr-1"></i> Pemilihan telah selesai</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <?php if ($hasVoted && $userVote): ?>
                        <!-- User Vote Status Section -->
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-6 mb-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-emerald-800 mb-2">
                                            <i class="fas fa-vote-yea mr-2"></i>Anda Telah Melakukan Voting
                                        </h4>
                                        <p class="text-emerald-700 mb-3">
                                            Terima kasih atas partisipasi Anda dalam pemilihan ini. Vote Anda telah tercatat pada 
                                            <span class="font-medium"><?= date('d M Y \p\u\k\u\l H:i', strtotime($userVote['voted_at'])) ?></span>
                                        </p>
                                        
                                        <div class="flex flex-wrap gap-3">
                                            <a href="<?= base_url('verify-vote/' . $userVote['id']) ?>" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                <i class="fas fa-shield-check mr-2"></i>
                                                Verifikasi Vote Saya
                                            </a>
                                            
                                            <?php if ($userBlockchainVote && $userBlockchainVote['tx_hash']): ?>
                                                <a href="https://sepolia.etherscan.io/tx/<?= $userBlockchainVote['tx_hash'] ?>" 
                                                   target="_blank" rel="noopener noreferrer"
                                                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fas fa-external-link-alt mr-2"></i>
                                                    Lihat di Etherscan
                                                </a>
                                                
                                                <div class="flex items-center text-sm text-emerald-600 bg-emerald-100 px-3 py-2 rounded-lg">
                                                    <i class="fas fa-link mr-2"></i>
                                                    <span class="font-mono text-xs" title="Transaction Hash: <?= $userBlockchainVote['tx_hash'] ?>">
                                                        TX: <?= substr($userBlockchainVote['tx_hash'], 0, 10) ?>...<?= substr($userBlockchainVote['tx_hash'], -8) ?>
                                                    </span>
                                                </div>
                                                
                                                <?php if (isset($userBlockchainVote['vote_hash']) && $userBlockchainVote['vote_hash']): ?>
                                                <div class="flex items-center text-sm text-emerald-600 bg-emerald-100 px-3 py-2 rounded-lg">
                                                    <i class="fas fa-fingerprint mr-2"></i>
                                                    <span class="font-mono text-xs" title="Vote Hash: <?= $userBlockchainVote['vote_hash'] ?>">
                                                        Vote: <?= substr($userBlockchainVote['vote_hash'], 0, 10) ?>...<?= substr($userBlockchainVote['vote_hash'], -8) ?>
                                                    </span>
                                                </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($user && $user['role'] === 'admin'): ?>
                        <?php 
                        // Check if voting period has started (current time >= start time)
                        $votingStarted = time() >= strtotime($election['start_time']);
                        $electionEnded = $election['status'] === 'completed' || time() > strtotime($election['end_time']);
                        ?>
                        
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-semibold"><i class="fas fa-users mr-2"></i> Kandidat</h5>
                            <?php if (!$votingStarted): ?>
                                <button type="button" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm transition-colors" onclick="document.getElementById('modal-kandidat').classList.remove('hidden')">
                                    <i class="fas fa-plus mr-1"></i> Tambah Kandidat
                                </button>
                            <?php elseif ($electionEnded): ?>
                                <div class="text-sm px-3 py-1 bg-gray-100 text-gray-600 rounded-lg">
                                    <i class="fas fa-lock mr-1"></i> Pemilihan telah berakhir
                                </div>
                            <?php else: ?>
                                <div class="text-sm px-3 py-1 bg-blue-100 text-blue-700 rounded-lg">
                                    <i class="fas fa-clock mr-1"></i> Pemilihan sedang berlangsung
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Modal Tambah Kandidat -->
                        <div id="modal-kandidat" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                                <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" onclick="document.getElementById('modal-kandidat').classList.add('hidden')">
                                    <i class="fas fa-times"></i>
                                </button>
                                <h4 class="text-xl font-bold mb-4">Tambah Kandidat</h4>
                                
                                <?php if ($electionEnded): ?>
                                <div class="bg-gray-50 border-l-4 border-gray-400 text-gray-700 p-3 mb-3 rounded">
                                    <p><i class="fas fa-lock mr-2"></i>Pemilihan telah berakhir. Kandidat tidak dapat ditambahkan.</p>
                                </div>
                                <?php elseif ($votingStarted): ?>
                                <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-800 p-3 mb-3 rounded">
                                    <p><i class="fas fa-clock mr-2"></i>Pemilihan sedang berlangsung. Kandidat tidak dapat ditambahkan.</p>
                                </div>
                                <?php endif; ?>
                                
                                <form id="form-kandidat" <?= $votingStarted ? 'class="opacity-50 pointer-events-none"' : '' ?>>
                                    <div class="mb-3">
                                        <input type="text" name="nim_ketua" class="border rounded w-full p-2 mb-2" placeholder="NIM Ketua" required <?= $votingStarted ? 'disabled' : '' ?>>
                                        <input type="text" name="nim_wakil" class="border rounded w-full p-2" placeholder="NIM Wakil" required <?= $votingStarted ? 'disabled' : '' ?>>
                                    </div>
                                    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded" <?= $votingStarted ? 'disabled' : '' ?>>Tambah</button>
                                </form>
                                <div id="modal-error" class="text-red-600 mt-2"></div>
                                <hr class="my-4">
                                <h5 class="font-semibold mb-2">Daftar Kandidat (Batch Belum Disimpan)</h5>
                                <div id="modal-batch-list"></div>
                                <hr class="my-4">
                                <h5 class="font-semibold mb-2">Kandidat Sudah Tersimpan</h5>
                                <?php if ($electionEnded): ?>
                                    <div class="bg-gray-50 border-l-4 border-gray-400 text-gray-700 p-3 mb-3 rounded">
                                        <p><i class="fas fa-lock mr-2"></i>Pemilihan telah berakhir. Kandidat tidak dapat ditambah atau dihapus.</p>
                                    </div>
                                <?php elseif ($votingStarted): ?>
                                    <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-800 p-3 mb-3 rounded">
                                        <p><i class="fas fa-clock mr-2"></i>Pemilihan sedang berlangsung. Kandidat tidak dapat ditambah atau dihapus.</p>
                                    </div>
                                <?php endif; ?>
                                
                                <div id="modal-candidates-list">
                                    <?php foreach ($candidates as $candidate): ?>
                                        <div class="flex items-center justify-between py-2 border-b">
                                            <span><?= esc($candidate['candidate_name']) ?> &amp; <?= esc($candidate['vice_candidate_name']) ?></span>
                                            <?php if (!$votingStarted): ?>
                                                <button class="text-red-600 hover:text-red-800" onclick="deleteCandidate(<?= $candidate['id'] ?>)"><i class="fas fa-trash"></i> Hapus</button>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-sm"><i class="fas fa-lock"></i> Terkunci</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (!$votingStarted): ?>
                                    <button id="save-all-candidates" class="bg-blue-600 text-white px-4 py-2 rounded mt-2">Simpan Semua</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <script>
                        function getBearerToken() {
                            return "<?= esc(session()->get('auth_token')) ?>";
                        }
                        let candidatePairs = [];
                        function renderBatchList() {
                            const batchList = document.getElementById('modal-batch-list');
                            batchList.innerHTML = '';
                            
                            // Check if voting period has started
                            const votingStarted = <?= (time() >= strtotime($election['start_time'])) ? 'true' : 'false' ?>;
                            const electionEnded = <?= ($election['status'] === 'completed' || time() > strtotime($election['end_time'])) ? 'true' : 'false' ?>;
                            
                            if (candidatePairs.length === 0) {
                                batchList.innerHTML = '<div class="text-gray-500">Belum ada pasangan kandidat ditambahkan.</div>';
                                return;
                            }
                            
                            candidatePairs.forEach((pair, idx) => {
                                const div = document.createElement('div');
                                div.className = 'flex items-center justify-between py-2 border-b';
                                
                                if (votingStarted) {
                                    div.innerHTML = `<span>${pair.nim_ketua} &amp; ${pair.nim_wakil}</span>
                                        <span class="text-gray-400 text-sm"><i class="fas fa-lock"></i> Terkunci</span>`;
                                } else {
                                    div.innerHTML = `<span>${pair.nim_ketua} &amp; ${pair.nim_wakil}</span>
                                        <button class='text-red-600 hover:text-red-800' onclick='removeBatchPair(${idx})'><i class='fas fa-trash'></i> Hapus</button>`;
                                }
                                
                                batchList.appendChild(div);
                            });
                        }
                        function removeBatchPair(idx) {
                            // Check if voting period has started
                            const votingStarted = <?= (time() >= strtotime($election['start_time'])) ? 'true' : 'false' ?>;
                            const electionEnded = <?= ($election['status'] === 'completed' || time() > strtotime($election['end_time'])) ? 'true' : 'false' ?>;
                            
                            if (electionEnded) {
                                alert('Pemilihan telah berakhir. Kandidat tidak dapat dihapus.');
                                return;
                            }
                            
                            if (votingStarted) {
                                alert('Periode voting telah dimulai. Kandidat tidak dapat dihapus.');
                                return;
                            }
                            
                            candidatePairs.splice(idx, 1);
                            renderBatchList();
                        }
                        document.getElementById('form-kandidat').addEventListener('submit', function(e) {
                            e.preventDefault();
                            
                            // Check if voting period has started
                            const votingStarted = <?= (time() >= strtotime($election['start_time'])) ? 'true' : 'false' ?>;
                            const electionEnded = <?= ($election['status'] === 'completed' || time() > strtotime($election['end_time'])) ? 'true' : 'false' ?>;
                            
                            if (electionEnded) {
                                alert('Pemilihan telah berakhir. Kandidat tidak dapat ditambahkan.');
                                return;
                            }
                            
                            if (votingStarted) {
                                alert('Periode voting telah dimulai. Kandidat tidak dapat ditambahkan.');
                                return;
                            }
                            
                            const nim_ketua = this.nim_ketua.value.trim();
                            const nim_wakil = this.nim_wakil.value.trim();
                            const errorDiv = document.getElementById('modal-error');
                            
                            if (!nim_ketua || !nim_wakil) return;
                            
                            if (nim_ketua === nim_wakil) {
                                errorDiv.textContent = 'NIM ketua dan wakil tidak boleh sama.';
                                return;
                            }
                            
                            candidatePairs.push({ nim_ketua, nim_wakil });
                            this.nim_ketua.value = '';
                            this.nim_wakil.value = '';
                            errorDiv.textContent = '';
                            renderBatchList();
                        });
                        document.getElementById('save-all-candidates')?.addEventListener('click', function() {
                            // Check if voting period has started
                            const votingStarted = <?= (time() >= strtotime($election['start_time'])) ? 'true' : 'false' ?>;
                            const electionEnded = <?= ($election['status'] === 'completed' || time() > strtotime($election['end_time'])) ? 'true' : 'false' ?>;
                            
                            if (electionEnded) {
                                alert('Pemilihan telah berakhir. Kandidat tidak dapat ditambahkan.');
                                return;
                            }
                            
                            if (votingStarted) {
                                alert('Periode voting telah dimulai. Kandidat tidak dapat ditambahkan.');
                                return;
                            }
                            
                            const errorDiv = document.getElementById('modal-error');
                            errorDiv.textContent = '';
                            
                            if (candidatePairs.length === 0) {
                                errorDiv.textContent = 'Batch kosong. Tambahkan pasangan kandidat terlebih dahulu.';
                                return;
                            }
                            
                            fetch('<?= base_url('api/admin/candidates/paired') ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': 'Bearer ' + getBearerToken()
                                },
                                body: JSON.stringify({ election_id: <?= $election['id'] ?>, level: "<?= isset($election['level']) ? $election['level'] : '' ?>", pairs: candidatePairs })
                            })
                            .then(res => res.json())
                            .then(data => {
                                console.log('Response data:', data); // Tambahkan log untuk debugging
                                
                                // Periksa format respons yang benar (data.status dan data.data.results)
                                if (data.status === 'success' && data.data && data.data.results) {
                                    let messages = data.data.results.map(r => `${r.nim_ketua} & ${r.nim_wakil}: ${r.status === 'success' ? 'Berhasil' : r.message}`).join('\n');
                                    errorDiv.textContent = messages;
                                    candidatePairs = [];
                                    renderBatchList();
                                    location.reload(); // Refresh halaman
                                } else if (data.results) {
                                    // Format respons lama, tetap mendukung untuk kompatibilitas
                                    let messages = data.results.map(r => `${r.nim_ketua} & ${r.nim_wakil}: ${r.status === 'success' ? 'Berhasil' : r.message}`).join('\n');
                                    errorDiv.textContent = messages;
                                    candidatePairs = [];
                                    renderBatchList();
                                    location.reload(); // Refresh halaman
                                } else if (data.error) {
                                    errorDiv.textContent = data.error + ' (' + JSON.stringify(candidatePairs) + ') - ' + JSON.stringify(data);
                                } else {
                                    // Jika format tidak sesuai ekspektasi
                                    errorDiv.textContent = 'Format respons tidak sesuai: ' + JSON.stringify(data);
                                }
                            })
                            .catch(error => {
                                errorDiv.textContent = 'Error: ' + error.message;
                                console.error('Fetch error:', error);
                            });
                        });
                        function deleteCandidate(id) {
                            // Check if voting period has started
                            const votingStarted = <?= (time() >= strtotime($election['start_time'])) ? 'true' : 'false' ?>;
                            const electionEnded = <?= ($election['status'] === 'completed' || time() > strtotime($election['end_time'])) ? 'true' : 'false' ?>;
                            
                            if (electionEnded) {
                                alert('Pemilihan telah berakhir. Kandidat tidak dapat dihapus.');
                                return;
                            }
                            
                            if (votingStarted) {
                                alert('Periode voting telah dimulai. Kandidat tidak dapat dihapus.');
                                return;
                            }
                            
                            if (confirm('Apakah Anda yakin ingin menghapus kandidat ini?')) {
                                fetch('<?= base_url('api/admin/candidates/') ?>' + id, {
                                    method: 'DELETE',
                                    headers: {
                                        'Authorization': 'Bearer ' + getBearerToken()
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.error) {
                                        alert(data.error);
                                    } else {
                                        location.reload();
                                    }
                                })
                                .catch(error => {
                                    console.error('Error deleting candidate:', error);
                                    alert('Terjadi kesalahan saat menghapus kandidat: ' + error.message);
                                });
                            }
                        }
                        // Initial render for batch list
                        renderBatchList();
                        </script>
                    <?php else: ?>
                        <?php if ($hasVoted): ?>
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-lg font-semibold"><i class="fas fa-users mr-2"></i> Kandidat</h5>
                            </div>
                        <?php else: ?>
                            <h5 class="text-lg font-semibold mb-4"><i class="fas fa-users mr-2"></i> Kandidat</h5>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if (empty($candidates)): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                            <div class="flex">
                                <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                                <p>Belum ada kandidat untuk pemilihan ini</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php 
                        // Cek apakah pemilihan sudah berakhir
                        $isElectionEnded = $election['status'] === 'completed' || time() > strtotime($election['end_time']);
                        ?>
                        
                        <div class="flex flex-wrap -mx-3">
                            <?php foreach ($candidates as $candidate): ?>
                                <div class="w-full md:w-1/2 lg:w-1/3 px-3 mb-6">
                                    <div class="bg-white rounded-lg shadow-md border border-gray-100 h-full">
                        <div class="p-5 text-center">
                            <img src="<?= !empty($candidate['photo']) ? base_url($candidate['photo']) : base_url('assets/img/user-placeholder.svg') ?>"
                                 alt="<?= esc($candidate['candidate_name']) ?>"
                                 class="rounded-full mx-auto mb-4 w-24 h-24 object-cover">                                            <h5 class="font-semibold mb-1"><?= esc($candidate['candidate_name']) ?> - <?= esc($candidate['vice_candidate_name']) ?></h5>
                                            <p class="text-gray-500 text-sm mb-4"><?= esc($candidate['candidate_department_name'] ?: 'Tidak ada jurusan') ?> - <?= esc($candidate['vice_candidate_department_name'] ?: 'Tidak ada jurusan') ?></p>
                                            
                                            <div class="text-left mt-4">
                                                <p class="text-sm mb-2"><span class="font-semibold">Visi:</span><br><?= esc($candidate['vision']) ?></p>
                                                <p class="text-sm mb-4"><span class="font-semibold">Misi:</span><br><?= esc($candidate['mission']) ?></p>
                                            </div>
                                            
                                            <!-- Tombol Detail Kandidat -->
                                            <div class="mb-4">
                                                <button type="button" 
                                                        class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm"
                                                        onclick="showCandidateDetail(<?= $candidate['id'] ?>)">
                                                    <i class="fas fa-info-circle mr-1"></i> Detail Kandidat
                                                </button>
                                            </div>
                                            
                                            <?php if ($isElectionEnded): ?>
                                                <!-- Result indicator will be inserted here by JavaScript -->
                                                <div class="candidate-result" data-candidate-id="<?= $candidate['id'] ?>">
                                                    <?php
                                                    $voteModel = new \App\Models\VoteModel(); 
                                                    $totalVotes = $voteModel->where('election_id', $election['id'])->countAllResults();
                                                    $candidateVotes = $voteModel->where(['election_id' => $election['id'], 'candidate_id' => $candidate['id']])->countAllResults();
                                                    $percentage = $totalVotes > 0 ? round(($candidateVotes / $totalVotes) * 100, 2) : 0;
                                                    ?>
                                                    <div class="w-full bg-gray-200 rounded-full h-6 mb-2">
                                                        <div class="bg-blue-600 h-6 rounded-full text-xs text-white font-medium text-center p-0.5 leading-none" 
                                                             style="width: <?= $percentage ?>%">
                                                            <?= $percentage ?>%
                                                        </div>
                                                    </div>
                                                    <p class="text-sm font-semibold">
                                                        <?= $candidateVotes ?> Suara
                                                    </p>
                                                </div>
                                            <?php elseif (!$hasVoted && $election['status'] === 'active' && time() >= strtotime($election['start_time']) && time() <= strtotime($election['end_time'])): ?>
                                                <button class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors vote-button"
                                                        data-election="<?= $election['id'] ?>"
                                                        data-candidate="<?= $candidate['id'] ?>"
                                                        data-candidate-name="<?= esc($candidate['candidate_name']) ?>"
                                                        onclick="showVoteConfirmation(this)">
                                                    <i class="fas fa-vote-yea mr-1"></i> Vote
                                                </button>
                                            <?php elseif ($hasVoted): ?>
                                                <button class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
                                                    <i class="fas fa-check mr-1"></i> Sudah Voting
                                                </button>
                                            <?php else: ?>
                                                <button class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed" disabled>
                                                    <i class="fas fa-clock mr-1"></i> Voting Tidak Aktif
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($isElectionEnded): ?>
                        <!-- Election Results Section -->
                        <div class="mt-8 mb-6">
                            <h4 class="text-lg font-semibold mb-4"><i class="fas fa-chart-bar mr-2"></i>Hasil Pemilihan</h4>
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                                <div id="electionResults" class="space-y-6">
                                    <!-- Results will load here -->
                                    <div class="text-center py-4">
                                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                                        <p class="mt-2 text-gray-600">Memuat hasil pemilihan...</p>
                                    </div>
                                </div>
                                
                                <hr class="my-6">
                                
                                <div class="mb-5">
                                    <h5 class="text-md font-semibold mb-3">Statistik Partisipasi</h5>
                                    <div id="participationStats" class="space-y-3">
                                        <!-- Participation stats will load here -->
                                        <div class="text-center py-4">
                                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                                            <p class="mt-2 text-gray-600">Memuat statistik partisipasi...</p>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($user && $user['role'] === 'admin'): ?>
                                <!-- Admin Actions for Election Results -->
                                <div id="adminResultActions" class="mt-6 pt-6 border-t border-gray-200">
                                    <h5 class="text-md font-semibold mb-3">Tindakan Admin</h5>
                                    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded mb-4">
                                        <div class="flex">
                                            <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                                            <p>Jika hasil pemilihan adalah seri, administrator dapat memilih untuk menyelenggarakan pemilihan ulang.</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button id="btnScheduleRunoff" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition-colors">
                                            <i class="fas fa-redo mr-1"></i> Jadwalkan Pemilihan Ulang
                                        </button>
                                        <button id="btnFinalizeResults" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm transition-colors">
                                            <i class="fas fa-check-circle mr-1"></i> Finalisasi Hasil
                                        </button>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Vote Confirmation Modal -->
            <div class="fixed inset-0 z-50 overflow-y-auto hidden" id="voteConfirmationModal" tabindex="-1" aria-labelledby="voteConfirmationModalLabel" aria-hidden="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fas fa-vote-yea text-emerald-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="voteConfirmationModalLabel">Konfirmasi Voting</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Apakah Anda yakin ingin memberikan suara untuk <span id="candidateName" class="font-semibold"></span>?</p>
                                        <p class="text-sm text-gray-500 mt-2">Perhatian: Anda tidak dapat mengubah pilihan setelah melakukan voting.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm" id="confirmVoteButton">
                                Ya, Saya Yakin
                            </button>
                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" id="cancelVoteButton">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Vote Success Modal -->
            <div class="fixed inset-0 z-50 overflow-y-auto hidden" id="voteSuccessModal" tabindex="-1" aria-labelledby="voteSuccessModalLabel" aria-hidden="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fas fa-check text-emerald-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="voteSuccessModalLabel">Voting Berhasil</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Terima kasih telah berpartisipasi dalam pemilihan ini.</p>
                                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                            <p class="text-sm font-medium text-gray-700 mb-1">Detail Transaksi:</p>
                                            <p class="text-xs text-gray-500 mb-1">Transaction Hash: <span id="transactionHash" class="font-mono"></span></p>
                                            <p class="text-xs text-gray-500 mb-1">Vote Hash: <span id="voteHash" class="font-mono"></span></p>
                                            <p class="text-xs text-gray-500">Anda dapat memverifikasi suara Anda di blockchain dengan hash transaksi di atas.</p>
                                        </div>
                                        <div class="mt-3">
                                            <a id="verifyVoteLink" href="#" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                                                <i class="fas fa-shield-check mr-1"></i> Verifikasi Vote di Blockchain
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm" id="closeSuccessButton">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Candidate Detail Modal -->
            <div class="fixed inset-0 z-50 overflow-y-auto hidden" id="candidateDetailModal" tabindex="-1" aria-labelledby="candidateDetailModalLabel" aria-hidden="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-white">
                            <!-- Modal Header -->
                            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-white" id="candidateDetailModalLabel">
                                    <i class="fas fa-user-circle mr-2"></i>Detail Kandidat
                                </h3>
                                <button type="button" class="text-white hover:text-gray-300 focus:outline-none" onclick="closeCandidateDetailModal()">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            
                            <!-- Modal Body -->
                            <div class="px-6 py-6" id="candidateDetailContent">
                                <div class="text-center py-8">
                                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                                    <p class="mt-2 text-gray-600">Memuat detail kandidat...</p>
                                </div>
                            </div>
                            
                            <!-- Modal Footer -->
                            <div class="bg-gray-50 px-6 py-3 flex justify-end">
                                <button type="button" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors" onclick="closeCandidateDetailModal()">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
            // Variables to store election and candidate IDs
            let currentElectionId = null;
            let currentCandidateId = null;
            
            // Function to show vote confirmation modal
            function showVoteConfirmation(button) {
                // Prevent default button behavior
                event.preventDefault();
                
                // Get election and candidate IDs from button data attributes
                currentElectionId = button.getAttribute('data-election');
                currentCandidateId = button.getAttribute('data-candidate');
                const candidateName = button.getAttribute('data-candidate-name');
                // Set candidate name in confirmation modal
                document.getElementById('candidateName').textContent = candidateName;
                
                // Show confirmation modal
                document.getElementById('voteConfirmationModal').classList.remove('hidden');
            }
            
            // Function to show candidate detail modal
            function showCandidateDetail(candidateId) {
                // Show modal
                document.getElementById('candidateDetailModal').classList.remove('hidden');
                
                // Reset content to loading state
                document.getElementById('candidateDetailContent').innerHTML = `
                    <div class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                        <p class="mt-2 text-gray-600">Memuat detail kandidat...</p>
                    </div>
                `;
                
                // Fetch candidate details
                fetch('<?= base_url('api/candidates') ?>/' + candidateId, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        document.getElementById('candidateDetailContent').innerHTML = `
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                                <div class="flex">
                                    <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                                    <p>Error: ${data.error}</p>
                                </div>
                            </div>
                        `;
                        return;
                    }
                    
                    const candidate = data.data;
                    
                    // Parse programs if it's a JSON string
                    let programs = [];
                    if (candidate.programs) {
                        try {
                            programs = typeof candidate.programs === 'string' ? JSON.parse(candidate.programs) : candidate.programs;
                        } catch (e) {
                            console.error('Error parsing programs:', e);
                            programs = [];
                        }
                    }
                    
                    // Generate programs HTML
                    let programsHTML = '';
                    if (programs && programs.length > 0) {
                        programsHTML = `
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold mb-3 text-gray-800">
                                    <i class="fas fa-tasks mr-2 text-blue-600"></i>Program Kerja
                                </h4>
                                <div class="space-y-3">
                        `;
                        
                        programs.forEach((program, index) => {
                            programsHTML += `
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                    <h5 class="font-semibold text-blue-900 mb-2">${index + 1}. ${program.title || program.name || 'Program ' + (index + 1)}</h5>
                                    <p class="text-blue-800 text-sm">${program.description || program.detail || program}</p>
                                </div>
                            `;
                        });
                        
                        programsHTML += `
                                </div>
                            </div>
                        `;
                    } else {
                        programsHTML = `
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold mb-3 text-gray-800">
                                    <i class="fas fa-tasks mr-2 text-blue-600"></i>Program Kerja
                                </h4>
                                <div class="bg-gray-50 border-l-4 border-gray-400 text-gray-700 p-4 rounded-r-lg">
                                    <p class="text-sm">Program kerja belum tersedia.</p>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Build complete candidate detail HTML
                    const detailHTML = `
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left Column - Photo and Basic Info -->
                            <div class="lg:col-span-1">
                                <div class="text-center">
                                    <!-- Candidate Pair Photo -->
                                    <div class="mb-6">
                                        <img src="${candidate.photo ? '<?= base_url() ?>/' + candidate.photo : '<?= base_url('assets/img/user-placeholder.svg') ?>'}" 
                                             alt="${candidate.candidate_name} & ${candidate.vice_candidate_name}" 
                                             class="rounded-lg mx-auto w-full max-w-sm object-cover border-4 border-gray-200">
                                        <h4 class="font-bold text-lg mt-4 text-center">${candidate.candidate_name} & ${candidate.vice_candidate_name}</h4>
                                        <p class="text-gray-600 text-center">${candidate.candidate_nim} & ${candidate.vice_candidate_nim}</p>
                                    </div>
                                    
                                    <!-- Individual Info -->
                                    <div class="grid grid-cols-1 gap-4 text-sm">
                                        <div class="bg-blue-50 p-3 rounded-lg">
                                            <h5 class="font-semibold text-blue-800 mb-1">Ketua</h5>
                                            <p class="font-medium">${candidate.candidate_name}</p>
                                            <p class="text-gray-600">${candidate.candidate_nim}</p>
                                            <p class="text-gray-500">${candidate.candidate_department_name || 'Tidak ada jurusan'}</p>
                                            ${candidate.candidate_faculty_name ? `<p class="text-gray-500">${candidate.candidate_faculty_name}</p>` : ''}
                                        </div>
                                        
                                        <div class="bg-green-50 p-3 rounded-lg">
                                            <h5 class="font-semibold text-green-800 mb-1">Wakil</h5>
                                            <p class="font-medium">${candidate.vice_candidate_name}</p>
                                            <p class="text-gray-600">${candidate.vice_candidate_nim}</p>
                                            <p class="text-gray-500">${candidate.vice_candidate_department_name || 'Tidak ada jurusan'}</p>
                                            ${candidate.vice_candidate_faculty_name ? `<p class="text-gray-500">${candidate.vice_candidate_faculty_name}</p>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column - Vision, Mission, and Programs -->
                            <div class="lg:col-span-2">
                                <!-- Vision -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold mb-3 text-gray-800">
                                        <i class="fas fa-eye mr-2 text-emerald-600"></i>Visi
                                    </h4>
                                    <div class="bg-emerald-50 border-l-4 border-emerald-400 p-4 rounded-r-lg">
                                        <p class="text-emerald-800">${candidate.vision || 'Visi belum tersedia.'}</p>
                                    </div>
                                </div>
                                
                                <!-- Mission -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold mb-3 text-gray-800">
                                        <i class="fas fa-bullseye mr-2 text-purple-600"></i>Misi
                                    </h4>
                                    <div class="bg-purple-50 border-l-4 border-purple-400 p-4 rounded-r-lg">
                                        <p class="text-purple-800">${candidate.mission || 'Misi belum tersedia.'}</p>
                                    </div>
                                </div>
                                
                                <!-- Programs -->
                                ${programsHTML}
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('candidateDetailContent').innerHTML = detailHTML;
                })
                .catch(error => {
                    console.error('Error fetching candidate details:', error);
                    document.getElementById('candidateDetailContent').innerHTML = `
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                            <div class="flex">
                                <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                                <p>Terjadi kesalahan saat memuat detail kandidat: ${error.message}</p>
                            </div>
                        </div>
                    `;
                });
            }
            
            // Function to close candidate detail modal
            function closeCandidateDetailModal() {
                document.getElementById('candidateDetailModal').classList.add('hidden');
            }
            
            // Function to load election results
                    function loadElectionResults(electionId) {
                // First update from server-side rendered data
                updateElectionResultsFromDom();
                
                // Then try to get real-time data from API
                fetch('<?= base_url('api/statistics/election') ?>/' + electionId, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Log the entire response for debugging
                    console.log('Statistics API response:', data);
                    
                    if (data.error) {
                        console.error('Error fetching results:', data.error);
                        document.getElementById('electionResults').innerHTML = `
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                                <div class="flex">
                                    <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                                    <p>Gagal memuat hasil: ${data.error}</p>
                                </div>
                            </div>
                        `;
                        return;
                    }
                    
                    // Update the results container
                    const resultsContainer = document.getElementById('electionResults');
                    
                    // Handle case when data or data.data is undefined
                    if (!data.data) {
                        resultsContainer.innerHTML = `
                            <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
                                <div class="flex">
                                    <i class="fas fa-exclamation-triangle mt-0.5 mr-2"></i>
                                    <p>Tidak ada data hasil pemilihan yang tersedia</p>
                                </div>
                            </div>
                        `;
                        return;
                    }
                    
                    // Handle case when no votes are cast
                    if (!data.data.results || data.data.results.length === 0 || data.data.total_votes === 0) {
                        resultsContainer.innerHTML = `
                            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                                <div class="flex">
                                    <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                                    <p>Tidak ada suara yang tercatat dalam pemilihan ini</p>
                                </div>
                            </div>
                            <div class="mt-4 text-center text-gray-600">
                                <p>Pemilihan telah berakhir tanpa ada suara yang masuk</p>
                            </div>
                        `;
                        
                        // Update all candidate cards to show 0 votes
                        document.querySelectorAll('.candidate-result').forEach(candidateElement => {
                            const progressBar = candidateElement.querySelector('.bg-blue-600');
                            const voteCount = candidateElement.querySelector('p');
                            
                            progressBar.style.width = '0%';
                            progressBar.textContent = '0%';
                            voteCount.textContent = '0 Suara';
                        });
                        
                        return;
                    }
                    
                    // Create the winner announcement
                    let winnerHTML = '';
                    if (data.data.results.length > 0) {
                        // Check for a tie between the top candidates
                        const topVoteCount = data.data.results[0].vote_count;
                        const tiedCandidates = data.data.results.filter(result => result.vote_count === topVoteCount);
                        
                        if (tiedCandidates.length > 1) {
                            // There's a tie between top candidates
                            winnerHTML = `
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                                    <h5 class="font-semibold text-amber-800 mb-2">
                                        <i class="fas fa-balance-scale text-amber-500 mr-2"></i>Hasil Seri
                                    </h5>
                                    <p class="text-amber-700 mb-3">
                                        Terdapat hasil seri antara ${tiedCandidates.length} kandidat teratas dengan perolehan ${topVoteCount} suara.
                                        Akan diadakan pemilihan ulang untuk kandidat dengan perolehan suara tertinggi yang seri.
                                    </p>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        ${tiedCandidates.map(candidate => {
                                            let candidateName = 'Unknown';
                                            let candidatePhoto = '<?= base_url("assets/img/user-placeholder.svg") ?>';
                                            
                                            if (candidate.candidate) {
                                                if (candidate.candidate.candidate_name && candidate.candidate.vice_candidate_name) {
                                                    candidateName = candidate.candidate.candidate_name + ' & ' + candidate.candidate.vice_candidate_name;
                                                } else if (candidate.candidate.user_name) {
                                                    candidateName = candidate.candidate.user_name;
                                                } else if (candidate.candidate.name) {
                                                    candidateName = candidate.candidate.name;
                                                }
                                                
                                                if (candidate.candidate.photo) {
                                                    candidatePhoto = candidate.candidate.photo.startsWith('http') ? candidate.candidate.photo : '<?= base_url() ?>/' + candidate.candidate.photo;
                                                }
                                            }
                                            
                                            return `
                                                <div class="flex items-center bg-white p-2 rounded-lg">
                                                    <img src="${candidatePhoto}" 
                                                         alt="${candidateName}" 
                                                         class="rounded-full w-8 h-8 object-cover mr-2">
                                                    <span class="text-sm font-medium">${candidateName}</span>
                                                </div>
                                            `;
                                        }).join('')}
                                    </div>
                                </div>
                            `;
                        } else {
                            // Clear winner
                            const winner = data.data.results[0];
                            let winnerName = 'Unknown';
                            let winnerPhoto = '<?= base_url("assets/img/user-placeholder.svg") ?>';
                            
                            if (winner.candidate) {
                                if (winner.candidate.candidate_name && winner.candidate.vice_candidate_name) {
                                    winnerName = winner.candidate.candidate_name + ' & ' + winner.candidate.vice_candidate_name;
                                } else if (winner.candidate.user_name) {
                                    winnerName = winner.candidate.user_name;
                                } else if (winner.candidate.name) {
                                    winnerName = winner.candidate.name;
                                }
                                
                                if (winner.candidate.photo) {
                                    winnerPhoto = winner.candidate.photo.startsWith('http') ? winner.candidate.photo : '<?= base_url() ?>/' + winner.candidate.photo;
                                }
                            }
                            
                            winnerHTML = `
                                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-4">
                                    <h5 class="font-semibold text-emerald-800 mb-2">
                                        <i class="fas fa-trophy text-yellow-500 mr-2"></i>Pemenang Pemilihan
                                    </h5>
                                    <div class="flex items-center">
                                        <img src="${winnerPhoto}" 
                                             alt="${winnerName}" 
                                             class="rounded-full w-16 h-16 object-cover mr-4">
                                        <div>
                                            <p class="font-semibold text-lg">${winnerName}</p>
                                            <p class="text-sm text-gray-600">Perolehan: ${winner.vote_count} suara (${winner.percentage}%)</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    }                    // Create the results table
                    let tableHTML = `
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 text-left text-sm font-semibold text-gray-700">Peringkat</th>
                                        <th class="py-2 px-4 text-left text-sm font-semibold text-gray-700">Kandidat</th>
                                        <th class="py-2 px-4 text-left text-sm font-semibold text-gray-700">Jumlah Suara</th>
                                        <th class="py-2 px-4 text-left text-sm font-semibold text-gray-700">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.data.results.forEach((result, index) => {
                        // Get candidate info from multiple possible sources
                        let candidateName = 'Unknown';
                        let candidateNim = '';
                        let candidatePhoto = '<?= base_url("assets/img/user-placeholder.svg") ?>';
                        
                        // Try to get data from result.candidate first
                        if (result.candidate) {
                            // Check for the correct field names returned by the API
                            if (result.candidate.candidate_name && result.candidate.vice_candidate_name) {
                                candidateName = result.candidate.candidate_name + ' & ' + result.candidate.vice_candidate_name;
                            } else if (result.candidate.user_name) {
                                candidateName = result.candidate.user_name;
                            } else if (result.candidate.name) {
                                candidateName = result.candidate.name;
                            }
                            
                            if (result.candidate.candidate_nim && result.candidate.vice_candidate_nim) {
                                candidateNim = result.candidate.candidate_nim + ' & ' + result.candidate.vice_candidate_nim;
                            } else if (result.candidate.user_nim) {
                                candidateNim = result.candidate.user_nim;
                            } else if (result.candidate.nim) {
                                candidateNim = result.candidate.nim;
                            }
                            
                            if (result.candidate.photo) {
                                candidatePhoto = result.candidate.photo.startsWith('http') ? result.candidate.photo : '<?= base_url() ?>/' + result.candidate.photo;
                            }
                        }
                        
                        // If still unknown, try to get from DOM
                        if (candidateName === 'Unknown' && result.candidate_id) {
                            const candidateElement = document.querySelector(`[data-candidate-id="${result.candidate_id}"]`);
                            if (candidateElement) {
                                const cardParent = candidateElement.closest('.border.border-gray-100');
                                if (cardParent) {
                                    const nameElement = cardParent.querySelector('h5.font-semibold');
                                    if (nameElement) {
                                        candidateName = nameElement.textContent.trim();
                                    }
                                    
                                    const imgElement = cardParent.querySelector('img');
                                    if (imgElement && imgElement.src) {
                                        candidatePhoto = imgElement.src;
                                    }
                                }
                            }
                        }
                        
                        tableHTML += `
                            <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                                <td class="py-2 px-4 text-sm">${index + 1}</td>
                                <td class="py-2 px-4 text-sm">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <img class="h-8 w-8 rounded-full object-cover" 
                                                 src="${candidatePhoto}" 
                                                 alt="${candidateName}">
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium">${candidateName}</p>
                                            <p class="text-xs text-gray-500">${candidateNim}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 px-4 text-sm">${result.vote_count}</td>
                                <td class="py-2 px-4 text-sm">${result.percentage}%</td>
                            </tr>
                        `;
                        
                        // Update candidate card progress bars if the candidate exists
                        // Get candidate ID from the result
                        const candidateId = result.candidate && result.candidate.id ? result.candidate.id : 
                                           (result.candidate_id ? result.candidate_id : null);
                        
                        if (candidateId) {
                            console.log(`Updating result for candidate ${candidateId}: ${result.vote_count} votes (${result.percentage}%)`);
                            
                            // Look for the candidate element using the ID
                            const candidateResultElement = document.querySelector(`.candidate-result[data-candidate-id="${candidateId}"]`);
                            
                            if (candidateResultElement) {
                                const progressBar = candidateResultElement.querySelector('.bg-blue-600') || candidateResultElement.querySelector('.rounded-full');
                                const voteCount = candidateResultElement.querySelector('p');
                                
                                if (progressBar) {
                                    progressBar.style.width = `${result.percentage}%`;
                                    progressBar.textContent = `${result.percentage}%`;
                                    
                                    // Add winner indicator if rank 1
                                    if (index === 0) {
                                        progressBar.className = 'bg-yellow-500 h-6 rounded-full text-xs text-white font-medium text-center p-0.5 leading-none';
                                        candidateResultElement.classList.add('winner');
                                        if (voteCount) {
                                            voteCount.innerHTML = `<span class="flex items-center justify-center"><i class="fas fa-trophy text-yellow-500 mr-1"></i> ${result.vote_count} Suara</span>`;
                                        }
                                    } else {
                                        if (voteCount) {
                                            voteCount.textContent = `${result.vote_count} Suara`;
                                        }
                                    }
                                } else {
                                    console.log(`Progress bar not found for candidate ${candidateId}`);
                                }
                            } else {
                                console.log(`Candidate result element not found for ID: ${candidateId}`);
                            }
                        }
                    });
                    
                    tableHTML += `
                                </tbody>
                            </table>
                        </div>
                        <p class="text-center mt-4 text-gray-600 text-sm">Total Suara: ${data.data.total_votes}</p>
                    `;
                    
                    resultsContainer.innerHTML = winnerHTML + tableHTML;
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('electionResults').innerHTML = `
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                            <div class="flex">
                                <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                                <p>Error: ${error.message}</p>
                            </div>
                        </div>
                    `;
                });
            }
            
            // Function to load participation statistics
            function loadParticipationStats(electionId) {
                fetch('<?= base_url('api/statistics/election') ?>/' + electionId + '/realtime', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Participation stats API response:', data);
                    
                    if (data.error) {
                        console.error('Error fetching participation stats:', data.error);
                        document.getElementById('participationStats').innerHTML = `
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                                <div class="flex">
                                    <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                                    <p>Gagal memuat statistik: ${data.error}</p>
                                </div>
                            </div>
                        `;
                        return;
                    }
                    
                    // Handle case when data or data.data is undefined
                    if (!data.data) {
                        // Try to fetch data from statistics endpoint as fallback
                        fetch('<?= base_url('api/statistics/election') ?>/' + electionId, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
                            }
                        })
                        .then(response => response.json())
                        .then(fallbackData => {
                            if (fallbackData.error || !fallbackData.data) {
                                document.getElementById('participationStats').innerHTML = `
                                    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
                                        <div class="flex">
                                            <i class="fas fa-exclamation-triangle mt-0.5 mr-2"></i>
                                            <p>Tidak ada data statistik partisipasi yang tersedia</p>
                                        </div>
                                    </div>
                                `;
                                return;
                            }
                            
                            // Use fallback data
                            renderParticipationStats(fallbackData.data, electionId);
                        })
                        .catch(error => {
                            console.error('Fallback fetch error:', error);
                            document.getElementById('participationStats').innerHTML = `
                                <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
                                    <div class="flex">
                                        <i class="fas fa-exclamation-triangle mt-0.5 mr-2"></i>
                                        <p>Tidak ada data statistik partisipasi yang tersedia</p>
                                    </div>
                                </div>
                            `;
                        });
                        return;
                    }
                    
                    renderParticipationStats(data.data, electionId);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('participationStats').innerHTML = `
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                            <div class="flex">
                                <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                                <p>Error: ${error.message}</p>
                            </div>
                        </div>
                    `;
                });
            }
            
            function renderParticipationStats(data, electionId) {
                // Destructure participation data safely with default values
                const total_votes = data.total_votes || 0;
                const eligible_voters = data.eligible_voters || 0;
                const participation_rate = data.participation_rate || 0;
                
                // Check if election data exists
                if (!data.election) {
                    // Try to use the election data from the page context
                    const electionLevel = '<?= isset($election['level']) ? $election['level'] : '' ?>';
                    
                    // Get vote count if available
                    const voteModel = new Promise((resolve) => {
                        fetch('<?= base_url('api/votes/count') ?>/' + electionId, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
                            }
                        })
                        .then(response => response.json())
                        .then(data => resolve(data.data || { count: total_votes }))
                        .catch(() => resolve({ count: total_votes }));
                    });
                    
                    // Render basic stats without election info
                    voteModel.then(voteData => {
                        const voteCount = voteData.count || total_votes;
                        document.getElementById('participationStats').innerHTML = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <p class="text-blue-800 font-semibold text-lg">${voteCount}</p>
                                    <p class="text-blue-600 text-sm">Total Suara</p>
                                </div>
                                <div class="bg-amber-50 p-4 rounded-lg">
                                    <p class="text-amber-800 font-semibold text-lg">${eligible_voters > 0 ? ((voteCount / eligible_voters) * 100).toFixed(1) + '%' : 'N/A'}</p>
                                    <p class="text-amber-600 text-sm">Tingkat Partisipasi</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">
                                    Statistik partisipasi untuk pemilihan tingkat ${electionLevel || 'universitas'}.
                                </p>
                            </div>
                        `;
                    }).catch(error => {
                        console.error('Fetch error:', error);
                        document.getElementById('participationStats').innerHTML = `
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
                                <div class="flex">
                                    <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                                    <p>Error: ${error.message}</p>
                                </div>
                            </div>
                        `;
                    });
                    return;
                }
                
                // Create stats display
                let levelText = "pemilih";
                switch (data.election.level) {
                    case 'universitas':
                        levelText = "seluruh mahasiswa universitas";
                        break;
                    case 'fakultas':
                        levelText = "mahasiswa fakultas";
                        break;
                    case 'jurusan':
                        levelText = "mahasiswa jurusan";
                        break;
                }
                
                const statsHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-blue-800 font-semibold text-lg">${total_votes}</p>
                            <p class="text-blue-600 text-sm">Total Suara</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-purple-800 font-semibold text-lg">${eligible_voters}</p>
                            <p class="text-purple-600 text-sm">Total Pemilih Eligible</p>
                        </div>
                        <div class="bg-amber-50 p-4 rounded-lg">
                            <p class="text-amber-800 font-semibold text-lg">${participation_rate}%</p>
                            <p class="text-amber-600 text-sm">Tingkat Partisipasi</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                            <div class="bg-green-600 h-4 rounded-full text-xs text-white text-center" style="width: ${participation_rate}%">${participation_rate}%</div>
                        </div>
                        <p class="text-sm text-gray-600">
                            Tingkat partisipasi berdasarkan ${levelText}. 
                            ${total_votes} dari ${eligible_voters} pemilih yang eligible telah menggunakan hak suara.
                        </p>
                    </div>
                `;
                
                document.getElementById('participationStats').innerHTML = statsHTML;
            }
            
            // Function to cast vote
            function castVote() {
                // Show loading state
                document.getElementById('confirmVoteButton').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...';
                document.getElementById('confirmVoteButton').disabled = true;
                document.getElementById('cancelVoteButton').disabled = true;
                
                // Add message about blockchain processing
                const modalContent = document.querySelector('#voteConfirmationModal .mt-2');
                modalContent.innerHTML = `
                    <p class="text-sm text-gray-500 mb-2">Memproses vote ke blockchain...</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full progress-bar" style="width: 0%"></div>
                    </div>
                `;
                
                // Animate progress bar
                const progressBar = document.querySelector('.progress-bar');
                let width = 0;
                const interval = setInterval(() => {
                    if (width >= 90) {
                        clearInterval(interval);
                    } else {
                        width += 5;
                        progressBar.style.width = width + '%';
                    }
                }, 300);
                
                // Send vote to server
                fetch('<?= base_url('/api/votes') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
                    },
                    body: JSON.stringify({
                        election_id: currentElectionId,
                        candidate_id: currentCandidateId
                    })
                })
                .then(response => {
                    clearInterval(interval);
                    progressBar.style.width = '100%';
                    
                    // Add a small delay to show the completed progress bar
                    setTimeout(() => {
                        // Hide confirmation modal
                        document.getElementById('voteConfirmationModal').classList.add('hidden');
                    }, 500);
                    
                    return response.json();
                })
                .then(result => {
                    if (result.error) {
                        console.error('Vote error:', result.error);
                        
                        // Show error in alert with more details
                        if (typeof result.error === 'object') {
                            alert('Error json: ' + JSON.stringify(result.error));
                        } else {
                            alert('Error result: ' + result.error);
                        }
                    } else if (result.data) {
                        console.log('Vote success:', result);
                        
                        // Show success modal with blockchain details
                        document.getElementById('transactionHash').textContent = result.data.transaction_hash || 'Pending';
                        document.getElementById('voteHash').textContent = result.data.vote_hash || 'N/A';
                        
                        // Set verification links
                        const verifyLink = document.getElementById('verifyVoteLink');
                        verifyLink.href = '<?= base_url('verify-vote') ?>/' + result.data.vote_id;
                        
                        // Add Etherscan and Transaction verification links if available
                        if (result.data.etherscan_url) {
                            const detailsDiv = document.querySelector('#voteSuccessModal .mt-4.p-4');
                            
                            // Create blockchain verification section
                            const blockchainVerifyDiv = document.createElement('div');
                            blockchainVerifyDiv.className = 'mt-3 pt-3 border-t border-gray-200';
                            blockchainVerifyDiv.innerHTML = `
                                <h4 class="text-sm font-semibold mb-2">Blockchain Verification</h4>
                                <div class="flex flex-col space-y-2">
                                    <a href="${result.data.etherscan_url}" target="_blank" rel="noopener noreferrer" 
                                       class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                                        <i class="fas fa-external-link-alt mr-1"></i> View on Etherscan
                                    </a>
                                </div>
                            `;
                            detailsDiv.appendChild(blockchainVerifyDiv);
                        }
                        
                        // Add blockchain status information
                        const statusText = result.simulation ? 'Simulation' : (result.status === 'pending' ? 'Pending' : 'Confirmed');
                        const statusClass = result.simulation ? 'text-blue-600' : (result.status === 'pending' ? 'text-yellow-600' : 'text-green-600');
                        
                        // Add status to the success modal
                        const detailsDiv = document.querySelector('#voteSuccessModal .mt-4.p-4');
                        const statusElement = document.createElement('p');
                        statusElement.className = `text-xs ${statusClass} mb-1 font-semibold`;
                        statusElement.textContent = `Status: ${statusText}`;
                        detailsDiv.insertBefore(statusElement, detailsDiv.firstChild);
                        
                        document.getElementById('voteSuccessModal').classList.remove('hidden');
                        
                        // Disable all vote buttons
                        document.querySelectorAll('.vote-button').forEach(button => {
                            button.disabled = true;
                            button.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                            button.classList.add('bg-gray-400', 'cursor-not-allowed');
                            button.innerHTML = '<i class="fas fa-check mr-1"></i> Sudah Voting';
                            button.removeAttribute('onclick');
                        });
                    } else {
                        // Unexpected response structure
                        console.error('Unexpected response structure:', result);
                        alert('Terjadi kesalahan: Response tidak sesuai format yang diharapkan. Response: ' + JSON.stringify(result));
                        
                        // Hide confirmation modal
                        document.getElementById('voteConfirmationModal').classList.add('hidden');
                        
                        // Reset button state
                        document.getElementById('confirmVoteButton').innerHTML = 'Ya, Saya Yakin';
                        document.getElementById('confirmVoteButton').disabled = false;
                        document.getElementById('cancelVoteButton').disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memproses vote: ' + error.message);
                    
                    // Hide confirmation modal
                    document.getElementById('voteConfirmationModal').classList.add('hidden');
                    
                    // Reset button state
                    document.getElementById('confirmVoteButton').innerHTML = 'Ya, Saya Yakin';
                    document.getElementById('confirmVoteButton').disabled = false;
                    document.getElementById('cancelVoteButton').disabled = false;
                });
            }
            
            // Function to update election results from DOM
            function updateElectionResultsFromDom() {
                const candidateResults = document.querySelectorAll('.candidate-result');
                if (candidateResults.length === 0) return;
                
                // Calculate total votes from DOM
                let totalVotes = 0;
                candidateResults.forEach(el => {
                    const voteText = el.querySelector('p').textContent;
                    const voteCount = parseInt(voteText.match(/\d+/)[0]) || 0;  // Extract number from "X Suara"
                    totalVotes += voteCount;
                });
                
                console.log(`Total votes from DOM: ${totalVotes}`);
                
                // Update percentages
                if (totalVotes > 0) {
                    candidateResults.forEach(el => {
                        const voteText = el.querySelector('p').textContent;
                        const voteCount = parseInt(voteText.match(/\d+/)[0]) || 0;
                        const percentage = totalVotes > 0 ? (voteCount / totalVotes) * 100 : 0;
                        const roundedPercentage = Math.round(percentage * 100) / 100;
                        
                        // Correctly select just the progress bar element (the inner div)
                        const progressBar = el.querySelector('.bg-blue-600');
                        if (progressBar) {
                            progressBar.style.width = `${roundedPercentage}%`;
                            progressBar.textContent = `${roundedPercentage}%`;
                        }
                    });
                }
            }
            
            // Set up event listeners when the DOM is loaded
            // Run initial DOM updates when page is interactive
            document.addEventListener('DOMContentLoaded', function() {
                // Check if election has ended and update progress bars immediately
                const isEnded = <?= ($election['status'] === 'completed' || time() > strtotime($election['end_time'])) ? 'true' : 'false' ?>;
                if (isEnded) {
                    console.log('Election has ended - updating progress bars immediately');
                    updateElectionResultsFromDom();
                }
                // Confirmation modal buttons
                document.getElementById('confirmVoteButton').addEventListener('click', castVote);
                document.getElementById('cancelVoteButton').addEventListener('click', function() {
                    document.getElementById('voteConfirmationModal').classList.add('hidden');
                });
                
                // Success modal close button
                document.getElementById('closeSuccessButton').addEventListener('click', function() {
                    document.getElementById('voteSuccessModal').classList.add('hidden');
                    location.reload();
                });
                
                // Close modals when clicking outside
                const modals = document.querySelectorAll('.fixed.inset-0.z-50');
                modals.forEach(modal => {
                    modal.addEventListener('click', function(event) {
                        if (event.target === modal) {
                            modal.classList.add('hidden');
                        }
                    });
                });
                
                // Close candidate detail modal when clicking outside or on backdrop
                document.getElementById('candidateDetailModal').addEventListener('click', function(event) {
                    if (event.target === this) {
                        closeCandidateDetailModal();
                    }
                });
                
                // Admin actions for tie scenarios
                const btnScheduleRunoff = document.getElementById('btnScheduleRunoff');
                const btnFinalizeResults = document.getElementById('btnFinalizeResults');
                
                if (btnScheduleRunoff) {
                    btnScheduleRunoff.addEventListener('click', function() {
                        if (confirm('Apakah Anda yakin ingin menjadwalkan pemilihan ulang untuk kandidat dengan suara tertinggi yang seri?')) {
                            // Get the top tied candidates
                            const candidateElements = document.querySelectorAll('.candidate-result');
                            const candidateVotes = [];
                            let maxVotes = 0;
                            
                            candidateElements.forEach(el => {
                                const candidateId = el.dataset.candidateId;
                                const voteText = el.querySelector('p').textContent;
                                // Extract the numeric part from "X Suara"
                                let voteCount = 0;
                                const match = voteText.match(/(\d+)/);
                                if (match && match[1]) {
                                    voteCount = parseInt(match[1]);
                                }
                                
                                // Track maximum votes
                                if (voteCount > maxVotes) {
                                    maxVotes = voteCount;
                                }
                                
                                candidateVotes.push({
                                    candidateId,
                                    voteCount
                                });
                            });
                            
                            // Get candidates with max votes
                            const tiedCandidates = candidateVotes.filter(c => c.voteCount === maxVotes);
                            
                            if (tiedCandidates.length > 1) {
                                const tiedIds = tiedCandidates.map(c => c.candidateId);
                                
                                // Call API to create runoff election
                                const electionId = <?= $election['id'] ?>;
                                fetch('<?= base_url('api/admin/elections') ?>/' + electionId + '/create-runoff', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
                                    },
                                    body: JSON.stringify({
                                        candidate_ids: tiedIds
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.error) {
                                        alert('Error: ' + data.error);
                                    } else if (data.data && data.data.runoff_election) {
                                        alert('Pemilihan ulang berhasil dibuat! ID: ' + data.data.runoff_election.id);
                                        window.location.href = '<?= base_url('election') ?>/' + data.data.runoff_election.id;
                                    } else {
                                        alert('Pemilihan ulang berhasil dibuat!');
                                        location.reload();
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Terjadi kesalahan: ' + error.message);
                                });
                                
                                // Redirect to election creation page for admin
                                if (confirm('Apakah Anda ingin dialihkan ke halaman pembuatan pemilihan baru?')) {
                                    window.location.href = '<?= base_url('admin/elections/create') ?>';
                                }
                            } else {
                                alert('Tidak ada kandidat yang seri dengan perolehan suara tertinggi. Tidak perlu pemilihan ulang.');
                            }
                        }
                    });
                }
                
                if (btnFinalizeResults) {
                    btnFinalizeResults.addEventListener('click', function() {
                        if (confirm('Apakah Anda yakin ingin memfinalisasi hasil pemilihan ini?')) {
                            // Make API call to finalize results
                            const electionId = <?= $election['id'] ?>;
                            fetch('<?= base_url('api/admin/elections') ?>/' + electionId + '/finalize', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    alert('Error: ' + data.error);
                                } else if (data.data) {
                                    let message = 'Hasil pemilihan telah difinalisasi.';
                                    
                                    if (data.data.has_tie) {
                                        message += '\\n\\nPeringatan: Terdapat hasil seri!\\n';
                                        message += `Jumlah suara tertinggi: ${data.data.max_votes}\\n`;
                                        message += 'Kandidat yang seri:\\n';
                                        data.data.tied_candidates.forEach((candidate, index) => {
                                            message += `${index + 1}. ${candidate.candidate_name}`;
                                            if (candidate.vice_candidate_name) {
                                                message += ` & ${candidate.vice_candidate_name}`;
                                            }
                                            message += '\\n';
                                        });
                                        message += '\\nAnda dapat membuat pemilihan ulang menggunakan tombol "Jadwalkan Pemilihan Ulang".';
                                    }
                                    
                                    alert(message);
                                    location.reload();
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Terjadi kesalahan: ' + error.message);
                            });
                        }
                    });
                }
                
                // Check if election is ended and load results if needed
                const isElectionEnded = <?= ($election['status'] === 'completed' || time() > strtotime($election['end_time'])) ? 'true' : 'false' ?>;
                const electionId = <?= $election['id'] ?>;
                const endTime = <?= strtotime($election['end_time']) ?> * 1000; // Convert to milliseconds
                
                // Log candidate result elements for debugging
                console.log('Candidate result elements:');
                document.querySelectorAll('.candidate-result').forEach(el => {
                    console.log(`Candidate ID: ${el.dataset.candidateId}`, el);
                });
                
                if (isElectionEnded) {
                    // Update progress bars immediately based on DOM
                    updateElectionResultsFromDom();
                    
                    // Election has already ended, load results from API
                    loadElectionResults(electionId);
                    loadParticipationStats(electionId);
                } else {
                    // Election is still active, set timer to check when it ends
                    const now = new Date().getTime();
                    const timeUntilEnd = endTime - now;
                    
                    if (timeUntilEnd > 0) {
                        console.log(`Election ends in ${Math.floor(timeUntilEnd / 1000)} seconds`);
                        
                        // Set timer to automatically refresh when election ends
                        setTimeout(function() {
                            console.log("Election has ended, reloading page to show results");
                            window.location.reload();
                        }, timeUntilEnd);
                        
                        // Display countdown timer
                        const countdownInterval = setInterval(function() {
                            const currentTime = new Date().getTime();
                            const remainingTime = endTime - currentTime;
                            
                            if (remainingTime <= 0) {
                                clearInterval(countdownInterval);
                                return;
                            }
                            
                            const hours = Math.floor((remainingTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);
                            
                            // Update countdown element if it exists
                            const countdownElement = document.getElementById('election-countdown');
                            if (countdownElement) {
                                countdownElement.textContent = `${hours}h ${minutes}m ${seconds}s`;
                            }
                        }, 1000);
                    }
                }
                
                // Fallback mechanism for election that has ended but results/stats aren't showing
                if (isElectionEnded) {
                    // First run updateElectionResultsFromDom immediately to show initial results
                    updateElectionResultsFromDom();
                    
                    // Then set a timeout to check if API loaded properly
                    setTimeout(function() {
                        const electionResults = document.getElementById('electionResults');
                        const participationStats = document.getElementById('participationStats');
                        
                        console.log('Running fallback mechanism check');
                        
                        // If results are still loading or showing an error, display calculated results from DOM
                        if (electionResults && (electionResults.innerHTML.includes('Memuat hasil pemilihan') || 
                            electionResults.innerHTML.includes('Error') || 
                            electionResults.innerHTML.includes('Tidak ada data hasil pemilihan'))) {
                            
                            // Try to calculate results from DOM
                            const candidateResults = [];
                            const candidateElements = document.querySelectorAll('.candidate-result');
                            let totalVotes = 0;
                            
                            candidateElements.forEach(el => {
                                const candidateId = el.dataset.candidateId;
                                const voteText = el.querySelector('p').textContent;
                                // Extract the numeric part from "X Suara"
                                let voteCount = 0;
                                const match = voteText.match(/(\d+)/);
                                if (match && match[1]) {
                                    voteCount = parseInt(match[1]);
                                }
                                console.log(`Candidate ${candidateId}: "${voteText}" -> ${voteCount} votes`);
                                
                                totalVotes += voteCount;
                                
                                candidateResults.push({
                                    candidateId,
                                    voteCount
                                });
                            });
                            
                            // Always show results, even if totalVotes is 0
                            {
                                // Sort by vote count descending
                                candidateResults.sort((a, b) => b.voteCount - a.voteCount);
                                
                                // Check for ties
                                const topVoteCount = candidateResults[0].voteCount;
                                const tiedCandidates = candidateResults.filter(result => result.voteCount === topVoteCount);
                                
                                let resultsHTML = '';
                                
                                if (tiedCandidates.length > 1) {
                                    // Handle tie
                                    resultsHTML = `
                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                                            <h5 class="font-semibold text-amber-800 mb-2">
                                                <i class="fas fa-balance-scale text-amber-500 mr-2"></i>Hasil Seri
                                            </h5>
                                            <p class="text-amber-700 mb-3">
                                                Terdapat hasil seri antara ${tiedCandidates.length} kandidat teratas dengan perolehan ${topVoteCount} suara.
                                                Akan diadakan pemilihan ulang untuk kandidat dengan perolehan suara tertinggi yang seri.
                                            </p>
                                        </div>
                                    `;
                                }
                                
                                resultsHTML += `
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-700">Peringkat</th>
                                                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-700">Kandidat</th>
                                                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-700">Jumlah Suara</th>
                                                    <th class="py-2 px-4 text-left text-sm font-semibold text-gray-700">Persentase</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;
                                
                                candidateResults.forEach((result, index) => {
                                    const candidateElement = document.querySelector(`.candidate-result[data-candidate-id="${result.candidateId}"]`);
                                    let candidateName = 'Kandidat ' + (index + 1);
                                    
                                    if (candidateElement) {
                                        const cardParent = candidateElement.closest('.border.border-gray-100');
                                        if (cardParent) {
                                            const nameElement = cardParent.querySelector('h5.font-semibold');
                                            if (nameElement) {
                                                candidateName = nameElement.textContent;
                                            }
                                        }
                                    }
                                    
                                    const percentage = totalVotes > 0 ? ((result.voteCount / totalVotes) * 100).toFixed(2) : 0;
                                    
                                    resultsHTML += `
                                        <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                                            <td class="py-2 px-4 text-sm">${index + 1}</td>
                                            <td class="py-2 px-4 text-sm">${candidateName}</td>
                                            <td class="py-2 px-4 text-sm">${result.voteCount}</td>
                                            <td class="py-2 px-4 text-sm">${percentage}%</td>
                                        </tr>
                                    `;
                                });
                                
                                resultsHTML += `
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="text-center mt-4 text-gray-600 text-sm">Total Suara: ${totalVotes}</p>
                                `;
                                
                                electionResults.innerHTML = resultsHTML;
                            }
                        }
                        
                        // If participation stats are still loading or showing an error
                        if (participationStats && (participationStats.innerHTML.includes('Memuat statistik partisipasi') || 
                            participationStats.innerHTML.includes('Error') || 
                            participationStats.innerHTML.includes('Tidak ada data statistik'))) {
                            
                            // Calculate basic participation stats
                            const candidateElements = document.querySelectorAll('.candidate-result');
                            let totalVotes = 0;
                            
                            candidateElements.forEach(el => {
                                const voteText = el.querySelector('p').textContent;
                                // Extract the numeric part from "X Suara"
                                let voteCount = 0;
                                const match = voteText.match(/(\d+)/);
                                if (match && match[1]) {
                                    voteCount = parseInt(match[1]);
                                }
                                totalVotes += voteCount;
                            });
                            
                            // Get eligible voters if available
                            let eligibleVoters = <?= isset($eligibleVotersCount) ? $eligibleVotersCount : 0 ?>;
                            if (!eligibleVoters) {
                                // Try to estimate based on election level
                                const electionLevel = '<?= $election['level'] ?? "universitas" ?>';
                                if (electionLevel === 'universitas') {
                                    eligibleVoters = Math.max(totalVotes * 2, 100); // Estimate
                                } else if (electionLevel === 'fakultas') {
                                    eligibleVoters = Math.max(totalVotes * 1.5, 50); // Estimate
                                } else {
                                    eligibleVoters = Math.max(totalVotes * 1.2, 30); // Estimate
                                }
                            }
                            
                            const participationRate = eligibleVoters > 0 ? Math.round((totalVotes / eligibleVoters) * 100) : 0;
                            
                            const statsHTML = `
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <p class="text-blue-800 font-semibold text-lg">${totalVotes}</p>
                                        <p class="text-blue-600 text-sm">Total Suara</p>
                                    </div>
                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <p class="text-purple-800 font-semibold text-lg">${eligibleVoters}</p>
                                        <p class="text-purple-600 text-sm">Total Pemilih Eligible</p>
                                    </div>
                                    <div class="bg-amber-50 p-4 rounded-lg">
                                        <p class="text-amber-800 font-semibold text-lg">${participationRate}%</p>
                                        <p class="text-amber-600 text-sm">Tingkat Partisipasi</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                                        <div class="bg-green-600 h-4 rounded-full text-xs text-white text-center" style="width: ${participationRate}%">${participationRate}%</div>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        Tingkat partisipasi pemilihan. 
                                        ${totalVotes} dari ${eligibleVoters} pemilih yang eligible telah menggunakan hak suara.
                                    </p>
                                </div>
                            `;
                            
                            participationStats.innerHTML = statsHTML;
                        }
                    }, 5000); // Wait 5 seconds to see if API loads first
                }
            });
            </script>
        </div>
    </div>
</div>