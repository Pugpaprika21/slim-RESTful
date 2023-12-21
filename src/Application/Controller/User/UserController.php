<?php

namespace App\Application\Controller\User;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    public function __construct(private ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showAll(Request $request, Response $response): Response
    {
        $db = $this->container->get('db');

        $users = $db->findAll('users');
        if ($users) {
            $users = $db->exportAll($users);
            $db->close();
        }

        return json($response, $users);
    }

    public function showOne(Request $request, Response $response, array $agre): Response
    {
        $db = $this->container->get('db');

        $id = $agre['id'];
        
        $user = $db->exportAll($db->findOne('users', 'id = ?', [$id]) ?? []);
        $db->close();

        return json($response, $user);
    }
}
