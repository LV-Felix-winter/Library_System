<?php

namespace App\Service;

use App\Http\ApiClient;

class BookService
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * 获取图书列表
     */
    public function list(int $page = 1, int $pageSize = 12, string $keyword = '', int $categoryId = 0): array
    {
        $params = [
            'page'      => $page,
            'page_size' => $pageSize,
        ];
        if ($keyword) {
            $params['keyword'] = $keyword;
        }
        if ($categoryId > 0) {
            $params['category_id'] = $categoryId;
        }

        return $this->client->get('books', $params);
    }

    /**
     * 获取图书详情
     */
    public function detail(int $id): array
    {
        return $this->client->get("books/{$id}");
    }

    /**
     * 搜索图书
     */
    public function search(string $keyword): array
    {
        return $this->client->get('books/search', ['keyword' => $keyword]);
    }

    /**
     * 添加图书
     */
    public function create(array $data): array
    {
        return $this->client->post('books', $data);
    }

    /**
     * 更新图书
     */
    public function update(int $id, array $data): array
    {
        return $this->client->put("books/{$id}", $data);
    }

    /**
     * 下架图书
     */
    public function delete(int $id): array
    {
        return $this->client->delete("books/{$id}");
    }

     /**
     * 即将到期提醒
     */
    public function reminders(): array
    {
        return $this->client->get('borrow/reminders');
    }
}