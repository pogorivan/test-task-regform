<?php

namespace App\Components;

use App\App;

class Controller
{
    public $layout = 'layout.twig';

    public function render ($viewName, array $params = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../Templates');
        $twig = new \Twig\Environment($loader);

        $twig->addGlobal('isAuthenticated', Auth::isAuthenticated());
        $twig->addGlobal('translator', App::$container->get('translator'));

        $translateFilter = new \Twig\TwigFilter('translate', function ($string) {
            return \App\App::$container->get('translator')->translate($string);
        });
        $twig->addFilter($translateFilter);

        echo $twig->render($viewName.'.twig', $params);
    }
}