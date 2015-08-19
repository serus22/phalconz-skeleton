<?php

namespace PhalconZ\Rest\Controllers;

use Phalcon\Mvc\Model\Message;

class RestValidationException extends \Exception {

    private $__messages;

    public function __construct($msg, $code = 500, $prev = null) {
        $this->__messages = [];
        if(is_array($msg)) {
            foreach ($msg as $key => $m) {
                if($m instanceof Message) {
                    $this->__messages[$m->getField()] = [$m->getType() => $m->getMessage()];
                } else {
                    $this->__messages[$key] = $m;
                }
            }
        }
        else $this->__messages = $msg;
        parent::__construct("Validation error", $code, $prev);
    }

    public function getMessages() {
        return $this->__messages;
    }
}