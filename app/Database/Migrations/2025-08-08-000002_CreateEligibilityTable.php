<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEligibilityTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'election_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'faculty_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'department_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addForeignKey('election_id', 'elections', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('faculty_id', 'faculties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('eligibility');
    }

    public function down()
    {
        $this->forge->dropTable('eligibility');
    }
}