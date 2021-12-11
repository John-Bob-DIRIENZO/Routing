<?php

namespace App\Controller;

use App\Core\BaseClasses\BaseController;
use App\Core\Utils\DIC;

class PostController extends BaseController
{
    public function getIndex()
    {
        var_dump((DIC::autowire('PostManager'))->findAll());
    }

    public function getShow($id, $mainId)
    {
        var_dump($this->HTTPRequest->getQuery(), $id, $mainId);
    }
}