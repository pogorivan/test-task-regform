<?php
$appRoot =  __DIR__.'/../';

require($appRoot.'vendor/autoload.php');

$config = require($appRoot.'config/main.php');

App\App::init($config);
App\App::$container->get('kernel')->run();