<?php
/**
 * Work模式
 */
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin', 'testhost');
$channel = $connection->channel();

$channel->queue_declare('q_work', false, true, false, false); //队列持久化

for ($i = 6; $i > 0; $i--) {
    $data = 'sleep ' . $i;
    $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]); //消息持久化
    $channel->basic_publish($msg, '', 'q_work');
    echo " [x] Sent '" . $data . "'\n";
}

$channel->close();
$connection->close();