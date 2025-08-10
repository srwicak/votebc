<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold mb-4">Verifikasi Vote Blockchain</h1>
        
        <?php if (isset($voteId) && !isset($vote)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <p>Vote dengan ID <?= $voteId ?> tidak ditemukan.</p>
            </div>
            
            <div class="mt-6">
                <form action="<?= base_url('verify-vote') ?>" method="get" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                        <input type="text" name="id" placeholder="Masukkan Vote ID atau Transaction Hash" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Verifikasi
                    </button>
                </form>
            </div>
        <?php elseif (!isset($voteId)): ?>
            <p class="mb-4">Masukkan Vote ID atau Transaction Hash untuk memverifikasi vote pada blockchain.</p>
            
            <form action="<?= base_url('verify-vote') ?>" method="get" class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow">
                    <input type="text" name="id" placeholder="Masukkan Vote ID atau Transaction Hash" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Verifikasi
                </button>
            </form>
        <?php else: ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <p class="font-bold">Vote ditemukan!</p>
                <p>Berikut adalah detail vote dan status verifikasi blockchain.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold mb-3">Detail Vote</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Vote ID:</span>
                            <span><?= $vote['id'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Pemilihan:</span>
                            <span><?= $election['title'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Kandidat:</span>
                            <span><?= $candidate['user_name'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Waktu Vote:</span>
                            <span><?= $vote['voted_at'] ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold mb-3">Detail Blockchain</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Transaction Hash:</span>
                            <span class="text-xs md:text-sm break-all"><?= $blockchainVote['transaction_hash'] ?></span>
                        </div>
                        <?php if (isset($blockchainVote['vote_hash'])): ?>
                        <div class="flex justify-between">
                            <span class="font-medium">Vote Hash:</span>
                            <span class="text-xs md:text-sm break-all"><?= $blockchainVote['vote_hash'] ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between">
                            <span class="font-medium">Status:</span>
                            <span class="<?= $blockchainVote['status'] === 'success' ? 'text-green-600' : ($blockchainVote['status'] === 'pending' ? 'text-yellow-600' : 'text-red-600') ?>">
                                <?= ucfirst($blockchainVote['status']) ?>
                            </span>
                        </div>
                        <?php if (isset($blockchainVote['block_number'])): ?>
                        <div class="flex justify-between">
                            <span class="font-medium">Block Number:</span>
                            <span><?= $blockchainVote['block_number'] ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-3">Verifikasi Blockchain</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div id="verification-loading" class="text-center py-4">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-600"></div>
                        <p class="mt-2">Memverifikasi vote pada blockchain...</p>
                    </div>
                    
                    <div id="verification-result" class="hidden">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-between">
                <a href="<?= base_url('verify-vote') ?>" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    Verifikasi Vote Lain
                </a>
                
                <a href="<?= base_url('election/' . $election['id']) ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Kembali ke Pemilihan
                </a>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Add animation to loading indicator
                const loadingIndicator = document.getElementById('verification-loading');
                loadingIndicator.innerHTML = `
                    <div class="flex flex-col items-center py-4">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-600"></div>
                        <p class="mt-4 text-blue-600 font-medium">Memverifikasi vote pada blockchain...</p>
                        <p class="mt-2 text-sm text-gray-500">Proses ini mungkin memerlukan waktu beberapa detik</p>
                    </div>
                `;
                
                // Fetch verification data from API
                fetch('<?= base_url('api/votes/verify/' . $vote['id']) ?>')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Hide loading indicator with fade-out effect
                        loadingIndicator.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                        setTimeout(() => {
                            loadingIndicator.classList.add('hidden');
                            loadingIndicator.classList.remove('opacity-0');
                        }, 500);
                        
                        // Show verification result
                        const resultDiv = document.getElementById('verification-result');
                        resultDiv.classList.remove('hidden');
                        resultDiv.classList.add('opacity-0');
                        
                        if (data.status === 'success') {
                            const verification = data.data.verification;
                            const receipt = data.data.receipt;
                            
                            let resultHTML = '';
                            
                            if (verification.valid) {
                                resultHTML = `
                                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-green-800">Vote Terverifikasi!</p>
                                                <p class="text-sm text-green-700 mt-1">Vote ini telah terverifikasi dan tercatat pada blockchain.</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            } else {
                                resultHTML = `
                                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded mb-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-yellow-800">Verifikasi Tidak Lengkap</p>
                                                <p class="text-sm text-yellow-700 mt-1">Vote ini belum sepenuhnya terverifikasi pada blockchain.</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            resultHTML += `
                                <div class="mt-4 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                                    <h3 class="text-lg font-semibold mb-3">Status Verifikasi</h3>
                                    <div class="space-y-3">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center ${verification.hash_valid ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    ${verification.hash_valid
                                                        ? '<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>'
                                                        : '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>'}
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <span class="font-medium">Hash Valid</span>
                                                <p class="text-sm text-gray-500">
                                                    ${verification.hash_valid
                                                        ? 'Hash vote cocok dengan data yang tercatat'
                                                        : 'Hash vote tidak cocok dengan data yang diharapkan'}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center ${verification.on_blockchain ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    ${verification.on_blockchain
                                                        ? '<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>'
                                                        : '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>'}
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <span class="font-medium">Tercatat di Blockchain</span>
                                                <p class="text-sm text-gray-500">
                                                    ${verification.on_blockchain
                                                        ? 'Vote telah tercatat pada blockchain'
                                                        : 'Vote belum tercatat pada blockchain'}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-blue-100 text-blue-600">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <span class="font-medium">Waktu Verifikasi</span>
                                                <p class="text-sm text-gray-500">${new Date(verification.verification_time * 1000).toLocaleString()}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                                    <h3 class="text-lg font-semibold mb-3">Detail Transaksi Blockchain</h3>
                                    <div class="space-y-2">
                                        <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-500">Block Number</span>
                                            <span class="col-span-2 text-sm">${receipt.blockNumber}</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-500">Block Hash</span>
                                            <span class="col-span-2 text-xs md:text-sm break-all font-mono">${receipt.blockHash}</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-500">Gas Used</span>
                                            <span class="col-span-2 text-sm">${receipt.gasUsed}</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-500">Confirmations</span>
                                            <span class="col-span-2 text-sm">${receipt.confirmations || 'N/A'}</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 py-2">
                                            <span class="text-sm font-medium text-gray-500">Timestamp</span>
                                            <span class="col-span-2 text-sm">${receipt.timestamp ? new Date(receipt.timestamp * 1000).toLocaleString() : 'N/A'}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            resultDiv.innerHTML = resultHTML;
                        } else {
                            resultDiv.innerHTML = `
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-red-800">Error!</p>
                                            <p class="text-sm text-red-700 mt-1">${data.message || 'Terjadi kesalahan saat memverifikasi vote.'}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                        
                        // Fade in the result
                        setTimeout(() => {
                            resultDiv.classList.add('transition-opacity', 'duration-500');
                            resultDiv.classList.remove('opacity-0');
                        }, 100);
                    })
                    .catch(error => {
                        console.error('Verification error:', error);
                        
                        // Hide loading indicator
                        loadingIndicator.classList.add('hidden');
                        
                        // Show error
                        const resultDiv = document.getElementById('verification-result');
                        resultDiv.classList.remove('hidden');
                        resultDiv.innerHTML = `
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800">Error!</p>
                                        <p class="text-sm text-red-700 mt-1">Terjadi kesalahan saat memverifikasi vote: ${error.message}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
            });
            </script>
        <?php endif; ?>
    </div>
</div>