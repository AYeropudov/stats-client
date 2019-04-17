<?php

namespace SbergradStats;


use RedisException;

class StatsRedisClient implements IStatsClient
{

    /** @var string */
    private $type;

    /** @var string */
    private $version = '1';

    private $host = 'redis';
    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->type = $config['type'];
        $this->version = $config['version'];
        $this->redis = $config['connection'];
        $this->host = $config['host'];
    }


    public function sendStat($event, $requestId)
    {
        $day = (new \DateTime('now'))->setTimezone(new \DateTimeZone('Europe/Moscow'))->format('Y-m-d');

        $body = [
            'day' => $day,
            'type' => implode('.', [$this->type , $event]),
            'id' => $requestId
        ];
        $que_name = 'statistic-tasks';
        try {
            $redisClient = new \Redis();
            $redisClient->connect($this->host);
            $redisClient->lPush(
                $que_name,
                json_encode(['event_name' => 'statistic_rq', 'payload' => $body])
            );
        } catch (RedisException $e) {
            // we get that due to default_socket_timeout
                throw $e;
        }
    }


}