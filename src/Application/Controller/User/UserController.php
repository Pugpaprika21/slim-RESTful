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

        $params = $request->getQueryParams();

        $limit = !empty($params['page']) ? esc($params['page']) : 1;
        $offset = !empty($params['limit']) ? esc($params['limit']) : $db->count('users');

        $users = $db->exportAll($db->findAll('users', 'ORDER BY created_at DESC LIMIT ?, ?', [$limit, $offset]));
        $db->close();

        return json($response, ['users' => $users, 'rows' => count($users)]);
    }

    public function showOne(Request $request, Response $response, array $agre): Response
    {
        $db = $this->container->get('db');

        $id = esc($agre['id']);
        $user = $db->findOne('users', 'id = ?', [$id]);
        if (!$user) {
            return json($response, ['msg' => 'User Not Found..'], 204);
        }

        $user = $db->exportAll($user);
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
            $user->profile = '';
            $id = $db->store($user);
            $user = $db->findOne('users', 'id = ?', [$id]);

            $db->close();
            return json($response, ['msg' => 'Create Success..', 'user' => $user], 201);
        }

        return json($response, ['msg' => 'Create Error..'], 500);
    }

    public function update(Request $request, Response $response, array $agre): Response
    {
        $db = $this->container->get('db');

        $body = $request->getParsedBody();

        $id = esc($agre['id']);
        $username = esc($body['username']);
        $password = esc($body['password']);

        if ($db->findOne('users', 'id = ?', [$id])) {

            if ($db->findOne('users', 'username = ? OR password = ?', [$username, $password])) {
                return json($response, ['msg' => 'Username OR Password Exiting..'], 200);
            }

            $user = $db->load('users', $id);
            $user->username = $username;
            $user->password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            $user->first_name = esc($body['first_name']);
            $user->last_name = esc($body['last_name']);
            $user->email = esc($body['email']);
            $user->address = esc($body['address']);
            $user->phone_number = esc($body['phone_number']);
            $user->profile = '';
            $id = $db->store($user);
            $user = $db->findOne('users', 'id = ?', [$id]);
            $db->close();

            return json($response, ['msg' => 'Update User Success..', 'user' => $user]);
        }

        return json($response, ['msg' => 'User Not Exiting..'], 500);
    }

    public function delete(Request $request, Response $response, array $agre): Response
    {
        $db = $this->container->get('db');

        $id = esc($agre['id']);
        $user = $db->findOne('users', 'id = ?', [$id]);
        if (!$user) {
            return json($response, ['msg' => 'User Not Found..']);
        }

        $db->trash('users', $id);
        $db->close();
        return json($response, ['msg' => 'Delete User Success']);
    }
}
