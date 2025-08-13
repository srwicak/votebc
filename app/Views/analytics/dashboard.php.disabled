<?= $this->extend('layout/admin_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Analytics Dashboard</h1>

    <!-- Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Elections</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalElections ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Votes Cast</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalVotes ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-vote-yea fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Registered Voters</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalUsers ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Voter Turnout</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $avgTurnout ?>%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Voter Turnout Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Voter Turnout by Election</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Export Options:</div>
                            <a class="dropdown-item" href="#" id="exportTurnoutPNG">Export as PNG</a>
                            <a class="dropdown-item" href="#" id="exportTurnoutCSV">Export as CSV</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="turnoutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verification Status Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Verification Status</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Export Options:</div>
                            <a class="dropdown-item" href="#" id="exportVerificationPNG">Export as PNG</a>
                            <a class="dropdown-item" href="#" id="exportVerificationCSV">Export as CSV</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="verificationChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Fully Verified
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Partially Verified
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Not Verified
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Voting Activity Timeline -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Voting Activity Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blockchain Transactions -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Blockchain Transactions</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Election</th>
                                    <th>Tx Hash</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $tx): ?>
                                <tr>
                                    <td><?= $tx['election_name'] ?></td>
                                    <td>
                                        <a href="https://sepolia.etherscan.io/tx/<?= $tx['tx_hash'] ?>" target="_blank">
                                            <?= substr($tx['tx_hash'], 0, 10) ?>...
                                        </a>
                                    </td>
                                    <td><?= $tx['created_at'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Geographic Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Geographic Distribution</h6>
                </div>
                <div class="card-body">
                    <div id="geoMap" style="height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- Device Usage -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Device Usage</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="deviceChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Desktop
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Mobile
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Tablet
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Metrics -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Security Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 mb-4">
                            <div class="card bg-primary text-white shadow">
                                <div class="card-body">
                                    Failed Login Attempts
                                    <div class="text-white-50 small"><?= $securityMetrics['failed_logins'] ?> in last 24h</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <div class="card bg-success text-white shadow">
                                <div class="card-body">
                                    Successful Verifications
                                    <div class="text-white-50 small"><?= $securityMetrics['successful_verifications'] ?> in last 24h</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <div class="card bg-warning text-white shadow">
                                <div class="card-body">
                                    Rate Limited Requests
                                    <div class="text-white-50 small"><?= $securityMetrics['rate_limited'] ?> in last 24h</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <div class="card bg-danger text-white shadow">
                                <div class="card-body">
                                    Blocked Suspicious IPs
                                    <div class="text-white-50 small"><?= $securityMetrics['blocked_ips'] ?> in last 24h</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Voter Turnout Chart
    var turnoutCtx = document.getElementById('turnoutChart').getContext('2d');
    var turnoutChart = new Chart(turnoutCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($turnoutData, 'election_name')) ?>,
            datasets: [{
                label: 'Voter Turnout (%)',
                data: <?= json_encode(array_column($turnoutData, 'turnout_percentage')) ?>,
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Verification Status Chart
    var verificationCtx = document.getElementById('verificationChart').getContext('2d');
    var verificationChart = new Chart(verificationCtx, {
        type: 'doughnut',
        data: {
            labels: ['Fully Verified', 'Partially Verified', 'Not Verified'],
            datasets: [{
                data: [
                    <?= $verificationStats['fully_verified'] ?>, 
                    <?= $verificationStats['partially_verified'] ?>, 
                    <?= $verificationStats['not_verified'] ?>
                ],
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#dda20a', '#be2617'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Activity Timeline Chart
    var activityCtx = document.getElementById('activityChart').getContext('2d');
    var activityChart = new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($activityData, 'date')) ?>,
            datasets: [{
                label: 'Votes Cast',
                data: <?= json_encode(array_column($activityData, 'vote_count')) ?>,
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Device Usage Chart
    var deviceCtx = document.getElementById('deviceChart').getContext('2d');
    var deviceChart = new Chart(deviceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Desktop', 'Mobile', 'Tablet'],
            datasets: [{
                data: [
                    <?= $deviceStats['desktop'] ?>, 
                    <?= $deviceStats['mobile'] ?>, 
                    <?= $deviceStats['tablet'] ?>
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Geographic Map
    var map = L.map('geoMap').setView([-2.5, 118], 5); // Indonesia centered
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add markers for each location
    <?php foreach ($geoData as $location): ?>
    L.marker([<?= $location['latitude'] ?>, <?= $location['longitude'] ?>])
        .addTo(map)
        .bindPopup("<?= $location['location_name'] ?>: <?= $location['voter_count'] ?> voters");
    <?php endforeach; ?>

    // Export functionality
    document.getElementById('exportTurnoutPNG').addEventListener('click', function() {
        var link = document.createElement('a');
        link.download = 'voter_turnout.png';
        link.href = turnoutChart.toBase64Image();
        link.click();
    });

    document.getElementById('exportVerificationPNG').addEventListener('click', function() {
        var link = document.createElement('a');
        link.download = 'verification_status.png';
        link.href = verificationChart.toBase64Image();
        link.click();
    });

    // CSV export would require additional server-side functionality
});
</script>

<?= $this->endSection() ?>