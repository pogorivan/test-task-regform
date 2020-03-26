<?php
namespace App;

class Kernel
{
    public $defaultController = 'Site';

    public $defaultAction = 'Index';

    public function run()
    {
        list($controllerName, $actionName, $params) = App::$container->get('router')->resolve();

        $controllerName = empty($controllerName) ? $this->defaultController : ucfirst($controllerName);
        if(!class_exists("App\\Controllers\\".ucfirst($controllerName))){
            throw new \Exception('Not found',404);
        }
        $controllerName = "App\\Controllers\\".ucfirst($controllerName);
        $controller = new $controllerName;
        $actionName = empty($actionName) ? $this->defaultAction : $actionName;
        if (!method_exists($controller, $actionName)){
            throw new \Exception('Not found',404);
        }
        return $controller->$actionName($params);
    }
}