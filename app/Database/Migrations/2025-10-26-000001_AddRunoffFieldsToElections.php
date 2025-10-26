<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRunoffFieldsToElections extends Migration
{
    public function up()
    {
        $fields = [
            'parent_election_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'created_by'
            ],
            'is_runoff' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'parent_election_id'
            ],
            'finalized_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'is_runoff'
            ],
        ];

        $this->forge->addColumn('elections', $fields);

        // Add foreign key for parent_election_id
        $this->forge->addForeignKey('parent_election_id', 'elections', 'id', 'SET NULL', 'CASCADE', 'fk_parent_election');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('elections', 'fk_parent_election');
        
        // Drop columns
        $this->forge->dropColumn('elections', ['parent_election_id', 'is_runoff', 'finalized_at']);
    }
}
