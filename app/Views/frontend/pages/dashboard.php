<div class="container mx-auto px-4 mt-6">
    <div class="flex flex-wrap">
        <div class="w-full">
            <h2 class="text-2xl font-bold mb-2"><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
            <p class="text-gray-600 mb-6">Selamat datang, <?= isset($user) && isset($user['name']) ? esc($user['name']) : 'User' ?>!</p>
        </div>
    </div>

    <div class="flex flex-wrap -mx-3 mb-8">
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <div class="bg-primary text-white rounded-lg shadow-md h-full">
                <div class="p-5">
                    <h5 class="font-semibold mb-2"><i class="fas fa-vote-yea"></i> Pemilihan Aktif</h5>
                    <h2 class="text-3xl font-bold"><?= isset($activeElections) ? count($activeElections) : 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <div class="bg-emerald-500 text-white rounded-lg shadow-md h-full">
                <div class="p-5">
                    <h5 class="font-semibold mb-2"><i class="fas fa-check-circle"></i> Sudah Voting</h5>
                    <h2 class="text-3xl font-bold"><?= isset($userVotes) ? array_sum($userVotes) : 0 ?></h2>
                </div>
            </div>
        </div>
        <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
            <div class="bg-blue-500 text-white rounded-lg shadow-md h-full">
                <div class="p-5">
                    <h5 class="font-semibold mb-2"><i class="fas fa-clock"></i> Belum Voting</h5>
                    <h2 class="text-3xl font-bold"><?= (isset($activeElections) ? count($activeElections) : 0) - (isset($userVotes) ? array_sum($userVotes) : 0) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap">
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-md">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h5 class="font-semibold mb-0"><i class="fas fa-list"></i> Pemilihan Aktif</h5>
                </div>
                <div class="p-6">
                    <?php if (empty($activeElections)): ?>
                        <div class="text-center py-10">
                            <i class="fas fa-info-circle text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500">Tidak ada pemilihan aktif saat ini</p>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-wrap -mx-3">
                            <?php foreach ($activeElections as $election): ?>
                                <div class="w-full md:w-1/2 lg:w-1/3 px-3 mb-6">
                                    <div class="bg-white rounded-lg shadow-md border border-gray-100 h-full">
                                        <div class="p-5">
                                            <h5 class="font-semibold mb-2"><?= esc($election['title']) ?></h5>
                                            <p class="text-sm text-gray-500 mb-3"><?= esc($election['description']) ?></p>
                                            <p class="text-sm mb-4">
                                                <i class="fas fa-layer-group text-gray-600"></i> <?= ucfirst($election['level']) ?><br>
                                                <i class="fas fa-calendar text-gray-600"></i>
                                                <?= date('d M Y', strtotime($election['start_time'])) ?> -
                                                <?= date('d M Y', strtotime($election['end_time'])) ?>
                                            </p>
                                            <?php if (isset($userVotes) && isset($userVotes[$election['id']]) && $userVotes[$election['id']]): ?>
                                                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-emerald-500 text-white">
                                                    <i class="fas fa-check"></i> Sudah Voting
                                                </span>
                                            <?php else: ?>
                                                <a href="<?= base_url('/election/' . $election['id']) ?>" class="inline-block px-4 py-2 text-sm font-medium rounded-lg bg-primary hover:bg-primary-hover text-white transition-all">
                                                    <i class="fas fa-vote-yea"></i> Vote Sekarang
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>