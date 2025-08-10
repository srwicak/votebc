<div class="container mx-auto px-4 py-8">
    <div class="flex flex-wrap">
        <div class="w-full">
            <nav class="py-3" aria-label="breadcrumb">
                <ol class="flex text-sm">
                    <li class="flex items-center">
                        <a href="<?= base_url('/admin/dashboard') ?>" class="text-primary hover:text-primary-hover">Dashboard</a>
                        <svg class="h-4 w-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li class="flex items-center">
                        <a href="<?= base_url('/admin/elections') ?>" class="text-primary hover:text-primary-hover">Pemilihan</a>
                        <svg class="h-4 w-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li class="text-gray-700">Edit Kandidat</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-primary text-white px-6 py-4">
            <h4 class="text-xl font-semibold">Edit Kandidat</h4>
        </div>
        <div class="p-6">
            <form id="editCandidateForm" class="space-y-6" enctype="multipart/form-data">
                <input type="hidden" id="candidate_id" name="id" value="<?= $candidate['id'] ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="election_id" class="block text-sm font-medium text-gray-700 mb-1">Pemilihan <span class="text-red-500">*</span></label>
                        <select id="election_id" name="election_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Pemilihan</option>
                            <?php foreach ($elections as $election): ?>
                                <option value="<?= $election['id'] ?>" <?= $candidate['election_id'] == $election['id'] ? 'selected' : '' ?>><?= esc($election['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Mahasiswa <span class="text-red-500">*</span></label>
                        <select id="user_id" name="user_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih Mahasiswa</option>
                            <?php foreach ($users as $user): ?>
                                <?php if ($user['role'] === 'user'): ?>
                                    <option value="<?= $user['id'] ?>" <?= $candidate['user_id'] == $user['id'] ? 'selected' : '' ?>><?= esc($user['name']) ?> (<?= esc($user['nim']) ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="vision" class="block text-sm font-medium text-gray-700 mb-1">Visi <span class="text-red-500">*</span></label>
                    <textarea id="vision" name="vision" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required><?= esc($candidate['vision']) ?></textarea>
                </div>
                
                <div>
                    <label for="mission" class="block text-sm font-medium text-gray-700 mb-1">Misi <span class="text-red-500">*</span></label>
                    <textarea id="mission" name="mission" rows="5" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required><?= esc($candidate['mission']) ?></textarea>
                </div>
                
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                    <div class="flex items-center space-x-4">
                        <div class="w-24 h-24 bg-gray-100 rounded-full overflow-hidden flex items-center justify-center">
                            <img id="photoPreview" src="<?= $candidateDetails['photo'] ?: 'https://via.placeholder.com/100' ?>" alt="Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <input type="file" id="photo" name="photo" accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Ukuran maksimal: 2MB.</p>
                            <p class="text-xs text-gray-500">Biarkan kosong jika tidak ingin mengubah foto.</p>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <h5 class="text-lg font-semibold mb-4">Pengaturan Blockchain</h5>
                    
                    <div class="mb-4">
                        <div class="flex items-center mb-2">
                            <input type="checkbox" id="useBlockchain" name="use_blockchain" class="mr-2" <?= isset($candidate['use_blockchain']) && $candidate['use_blockchain'] ? 'checked' : '' ?>>
                            <label for="useBlockchain" class="text-sm font-medium text-gray-700">Gunakan Blockchain</label>
                        </div>
                        <p class="text-xs text-gray-500">Jika dicentang, data kandidat akan dicatat di blockchain untuk verifikasi.</p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="<?= base_url('/admin/elections') ?>" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition">
                        Simpan
                    </button>
                </div>
            </form>
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
    
    // Handle form submission
    document.getElementById('editCandidateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const candidateId = formData.get('id');
        
        // Send data to server
        fetch(`<?= base_url('/api/admin/candidates') ?>/${candidateId}`, {
            method: 'POST', // Using POST with _method=PUT for file uploads
            headers: {
                'Authorization': 'Bearer <?= session()->get('auth_token') ?>'
            },
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.error) {
                alert('Error: ' + result.error);
            } else {
                alert('Kandidat berhasil diperbarui');
                window.location.href = '<?= base_url('/admin/elections') ?>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memperbarui kandidat');
        });
    });
});
</script>