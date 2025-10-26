<main class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold"><i class="fas fa-users mr-2"></i> Daftar Kandidat</h2>
                    <p class="text-gray-600 mt-1"><?= esc($election['title']) ?></p>
                </div>
                <div class="flex space-x-2">
                    <a class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors" href="<?= base_url('/admin/elections') ?>">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <a class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors" href="<?= base_url('/admin/election/' . $election['id'] . '/candidates/create') ?>">
                        <i class="fas fa-plus mr-2"></i> Tambah Kandidat
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Election Info Card -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
            <div class="flex-1">
                <h3 class="font-semibold text-blue-900 mb-2">Informasi Pemilihan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-blue-700 font-medium">Level:</span>
                        <span class="text-blue-900 ml-2"><?= ucfirst($election['level']) ?></span>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Status:</span>
                        <span class="text-blue-900 ml-2"><?= ucfirst($election['status']) ?></span>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Total Kandidat:</span>
                        <span class="text-blue-900 ml-2 font-bold"><?= count($candidates) ?></span>
                    </div>
                </div>
                <div class="mt-2 text-sm">
                    <span class="text-blue-700 font-medium">Periode Voting:</span>
                    <span class="text-blue-900 ml-2">
                        <?= date('d M Y, H:i', strtotime($election['start_time'])) ?> -
                        <?= date('d M Y, H:i', strtotime($election['end_time'])) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidates Cards -->
    <div class="flex flex-wrap">
        <div class="w-full">
            <?php if (empty($candidates)): ?>
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-user-slash text-6xl text-gray-300 mb-4"></i>
                    <h4 class="text-xl font-semibold mb-2 text-gray-700">Belum ada kandidat</h4>
                    <p class="text-gray-500 mb-6">Klik tombol "Tambah Kandidat" untuk menambahkan kandidat pertama</p>
                    <a class="inline-block px-6 py-3 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors" href="<?= base_url('/admin/election/' . $election['id'] . '/candidates/create') ?>">
                        <i class="fas fa-plus mr-2"></i> Tambah Kandidat Sekarang
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($candidates as $candidate): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Header with Candidate ID Badge -->
                            <div class="relative bg-gradient-to-br from-blue-500 to-purple-600 h-48 flex items-center justify-center">
                                <?php if (!empty($candidate['photo'])): ?>
                                    <img src="<?= base_url('uploads/' . $candidate['photo']) ?>" 
                                         alt="<?= esc($candidate['user_name']) ?>"
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="text-white text-6xl">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Blockchain Candidate ID Badge -->
                                <div class="absolute top-3 right-3">
                                    <div class="bg-yellow-400 text-yellow-900 px-3 py-2 rounded-lg shadow-lg">
                                        <div class="text-xs font-semibold">Blockchain ID</div>
                                        <div class="text-xl font-bold"><?= $candidate['id'] ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-5">
                                <!-- Candidate Name -->
                                <div class="mb-4">
                                    <h3 class="text-xl font-bold text-gray-800 mb-1">
                                        <?= esc($candidate['user_name'] ?? 'Unknown') ?>
                                    </h3>
                                    <?php if (!empty($candidate['user_nim'])): ?>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-id-card mr-2"></i>
                                            NIM: <?= esc($candidate['user_nim']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Faculty & Department -->
                                <div class="mb-4 space-y-2">
                                    <?php if (!empty($candidate['faculty_name'])): ?>
                                        <div class="flex items-start">
                                            <i class="fas fa-building text-blue-500 mt-1 mr-2 flex-shrink-0"></i>
                                            <span class="text-sm text-gray-700"><?= esc($candidate['faculty_name']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($candidate['department_name'])): ?>
                                        <div class="flex items-start">
                                            <i class="fas fa-graduation-cap text-blue-500 mt-1 mr-2 flex-shrink-0"></i>
                                            <span class="text-sm text-gray-700"><?= esc($candidate['department_name']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Running Mate -->
                                <?php if (!empty($candidate['running_mate_name'])): ?>
                                    <div class="border-t border-gray-200 pt-4 mb-4">
                                        <p class="text-xs text-gray-500 mb-2">
                                            <i class="fas fa-user-friends mr-1"></i> Wakil:
                                        </p>
                                        <p class="text-sm font-semibold text-gray-700">
                                            <?= esc($candidate['running_mate_name']) ?>
                                        </p>
                                        <?php if (!empty($candidate['running_mate_nim'])): ?>
                                            <p class="text-xs text-gray-500 mt-1">
                                                NIM: <?= esc($candidate['running_mate_nim']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Blockchain Info Box -->
                                <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-lg border border-blue-200 mb-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs text-gray-600 mb-1">
                                                <i class="fas fa-link mr-1"></i> Blockchain Candidate ID
                                            </p>
                                            <p class="font-mono text-2xl font-bold text-blue-600">
                                                <?= $candidate['id'] ?>
                                            </p>
                                        </div>
                                        <div class="text-blue-500 text-3xl">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        ID ini digunakan untuk verifikasi vote di blockchain
                                    </p>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <button onclick="showCandidateDetail(<?= $candidate['id'] ?>)"
                                            class="flex-1 px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </button>
                                    <button onclick="copyCandidateId(<?= $candidate['id'] ?>)"
                                            class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors"
                                            title="Copy Candidate ID">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Export Options -->
                <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-download mr-2"></i> Export Data Kandidat
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Download daftar kandidat beserta Blockchain ID untuk verifikasi independen
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="downloadCandidatesJSON()" 
                                class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fas fa-file-code mr-2"></i> Download JSON
                        </button>
                        <button onclick="downloadCandidatesCSV()" 
                                class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-file-csv mr-2"></i> Download CSV
                        </button>
                        <button onclick="printCandidatesList()" 
                                class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Candidate Detail Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="candidateDetailModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900">Detail Kandidat</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="candidateDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const electionId = <?= $election['id'] ?>;
const candidates = <?= json_encode($candidates) ?>;

function showCandidateDetail(candidateId) {
    const candidate = candidates.find(c => c.id === candidateId);
    if (!candidate) {
        alert('Kandidat tidak ditemukan');
        return;
    }
    
    const content = `
        <div class="space-y-4">
            <div class="text-center">
                ${candidate.photo ? 
                    `<img src="<?= base_url('uploads/') ?>${candidate.photo}" alt="${candidate.user_name}" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">` :
                    '<div class="w-32 h-32 rounded-full mx-auto mb-4 bg-gray-200 flex items-center justify-center"><i class="fas fa-user text-4xl text-gray-400"></i></div>'
                }
                <h3 class="text-2xl font-bold">${candidate.user_name || 'Unknown'}</h3>
                <p class="text-gray-600">NIM: ${candidate.user_nim || '-'}</p>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Blockchain Candidate ID:</p>
                <p class="font-mono text-3xl font-bold text-blue-600">${candidate.id}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Fakultas</p>
                    <p class="font-semibold">${candidate.faculty_name || '-'}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jurusan</p>
                    <p class="font-semibold">${candidate.department_name || '-'}</p>
                </div>
            </div>
            
            ${candidate.running_mate_name ? `
                <div class="border-t pt-4">
                    <p class="text-sm text-gray-500 mb-2">Wakil Kandidat:</p>
                    <p class="font-semibold text-lg">${candidate.running_mate_name}</p>
                    <p class="text-sm text-gray-600">NIM: ${candidate.running_mate_nim || '-'}</p>
                </div>
            ` : ''}
            
            <div class="flex gap-2 pt-4">
                <button onclick="copyCandidateId(${candidate.id})" class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    <i class="fas fa-copy mr-2"></i> Copy ID
                </button>
                <button onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Tutup
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('candidateDetailContent').innerHTML = content;
    document.getElementById('candidateDetailModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('candidateDetailModal').classList.add('hidden');
}

function copyCandidateId(candidateId) {
    navigator.clipboard.writeText(candidateId.toString()).then(() => {
        alert(`Candidate ID ${candidateId} berhasil di-copy!`);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Gagal copy ID');
    });
}

function downloadCandidatesJSON() {
    const data = {
        election_id: electionId,
        election_title: "<?= addslashes($election['title']) ?>",
        generated_at: new Date().toISOString(),
        candidates: candidates.map(c => ({
            candidate_id: c.id,
            name: c.user_name || 'Unknown',
            nim: c.user_nim || null,
            faculty: c.faculty_name || null,
            department: c.department_name || null,
            running_mate: c.running_mate_name || null,
            running_mate_nim: c.running_mate_nim || null
        }))
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `election_${electionId}_candidates.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function downloadCandidatesCSV() {
    let csv = 'Candidate ID,Name,NIM,Faculty,Department,Running Mate,Running Mate NIM\n';
    
    candidates.forEach(c => {
        csv += `${c.id},"${c.user_name || 'Unknown'}","${c.user_nim || ''}","${c.faculty_name || ''}","${c.department_name || ''}","${c.running_mate_name || ''}","${c.running_mate_nim || ''}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `election_${electionId}_candidates.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function printCandidatesList() {
    const printWindow = window.open('', '_blank');
    let content = `
        <html>
        <head>
            <title>Daftar Kandidat - <?= addslashes($election['title']) ?></title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .info { background: #f0f0f0; padding: 15px; margin: 20px 0; border-radius: 5px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #4CAF50; color: white; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .blockchain-id { font-weight: bold; color: #2563eb; font-size: 1.2em; }
            </style>
        </head>
        <body>
            <h1>Daftar Kandidat</h1>
            <div class="info">
                <p><strong>Pemilihan:</strong> <?= addslashes($election['title']) ?></p>
                <p><strong>Level:</strong> <?= ucfirst($election['level']) ?></p>
                <p><strong>Total Kandidat:</strong> <?= count($candidates) ?></p>
                <p><strong>Dicetak pada:</strong> ${new Date().toLocaleString('id-ID')}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Blockchain ID</th>
                        <th>Nama Kandidat</th>
                        <th>NIM</th>
                        <th>Fakultas</th>
                        <th>Jurusan</th>
                        <th>Wakil</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    candidates.forEach(c => {
        content += `
            <tr>
                <td class="blockchain-id">${c.id}</td>
                <td>${c.user_name || 'Unknown'}</td>
                <td>${c.user_nim || '-'}</td>
                <td>${c.faculty_name || '-'}</td>
                <td>${c.department_name || '-'}</td>
                <td>${c.running_mate_name || '-'}${c.running_mate_nim ? '<br><small>' + c.running_mate_nim + '</small>' : ''}</td>
            </tr>
        `;
    });
    
    content += `
                </tbody>
            </table>
        </body>
        </html>
    `;
    
    printWindow.document.write(content);
    printWindow.document.close();
    printWindow.print();
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('candidateDetailModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>
