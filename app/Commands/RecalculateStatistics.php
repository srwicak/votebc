<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\VoteModel;
use App\Models\ElectionModel;
use App\Models\CandidateModel;

class RecalculateStatistics extends BaseCommand
{
    protected $group       = 'Elections';
    protected $name        = 'statistics:recalculate';
    protected $description = 'Recalculates statistics for all completed elections';

    public function run(array $params)
    {
        $electionModel = new ElectionModel();
        $voteModel = new VoteModel();
        $candidateModel = new CandidateModel();

        // Get all completed elections
        $elections = $electionModel->where('status', 'completed')
                                  ->orWhere('end_time <', date('Y-m-d H:i:s'))
                                  ->findAll();

        if (empty($elections)) {
            CLI::write('No completed elections found.', 'yellow');
            return;
        }

        CLI::write('Found ' . count($elections) . ' completed elections. Recalculating statistics...', 'yellow');

        foreach ($elections as $election) {
            CLI::write("Processing election #{$election['id']}: {$election['title']}", 'green');

            // Get vote counts
            $results = $voteModel->getElectionResults($election['id']);
            $totalVotes = $voteModel->getTotalVotes($election['id']);
            $candidates = $candidateModel->getCandidatesWithUser($election['id']);

            CLI::write("  Total votes: {$totalVotes}", 'white');
            CLI::write("  Results:", 'white');

            // If no votes, just show message
            if (empty($results) || $totalVotes == 0) {
                CLI::write("  No votes recorded for this election.", 'yellow');
                continue;
            }

            // Format results
            $formattedResults = [];
            foreach ($results as $result) {
                $candidateName = "Unknown";
                foreach ($candidates as $candidate) {
                    if ($candidate['id'] == $result['candidate_id']) {
                        $candidateName = $candidate['user_name'] ?? $candidate['candidate_name'] ?? "Candidate #{$candidate['id']}";
                        break;
                    }
                }

                $percentage = $totalVotes > 0 ? ($result['vote_count'] / $totalVotes) * 100 : 0;
                $formattedResults[] = [
                    'candidate_id' => $result['candidate_id'],
                    'name' => $candidateName,
                    'votes' => $result['vote_count'],
                    'percentage' => round($percentage, 2) . '%'
                ];
            }

            // Sort by vote count (descending)
            usort($formattedResults, function($a, $b) {
                return $b['votes'] - $a['votes'];
            });

            // Display results table
            $tbody = [];
            foreach ($formattedResults as $index => $result) {
                $rank = $index + 1;
                $tbody[] = [
                    $rank,
                    $result['name'],
                    $result['votes'],
                    $result['percentage']
                ];
            }

            CLI::table($tbody, ['Rank', 'Candidate', 'Votes', 'Percentage']);
        }

        CLI::write('All statistics have been recalculated successfully.', 'green');
    }
}
