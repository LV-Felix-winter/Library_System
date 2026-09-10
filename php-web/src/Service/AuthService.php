<?php

namespace App\Service;

use App\Http\ApiClient;

class AuthService
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * 管理员登录
     */
    public function login(string $username, string $password): array
    {
        return $this->client->post('auth/login', [
            'username' => $username,
            'password' => $password,
        ]);
    }

      /**
     * 修改密码
     */
    public function changePassword(string $oldPassword, string $newPassword): array
    {
        return $this->client->post('auth/change-password', [
            'old_password' => $oldPassword,
            'new_password' => $newPassword,
        ]);
    }
}
