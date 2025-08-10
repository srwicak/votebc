<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCandidatesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'candidate_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'vice_candidate_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'election_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'photo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'vision' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'mission' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'programs' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('candidate_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('vice_candidate_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('election_id', 'elections', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('candidates', true);
    }

    public function down()
    {
        $this->forge->dropTable('candidates', true);
    }
}