<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlockchainTransactionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'election_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'ID of the related election',
            ],
            'vote_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID of the related vote (if applicable)',
            ],
            'tx_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Blockchain transaction hash',
            ],
            'tx_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Type of transaction (vote, election_creation, etc.)',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'pending',
                'comment'    => 'Transaction status (pending, confirmed, failed)',
            ],
            'block_number' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Block number where transaction was confirmed',
            ],
            'gas_used' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,0',
                'null'       => true,
                'comment'    => 'Gas used for the transaction',
            ],
            'gas_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,0',
                'null'       => true,
                'comment'    => 'Gas price in wei',
            ],
            'data' => [
                'type'       => 'JSON',
                'null'       => true,
                'comment'    => 'Additional transaction data',
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'comment'    => 'Timestamp when the transaction was created',
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'comment'    => 'Timestamp when the transaction was last updated',
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('election_id');
        $this->forge->addKey('vote_id');
        $this->forge->addKey('tx_hash');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        
        $this->forge->createTable('blockchain_transactions');
    }

    public function down()
    {
        $this->forge->dropTable('blockchain_transactions');
    }
}