<?php

namespace Rest\Models;

use Phalcon\Mvc\Collection;

class User extends Collection {

    public function getSource() {
        return "user";
    }

    public function __get($name) {
        if($name === "id") {
            $i = '$id';
            return $this->_id->$i;
        }
        return isSet($this->$name) ? $this->$name : null;
    }
}