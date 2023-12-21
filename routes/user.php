<?php

use App\Application\Controller\User\UserController;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

$app->group('/user', function (Group $group) {
    $group->get('/showAll', UserController::class . ':showAll');
    $group->get('/showOne/{id}', UserController::class . ':showOne');
    $group->post('/create', UserController::class . ':create');
});
