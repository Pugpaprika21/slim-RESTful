<?php

use App\Application\Controller\User\UserController;
use App\Application\Middleware\AuthMiddleware;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

$app->group('/user', function (Group $group) {
    $group->post('/create', UserController::class . ':create');
    $group->get('/showAll[{page}/{limit}]', UserController::class . ':showAll');
    $group->get('/showOne/{id}', UserController::class . ':showOne');
    $group->put('/update/{id}', UserController::class . ':update');
    $group->delete('/delete/{id}', UserController::class . ':delete');
})->add(AuthMiddleware::class);
