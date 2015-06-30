<?php

error_reporting(E_ALL);

define('APP_PATH', realpath('..'));
define('START', microtime(true));

use MongoClient;
use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Config\Adapter\Php as Config;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Application as BaseApplication;
use Phalcon\Mvc\Collection\Manager as CollectionManager;

class Application extends BaseApplication
{
    /**
     * Register the services here to make them general
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

    /**
     * Project main
     */
    public function main() {

        $di = $this->registerServices();
        $modules = [];
        $config = new Config('../config/application.config.php');

        $config->merge(new Config('../config/local.php'));

        /**
         * Register mongo adapter
         */
        if($config->db->adapter === "mongo") {
            $di->set('mongo', function() use (&$config) {
                $mongo = new \MongoClient($config->db->host . ":" . $config->db->port);
                return $mongo->selectDB($config->db->dbname);
            }, true);

            $di->set('collectionManager', function() {
                $modelsManager = new CollectionManager();
                return $modelsManager;
            }, true);
        }
        
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
                $module . '\Models'      => '../modules/' . $module . '/models/',
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
        foreach($config->route as $url => $route)
            $router->add($url, $route->toArray());
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