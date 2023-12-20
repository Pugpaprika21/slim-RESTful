<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

$app->group('/user', function (Group $group) {
    $group->get('/all', function (Request $request, Response $response) {
        try {
            $db = $this->get('db');
            $users = $db->findAll('users');
            if ($users) {
                $users = $db->exportAll($users);
                $db->close();
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return json($response, $users);
    });

    $group->get('/one/{id}', function (Request $request, Response $response, array $agre) {
        try {
            $id = $agre['id'];
            $db = $this->get('db');
            
            $user = $db->exportAll($db->findOne('users', 'id = ?', [$id]) ?? []);
            $db->close();
        } catch (\Throwable $th) {
            throw $th;
        }

        return json($response, $user);
    });
});
