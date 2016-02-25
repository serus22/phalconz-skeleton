<?php

namespace App\View;

use PhalconZ\Lib\AbstractViewHelper;

class MenuHelper extends AbstractViewHelper {

  public function __invoke($args = null) {
    $view = $this->di()->get('view');
    return $view->partial('layout/partial/menu');
  }
}