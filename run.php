<?php

$autoload = require_once __DIR__.'/vendor/autoload.php';

use Rswork\EntityFactory;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;

$app = new \Pimple();

$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'dbname'   => 'looptest',
    'host'     => '127.0.0.1',
    'user'     => 'root',
    'password' => null,
    'port'     => null,
);

$app['db.config'] = $app->share( function() use ( $app ) {
    return new Configuration();
} );

$app['db.event_manager'] = $app->share( function() use ( $app ) {
    return new EventManager();
} );

$app['db'] = $app->share( function() use ( $app ) {
    return DriverManager::getConnection( $app['db.options'], $app['db.config'], $app['db.event_manager'] );
} );

$app['entity'] = $app->share( function() use ( $app ) {
    return new EntityFactory( $app );
} );

$nav = $app['entity']->getEntity( 'Nav' );
