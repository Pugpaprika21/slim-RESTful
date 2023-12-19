<?php

require __DIR__ . '../../container/database/Redbean.php';

$container->set('db', function () {
    global $env;
    if (isset($env['DB_USERNAME'])) {
        R::setup($env['DB_CONNECT_DNS'], $env['DB_USERNAME'], '');
        R::debug(false);
        return new R();
    }
    throw new Exception('db name not define ..');
});
