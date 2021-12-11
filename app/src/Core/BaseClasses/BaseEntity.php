<?php

namespace App\Core\BaseClasses;

use App\Core\Utils\Hydrator;

abstract class BaseEntity
{
    use Hydrator;

    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    private function readDocBloc(string $docBlocComment)
    {
        preg_match('/@SQL={(.*)}/', $docBlocComment, $match);
        return $match[1];
    }

    public function getSqlTypesFromDocBloc() : array
    {
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getDocComment()) {
                $sqlTypes[$reflectionProperty->getName()] = $this->readDocBloc($reflectionProperty->getDocComment());
            }
        }

        return $sqlTypes;
    }

    public function makeSqlCreateTableQuery()
    {
        $table = (new \ReflectionClass($this))->getShortName();
        $query = "CREATE TABLE IF NOT EXISTS `$table` (";
        foreach ($this->getSqlTypesFromDocBloc() as $name => $property) {
            if ($name !== array_key_last($this->getSqlTypesFromDocBloc())) {
                $query .= "`$name` $property, ";
            } else {
                $query .= "`$name` $property ";
            }
        }
        return $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    }
}