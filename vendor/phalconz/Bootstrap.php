<?php

namespace PhalconZ;

use Phalcon\Di,
    \MongoClient,
    Phalcon\Loader,
    Phalcon\Mvc\View,
    Phalcon\Mvc\Router,
    Phalcon\Mvc\Application,
    Phalcon\DI\FactoryDefault,
    Phalcon\CLI\Console as ConsoleApp,
    Phalcon\Config\Adapter\Php as Config,
    Phalcon\DI\FactoryDefault\CLI as CliDI,
    Phalcon\Mvc\Collection\Manager as CollectionManager;


class Bootstrap {

    private $__config;

    private function __construct() {

    }

    /**
     * @return Bootstrap
     */
    public static function get() {
        static $inst = null;
        if ($inst === null) {
            $inst = new Bootstrap();
        }
        return $inst;
    }

    public function config($name = null) {
        if($this->__config === null) {
            $this->__config = new Config(APP_PATH . '/config/application.config.php');
            $this->__config->merge(new Config(APP_PATH . '/config/local.php'));
            $this->loader();
        }
        return $name ? @$this->__config[$name]  : $this->__config;
    }

    private function loader() {
        $loader = new Loader();
        $loader->registerNamespaces([
            'Zend\\Filter'      => APP_PATH . '/vendor/zendframework/zend-filter/src/',
            'Zend\\InputFilter' => APP_PATH . '/vendor/zendframework/zend-inputfilter/src/',
            'Zend\\Stdlib'      => APP_PATH . '/vendor/zendframework/zend-stdlib/src/',
            'Zend\\Validator'   => APP_PATH . '/vendor/zendframework/zend-validator/src/',
            'PhalconZ\\Rest'    => APP_PATH . '/vendor/phalconz/Rest/src/',
        ])->register();

        foreach($this->config('modules') as $module) {
            //Register loader with module namespaces
            $loader = new Loader();
            $loader->registerNamespaces([
                $module => APP_PATH . '/modules/' . $module . '/src/'
            ])->register();

            //Merge config
            $moduleConfig = new Config(APP_PATH . '/modules/' . $module . '/config/module.config.php');
            $this->__config->merge($moduleConfig);
        }
    }

    private function modules() {
        $modules = [];
        foreach($this->config('modules') as $module) {
            $modules[$module] = [
                'className' => $module . '\Module',
                'path' => APP_PATH . '/modules/' . $module . '/Module.php',
            ];
        }
        return $modules;
    }

    private function mvcRouter() {
        //Register routing
        $router = new Router();
        foreach($this->config('route') as $url => $route)
            $router->add($url, $route->toArray());
        return $router;
    }

    private function mvcDi() {
        $this->config();
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
            $view->registerEngines([
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
            ]);
            return $view;
        });

        $di->set('config', $this->config());
        $di->set('router', $this->mvcRouter());

        $this->database($di);

        return $di;
    }

    private function cliDi() {
        $this->config();
        $di = new CliDI();
        $this->database($di);
        return $di;
    }

    private function database(Di $di) {
        if($this->config()->db->adapter === "mongo") {
            $config = $this->config()
            ;
            $di->set('mongo', function() use (&$config) {
                $mongo = new MongoClient($config->db->host . ":" . $config->db->port);
                return $mongo->selectDB($config->db->dbname);
            }, true);

            $di->set('collectionManager', function() {
                $modelsManager = new CollectionManager();
                return $modelsManager;
            }, true);
        }
    }

    public function mvcApp() {
        $app = new Application();
        $app->setDI(Bootstrap::get()->mvcDi());
        $app->registerModules(Bootstrap::get()->modules());
        return $app;
    }

    public function cliApp() {
        $console = new ConsoleApp();
        $console->setDI(Bootstrap::get()->cliDi());
        $console->registerModules(Bootstrap::get()->modules());
        return $console;
    }
}