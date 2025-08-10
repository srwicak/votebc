<main class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-vote-yea mr-2"></i> Manajemen Pemilihan</h2>
                <div class="flex space-x-2">
                    <a class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors" href="<?= base_url('/admin/elections/create') ?>">
                        <i class="fas fa-plus mr-2"></i> Buat Pemilihan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h5 class="font-semibold mb-0"><i class="fas fa-list mr-2"></i> Daftar Pemilihan</h5>
                </div>
                <div class="p-6">
                    <?php if (empty($elections)): ?>
                        <div class="text-center py-10">
                            <i class="fas fa-vote-yea text-4xl text-gray-400 mb-4"></i>
                            <h4 class="text-xl font-semibold mb-2">Belum ada pemilihan</h4>
                            <p class="text-gray-500">Klik tombol "Buat Pemilihan Baru" untuk membuat pemilihan pertama</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($elections as $election): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($election['title']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc(substr($election['description'], 0, 50)) ?><?= strlen($election['description']) > 50 ? '...' : '' ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= ucfirst($election['level']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <?php
                                                switch ($election['status']) {
                                                    case 'active':
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Aktif</span>';
                                                        break;
                                                    case 'draft':
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Draft</span>';
                                                        break;
                                                    case 'completed':
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Selesai</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">' . ucfirst($election['status']) . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= date('d/m/Y', strtotime($election['start_time'])) ?> -
                                                <?= date('d/m/Y', strtotime($election['end_time'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex space-x-2">
                                                    <a href="<?= base_url('/admin/elections/edit/' . $election['id']) ?>"
                                                       class="inline-flex items-center p-1.5 border border-primary text-primary hover:bg-primary hover:text-white rounded transition-colors">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="inline-flex items-center p-1.5 border border-red-500 text-red-500 hover:bg-red-500 hover:text-white rounded transition-colors"
                                                            onclick="deleteElection(<?= $election['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Create Election Modal -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="createElectionModal" tabindex="-1" aria-labelledby="createElectionModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900" id="createElectionModalLabel">Buat Pemilihan Baru</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createElectionForm">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Pemilihan *</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="title" name="title" required>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="level" name="level" required>
                                <option value="">Pilih Level</option>
                                <option value="jurusan">Jurusan</option>
                                <option value="fakultas">Fakultas</option>
                                <option value="universitas">Universitas</option>
                            </select>
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="target_id" class="block text-sm font-medium text-gray-700 mb-1">Target *</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="target_id" name="target_id" required>
                                <option value="">Pilih Target</option>
                                <!-- Options akan diisi dengan JavaScript -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai *</label>
                            <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="start_time" name="start_time" required>
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-4">
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai *</label>
                            <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" id="end_time" name="end_time" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="createElection()">Simpan</button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup event listeners
    document.getElementById('level').addEventListener('change', loadTargets);
    
    // Setup modal functionality
    setupModal();
});

function setupModal() {
    // Get the modal
    const modal = document.getElementById('createElectionModal');
    
    // Get all buttons that open the modal
    const btns = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#createElectionModal"]');
    
    // Get all elements that close the modal
    const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
    
    // When the user clicks the button, open the modal
    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });
    });
    
    // When the user clicks on a close button, close the modal
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    });
    
    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
}

function loadTargets() {
    const level = document.getElementById('level').value;
    const targetSelect = document.getElementById('target_id');
    
    if (!level) {
        targetSelect.innerHTML = '<option value="">Pilih Target</option>';
        return;
    }
    
    targetSelect.innerHTML = '<option value="">Loading...</option>';
    
    // Ini akan diimplementasikan dengan AJAX call ke API
    // Untuk sementara, kita isi dengan data dummy
    let options = '<option value="">Pilih Target</option>';
    
    if (level === 'jurusan') {
        options += `
            <option value="1">Teknik Informatika</option>
            <option value="2">Teknik Elektro</option>
            <option value="3">Manajemen</option>
        `;
    } else if (level === 'fakultas') {
        options += `
            <option value="1">Fakultas Teknik</option>
            <option value="2">Fakultas Ekonomi dan Bisnis</option>
        `;
    } else if (level === 'universitas') {
        options += '<option value="1">Seluruh Universitas</option>';
    }
    
    targetSelect.innerHTML = options;
}

function createElection() {
    const form = document.getElementById('createElectionForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validasi
    if (!data.title || !data.level || !data.target_id || !data.start_time || !data.end_time) {
        alert('Semua field yang bertanda * harus diisi');
        return;
    }
    
    // Konversi datetime untuk API
    const start_time = new Date(data.start_time).toISOString().slice(0, 19).replace('T', ' ');
    const end_time = new Date(data.end_time).toISOString().slice(0, 19).replace('T', ' ');
    
    fetch('<?= base_url('/api/admin/elections') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        },
        body: JSON.stringify({
            title: data.title,
            description: data.description,
            level: data.level,
            target_id: parseInt(data.target_id),
            start_time: start_time,
            end_time: end_time
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Pemilihan berhasil dibuat!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat membuat pemilihan');
    });
    
    // Close the modal
    document.getElementById('createElectionModal').classList.add('hidden');
}

function deleteElection(electionId) {
    if (!confirm('Apakah Anda yakin ingin menghapus pemilihan ini?')) {
        return;
    }
    
    fetch(`<?= base_url('/api/admin/elections/') ?>${electionId}`, {
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Pemilihan berhasil dihapus!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus pemilihan');
    });
}
</script>