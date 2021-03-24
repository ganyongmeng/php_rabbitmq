<?php

namespace ganyongmeng\php_rabbitmq\WebService;
use ganyongmeng\php_rabbitmq\ProviderInterface;
use ganyongmeng\php_rabbitmq\Connection\RabbitmqConnection;
/**
 * Class consumer
 * @package Rabbitmq\WebService
 * @Author:Devin
 * @Date: 2021/3/23   17:22
 */
class Consumer implements ProviderInterface
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
    }

    public function run($func, $autoack = True) {
        $this->conn->run($func, $autoack);
    }

    /**
     * @param $envelope
     */
    public function receive($envelope)
    {
        //休眠两秒，
        sleep(2);
        //echo消息内容
        echo $envelope->getBody()."\n";
        //显式确认，队列收到消费者显式确认后，会删除该消息
        $this->conn->ack($envelope->getDeliveryTag());
    }





}
