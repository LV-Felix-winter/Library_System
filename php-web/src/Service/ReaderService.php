<?php

namespace App\Service;

use App\Http\ApiClient;

class ReaderService
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * 获取读者列表
     */
    public function list(int $page = 1, int $pageSize = 10): array
    {
        return $this->client->get('readers', [
            'page'      => $page,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * 获取读者详情
     */
    public function detail(int $id): array
    {
        return $this->client->get("readers/{$id}");
    }

    /**
     * 注册读者
     */
    public function create(array $data): array
    {
        return $this->client->post('readers', $data);
    }

    /**
     * 更新读者信息
     */
    public function update(int $id, array $data): array
    {
        return $this->client->put("readers/{$id}", $data);
    }

    /**
     * 更新读者状态
     */
    public function updateStatus(int $id, int $status): array
    {
        return $this->client->put("readers/{$id}/status", ['status' => $status]);
    }

    /**
     * 删除读者
     */
    public function delete(int $id): array
    {
        return $this->client->delete("readers/{$id}");
    }

     /**
     * 读者登录
     */
    public function login(string $cardNo, string $password): array
    {
        return $this->client->post('reader/login', [
            'card_no'  => $cardNo,
            'password' => $password,
        ]);
    }

    /**
     * 我的借阅记录
     */
    public function myRecords(): array
    {
        return $this->client->get('reader/records');
    }

    /**
     * 自助借书
     */
    public function borrowBook(int $bookId): array
    {
        return $this->client->post('reader/borrow', ['book_id' => $bookId]);
    }

    /**
     * 自助还书
     */
    public function returnBook(int $borrowId): array
    {
        return $this->client->post('reader/return', ['borrow_id' => $borrowId]);
    }

    /**
     * 修改自己的密码
     */
    public function changePassword(string $old, string $new): array
    {
        return $this->client->post('reader/change-password', [
            'old_password' => $old,
            'new_password' => $new,
        ]);
    }
}
