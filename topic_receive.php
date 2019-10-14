<?php
/**
 * 主题模式
 * 接收者
 */
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$ex_name = 'ex_topic';
$q_name = 'q_topic';

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin', 'testhost');
$channel = $connection->channel();

$channel->exchange_declare($ex_name, 'topic', false, true, false); //设置交换机类型并持久化
$channel->queue_declare($q_name, false, true, false, false); //队列持久化
$channel->queue_bind($q_name, $ex_name, '*.orange.*'); //将队列绑定交换机和路由键r_key

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received routing_key:', $msg->delivery_info['routing_key'], ' ', $msg->body, " from queue:q_topic\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']); //手动发送ACK应答
};

$channel->basic_qos(null, 1, null); //一次只接受一个消息
$channel->basic_consume($q_name, '', false, false, false, false, $callback); //设置no_ack为false

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();