<?php

namespace App\Service;

use App\Http\ApiClient;

class StatsService
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * 获取仪表盘统计数据
     */
    public function dashboard(): array
    {
        return $this->client->get('stats/dashboard');
    }
}