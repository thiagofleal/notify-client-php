<?php

namespace NotifyClient\Notifications;

use json_encode;

class Client
{
    private $headers;
    
    public function __construct() {
        $this->headers = array();
        $this->setHeader("Content-type", "application/json");
    }

    public function setHeader($header, $value) {
        $this->headers[$header] = $value;
    }

    private function makeBody($body) {
        return json_encode($body);
    }

    public static function sendRequest($url, $method, $headers, $body) {
        $options = array(
            'http' => array(
                'header' => implode("", array_map( function($key, $value) {
                    return "{$key}: {$value}\r\n";
                }, array_keys($headers), $headers)),
                'method'  => $method,
                'content' => $body
            )
        );
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    public function request($url, $method, $body, array $headers = array()) {
        return self::sendRequest($url, $method, array_merge($this->headers, $headers), $body);
    }

    public function post($url, $body, array $headers = array()) {
        return $this->request($url, "POST", $this->makeBody($body), $headers);
    }
}