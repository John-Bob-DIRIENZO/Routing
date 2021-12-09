<?php

namespace App\Core\BaseClasses;

use App\Core\Interfaces\ConnectionInterface;
use Psr\Container\ContainerInterface;

class BaseManager
{
    protected \PDO $pdo;

    /**
     * @param ConnectionInterface $pdo
     */
    public function __construct(ConnectionInterface $pdo)
    {
        $this->pdo = $pdo->getConnection();
    }
}