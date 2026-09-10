<?php

namespace App\Http;

class ApiClient
{
    private string $baseUrl;
    private ?string $token = null;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * 设置 JWT Token（登录后调用）
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * 发送 GET 请求
     */
    public function get(string $path, array $params = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $this->request('GET', $url);
    }

    /**
     * 发送 POST 请求
     */
    public function post(string $path, array $data = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        return $this->request('POST', $url, $data);
    }

    /**
     * 发送 PUT 请求
     */
    public function put(string $path, array $data = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        return $this->request('PUT', $url, $data);
    }

    /**
     * 发送 DELETE 请求
     */
    public function delete(string $path): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        return $this->request('DELETE', $url);
    }

    /**
     * 统一处理 HTTP 请求
     */
    private function request(string $method, string $url, array $data = []): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // 设置请求头
        $headers = ['Content-Type: application/json'];
        if ($this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // POST/PUT 请求带上 JSON body
        if (!empty($data) && in_array($method, ['POST', 'PUT'])) {
            $jsonBody = json_encode($data, JSON_UNESCAPED_UNICODE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        // 请求失败
        if ($error) {
            return [
                'code'    => 500,
                'message' => '网络请求失败：' . $error,
                'data'    => null,
            ];
        }

        // 解析 JSON
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'code'    => $httpCode,
                'message' => '响应解析失败',
                'data'    => null,
            ];
        }

        return $result;
    }
}