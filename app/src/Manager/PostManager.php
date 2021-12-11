<?php

namespace App\Manager;

use App\Core\BaseClasses\BaseManager;

class PostManager extends BaseManager
{
    public function getAllPosts()
    {
        return $this->pdo
            ->query('SELECT * FROM test')
            ->fetchAll(\PDO::FETCH_FUNC, function () {
                var_dump(func_get_args());
            });
    }
}