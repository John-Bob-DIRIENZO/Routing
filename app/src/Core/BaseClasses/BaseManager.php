<?php

namespace App\Core\BaseClasses;

use App\Core\Interfaces\ConnectionInterface;

abstract class BaseManager
{
    protected \PDO $pdo;
    protected string $shortEntityName;
    protected string $entityName;

    /**
     * @param ConnectionInterface $pdo
     */
    public function __construct(ConnectionInterface $pdo)
    {
        $this->pdo = $pdo->getConnection();

        $shortManagerName = (new \ReflectionClass($this))->getShortName();
        $this->shortEntityName = str_replace('Manager', '', $shortManagerName);
        $this->entityName = 'App\\Entity\\' . $this->shortEntityName;

        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        $shortName = (new \ReflectionClass($this))->getShortName();
        $entityName = 'App\\Entity\\' . str_replace('Manager', '', $shortName);
        $this->pdo->exec($entityName::SqlCreateTableQuery());
    }

    public function findAll()
    {
        $results = $this->pdo
            ->query("SELECT * FROM `$this->shortEntityName`")
            ->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($results as $result) {
            $classes[] = new $this->entityName($result);
        }
        return $classes;
    }
}