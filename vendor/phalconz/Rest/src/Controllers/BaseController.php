<?php

namespace PhalconZ\Rest\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Http\Response;

abstract class BaseController extends Controller {

    /**
     * @var Request
     */
    private $__request;

    /**
     * @var Response
     */
    private $__response;

    /**
     * @return Request
     */
    public function request() {
        if($this->__request === null)
            $this->__request = new Request();
        return $this->__request;
    }

    /**
     * @return Response
     */
    public function response() {
        if($this->__response === null) {
            $this->__response = new Response();
            $this->__response->setStatusCode(200);
        }
        return $this->__response;
    }

    public function jsonOutput($data, $code = null) {
        $response = $this->response();
        $response->setContentType('application/json', 'UTF-8');
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, PUT, DELETE, POST, OPTIONS');
        if($code) $response->setStatusCode($code, $this->httpMessage($code));
        if(empty($data)) $data = $this->httpMessage($code);
        $response->setContent(json_encode($data, JSON_PRETTY_PRINT));
        $this->view->disable();
        return $response;
    }

    protected function httpMessage($code) {
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

        return isset($data[$code]) ? $data[$code] : null;
    }

    /**
     * @return \Phalcon\Logger\AdapterInterface|null
     */
    private function logger() {
        $this->getDI()->get('logger');
    }

    /**
     * @param mixed $msg
     * @return BaseController
     */
    protected function log(string $msg) {
        if($this->logger())
            $this->logger()->log($msg);
        return $this;
    }

    /**
     * @param mixed $msg
     * @return Controller
     */
    protected function error(string $msg) {
        if($this->logger())
            $this->logger()->error($msg);
        return $this;
    }

    /**
     * @param mixed $msg
     * @return BaseController
     */
    protected function info(string $msg) {
        if($this->logger())
            $this->logger()->info($msg);
        return $this;
    }
}