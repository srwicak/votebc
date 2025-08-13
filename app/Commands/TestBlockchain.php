<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\Blockchain;

class TestBlockchain extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Blockchain';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'blockchain:test';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Test blockchain integration and configuration';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'blockchain:test [test|status|vote]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'action' => 'Action to perform (test, status, vote)'
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--sim' => 'Force simulation mode',
        '--real' => 'Force real mode',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Testing Blockchain Integration', 'green');
        CLI::write('---------------------------', 'green');
        
        // Check environment configuration
        CLI::write('Current environment settings:', 'yellow');
        CLI::write('RPC URL: ' . getenv('blockchain.rpc_url'));
        CLI::write('Simulation Mode: ' . getenv('blockchain.simulation_mode'));
        CLI::write('Chain ID: ' . getenv('blockchain.chain_id'));
        CLI::write('Contract Address: ' . getenv('blockchain.contract_address'));
        CLI::write('Private Key: ' . (getenv('blockchain.private_key') ? substr(getenv('blockchain.private_key'), 0, 10) . '...' : 'Not set'));
        CLI::newLine();
        
        // Initialize blockchain
        $blockchain = new Blockchain();
        
        // Override simulation mode if requested
        if (CLI::getOption('sim') !== null) {
            CLI::write('Forcing SIMULATION mode', 'yellow');
            $blockchain->simulationMode = true;
        }
        
        if (CLI::getOption('real') !== null) {
            CLI::write('Forcing REAL mode', 'yellow');
            $blockchain->simulationMode = false;
        }
        
        // Get action from params
        $action = $params[0] ?? 'status';
        
        CLI::write('Executing action: ' . $action, 'green');
        CLI::newLine();
        
        switch ($action) {
            case 'status':
                $this->checkStatus($blockchain);
                break;
                
            case 'test':
                $this->testTransaction($blockchain);
                break;
                
            case 'vote':
                $this->castVote($blockchain);
                break;
                
            default:
                CLI::error('Unknown action: ' . $action);
                break;
        }
    }
    
    /**
     * Check blockchain status
     */
    private function checkStatus(Blockchain $blockchain)
    {
        CLI::write('Checking blockchain status...', 'yellow');
        
        $status = $blockchain->checkBlockchainStatus();
        
        CLI::write('Status: ' . json_encode($status, JSON_PRETTY_PRINT));
    }
    
    /**
     * Test a blockchain transaction
     */
    private function testTransaction(Blockchain $blockchain)
    {
        CLI::write('Testing blockchain transaction...', 'yellow');
        
        $result = $blockchain->testTransaction();
        
        CLI::write('Transaction Result: ' . json_encode($result, JSON_PRETTY_PRINT));
    }
    
    /**
     * Cast a test vote
     */
    private function castVote(Blockchain $blockchain)
    {
        CLI::write('Casting test vote...', 'yellow');
        
        $electionId = 1; // Back to election 1, but now with unique election ID generation
        $candidateId = 1;
        $voterId = 'test-voter-' . time();
        $metadata = ['test' => true, 'timestamp' => time()];
        
        $result = $blockchain->castVote($electionId, $candidateId, $voterId, $metadata);
        
        CLI::write('Vote Result: ' . json_encode($result, JSON_PRETTY_PRINT));
    }
}
