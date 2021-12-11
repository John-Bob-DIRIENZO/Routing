<?php

namespace App\Core\BaseClasses;

use App\Core\Utils\Hydrator;
use App\Core\Utils\Regex;

abstract class BaseEntity
{
    use Hydrator;

    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    public static function getSqlTypesFromDocBloc() : array
    {
        $reflection = new \ReflectionClass(static::class);
        foreach ($reflection->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getDocComment()) {
                $sqlTypes[$reflectionProperty->getName()] = Regex::readFromDocBloc('SQL', $reflectionProperty->getDocComment());
            }
        }

        return $sqlTypes;
    }

    public static function sqlCreateTableQuery()
    {
        $table = (new \ReflectionClass(static::class))->getShortName();
        $query = "CREATE TABLE IF NOT EXISTS `$table` (";
        foreach (self::getSqlTypesFromDocBloc() as $name => $property) {
            if ($name !== array_key_last(self::getSqlTypesFromDocBloc())) {
                $query .= "`$name` $property, ";
            } else {
                $query .= "`$name` $property ";
            }
        }
        return $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    }
}