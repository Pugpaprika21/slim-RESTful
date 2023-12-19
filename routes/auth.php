<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/auth', function (Request $request, Response $response) {
    $users = $this->get('db')->getAll('SELECT * FROM users');
    
    return json($response, ['data' => $users]);
});
