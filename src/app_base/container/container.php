<?php

$container->set('db', function () {
    global $env;
    if (!empty($env['DB_USERNAME'])) {
        R::setup($env['DB_CONNECT_DNS'], $env['DB_USERNAME'], '');
        R::debug(false);
        R::freeze(true);
        return new R();
    }
    throw new Exception('db name not define ..');
});
