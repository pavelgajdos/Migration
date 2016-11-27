<?php

namespace PG\Migration\Console\Commands;

use Skritek\Migration\MigrationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMigrationCommand extends Command
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
        $this->setName('db:migration:create');
        $this->setDescription('Creates a new migration file');
        $definition = new InputDefinition();
        $definition->addArgument(
            new InputArgument(
                'name',
                InputArgument::OPTIONAL,
                'Name of the new migration file. The value will be prefixed with a sequential migration number.'
            )
        );
        $this->setDefinition($definition);
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $name = $input->getArgument('name');
        } catch (InvalidArgumentException $e) {
            $name = null;
        }

        if ($name) {
            $this->migrationManager->createMigration($name);
        } else {
            $this->migrationManager->createMigration();
        }
    }
}
