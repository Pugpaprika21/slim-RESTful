<?php

$container->set('db', function () {
    global $env;

    if (!empty($env['DB_USERNAME'])) {
        $rb = new R();
        $rb->setup($env['DB_CONNECT_DNS'], $env['DB_USERNAME'], '');
        $rb->debug(false);
        $rb->freeze(false);
        return $rb;
    }

    throw new Exception('R Instance Not Found...');
});
