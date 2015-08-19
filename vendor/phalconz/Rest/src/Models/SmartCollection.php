<?php

namespace PhalconZ\Rest\Models;

use Rest\Controllers\RestValidationException;
use Zend\Filter\Filter;
use Phalcon\Mvc\Collection;

abstract class SmartCollection extends Collection {

    /**
     * @var array
     */
    private $__blackList;


    /**
     * @var Filter
     */
    private $__filter;

    /**
     * @return array
     */
    public function getReservedAttributes() {
        $this->__blackList['__blackList'] = '__blacklList';
        $this->__blackList['__filter'] = '__filter';
        return array_values($this->__blackList);
    }

    /**
     * @param $name
     * @return SmartCollection
     */
    public function ignore($name) {
        $this->__blackList[$name] = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource() {
        $a = explode('\\', get_called_class());
        return strtolower(end($a));
    }

    /**
     * @return Filter|null
     */
    public function filter() {
        if($this->__filter instanceof Filter) return $this->__filter;
        $class = str_replace('Filters', '', __NAMESPACE__) . __CLASS__;
        if(class_exists($class))
            $this->__filter = $class($this);
        return $this->__filter;
    }

    /**
     * @return bool
     * @throws RestValidationException
     */
    public function validation() {
        if(empty($this->filter()) || $this->filter()->setData($this->toArray())->isValid()) return true;
        throw new RestValidationException($this->filter());
    }

}