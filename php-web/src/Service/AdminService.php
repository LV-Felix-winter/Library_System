<?php

namespace App\Service;

use App\Http\ApiClient;

class AdminService
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * 管理员列表
     */
    public function all(): array
    {
        return $this->client->get('admins');
    }

    /**
     * 修改权限
     */
    public function setRole(int $id, int $role): array
    {
        return $this->client->put("admins/{$id}/role", ['role' => $role]);
    }
}