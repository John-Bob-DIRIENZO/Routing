<?php

namespace App\Controller;

use App\Core\BaseClasses\BaseController;
use App\Core\Utils\DIC;
use App\Entity\Post;

class PostController extends BaseController
{
    public function getIndex()
    {
//        var_dump((new Post())->createTableIfNotExist()); die;
        var_dump((DIC::autowire('PostManager'))->getAllPosts());
//        (DIC::autowire('CommentManager'));
    }

    public function getShow($id, $mainId)
    {
        var_dump(get_called_class()); die();
        var_dump($this->HTTPRequest->getQuery(), $id, $mainId);
    }
}