<?php
/**
 * Publisher
 * @author ganyongmeng
 * @date 2012-03-25
 */
require_once __DIR__ . '/../vendor/autoload.php';
use ganyongmeng\php_rabbitmq\WebService\Publisher;
$config = [
    // Rabbitmq 服务地址
    'host' => '127.0.0.1',
    // Rabbitmq 服务端口
    'port' => '5672',
    // Rabbitmq 帐号
    'login' => 'guest',
    // Rabbitmq 密码
    'password' => 'guest',
    'vhost'=>'/'
];
$publisher = new Publisher($config, 'exchange_1', 'queue_1');
$publisher->process(json_encode(['data'=>'message1', 'hello'=>'world']));