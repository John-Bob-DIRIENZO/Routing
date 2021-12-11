<?php

namespace App\Entity;

use App\Core\BaseClasses\BaseEntity;

class Comment extends BaseEntity
{
    /**
     * @SQL={int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT}
     */
    private int $id;
    /**
     * @SQL={text NOT NULL}
     */
    private string $content;
}