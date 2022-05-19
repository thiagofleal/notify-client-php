<?php

namespace NotifyClient\Notifications;

use Tonight\Tools\Client;

class EventEmitter
{
    private $url;
    private $client;
    private $query;

    public function __construct($url, $key) {
        $this->client = new Client(Client::JSON);
        $this->url = $url;
        $this->client->setHeader("Authorization", $key);
        $this->query = array();
    }

    public function where($field, $operator, $value) {
        $this->query[] = array($field, $operator, $value);
        return $this;
    }

    private function prepareQuery($connector) {
        if (count($this->query) > 1) {
            $ret = array();
            $ret[] = array_shift($this->query);
            $ret[] = $connector;
            $ret[] = $this->prepareQuery($connector);
            return $ret;
        }
        return array_shift($this->query);
    }

    public function emit($event, array $data = NULL) {
        $params = array('event' => $event);

        if ($this->query) {
            $params['where'] = $this->prepareQuery("AND");
        }
        if ($data) {
            $params['data'] = $data;
        }
        $this->client->post($this->url, $params);
    }
}