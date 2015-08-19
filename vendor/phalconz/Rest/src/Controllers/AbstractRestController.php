<?php

namespace PhalconZ\Rest\Controllers;

use MongoException;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Collection;
use PhalconZ\Rest\Controllers\RestValidationException as InvalidData;
use PhalconZ\Rest\Controllers\RestDocumentNotFoundException as DocumentNotFound;
use PhalconZ\Rest\Controllers\RestUnknownCollectionException as InvalidCollection;

abstract class AbstractRestController extends BaseController {

    private $__request;

    public function getRequestParam($name) {
        return trim($this->dispatcher->getParam($name));
    }

    /**
     * @return Request
     */
    public function request() {
        if($this->__request === null)
            $this->__request = new Request();
        return $this->__request;
    }

    public function getData() {
        $data = json_decode($this->request()->getRawBody()) ?: new \StdClass();
        if(isSet($data->_id)) unset($data->_id);
        if(is_array($data)) unset($data['_id']);
        return $data;
    }

    public function getId() {
        return $this->getRequestParam("id");
    }

    public function indexAction() {
        $this->response()->setStatusCode(200);
        try {
            $id = $this->getId();
            $data = $this->getData();
            $out = null;
            $request = $this->request();
            if ($request->isGet())
                $out = $this->get($id, $data);
            else if ($request->isPost() || $request->isPatch())
                $out = $this->post($data);
            else if ($request->isPut())
                $out = $this->put($id, $data);
            else if ($request->isDelete())
                $out = $this->delete($id);
            else if ($request->isOptions())
                $this->response()->setStatusCode(200);
            else
                $this->response()->setStatusCode(200);
            return $this->jsonOutput($out);
        } catch (InvalidData $e) {
            return $this->jsonOutput([
                'message' => 'Validation error',
                'messages' => $e->getMessages()
            ], 500);
        } catch (DocumentNotFound $e) {
            return $this->jsonOutput([
                'message' => 'Document not found',
            ], 500);
        } catch(InvalidCollection $e) {
            return $this->jsonOutput([
                'message' => 'Unknown collection',
            ], 500);
        } catch(MongoException $e) {
            return $this->jsonOutput(500, [
                'message' => 'MongoException #' . $e.$this->getCode() . ', ' . $e->getMessage()
            ]);
        } catch(\Exception $e) {
            return $this->jsonOutput([
                'message' => 'Error #' . $e->getCode() . ', ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            ], 500);
        }
    }

    //Abstract methods = REST methods for CRUD actions

    /**
     * Get list or specific record
     * @param string
     * @param array()
     * @return array(), mixed
     */
    abstract public function get($id = null);

    /**
     * Create record
     * @param array
     * @return array
     */
    abstract public function post($data);

    /**
     * Edit new record
     * @param string
     * @param array
     * @return array
     */
    abstract public function put($id, $data);

    /**
     * Remove record 
     * @param string
     * @return array
     */
    abstract public function delete($id);
}