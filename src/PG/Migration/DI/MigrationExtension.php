<?php

namespace PG\Migration\DI;

use PG\Migration\Console\Commands\CreateMigrationCommand;
use PG\Migration\Console\Commands\MigrateCommand;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;
use Nette\Utils\Validators;
use Skritek\Migration\MigrationManager;

class MigrationExtension extends CompilerExtension
{

    const TAG_JOSEKI_COMMAND = 'joseki.console.command';
    const TAG_KDYBY_COMMAND = 'kdyby.console.command';

    public $defaults = [
        'migrationDir' => '',
    ];



    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig($this->defaults);

        Validators::assert($config['migrationDir'], 'string');

        $manager = $container->addDefinition($this->prefix('manager'))
            ->setClass(MigrationManager::class, [$config['migrationDir']]);

        $container->addDefinition($this->prefix('command.create'))
            ->setClass(CreateMigrationCommand::class)
            ->addTag(self::TAG_JOSEKI_COMMAND)
            ->addTag(self::TAG_KDYBY_COMMAND);

        $container->addDefinition($this->prefix('command.migrate'))
            ->setClass(MigrateCommand::class)
            ->addTag(self::TAG_JOSEKI_COMMAND)
            ->addTag(self::TAG_KDYBY_COMMAND);
    }

}
