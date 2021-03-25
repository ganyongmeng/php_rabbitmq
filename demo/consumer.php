<?php
/**
 * Consumer
 * @author ganyongmeng
 * @date 2012-03-25
 */
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'addtask.php';
use ganyongmeng\php_rabbitmq\WebService\Consumer;
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
$consumer = new Consumer($config, 'exchange_1', 'queue_1');
$add = new Add();
$consumer->run([$add, 'run']);

