<?php

use App\Core\Router\Router;
use App\Core\Utils\DIC;

require './../vendor/autoload.php';

(new DIC('./../src/'))->run();

$router = new Router('./../config/routes.yaml');
$router->runYAML();