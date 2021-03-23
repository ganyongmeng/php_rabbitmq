<?php

namespace Rabbitmq\WebService;

use Rabbitmq\ProviderInterface;

/**
 * Class Publisher
 * @package Rabbitmq\WebService
 * @Author:Devin
 * @Date: 2021/3/23   17:22
 */
class Publisher implements ProviderInterface
{
    protected $conn;
    public function __construct($config, $exchange, $queue)
    {
        $this->conn = \RabbitmqConnection::getInstance($config)->listen($exchange, $queue);
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
     * @param $msg
     */
    public function send($msg)
    {
        $this->conn->publish($msg);
    }




}
