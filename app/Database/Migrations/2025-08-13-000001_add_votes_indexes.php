<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVotesIndexes extends Migration
{
    public function up()
    {
        // Add indexes to improve query performance
        // Check if indexes exist first to avoid errors
        // NOTE: These must be NON-UNIQUE indexes.
        // The UNIQUE constraint for voting is handled by a composite unique (election_id, voter_id)
        $this->forge->addKey('election_id', false, false, 'votes_election_id_idx');
        $this->forge->addKey('candidate_id', false, false, 'votes_candidate_id_idx');
        $this->forge->addKey('voter_id', false, false, 'votes_voter_id_idx');
        
        // Apply the indexes to the votes table
        $this->forge->processIndexes('votes');
    }

    public function down()
    {
        // We cannot drop specific indexes easily with the forge
        // We'll use direct queries but handle exceptions
        try {
            $this->db->query('ALTER TABLE votes DROP INDEX votes_election_id_idx');
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
        
        try {
            $this->db->query('ALTER TABLE votes DROP INDEX votes_candidate_id_idx');
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
        
        try {
            $this->db->query('ALTER TABLE votes DROP INDEX votes_voter_id_idx');
        } catch (\Exception $e) {
            // Index doesn't exist, ignore
        }
    }
}
