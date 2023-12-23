<?php

$container->set('db', function () use ($env) {
    if (!empty($env['DB_USERNAME'])) {
        $rb = new R();
        $rb->setup($env['DB_CONNECT_DNS'], $env['DB_USERNAME'], '');
        $rb->debug(false);
        $rb->freeze(false);
        return $rb;
    }

    throw new Exception('R Instance Not Found...');
});

$container->set('jwt_secret_key', function () use ($env) {
    return !empty($env['APP_API_KEY']) ? $env['APP_API_KEY'] : "";
});
