<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanupVotesIndexes extends Migration
{
    public function up()
    {
        // Drop problematic UNIQUE single-column index on voter_id if it exists
        foreach (['votes_voter_id_idx', 'voter_id'] as $idx) {
            try { $this->db->query("ALTER TABLE votes DROP INDEX `$idx`"); } catch (\Throwable $e) {}
        }

        // There are duplicate composite UNIQUE indexes present on some DBs:
        // - election_id_voter_id
        // - unique_vote
        // Normalize to a single canonical name: votes_election_voter_unique
        foreach (['unique_vote', 'election_id_voter_id', 'votes_election_voter_unique'] as $idx) {
            try { $this->db->query("ALTER TABLE votes DROP INDEX `$idx`"); } catch (\Throwable $e) {}
        }

        // Recreate composite UNIQUE index
        try {
            $this->db->query('ALTER TABLE votes ADD UNIQUE INDEX votes_election_voter_unique (election_id, voter_id)');
        } catch (\Throwable $e) {}

        // Ensure non-unique single-column indexes exist with canonical names
        // Avoid creating duplicates: attempt to add, ignore errors if exists
        try { $this->db->query('ALTER TABLE votes ADD INDEX idx_votes_election_id (election_id)'); } catch (\Throwable $e) {}
        try { $this->db->query('ALTER TABLE votes ADD INDEX idx_votes_candidate_id (candidate_id)'); } catch (\Throwable $e) {}
        try { $this->db->query('ALTER TABLE votes ADD INDEX idx_votes_voter_id (voter_id)'); } catch (\Throwable $e) {}
    }

    public function down()
    {
        // Best-effort revert: drop canonical indexes and recreate prior state (not re-adding buggy unique on voter_id)
        foreach (['idx_votes_election_id', 'idx_votes_candidate_id', 'idx_votes_voter_id', 'votes_election_voter_unique'] as $idx) {
            try { $this->db->query("ALTER TABLE votes DROP INDEX `$idx`"); } catch (\Throwable $e) {}
        }

        // Optionally recreate older composite unique names (not recommended, but for down migration):
        try { $this->db->query('ALTER TABLE votes ADD UNIQUE INDEX election_id_voter_id (election_id, voter_id)'); } catch (\Throwable $e) {}
    }
}
