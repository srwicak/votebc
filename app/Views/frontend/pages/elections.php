<div class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-vote-yea mr-2"></i> Daftar Pemilihan</h2>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= base_url('/admin/elections/create') ?>" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i> Buat Pemilihan
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (empty($elections)): ?>
        <div class="flex flex-wrap">
            <div class="w-full">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-10 text-center">
                        <i class="fas fa-vote-yea text-4xl text-gray-400 mb-4"></i>
                        <h4 class="text-xl font-semibold mb-2">Tidak ada pemilihan</h4>
                        <p class="text-gray-500">Belum ada pemilihan yang dibuat</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="flex flex-wrap -mx-3">
            <?php foreach ($elections as $election): ?>
                <div class="w-full md:w-1/2 lg:w-1/3 px-3 mb-6">
                    <div class="bg-white rounded-lg shadow-md border border-gray-100 h-full">
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-3">
                                <h5 class="font-semibold"><?= esc($election['title']) ?></h5>
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch ($election['status']) {
                                    case 'active':
                                        $statusClass = 'bg-emerald-100 text-emerald-800';
                                        $statusText = 'Aktif';
                                        break;
                                    case 'draft':
                                        $statusClass = 'bg-amber-100 text-amber-800';
                                        $statusText = 'Draft';
                                        break;
                                    case 'completed':
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Selesai';
                                        break;
                                    default:
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                        $statusText = ucfirst($election['status']);
                                }
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>"><?= $statusText ?></span>
                            </div>
                            
                            <p class="text-sm mb-4"><?= esc($election['description']) ?></p>
                            
                            <div class="text-sm text-gray-500 mb-4">
                                <p class="mb-1"><i class="fas fa-layer-group text-gray-400 mr-2"></i> <?= ucfirst($election['level']) ?></p>
                                <p class="mb-1"><i class="fas fa-calendar text-gray-400 mr-2"></i>
                                    <?= date('d M Y H:i', strtotime($election['start_time'])) ?> -
                                    <?= date('d M Y H:i', strtotime($election['end_time'])) ?>
                                </p>
                                <p class="mb-0"><i class="fas fa-user text-gray-400 mr-2"></i> Dibuat oleh: <?= esc($election['creator_name']) ?></p>
                            </div>
                            
                            <a href="<?= base_url('/election/' . $election['id']) ?>" class="block w-full text-center px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors mb-2">
                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>