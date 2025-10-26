<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class InspectVotesIndexes extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'inspect:votes-indexes';
    protected $description = 'Show indexes of the votes table including uniqueness info';

    public function run(array $params)
    {
        $db = Database::connect();
        try {
            $indexes = $db->query('SHOW INDEX FROM votes')->getResultArray();
        } catch (\Throwable $e) {
            CLI::error('Failed to query indexes: ' . $e->getMessage());
            return;
        }

        if (empty($indexes)) {
            CLI::write('No indexes found for table votes or table does not exist.', 'yellow');
            return;
        }

        CLI::write('Indexes for table `votes`:', 'green');
        CLI::write(str_pad('Key_name', 32) . str_pad('Column_name', 20) . str_pad('Non_unique', 12) . 'Index_type');
        CLI::write(str_repeat('-', 90));

        foreach ($indexes as $idx) {
            $keyName    = $idx['Key_name'] ?? ($idx['key_name'] ?? '');
            $colName    = $idx['Column_name'] ?? ($idx['column_name'] ?? '');
            $nonUnique  = $idx['Non_unique'] ?? ($idx['non_unique'] ?? '');
            $indexType  = $idx['Index_type'] ?? ($idx['index_type'] ?? '');

            CLI::write(
                str_pad($keyName, 32) .
                str_pad($colName, 20) .
                str_pad((string)$nonUnique, 12) .
                $indexType
            );
        }

        CLI::newLine();
        CLI::write('Non_unique = 0 means UNIQUE. Expected:', 'yellow');
        CLI::write('- Composite UNIQUE on (election_id, voter_id)');
        CLI::write('- Non-unique indexes on election_id, voter_id, candidate_id');
    }
}
