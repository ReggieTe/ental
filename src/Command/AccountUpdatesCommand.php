<?php 
namespace App\Command;

use App\Service\ClientService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;


#[AsCommand(
    name: 'app:alert:accountUpdates',
    description: 'Send rental updates',
    hidden: false,
    aliases: ['app:alert:accountUpdates']
)]
class AccountUpdatesCommand extends Command
{

    private  $clientService;

    protected static $defaultDescription = 'Send rental updates';
    
    protected static $defaultName = 'app:alert:accountUpdates';

    public function __construct(ClientService $clientService) {
        $this->clientService = $clientService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);        
        $count = $this->clientService->accountUpdates();
        $io->success(sprintf(' "%d"  Account updates sent.', $count));
        return Command::SUCCESS;
    }
}