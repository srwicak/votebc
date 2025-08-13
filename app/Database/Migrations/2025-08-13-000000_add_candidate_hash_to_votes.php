<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCandidateHashToVotes extends Migration
{
    public function up()
    {
        // Check if candidate_hash column already exists
        if (!$this->db->fieldExists('candidate_hash', 'votes')) {
            // Add candidate_hash column
            $this->forge->addColumn('votes', [
                'candidate_hash' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'candidate_id'
                ]
            ]);
        }
    }

    public function down()
    {
        // Drop candidate_hash column if it exists
        if ($this->db->fieldExists('candidate_hash', 'votes')) {
            $this->forge->dropColumn('votes', 'candidate_hash');
        }
    }
}
