<main class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <h2 class="text-2xl font-bold mb-2"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
            <p class="text-gray-600 mb-6">Selamat datang kembali, Administrator!</p>
        </div>
    </div>

    <div class="flex flex-wrap -mx-3 mb-8">
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <div class="bg-primary text-white rounded-lg shadow-md h-full">
                <div class="p-5">
                    <h5 class="font-semibold mb-2"><i class="fas fa-users"></i> Total User</h5>
                    <h2 class="text-3xl font-bold"><?= $totalUsers ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <div class="bg-emerald-500 text-white rounded-lg shadow-md h-full">
                <div class="p-5">
                    <h5 class="font-semibold mb-2"><i class="fas fa-vote-yea"></i> Pemilihan</h5>
                    <h2 class="text-3xl font-bold"><?= $totalElections ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <div class="bg-blue-500 text-white rounded-lg shadow-md h-full">
                <div class="p-5">
                    <h5 class="font-semibold mb-2"><i class="fas fa-check-circle"></i> Total Vote</h5>
                    <h2 class="text-3xl font-bold"><?= $totalVotes ?? 0 ?></h2>
                </div>
            </div>
        </div>
        <!-- <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
            <div class="bg-amber-500 text-white rounded-lg shadow-md h-full">
                <div class="p-5">
                    <h5 class="font-semibold mb-2"><i class="fas fa-chart-bar"></i> Aktivitas</h5>
                    <h2 class="text-3xl font-bold">24</h2>
                </div>
            </div>
        </div> -->
    </div>

    <div class="flex flex-wrap -mx-3">
        <div class="w-full md:w-2/3 px-3 mb-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h5 class="font-semibold mb-0"><i class="fas fa-list"></i> Pemilihan Terbaru</h5>
                    <a href="<?= base_url('/admin/elections') ?>" class="inline-block px-3 py-1 text-sm font-medium rounded-lg bg-primary hover:bg-primary-hover text-white transition-all">Lihat Semua</a>
                </div>
                <div class="p-6">
                    <?php if (empty($elections)): ?>
                        <div class="text-center py-6">
                            <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                            <p class="text-gray-500 mb-0">Belum ada pemilihan</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($elections as $election): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($election['title']) ?></td>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('d M Y', strtotime($election['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="w-full md:w-1/3 px-3 mb-6">
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h5 class="font-semibold mb-0"><i class="fas fa-chart-pie"></i> Statistik</h5>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <canvas id="statsChart" width="200" height="200"></canvas>
                        <p class="text-gray-500 mt-4">Grafik partisipasi voting</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Simple chart for dashboard
document.addEventListener('DOMContentLoaded', function() {
    // This is just a placeholder - you can implement actual charts with Chart.js
    console.log('Dashboard loaded');
});
</script>