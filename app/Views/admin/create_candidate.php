<!-- app/Views/admin/create_candidate.php -->
<div class="container mx-auto px-4 mt-6">
    <h2 class="text-2xl font-bold mb-4">Tambah Kandidat untuk "<?= esc($election['title']) ?>"</h2>
    <?php if (session('error')): ?>
        <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
            <?= esc(session('error')) ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?= base_url('admin/election/' . $election['id'] . '/candidates/store') ?>">
        <div id="candidate-pairs">
            <div class="flex flex-wrap mb-2 candidate-pair">
                <div class="w-full md:w-1/2 px-2 mb-2">
                    <input type="text" name="pairs[0][nim_ketua]" class="border rounded w-full p-2" placeholder="NIM Ketua" required>
                </div>
                <div class="w-full md:w-1/2 px-2 mb-2">
                    <input type="text" name="pairs[0][nim_wakil]" class="border rounded w-full p-2" placeholder="NIM Wakil" required>
                </div>
            </div>
        </div>
        <button type="button" id="add-pair" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Tambah Kandidat</button>
        <br>
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded">Simpan Semua Kandidat</button>
    </form>
</div>
<script>
let pairIndex = 1;
document.getElementById('add-pair').addEventListener('click', function() {
    const container = document.getElementById('candidate-pairs');
    const div = document.createElement('div');
    div.className = 'flex flex-wrap mb-2 candidate-pair';
    div.innerHTML = `
        <div class="w-full md:w-1/2 px-2 mb-2">
            <input type="text" name="pairs[${pairIndex}][nim_ketua]" class="border rounded w-full p-2" placeholder="NIM Ketua" required>
        </div>
        <div class="w-full md:w-1/2 px-2 mb-2">
            <input type="text" name="pairs[${pairIndex}][nim_wakil]" class="border rounded w-full p-2" placeholder="NIM Wakil" required>
        </div>
    `;
    container.appendChild(div);
    pairIndex++;
});
</script>
