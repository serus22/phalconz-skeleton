<?php

error_reporting(E_ALL);

define('APP_PATH', realpath('..'));
define('START', microtime(true));

use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Application as BaseApplication;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Config\Adapter\Php as Config;

class Application extends BaseApplication
{
    /**
     * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
     */
    protected function registerServices() {

        $di = new FactoryDefault();

        //TODO:url

        //Register a session container
        $di->setShared('session', function () {
            $session = new SessionAdapter();
            $session->start();
            return $session;
        });

        //Register rendering mechanism
        $di->setShared('view', function () {

            $view = new View();
            $view->registerEngines(array(
                '.volt' => function ($view, $di) {

                    $volt = new VoltEngine($view, $di);
                    $volt->setOptions(array(
                        'compiledPath' => APP_PATH . '/data/cache/',
                        'compiledSeparator' => '_'
                    ));
                    return $volt;
                },
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
            ));

            return $view;
        });

        //Save DI provider
        $this->setDI($di);

        return $di;
    }

    public function main() {

        $di = $this->registerServices();
        $modules = [];
        $config = new Config('../config/application.config.php');
        
        foreach ($config['modules'] as $module) {
            //Create module list for register
            $modules[$module] =  [
                'className' => $module . '\Module',
                'path' => APP_PATH . '/modules/' . $module . '/Module.php',
            ];

            //Register loader with module namespaces
            $loader = new Loader();
            $loader->registerNamespaces(array(
                $module . '\Controllers' => '../modules/' . $module . '/controllers/',
                //'Backend\Models'      => '../modules/Backend/models/',
                //'Backend\Plugins'     => '../modules/Backend/plugins/',
            ))->register();
            //Merge config 
            $moduleConfig = new Config('../modules/' . $module . '/config/module.config.php');
            $config->merge($moduleConfig);
        }

        //Save global merged config (later registered module owerrides configuration)
        $di->set('config', $config);

        //Register modules from application.config.php
        $this->registerModules($modules);
        //Register routing

        $router = new Router();
        foreach($config->route as $url => $route) {
            $router->add($url, $route->toArray());
        }
        $def = array_keys($modules);
        $router->setDefaultModule($def[0]);
        $di->set('router', $router);
        //Dispatch route
        echo $this->handle()->getContent();
    }

}

$application = new Application();

try {
    $application->main();
    //echo "runtime:" . (microtime(true) - START)*1000;
} catch(\Exception $e) {
    echo $e->getMessage();
}