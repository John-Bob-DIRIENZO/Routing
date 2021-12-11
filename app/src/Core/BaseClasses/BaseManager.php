<?php

namespace App\Core\BaseClasses;

use App\Core\Interfaces\ConnectionInterface;
use Psr\Container\ContainerInterface;

abstract class BaseManager
{
    protected \PDO $pdo;

    /**
     * @param ConnectionInterface $pdo
     */
    public function __construct(ConnectionInterface $pdo)
    {
        $this->pdo = $pdo->getConnection();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        $shortName = (new \ReflectionClass($this))->getShortName();
        $entityName = 'App\\Entity\\' . str_replace('Manager', '', $shortName);
        $this->pdo->exec((new $entityName)->makeSqlCreateTableQuery());
    }
}