<?php

namespace Rest\Controllers;

class RestController extends AbstractRestController {

    public function get($id = null) {
        try {
            if($id) {
                $user = @forward_static_call($this->collection() . '::findById', new \MongoId($id));
                return $user ? [200, $user] : [200, ['message' => 'not found']];
            }
            $list = forward_static_call($this->collection() .'::Find');
            return [200, $list];
        } catch(\Exception $e) {
            return [500, ['message' => $e->getMessage()]];
        }
    }

    public function post($id, $data) {
        try {
            $user = forward_static_call($this->collection() . '::findById', new \MongoId($id));

        } catch(\Exception $e) {
            return [500, ['message' => $e->getMessage()]];
        }
    }

    public function put($data) {

    }

    public function delete($id) {

    }
}