<?php


namespace SbergradStats;


interface IStatsClient
{
    public function sendStat($event, $requestId);
}