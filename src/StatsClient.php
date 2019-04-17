<?php

namespace SbergradStats;

/**
 * Class StatsClient
 * @package SbergradStats
 */
class StatsClient implements IStatsClient
{
    /** @var string  */
    private $host = 'http://localhost:5001/';

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
        $this->host = $config['host'];
        $this->type = $config['type'];
        $this->version = $config['version'];
    }

    public function sendStat($event, $requestId)
    {
        $day = (new \DateTime('now'))->format('Y-m-d');
        $body = [
            'day' => $day,
            'type' => implode('.', [$this->type , $event]),
            'id' => $requestId
        ];

        return $this->sendRequest($body);
    }

    private function sendRequest(array $body)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->host . '/api/v' . $this->version . '/statistic',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }
}