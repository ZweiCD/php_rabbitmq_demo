<?php
/**
 * 主题模式
 */
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$ex_name = 'ex_topic';
$r_keys = [
    'quick.orange.rabbit',
    'lazy.orange.elephant',
    'quick.orange.fox',
    'lazy.brown.fox',
    'lazy.pink.rabbit',
    'quick.brown.fox',
    'quick.orange.male.rabbit',
    'lazy.orange.male.rabbit',
];

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin', 'testhost');
$channel = $connection->channel();

$channel->exchange_declare($ex_name, 'topic', false, true, false); //设置交换机类型并持久化

foreach ($r_keys as $k => $v) {
    $msg = new AMQPMessage($v, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]); //消息持久化
    $channel->basic_publish($msg, $ex_name, $v); //将消息推送到交换机
    echo " [x] Sent '" . $v . "'\n";
}

$channel->close();
$connection->close();