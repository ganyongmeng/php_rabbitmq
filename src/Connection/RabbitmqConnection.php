<?php
namespace ganyongmeng\php_rabbitmq\Connection;
use ganyongmeng\php_rabbitmq\Connection\ConnectionInterface;
/**
 * Class RabbitmqConnection
 * @Author:ganyongmeng
 * @Date: 2021/3/23   17:30
 */
class RabbitmqConnection implements ConnectionInterface
{

    static private $_instance;
    static private $_conn;
    static private $amp ;
    static private $route = 'mm';
    static private $q ;
    static private $ex ;
    static private $queue;
    static private $config;
    static private $exchange_type = AMQP_EX_TYPE_DIRECT;
    static private $durable = AMQP_DURABLE;


    public function config()
    {
        return $arr = [
            'RabbitMq' => [
                // Rabbitmq 服务地址
                'host' => '127.0.0.1',
                // Rabbitmq 服务端口
                'port' => '5672',
                // Rabbitmq 帐号
                'login' => 'guest',
                // Rabbitmq 密码
                'password' => 'guest',
                'vhost'=>'/',
                //路由
                'route'=>'',
                'exchange_type'=>AMQP_EX_TYPE_DIRECT,
                'durable'=>AMQP_DURABLE,
            ]
        ];
    }

    /**
     * @param array $config
     * @return RabbitmqConnection
     * @throws \AMQPConnectionException
     */
    public static function getInstance($config = []){
        if (!empty($config['route'])) {
            self::$route = $config['route'];
        }
        if (!empty($config['exchange_type'])) {
            self::$exchange_type = $config['exchange_type'];
        }

        if (!empty($config['durable'])) {
            self::$durable = $config['durable'];
        }
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($config);
            return self::$_instance;
        }
        return self::$_instance;
    }

    /**
     * Connection constructor.
     * RabbitmqConnection constructor.
     * @param $conn
     * @throws \AMQPConnectionException
     */
    private function __construct($conn)
    {
        //创建连接和channel
        $conn = new \AMQPConnection($conn);
        if(!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }
        self::$_conn = new \AMQPChannel($conn);
        self::$amp = $conn;
    }

    /**
     * Listen
     * @param $exchangeName
     * @param $queuename
     * @return mixed
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function listen($exchangeName,$queuename){
        self::$queue = $queuename;
        return $this->setExchange($exchangeName,$queuename);
    }

    /**
     * Set exchange
     * @param $exchangeName
     * @param $queueName
     * @return mixed
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     */
    public function setExchange($exchangeName,$queueName){
        //创建交换机
        $ex = new \AMQPExchange(self::$_conn);
        self::$ex = $ex;
        $ex->setName($exchangeName);

        $ex->setType(self::$exchange_type); //direct类型
        $ex->setFlags(self::$durable); //持久化
        $ex->declareExchange();
        return self::setQueue($queueName,$exchangeName);
    }

    /**
     * Set queue
     * @param $queueName
     * @param $exchangeName
     * @return mixed
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    private static function setQueue($queueName,$exchangeName){
        //  创建队列
        $q = new \AMQPQueue(self::$_conn);
        $q->setName($queueName);
        $q->setFlags(self::$durable);
        $q->declareQueue();

        // 用于绑定队列和交换机
        $routingKey = self::$route;
        $q->bind($exchangeName,  $routingKey);
        self::$q = $q;
        return(self::$_instance);
    }


    /**
     * @param $func
     * @param bool $autoack
     * @return bool
     */
    public function run($func, $autoack = True){
        if (!$func || !self::$q) return False;
        while(True){
            if ($autoack) {
                if(!self::$q->consume($func, AMQP_AUTOACK)){
                    self::$q->ack($func->getDeliveryTag());
                }
            } else {
                self::$q->consume($func);
            }
        }
    }


    private static function closeConn(){
        self::$amp->disconnect();
    }

    /**
     * @param $msg
     * @return bool
     */
    public function pushlish($msg){
        if (self::$ex->publish($msg, self::$route)) {
            //write file
            //echo $msg;
            self::closeConn();
            return true;
        }
        return false;
    }

    /**
     * clone
     */
    public function __clone()
    {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }


}

