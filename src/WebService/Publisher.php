<?php

namespace ganyongmeng\php_rabbitmq\WebService;

use ganyongmeng\php_rabbitmq\ProviderInterface;
use ganyongmeng\php_rabbitmq\Connection\RabbitmqConnection;

/**
 * Class Publisher
 * @package Rabbitmq\WebService
 * @Author:ganyongmeng
 * @Date: 2021/3/23   17:22
 */
class Publisher implements ProviderInterface
{
    protected $conn;
    public function __construct($config, $exchange, $queue)
    {
        $this->conn = RabbitmqConnection::getInstance($config)->listen($exchange, $queue);
    }

    /**
     * @param $msg
     * @return mixed
     */
    public function process($msg = '')
    {
        return $this->send($msg);
    }

    /**
     * Send
     * @param $msg
     * @return mixed
     */
    public function send($msg)
    {
        return $this->conn->pushlish($msg);
    }




}
