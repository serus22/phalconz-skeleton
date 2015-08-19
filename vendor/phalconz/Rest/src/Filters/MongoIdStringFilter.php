<?php

namespace PhalconZ\Rest\Filters;

use \MongoId;
use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;

class MongoIdStringFilter implements FilterInterface {

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return string
     */
    public function filter($value) {
        $n = '$id';
        if(is_array($value) && isSet($value[$n]) && MongoId::isValid($value[$n]))
            return strtolower(trim($value[$n]));
        else if(is_object($value) && $value instanceof MongoId)
            return $value->id;
        else if(is_string($value) && MongoId::isValid($value))
            return strtolower(trim($value));
        return null;
    }
}