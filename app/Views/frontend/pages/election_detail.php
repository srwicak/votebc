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
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <?php if ($user && $user['role'] === 'admin'): ?>
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-semibold"><i class="fas fa-users mr-2"></i> Kandidat</h5>
                            <button type="button" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm transition-colors" onclick="document.getElementById('modal-kandidat').classList.remove('hidden')">
                                <i class="fas fa-plus mr-1"></i> Tambah Kandidat
                            </button>
                        </div>
                        <!-- Modal Tambah Kandidat -->
                        <div id="modal-kandidat" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                                <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" onclick="document.getElementById('modal-kandidat').classList.add('hidden')">
                                    <i class="fas fa-times"></i>
                                </button>
                                <h4 class="text-xl font-bold mb-4">Tambah Kandidat</h4>
                                <form id="form-kandidat">
                                    <div class="mb-3">
                                        <input type="text" name="nim_ketua" class="border rounded w-full p-2 mb-2" placeholder="NIM Ketua" required>
                                        <input type="text" name="nim_wakil" class="border rounded w-full p-2" placeholder="NIM Wakil" required>
                                    </div>
                                    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded">Tambah</button>
                                </form>
                                <div id="modal-error" class="text-red-600 mt-2">Error: </div>
                                <hr class="my-4">
                                <h5 class="font-semibold mb-2">Daftar Kandidat (Batch Belum Disimpan)</h5>
                                <div id="modal-batch-list"></div>
                                <hr class="my-4">
                                <h5 class="font-semibold mb-2">Kandidat Sudah Tersimpan</h5>
                                <div id="modal-candidates-list">
                                    <?php foreach ($candidates as $candidate): ?>
                                        <div class="flex items-center justify-between py-2 border-b">
                                            <span><?= esc($candidate['candidate_name']) ?> &amp; <?= esc($candidate['vice_candidate_name']) ?></span>
                                            <button class="text-red-600 hover:text-red-800" onclick="deleteCandidate(<?= $candidate['id'] ?>)"><i class="fas fa-trash"></i> Hapus</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button id="save-all-candidates" class="bg-blue-600 text-white px-4 py-2 rounded mt-2">Simpan Semua</button>
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
                            if (candidatePairs.length === 0) {
                                batchList.innerHTML = '<div class="text-gray-500">Belum ada pasangan kandidat ditambahkan.</div>';
                                return;
                            }
                            candidatePairs.forEach((pair, idx) => {
                                const div = document.createElement('div');
                                div.className = 'flex items-center justify-between py-2 border-b';
                                div.innerHTML = `<span>${pair.nim_ketua} &amp; ${pair.nim_wakil}</span>
                                    <button class='text-red-600 hover:text-red-800' onclick='removeBatchPair(${idx})'><i class='fas fa-trash'></i> Hapus</button>`;
                                batchList.appendChild(div);
                            });
                        }
                        function removeBatchPair(idx) {
                            candidatePairs.splice(idx, 1);
                            renderBatchList();
                        }
                        document.getElementById('form-kandidat').addEventListener('submit', function(e) {
                            e.preventDefault();
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
                        document.getElementById('save-all-candidates').addEventListener('click', function() {
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
                                if (data.results) {
                                    let messages = data.results.map(r => `${r.nim_ketua} & ${r.nim_wakil}: ${r.status === 'success' ? 'Berhasil' : r.message}`).join('\n');
                                    errorDiv.textContent = messages;
                                    candidatePairs = [];
                                    renderBatchList();
                                    setTimeout(() => location.reload(), 1500);
                                } else if (data.error) {
                                    errorDiv.textContent = data.error + ' (' + JSON.stringify(candidatePairs) + ') - ' + JSON.stringify(data);
                                }
                            });
                        });
                        // function deleteCandidate(id) {
                        //     fetch('<?= base_url('api/admin/candidates/') ?>' + id, {
                        //         method: 'DELETE',
                        //         headers: {
                        //             'Authorization': 'Bearer ' + getBearerToken()
                        //         }
                        //     })
                        //     .then(res => res.json())
                        //     .then(data => {
                        //         if (data.error) {
                        //             alert(data.error);
                        //         } else {
                        //             location.reload();
                        //         }
                        //     });
                        // }
                        // Initial render for batch list
                        renderBatchList();
                        </script>
                    <?php else: ?>
                        <h5 class="text-lg font-semibold mb-4"><i class="fas fa-users mr-2"></i> Kandidat</h5>
                    <?php endif; ?>
                    
                    <?php if (empty($candidates)): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                            <div class="flex">
                                <i class="fas fa-info-circle mt-0.5 mr-2"></i>
                                <p>Belum ada kandidat untuk pemilihan ini</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-wrap -mx-3">
                            <?php foreach ($candidates as $candidate): ?>
                                <div class="w-full md:w-1/2 lg:w-1/3 px-3 mb-6">
                                    <div class="bg-white rounded-lg shadow-md border border-gray-100 h-full">
                                        <div class="p-5 text-center">
                                            <img src="<?= $candidate['photo'] ?: 'https://via.placeholder.com/100' ?>"
                                                 alt="<?= esc($candidate['candidate_name']) ?>"
                                                 class="rounded-full mx-auto mb-4 w-24 h-24 object-cover">
                                            
                                            <h5 class="font-semibold mb-1"><?= esc($candidate['candidate_name']) ?></h5>
                                            <p class="text-gray-500 text-sm mb-4"><?= esc($candidate['candidate_department_name'] ?: 'Tidak ada jurusan') ?></p>
                                            
                                            <div class="text-left mt-4">
                                                <p class="text-sm mb-2"><span class="font-semibold">Visi:</span><br><?= esc($candidate['vision']) ?></p>
                                                <p class="text-sm mb-4"><span class="font-semibold">Misi:</span><br><?= esc($candidate['mission']) ?></p>
                                            </div>
                                            
                                            <?php if (!$hasVoted && $election['status'] === 'active' && time() >= strtotime($election['start_time']) && time() <= strtotime($election['end_time'])): ?>
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
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
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
                            alert('Error: ' + JSON.stringify(result.error));
                        } else {
                            alert('Error: ' + result.error);
                        }
                    } else {
                        console.log('Vote success:', result);
                        
                        // Show success modal with blockchain details
                        document.getElementById('transactionHash').textContent = result.transaction_hash || 'Pending';
                        document.getElementById('voteHash').textContent = result.vote_hash || 'N/A';
                        
                        // Set verification link
                        const verifyLink = document.getElementById('verifyVoteLink');
                        verifyLink.href = '<?= base_url('verify-vote') ?>/' + result.vote_id;
                        
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
            
            // Set up event listeners when the DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                // Confirmation modal buttons
                document.getElementById('confirmVoteButton').addEventListener('click', castVote);
                document.getElementById('cancelVoteButton').addEventListener('click', function() {
                    document.getElementById('voteConfirmationModal').classList.add('hidden');
                });
                
                // Success modal close button
                document.getElementById('closeSuccessButton').addEventListener('click', function() {
                    document.getElementById('voteSuccessModal').classList.add('hidden');
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
            });
            </script>
        </div>
    </div>
</div>