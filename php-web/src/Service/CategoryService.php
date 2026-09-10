<?php

namespace App\Service;

use App\Http\ApiClient;

class CategoryService
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * 获取所有分类
     */
    public function all(): array
    {
        return $this->client->get('categories');
    }

    /**
     * 添加分类
     */
    public function create(array $data): array
    {
        return $this->client->post('categories', $data);
    }

    /**
     * 更新分类
     */
    public function update(int $id, array $data): array
    {
        return $this->client->put("categories/{$id}", $data);
    }

    /**
     * 删除分类
     */
    public function delete(int $id): array
    {
        return $this->client->delete("categories/{$id}");
    }
}