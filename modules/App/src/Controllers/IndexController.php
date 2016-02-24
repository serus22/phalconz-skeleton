<?php

namespace App\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller {

  public function indexAction() {
    $this->view->greeting = "Hello World!";
  }

  public function fooAction() {
    die('Bar!');
  }
}