<?php

namespace App\Controller;

use App\Service\StatsService;
use App\Service\BorrowService;

class DashboardController
{
    private StatsService $statsService;
    private BorrowService $borrowService;

    public function __construct(StatsService $statsService, BorrowService $borrowService)
    {
        $this->statsService  = $statsService;
        $this->borrowService = $borrowService;
    }

    /**
     * 仪表盘首页
     */
    public function index(): void
    {
        $result = $this->statsService->dashboard();
        $stats  = $result['data'] ?? [
            'total_books'     => 0,
            'available_books' => 0,
            'active_borrows'  => 0,
            'overdue_borrows' => 0,
        ];

        $reminderResult = $this->borrowService->reminders();
        $reminders      = $reminderResult['data'] ?? [];

        $pageTitle = '仪表盘 - 图书馆借阅系统';

        include __DIR__ . '/../../templates/dashboard/index.php';
    }
}
