<?php

namespace App\Controllers;

use App\Models\ElectionModel;
use App\Models\VoteModel;
use App\Models\CandidateModel;

class Statistics extends BaseController
{
    public function getElectionResults($electionId)
    {
        try {
            $this->requireAuth();

            $electionModel = new ElectionModel();
            $voteModel = new VoteModel();
            $candidateModel = new CandidateModel();

            $election = $electionModel->find($electionId);
            if (!$election) {
                return $this->sendError('Pemilihan tidak ditemukan', 404);
            }

            $results = $voteModel->getElectionResults($electionId);
            $totalVotes = $voteModel->getTotalVotes($electionId);
            $candidates = $candidateModel->getCandidatesWithUser($electionId);

            // Format hasil
            $formattedResults = [];
            foreach ($results as $result) {
                $candidate = null;
                foreach ($candidates as $c) {
                    if ($c['id'] == $result['candidate_id']) {
                        $candidate = $c;
                        break;
                    }
                }

                $percentage = $totalVotes > 0 ? ($result['vote_count'] / $totalVotes) * 100 : 0;

                $formattedResults[] = [
                    'candidate' => $candidate,
                    'vote_count' => (int)$result['vote_count'],
                    'percentage' => round($percentage, 2)
                ];
            }

            // Urutkan berdasarkan vote_count descending
            usort($formattedResults, function($a, $b) {
                return $b['vote_count'] <=> $a['vote_count'];
            });

            return $this->sendResponse([
                'election' => $election,
                'total_votes' => $totalVotes,
                'results' => $formattedResults
            ]);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getRealtimeStats($electionId)
    {
        try {
            $this->requireAuth();

            $electionModel = new ElectionModel();
            $voteModel = new VoteModel();

            $election = $electionModel->find($electionId);
            if (!$election) {
                return $this->sendError('Pemilihan tidak ditemukan', 404);
            }

            $totalVotes = $voteModel->getTotalVotes($electionId);
            $eligibleVoters = count($electionModel->getEligibleVoters($electionId));
            $participationRate = $eligibleVoters > 0 ? ($totalVotes / $eligibleVoters) * 100 : 0;

            return $this->sendResponse([
                'election' => $election,
                'total_votes' => $totalVotes,
                'eligible_voters' => $eligibleVoters,
                'participation_rate' => round($participationRate, 2)
            ]);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}