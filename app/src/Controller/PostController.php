<?php

namespace App\Controller;

use App\Core\BaseClasses\BaseController;
use App\Core\Utils\DIC;

class PostController extends BaseController
{
    public function getIndex()
    {
        echo (DIC::autowire('PostManager'))->getAllPosts();
    }

    public function getShow($id, $mainId)
    {
        var_dump($this->HTTPRequest->getQuery(), $id, $mainId);
    }
}