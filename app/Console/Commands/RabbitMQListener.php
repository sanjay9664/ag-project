<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQListener extends Command
{
    protected $signature = 'rabbitmq:listen';
    protected $description = 'Listen to RabbitMQ messages for specific device';

    public function handle()
    {
        $host = 'app.sochiot.com';
        $port = 5672;
        $user = 'isab-qa-admin';
        $password = 'I0t3ch';
        $vhost = 'isab-qa'; // default, change if you have a custom vhost
        $exchange = 'amq.topic';
        $queue = 'altrix-test';
        $routingKey = 'altrix.test.*'; // this is a pattern

        try {
            $connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
            $channel = $connection->channel();

            // Declare topic exchange
            $channel->exchange_declare($exchange, 'topic', false, true, false);

            // Declare a durable queue
            $channel->queue_declare($queue, false, true, false, false);

            // Bind queue to exchange with routing key pattern
            $channel->queue_bind($queue, $exchange, $routingKey);

            echo " [*] Waiting for messages. To exit press CTRL+C\n";

            // Callback function when message received
            $callback = function ($msg) {
                echo " [x] Received message with routing key {$msg->delivery_info['routing_key']}: {$msg->body}\n";

                // You can parse/process the message here
                // For example, log to Laravel log:
                \Log::info('RabbitMQ Message Received', [
                    'routing_key' => $msg->delivery_info['routing_key'],
                    'body' => $msg->body,
                ]);

                // Acknowledge the message
                $msg->ack();
            };

            // Start consuming
            $channel->basic_consume($queue, '', false, false, false, false, $callback);

            // Keep script running
            while ($channel->is_consuming()) {
                $channel->wait();
            }

        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
            \Log::error('RabbitMQ listener error: ' . $e->getMessage());
        }
    }
}
