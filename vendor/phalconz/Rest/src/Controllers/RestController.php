<?php

namespace PhalconZ\Rest\Controllers;

abstract class RestController extends AbstractRestController {

    /**
     * Define full path to model class
     * @return mixed
     */
    public abstract function collectionName();

    /**
     * @return \Phalcon\Mvc\CollectionInterface
     * @throws RestUnknownCollectionException
     */
    public function collection() {
        if(class_exists($this->collectionName())) {
            $a = $this->collectionName();
            return new $a();
        }
        throw new RestUnknownCollectionException;
    }

    /**
     * Return list of items or specific object
     * @param mixed|null $id
     * @return array|object
     * @throws \PhalconZ\Rest\Controllers\RestNotFoundException
     */
    public function get($id = null) {
        if($id) {
            $object = @forward_static_call($this->collectionName() . '::findById', new \MongoId($id));
            if($object) return $object;
            throw new RestNotFoundException;
        }
        $list = forward_static_call($this->collectionName() .'::Find');
        return $list;
    }

    /**
     * @param object $data
     * @return \PhalconZ\Rest\Models\SmartCollection
     * @throws \Exception
     */
    public function post($data) {
        if(empty($data)) throw new \Exception('No data recieved');
        $obj = $this->collection();
        foreach ($data as $key => $value) {
            if($key === '_id') continue;
            $obj->$key = $value;
        }
        $obj->save();
        return $obj;
    }

    /**
     * @param mixed $id
     * @param object $data
     * @return \PhalconZ\Rest\Models\SmartCollection
     * @throws \PhalconZ\Rest\Controllers\RestNotFoundException
     */
    public function put($id, $data) {
        $obj = @forward_static_call($this->collectionName() . '::findById', new \MongoId($id));
        if(! $obj) throw new RestNotFoundException();
        foreach($data as $key => $value) {
            if($key === '_id') continue;
            $obj->$key = $value;
        }
        $obj->save();
        return $obj;
    }

    /**
     * @param mixed $id
     * @throws \PhalconZ\Rest\Controllers\RestDocumentNotFoundException
     * @return null
     */
    public function delete($id) {
        $object = @forward_static_call($this->collectionName() . '::findById', new \MongoId($id));
        if($object) {
            $object->delete();
            return null;
        }
        throw new RestDocumentNotFoundException();
    }
}