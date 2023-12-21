<?php

declare(strict_types=1);

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

        $users = $db->exportAll($db->findAll('users'));
        $userNums = $db->count('users');
        $db->close();

        return json($response, ['users' => $users, 'rows' => $userNums]);
    }

    public function showOne(Request $request, Response $response, array $agre): Response
    {
        $db = $this->container->get('db');

        $id = esc($agre['id']);
        $user = $db->exportAll($db->findOne('users', 'id = ?', [$id]) ?? []);
        $db->close();

        return json($response, $user);
    }

    public function create(Request $request, Response $response): Response
    {
        $db = $this->container->get('db');

        $body = $request->getParsedBody();

        $username = esc($body['username']);
        $password = esc($body['password']);

        if ($db->findOne('users', 'username = ? OR password = ?', [$username, $password])) {
            return json($response, ['msg' => 'Username OR Password Exiting..'], 200);
        }

        $user = $db->dispense('users');
        if ($user->isEmpty()) {
            $user->username = $username;
            $user->password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            $user->first_name = esc($body['first_name']);
            $user->last_name = esc($body['last_name']);
            $user->email = esc($body['email']);
            $user->address = esc($body['address']);
            $user->phone_number = esc($body['phone_number']);
            $id = $db->store($user);
            $user = $db->findOne('users', 'id = ?', [$id]);

            $db->close();
            return json($response, ['msg' => 'Create Success..', 'user' => $user], 201);
        }

        return json($response, ['msg' => 'Create Error..'], 500);
    }

    public function delete(Request $request, Response $response, array $agre): Response
    {
        $db = $this->container->get('db');

        $id = esc($agre['id']);
        return json($response, []);
    }
}