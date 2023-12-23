<?php

use App\Application\Controller\Auth\AuthController;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

$app->group('/auth', function (Group $group) {
    $group->get('/gen-token/{id}', AuthController::class . ':generateTokenJWT');
    $group->post('/vali-token', AuthController::class . ':validateTokenJWT');
});
