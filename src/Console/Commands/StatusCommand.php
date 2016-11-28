<?php

namespace PG\Migration\Console\Commands;

use Skritek\Migration\MigrationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command
{

    /** @var MigrationManager */
    private $migrationManager;



    public function __construct(MigrationManager $migrationManager)
    {
        parent::__construct();
        $this->migrationManager = $migrationManager;
    }



    protected function configure()
    {
        $this->setName('pg:migration:status');
        $this->setDescription('Displays a list of migrations to be processed.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrationManager->printUnprocessedMigrations();
    }
}
