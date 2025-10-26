<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixVotesIndexes extends Migration
{
	public function up()
	{
		// Fix incorrect UNIQUE indexes on votes table.
		// We want ONLY a composite UNIQUE (election_id, voter_id),
		// and non-unique indexes on election_id, candidate_id, voter_id.

		// Drop wrongly created unique single-column indexes if they exist
		try { $this->db->query('ALTER TABLE votes DROP INDEX votes_election_id_idx'); } catch (\Throwable $e) {}
		try { $this->db->query('ALTER TABLE votes DROP INDEX votes_candidate_id_idx'); } catch (\Throwable $e) {}
		try { $this->db->query('ALTER TABLE votes DROP INDEX votes_voter_id_idx'); } catch (\Throwable $e) {}

		// Recreate them as NON-UNIQUE indexes
		try { $this->db->query('ALTER TABLE votes ADD INDEX votes_election_id_idx (election_id)'); } catch (\Throwable $e) {}
		try { $this->db->query('ALTER TABLE votes ADD INDEX votes_candidate_id_idx (candidate_id)'); } catch (\Throwable $e) {}
		try { $this->db->query('ALTER TABLE votes ADD INDEX votes_voter_id_idx (voter_id)'); } catch (\Throwable $e) {}

		// Ensure the composite unique (election_id, voter_id) exists
		// Try to add it; if it already exists, ignore
		try { $this->db->query('ALTER TABLE votes ADD UNIQUE INDEX votes_election_voter_unique (election_id, voter_id)'); } catch (\Throwable $e) {}
	}

	public function down()
	{
		// Attempt to revert changes: drop non-unique indexes and re-add them as UNIQUE (original buggy state)
		// Not ideal, but provides a reversible migration.
		try { $this->db->query('ALTER TABLE votes DROP INDEX votes_election_id_idx'); } catch (\Throwable $e) {}
		try { $this->db->query('ALTER TABLE votes DROP INDEX votes_candidate_id_idx'); } catch (\Throwable $e) {}
		try { $this->db->query('ALTER TABLE votes DROP INDEX votes_voter_id_idx'); } catch (\Throwable $e) {}

		// Recreate the buggy UNIQUE indexes (for down migration only)
		try { $this->db->query('ALTER TABLE votes ADD UNIQUE INDEX votes_election_id_idx (election_id)'); } catch (\Throwable $e) {}
		try { $this->db->query('ALTER TABLE votes ADD UNIQUE INDEX votes_candidate_id_idx (candidate_id)'); } catch (\Throwable $e) {}
		try { $this->db->query('ALTER TABLE votes ADD UNIQUE INDEX votes_voter_id_idx (voter_id)'); } catch (\Throwable $e) {}

		// Keep the composite unique as is; if needed, another migration should handle removal
	}
}

