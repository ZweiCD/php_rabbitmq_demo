<?php
/**
 * 路由模式
 * 发送者1
 */
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$ex_name = 'ex_routing';
$r_key = 'r_key_1';

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin', 'testhost');
$channel = $connection->channel();

$channel->exchange_declare($ex_name, 'direct', false, true, false); //设置交换机类型并持久化

for ($i = 6; $i > 0; $i--) {
    $data = 'sleep ' . $i;
    $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]); //消息持久化
    $channel->basic_publish($msg, $ex_name, $r_key); //将消息推送到交换机
    echo " [x] Sent '" . $data . "' to key:" . $r_key . "\n";
}

$channel->close();
$connection->close();