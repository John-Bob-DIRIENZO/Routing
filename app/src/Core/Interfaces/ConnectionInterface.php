<?php

namespace App\Core\Interfaces;

use PDO;

interface ConnectionInterface
{
    /**
     * @return PDO
     */
    public function getConnection(): PDO;
}