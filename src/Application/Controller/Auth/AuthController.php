<?php

declare(strict_types=1);

namespace App\Application\Controller\Auth;

use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class AuthController
{
    public function __construct(private ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function generateTokenJWT(Request $request, Response $response, array $agre): Response
    {
        $db = $this->container->get('db');
        $key = $this->container->get('jwt_secret_key');

        $id = esc($agre['id']);
        $user = $db->findOne('users', 'id = ?', [$id]);
        if ($user) {
            $user = $db->exportAll($user)[0];

            $issued_at = time();
            $payload = [
                'iat' => $issued_at,
                'exp' => ($issued_at + 60),
                'sub' => [
                    'id' => (int)$user['id'],
                    'username' => $user['username'],
                    'password' => $user['password']
                ],
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');
            $db->close();

            return json($response, ['token' =>  $jwt]);
        }

        throw new HttpNotFoundException($request, 'User Not Found..');
    }

    public function validateTokenJWT(Request $request, Response $response): Response
    {
        $key = $this->container->get('jwt_secret_key');

        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            return json($response, ['msg' => 'Unauthorized..'], 401);
        }

        $jwt = str_replace('Bearer ', '', $authHeader[0]);

        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            return json($response, ['data' => $decoded]);
        } catch (ExpiredException $e) {
            return json($response, ['msg' => 'Token expired..'], 401);
        } catch (SignatureInvalidException $e) {
            return json($response, ['msg' => 'Invalid token signature..'], 401);
        } catch (BeforeValidException $e) {
            return json($response, ['msg' => 'Token not valid yet..'], 401);
        } catch (Exception $e) {
            return json($response, ['msg' => 'Invalid token..'], 401);
        }
    }
}