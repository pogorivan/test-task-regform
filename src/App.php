<?php
namespace App;

use App\Components\Router;
use App\Components\Translate;
use DI\Container;

class App
{
    /**
     * @var Container
     */
    public static $container;

    /**
     * @param array $config
     */
    public static function init($config)
    {
        session_start();

        self::$container = new Container();

        self::$container->set('kernel', new Kernel());
        self::$container->set('db', new \PDO($config['db_dsn'], $config['db_user'], $config['db_password']));
        self::$container->set('router', new Router());
        self::$container->set('translator', new Translate());
    }
}