<?php
/**
 * Work模式
 * 能者多劳
 * 手动确认
 */
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin', 'testhost');
$channel = $connection->channel();

$channel->queue_declare('q_work', false, true, false, false); //队列持久化

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr($msg->body, 6));
    echo " [x] Done", "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']); //手动发送ACK应答
};

$channel->basic_qos(null, 1, null); //一次只接受一个消息
$channel->basic_consume('q_work', '', false, false, false, false, $callback); //设置no_ack为false

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();