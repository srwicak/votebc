<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBlockchainElectionIdMigration extends Migration
{
    public function up()
    {
        $this->forge->addColumn('blockchain_transactions', [
            'blockchain_election_id' => [
                'type'       => 'BIGINT',
                'null'       => true,
                'comment'    => 'Unique blockchain election ID (different from database election_id)'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('blockchain_transactions', 'blockchain_election_id');
    }
}
