<?php

namespace App\Controllers;

use App\Models\Todo;
use Phalcon\Mvc\Controller;

class AnotherController extends Controller {

  public function indexAction() {
    $this->view->greeting = "Hello World!";
  }

  public function fooAction() {
    $list = Todo::find();
    $this->view->todoList = $list;
  }
}