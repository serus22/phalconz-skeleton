<?php

namespace Rest\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;

abstract class AbstractRestController extends Controller {

    private $__collection;

    //Return collection name with full namespace
    public function collection() {
        return $this->__collection;
    }

    //Action for resolve request
    public function indexAction() {
        $this->__collection = "\Rest\Models\\" . ucfirst(strtolower(trim($this->dispatcher->getParam("collection"))));

        if(! class_exists($this->collection())) return $this->printJson(['message' => 'Collection does not exist'], 500);

        $id = strtolower(trim($this->dispatcher->getParam("id")));

        $request = new Request();

        $data = [];

        if($request->isGet())
            $out = $this->get($id);
        else if($request->isPost())
            $out = $this->post($id, $data);
        else if($request->isPut())
            $out = $this->put($data);
        else if($request->isDelete())
            $out = $this->delete($id);
        else
            $out = [306, ["message" => $this->getMessage(306)]];

        return $this->printJson($out[1], $out[0]);
    }

    //Abstract methods = REST methods for CRUD actions

    /**
     * Get list or specific record
     * @param string
     * @return array(), mixed
     */
    abstract public function get($id = null);

    /**
     * Edit record
     * @param string
     * @param array
     * @return array
     */
    abstract public function post($id, $data);

    /**
     * Create new record
     * @param array
     * @return array
     */
    abstract public function put($data);

    /**
     * Remove record 
     * @param string
     * @return array
     */
    abstract public function delete($id);

    //pritn output with headers
    protected function printJson($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        header('HTTP/1.0 ' . $code .' ' . $this->getMessage($code));
        echo json_encode($data, JSON_PRETTY_PRINT);
        $this->view->disable();
    }

    //Return message by code (90% never used :D)
    protected function getMessage($code) {
        $data = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',

            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            420 => 'Enhance Your Calm',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Reserved for WebDAV',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            444 => 'No Response',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            499 => 'Client Closed Request',
            
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
            598 => 'Network read timeout error',
            599 => 'Network connect timeout error',
        ];

        return isset($data[$code]) ? $data[$code] : $data[306];
    }
}