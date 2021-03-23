<?php

/**
 * Class RabbitmqConnection
 * @Author:Devin
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
                'vhost'=>'/'
            ]
        ];
    }


    public static function getInstance($config = []){
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($config);
            return self::$_instance;
        }
        return self::$_instance;
    }

    /**
     * Connection constructor.
     * @param $conn
     * @throws AMQPConnectionException
     */
    private function __construct($conn)
    {
        //创建连接和channel
        $conn = new AMQPConnection($conn);
        if(!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }
        self::$_conn = new AMQPChannel($conn);
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
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function setExchange($exchangeName,$queueName){
        //创建交换机
        $ex = new AMQPExchange(self::$_conn);
        self::$ex = $ex;
        $ex->setName($exchangeName);

        $ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
        $ex->setFlags(AMQP_DURABLE); //持久化
        $ex->declare();
        return self::setQueue($queueName,$exchangeName);
    }

    /**
     * Set queue
     * @param $queueName
     * @param $exchangeName
     * @return mixed
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     */
    private static function setQueue($queueName,$exchangeName){
        //  创建队列
        $q = new AMQPQueue(self::$_conn);
        $q->setName($queueName);
        $q->setFlags(AMQP_DURABLE);
        $q->declareQueue();

        // 用于绑定队列和交换机
        $routingKey = self::$route;
        $q->bind($exchangeName,  $routingKey);
        self::$q = $q;
        return(self::$_instance);
    }

    /*
 * 消费者
 * $fun_name = array($classobj,$function) or function name string
 * $autoack 是否自动应答
 *
 * function processMessage($envelope, $queue) {
        $msg = $envelope->getBody();
        echo $msg."\n"; //处理消息
        $queue->ack($envelope->getDeliveryTag());//手动应答
    }
 */

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
//                    self::$q->ack($envelope->getDeliveryTag());
                    //失败之后会默认进入 noack 队列。下次重新开启会再次调用，目前还不清楚 回调配置应该这里做一个失败反馈
                    //todu
                }
            }
            self::$q->consume($func);
        }
    }


    private static function closeConn(){
        self::$amp->disconnect();
    }

    /**
     * @param $msg
     */
    public function pushlish($msg){
        if (self::$ex->publish(date('H:i:s') . "user" . "register", self::$route)) {
            //write file
            echo $msg;
        }

    }

    /**
     * clone
     */
    public function __clone()
    {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }


}

