<?php

namespace App\Controllers;

use PhalconZ\Rest\Controllers\RestController as Controller;

class IndexController extends Controller {

    /**
     * Define full path to model class
     * @return mixed
     */
    public function collectionName() {
        return "App\\Models\\Connector";
    }
}