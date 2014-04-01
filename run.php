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
    'charset'  => 'utf8',
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

$navs = $app['entity']->getEntity( 'Nav' );

foreach( $navs as $nav ) {
    echo $nav->name . ": \n\n";

    foreach( $nav->children as $child ) {
        if( $child->children ) {
            echo '  '.$child->name . ":\n";
            foreach( $child->children as $subchild ) {
                echo '    '.$subchild->name . "\n";
            }
        } else {
            echo '  '.$child->name . "\n";
        }
    }
}

echo "\n\nPorducts\n\n";

$products = $app['entity']->getEntity( 'Product' );

foreach( $products as $product ) {
    echo $product->name."\n";
}
