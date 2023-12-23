<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Slim\Exception\HttpUnauthorizedException;

class AuthMiddleware implements Middleware
{
    public function __construct(private ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $handler->handle($request);
        return $this->validateTokenJWT($request, $handler);
    }

    private function validateTokenJWT(Request $request, RequestHandler $handler): Response
    {
        $key = $this->container->get('jwt_secret_key');

        $response = $handler->handle($request);

        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            throw new HttpUnauthorizedException($request, 'Unauthorized');
        }

        $jwt = str_replace('Bearer ', '', $authHeader[0]);

        try {
            // $request = $request->withAttribute('session', $_SESSION);
            JWT::decode($jwt, new Key($key, 'HS256'));
            return $response;
        } catch (ExpiredException $e) {
            throw new HttpUnauthorizedException($request, 'Token expired..');
        } catch (SignatureInvalidException $e) {
            throw new HttpUnauthorizedException($request, 'Invalid token signature..');
        } catch (BeforeValidException $e) {
            throw new HttpUnauthorizedException($request, 'Token not valid yet..');
        } catch (Exception $e) {
            throw new HttpUnauthorizedException($request, 'Invalid token..');
        }
    }
}
