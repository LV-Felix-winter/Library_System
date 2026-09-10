<?php

namespace App\Service;

use App\Http\ApiClient;

class BorrowService
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * 借书
     */
    public function borrow(int $readerId, int $bookId): array
    {
        return $this->client->post('borrow/borrow', [
            'reader_id' => $readerId,
            'book_id'   => $bookId,
        ]);
    }

    /**
     * 还书
     */
    public function returnBook(int $borrowId): array
    {
        return $this->client->post('borrow/return', [
            'borrow_id' => $borrowId,
        ]);
    }

    /**
     * 续借
     */
    public function renew(int $borrowId): array
    {
        return $this->client->post('borrow/renew', [
            'borrow_id' => $borrowId,
        ]);
    }

    /**
     * 查询借阅记录
     */
    public function records(int $readerId): array
    {
        return $this->client->get('borrow/records', [
            'reader_id' => $readerId,
        ]);
    }

    /**
     * 逾期列表
     */
    public function overdue(): array
    {
        return $this->client->get('borrow/overdue');
    }

     /**
     * 所有借阅记录（分页）
     */
    public function allRecords(int $page = 1): array
    {
        return $this->client->get('borrow/all', ['page' => $page, 'page_size' => 10]);
    }

        /**
     * 即将到期提醒
     */
    public function reminders(): array
    {
        return $this->client->get('borrow/reminders');
    }

}

