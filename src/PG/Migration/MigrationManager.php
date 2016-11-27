<?php

namespace Skritek\Migration;

use Dibi\Connection;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Tracy\Debugger;

class MigrationManager
{
    private $path;

    /** @var Connection */
    private $connection;



    public function __construct($dir, Connection $connection)
    {
        $this->path = rtrim($dir, "/") . "/";
        $this->connection = $connection;
    }



    public function migrate()
    {
        $info = $this->findUnprocessedMigrations();

        $totalCount = count($info);

        if ($totalCount == 0) {
            echo "Nothing to migrate." . PHP_EOL;
            return;
        }

        $count = 0;
        foreach ($info as $id => $path) {
            echo "Processing migration #$id: ";
            $result = $this->processMigration($path);

            echo ($result ? "OK" : "error") . PHP_EOL;

            if (!$result) {
                echo "Error, ending prematurely." . PHP_EOL;
                break;
            }
            $count++;
        }
        echo "Finished. $count out of $totalCount successfully processed." . PHP_EOL;
    }



    private function processMigration($path)
    {
        $this->connection->begin();
        try {
            $sql = file_get_contents($path);
            $this->connection->nativeQuery($sql);
            rename($path, Strings::replace($path, "/\.sql$/", "_OK.sql"));
        } catch (\Exception $e) {
            Debugger::log($e, Debugger::ERROR);
            $this->connection->rollback();
            return false;
        }
        $this->connection->commit();
        return true;
    }



    public function createMigration($name = "migration")
    {
        $filename = $this->getNextMigrationNumber() . "_" . Strings::toAscii($name) . ".sql";
        file_put_contents($this->path . $filename, '/* insert sql code here */');

        if (file_exists($this->path . $filename)) {
            echo "Migration file was created: '" . $this->path . $filename . "'" . PHP_EOL;
            echo "Add your SQL queries to this file." . PHP_EOL;
        } else {
            echo "ERROR: Migration file could not be created at '" . $this->path . $filename . "'" . PHP_EOL;
        }
    }



    public function printUnprocessedMigrations()
    {
        $unprocessed = $this->findUnprocessedMigrations();

        echo "Unprocessed migrations:" . PHP_EOL;

        if (!$unprocessed) {
            echo "No unprocessed migrations." . PHP_EOL;
            return;
        }

        foreach ($unprocessed as $number => $path) {
            echo "Migration #$number at\t$path" . PHP_EOL;
        }
    }



    public function getNextMigrationNumber()
    {
        $lastMigration = $this->findLastMigration();
        if ($lastMigration) {
            $id = $lastMigration[0];
            return $id + 1;
        } else {
            return 1;
        }
    }



    public function findLastProcessedMigration()
    {
        $paths = [];
        foreach ($this->findFiles("*OK.sql") as $key => $file) {
            $id = $this->parseNumber($key);
            $paths[$id] = $key;
        }
        if ($paths) {
            $keys = array_keys($paths);
            rsort($keys);
            return [$keys[0], $paths[$keys[0]]];
        }
        return null;
    }



    public function findLastMigration()
    {
        $paths = [];
        foreach ($this->findFiles() as $key => $file) {
            $id = $this->parseNumber($key);
            $paths[$id] = $key;
        }
        if ($paths) {
            $keys = array_keys($paths);
            rsort($keys);
            return [$keys[0], $paths[$keys[0]]];
        }
        return null;
    }



    private function findUnprocessedMigrations()
    {
        $paths = [];
        foreach ($this->findFiles("*.sql", "*OK.sql") as $key => $file) {
            $id = $this->parseNumber($key);
            $paths[$id] = $key;
        }
        $items = [];
        if ($paths) {
            $keys = array_keys($paths);
            sort($keys);
            foreach ($keys as $key) {
                $items[$key] = $paths[$key];
            }
        }
        return $items;
    }



    private function findFiles($mask = "*.sql", $exclude = null)
    {
        return Finder::findFiles($mask)->exclude($exclude)->in($this->path);
    }



    private function parseNumber($path)
    {
        $match = Strings::match($path, "|/([0-9]+)_.*\.sql|");
        $number = $match[1];
        return (int)$number;
    }
}