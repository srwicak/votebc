<?php

namespace App\Controllers;

use App\Models\ElectionModel;
use App\Models\CandidateModel;

class Election extends BaseController
{
    public function index()
    {
        try {
            $this->requireAuth();

            $electionModel = new ElectionModel();
            $elections = $electionModel->findAll();

            return $this->sendResponse($elections);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show($electionId)
    {
        try {
            $this->requireAuth();

            $electionModel = new ElectionModel();
            $election = $electionModel->getElectionWithDetails($electionId);

            if (!$election) {
                return $this->sendError('Pemilihan tidak ditemukan', 404);
            }

            return $this->sendResponse($election);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getCandidates($electionId)
    {
        try {
            $this->requireAuth();

            $candidateModel = new CandidateModel();
            $candidates = $candidateModel->getCandidatesWithUser($electionId);

            return $this->sendResponse($candidates);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getEligibleVoters($electionId)
    {
        try {
            $this->requireRole(['admin']);

            $electionModel = new ElectionModel();
            $voters = $electionModel->getEligibleVoters($electionId);

            return $this->sendResponse($voters);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}