<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kandidat - E-Voting BEM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#4f46e5',
                            hover: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-primary to-primary-hover text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-vote-yea text-2xl"></i>
                    <span class="text-xl font-bold">E-Voting BEM</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?= base_url('/admin/dashboard') ?>" class="hover:text-gray-200 transition">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a href="<?= base_url('/admin/elections') ?>" class="hover:text-gray-200 transition">
                        <i class="fas fa-list mr-1"></i> Pemilihan
                    </a>
                    <a href="<?= base_url('/logout') ?>" class="hover:text-gray-200 transition">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex items-center text-sm text-gray-600">
                <a href="<?= base_url('/admin/dashboard') ?>" class="hover:text-primary transition">
                    <i class="fas fa-home mr-1"></i> Dashboard
                </a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <a href="<?= base_url('/admin/elections') ?>" class="hover:text-primary transition">Pemilihan</a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <span class="text-gray-800 font-medium">Tambah Kandidat</span>
            </nav>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-plus text-primary"></i> Tambah Kandidat
            </h1>
            <p class="text-gray-600">Pemilihan: <span class="font-semibold text-gray-800"><?= esc($election['title']) ?></span></p>
        </div>

        <!-- Alert Messages -->
        <?php if (session('error')): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 shadow-md animate-shake">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                    <span><?= esc(session('error')) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session('success')): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 shadow-md">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-xl mr-3"></i>
                    <span><?= esc(session('success')) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-primary to-primary-hover text-white px-8 py-6">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Form Kandidat</h2>
                        <p class="text-sm opacity-90 mt-1">Masukkan NIM ketua dan wakil untuk setiap pasangan kandidat</p>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-8">
                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-5 rounded-lg mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                        <div>
                            <h3 class="font-semibold text-blue-900 mb-2">Petunjuk Pengisian:</h3>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                                    Masukkan NIM yang valid dan terdaftar di sistem
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                                    Pastikan mahasiswa eligible untuk pemilihan ini
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                                    Klik "Tambah Kandidat" untuk menambah pasangan baru
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form method="post" action="<?= base_url('admin/election/' . $election['id'] . '/candidates/store') ?>" id="candidateForm">
                    <!-- Candidate Pairs Container -->
                    <div id="candidate-pairs" class="space-y-6">
                        <div class="candidate-pair bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-all" data-index="0">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-800">
                                    <i class="fas fa-users text-primary mr-2"></i>
                                    Pasangan Kandidat <span class="pair-number">1</span>
                                </h3>
                                <button type="button" class="remove-pair hidden text-red-500 hover:text-red-700 transition" onclick="removePair(this)">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-user text-emerald-500 mr-1"></i> NIM Ketua <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="pairs[0][nim_ketua]" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm" 
                                           placeholder="Contoh: 123456789" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-user-friends text-blue-500 mr-1"></i> NIM Wakil <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="pairs[0][nim_wakil]" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm" 
                                           placeholder="Contoh: 987654321" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex flex-wrap gap-4 items-center justify-between pt-6 border-t border-gray-200">
                        <button type="button" id="add-pair" class="px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition-all shadow-md hover:shadow-lg flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah Kandidat
                        </button>
                        <div class="flex gap-4">
                            <a href="<?= base_url('/admin/elections') ?>" class="px-8 py-3 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition-all shadow-md hover:shadow-lg flex items-center">
                                <i class="fas fa-times mr-2"></i> Batal
                            </a>
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all shadow-md flex items-center">
                                <i class="fas fa-save mr-2"></i> Simpan Semua Kandidat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12 py-6">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm">&copy; <?= date('Y') ?> E-Voting BEM. All rights reserved.</p>
        </div>
    </footer>

    <script>
    let pairIndex = 1;

    document.getElementById('add-pair').addEventListener('click', function() {
        const container = document.getElementById('candidate-pairs');
        const div = document.createElement('div');
        div.className = 'candidate-pair bg-gray-50 rounded-lg p-6 border border-gray-200 hover:shadow-md transition-all animate-fadeIn';
        div.setAttribute('data-index', pairIndex);
        div.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-users text-primary mr-2"></i>
                    Pasangan Kandidat <span class="pair-number">${pairIndex + 1}</span>
                </h3>
                <button type="button" class="remove-pair text-red-500 hover:text-red-700 transition" onclick="removePair(this)">
                    <i class="fas fa-trash-alt"></i> Hapus
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-emerald-500 mr-1"></i> NIM Ketua <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="pairs[${pairIndex}][nim_ketua]" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm" 
                           placeholder="Contoh: 123456789" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-friends text-blue-500 mr-1"></i> NIM Wakil <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="pairs[${pairIndex}][nim_wakil]" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all shadow-sm" 
                           placeholder="Contoh: 987654321" required>
                </div>
            </div>
        `;
        container.appendChild(div);
        pairIndex++;
        updatePairNumbers();
        updateRemoveButtons();
    });

    function removePair(button) {
        const pair = button.closest('.candidate-pair');
        pair.style.opacity = '0';
        pair.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            pair.remove();
            updatePairNumbers();
            updateRemoveButtons();
        }, 300);
    }

    function updatePairNumbers() {
        const pairs = document.querySelectorAll('.candidate-pair');
        pairs.forEach((pair, index) => {
            const numberSpan = pair.querySelector('.pair-number');
            if (numberSpan) {
                numberSpan.textContent = index + 1;
            }
        });
    }

    function updateRemoveButtons() {
        const pairs = document.querySelectorAll('.candidate-pair');
        const removeButtons = document.querySelectorAll('.remove-pair');
        
        if (pairs.length <= 1) {
            removeButtons.forEach(btn => btn.classList.add('hidden'));
        } else {
            removeButtons.forEach(btn => btn.classList.remove('hidden'));
        }
    }

    // Form validation
    document.getElementById('candidateForm').addEventListener('submit', function(e) {
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
    });

    // Initialize
    updateRemoveButtons();
    </script>

    <style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    .animate-fadeIn {
        animation: fadeIn 0.5s ease-out;
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }

    .candidate-pair {
        transition: all 0.3s ease;
    }

    select, input {
        background-image: none;
    }

    input:focus {
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    </style>
</body>
</html>
