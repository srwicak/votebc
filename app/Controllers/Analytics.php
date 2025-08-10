<?php

namespace App\Controllers;

use App\Models\ElectionModel;
use App\Models\VoteModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;
use App\Libraries\Blockchain;

class Analytics extends BaseController
{
    protected $electionModel;
    protected $voteModel;
    protected $userModel;
    protected $auditLogModel;
    protected $blockchain;
    
    public function __construct()
    {
        $this->electionModel = new ElectionModel();
        $this->voteModel = new VoteModel();
        $this->userModel = new UserModel();
        $this->auditLogModel = new AuditLogModel();
        $this->blockchain = new Blockchain();
    }
    
    /**
     * Display the analytics dashboard
     */
    public function index()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to access analytics.');
        }
        
        // Get basic statistics
        $data = [
            'totalElections' => $this->electionModel->countAllResults(),
            'totalVotes' => $this->voteModel->countAllResults(),
            'totalUsers' => $this->userModel->countAllResults(),
            'avgTurnout' => $this->calculateAverageTurnout(),
            
            // Get voter turnout data for chart
            'turnoutData' => $this->getTurnoutData(),
            
            // Get verification statistics
            'verificationStats' => $this->getVerificationStats(),
            
            // Get voting activity timeline
            'activityData' => $this->getActivityData(),
            
            // Get recent blockchain transactions
            'recentTransactions' => $this->getRecentTransactions(),
            
            // Get geographic distribution data
            'geoData' => $this->getGeographicData(),
            
            // Get device usage statistics
            'deviceStats' => $this->getDeviceStats(),
            
            // Get security metrics
            'securityMetrics' => $this->getSecurityMetrics(),
        ];
        
        return view('analytics/dashboard', $data);
    }
    
    /**
     * Calculate average voter turnout across all elections
     */
    private function calculateAverageTurnout()
    {
        $elections = $this->electionModel->findAll();
        
        if (empty($elections)) {
            return 0;
        }
        
        $totalTurnout = 0;
        
        foreach ($elections as $election) {
            $eligibleVoters = $this->userModel->countEligibleVoters($election['id']);
            $actualVotes = $this->voteModel->countVotesByElection($election['id']);
            
            if ($eligibleVoters > 0) {
                $turnout = ($actualVotes / $eligibleVoters) * 100;
                $totalTurnout += $turnout;
            }
        }
        
        return round($totalTurnout / count($elections), 1);
    }
    
    /**
     * Get voter turnout data for each election
     */
    private function getTurnoutData()
    {
        $elections = $this->electionModel->findAll();
        $turnoutData = [];
        
        foreach ($elections as $election) {
            $eligibleVoters = $this->userModel->countEligibleVoters($election['id']);
            $actualVotes = $this->voteModel->countVotesByElection($election['id']);
            
            $turnoutPercentage = ($eligibleVoters > 0) 
                ? round(($actualVotes / $eligibleVoters) * 100, 1) 
                : 0;
            
            $turnoutData[] = [
                'election_id' => $election['id'],
                'election_name' => $election['title'],
                'eligible_voters' => $eligibleVoters,
                'actual_votes' => $actualVotes,
                'turnout_percentage' => $turnoutPercentage
            ];
        }
        
        return $turnoutData;
    }
    
    /**
     * Get verification statistics
     */
    private function getVerificationStats()
    {
        $totalUsers = $this->userModel->countAllResults();
        
        if ($totalUsers === 0) {
            return [
                'fully_verified' => 0,
                'partially_verified' => 0,
                'not_verified' => 0
            ];
        }
        
        $fullyVerified = $this->userModel->where([
            'email_verified' => 1,
            'otp_verified' => 1,
            'ktm_verified' => 1,
            'face_verified' => 1
        ])->countAllResults();
        
        $notVerified = $this->userModel->where([
            'email_verified' => 0,
            'otp_verified' => 0,
            'ktm_verified' => 0,
            'face_verified' => 0
        ])->countAllResults();
        
        $partiallyVerified = $totalUsers - $fullyVerified - $notVerified;
        
        return [
            'fully_verified' => $fullyVerified,
            'partially_verified' => $partiallyVerified,
            'not_verified' => $notVerified
        ];
    }
    
    /**
     * Get voting activity timeline data
     */
    private function getActivityData()
    {
        // Get votes grouped by date for the last 30 days
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as vote_count
            FROM 
                votes
            WHERE 
                created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY 
                DATE(created_at)
            ORDER BY 
                date ASC
        ");
        
        $results = $query->getResultArray();
        
        // Fill in missing dates with zero votes
        $activityData = [];
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days'));
        
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $found = false;
            
            foreach ($results as $result) {
                if ($result['date'] === $currentDate) {
                    $activityData[] = [
                        'date' => $currentDate,
                        'vote_count' => (int)$result['vote_count']
                    ];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $activityData[] = [
                    'date' => $currentDate,
                    'vote_count' => 0
                ];
            }
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        return $activityData;
    }
    
    /**
     * Get recent blockchain transactions
     */
    private function getRecentTransactions()
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT 
                b.tx_hash,
                e.title as election_name,
                b.created_at
            FROM 
                blockchain_transactions b
            JOIN 
                elections e ON b.election_id = e.id
            ORDER BY 
                b.created_at DESC
            LIMIT 10
        ");
        
        return $query->getResultArray();
    }
    
    /**
     * Get geographic distribution data
     */
    private function getGeographicData()
    {
        // This would typically come from a locations or user_locations table
        // For demonstration, we'll return sample data
        return [
            [
                'location_name' => 'Jakarta',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'voter_count' => 1250
            ],
            [
                'location_name' => 'Surabaya',
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'voter_count' => 850
            ],
            [
                'location_name' => 'Bandung',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'voter_count' => 720
            ],
            [
                'location_name' => 'Medan',
                'latitude' => 3.5952,
                'longitude' => 98.6722,
                'voter_count' => 510
            ],
            [
                'location_name' => 'Makassar',
                'latitude' => -5.1477,
                'longitude' => 119.4327,
                'voter_count' => 480
            ]
        ];
    }
    
    /**
     * Get device usage statistics
     */
    private function getDeviceStats()
    {
        // This would typically come from user_agents or a similar table
        // For demonstration, we'll return sample data
        return [
            'desktop' => 45,
            'mobile' => 48,
            'tablet' => 7
        ];
    }
    
    /**
     * Get security metrics
     */
    private function getSecurityMetrics()
    {
        // Get failed login attempts in the last 24 hours
        $failedLogins = $this->auditLogModel
            ->where('event', 'login.failed')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();
        
        // Get successful verifications in the last 24 hours
        $successfulVerifications = $this->auditLogModel
            ->where('event LIKE', 'verification.%.success')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();
        
        // Get rate limited requests in the last 24 hours
        $rateLimited = $this->auditLogModel
            ->where('event', 'security.rate_limited')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();
        
        // Get blocked suspicious IPs in the last 24 hours
        $blockedIPs = $this->auditLogModel
            ->where('event', 'security.blocked_ip')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();
        
        return [
            'failed_logins' => $failedLogins,
            'successful_verifications' => $successfulVerifications,
            'rate_limited' => $rateLimited,
            'blocked_ips' => $blockedIPs
        ];
    }
    
    /**
     * Export analytics data as CSV
     */
    public function exportCSV($type)
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to export analytics.');
        }
        
        $filename = 'analytics_' . $type . '_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        switch ($type) {
            case 'turnout':
                $data = $this->getTurnoutData();
                fputcsv($output, ['Election ID', 'Election Name', 'Eligible Voters', 'Actual Votes', 'Turnout Percentage']);
                
                foreach ($data as $row) {
                    fputcsv($output, [
                        $row['election_id'],
                        $row['election_name'],
                        $row['eligible_voters'],
                        $row['actual_votes'],
                        $row['turnout_percentage']
                    ]);
                }
                break;
                
            case 'verification':
                $stats = $this->getVerificationStats();
                fputcsv($output, ['Verification Status', 'Count']);
                fputcsv($output, ['Fully Verified', $stats['fully_verified']]);
                fputcsv($output, ['Partially Verified', $stats['partially_verified']]);
                fputcsv($output, ['Not Verified', $stats['not_verified']]);
                break;
                
            case 'activity':
                $data = $this->getActivityData();
                fputcsv($output, ['Date', 'Vote Count']);
                
                foreach ($data as $row) {
                    fputcsv($output, [$row['date'], $row['vote_count']]);
                }
                break;
                
            default:
                fputcsv($output, ['Invalid export type']);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Check if current user is an admin
     */
    private function isAdmin()
    {
        $user = session()->get('user');
        return isset($user['role']) && $user['role'] === 'admin';
    }
    
    /**
     * API method to get overview data
     */
    public function getOverview()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to access analytics.'
            ])->setStatusCode(403);
        }
        
        $data = [
            'totalElections' => $this->electionModel->countAllResults(),
            'totalVotes' => $this->voteModel->countAllResults(),
            'totalUsers' => $this->userModel->countAllResults(),
            'avgTurnout' => $this->calculateAverageTurnout()
        ];
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * API method to get turnout data
     */
    public function getTurnoutDataAPI()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to access analytics.'
            ])->setStatusCode(403);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->getTurnoutData()
        ]);
    }
    
    /**
     * API method to get verification stats
     */
    public function getVerificationStatsAPI()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to access analytics.'
            ])->setStatusCode(403);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->getVerificationStats()
        ]);
    }
    
    /**
     * API method to get activity data
     */
    public function getActivityDataAPI()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to access analytics.'
            ])->setStatusCode(403);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->getActivityData()
        ]);
    }
    
    /**
     * API method to get recent transactions
     */
    public function getRecentTransactionsAPI()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to access analytics.'
            ])->setStatusCode(403);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->getRecentTransactions()
        ]);
    }
    
    /**
     * API method to get security metrics
     */
    public function getSecurityMetricsAPI()
    {
        // Check if user is admin
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to access analytics.'
            ])->setStatusCode(403);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->getSecurityMetrics()
        ]);
    }
}