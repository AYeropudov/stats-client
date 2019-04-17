<?php


namespace SbergradStats;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class StatsQueueClient implements IStatsClient
{
    /** @var AMQPStreamConnection */
    private $connection;

    /** @var string */
    private $type;

    /** @var string */
    private $version = '1';

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->type = $config['type'];
        $this->version = $config['version'];
        $this->connection = $config['connection'];
    }


    public function sendStat($event, $requestId)
    {
        $day = (new \DateTime('now'))->setTimezone(new \DateTimeZone('Europe/Moscow'))->format('Y-m-d');

        $body = [
            'day' => $day,
            'type' => implode('.', [$this->type , $event]),
            'id' => $requestId
        ];
        $channel = $this->connection->channel();
        $channel->queue_declare('stats_queue', false, true, false, false);

        $msg = new AMQPMessage(json_encode($body));
        $channel->basic_publish($msg, '', 'stats_queue');
        $channel->close();
        $this->connection->close();
    }
}