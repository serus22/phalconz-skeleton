<?php

namespace App;

use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\DiInterface;

class Module {

    public function registerAutoloaders() {

    }

    /**
     * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
     */
    public function registerServices(DiInterface $di)
    {
        //Registering a dispatcher
        $di->set('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace( __NAMESPACE__ . '\\Controllers\\');
            return $dispatcher;
        });

        //Registering the view component
        $di->set('view', function() {
            $view = new View();
            $view->setViewsDir(APP_PATH . '/modules/' . __NAMESPACE__ . '/view');
            $view->setLayoutsDir('layout/');
            $view->setTemplateAfter('main');
            return $view;
        });
    }
}
