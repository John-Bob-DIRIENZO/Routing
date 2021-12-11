<?php

namespace App\Controller;

use App\Core\BaseClasses\BaseController;

class ApiController extends BaseController
{
    public function getUploadImage()
    {
        $this->HTTPResponse->redirect('/uploads/random2.jpg');
    }

    public function getPosts($id)
    {
        if ($id) {
            $this->renderJSON([
                'articleId' => $id
            ]);
        } else {
            $this->renderJSON([
                'liste des posts' => 'on en aura'
            ]);
        }
    }
}