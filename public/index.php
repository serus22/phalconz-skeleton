<?php

error_reporting(E_ALL);

define('APP_PATH', realpath(dirname(__FILE__) . '/..'));
define('START', microtime(true));

require_once(APP_PATH . '/vendor/serus22/phalconz/src/Bootstrap.php');

use PhalconZ\Bootstrap;

try {
    //Cli application
    if(php_sapi_name() === 'cli') {
        $console = Bootstrap::get()->cliApp();

        $args = [
            'task'   => 'Galileo\Tasks\\' . (ucfirst(strtolower(@$argv[1])) ?: 'Default'),
            'action' => ucfirst(strtolower(@$argv[2])) ?: 'default',
        ];
        unset($argv[0], $argv[1], $argv[2]);
        $args['params'] = count($argv) > 0 ? array_values($argv) : array();

        $console->handle($args);
        print_r("\n");
    }
    //MVC application
    else {
        $app = Bootstrap::get()->mvcApp();
        //Dispatch route
        echo $app->handle()->getContent();
    }
} catch(\Exception $e) {
    print_r($e->getMessage() . "\n");
    print_r($e->getFile() . ' on line ' . $e->getLine() . "\n");
}